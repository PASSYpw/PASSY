<?php
require_once __DIR__ . "/../config.inc.php";
?>
<div id="page_login" data-apply-page-scope="logged_out" class="container" style="display: none">
    <div class="jumbotron depth-1">
        <div class="row">
            <div class="col-xs-12">
                <h2 class="text-center">Login</h2>
                <p>Welcome to <?php echo $config["general"]["title"] ?>. You will need an account to use this service.</p>
            </div>
        </div>
        <div class="row row-margin">
            <div class="col-xs-12">
                <form id="loginForm" method="POST" action="backend/login.php">
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
                        <?php
                        if ($config["general"]["enable_forgot_password"]) {
                            ?>
                            <button type="button" class="btn btn-default btn-flat" data-to-page="forgotpass">
                                Forgot password
                            </button>
                            <?php
                        }
                        ?>
                        <button type="submit" class="btn btn-success pull-right">Login</button>
                    </div>
                </form>
                <div class="alert alert-warning" id="warningInactive" style="display: none">
                    <strong>Warning!</strong> Your session has expired. Please authenticate to access your passwords.
                </div>
                <div class="alert alert-success" id="successLoggedOut" style="display: none">
                    <strong>Success!</strong> You have been logged out.
                </div>
                <div class="alert alert-success" id="successAccountCreated" style="display: none">
                    <strong>Success!</strong> You can now log in.
                </div>
                <div class="alert alert-danger" id="errorInvalidCredentials" style="display: none">
                    <strong>Error!</strong> The entered credentials do not match any account.
                </div>
                <div class="alert alert-danger" id="errorAccountLocked" style="display: none">
                    <strong>Error!</strong> The account you are trying to access has been locked. Please contact the
                    administrator.
                </div>
                <div class="alert alert-danger" id="errorLoginEmailInvalid" style="display: none">
                    <strong>Error!</strong> The specified email is invalid.
                </div>
                <div class="alert alert-danger" id="errorLoginFormInvalid" style="display: none">
                    <strong>Error!</strong> The form is invalid.
                </div>
                <div class="alert alert-danger" id="errorLoginServer" style="display: none">
                    <strong>Error!</strong> There has been a problem with the server. Please contact the administrator.
                </div>
                <div class="alert alert-danger" id="errorLoginDatabase" style="display: none">
                    <strong>Error!</strong> There was a problem with the database connection.
                </div>
            </div>
        </div>
    </div>
</div>
