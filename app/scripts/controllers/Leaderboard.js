'use strict';

angular.module('blApp')
  .controller('LeaderboardCtrl', ['$scope', function ($scope) {

    // mock data
    $scope.leaderboard = [

      {'idPlayer': 1, 'userName': 'testUser1', 'best_time': '3:00:00'},
      {'idPlayer': 19, 'userName': 'testUser19', 'best_time': '2:50:00'},
      {'idPlayer': 199, 'userName': 'testUser199', 'best_time': '1:59:00'},
      {'idPlayer': 32, 'userName': 'testUser32', 'best_time': '1:50:00'},
      {'idPlayer': 24, 'userName': 'testUser24', 'best_time': '1:49:00'},

    ];

  }]);