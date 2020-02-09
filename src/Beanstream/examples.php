<pre><?php
//BEANSTREAM REST API SDK USAGE EXAMPLES	
//get Beanstream Gateway
require_once 'Gateway.php';

//init api settings (beanstream dashboard > administration > account settings > order settings)
$merchant_id = ''; //INSERT MERCHANT ID (must be a 9 digit string)
$api_key = ''; //INSERT API ACCESS PASSCODE
$api_version = 'v1'; //default
$platform = 'api'; //default


//generate a random order number, and set a default $amount (only used for example functions)
$order_number = bin2hex(substr(md5(mt_rand() . microtime()),0 ,22));
$amount = 1.00;

// enable error reporting for debugging purposes 
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

//init new Beanstream Gateway object
$beanstream = new \Beanstream\Gateway($merchant_id, $api_key, $platform, $api_version);


//example array data for use in example functions

//example payment transaction data
$payment_data = array(
        'order_number' => $order_number,
        'amount' => $amount,
        'payment_method' => 'card',
        'card' => array(
	        'name' => 'Mr. Sam Sitherson',
            'number' => '4030000010001234',
            'expiry_month' => '07',
            'expiry_year' => '22',
            'cvd' => '123'
        ),
	    'billing' => array(
	        'name' => 'Mr. Sam Sitherson',
	        'email_address' => 'samsmitherson@email.com',
	        'phone_number' => '1234567800',
	        'address_line1' => 'Smithwerson St.',
	        'city' => 'Smithers',
	        'province' => 'BC',
	        'postal_code' => 'V8J9I5',
	        'country' => 'CA'
		),
	    'shipping' => array(
	        'name' => 'Shipping Name',
	        'email_address' => 'samsmitherson@email.com',
	        'phone_number' => '1234567890',
	        'address_line1' => '789-123 Shipping St.',
	        'city' => 'Shippingsville',
	        'province' => 'BC',
	        'postal_code' => 'V8J9I5',
	        'country' => 'CA'
		)
);


//example profile function test vars
$profile_id = ''; //enter a profile_id to get a profile
$card_id = '1'; //default card, 1-based index

//example profile data to create
$profile_data = array(
    'billing' => array(
        'name' => 'Profile Billing Name',
        'email_address' => 'email@email.com',
        'phone_number' => '1234567890',
        'address_line1' => '456-123 Shipping St.',
        'city' => 'Shippingville',
        'province' => 'BC',
        'postal_code' => 'V8J9I5',
        'country' => 'CA'
		)
	);

//example card data to add to a profile
$card_data = array(
    'card' => array(
        'name' => 'Test Testerson',
        'number' => '4030000010001234',
        'expiry_month' => '07',
        'expiry_year' => '22',
        'cvd' => '123'
		)
	);

//example unreferenced return data
$return_data = array(
		'order_number' => $order_number,
        'amount' => $amount,
        'payment_method' => 'card',
        'card' => array(
            'name' => 'Mr. Refund Testerson',
            'number' => '4030000010001234',
            'expiry_month' => '07',
            'expiry_year' => '22',
            'cvd' => '123'
        )
	);
	
//example profile payment data
$profile_payment_data = array(
    'order_number' => $order_number, 
    'amount' => $amount
	);


//example data to simulate getting a legato token
$legato_token_data = array(
        'number' => '4030000010001234',
        'expiry_month' => '07',
        'expiry_year' => '22',
        'cvd' => '123'
    	);	

//example legato payment data
//name is actually insterted into ['token']['name'] 
$legato_payment_data = array(
        'order_number' => $order_number,
        'amount' => $amount,
        'name' => 'Mrs. Legato Testerson'	
	);


//example search criteria data
$search_criteria = array(
     'name' => 'TransHistoryMinimal', // or 'Search',
     'start_date' => '1999-01-01T00:00:00',
     'end_date' => '2016-01-01T23:59:59',   
     'start_row' => '1',
     'end_row' => '15000',
     'criteria' => array(
         'field' => '1',
         'operator' => '%3E',
         'value' => '1000000'
     )
);

//example payment function test vars
$transaction_id = ''; //enter a transaction id to use in below functions
$complete = TRUE;


