var app = angular.module("app",[]);

app.controller("controller",['$http','$scope',function($http,$scope) {
  var app = $scope;
  var self = this;

  app.username = "admin";
  app.password = "123";
  app.node = null
  app.pin = [];

  app.login = function() {
    var data = {username:app.username,password:app.password};
    // console.log(data);
    var promise = $http.post("api/login",data);
    promise.then(
      function(respond) {
        console.log(respond.data);
        if(respond.data == "true") {
          self.loadNode();
        }
      }
    )
  }

  self.loadNode = function(data) {
    var promise = $http.post("api/node");
    promise.then(
      function(respond) {
        // console.log(respond.data);
        angular.forEach(respond.data, function(value, key) {
          self.loadPin(value.id)
        });
        app.node = respond.data;
      }
    )
    promise.catch(
      function() {
          console.log("err");
      }
    )
  }

  self.loadPin = function(node) {
    var data = {"node":node};
    var promise = $http.post("api/pin",data);
    promise.then(
      function(respond) {
        // console.log(respond.data);
        app.pin[node] = respond.data;
      }
    )
    promise.catch(
      function() {
          console.log("err");
      }
    )
  }

  app.update = function() {
    var data = {node:app.node,pin: app.pin};
    var promise = $http.post("api/update",data);
    promise.then(
      function(respond) {
        console.log(respond.data);
      }
    )
    promise.catch(
      function() {
          console.log("err");
      }
    )
  }

  app.setPin = function(pin,node) {
    var ip = node.ip;
    var status = pin.status == 1 ? 1:0;
    console.log("new Status : " + status);
    console.log("http://" + ip + "/digital/" + pin.pin_id + "/" + status);
    var promise = $http.get("http://" + ip + "/digital/" + pin.pin_id + "/" + status);
    promise.then(
      function(respond) {
        console.log(respond.data);
      }
    )
    promise.catch(
      function() {
          console.log("err");
      }
    )
  }

  app.status = function(status) {
    return status == 1 ? "ON" : "OFF";
  }

  var utf8_to_b64 = function(str) {
    return window.btoa(unescape(encodeURIComponent( str )));
  }

  var b64_to_utf8 = function(str) {
    return decodeURIComponent(escape(window.atob( str )));
  }

}]);
