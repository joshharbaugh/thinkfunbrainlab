'use strict';

angular.module('blApp')
  .service('Helper', ['$rootScope', '$timeout', '$http', function ($rootScope, $timeout, $http) {

    var helperService = {

      months: function () {

        return [
          {name: 'January', value: '01'},
          {name: 'February', value: '02'},
          {name: 'March', value: '03'},
          {name: 'April', value: '04'},
          {name: 'May', value: '05'},
          {name: 'June', value: '06'},
          {name: 'July', value: '07'},
          {name: 'August', value: '08'},
          {name: 'September', value: '09'},
          {name: 'October', value: '10'},
          {name: 'November', value: '11'},
          {name: 'December', value: '12'}
        ];

      },

      countries: function () {

        var apiLib = 'Account';
        var countries,
            countriesArray = [];
        var payload = 'API_VER=1.0&API_KEY=123456789&API_LIBRARY=' + apiLib + '&API_METHOD=getCountryList';

        $http.post('//sandbox.thinkfunbrainlab.com/api/api.php', payload, { headers: {'Content-Type': 'application/x-www-form-urlencoded'} }).success(function (data, responseType) {

          if (responseType === 200) {

            if ($rootScope.resultData) {

              countries = $rootScope.resultData.country;

              for (var c in countries) {

                if (countries.hasOwnProperty(c)) {

                  countriesArray.push({'name': countries[c].countryName, 'value': countries[c].idCountry});

                }

              }

              sessionStorage.setItem('countries', JSON.stringify(countriesArray));

            }

          }

        });

        return countriesArray;

      }

    };
    
    return helperService;

  }]);

angular.module('blApp').run(['Helper', function () {}]);