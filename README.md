# PASSY
[![GitHub release](https://img.shields.io/github/release/PASSYpw/PASSY.svg)](https://github.com/PASSYpw/PASSY/releases)
[![GitHub issues](https://img.shields.io/github/issues/PASSYpw/PASSY.svg)](https://github.com/PASSYpw/PASSY/issues)
[![Join the Discord](https://discordapp.com/api/guilds/324602899839844352/widget.png?style=shield)](https://discord.gg/5K6XDnR)
[![GitHub license](https://img.shields.io/badge/license-GPL%203.0-blue.svg)](https://raw.githubusercontent.com/PASSYpw/PASSY/master/LICENSE)

[PASSY in the cloud](https://app.passy.pw)

A password manager written in PHP to serve you over the internet.

## Requirements
 - MySQL Server (mysql-server, mariadb-server)
 - Web Server (nginx / Apache 2 / lighttpd / ...)
 - PHP 7.0
 - PHP 7.0 openssl module (often included with PHP 7.0)
 - PHP 7.0 mysql module
 - PHP 7.0 json module
 - PHP 7.0 curl module (only needed for ReCaptcha support)
 - [Composer](https://getcomposer.org/download/) (For Ubuntu 16.04 and newer: `apt install composer`)
 - [npm](https://docs.npmjs.com/getting-started/installing-node) (For Ubuntu 14.04 and newer: `apt install npm`)
 - [Yarn](https://yarnpkg.com) (`npm install -g yarn`)
 
## Installation
 - [Download](https://github.com/PASSYpw/PASSY/releases/latest) a version of PASSY in your preferred format (zip / tar.gz).
 - Unzip it in your web root.
 - Run the following command: `yarn`
 - Edit the `config.inc.php`

Walkthrough (Ubuntu 16.04.3):
[![Installation](https://asciinema.org/a/XmWH8YVcd1zpuidHl4yydAeYF.png)](https://asciinema.org/a/XmWH8YVcd1zpuidHl4yydAeYF)

## Installation (Pre 2.0.3)
 - [Download](https://github.com/PASSYpw/PASSY/releases/latest) a version of PASSY in your preferred format (zip / tar.gz).
 - Unzip it in your web root.
 - Run the following command: `composer install`
 - Edit the `config.inc.php`

## Contributing
More information on how to contribute to PASSY can be found under [CONTRIBUTING](CONTRIBUTING.md). Please also refer to the [CODE OF CONDUCT](CODE_OF_CONDUCT.md) file.

## License
This project is licensed under the GNU General Public License v3.
You can find more information about it in the [LICENSE](LICENSE) file.
