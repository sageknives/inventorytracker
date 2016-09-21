// Ionic Starter App

// angular.module is a global place for creating, registering and retrieving Angular modules
// 'starter' is the name of this angular module example (also set in a <body> attribute in index.html)
// the 2nd parameter is an array of 'requires'
// 'starter.controllers' is found in controllers.js
angular.module('starter', [
  'ionic','ionic.service.core', 
  'starter.controllers', 'ionic.service.push',
  'ngCordova'
  ])
.config(['$ionicAppProvider', function($ionicAppProvider) {
  $ionicAppProvider.identify({
    app_id: '7caae591',
    api_key: '95fb264e37ba9b175fa2578873d4ec89a942a31d6e73095b',
    dev_push: false
  });
}])
.run(function($ionicPlatform,$state,$ionicPopup,$ionicPush) {
  
  $ionicPlatform.ready(function() {
    var io = Ionic.io();
    var user = Ionic.User.current();
    if(ionic.Platform.isAndroid()){
      user.set('platform','android');
    }else if(ionic.Platform.isIOS()){
      user.set('platform','ios');  
    }else{
      user.set('platform',ionic.Platform.platform()); 
    }
    /*var push = new Ionic.Push({
      "debug": true
    });*/
    var push = new Ionic.Push({
      "debug": true,
      "canRunActionsOnWake": true //Can run actions outside the app,
    });
 
    push.register(function(token) {
      console.log("My Device token:");
      console.log(token.token);
      push.saveToken(token);  // persist the token in the Ionic Platform
      user.set('token', token.token);
      user.save();
    });
    
    // Hide the accessory bar by default (remove this to show the accessory bar above the keyboard
    // for form inputs)
    if (window.cordova && window.cordova.plugins.Keyboard) {
      cordova.plugins.Keyboard.hideKeyboardAccessoryBar(true);
      cordova.plugins.Keyboard.disableScroll(true);

    }
    if (window.StatusBar) {
      // org.apache.cordova.statusbar required
      StatusBar.styleDefault();
    }
  });
})

.config(function($stateProvider, $urlRouterProvider) {
  $stateProvider

    .state('app', {
    url: '/app',
    abstract: true,
    templateUrl: 'templates/menu.html',
    controller: 'AppCtrl'
  })
  .state('app.loginScreen', {
      url: '/loginscreen',
      views: {
        'menuContent': {
          templateUrl: 'templates/loginscreen.html',
          controller: 'LoginScreenCtrl'
        }
      }
    })
  .state('app.register', {
      url: '/register',
      views: {
        'menuContent': {
          templateUrl: 'templates/register.html',
          controller: 'RegisterCtrl'
        }
      }
    })
  .state('app.about', {
    url: '/about',
    views: {
      'menuContent': {
        templateUrl: 'templates/about.html',
        controller: 'AboutCtrl'
      }
    }
  })

  .state('app.browse', {
      url: '/browse',
      views: {
        'menuContent': {
          templateUrl: 'templates/browse.html'
        }
      }
    })
    .state('app.groups', {
      url: '/groups',
      views: {
        'menuContent': {
          templateUrl: 'templates/groups.html',
          controller: 'GroupsCtrl'
        }
      }
    })

  .state('app.group', {
    url: '/groups/:groupId',
    views: {
      'menuContent': {
        templateUrl: 'templates/group.html',
        controller: 'GroupCtrl'
      }
    }
  })
  .state('app.users', {
    url: '/groups/:groupId/users',
    views: {
      'menuContent': {
        templateUrl: 'templates/users.html',
        controller: 'UsersCtrl'
      }
    }
  })
  .state('app.category', {
    url: '/groups/:groupId/categories/:categoryId',
    views: {
      'menuContent': {
        templateUrl: 'templates/category.html',
        controller: 'CategoryCtrl'
      }
    }
  })
  .state('app.item', {
    url: '/groups/:groupId/categories/:categoryId/items/:itemId',
    views: {
      'menuContent': {
        templateUrl: 'templates/item.html',
        controller: 'ItemCtrl'
      }
    }
  });
  // if none of the above states are matched, use this as the fallback
  $urlRouterProvider.otherwise('/app/loginscreen');
});
