angular.module('starter.controllers', [])

.controller('AppCtrl', function($scope, $ionicModal, $timeout,userApi,$rootScope,$ionicPopup,$ionicHistory,$state,$ionicSideMenuDelegate) {

  // With the new view caching in Ionic, Controllers are only called
  // when they are recreated or on app start, instead of every page change.
  // To listen for when this page is active (for example, to refresh data),
  // listen for the $ionicView.enter event:
  //$scope.$on('$ionicView.enter', function(e) {
  //});

  $rootScope.showAlert = function(title,data) {
     var alertPopup = $ionicPopup.alert({
       title: title,
       template: JSON.stringify(data)
     });

     alertPopup.then(function(res) {console.log(title + JSON.stringify(data));});
   };
   $rootScope.isLoggedIn = false;
   $rootScope.imgUrl = "http://sagegatzke.com/inventorytracker/images/";
   $rootScope.logOut = function(){
      console.log("in log out");
      Ionic.Auth.logout();
      $rootScope.isLoggedIn =false;
      $ionicSideMenuDelegate.toggleLeft();
      $ionicHistory.nextViewOptions({
        historyRoot: true
      });
      $state.go("app.loginScreen", null, {reload: true});
   };

})
.controller('RegisterCtrl', function($scope,$location,$state,$ionicHistory, userApi,$rootScope) {
  var io = Ionic.io();
  $scope.$on('$ionicView.enter', function(e) {
    user = Ionic.User.current();
    $scope.loginData = {};
    
    $scope.loginData.username = "";
    $scope.loginData.password = "";
    $scope.loginData.token = user.get('token');
    $scope.loginData.platform = user.get('platform');
  });
  

  var registerSuccess = function(data){
    $rootScope.showAlert("Database sign up success data:",data);
    $ionicHistory.nextViewOptions({
      historyRoot: true
    });
    $state.go("app.loginScreen", null, {reload: true});
  };
  var registerFailure = function(data){
    $rootScope.showAlert("Database register failure data:",data);
  };

  var signupSuccess = function(data){
    $rootScope.showAlert("ionic sign up success data:",data);
    user.set('username', $scope.loginData.username);
    user.set('password', $scope.loginData.password);
    user.save();
    console.log("user object:");
    console.log(JSON.stringify(user));
    userApi.register($scope.loginData.username, $scope.loginData.password, $scope.loginData.token,$scope.loginData.platform).then(registerSuccess,registerFailure);
  };
  var signupFailure = function(data){
    $rootScope.showAlert("ionic signup fail data:",data);
  };
  $scope.doRegister = function() {
    var details = {
      'account': $scope.loginData.acount,
      'username': $scope.loginData.username,
      'email': $scope.loginData.username + '@sagegatzke.com',
      'password': $scope.loginData.password
    }
    Ionic.Auth.signup(details).then(signupSuccess, signupFailure);    
  };
})
.controller('LoginScreenCtrl', function($scope,$location,$state,$ionicHistory, userApi,$rootScope) {
  user = Ionic.User.current();
  
  $scope.$on('$ionicView.enter', function(e) {
    if ($rootScope.isLoggedIn) {
      $ionicHistory.nextViewOptions({
          historyRoot: true
      });
      $state.transitionTo("app.groups",null,{reload: true,});
    } 
    $scope.isLoggedIn = $rootScope.isLoggedIn;
    $scope.loginData = {};
    $scope.loginData.username = user.get('username');
    $scope.loginData.password = user.get('password');
    $scope.loginData.token = user.get('token');
    $scope.loginData.platform = user.get('platform');
  });

  var loginSuccess = function(data){
    //$rootScope.showAlert("Database login success data:",data);
    $rootScope.isLoggedIn = true;
    $ionicHistory.nextViewOptions({
        historyRoot: true
    });
    $state.transitionTo("app.groups",null,{reload: true,});
  };
  var loginFailure = function(data){
    $rootScope.showAlert("Database login failure data:",data);
  };
  var authSuccess = function(data){
    //$rootScope.showAlert("ionic login success data:",data);
    user.set('username', $scope.loginData.username);
    user.set('password', $scope.loginData.password);
    user.save();
    console.log("user object:");
    console.log(JSON.stringify(user));      
    userApi.login($scope.loginData.username, $scope.loginData.password, $scope.loginData.token, $scope.loginData.platform).then(loginSuccess,loginFailure);
  };

  var authFailure = function(data){
    $rootScope.showAlert("ionic login fail data:",data);
  };

  $scope.doLogin = function() {
    var details = 
    {
      'account': $scope.loginData.account,
      'username': $scope.loginData.username,
      'email': $scope.loginData.username + '@sagegatzke.com',
      'password': $scope.loginData.password
    }
    var options = { 'remember': true };
    Ionic.Auth.login('basic', options, details).then(authSuccess, authFailure);
  };
})
.controller('GroupsCtrl', function($scope,$rootScope,userApi,$location) {
  $scope.groups = [];
  $scope.imgUrl = $rootScope.imgUrl;
  var syncSuccess = function(data){
    //$rootScope.showAlert("Database sync success data:",data.groups);
    $scope.groups = data.groups;
  };
  var syncFailure = function(data){
    $rootScope.showAlert("Database sync failure data:",data);
  };
   $scope.$on('$ionicView.enter', function(e) {
    userApi.clone().then(syncSuccess,syncFailure);
  });

  $scope.addCollection = function(){
    $rootScope.showAlert("Add Collection not Implemented yet:","TODO");
  };
  $scope.go = function ( path ) {
    $location.path( path );
  };
  $scope.popUpMenu = function($event){
    $event.stopPropagation();
    $event.preventDefault();
    console.log("in popup Menu");
    $rootScope.showAlert("SubMenu not Implemented yet:","TODO");
  };
  
})

