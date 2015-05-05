<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
	<title>Topups App</title>

	<!-- CSS -->
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css"> <!-- load bootstrap via cdn -->
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css"> <!-- load fontawesome -->
    <style>
        body        { padding-top:30px; }
        form        { padding-bottom:20px; }
        .sent    { padding-bottom:20px; }
    </style>

     <!-- JS -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.8/angular.min.js"></script> <!-- load angular -->
    
    <!-- ANGULAR -->
    <!-- all angular resources will be loaded from the /public folder -->
        <script src="js/controllers/mainCtrl.js"></script> <!-- load our controller -->
        <script src="js/services/sentService.js"></script> <!-- load our service -->
        <script src="js/app.js"></script> <!-- load our application -->

</head>

<!--Declare the entry point of our ng-app and Controllers -->
<body class="container" ng-app="sentApp" ng-controller="mainController">

<div class="col-md-8 col-md-offset-2">
 <!-- PAGE TITLE =============================================== -->
    <div class="page-header">
        <h2>Topups App</h2>
        <h4>Safaricom and Airtel</h4>
    </div>
    
    <!-- NEW COMMENT FORM =============================================== -->
    <form ng-submit="submitTopup()"> <!-- ng-submit will disable the default form action and use our function -->
    
        <!-- Amount -->
        <div class="form-group">
            <input type="text" class="form-control input-sm" name="amount" ng-model="sentData.amount" placeholder="50">
        </div>
    
        <!-- Phone Number -->
        <div class="form-group">
            <input type="text" class="form-control input-lg" name="phoneNumber" ng-model="sentData.phoneNumber" placeholder="0722123456,0733450451">
        </div>
    
        <!-- SUBMIT BUTTON -->
        <div class="form-group text-right">   
            <button type="submit" class="btn btn-primary btn-lg">Send Airtime</button>
        </div>
    </form>

<!-- LOADING ICON =============================================== -->
    <!-- show loading icon if the loading variable is set to true -->
    <p class="text-center" ng-show="loading"><span class="fa fa-meh-o fa-5x fa-spin"></span></p>
    
    <!-- THE COMMENTS =============================================== -->
    <!-- hide these comments if the loading variable is true -->
   		 <span>Search: <input ng-model="query"></span>
   		 <br/>
   		 <br/>
    <div class="sent" ng-hide="loading" ng-repeat="sent in sents | filter:query | orderBy:'created_at':true">
    	
    	<table class="table table-striped table-bordered">
    	<thead>
    		<tr>
    			<td>Status</td>
	            <td>Amount</td>
	            <td>PhoneNumber</td>
	            <td>Sent On</td>
    		</tr>
    	</thead>
    	<tbody>
    		<tr>
    			<td>{{ sent.status }}</td>
	            <td>{{ sent.amount }}</td>
	            <td>{{ sent.phoneNumber }}</td>
	            <td>{{ sent.created_at }}</td>
	            <td><a href="#" ng-click="deleteTopup(sent.id)" class="text-muted">Delete</a></td>
    		</tr>
    	</tbody>
       <!-- <h5><span>Topups: {{ sent.status }} </span> <span>Amount : {{ sent.amount }}</span> <span>To: {{ sent.phoneNumber }} </span> <span>On: {{ sent.created_at }}</span></h5>
        <p><a href="#" ng-click="deleteTopup(sent.id)" class="text-muted">Delete</a></p> -->

        </table>
    </div>




</div>

</body>
</html>