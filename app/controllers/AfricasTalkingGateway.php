<?php
/*

  # COPYRIGHT (C) 2014 AFRICASTALKING LTD <www.africastalking.com>                                                   
 
 AFRICAStALKING SMS GATEWAY CLASS IS A FREE SOFTWARE IE. CAN BE MODIFIED AND/OR REDISTRIBUTED                        
 UNDER THE TERMS OF GNU GENERAL PUBLIC LICENCES AS PUBLISHED BY THE                                                 
 FREE SOFTWARE FOUNDATION VERSION 3 OR ANY LATER VERSION                                                            
 
 THE CLASS IS DISTRIBUTED ON 'AS IS' BASIS WITHOUT ANY WARRANTY, INCLUDING BUT NOT LIMITED TO                       
 THE IMPLIED WARRANTY OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.                     
 IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,            
 WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE       
 OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 
 */

class AfricasTalkingGatewayException extends Exception{}

class AfricasTalkingGateway
{
  protected $_username;
  protected $_apiKey;
  
  protected $_requestBody;
  protected $_requestUrl;
  
  protected $_responseBody;
  protected $_responseInfo;
  
  const SMS_URL          = 'https://api.africastalking.com/version1/messaging';
  const VOICE_URL        = 'https://voice.africastalking.com';
  const USER_DATA_URL    = 'https://api.africastalking.com/version1/user';
  const SUBSCRIPTION_URL = 'https://api.africastalking.com/version1/subscription';
  const AIRTIME_URL = 'https://api.africastalking.com/version1/airtime';

  /*
   * Turn this on if you run into problems. It will print the raw HTTP response from our server
   */
  const Debug = False;
  
  const HTTP_CODE_OK      = 200;
  const HTTP_CODE_CREATED = 201;
  
  public function __construct($username_, $apiKey_)
  {
    $this->_username = $username_;
    $this->_apiKey   = $apiKey_;
    
    $this->_requestBody = null;
    $this->_requestUrl  = null;
    
    $this->_responseBody = null;
    $this->_responseInfo = null;    
  }
  
  /*
		 #  to_ parameter contains the recipients of the message. Phone numbers should be comma separated for may receivers.
		 		 The format is "+254700XXXYYY, +25433YYYZZZ"
		 
		 # message parameter contains the message to be sent in a string format
	 	
	 	# The from_ parameter should be populated with the value of a shortcode or alphanumeric that is 
		   registered with us 
	 	
	 	# bulkSMSMode  will be used by the Mobile Service Provider to determine who gets billed for a message sent using a Mobile-Terminated ShortCode.
		   The default value is 1 (which means that you, the sender, gets charged). 
	 	  This parameter will be ignored for messages sent using alphanumerics or Mobile-Originated shortcodes.
	
		 * Other options can be passed into the options_ map. These are:
	 	# - keyword : Specify which subscription product to use to send messages for premium rated short codes
		 # - linkId  : Specified when responding to an on-demand content request on a premium rated short code
		 # - retryDurationInHours : Specify the number of hours an option should be retried before it is dropped incase of failure
	 	*/
  public function sendMessage($to_, $message_, $from_ = null, $bulkSMSMode_ = 1, Array $options_ = array())
  {    
    if ( strlen($to_) == 0 || strlen($message_) == 0 ) {
      throw new AfricasTalkingGatewayException('Please supply both to and message parameters');
    }
    
    $params = array(
		    'username' => $this->_username,
		    'to'=> $to_,
		    'message'  => $message_,
		    );
    
    if ( $from_ !== null ) {
      $params['from']        = $from_;
      $params['bulkSMSMode'] = $bulkSMSMode_;
    }
    
    //This contains a list of parameters that can be passed in $options_ parameter
    if ( count($options_) > 0 ) {
      $allowedKeys = array (
			    'enqueue',
			    'keyword',
			    'linkId',
			    'retryDurationInHours'
			    );
			    
			    //Check whether data has been passed in options_ parameter
      foreach ( $options_ as $key => $value ) {
							if ( in_array($key, $allowedKeys) && strlen($value) > 0 ) {
	  					$params[$key] = $value;
							} else {
	  					throw new AfricasTalkingGatewayException("Invalid key in options array: [$key]");
							}
      }
    }
    
    $this->_requestUrl  = self::SMS_URL;
    $this->_requestBody = http_build_query($params, '', '&');
    $this->execute('POST');
    
    if ( $this->_responseInfo['http_code'] != self::HTTP_CODE_CREATED ) {
      throw new AfricasTalkingGatewayException($this->_responseBody);
    }
    
    return $this->_responseBody->SMSMessageData->Recipients;
  }
  
  
  /*
     # from_ parameter contains the phone number the call will originate from. It must be registered with us.
     
     # to_ parameter has the phone numbers to be called in a string format. They should be comma separated for man numbers.
       eg. "+254700XXXYYY, +254733YYYZZZ"
     */
  public function call($from_, $to_)
  {
    if ( strlen($from_) == 0 || strlen($to_) == 0 ) {
      throw new AfricasTalkingGatewayException('Please supply both from and to parameters');
    }
    
    $params = array(
		    'username' => $this->_username,
		    'from'     => $from_,
		    'to'       => $to_
		    );
    
    $this->_requestUrl  = self::VOICE_URL . "/call";
    $this->_requestBody = http_build_query($params, '', '&');
    $this->execute('POST');
    
    if ( $this->_responseInfo['http_code'] != self::HTTP_CODE_CREATED ) {
      throw new AfricasTalkingGatewayException($this->_responseBody);
    }
  }
  
