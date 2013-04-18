'use strict';

angular.module('blApp')
  .service('Routing', ['$rootScope', '$location', '$timeout', function ($rootScope, $location, $timeout) {
    var current = null;
    $rootScope.$watch(function () { return $location.url(); }, function () {
      processUrl($location.url());
    });

    function processUrl(url) {
      var split = url.split('?');
      if (split[0][split[0].length - 1] !== '/') {
        split[0] += '/';
      }
      var path = split.shift();
      var query = '';
      while (split.length > 0) {
        var piece = split.shift();
        if (piece.length > 0) {
          if (query.length > 0) {
            query += '&';
          }
          query += piece;
        }
      }
      url = path + (query.length > 0 ? '?' + query : '');
      if (url !== current) {
        current = url;

        // Process the query parameters
        var params = {};
        var querySplit = query.split('&');
        while (querySplit.length > 0) {
          var queryPiece = querySplit.shift();
          if (jQuery.trim(queryPiece).length === 0) {
            continue;
          }
          var queryPieceSplit = queryPiece.split('=');
          var name = queryPieceSplit[0];
          var value = queryPieceSplit.length > 0 ? queryPieceSplit.join('=') : null;
          params[name] = value;
        }

        for (var i = 0; i < registrants.length; i++) {
          var match = url.match(registrants[i].pattern);
          if (match && match[0] === url) {
            try {
              registrants[i].callback.call(registrants[i], path, params);
            } catch (e) {
              if (console && console.error) {
                console.error('URL Listener Error:', e.message);
                console.error('Listener: ', registrants[i]);
              }
            }
          }
        }
      }
    }

    var registrants = [];
    var navigationService = {
      register: function (obj) {
        if (angular.isFunction(obj)) {
          navigationService.register({pattern: /.*/, callback: obj});
          return;
        }
        if (angular.isArray(obj)) {
          for (var i = 0; i < obj.length; i++) {
            navigationService.register(obj[i]);
          }
          return;
        }
        if (typeof obj.pattern === 'undefined' || typeof obj.callback === 'undefined') {
          for (var pattern in obj) {
            if (typeof pattern.match !== 'undefined' && angular.isFunction(obj[pattern])) {
              navigationService.register({pattern: new RegExp(pattern), callback: obj[pattern]});
            }
          }
          return;
        }

        if (angular.isFunction(obj.callback)) {
          registrants.push(obj);
        }
      },
      set: function (url) {
        function attempt() {
          $location.url(url);
        }
        $timeout(attempt);
      },
      get: function () {
        return $location.url();
      }
    };
    
    return navigationService;

  }]);

angular.module('blApp').run(['Routing', function () {}]);
