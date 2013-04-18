'use strict';

describe('Directive: blDialogModal', function () {
  beforeEach(module('yoApp'));

  var element;

  it('should make hidden element visible', inject(function ($rootScope, $compile) {
    element = angular.element('<bl-dialog-modal></bl-dialog-modal>');
    element = $compile(element)($rootScope);
    expect(element.text()).toBe('this is the blDialogModal directive');
  }));
});