  public function fetchMessages($lastReceivedId_)
  {
    $username = $this->_username;
    $this->_requestUrl = self::SMS_URL.'?username='.$username.'&lastReceivedId='. intval($lastReceivedId_);
    
    $this->execute('GET');      
    if ( $this->_responseInfo['http_code'] != self::HTTP_CODE_OK ) {
      throw new AfricasTalkingGatewayException($this->_responseBody);
    }
    return $this->_responseBody->SMSMessageData->Messages;
  }
  
  
  /*
    	  This method fetches numbers subscribed to the given shortcode and keyword.
    	  It fetches then in batches and the lastReceivedId_ parameter is used to check the id of the last number received so the system
    	  may know where to start.
    	  
    	  phoneNumber_ parameter contains the phone number to be removed from the subscription list of the shortcode and keyword provided
  */
  public function fetchPremiumSubscriptions($shortCode_, $keyword_, $lastReceivedId_ = 0)
  {
    $username = $this->_username;
    $this->_requestUrl  = self::SUBSCRIPTION_URL.'?username='.$username.'&shortCode='.$shortCode_;
    $this->_requestUrl .= '&keyword='.$keyword_.'&lastReceivedId='.intval($lastReceivedId_);
    
    $this->execute('GET');      
    if ( $this->_responseInfo['http_code'] != self::HTTP_CODE_OK ) {
      throw new AfricasTalkingGatewayException($this->_responseBody);
    }
    
    return $this->_responseBody->SubscriptionData->Subscriptions;
  }

  public function createSubscription($phoneNumber_, $shortCode_, $keyword_)
  {
  	/*
    	 * This method is used to add a number to a subscription service.
    	 * phoneNumber_ parameter contains the phone number to be added to the subscription list of the shortcode and keyword provided
    	 */
    	
    if ( strlen($phoneNumber_) == 0 || 
	 strlen($shortCode_) == 0   ||
	 strlen($keyword_) == 0 ) {
      throw new AfricasTalkingGatewayException('Please supply phoneNumber, shortCode and keyword');
    }
    
    $params = array(
		    'username'    => $this->_username,
		    'phoneNumber' => $phoneNumber_,
		    'shortCode'   => $shortCode_,
		    'keyword'     => $keyword_
		    );
    
    $this->_requestUrl  = self::SUBSCRIPTION_URL."/create";
    $this->_requestBody = http_build_query($params, '', '&');
    
    $this->execute('POST');
    
    if ( $this->_responseInfo['http_code'] != self::HTTP_CODE_CREATED ) {
      throw new AfricasTalkingGatewayException($this->_responseBody);
    }
    
    return $this->_responseBody->status;
  }

  public function deleteSubscription($phoneNumber_, $shortCode_, $keyword_)
  {
				/*
    	 * This method is used to delete a number to a subscription service.
    	 * phoneNumber_ parameter contains the phone number to be removed from the subscription list of the shortcode and keyword provided
    	 */  	
  	
    if ( strlen($phoneNumber_) == 0 || 
	 strlen($shortCode_) == 0   ||
	 strlen($keyword_) == 0 ) {
      throw new AfricasTalkingGatewayException('Please supply phoneNumber, shortCode and keyword');
    }
    
    $params = array(
		    'username'    => $this->_username,
		    'phoneNumber' => $phoneNumber_,
		    'shortCode'   => $shortCode_,
		    'keyword'     => $keyword_
		    );
    
    $this->_requestUrl  = self::SUBSCRIPTION_URL."/delete";
    $this->_requestBody = http_build_query($params, '', '&');
    $this->execute('POST');
    
    if ( $this->_responseInfo['http_code'] != self::HTTP_CODE_CREATED ) {
      throw new AfricasTalkingGatewayException($this->_responseBody);
    }
    
    return $this->_responseBody->status;
  }

  
  public function getUserData()
  {
    $username = $this->_username;
    $this->_requestUrl = self::USER_DATA_URL.'?username='.$username;
    $this->execute('GET');
    
    if ( $this->_responseInfo['http_code'] != self::HTTP_CODE_OK ) {
      throw new AfricasTalkingGatewayException($this->_responseBody);
    }
    
    return $this->_responseBody->UserData;
  }

