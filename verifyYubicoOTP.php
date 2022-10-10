<?php
/**
Yubico OTP v2 Verifier

@param	$otp			Yubico OTP
@param	$apiClientId	YubiCloud API Client ID
@param	$apiSecretKey	YubiCloud API Secret Key
@return					YubiKey Public ID if validation is successful, false if not

@version	1.0.1
@copyright	(c) 2022 M. Taniguchi
@license	MIT License
*/
function verifyYubicoOTP(string $otp, string $apiClientId, string $apiSecretKey) : ?string {
	// Generate API call URL
	$nonce = md5(rand(0, 0x7fffffff));
	$server = (int)rand(1, 5);	// Randomly select a YubiCloud server (api*.yubico.com)
	$url = 'https://api' . (($server <= 1)? '' : $server) . '.yubico.com/wsapi/2.0/verify?id=' . urlencode($apiClientId) . '&otp=' . urlencode($otp) . '&nonce=' . urlencode($nonce);

	// Execute cURL
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	$params = curl_exec($ch);
	curl_close($ch);
	if (!$params) return null;	// Returns null if communication fails

	// Make the returned information into an associative array
	$data = array();
	$params = explode("\n", $params);
	forEach($params as $param) {
		if (!$param) continue;
		if (($p = strpos($param, '=')) === false) continue;
		$data[substr($param, 0, $p)] = substr($param, $p + 1, -1);
	}
	ksort($data);	// Sort by key (required for specification)

	// Reconstruct information into GET parameter format and verify HMAC-SHA-1 signatures
	$params = '';
	$hash = $id = $status = null;
	forEach($data as $key => $val) {
		switch ($key) {
		case 'h':
			$hash = $val;
			continue 2;

		case 'status':
			$status = $val;
			break;

		case 'otp':
			$id = substr($val, 0, strlen($val) - 32);
			break;

		case 'nonce':
			if ($nonce == $val) $nonce = true;
			break;
		}

		if ($params) $params .= '&';
		$params .= $key . '=' . $val;
	}
	$hash = ($hash === base64_encode(hash_hmac('sha1', $params, base64_decode($apiSecretKey), true)));

	// Returns Public ID of YubiKey on successful verification, null on failure
	return ($hash && $nonce === true && $status === 'OK' && $id)? $id : null;
}