.controller('GroupCtrl', function($scope, $stateParams,$rootScope,userApi,$location) {
  $scope.imgUrl = $rootScope.imgUrl;
  $scope.group = userApi.getGroup($stateParams.groupId);
  $scope.addCategory = function(){
    $rootScope.showAlert("Add Category not Implemented yet:","TODO");
  };
  $scope.editCollection = function(){
    $rootScope.showAlert("Edit Collection not Implemented yet:","TODO");
  };
  $scope.showUsers = function(){
    $rootScope.showAlert("Show users not Implemented yet:","TODO");
  };
  $scope.go = function ( path ) {
    $location.path( path );
  };
  $scope.popUpMenu = function($event){
    $event.stopPropagation();
    $event.preventDefault();
    console.log("in popup Menu");
    $rootScope.showAlert("SubMenu not Implemented yet:","TODO");
  };
  //$rootScope.showAlert("info:",$scope.group);
})
.controller('CategoryCtrl', function($scope, $stateParams,$rootScope,userApi,$location) {
  $scope.imgUrl = $rootScope.imgUrl;
  $scope.thisGroupId = $stateParams.groupId;
  $scope.thisCategoryId = $stateParams.categoryId;
  $scope.category = userApi.getCategory($stateParams.groupId,$stateParams.categoryId);
  //$rootScope.showAlert("info:",$scope.category);
  $scope.addItem = function(){
    $rootScope.showAlert("Add Item not Implemented yet:","TODO");
  };
  $scope.editCategory = function(){
    $rootScope.showAlert("Edit Category not Implemented yet:","TODO");
  };
  $scope.go = function ( path ) {
    $location.path( path );
  };
  $scope.popUpMenu = function($event){
    $event.stopPropagation();
    $event.preventDefault();
    console.log("in popup Menu");
    $rootScope.showAlert("SubMenu not Implemented yet:","TODO");
  };
})
.controller('ItemCtrl', function($scope, $stateParams,$rootScope,userApi) {
  //$scope.groupId = $stateParams.groupId;
  //$scope.categoryId =  $stateParams.categoryId;
  var syncSuccess = function(data){
    $scope.item = data;
    $scope.item.tempCount = "";
    $scope.item.isRestock = false;
    //$rootScope.showAlert("Database item update success data:",data);
  };
  var syncFailure = function(data){
    $rootScope.showAlert("Database item update failure data:",data);
  };
  var getLogSuccess = function(data){
    //$rootScope.showAlert("Got Log Data:",data);
    $scope.logs = data.logs;
  };
  var getLogFailure = function(data){
    $rootScope.showAlert("Failed to get Log Data:",data);
  };
  $scope.showLogs = function(){
    userApi.getItemLogs($stateParams.groupId, $stateParams.categoryId, $scope.item.id).then(getLogSuccess,getLogFailure);
  };
  $scope.item = userApi.getItem($stateParams.groupId,$stateParams.categoryId,$stateParams.itemId);
  $scope.item.tempCount = "";
  $scope.item.isRestock = false;
  $scope.imgUrl = $rootScope.imgUrl;
  $scope.updateItemCount = function(){
    console.log("tempCount before userapi:" +  $scope.item.tempCount);
    console.log("isRestock before userapi:" +  $scope.item.isRestock);

    userApi.updateItemCount($stateParams.groupId, $stateParams.categoryId, $scope.item.id, $scope.item.tempCount, $scope.item.isRestock).then(syncSuccess,syncFailure);
  };
  
  //$rootScope.showAlert("info:",$scope.item);
})
.controller('AboutCtrl', function($scope, $stateParams,$rootScope,userApi,$location,$interval) {
    


})
.controller('UsersCtrl', function($scope, $stateParams,$rootScope,userApi,$location) {
    $scope.users = userApi.getUsers($stateParams.groupId);
        //$rootScope.showAlert("users data:",$scope.users);

})
;
