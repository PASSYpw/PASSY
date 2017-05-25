<?php

namespace PASSY;

require_once __DIR__ . "/../../vendor/autoload.php";

use PragmaRX\Google2FA\Google2FA;


class TwoFactor
{

	//TODO: 2fa
	private $google2fa;

	function __construct()
	{
		$this->google2fa = new Google2FA();
	}

	function enableTwoFactor($userId) {
		return; //TODO
		$privateKey = $this->google2fa->generateSecretKey();
		$qrUrl = $this->google2fa->getQRCodeGoogleUrl('PASSY', $userId, $privateKey);

	}

}