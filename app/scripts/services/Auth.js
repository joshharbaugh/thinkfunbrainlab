'use strict';

angular.module('blApp')
  .provider('Auth', function () {
    /**
     * Holds all the requests which failed due to 401 response,
     * so they can be re-requested in future, once login is completed.
     */
    var buffer = [];
    
    /**
     * Required by HTTP interceptor.
     * Function is attached to provider to be invisible for regular users of this service.
     */
    this.pushToBuffer = function (config, deferred) {
      buffer.push({
        config: config,
        deferred: deferred
      });
    };
    
    this.$get = ['$rootScope', '$injector', function ($rootScope, $injector) {
      var $http; //initialized later because of circular dependency problem
      function retry(config, deferred) {
        $http = $http || $injector.get('$http');
        $http(config).then(function (response) {
          deferred.resolve(response);
        });
      }
      function retryAll() {
        for (var i = 0; i < buffer.length; ++i) {
          retry(buffer[i].config, buffer[i].deferred);
        }
        buffer = [];
      }

      return {
        loginConfirmed: function () {
          $rootScope.$broadcast('event:auth-loginConfirmed');
          $rootScope.authenticated = true;
          retryAll();
        }
      };
    }];
  })

  /**
   * $http interceptor
   */
  .config(function ($httpProvider, AuthProvider) {
    
    var interceptor = ['$rootScope', '$q', function ($rootScope, $q) {
      function success(response) {

        try {

          var json = $.xml2json(response.data);
          var httpResponse = json;
          $rootScope.resultCode = httpResponse.resultcode;
          $rootScope.resultText = httpResponse.resulttext;
          $rootScope.displayText = httpResponse.displaytext || null;
          $rootScope.resultData = httpResponse.data || null;

          if (!json.parsererror && typeof json.data === 'object') {

            if (console && console.log) {
              console.log('HTTP response: ', httpResponse);
              console.log('Result code: ' + $rootScope.resultCode);
              console.log('Result text: ' + $rootScope.resultText);
              console.log('Result data: ', $rootScope.resultData);
            }

            if ($rootScope.resultCode !== '0') {

              $rootScope.msg('Error:', $rootScope.resultText);

            }

          } else {

            if (console && console.log) {
              console.log('HTTP response: ', httpResponse);
              console.log('Result code: ' + $rootScope.resultCode);
              console.log('Result text: ' + $rootScope.resultText);
              console.log('Result displaytext: ' + $rootScope.displayText);
              console.log('Result data: ', $rootScope.resultData);
            }

          }

        } catch (e) {}

        return response;

      }
 
      function error(response) {

        console.log(response);

        if (response.status === 401) {
          var deferred = $q.defer();
          AuthProvider.pushToBuffer(response.config, deferred);
          $rootScope.$broadcast('event:auth-loginRequired');
          $rootScope.authenticated = false;
          return deferred.promise;
        }
        // otherwise
        return $q.reject(response);
      }
 
      return function (promise) {
        return promise.then(success, error);
      };
 
    }];
    $httpProvider.responseInterceptors.push(interceptor);
  });
