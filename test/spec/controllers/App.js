'use strict';

describe('Controller: AppCtrl', function () {

  // load the controller's module
  beforeEach(module('yoApp'));

  var AppCtrl,
    scope;

  // Initialize the controller and a mock scope
  beforeEach(inject(function ($controller) {
    scope = {};
    AppCtrl = $controller('AppCtrl', {
      $scope: scope
    });
  }));

  it('should attach a list of awesomeThings to the scope', function () {
    expect(scope.awesomeThings.length).toBe(3);
  });
});
