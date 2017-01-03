<?php
include __DIR__ . "/include/user.inc.php";
if (isLoggedIn() == 1) {
    header("Location: /manage/");
    die();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ff5722">
    <title>Passy.pw</title>

    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="assets/css/ripple.min.css" rel="stylesheet">
    <link href="assets/css/theme.min.css" rel="stylesheet">

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="#!p=login" data-to-page="login">Passy
                <small>.pw</small>
            </a>
        </div>
    </div>
    <div class="container">
        <ul class="nav navbar-nav" style="position: static">
            <li data-page-highlight="login"><a href="#!p=login" data-to-page="login"><i
                            class="material-icons">person</i>
                    Login</a></li>
            <li data-page-highlight="register"><a href="#!p=register" data-to-page="register"><i class="material-icons">person_pin_circle</i>
                    Register</a></li>
        </ul>
    </div>
</nav>
<div class="content">
    <div id="page_login" class="container" style="display: none">
        <div class="jumbotron depth-1">
            <div class="row">
                <div class="col-xs-12">
                    <h2 class="text-center">Login</h2>
                </div>
            </div>
            <div class="row row-margin">
                <div class="col-xs-12">
                    <form id="loginForm" method="POST" action="manage/backend/login.php">
                        <div class="form-group">
                            <div class="text">
                                <input type="text" class="form-control" title="Username" name="login_email"
                                       required/>
                                <label>Username</label>
                            </div>
                            <div class="text">
                                <input type="password" class="form-control" title="Password" name="login_password"
                                       required/>
                                <label>Password</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success">Login</button>
                            <a data-to-page="forgotpass" class="btn btn-default btn-flat btn-sm">Forgot password</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="page_register" class="container" style="display: none">
        <div class="jumbotron depth-1">
            <div class="row">
                <div class="col-xs-12">
                    <h2 class="text-center">Register</h2>
                </div>
            </div>
            <div class="row row-margin">
                <div class="col-xs-12">
                    <form id="registerForm" method="POST" action="manage/backend/register.php">
                        <div class="form-group">
                            <div class="text">
                                <input type="text" class="form-control" title="Username" name="register_email"
                                       required/>
                                <label>Username</label>
                            </div>
                            <div class="text">
                                <input type="text" class="form-control" title="Password" name="register_password"
                                       required/>
                                <label>Password</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-default">Register</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="page_forgotpass" class="container" style="display: none">
        <div class="jumbotron depth-1">
            <div class="row">
                <div class="col-xs-12">
                    <h2 class="text-center">Forgot password ?</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="footer">
        <div class="container">
            <h1>foootererere</h1>
        </div>
    </div>
</div>

<!-- CONTEXTMENU -->
<div class="dropdown contextmenu" id="dropdownContextMenu">
    <ul class="dropdown-menu">
        <li><a onclick="location.reload();"><i class="material-icons">refresh</i> Reload</a></li>
        <li><a><i class="material-icons">help</i> Help</a></li> <!-- //TODO: Add Help-Function -->
    </ul>
</div>

<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/ripple.min.js"></script>
<script src="assets/js/app.login.js"></script>
<script src="assets/js/ui.js"></script>
</body>
</html>