<?php
$config = array(
    "general" => array(
        "title" => "PASSY",
        "enable_register" => true,
        "enable_forgot_password" => true,
        "enable_login_history" => true,
        "enable_account_lock_on_failed_logins" => true
    ),
    "mysql" => array(
        "host" => "localhost",
        "db" => "passy",
        "user" => "passy",
        "pass" => ""
    ),
    "geoip" => array(
        "enabled" => false,
        "ip_and_port" => "localhost:8080",
        "download_geoip_server" => "https://github.com/fiorix/freegeoip/releases"
    ),
    "recaptcha" => array(
        "enabled" => false,
        "website_key" => "",
        "secret_key" => ""
    ),

    "passy" => array(
        "version" => "1.0.1",
        "version_number" => "2",
        "authors" => array(
            "Scrumplex" => "https://scrumplex.net"
        ),
        "github" => "https://github.com/Scrumplex/PASSY",
        "issues" => "https://github.com/Scrumplex/PASSY/issues"
    )
);

