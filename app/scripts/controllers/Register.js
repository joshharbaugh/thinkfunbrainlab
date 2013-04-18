'use strict';

angular.module('blApp')
  .controller('RegisterCtrl', ['$scope', '$rootScope', '$http', 'Routing', 'Auth', function ($scope, $rootScope, $http, Routing, Auth) {

    if (!$rootScope.age) {

      Routing.set('/');
    
    } else {

      console.log('Age: ' + $rootScope.age);

      console.log($rootScope.user);

      $scope.birthMonth = $rootScope.user.birthMonth;
      $scope.birthYear = $rootScope.user.birthYear;
      $scope.country = {};

      $scope.registerPlayer = function () {

        var form = $('#register-account');
        var formSerialized = form.serialize();
        var apiLib = 'Account';
        var payload = $scope.API_PAYLOAD + apiLib + '&API_METHOD=addPlayer&' + formSerialized;

        $http.post($scope.API_URL, payload, { headers: {'Content-Type': 'application/x-www-form-urlencoded'} }).success(function (data, responseType) {

          if (responseType === 200) {

            var responseData = $(data),
                resultText = responseData.find('resulttext').text(),
                displayText = responseData.find('displaytext').text();

            if (resultText !== 'Success') {

              // there was an error response
              $rootScope.msg(resultText, displayText, null, true);

            } else {

              // success, authenticate player and redirect to homepage
              var apiLib = 'Account';
              var payload = $scope.API_PAYLOAD + apiLib + '&API_METHOD=loginPlayer&' + formSerialized;
              $http.post($scope.API_URL, payload, { headers: {'Content-Type': 'application/x-www-form-urlencoded'} }).success(function (data, responseType) {

                if (responseType === 200) {
                
                  var responseData = $(data),
                      resultText = responseData.find('resulttext').text();

                  if (resultText === 'Success') {

                    // success
                    $rootScope.msg(resultText, 'You have successfully registered with Brain Lab.');

                    Auth.loginConfirmed();

                    responseData.find('data').each(function () {

                      var data = this;
                      var player = data.innerHTML;

                      $rootScope.user = $.xml2json(player);
                      
                      $rootScope.authenticated = true;

                    });

                    Routing.set('/');

                  }

                }

              }).error(function (data, responseType) {

                if (console && console.log) {
                  console.log(data, responseType);
                }

                $rootScope.msg('Error:', 'There was a problem. Please try again.');
              
              });

            }

          }

        }).error(function (data, responseType) {

          if (console && console.log) {
            console.log(data, responseType);
          }

          $rootScope.msg('Error:', 'There was a problem. Please try again.');
        
        });

      };

    }

  }]);