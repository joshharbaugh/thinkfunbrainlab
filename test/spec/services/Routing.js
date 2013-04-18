'use strict';

describe('Service: Routing', function () {

  // load the service's module
  beforeEach(module('yoApp'));

  // instantiate service
  var Routing;
  beforeEach(inject(function (_Routing_) {
    Routing = _Routing_;
  }));

  it('should do something', function () {
    expect(!!Routing).toBe(true);
  });

});
