'use strict';

angular.module('blApp')
  .controller('JoinNow', ['$scope', '$rootScope', 'Routing', function ($scope, $rootScope, Routing) {

    function getAge(dateString) {
      var today = new Date();
      var birthDate = new Date(dateString);
      var age = today.getFullYear() - (birthDate.getFullYear() + 1);
      var m = today.getMonth() - (birthDate.getMonth() + 1);
      if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
      }
      return age;
    }

    $scope.verifyMonth = $rootScope.months[0];

    $scope.validatePlayerAge = function () {

      var dateString = $scope.verifyMonth.name + ' 01, ' + $scope.verifyYear;
      
      var age = getAge(dateString);

      $rootScope.age = age;

      if (age) {

        $scope.closeDialog();

        $rootScope.user.birthMonth = $scope.verifyMonth;
        
        $rootScope.user.birthYear = $scope.verifyYear;
        
        Routing.set('/register');

      } else {

        $rootScope.msg('Error:', 'We could not verify your age. Please try again.');

      }

    };

  }]);