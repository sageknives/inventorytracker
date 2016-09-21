angular.module('starter')
.factory('userApi', function($http, $q, $ionicLoading) {
	var myToken = {};
    var groupsData = {};
    var userData = {};

	var base_url = 'http://sagegatzke.com/inventorytracker/';
 
    function register(username, password){
        console.log("in register");
        var deferred = $q.defer();
        $ionicLoading.show();

        $http.post(base_url + 'register.php/', {'username':username,'password':password})
            .success(function(response){
                console.log("in success");
                $ionicLoading.hide();
                deferred.resolve(response);
            })
            .error(function(data){
                console.log("in error");
                console.log(data);
                $ionicLoading.hide();
                deferred.reject();
            });
        return deferred.promise;
    };
    function login(username, password, token, platform){

        console.log("in login");
        var deferred = $q.defer();
        $ionicLoading.show();

        $http.post(base_url + 'login.php/', {'username':username,'password':password,'token':token,'platform':platform})
            .success(function(response){
            	console.log("in success");
                $ionicLoading.hide();
                deferred.resolve(response);
                console.log("saving token");
                myToken = response.secToken;
                userData = response.user;
                console.log(response);

            })
            .error(function(data){
            	console.log("in error");
            	console.log(data);
            	$ionicLoading.hide();
                //deferred.resolve("fun");
                deferred.reject();
            });


        return deferred.promise;

    };
    function clone(){
        console.log("in clone");
        var deferred = $q.defer();
        $ionicLoading.show();

        $http.post(base_url + 'groups.php/', {'token':myToken,'timeStamp':'now'})
            .success(function(response){
                console.log("in success");
                groupsData = response.groups;
                $ionicLoading.hide();
                deferred.resolve(response);
            })
            .error(function(data){
                console.log("in error");
                $ionicLoading.hide();
                deferred.reject();
            });
        return deferred.promise;
    };
    function getGroup(groupId){ 
        return groupsData[groupId];
    };
    function getCategory(groupId,categoryId){ 
        return groupsData[groupId].categories[categoryId];
    };
    function getItem(groupId,categoryId,itemId){ 
        return groupsData[groupId].categories[categoryId].items[itemId];
    };
    function getUsers(groupId){ 
        return groupsData[groupId].users;
    };

    function updateItemCount(groupId, categoryId, itemId, itemCount, isRestock){
        console.log("in updateItemCount");
        console.log("itemId :" + itemId);
        console.log("itemCount:" + itemCount);
        var deferred = $q.defer();
        $ionicLoading.show();

        $http.post(base_url + 'items.php/', {'token':myToken,'itemId':itemId,'itemCount':itemCount,'isRestock':isRestock,'objectType':3,'propertyType':6,'cmd':"updateCount"})
            .success(function(response){
                console.log("in success");
                groupsData[groupId].categories[categoryId].items[itemId].lastUpdated = response.lastUpdated;
                groupsData[groupId].categories[categoryId].items[itemId].count = itemCount;
                $ionicLoading.hide();
                deferred.resolve(groupsData[groupId].categories[categoryId].items[itemId]);
            })
            .error(function(data){
                console.log("in error");
                $ionicLoading.hide();
                deferred.reject();
            });
        return deferred.promise;
    };

    function getItemLogs(groupId, categoryId, itemId){
        console.log("in get item logs");
        console.log("itemId :" + itemId);
        var deferred = $q.defer();
        $ionicLoading.show();

        $http.post(base_url + 'items.php/', {'token':myToken,'itemId':itemId,'cmd':'getLogs'})
            .success(function(response){
                console.log("in success");
                groupsData[groupId].categories[categoryId].items[itemId].logs = response.logs;
                $ionicLoading.hide();
                deferred.resolve(response);
            })
            .error(function(data){
                console.log("in error");
                $ionicLoading.hide();
                deferred.reject();
            });
        return deferred.promise;
    };

    function sync(){
        console.log("in clone");
        var deferred = $q.defer();
        $ionicLoading.show();

        $http.post(base_url + 'groups.php/', {'token':myToken,'timeStamp':'now'})
            .success(function(response){
                console.log("in success");
                groupsData = response.groups;
                $ionicLoading.hide();
                deferred.resolve(response);
            })
            .error(function(data){
                console.log("in error");
                $ionicLoading.hide();
                deferred.reject();
            });
        return deferred.promise;
    };
    function getNewState(action,state){
        console.log("in new state");
        var deferred = $q.defer();
        $ionicLoading.show();

        $http.post(base_url + '/paygoservices.php', {'action':action,'token':myToken,'state':state})
            .success(function(response){
            	console.log("in action success");
            	console.log(JSON.stringify(response));
                $ionicLoading.hide();
                deferred.resolve(response);

            })
            .error(function(data){
            	console.log("in action error");
            	console.log(data);
            	$ionicLoading.hide();
                deferred.reject();
            });


        return deferred.promise;

    };
    function getOrderInfo(orderId){
        console.log("in new state");
        var deferred = $q.defer();
        $ionicLoading.show();

        $http.post(base_url + '/paygoservices.php', {'action':"carstate",'token':myToken,'state':"5"})
            .success(function(response){
                console.log("in action success");
                console.log(JSON.stringify(response));
                $ionicLoading.hide();
                deferred.resolve({
            title:"Thank you for parking with Paygo",
            message: "You've successfully checked out. Your credit card will be billed a total of $20, which includes parking fees and tip. You can change the tip amount at this time."
        });

            })
            .error(function(data){
                console.log("in action error");
                console.log(data);
                $ionicLoading.hide();
                deferred.reject();
            });


        return deferred.promise;
    };

	return {
		getState: function(stateIndex){
			return $http.get("http://sagegatzke.com/paygo/customer/state.php?state=" + stateIndex);
		},
		getNewState: getNewState,
		login: login,
        register: register,
        clone: clone,
        getOrderInfo:getOrderInfo,
		getGroup: getGroup,
        getUsers: getUsers,
        getCategory: getCategory,
        getItem: getItem,
        updateItemCount: updateItemCount,
        getItemLogs: getItemLogs
	}
})