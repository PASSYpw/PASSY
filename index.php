<?php
include __DIR__ . "/include/user.inc.php";
if (isLoggedIn()) {
    header("Location: manage/");
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
    <title>PASSY.pw</title>

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
            <!-- @formatter:off -->
            <span class="navbar-brand">PASSY<small>.pw</small></span>
            <!-- @formatter:on -->
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
                                <input type="text" class="form-control" title="E-Mail" name="login_email"
                                       required/>
                                <label>E-Mail</label>
                            </div>
                            <div class="text">
                                <input type="password" class="form-control" title="Password" name="login_password"
                                       required/>
                                <label>Password</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success">Login</button>
                            <button type="button" class="btn btn-default btn-flat" data-to-page="forgotpass">Forgot
                                password
                            </button>
                        </div>
                    </form>
                    <div class="alert alert-success" id="successAccountCreated" style="display: none">
                        <strong>Success!</strong> You can now login.
                    </div>
                    <div class="alert alert-danger" id="errorInvalidCredentials" style="display: none">
                        <strong>Error!</strong> Invalid Credentials!
                    </div>
                    <div class="alert alert-danger" id="errorAccountLocked" style="display: none">
                        <strong>Error!</strong> The account you are trying to access has been locked! Please contact
                        support.
                    </div>
                    <div class="alert alert-danger" id="errorLoginEmailInvalid" style="display: none">
                        <strong>Error!</strong> The specified email is invalid!
                    </div>
                    <div class="alert alert-danger" id="errorLoginFormInvalid" style="display: none">
                        <strong>Error!</strong> The form is invalid
                    </div>
                    <div class="alert alert-danger" id="errorLoginServer" style="display: none">
                        <strong>Error!</strong> There has been a problem with the server. Please contact support.
                    </div>
                    <div class="alert alert-danger" id="errorLoginDatabase" style="display: none">
                        <strong>Error!</strong> There was a problem with the database connection.
                    </div>
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
                                <input type="text" class="form-control" title="E-Mail" name="register_email"
                                       required/>
                                <label>E-Mail</label>
                            </div>
                            <div class="text">
                                <input type="password" class="form-control" title="Password" name="register_password"
                                       required/>
                                <label>Password</label>
                            </div>
                            <div class="text">
                                <input type="password" class="form-control" title="Password" name="register_password2"
                                       required/>
                                <label>Password (again)</label>
                            </div>
                            <div class="g-recaptcha" data-sitekey="6LeUfBEUAAAAAG7h_NoKmOx1FzP3gPpDs_lwhlcE"></div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-default">Register</button>
                        </div>
                    </form>
                    <div class="alert alert-danger" id="errorAccountRegistered" style="display: none">
                        <strong>Error!</strong> There is an account registered with that email!
                    </div>
                    <div class="alert alert-danger" id="errorEmailInvalid" style="display: none">
                        <strong>Error!</strong> The specified email is invalid!
                    </div>
                    <div class="alert alert-danger" id="errorFormInvalid" style="display: none">
                        <strong>Error!</strong> The form is invalid
                    </div>
                    <div class="alert alert-danger" id="errorRegisterServer" style="display: none">
                        <strong>Error!</strong> There has been a problem with the server. Please contact the support!
                    </div>
                    <div class="alert alert-danger" id="errorDatabase" style="display: none">
                        <strong>Error!</strong> There has been a problem with the database connection.
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="page_forgotpass" class="container" style="display: none">
        <div class="jumbotron depth-1">
            <div class="row">
                <div class="col-xs-12">
                    <h2 class="text-center">Reset password</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include __DIR__ . "/include/ui/footer.php";
?>

<script src="assets/js/jquery.min.js"></script>
<script src='https://www.google.com/recaptcha/api.js'></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/ripple.min.js"></script>
<script src="assets/js/app.login.js"></script>
<script src="assets/js/ui.js"></script>
</body>
</html>
