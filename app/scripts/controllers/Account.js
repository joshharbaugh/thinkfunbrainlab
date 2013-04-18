'use strict';

angular.module('blApp')
  .controller('AccountCtrl', ['$scope', '$rootScope', '$http', 'User', function ($scope, $rootScope, $http, User) {
    
    $scope.user = User.get();

    if ($scope.user) {

      var idPlayer = $scope.user.idplayer;
      var apiLib = 'Account';
      var payload = $scope.API_PAYLOAD + apiLib + '&API_METHOD=getPlayerById&idPlayer=' + idPlayer;

      $http.post($scope.API_URL, payload, { headers: {'Content-Type': 'application/x-www-form-urlencoded'} }).success(function () {

        if (console && console.log) {
          console.log('getPlayerById\n');
          console.log($scope.resultText);
          console.log($scope.resultData);
        }

        var DOB = $scope.user.dob;
        DOB = DOB.split("-");

        var DOB_Month = DOB[1];
        var DOB_Day   = DOB[2];

        console.log($rootScope.countries[parseInt($scope.user.idcountry)]);

        $scope.userCountry    = $rootScope.countries[parseInt($scope.user.idcountry)];
        $scope.userBirthMonth = $rootScope.months[parseInt(DOB_Month)-1];
        $scope.userBirthDay   = DOB_Day;

        if (!$scope.resultData) {

          $rootScope.resetPlayerState();

        }

      }).error(function (data, responseType) {

        if (console && console.log) {
          console.log(data, responseType);
        }

        $rootScope.msg('Error:', 'There was a problem. Please try again.');

        $rootScope.authenticated = false;
      
      });

      $scope.changePassword = function () {

        if ($scope.newPwd !== $scope.confirmNewPwd) {

          $rootScope.msg('', 'New password and confirm new password values do not match.');

          return;

        }

        var form = $('#account-password-change');
        var formSerialized = form.serialize();
        var userName = $scope.user.username;
        var apiLib = 'Account';
        var payload = $scope.API_PAYLOAD + apiLib + '&API_METHOD=changePassword&userName=' + userName + '&' + formSerialized;

        $http.post($scope.API_URL, payload, { headers: {'Content-Type': 'application/x-www-form-urlencoded'} }).success(function (data, responseType) {

          if (console && console.log) {
            console.log(data);
          }

          if (responseType === 200) {

            var responseData = $(data),
              resultText = responseData.find('resulttext').text(),
              resultCode = responseData.find('resultcode').text();

            switch (resultCode)
            {
            case '0':
              $scope.newPwd = '';
              $scope.confirmNewPwd = '';
              $scope.pwd = '';
              $rootScope.msg('Hooray!', 'You have successfully changed your password.');
              break;
              
            case '108':
              $rootScope.resetPlayerState();
              break;
              
            default:
              $rootScope.msg('', resultText);
            }

          }

        }).error(function (data, responseType) {

          if (console && console.log) {
            console.log(data, responseType);
          }

          $rootScope.msg('Error:', 'There was a problem. Please try again.');
        
        });

      };

      $scope.updatePlayer = function () {

        var form = $('#account-profile');
        var formSerialized = form.serialize();
        var idPlayer = $scope.user.idplayer;
        var apiLib = 'Account';
        var payload = $scope.API_PAYLOAD + apiLib + '&API_METHOD=updatePlayer&idPlayer=' + idPlayer + '&' + formSerialized;

        $http.post($scope.API_URL, payload, { headers: {'Content-Type': 'application/x-www-form-urlencoded'} }).success(function (data) {

          if (console && console.log) {
            console.log(data);
          }

        }).error(function (data, responseType) {

          if (console && console.log) {
            console.log(data, responseType);
          }

          $rootScope.msg('Error:', 'There was a problem. Please try again.');
        
        });

      };

    } else {

      $rootScope.resetPlayerState();

      $rootScope.msg('', 'Please login before accessing this page');

    }

  }]);
