<?php  

class SentTableSeeder extends Seeder{
	public function run()
	{
		DB::table('sents') ->delete();

		Sent::create(array(
			'status' => 'Failed',
			'amount' => 'KES 600.0000',
			'phoneNumber' => '+254787235065',
			'requestId' => 'None',
		));

		Sent::create(array(
			'status' => 'Sent',
			'amount' => 'KES 100.0000',
			'phoneNumber' => '+254722235065',
			'requestId' => 'ATQid_729b7453069ae54bdd7fe62d',
		));

		Sent::create(array(
			'status' => 'Sent',
			'amount' => 'KES 50.0000',
			'phoneNumber' => '+254713254088',
			'requestId' => 'ATQid_72rt7453069ae54bdd7fe62d',
		));

		Sent::create(array(
			'status' => 'Failed',
			'amount' => 'KES 200.0000',
			'phoneNumber' => '+254723711822',
			'requestId' => 'ATQid_732b7453069ae54bdd7fe62d',
		));
	}
	
}

?>