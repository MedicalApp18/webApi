<?php
namespace AppBundle\Service;

class Jwtauth{
	public function decode($jwt, $key = null, $verify = true)
	{
		//echo $jwt;die;
		$tks = explode('.', $jwt);
		if (count($tks) != 3) {
			throw new UnexpectedValueException('Wrong number of segments');
		}
		list($headb64, $bodyb64, $cryptob64) = $tks;
		if (null === ($header = $this->jsonDecode($this->urlsafeB64Decode($headb64)))) {
			throw new UnexpectedValueException('Invalid segment encoding');
		}
		if (null === $payload = $this->jsonDecode($this->urlsafeB64Decode($bodyb64))) {
			throw new UnexpectedValueException('Invalid segment encoding');
		}
		$sig = $this->urlsafeB64Decode($cryptob64);
		if ($verify) {
			if (empty($header->alg)) {
				throw new DomainException('Empty algorithm');
			}
			if ($sig != Jwtauth::sign("$headb64.$bodyb64", $key, $header->alg)) {
				//throw new UnexpectedValueException('Signature verification failed');
			}
		}
		return $payload;
	}
	/**
	 * Converts and signs a PHP object or array into a JWT string.
	 *
	 * @param object|array $payload PHP object or array
	 * @param string       $key     The secret key
	 * @param string       $algo    The signing algorithm. Supported
	 *                              algorithms are 'HS256', 'HS384' and 'HS512'
	 *
	 * @return string      A signed JWT
	 * @uses jsonEncode
	 * @uses urlsafeB64Encode
	 */
	public function encode($payload, $key, $algo = 'HS256')
	{
		$header = array('typ' => 'JWT', 'alg' => $algo);
		$segments = array();
		$segments[] = $this->urlsafeB64Encode($this->jsonEncode($header));
		$segments[] = $this->urlsafeB64Encode($this->jsonEncode($payload));
		$signing_input = implode('.', $segments);
		$signature = $this->sign($signing_input, $key, $algo);
		$segments[] = $this->urlsafeB64Encode($signature);
		return implode('.', $segments);
	}
	/**
	 * Sign a string with a given key and algorithm.
	 *
	 * @param string $msg    The message to sign
	 * @param string $key    The secret key
	 * @param string $method The signing algorithm. Supported
	 *                       algorithms are 'HS256', 'HS384' and 'HS512'
	 *
	 * @return string          An encrypted message
	 * @throws DomainException Unsupported algorithm was specified
	 */
	public function sign($msg, $key, $method = 'HS256')
	{
		$methods = array(
			'HS256' => 'sha256',
			'HS384' => 'sha384',
			'HS512' => 'sha512',
		);
		if (empty($methods[$method])) {
			throw new DomainException('Algorithm not supported');
		}
		return hash_hmac($methods[$method], $msg, $key, true);
	}
	/**
	 * Decode a JSON string into a PHP object.
	 *
	 * @param string $input JSON string
	 *
	 * @return object          Object representation of JSON string
	 * @throws DomainException Provided string was invalid JSON
	 */
	public function jsonDecode($input)
	{
		$obj = json_decode($input);
		if (function_exists('json_last_error') && $errno = json_last_error()) {
			$this->_handleJsonError($errno);
		} else if ($obj === null && $input !== 'null') {
			throw new DomainException('Null result with non-null input');
		}
		return $obj;
	}
	/**
	 * Encode a PHP object into a JSON string.
	 *
	 * @param object|array $input A PHP object or array
	 *
	 * @return string          JSON representation of the PHP object or array
	 * @throws DomainException Provided object could not be encoded to valid JSON
	 */
	public function jsonEncode($input)
	{
		$json = json_encode($input);
		if (function_exists('json_last_error') && $errno = json_last_error()) {
			$this->_handleJsonError($errno);
		} else if ($json === 'null' && $input !== null) {
			throw new DomainException('Null result with non-null input');
		}
		return $json;
	}
	/**
	 * Decode a string with URL-safe Base64.
	 *
	 * @param string $input A Base64 encoded string
	 *
	 * @return string A decoded string
	 */
	public static function urlsafeB64Decode($input)
	{
		$remainder = strlen($input) % 4;
		if ($remainder) {
			$padlen = 4 - $remainder;
			$input .= str_repeat('=', $padlen);
		}
		return base64_decode(strtr($input, '-_', '+/'));
	}
	/**
	 * Encode a string with URL-safe Base64.
	 *
	 * @param string $input The string you want encoded
	 *
	 * @return string The base64 encode of what you passed in
	 */
	public function urlsafeB64Encode($input)
	{
		return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
	}
	/**
	 * Helper method to create a JSON error.
	 *
	 * @param int $errno An error number from json_last_error()
	 *
	 * @return void
	 */
	private function _handleJsonError($errno)
	{
		$messages = array(
			JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
			JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
			JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON'
		);
		throw new DomainException(
			isset($messages[$errno])
			? $messages[$errno]
			: 'Unknown JSON error: ' . $errno
		);
	}
	
	public function checkToken($token, $userRepository){
		$returnToken = str_replace("Bearer ", "", $token);
		if (preg_match('/bearer/',$token)){
			$returnToken  = str_replace("bearer ", "", $token);
        }
		$tokenArray = $this->decode($returnToken);
		$mobile		= $tokenArray->sub;
		$expTime 	= $tokenArray->exp;
		$user 		= $userRepository->findOneBy(array('mobileNumber' => $mobile));
		$returnArray =array();
		if(count($user)>0){
			$return=1;
			$returnArray['verify'] =1;
			$returnArray['user'] =$user;
		}
		return $returnArray;
    }
	
	public function createToken($userId, $mobile){
		$issuedAt           = time();
		$notBefore          = $issuedAt + 10;            
		$expire             = $notBefore + 60;            
		$secretKey          = base64_decode('sNpBQtCp4f31jqLJCRDqtBTWvnCcCynC');
		$serverName         = '';
		$data=['iat'=>$issuedAt,'jti'=>'','iss'=>$serverName,'nbf'=>$notBefore,'exp'=>$expire,'sub'=>$mobile, 'data'=>['userId'=>$userId,'userMobileNumber'=>$mobile]];
		$jwtToken 			= $this->encode($data,$secretKey,'HS512');
		return $jwtToken;
    }
}
