<?php
require_once __DIR__ . "/src/autoload.php";
require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/config.inc.php";
require_once __DIR__ . "/meta.inc.php";

use PASSY\Database;
use PASSY\TwoFactor;
use PASSY\UserManager;
use PASSY\Passwords;
use PASSY\IPLog;
use PASSY\Response;
use PASSY\Util;


$unauthenticatedActions = array(
	"user/login" => true,
	"user/logout" => true,
	"user/register" => $generalConfig["registration"]["enabled"],
	"status" => true
);

$authenticatedActions = array(
	"password/create" => true,
	"password/edit" => true,
	"password/query" => true,
	"password/queryAll" => true,
	"password/archive" => true,
	"password/restore" => true,
	"password/delete" => true,
	"iplog/queryAll" => $generalConfig["login_history"]["enabled"],
	"user/changeUsername" => true,
	"user/changePassword" => true,
	"user/2faGenerateKey" => true,
	"user/2faEnable" => true,
	"user/2faDisable" => true,
	"misc/export" => true,
	"misc/import" => true
);

$db = new Database($mysqlConfig);
$passwords = new Passwords($db);
$userManager = new UserManager($db, $passwords, $generalConfig["redirect_ssl"]);
$twoFactor = new TwoFactor($db);
$ipLog = new IPLog($db);

$action = @$_POST["a"];

header("Content-Type: application/json; charset=UTF-8");

// Report exceptions to error log and print error message
set_exception_handler(function ($exception) {
	error_log($exception);
	$response = new Response(false, "server_error");
	die($response->getJSONResponse());
});
$userManager->checkSessionExpiration();

