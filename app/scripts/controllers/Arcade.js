'use strict';

angular.module('blApp')
  .controller('ArcadeCtrl', ['$scope', '$rootScope', '$http', function ($scope, $rootScope, $http) {

    var games = $rootScope.games || null;

    if ($rootScope.authenticated) {

      var apiLib = 'Game';
      var payload = $scope.API_PAYLOAD + apiLib + '&API_METHOD=getGameList';

      $http.post($scope.API_URL, payload, { headers: {'Content-Type': 'application/x-www-form-urlencoded'} }).success(function (data, responseType) {

        if (responseType === 200) {

          var responseData = $(data),
              resultText = responseData.find('resulttext').text();

          if (resultText !== 'Success') {

            // mock data
            $rootScope.previewGames = [
              {'idGame': 1, 'masterGame': 'Chocolate Fix Blitz', 'active': true, 'thumbnail': 'assets/img/chocolate-fix-button.jpg'},
              {'idGame': 2, 'masterGame': 'Rush Hour', 'active': false, 'thumbnail': 'assets/img/rush-hour-button.jpg'},
              {'idGame': 3, 'masterGame': 'What\'s GNU', 'active': false, 'thumbnail': 'assets/img/whats-gnu-button.jpg'},
              {'idGame': 4, 'masterGame': 'Zingo', 'active': false, 'thumbnail': 'assets/img/zingo-button.jpg'},
              {'idGame': 5, 'masterGame': 'Zingo 123', 'active': false, 'thumbnail': 'assets/img/zingo-123-button.jpg'}
            ];

            $rootScope.games = [
              {'idGame': 1, 'masterGame': 'Chocolate Fix Blitz', 'active': true, 'thumbnail': 'assets/img/chocolate-fix-button.jpg'},
              {'idGame': 2, 'masterGame': 'Rush Hour', 'active': true, 'thumbnail': 'assets/img/rush-hour-button.jpg'},
              {'idGame': 3, 'masterGame': 'What\'s GNU', 'active': true, 'thumbnail': 'assets/img/whats-gnu-button.jpg'},
              {'idGame': 4, 'masterGame': 'Zingo', 'active': true, 'thumbnail': 'assets/img/zingo-button.jpg'},
              {'idGame': 5, 'masterGame': 'Zingo 123', 'active': true, 'thumbnail': 'assets/img/zingo-123-button.jpg'},
              {'idGame': 6, 'masterGame': 'Chocolate Fix Blitz', 'active': true, 'thumbnail': 'assets/img/chocolate-fix-button.jpg'},
              {'idGame': 7, 'masterGame': 'Rush Hour', 'active': true, 'thumbnail': 'assets/img/rush-hour-button.jpg'},
              {'idGame': 8, 'masterGame': 'What\'s GNU', 'active': true, 'thumbnail': 'assets/img/whats-gnu-button.jpg'},
              {'idGame': 9, 'masterGame': 'Zingo', 'active': true, 'thumbnail': 'assets/img/zingo-button.jpg'},
              {'idGame': 10, 'masterGame': 'Zingo 123', 'active': true, 'thumbnail': 'assets/img/zingo-123-button.jpg'}
            ];

          } else {

            responseData.find('data').each(function () {

              var data = this;
              games = data.innerHTML;

              $rootScope.games = $.xml2json(games);
              $rootScope.previewGames = $.xml2json(games);

              console.log($rootScope.games);

            });

          }

        }

      });

    } else {

      // mock data
      $rootScope.previewGames = [
        {'idGame': 1, 'masterGame': 'Chocolate Fix Blitz', 'active': true, 'thumbnail': 'assets/img/chocolate-fix-button.jpg'},
        {'idGame': 2, 'masterGame': 'Rush Hour', 'active': false, 'thumbnail': 'assets/img/rush-hour-button.jpg'},
        {'idGame': 3, 'masterGame': 'What\'s GNU', 'active': false, 'thumbnail': 'assets/img/whats-gnu-button.jpg'},
        {'idGame': 4, 'masterGame': 'Zingo', 'active': false, 'thumbnail': 'assets/img/zingo-button.jpg'},
        {'idGame': 5, 'masterGame': 'Zingo 123', 'active': false, 'thumbnail': 'assets/img/zingo-123-button.jpg'}
      ];

      $rootScope.games = [
        {'idGame': 1, 'masterGame': 'Chocolate Fix Blitz', 'active': true, 'thumbnail': 'assets/img/chocolate-fix-button.jpg'},
        {'idGame': 2, 'masterGame': 'Rush Hour', 'active': true, 'thumbnail': 'assets/img/rush-hour-button.jpg'},
        {'idGame': 3, 'masterGame': 'What\'s GNU', 'active': true, 'thumbnail': 'assets/img/whats-gnu-button.jpg'},
        {'idGame': 4, 'masterGame': 'Zingo', 'active': true, 'thumbnail': 'assets/img/zingo-button.jpg'},
        {'idGame': 5, 'masterGame': 'Zingo 123', 'active': true, 'thumbnail': 'assets/img/zingo-123-button.jpg'},
        {'idGame': 6, 'masterGame': 'Chocolate Fix Blitz', 'active': true, 'thumbnail': 'assets/img/chocolate-fix-button.jpg'},
        {'idGame': 7, 'masterGame': 'Rush Hour', 'active': true, 'thumbnail': 'assets/img/rush-hour-button.jpg'},
        {'idGame': 8, 'masterGame': 'What\'s GNU', 'active': true, 'thumbnail': 'assets/img/whats-gnu-button.jpg'},
        {'idGame': 9, 'masterGame': 'Zingo', 'active': true, 'thumbnail': 'assets/img/zingo-button.jpg'},
        {'idGame': 10, 'masterGame': 'Zingo 123', 'active': true, 'thumbnail': 'assets/img/zingo-123-button.jpg'}
      ];

    }

    $scope.startGame = function () {

      console.log(this);

      if (this.game.active === 'true') {

        console.log('load game.');

      } else {

        console.log('please register to gain access to this game.');

      }

    };

  }]);