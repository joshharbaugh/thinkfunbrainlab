'use strict';

angular.module('blApp')
  .controller('LoginCtrl', ['$scope', '$rootScope', '$http', 'Auth', 'Routing', 'User', function ($scope, $rootScope, $http, Auth, Routing, User) {

    $scope.loginUser = function () {

      var form = $('.account-login').find('form');
      var formSerialized = form.serialize();
      var apiLib = 'Account';
      var payload = $scope.API_PAYLOAD + apiLib + '&API_METHOD=loginPlayer&' + formSerialized;

      $http.post($scope.API_URL, payload, { headers: {'Content-Type': 'application/x-www-form-urlencoded'} }).success(function (data) {

        if ($scope.resultCode === '0') {

          var responseData = $(data);
          var resultText = responseData.find('resulttext').text();

          responseData.find('resultcode').each(function () {

            if (resultText === 'Success') {

              Auth.loginConfirmed();

              responseData.find('data').each(function () {

                var data   = this;
                var player = data.innerHTML;
                var user   = $.xml2json(player);

                User.set(user);

                if(!$rootScope.user) {

                  var user = User.get();

                }        

              });
            
            }

          });

        } else {

          $rootScope.msg($scope.resultText, $scope.displayText);

        }

      }).error(function () {

        $rootScope.msg('Error:', 'There was a problem. Please try again.');

        $rootScope.authenticated = false;
      
      });

    };

    $scope.logoutUser = function (user) {

      console.log('logging out user: ', user);

      var apiLib = 'Account';
      var payload = $scope.API_PAYLOAD + apiLib + '&API_METHOD=logoutPlayer&idPlayer=' + user.idplayer;

      $http.post($scope.API_URL, payload, { headers: {'Content-Type': 'application/x-www-form-urlencoded'} }).success(function () {

        $rootScope.authenticated = false;

        User.clear();

        Routing.set('/');

        $scope.$broadcast('event:auth-logoutConfirmed');

      });

    };

    $scope.$on('event:auth-loginRequired', function () {

      setTimeout(function () {

        User.clear();

        $rootScope.msg('', 'Must be logged in to access this page.');

      });

    });

  }]);