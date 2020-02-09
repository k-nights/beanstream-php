<?php 	namespace Beanstream;

/**
 * BatchProcessing class to handle batch processing actions
 *  
 * @author Kevin Saliba
 */
class BatchProcessing {


    /**
     * BatchProcessing Endpoint object
     * 
     * @var string $_endpoint
     */	
	protected $_endpoint;

	/**
     * HttpConnector object
	 * 
     * @var	\Beanstream\HttpConnector	$_connector
     */	
	protected $_connector;
	
	
    /**
     * Constructor
     * 
	 * Inits the appropriate endpoint and httpconnector objects 
	 * Sets all of the BatchProcessing class properties
	 * 
     * @param \Beanstream\Configuration $config
     */
	function __construct(Configuration $config) {
		
		//init endpoint
		$this->_endpoint = new Endpoints($config->getPlatform(), $config->getApiVersion());
		
		//init http connector
		$this->_connector = new HttpConnector(base64_encode($config->getMerchantId().':'.$config->getApiKey()));

	}
	
	
	
  /**
     * upload() function - uploads a batch to the batch processing api
     * @link http://support.beanstream.com/bic/w/#docs/batch-upload-rest-api.htm
     * 
     * @param array $data batch processing paramater data
     * @param array $batchFile ascii file holding batch file (csv)
     */
    public function uploadBatchFile($data = NULL, $batchFile = NULL) {
    	
		//get endpoint for bp
		$endpoint =  $this->_endpoint->getBatchProcessingURL();

		if (is_null($batchFile)) {
	            throw new ApiException('Batch File is NULL',0);
		}
		
		//upload and process batch
		return $this->_connector->processBatchFile($endpoint, 'POST', $data, $batchFile);
    }
	
	
}
