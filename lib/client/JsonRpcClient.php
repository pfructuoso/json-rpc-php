<?php
function __autoload($className) {
    if(strstr($className,'Exception')) {
       include_once(__DIR__. "../server/JsonRpcExceptions.php");
    }
    else {
        include_once($className.".php");
    }
}
class JsonRpcClient {
	private $_rpcUrl;
	private $_curlCookie;
	private $_replyTimeout;
	private $_connectTimeout;

	public function __construct($rpcUrl, $connectTimeout = 1, $replyTimeout = 1) {
		$this->_rpcUrl = $rpcUrl;
		$this->_curlCookie = dirname(__FILE__).DIRECTORY_SEPARATOR.'curl_cookie';
        $this->_replyTimeout = $replyTimeout;
        $this->_connectTimeout = $connectTimeout;
	}
	public function __call($name, $arguments) {
		return $this->call(new RpcRequest($name,$arguments));
	}

    /**
     * Set reply timeout in seconds.
     * @param $secs
     */
    public function setReplyTimeout($secs) {
        $this->_replyTimeout = $secs;
    }

    /**
     * Get reply timeout in seconds.
     */
    public function getReplyTimeout() {
        return $this->_replyTimeout;
    }

    /**
     * Set connect timeout in seconds.
     * @param $secs
     */
    public function setConnectTimeout($secs) {
        $this->_connectTimeout = $secs;
    }

    /**
     * Get connect timeout in seconds.
     */
    public function getConnectTimeout() {
        return $this->_connectTimeout;
    }

	public function call($rpcRequest) {
		if($rpcRequest instanceof RpcRequest) {
			return $this->httpRequest($rpcRequest->getRpcRequestObject());
		}
	}
	public function callBatch($rpcRequestList) {
        $rpcBatchArray = array();
        foreach($rpcRequestList as $rpcRequest) {
            if($rpcRequest instanceof RpcRequest) {
                array_push($rpcBatchArray, $rpcRequest->getRpcRequestObject());
            }
        }
        return $this->httpRequest($rpcBatchArray);
	}
	private function httpRequest($rpcBatchArray) {
		$curlHandler = curl_init();
		$curlOptions = array(
			CURLOPT_URL => $this->_rpcUrl,
            // Timeout
            CURLOPT_TIMEOUT => $this->_replyTimeout,
            CURLOPT_CONNECTTIMEOUT => $this->_connectTimeout,
			// cookie stuff
			CURLOPT_COOKIE => true,
            CURLOPT_COOKIEFILE => $this->_curlCookie,
            CURLOPT_COOKIEJAR => $this->_curlCookie,
			// request specific stuff
			CURLINFO_CONTENT_TYPE => "application/json",
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => json_encode($rpcBatchArray),
			CURLOPT_RETURNTRANSFER => true
		);
		curl_setopt_array($curlHandler,$curlOptions);

        $response = curl_exec($curlHandler);
        if(!$response) {
          throw new JsonRpcCurlException($curlHandler);
        }
        curl_close($curlHandler);
        $json_response = json_decode($response);
        if (json_last_error() != JSON_ERROR_NONE) {
          throw new JsonRpcParseErrorException();
        }
        return $json_response;
	}
}
