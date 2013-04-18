'use strict';

angular.module('blApp')
  .service('User', ['$rootScope', '$timeout', function ($rootScope, $timeout) {

    var user = {};

    var userService = {

      get: function () {

        user = JSON.parse(sessionStorage.getItem('user'));

        if(user) {

          $rootScope.user = user;

        } else {

          $rootScope.user = {};

        }

        return $rootScope.user;

      },

      set: function (user) {

        sessionStorage.setItem('user', JSON.stringify(user));

        $rootScope.user = userService.get();

      },

      clear: function () {

        sessionStorage.clear();

        $rootScope.user = {};

        return;

      }

    }

    return userService;

  }]);

angular.module('blApp').run(['User', function () {}]);