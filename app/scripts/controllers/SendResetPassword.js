'use strict';

angular.module('blApp')
  .controller('SendResetPassword', ['$scope', '$rootScope', '$http', function ($scope, $rootScope, $http) {

    $scope.sendResetPassword = function () {

      var form = $('#register-account');
      var formSerialized = form.serialize();
      var apiLib = 'Account';
      var payload = $scope.API_PAYLOAD + apiLib + '&API_METHOD=sendResetPassword&' + formSerialized;

      $http.post($scope.API_URL, payload, { headers: {'Content-Type': 'application/x-www-form-urlencoded'} }).success(function (data, responseType) {

        if (responseType === 200) {

          var responseData = $(data),
              resultText = responseData.find('resulttext').text(),
              resultCode = responseData.find('resultcode').text(),
              displayText = responseData.find('displaytext').text() || resultCode;

          if (resultText !== 'Success') {

            // there was an error response
            $rootScope.msg(resultText, displayText, null, true);

          } else {

            // Routing.set(/passwordResetSent);

          }

        }

      }).error(function (data, responseType) {

        if (console && console.log) {
          console.log(data, responseType);
        }

        $rootScope.msg('Error:', 'There was a problem. Please try again.');
      
      });

    };

  }]);