angular.module('mainCtrl',[])
//Pass in the Sent Service into the mainController
.controller('mainController', function($scope, $http, Sent){
	//create the object that holds all the sentData
	$scope.sentData = {};

	//Have a loading variable that shows a spinning loading icon
	$scope.loading = true;

	//first get the already sent topups and bind to the scope
	//use our service to accomplish this
	
	//Get all Sent TopUps
	Sent.get().success(function(data){
		$scope.sents = data;
		$scope.loading = false;
	});

	//SEND topups to AT gateway and Persist
	$scope.submitTopup = function(){
		$scope.loading = true;

		//Send the TopUp and Save the response
		//Pass the sentData from the form
		//Use the send function we created in our service
		Sent.send($scope.sentData)
			.success(function(data){
				//If success we refresh the topups list
				Sent.get().success(function(getData){
					$scope.sents = getData;
					$scope.loading = false;
				});
			})
			.error(function(data){
				console.log(data);
			});
	};

	//Delete any TopUps Info we dont need
	$scope.deleteTopup = function(id){
		$scope.loading = true;
		// Use the delete function that we have in our service
		Sent.destroy(id)
			.success(function(data){
				//For a success response we refresh the topups list
				Sent.get()
					.success(function(getData){
						$scope.sents = getData;
						$scope.loading = false;
					});
			});
	};

});