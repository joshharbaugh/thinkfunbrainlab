'use strict';

angular.module('blApp', ['ngCookies'])
  .config(['$routeProvider', function ($routeProvider) {
    $routeProvider

      .when('/', {

        templateUrl: 'views/main/show.html',

        controller: 'MainCtrl'

      })

      .when('/register', {

        templateUrl: 'views/account/register.html',

        controller: 'RegisterCtrl'

      })

      .when('/account', {

        templateUrl: 'views/account/show.html',

        controller: 'AccountCtrl'

      })

      .when('/arcade', {

        templateUrl: 'views/arcade/show.html',

        controller: 'ArcadeCtrl'

      })

      .when('/activities', {

        templateUrl: 'views/activities/show.html',

        controller: 'ActivitiesCtrl'

      })

      .otherwise({

        redirectTo: '/'

      });

      //$locationProvider.html5Mode(false).hashPrefix('!');

  }]);
