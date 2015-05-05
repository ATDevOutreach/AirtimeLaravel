<?php
// 1. Import the helper Gateway class
require_once('AfricasTalkingGateway.php');


class SentController extends \BaseController {

	/**
	 * Send back all the topups as JSON
	 *
	 * @return Response
	 */
	public function index()
	{
		// get All
		return Response::json(Sent::get());
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		// 2. Specify your login credentials
		 $api = '680fdfa9eae83b8649c7d3884ad0679b827d7393140bc68b85e0e5b0a31dcb68';
		 $user = 'YourAPIUsername' ;
		 //get input
		 $amount = Input::get('amount');
		 $phone = Input::get('phoneNumber');
		//First Send Airtime
		try{
 		$gateway = new AfricasTalkingGateway($user, $api);
		 
		 //before sending, JSON encode
		 //explode by comma if more that one number exists 
		 $phoneArr = explode(',', $phone);
		 $recipients = array();

		 foreach($phoneArr as $phone) {
		  $recipients[] = array("phoneNumber"=>$phone, "amount"=>'KES ' . $amount);
		 }
		 $rec = json_encode($recipients);

		 //Now send Airtime
		 $results = $gateway->sendAirtime($rec);
		 }
		 catch (AfricasTalkingGatewayException $e){
		  echo $e->getMessage();
		 }

		 foreach($results as $result) {
		//Now Persist the results
		Sent::create(array(
			'status' => $result->status,
			'amount' => $result->amount,
			'phoneNumber' => $result->phoneNumber,
			'requestId' => $result->requestId,
		));

		//Display once storing is successful
		return Response::json(array('success' => true));
	}

	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//Destroy Transactions
		Sent::destroy($id);

		return Response::json(array('success' => true));
	}


}
