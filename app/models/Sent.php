<?php 

class Sent extends Eloquent {

	protected $fillable = array('status','amount', 'phoneNumber', 'requestId');	
}

 ?>