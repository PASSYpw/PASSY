<div id="page_user_settings" data-apply-page-scope="logged_in" class="container" style="display: none">
    <div class="jumbotron">
        <div class="row">
            <div class="col-xs-12">
                <h2 class="text-center">User Settings</h2>
                <p>Your account needs to be secure! Change your password, add two-step-verifications or change
                    your
                    email.</p>
            </div>
        </div>
        <div class="row row-margin">
            <div class="col-xs-12">
                <h3>Change password</h3>
                <form id="formChangePassword" action="backend/user/changePassword.php" method="post">
                    <div class="text">
                        <input type="password" class="form-control" title="Current password" name="oldPassword"
                               required
                               autocomplete="off"/>
                        <label>Current password</label>
                    </div>
                    <div class="text">
                        <input type="password" class="form-control" title="New Password" name="newPassword"
                               required
                               autocomplete="off"/>
                        <label>New Password</label>
                    </div>
                    <div class="text">
                        <input type="password" class="form-control" title="New Password (again)"
                               name="newPassword2" required autocomplete="off"/>
                        <label>New Password (again)</label>
                    </div>
                    <p>This process could take a while, because all your passwords have to be encrypted
                        again!</p>
                    <button type="submit" class="btn btn-primary pull-right">Apply</button>
                </form>
            </div>
            <div class="col-xs-12">
                <h3>Change email</h3>
                <form id="formChangeEmail" action="backend/user/changeEmail.php" method="post">
                    <div class="text">
                        <input type="email" id="inputCurrentEmail" class="form-control" title="Current email"
                               disabled autocomplete="off"/>
                        <label>Current email</label>
                    </div>
                    <div class="text">
                        <input type="password" class="form-control" title="Current password" name="password"
                               autocomplete="off"/>
                        <label>Current password</label>
                    </div>
                    <div class="text">
                        <input type="email" class="form-control" title="New email" name="newemail" required
                               autocomplete="off"/>
                        <label>New email</label>
                    </div>
                    <button type="submit" class="btn btn-primary pull-right">Apply</button>
                </form>
            </div>
        </div>
    </div>
</div>