var app = angular.module("app",['LocalStorageModule','ui-notification']);

app.controller("controller",['$http','$scope','localStorageService','Notification',function($http,$scope,localStorageService,Notification) {
  var app = $scope;
  var self = this;

  app.username = "admin";
  app.password = "123";
  app.node = null
  app.pin = [];
  app.mcu = [];
  app.removeTMP = [];
  app.newNode = "";
  app.newIP = "";
  app.newPin = 8;

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
          self.loadMCU();
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
          self.loadMCU();
          localStorageService.set("session", respond.data.session);
          localStorageService.set("username", respond.data.username);
          app.username = localStorageService.get("username");
          Notification.success('Login Success');
        }
      }
    )
  }

  self.loadNode = function(data) {
    var promise = $http.post("api/node");
    promise.then(
      function(respond) {
        // console.log("1" , respond.data);
        angular.forEach(respond.data, function(value, key) {
          value.ip = value.ip + "/digital/" + value.pin_id
        });
        app.node = respond.data;
        Notification.success('Load Node Success');
        // console.log("1" , app.node);
      }
    )
    promise.catch(
      function() {
          console.log("err");
      }
    )
  }

  self.loadMCU = function(data) {
    var promise = $http.post("api/mcu");
    promise.then(
      function(respond) {
        // console.log("1" , respond.data);
        app.mcu = respond.data;
        // console.log("1" , app.node);
      }
    )
    promise.catch(
      function() {
          console.log("err");
      }
    )
  }



  app.switch = function(item) {
    // console.log(item.status);
    var data = {item};
    var promise = $http.post("api/switch",data);
    promise.then(
      function(respond) {
        // console.log(respond.data);
        self.loadNode();

      }
    )
    promise.catch(
      function() {
          console.log("err");
      }
    )
  }

  app.update = function() {
    var data = {node:app.node};
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

  app.updateMachine = function() {
    var data = {mcu:app.mcu};
    var promise = $http.post("api/updateMachine",data);
    promise.then(
      function(respond) {
        self.loadMCU();
        if(respond.data == "true") {
          Notification.success('Update Machine Success');
        }
      }
    )
    promise.catch(
      function() {
          console.log("err");
      }
    )
  }

  app.goAdmin = function() {
    $("#adminControl").modal("show");
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

    if(app.newNode == "" ) {
      Notification.error("Please enter value");
      return;
    }
    var data = {name:app.newNode};
    self.resetNewNode();
    var promise = $http.post("api/addNode",data);
    promise.then(
      function(respond) {
        console.log("2131" , respond.data);
        self.loadNode();
        $("#addNode").modal("hide");
        Notification.success('Add Node Success');
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
    app.removeTMP = node;
  }

  app.cancelRemove = function() {
    app.removeTMP = [];
    $("#confirmDeleteNode").modal("hide");
  }

  app.removeNode = function(node_id) {
    var data = {"node_id":node_id};
    var promise = $http.post("api/removeNode",data);
    promise.then(
      function(respond) {
        // console.log(respond.data);
        self.loadNode();
        $("#confirmDeleteNode").modal("hide");
        Notification.success('Remove Node Success');
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
  }

}]);


app.config(function (localStorageServiceProvider,NotificationProvider) {
  localStorageServiceProvider
    .setPrefix('myApp')
    .setStorageType('sessionStorage')
    .setNotify(true, true)

    NotificationProvider.setOptions({
            delay: 5000,
            startTop: 20,
            startRight: 10,
            verticalSpacing: 20,
            horizontalSpacing: 20,
            positionX: 'right',
            positionY: 'top'
        });
});
