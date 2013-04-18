'use strict';

angular.module('blApp')
  .controller('ActivitiesCtrl', ['$scope', '$rootScope', 'Routing', function ($scope, $rootScope, Routing) {

    if(!$rootScope.authenticated) {

      Routing.set('/');

    }

  }]);