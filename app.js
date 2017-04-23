var app = angular.module("app",['LocalStorageModule']);

app.controller("controller",['$http','$scope','localStorageService',function($http,$scope,localStorageService) {
  var app = $scope;
  var self = this;

  app.username = "admin";
  app.password = "123";
  app.node = null
  app.pin = [];
  app.removeTMP = [];

  this.initial = function() {
    if(localStorageService.isSupported) {
      localStorageService.set("session", localStorageService.get("session") == null ? null:localStorageService.get("session"));
      console.log("OK");
      console.log(localStorageService.get("session"));
    }
    var data = localStorageService.get("session") == "-" ? "-" : localStorageService.get("session");
    var session = {"session":data};
    var promise = $http.post("api/session",session);
    promise.then(
      function(respond) {
        // console.log(respond.data);
        if(respond.data == "true") {
          self.loadNode();
        }
      }
    )
    promise.catch(
      function() {
          console.log("err");
      }
    )
  }
  this.initial();

  app.login = function() {
    var data = {username:app.username,password:app.password};
    // console.log(data);
    var promise = $http.post("api/login",data);
    promise.then(
      function(respond) {
        console.log(respond.data);
        if(respond.data.status == "true") {
          self.loadNode();
          localStorageService.set("session", respond.data.session);
          localStorageService.set("username", respond.data.username);
          app.username = localStorageService.get("username");
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
          value.isShow = false;
        });
        app.node = respond.data;
        console.log(app.node);
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

  app.logout = function() {
    localStorageService.clearAll();
    location.reload();
  }

  app.addNode = function() {
    var data = {name:app.newNode,ip:app.newIP,number:app.newPin};
    self.resetNewNode();
    var promise = $http.post("api/addNode",data);
    promise.then(
      function(respond) {
        console.log(respond.data);
        self.loadNode();
        $("#addNode").modal("hide");
      }
    )
    promise.catch(
      function() {
          console.log("err");
      }
    )
  }

  app.confirmDeleteNode = function(node) {
    $("#confirmDeleteNode").modal("show");
    $("#removeNode").modal("hide");
    app.removeTMP = node;
  }

  app.cancelRemove = function() {
    app.removeTMP = [];
    $("#confirmDeleteNode").modal("hide");
    $("#removeNode").modal("show");
  }

  app.removeNode = function(node_id) {
    var data = {"node_id":node_id};
    var promise = $http.post("api/removeNode",data);
    promise.then(
      function(respond) {
        // console.log(respond.data);
        self.loadNode();
        $("#confirmDeleteNode").modal("hide");
      }
    )
    promise.catch(
      function() {
          console.log("err");
      }
    )
  }

  self.resetNewNode = function() {
    app.newNode = "";
    app.newIP = "";
    app.newPin = "";
  }

}]);


app.config(function (localStorageServiceProvider) {
  localStorageServiceProvider
    .setPrefix('myApp')
    .setStorageType('sessionStorage')
    .setNotify(true, true)
});
