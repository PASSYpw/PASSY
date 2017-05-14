<?php
require_once __DIR__ . "/lib/autoload.php";
require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/config.inc.php";

use Scrumplex\PASSY\Database;
use Scrumplex\PASSY\UserManager;
use Scrumplex\PASSY\Passwords;
use Scrumplex\PASSY\IPLog;
use Scrumplex\PASSY\Response;

$unauthenticatedActions = array(
	"user/login",
	"user/logout",
	"user/register",
	"status"
);

$authenticatedActions = array(
	"password/create",
	"password/edit",
	"password/query",
	"password/queryAll",
	"password/archive",
	"password/restore",
	"password/delete",
	"iplog/queryAll",
	"user/changeUsername",
	"user/changePassword",
	"misc/export",
	"misc/import"
);

$db = new Database($mysqlConfig);
$passwords = new Passwords($db);
$userManager = new UserManager($db, $passwords);
$ipLog = new IPLog($db);

$action = @$_POST["a"];

header("Content-Type: application/json; charset=UTF-8");

$userManager->checkSessionExpiration();

if (in_array($action, $unauthenticatedActions)) {
	switch ($action) {
		case "user/login":
			$username = $_POST["username"];
			$password = $_POST["password"];

			$result = $userManager->login($username, $password);
			if ($result->wasSuccess())
				$ipLog->logIP($_SERVER["REMOTE_ADDR"], $_SERVER["HTTP_USER_AGENT"], $userManager->getUserID());

			die($result->getJSONResponse());
			break;

		case "user/logout":
			$result = $userManager->logout();
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

			$result = $userManager->register($username, $password);

			die($result->getJSONResponse());
			break;

		case "status":
			$response = new Response(true, array(
				"logged_in" => $userManager->isAuthenticated(),
				"last_activity" => $userManager->getLastActivity(),
				"user_id" => $userManager->getUserID()
			));
			die($response->getJSONResponse());
			break;
	}
} else if (in_array($action, $authenticatedActions)) {
	if ($userManager->isAuthenticated()) {
		$userManager->trackActivity();
		switch ($action) {
			case "password/create":
				$username = $_POST["username"];
				$password = $_POST["password"];
				$description = $_POST["description"];
				$result = $passwords->create($username, $password, $description, $userManager->getUserID(), $userManager->getMasterPassword());
				die($result->getJSONResponse());
				break;
			case "password/edit":
				$passwordId = $_POST["id"];
				$username = $_POST["username"];
				$password = $_POST["password"];
				$description = $_POST["description"];
				$result = $passwords->edit($passwordId, $username, $password, $description, $userManager->getMasterPassword());
				die($result->getJSONResponse());
				break;

			case "misc/import":
				$content = $_FILES['parse-file']['tmp_name'];
				$result = $passwords->importPasswords(file_get_contents($content), $userManager->getUserID(), $userManager->getMasterPassword());
				die($result->getJSONResponse());
				break;

			case "misc/export":
				$result = $passwords->queryExport($userManager->getUserID(), $userManager->getMasterPassword());
				$json = $result->getJSONResponse();
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename=PASSY-Export-' . time() . ".json");
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . strlen($json));
				die($json);
				break;
			case "password/query":
				$passwordId = $_POST["id"];
				$result = $passwords->query($passwordId, $userManager->getMasterPassword());
				die($result->getJSONResponse());
				break;

			case "password/queryAll":
				$result = $passwords->queryAll($userManager->getUserID());
				die($result->getJSONResponse());
				break;

			case "password/archive":
				$passwordId = $_POST["id"];
				$result = $passwords->archive($passwordId);
				die($result->getJSONResponse());
				break;

			case "password/restore":
				$passwordId = $_POST["id"];
				$result = $passwords->restore($passwordId);
				die($result->getJSONResponse());
				break;

			case "password/delete":
				$passwordId = $_POST["id"];
				$result = $passwords->delete($passwordId);
				die($result->getJSONResponse());
				break;

			case "iplog/queryAll":
				$result = $ipLog->queryAll($userManager->getUserID());
				die($result->getJSONResponse());
				break;

			case "user/changeUsername":
				$newUsername = $_POST["new_username"];
				$result = $userManager->changeUsername($userManager->getUserID(), $newUsername);
				die($result->getJSONResponse());
				break;

			case "user/changePassword":
				$newPassword = $_POST["new_password"];
				$newPassword2 = $_POST["new_password2"];
				if ($newPassword != $newPassword2) {
					$response = new Response(false, "passwords_not_matching");
					die($response->getJSONResponse());
				}

				$result = $userManager->changePassword($userManager->getUserID(), $userManager->getMasterPassword(), $newPassword);
				die($result->getJSONResponse());
				break;
		}
	} else {
		$response = new Response(false, "not_authenticated");
		die($response->getJSONResponse());
	}
}

$response = new Response(false, "no_handler");
die($response->getJSONResponse());
