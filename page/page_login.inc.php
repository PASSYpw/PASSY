<?php
include_once __DIR__ . "/../config.inc.php";
?>
<div id="page_login" data-apply-page-scope="logged_out" class="container" style="display: none">
    <div class="jumbotron depth-1">
        <div class="row">
            <div class="col-xs-12">
                <h2 class="text-center">Login</h2>
                <p>Welcome to <?= $customizationConfig["title"] ?>. You need an account to use this password
                    manager.</p>
            </div>
        </div>
        <div class="row row-margin">
            <div class="col-xs-12">
                <form id="page_login_form_login" method="POST" action="action.php">
                    <input type="hidden" name="a" value="user/login" readonly style="display: none">
                    <div class="form-group">
                        <div class="text">
                            <input type="text" class="form-control" title="Username (required)" minlength="3"
                                   name="username" autocomplete="username" required/>
                            <label>Username <span class="text-danger">*</span></label>
                        </div>
                        <div class="text">
                            <input type="password" class="form-control" title="Password (required)" minlength="8"
                                   name="password" autocomplete="current-password" required/>
                            <label>Password <span class="text-danger">*</span></label>
                        </div>
                        <input id="checkboxPersistent" type="checkbox" title="Stay logged in" name="persistent">
                        <label for="checkboxPersistent">Stay logged in</label>
                    </div>

                    <div class="modal fade" id="page_login_modal_2fa" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content depth-5">
                                <div class="modal-header">
                                    <h4 class="modal-title">Two-Factor-Authentication</h4>
                                </div>
                                <div class="modal-body">
                                    <p>
                                        You have two-factor-authentication enabled! Please enter you generated 6-digit
                                        code below.
                                        <br>
                                        You may enter your recovery code, which will disable two-factor-authentication
                                        permanently.
                                    </p>
                                    <div class="form-group">
                                        <div class="text">
                                            <input type="text" class="form-control"
                                                   title="Authentication Code (required)" name="2faCode"
                                                   autocomplete="off" disabled/>
                                            <label>Authentication Code <span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer clearfix">
                                    <span class="text-danger pull-left">* Required</span>
                                    <button type="button" class="btn btn-flat btn-danger" data-dismiss="modal">Cancel
                                    </button>
                                    <button type="submit" class="btn btn-flat btn-primary">Login</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group clearfix">
                        <span class="text-danger">* Required</span>
                        <button type="submit" class="btn btn-success pull-right">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
