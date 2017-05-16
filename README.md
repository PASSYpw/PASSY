# PASSY
[Hosted Version](https://app.passy.pw)

A web based password manager with multiple user accounts.

## Requirements
 - MYSQL Server (Maria DB not tested)
 - Web Server (nginx / Apache 2 / CaddyServer / ...)
 - PHP 7.0 (PHP 5 not tested)
 - PHP 7.0 openssl module (often included with PHP 7.0)
 - PHP 7.0 mysql module
 - PHP 7.0 json module
 - PHP 7.0 curl module (for ReCaptcha support)
 - Composer (`apt install composer`) [Learn more](https://getcomposer.org/download/)
 
## Installation
 - [Download](/releases) a version of PASSY in your preferred format (zip / tar.gz).
 - Unzip it in your web root.
 - Run the following command: `composer install`
 - Edit the `config.inc.php`
 
## Contributing
Of course you can help make PASSY a better project. You can search and find bugs and report them in GitHub's issue system.
If you are a developer you can also add code to PASSY.

If you are a developer you can use the predefined `Vagrantfile` to deploy a suitable test environment.
Just execute the following command `vagrant up` after [installing Vagrant](https://www.vagrantup.com/).

## License
This project is licensed under the Apache License 2.0.
You can find more information about it in the [LICENSE](LICENSE) file.
