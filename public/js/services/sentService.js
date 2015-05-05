angular.module('sentService', [])
.factory('Sent', function($http){
	return{
		//get all airtime TopUps
		get : function(){
			return $http.get('api/sents');
		},

		show : function(id) {
				return $http.get('api/sents/' + id);

		},

		//send a topup (pass in topUp data)
		send : function(sentData){
			return $http({
				method: 'POST',
				url: '/api/sents',
				headers: {'Content-Type' : 'application/x-www-form-urlencoded'},
				data: $.param(sentData)
			});
		},

		//destroy a comment
		destroy : function(id){
			return $http.delete('/api/sents'+id);
		}
	}
})