//batch processing api example data
$batch_example_data = array(
	'process_date' => '20200212', //required, YYYYMMDD format, up to 30 days in the future
	'process_now' => 0, //optional, only for cc data, does not affect EFT/ACH/SEPA/BACS/etc.. (note: processing hard cut-off time for EFT/ACH batches is 11AM PST/2PM EST)
	'addendum'=>'test addendum' //optional, An addendum note, or data associated with the uploaded batch file. See your merchant-specific documentation for usage and specifications.
);

$batch_example_file_cc = realpath('./examplebatch_cc.csv'); //path to batch file (as .csv)
$batch_example_file_eft = realpath('./examplebatch_eft.txt'); //path to batch file (as .txt)
$batch_example_file_ach = realpath('./examplebatch_ach'); //path to batch file (with no extension)

//REQUEST EXAMPLE FUNCTIONS BELOW
//UNCOMMENT THE ONES YOU WOULD LIKE TO TEST 

try {

	//**** PAYMENTS EXAMPLES
	
	//make a credit card payment
	//$result = $beanstream->payments()->makeCardPayment($payment_data, $complete);
	//$transaction_id = $result['id'];
	
	//complete a PA
	//$result = $beanstream->payments()->complete($transaction_id, $amount, $order_number);
	
	//cash payment
	//$result = $beanstream->payments()->makeCashPayment($payment_data);
	
	//cheque payment
	//$result = $beanstream->payments()->makeChequePayment($payment_data);
	
	//return a payment
	//$result = $beanstream->payments()->returnPayment($transaction_id, $amount, $order_number);
	
	//return a payment (unreferenced)
	//$result = $beanstream->payments()->unreferencedReturn($return_data);
	
	//void a payment
	//$result = $beanstream->payments()->voidPayment($transaction_id, $amount);
	
	//simulate legato token payment (SHOULD NEVER BE CALLED IN PRODUCTION)
	//$token = $beanstream->payments()->getTokenTest($legato_token_data);
	
	//make legato payment with above token
	//$result = $beanstream->payments()->makeLegatoTokenPayment($token, $legato_payment_data, $complete);
	
	
	
	
	//**** PROFILES EXAMPLES

	//create a profile
	//$profile_id = $beanstream->profiles()->createProfile($profile_data);
	
	//get a profile based on a profile cust code
	//$result = $beanstream->profiles()->getProfile($profile_id);
	
	//update a profile based on a profile cust code
	//$result = $beanstream->profiles()->updateProfile($profile_id, $profile_data);
	
	//delete a profile
	//$result = $beanstream->profiles()->deleteProfile($profile_id);
	
	//add a card to a profile
	//$result = $beanstream->profiles()->addCard($profile_id, $card_data);

		
	//profile payment
	//$result = $beanstream->payments()->makeProfilePayment($profile_id, $card_id, $profile_payment_data, $complete);
	//$transaction_id = $result['id'];
	
	//complete a profile payment
	//$result = $beanstream->payments()->complete($transaction_id, $profile_payment_data['amount'], $order_number);
	

	//get all cards in profile
	//$result = $beanstream->profiles()->getCards($profile_id);
	
	//update a specfic card in a profile
	//$result = $beanstream->profiles()->updateCard($profile_id, $card_id, $card_data);
	
	//delete a specfic card in a profile
	//$result = $beanstream->profiles()->deleteCard($profile_id, $card_id);
	
	
	
	
	//**** REPORTING EXAMPLES
	
	//search for transactions that match criteria //DOESN'T RETURN ALL TX (ie. VP/VR)?
	//$result = $beanstream->reporting()->getTransactions($search_criteria);
	
	//get a specific transaction
	//$result = $beanstream->reporting()->getTransaction($transaction_id);




	//**** BATCH PROCESSING EXAMPLES
	
	//upload a credit card batch
	//$result = $beanstream->batchProcessing()->uploadBatchFile($batch_example_data, $batch_example_file_cc);
	
	//upload an eft batch
	//$result = $beanstream->batchProcessing()->uploadBatchFile($batch_example_data, $batch_example_file_eft);
	
	//upload an ach batch
	//$result = $beanstream->batchProcessing()->uploadBatchFile($batch_example_data, $batch_example_file_ach);

	
	
	//display result
	is_null($result)?:print_r($result);






} catch (\Beanstream\Exception $e) {
    /*
     * Handle transaction error, $e->code can be checked for a
     * specific error, e.g. 211 corresponds to transaction being
     * DECLINED, 314 - to missing or invalid payment information
     * etc.
     */
     
     print_r($e);
     
}
?></pre>