'use strict';

angular.module('blApp')
  .directive('blDialogModal', ['$http', '$compile', function ($http, $compile) {

    return {
      restrict: 'A',
      link: function (scope, element, attr) {
        var showing = false;
        var partial = attr['blDialogModal'];
        element.click(function () {
          if (showing) {
            return;
          }
          showing = true;
          $http({method: 'GET', url: partial}).success(function (html) {
            var context = scope.$eval(attr['blDialogContext']);
            var dialogScope = scope.$new();
            dialogScope.context = context;
            dialogScope.closeDialog = function () {
              $div.modal('hide');
            };
            var $div = $('<div/>').appendTo('body');
            $div.addClass('modal fade');
            $div.html(html);
            $compile($div.contents())(dialogScope);
            $div.modal('show');
            $div.on('hidden', function () {
              showing = false;
              $div.remove();
              dialogScope.$destroy();
            });
          });
        });
      }
    };

  }]);