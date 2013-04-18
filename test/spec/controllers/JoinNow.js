'use strict';

describe('Controller: JoinNowCtrl', function () {

  // load the controller's module
  beforeEach(module('yoApp'));

  var JoinNowCtrl,
    scope;

  // Initialize the controller and a mock scope
  beforeEach(inject(function ($controller) {
    scope = {};
    JoinNowCtrl = $controller('JoinNowCtrl', {
      $scope: scope
    });
  }));

  it('should attach a list of awesomeThings to the scope', function () {
    expect(scope.awesomeThings.length).toBe(3);
  });
});
