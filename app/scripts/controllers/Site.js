'use strict';

angular.module('blApp')
  .controller('SiteCtrl', ['$scope', '$rootScope', '$http', 'Routing', 'User', function ($scope, $rootScope, $http, Routing, User) {

    $rootScope.authenticated = false;
    $rootScope.error = false;    

    $scope.API_URL = 'http://sandbox.thinkfunbrainlab.com/api/api.php';
    $scope.API_PAYLOAD = 'API_VER=1.0&API_KEY=123456789&API_LIBRARY=';

    var user = User.get();

    if(user.idplayer) {

      $rootScope.authenticated = true;

    }

    $.extend($.gritter.options, {
      position: 'top-right',
      fade_in_speed: 800,
      fade_out_speed: 1000,
      time: 2000
    });

    $rootScope.msg = function (title, text, time, sticky) {

      var _time = time || 3000;

      var _sticky = sticky || false;

      $.gritter.add({

        title: title,

        text: text,

        time: _time,

        sticky: _sticky

      });

    };

    $rootScope.months    = window.app.utils.months();
    $rootScope.countries = window.app.utils.countries();

    if($rootScope.countries.length == 0) {

      $rootScope.countries = JSON.parse(sessionStorage.getItem('countries'));
      
    }

    $rootScope.resetPlayerState = function () {

      User.clear();

      $rootScope.user = {};

      $rootScope.authenticated = false;

      Routing.set('/');

    };

  }]);