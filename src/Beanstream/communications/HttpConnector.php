<?php 	namespace Beanstream;

/**
 * HTTPConnector class to handle HTTP requests to the REST API
 *  
 * @author Kevin Saliba
 */
class HttpConnector {

private $debug;
    /**
     * Base64 Encoded Auth String
     * 
     * @var string $_auth
     */
	protected $_auth;
	
    /**
     * Constructor
     * 
     * @param string $auth base64 encoded string to assign to the http header
     */	
	function __construct($auth) {
		//set auth for this connection only
		$this->_auth = $auth;
	}
	
	
    /**
     * processTransaction() function - Public facing function to send a request to an endpoint.
     * 
     * @param	string	$http_method HTTP method to use (defaults to GET if $data==null; defaults to PUT if $data!=null)
     * @param	string	$endpoint Incoming API Endpoint
	 * @param	array 	$data Data for POST requests, not needed for GETs
     * @access	public
	 * @return	array	Parsed API response from private request method
	 * 
     */
	public function processTransaction($http_method, $endpoint, $data) {
		//call internal request function
		return $this->request($endpoint, $http_method, $data);
	}
	
    /**
     * processBatchFile() function - Public facing function to send a request to an endpoint.
     * 
     * @param	string	$http_method HTTP method to use (defaults to GET if $data==null; defaults to PUT if $data!=null)
     * @param	string	$endpoint Incoming API Endpoint
	 * @param	array 	$data Data for POST requests, not needed for GETs
	 * @param	file 	$file Batch File to upload
     * @access	public
	 * @return	array	Parsed API response from private request method
	 * 
     */
	public function processBatchFile($endpoint, $http_method, $data, $file) {
		//call internal request function (with $file)
		return $this->request($endpoint, $http_method, $data, $file);
	}
	
	
	
	
	
	
	 /**
     * request() function - Internal function to send a request to an endpoint.
     * 
     * @param	string|null	$http_method HTTP method to use (defaults to GET if $data==null; defaults to PUT if $data!=null)
     * @param	string $url	Incoming API Endpoint
	 * @param	array|null	$data Data for POST requests, not needed for GETs
     * @access	private
	 * @return	array Parsed API response
	 * 
     * @throws ApiException
     * @throws ConnectorException
     */
    private function request($url, $http_method = NULL, $data = NULL, $file = NULL)
    {
    	//check to see if we have curl installed on the server 
        if ( ! extension_loaded('curl')) {
        	//no curl
            throw new ConnectorException('The cURL extension is required', 0);
        }
		
		//init the curl request
		//via endpoint to curl
        $req = curl_init($url);
		
		//check if the incoming $file is null (default) or not (indicates batch processing)
		if (is_null($file)) {

			//set request headers with encoded auth
			curl_setopt($req, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Authorization: Passcode '.$this->_auth,
			));
				
			//set http method in curl
			curl_setopt($req, CURLOPT_CUSTOMREQUEST, $http_method);

			//make sure incoming payload is good to go, set it
			if ( ! is_null($data)) {
				curl_setopt($req, CURLOPT_POSTFIELDS, json_encode($data));
			}

		} else {

			//set the batch file and headers, including auth
			$this->curl_custom_postfields($req, array('criteria' => json_encode($data)), array('data' => $file));		


		}
		
		//set other curl options        
        curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($req, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($req, CURLOPT_TIMEOUT, 30);
        
		//set http method
		//default to GET if data is null
		//default to POST if data is not null
        if (is_null($http_method)) {
            if (is_null($data)) {
                $http_method = 'GET';
            } else {
                $http_method = 'POST';
            }
        }
        
		//execute curl request
        $raw = curl_exec($req);

		
        if (false === $raw) { //make sure we got something back
            throw new ConnectorException(curl_error($req), -curl_errno($req));
        }
        
		//decode the result
        $res = json_decode($raw, true);
        if (is_null($res)) { //make sure the result is good to go
            throw new ConnectorException('Unexpected response format', 0);
        }
        
		//check for return errors from the API
        if (isset($res['code']) && 1 < $res['code'] && !($req['http_code'] >= 200 && $req['http_code'] < 300)) {
            throw new ApiException($res['message'], $res['code']);
        }
        
        return $res;
    }	
	
		
	/**
	* For safe multipart POST request for PHP5.3 ~ PHP 5.4.
	* http://php.net/manual/en/class.curlfile.php#115161
	* 
	* @param resource $ch cURL resource
	* @param array $assoc "name => value"
	* @param array $files "name => path"
	* @return bool
	*/
	private function curl_custom_postfields($ch, array $assoc = array(), array $files = array()) {
	   
	    // invalid characters for "name" and "filename"
	    static $disallow = array("\0", "\"", "\r", "\n");
   	    $guid = md5(mt_rand() . microtime());
	   
	    // build normal parameters
	    foreach ($assoc as $k => $v) {
	        $k = str_replace($disallow, "_", $k);
	        $body[] = implode("\r\n", array(
	            "Content-Disposition: form-data; name=\"{$k}\"",
                'Content-Type: application/json',
	            "",
	            // "{ 'process_date':'20181231' ,'process_now':1 }",
	            filter_var($v)
	        ));
	    }
	   
	    // build file parameters
	    foreach ($files as $k => $v) {
	        switch (true) {
	            case false === $v = realpath(filter_var($v)):
	            case !is_file($v):
	            case !is_readable($v):
	                break; // or return false, throw new InvalidArgumentException
	        }
	        $data = file_get_contents($v);
	        $k = str_replace($disallow, "_", $k);
	        $v = str_replace($disallow, "_", basename($v));
	        $body[] = implode("\r\n", array(
	            "Content-Disposition: form-data; name=\"{$k}\"; filename=\"".substr($guid, 0, 5)."_".$v ."\"",
	            "Content-Type: text/plain",
	            "",
	            $data
	        ));
	    }
	   
	    // generate safe boundary
		
	    do {
	        $boundary = "---------------------" . $guid;
	    } while (preg_grep("/{$boundary}/", $body));
	   
	    // add boundary for each parameters
	    array_walk($body, function (&$part) use ($boundary) {
	        $part = "--{$boundary}\r\n{$part}";
	    });
	   
	    // add final boundary
	    $body[] = "--{$boundary}--";
	    $body[] = "";
		
		
	   $heybody = str_replace("\r\n","<br/>",$body);
	   $this->debug=(implode('<br/>',$heybody));
	   
  	   $headers  =  array( "Accept:" );
	   
	   
	    // set options
	    return @curl_setopt_array($ch, array(
	        CURLOPT_POST       => true,
	        CURLOPT_POSTFIELDS => implode("\r\n", $body),
	        CURLOPT_HTTPHEADER => array(
				"Accept:",
	            'Authorization: Passcode '.$this->_auth,
                'FileType: STD',
	            "Content-Type: multipart/form-data; boundary={$boundary}"
	        ),
	    ));
	}
	

}