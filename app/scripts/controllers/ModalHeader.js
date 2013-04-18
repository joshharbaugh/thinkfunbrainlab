'use strict';

angular.module('blApp')
  .controller('ModalHeaderCtrl', ['$scope', '$timeout', function ($scope, $timeout) {

    $scope.close = function () {

      $timeout(function () {

        try {
            
          $scope.closeDialog();

        } catch (e) {}
      
      });

    };

  }]);