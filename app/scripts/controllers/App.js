'use strict';

angular.module('blApp')
  .controller('AppCtrl', ['$rootScope', '$http', 'Helper', function ($rootScope, $http, Helper) {
    (function (window) {

      window.app = {};

      window.app.utils = {

        months: function () {

          return Helper.months();

        },

        countries: function () {

          return Helper.countries();

        }

      };

      return window.app;

    })(window);
  }]);
