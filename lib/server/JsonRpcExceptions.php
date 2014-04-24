<?php
/**
 * Class JsonRpcParseErrorException
 * Invalid JSON was received. An error occurred while parsing a JSON string.
 */
class JsonRpcParseErrorException extends Exception {
    /**
     * Contructor. If message and code are empty last json code will be used.
     * @param null $message
     * @param null $code
     */
    public function __construct($message = NULL, $code = NULL) {
        if(empty($message) && empty($code)) {
            $code = json_last_error();
            switch ($code) {
                case JSON_ERROR_DEPTH:
                    $message = 'The maximum stack depth has been exceeded';
                    break;
                case JSON_ERROR_STATE_MISMATCH:
                    $message = 'Invalid or malformed JSON';
                    break;
                case JSON_ERROR_CTRL_CHAR:
                    $message = 'Control character error, possibly incorrectly encoded';
                    break;
                case JSON_ERROR_SYNTAX:
                    $message = 'Syntax error';
                    break;
                case JSON_ERROR_UTF8:
                    $message = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                    break;
                default:
                    $message = "Parse error";
                    break;
            }
        }
		parent::__construct($message, $code);
	}
}
// The JSON sent is not a valid Request object.
class JsonRpcInvalidRequestException extends Exception {
	public function __construct() {
		parent::__construct("Invalid Request",-32600);
	}
}
// The method does not exist / is not available.
class JsonRpcMethodNotFoundException extends Exception {
	public function __construct() {
		parent::__construct("Method not found",-32601);
	}
}
// Invalid method parameter(s).
class JsonRpcInvalidParamsException extends Exception {
	public function __construct() {
		parent::__construct("Invalid params",-32602);
	}
}
// Internal JSON-RPC error.
class JsonRpcInternalErrorException extends Exception {
	public function __construct() {
		parent::__construct("Internal error",-32603);
	}
}
// Reserved for implementation-defined server-errors.
class JsonRpcServererrorException extends Exception {
	public function __construct() {
		parent::__construct("Server error",-32099||-32000);
	}
}
/**
 * Class JsonRpcCurlException
 * JsonRpcCurlException is thrown whenever an error is reported by the low level HTTP cURL library
 */
class JsonRpcCurlException extends Exception {
	public function __construct($curlHandler) {
		parent::__construct(curl_error($curlHandler), curl_errno($curlHandler));
	}
}