if (in_array($action, $unauthenticatedActions) && $unauthenticatedActions[$action]) {
	switch ($action) {
		case "user/login":
			$username = $_POST["username"];
			$password = $_POST["password"];

			$persistent = isset($_POST["persistent"]) && $_POST["persistent"] == "on";

			$result = $userManager->_login($username, $password);
			if ($result->wasSuccess()) {
				if ($twoFactor->enabled($_SESSION["userId"])) {
					if (isset($_POST["2faCode"])) {
						$twoFactorCode = $_POST["2faCode"];
						$twoFactorCode = trim($twoFactorCode);
						if (strlen($twoFactorCode) == 6) {
							$result = $twoFactor->_checkCode($_SESSION["userId"], $password, $twoFactorCode);
							if ($result->wasSuccess()) {
								$ipLog->_logIP($_SERVER["REMOTE_ADDR"], $_SERVER["HTTP_USER_AGENT"], $userManager->getUserID());
								if ($persistent)
									$userManager->setSessionExpirationTime(0);
								die($result->getJSONResponse());
							}
						} else if (strlen($twoFactorCode) > 6) {
							if ($twoFactor->checkPrivateKey($_SESSION["userId"], $password, $twoFactorCode)) {
								$twoFactor->_disable2FA($_SESSION["userId"]);
								$ipLog->_logIP($_SERVER["REMOTE_ADDR"], $_SERVER["HTTP_USER_AGENT"], $userManager->getUserID());
								if ($persistent)
									$userManager->setSessionExpirationTime(0);
								$response = new Response(true, array());
								die($response->getJSONResponse());
							}
						} else {
							$result = new Response(false, "invalid_code");
						}
						$userManager->_logout();
						die($result->getJSONResponse());
					}
					$userManager->_logout();
					$response = new Response(false, "two_factor_needed");
					die($response->getJSONResponse());
				}

				$ipLog->_logIP($_SERVER["REMOTE_ADDR"], $_SERVER["HTTP_USER_AGENT"], $userManager->getUserID());
				if ($persistent)
					$userManager->setSessionExpirationTime(0);
			}

			die($result->getJSONResponse());
			break;

		case "user/logout":
			$result = $userManager->_logout();
			die($result->getJSONResponse());
			break;

		case "user/register":
			$username = $_POST["username"];
			$password = $_POST["password"];
			$password2 = $_POST["password2"];

			if ($generalConfig["recaptcha"]["enabled"]) {
				$recaptcha = new \ReCaptcha\ReCaptcha($generalConfig["recaptcha"]["private_key"]);
				$resp = $recaptcha->verify($_POST["g-recaptcha-response"], $_SERVER["REMOTE_ADDR"]);
				if (!$resp->isSuccess()) {
					$response = new Response(false, "recaptcha_fail");
					die($response->getJSONResponse());
				}
			}

			if ($password != $password2) {
				$response = new Response(false, "passwords_not_matching");
				die($response->getJSONResponse());
			}

			$result = $userManager->_register($username, $password);

			die($result->getJSONResponse());
			break;

		case "status":
			die($userManager->_status()->getJSONResponse());
			break;
	}
} else if (in_array($action, $authenticatedActions) && $authenticatedActions[$action]) {
	if ($userManager->isAuthenticated()) {
		$userManager->trackActivity();
		switch ($action) {
			case "password/create":
				$username = $_POST["username"];
				$password = $_POST["password"];
				$description = $_POST["description"];
				$result = $passwords->_create($username, $password, $description, $userManager->getUserID(), $userManager->getMasterPassword());
				die($result->getJSONResponse());
				break;
			case "password/edit":
				$passwordId = $_POST["id"];
				$username = $_POST["username"];
				$password = $_POST["password"];
				$description = $_POST["description"];
				$result = $passwords->_edit($passwordId, $username, $password, $description, $userManager->getMasterPassword());
				die($result->getJSONResponse());
				break;

			case "misc/import":
				$content = $_FILES['parse-file']['tmp_name'];
				if (Util::endsWith($_FILES['parse-file']['name'], ".passy-json")) {
					$result = $passwords->_import(file_get_contents($content), $userManager->getUserID(), $userManager->getMasterPassword());
					die($result->getJSONResponse());
				} elseif (Util::endsWith($_FILES['parse-file']['name'], ".csv")) {
					$result = $passwords->_import(file_get_contents($content), $userManager->getUserID(), $userManager->getMasterPassword(), "CSV");
					die($result->getJSONResponse());
				} else {
					$result = new Response(false, array("not_supported_format"));
					die($result->getJSONResponse());
				}

				break;

			case "misc/export":
				$result = $passwords->_exportAll($userManager->getUserID(), $userManager->getMasterPassword());
				$json = $result->getJSONResponse();
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename=PASSY-Export-' . time() . ".passy-json");
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . strlen($json));
				die($json);
				break;
			case "password/query":
				$passwordId = $_POST["id"];
				$result = $passwords->_query($passwordId, $userManager->getMasterPassword());
				die($result->getJSONResponse());
				break;

			case "password/queryAll":
				$result = $passwords->_queryAll($userManager->getUserID());
				die($result->getJSONResponse());
				break;

			case "password/archive":
				$passwordId = $_POST["id"];
				$result = $passwords->_archive($passwordId);
				die($result->getJSONResponse());
				break;

			case "password/restore":
				$passwordId = $_POST["id"];
				$result = $passwords->_restore($passwordId);
				die($result->getJSONResponse());
				break;

			case "password/delete":
				$passwordId = $_POST["id"];
				$result = $passwords->_delete($passwordId);
				die($result->getJSONResponse());
				break;

			case "iplog/queryAll":
				$result = $ipLog->_queryAll($userManager->getUserID());
				die($result->getJSONResponse());
				break;

			case "user/changeUsername":
				$password = $_POST["password"];
				$newUsername = $_POST["new_username"];
				if (!$userManager->checkPassword($password)) {
					$response = new Response(false, "invalid_credentials");
					die($response->getJSONResponse());
				}
				$result = $userManager->_changeUsername($userManager->getUserID(), $newUsername);
				die($result->getJSONResponse());
				break;

			case "user/changePassword":
				$password = $_POST["password"];
				$newPassword = $_POST["new_password"];
				$newPassword2 = $_POST["new_password2"];
				if (!$userManager->checkPassword($password)) {
					$response = new Response(false, "invalid_credentials");
					die($response->getJSONResponse());
				}
				if ($newPassword != $newPassword2) {
					$response = new Response(false, "passwords_not_matching");
					die($response->getJSONResponse());
				}

				$result = $userManager->_changePassword($userManager->getUserID(), $userManager->getMasterPassword(), $newPassword);
				die($result->getJSONResponse());
				break;

			case "user/2faGenerateKey":
				if ($twoFactor->enabled($_SESSION["userId"])) {
					$response = new Response(false, "2fa_enabled");
					die($response->getJSONResponse());
				}
				die($twoFactor->_generateSecretKey($_SESSION["username"])->getJSONResponse());
				break;

			case "user/2faEnable":
				if ($twoFactor->enabled($_SESSION["userId"])) {
					$response = new Response(false, "2fa_enabled");
					die($response->getJSONResponse());
				}
				$privateKey = $_POST["2faPrivateKey"];
				$code = $_POST["2faCode"];
				$result = $twoFactor->_enable2FA($_SESSION["userId"], $_SESSION["master_password"], $privateKey, $code);
				die($result->getJSONResponse());
				break;

			case "user/2faDisable":
				$result = $twoFactor->_disable2FA($_SESSION["userId"]);
				die($result->getJSONResponse());
				break;
		}
	} else {
		$response = new Response(false, "not_authenticated");
		die($response->getJSONResponse());
	}
}

$response = new Response(false, "invalid_request");
die($response->getJSONResponse());
