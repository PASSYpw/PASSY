<?php
define("END", "FRONT");
require_once __DIR__ . "/include/user.inc.php";
require_once __DIR__ . "/include/config.inc.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ff5722">
    <title>PASSY</title>

    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/material-icons.min.css" rel="stylesheet">
    <link href="assets/css/ripple.min.css" rel="stylesheet">
    <link href="assets/css/app.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <!-- @formatter:off -->
            <span class="navbar-brand">PASSY</span>
            <!-- @formatter:on -->
        </div>
        <ul class="nav navbar-nav navbar-right">
            <li>
                <a href="#" id="btnLogout" data-page-scope="logged_in" style="display: none;">
                    <i class="material-icons">exit_to_app</i>
                </a>
            </li>
            <li class="dropdown" data-page-scope="logged_in" style="display: none;">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                   aria-expanded="false">
                    <i class="material-icons" id="aMenu">more_vert</i>
                </a>
                <ul class="dropdown-menu">
                    <li style="animation-delay: 100ms">
                        <a href="#!p=user_settings" data-to-page="user_settings">
                            <i class="material-icons">edit</i> User Settings
                        </a>
                    </li>

                    <?php
                    if ($config["general"]["enable_login_history"]) {
                        ?>
                        <li style="animation-delay: 100ms">
                            <a href="#!p=login_history" data-to-page="login_history">
                                <i class="material-icons">list</i> Login History
                            </a>
                        </li>
                        <?php
                    }
                    ?>

                </ul>
            </li>
        </ul>
    </div>
    <div class="container">
        <ul class="nav navbar-nav">
            <li data-page-highlight="login" data-page-scope="logged_out">
                <a href="#!p=login" data-to-page="login">
                    <i class="material-icons">person</i> Login
                </a>
            </li>

            <?php
            if ($config["general"]["enable_register"]) {
                ?>
                <li data-page-highlight="register" data-page-scope="logged_out">
                    <a href="#!p=register" data-to-page="register">
                        <i class="material-icons">person_pin_circle</i> Register
                    </a>
                </li>
                <?php
            }
            ?>

            <li data-page-highlight="passwords" data-page-scope="logged_in" style="display: none">
                <a href="#!p=passwords" data-to-page="passwords">
                    <i class="material-icons">lock_outline</i> Passwords
                </a>
            </li>

            <li data-page-highlight="archive" data-page-scope="logged_in" style="display: none">
                <a href="#!p=archive" data-to-page="archive">
                    <i class="material-icons">archive</i> Archive
                </a>
            </li>
        </ul>
    </div>
</nav>

<div class="statusMessageContainer" style="display: none">
    <div class="statusMessage text-center col-xs-11 col-sm-5 col-md-4 col-lg-3">
        <h3 class="statusMessageText"></h3>
        <button class="btn btn-flat btn-primary statusMessageButton"></button>
    </div>
</div>

<div class="content">
    <div class="load-spinner">
        <svg class="spinner" width="20px" height="20px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
            <circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle>
        </svg>
    </div>

    <?php

    include_once __DIR__ . "/include/page/login.page.inc.php";

    if ($config["general"]["enable_register"])
        include_once __DIR__ . "/include/page/register.page.inc.php";

    if ($config["general"]["enable_forgot_password"])
        include_once __DIR__ . "/include/page/forgotpass.page.inc.php";

    include_once __DIR__ . "/include/page/passwords.page.inc.php";

    include_once __DIR__ . "/include/page/archive.page.inc.php";

    if ($config["general"]["enable_login_history"])
        include_once __DIR__ . "/include/page/login_history.page.inc.php";

    include_once __DIR__ . "/include/page/user_settings.page.inc.php";

    ?>
</div>


<!-- CONTEXTMENU -->
<div class="dropdown contextmenu" id="dropdownContextMenu">
    <ul class="dropdown-menu">
        <li>
            <a href="#" id="aRefresh">
                <i class="material-icons">refresh</i> Refresh
            </a>
        </li>
    </ul>
</div>

<?php
include __DIR__ . "/include/ui/footer.ui.inc.php";
?>

<script src="assets/js/jquery.min.js "></script>
<script src="assets/js/bootstrap.min.js "></script>
<script src="assets/js/ripple.min.js"></script>
<script src="assets/js/app.js"></script>
<script src='https://www.google.com/recaptcha/api.js'></script>
</body>
</html>