		/*
		   
		*/
  public function uploadMediaFile($url_) {
  	$params = array("username" => $this->_username, "url"=>$url_);
  	$this->_requestBody = http_build_query($params, '', '&');
  	$this->_requestUrl = self::VOICE_URL . "/mediaUpload";
  	$this->execute('POST');
  	if($this->_responseInfo['http_code'] == self::HTTP_CODE_CREATED)
  		return $this->_responseBody;
  	$error = json_decode($this->_responseBody);
  	if(isset($error->ErrorMessage))
  	 throw new AfricasTalkingGatewayException($error->ErrorMessage);
  	throw new AfricasTalkingGatewayException($this->_responseBody);
  }
  
  
  /*
    	  This method gets the number of queued calls for a certain number passed.
    	  The queueName parameter notifies the system what the queue to check. It is null by default
  */
  public function getNumQueuedCalls($phoneNumber_, $queueName = null) 
  {  	
  	$this->_requestUrl = self::VOICE_URL . "/queueStatus";
  	$params = array("username"=>$_username, "phoneNumber"=>$phoneNumber);
  	if($queueName !== null)
  		$params['queueName'] = $queueName;
  	$this->_requestBody = http_build_query($params, '', '&');
  	$this->execute('POST');
  	if($this->_responseInfo['http_code'] == self::HTTP_CODE_OK)
  		return $this->_responseBody->NumQueued;
  	$error = json_decode($this->_responseBody);
  	if(isset($error->ErrorMessage))
  	 throw new AfricasTalkingGatewayException($error->ErrorMessage);
  	throw new AfricasTalkingGatewayException($this->_responseBody);
  }
  
  
  /*
    	  This method sends airtime to recipient numbers passes. The expected recipient_ string format is:
    	  [{"phoneNumber":"+254700XXXYYY","amount":"KES X"},{"phoneNumber":"+254700YYYZZZ","amount":"KES Y"}]
  */
  public function sendAirtime($recipients) 
  {  	
  	$this->_requestUrl = self::AIRTIME_URL . "/send";
  	$params = array("username"=>$this->_username, "recipients"=>$recipients);
  	$this->_requestBody = http_build_query($params, '', '&');
  	$this->execute('POST');
  	if($this->_responseInfo['http_code'] == self::HTTP_CODE_CREATED and count($this->_responseBody->responses) > 0) {
  		return $this->_responseBody->responses;
  	}
  	throw new AfricasTalkingGatewayException($this->_responseBody->errorMessage);
  }

  protected function execute ($verb_)
  {
    $ch = curl_init();
    try {
      switch (strtoupper($verb_)){
      case 'GET':
	$this->executeGet($ch);
	break;
      case 'POST':
	$this->executePost($ch);
	break;
      default:
	throw new InvalidArgumentException('Current verb (' . $verb_ . ') is not implemented.');
      }
    }
    catch (InvalidArgumentException $e){
      curl_close($ch);
      throw $e;
    }
    catch (Exception $e){
      curl_close($ch);
      throw $e;
    }
  }
  
  protected function doExecute (&$curlHandle_)
  {
    $this->setCurlOpts($curlHandle_);
    $responseBody = curl_exec($curlHandle_);
    
    if ( self::Debug ) {
      echo "Full response: ". print_r($responseBody, true)."\n";
    }
    
    $this->_responseInfo = curl_getinfo($curlHandle_);
    
    if ( $this->_responseInfo['http_code'] == self::HTTP_CODE_OK ||
	 $this->_responseInfo['http_code'] == self::HTTP_CODE_CREATED ) {
      
      $this->_responseBody = json_decode($responseBody);
      
    } else {
      
      $this->_responseBody = $responseBody;
      
    }
    curl_close($curlHandle_);
  }
  
  protected function executeGet ($ch_)
  {
    $this->doExecute($ch_);
  }
  
  protected function executePost ($ch_)
  {
    curl_setopt($ch_, CURLOPT_POSTFIELDS, $this->_requestBody);
    curl_setopt($ch_, CURLOPT_POST, 1);
    $this->doExecute($ch_);
  }
  
  protected function setCurlOpts (&$curlHandle_)
  {
    curl_setopt($curlHandle_, CURLOPT_TIMEOUT, 60);
    curl_setopt($curlHandle_, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curlHandle_, CURLOPT_URL, $this->_requestUrl);
    curl_setopt($curlHandle_, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curlHandle_, CURLOPT_HTTPHEADER, array ('Accept: application/json',
							 'apikey: ' . $this->_apiKey));
  }
}