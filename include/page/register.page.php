<?php
require_once __DIR__ . "/../config.inc.php";
?>
<div id="page_register" data-apply-page-scope="logged_out" class="container" style="display: none">
    <div class="jumbotron depth-1">
        <div class="row">
            <div class="col-xs-12">
                <h2 class="text-center">Register</h2>
            </div>
        </div>
        <div class="row row-margin">
            <div class="col-xs-12">
                <form id="registerForm" method="POST" action="backend/register.php">
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
                        <?php
                        if ($config["recaptcha"]["enabled"]) {
                            echo '<div class="g-recaptcha" data-sitekey="' . $config["recaptcha"]["website_key"] .  '"></div>';
                        }
                        ?>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary pull-right">Register</button>
                    </div>
                </form>
                <div class="alert alert-danger" id="errorPasswordsNotMatch" style="display: none">
                    <strong>Error!</strong> The passwords do not match.
                </div>
                <div class="alert alert-danger" id="errorAccountRegistered" style="display: none">
                    <strong>Error!</strong> The specified email is already in use.
                </div>
                <div class="alert alert-danger" id="errorEmailInvalid" style="display: none">
                    <strong>Error!</strong> The specified email is invalid.
                </div>
                <div class="alert alert-danger" id="errorVerification" style="display: none">
                    <strong>Error!</strong> The captcha could not be verified.
                </div>
                <div class="alert alert-danger" id="errorFormInvalid" style="display: none">
                    <strong>Error!</strong> The form is invalid.
                </div>
                <div class="alert alert-danger" id="errorRegisterServer" style="display: none">
                    <strong>Error!</strong> There has been a problem with the server. Please contact the administrator.
                </div>
                <div class="alert alert-danger" id="errorDatabase" style="display: none">
                    <strong>Error!</strong> There has been a problem with the database connection.
                </div>
            </div>
        </div>
    </div>
</div>