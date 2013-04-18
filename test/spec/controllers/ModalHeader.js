'use strict';

describe('Controller: ModalHeaderCtrl', function () {

  // load the controller's module
  beforeEach(module('yoApp'));

  var ModalHeaderCtrl,
    scope;

  // Initialize the controller and a mock scope
  beforeEach(inject(function ($controller) {
    scope = {};
    ModalHeaderCtrl = $controller('ModalHeaderCtrl', {
      $scope: scope
    });
  }));

  it('should attach a list of awesomeThings to the scope', function () {
    expect(scope.awesomeThings.length).toBe(3);
  });
});
