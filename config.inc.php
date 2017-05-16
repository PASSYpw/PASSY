<?php

$mysqlConfig = array(

	"host" => "localhost",
	"user" => "passy",
	"password" => "",
	"db" => "passy"

);

$customizationConfig = array(
	"title" => "PASSY" // Will be shown in titlebar and footer.
);

$generalConfig = array(

	"redirect_ssl" => false, // Redirects from HTTP to HTTPS. May not work on every setup.

	"security" => array(
		"lock_session_to_ip" => true // If your IP changes you will be logged out. This is necessary if there is a MITM and someone steals your cookie.
	),

	"registration" => array(

		"enabled" => true // Enable / Disable registration.

	),
	"login_history" => array(

		"enabled" => true // Logs IP and User-Agent on every login.

	),
	"recaptcha" => array(
		"enabled" => false, // Prevent spam registrations
		"website_key" => "",
		"private_key" => ""
	)

);

$passyMetadata = array(
	"version" => "2.0.3",
	"build" => 203,
	"github" => "https://github.com/Scrumplex/PASSY",
	"issues" => "https://github.com/Scrumplex/PASSY/issues"
);
