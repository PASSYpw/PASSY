<div id="page_user_settings" data-apply-page-scope="logged_in" class="container" style="display: none">
    <div class="jumbotron">
        <div class="row">
            <div class="col-xs-12">
                <h2 class="text-center">User Settings</h2>
                <p>Your account needs to be secure! Change your password, add two-step-verifications or change your
                    email.</p>
            </div>
        </div>
        <div class="row row-margin">
            <div class="col-xs-12">
                <h3>Change password</h3>
                <form id="page_user_settings_form_change_password" action="action.php" method="post">
                    <input type="hidden" name="a" value="user/changePassword">
                    <div class="text">
                        <input type="password" class="form-control" title="Current Password" name="password"
                               required
                               autocomplete="off"/>
                        <label>Current Password</label>
                    </div>
                    <div class="text">
                        <input type="password" class="form-control" title="New Password" name="new_password"
                               required
                               autocomplete="off"/>
                        <label>New Password</label>
                    </div>
                    <div class="text">
                        <input type="password" class="form-control" title="New Password (again)"
                               name="new_password2" required autocomplete="off"/>
                        <label>New Password (again)</label>
                    </div>
                    <p>This process could take a while, because all your passwords have to be decrypted and encrypted
                        again!</p>
                    <div class="alert alert-danger" id="errorChangePasswordInvalidCredentials" style="display: none">
                        <strong>Error!</strong> Current password is not correct.
                    </div>
                    <button type="submit" class="btn btn-primary pull-right">Apply</button>
                </form>
            </div>
            <div class="col-xs-12">
                <h3>Change username</h3>
                <form id="page_user_settings_form_change_username" action="action.php" method="post">
                    <input type="hidden" name="a" value="user/changeUsername">
                    <div class="text">
                        <input type="password" class="form-control" title="Current Password" name="password"
                               required
                               autocomplete="off"/>
                        <label>Current Password</label>
                    </div>
                    <div class="text">
                        <input type="text" class="form-control" title="New username" name="new_username" required
                               autocomplete="off"/>
                        <label>New username</label>
                    </div>
                    <div class="alert alert-danger" id="error_username_exists" style="display: none">
                        <strong>Error!</strong> User with username already exists.
                    </div>
                    <div class="alert alert-danger" id="errorChangeEmailInvalidCredentials" style="display: none">
                        <strong>Error!</strong> Current password is not correct.
                    </div>
                    <button type="submit" class="btn btn-primary pull-right">Apply</button>
                </form>
            </div>
            <div class="col-xs-12">
                <h3>Export</h3>
                <p>This will put all your passwords into a file, so you can import them later in any PASSY server.</p>
                <form id="page_user_settings_form_export" action="action.php" method="post">
                    <input type="hidden" name="a" value="misc/export">
                    <select name="format">
                        <option value="passy">PASSY</option>
                        <option value="keepass" disabled>KeePass (not implemented)</option>
                        <option value="csv">CSV</option>
                        <option value="text" disabled>Plaintext</option>
                    </select>
                    <p>This process could take a while, because all your passwords have to be decrypted!</p>
                    <button type="submit" class="btn btn-primary pull-right">Export</button>
                </form>

            </div>
            <div class="col-xs-12">
                <h3>Import</h3>
                <p>Import your previously exported passwords.</p>
                <form id="page_user_settings_form_import" action="action.php" enctype="multipart/form-data"
                      method="post">
                    <input type="file" id="import-file" name="parse-file">
                    <input type="hidden"  name="a" value="misc/import">
                    <button type="submit" class="btn btn-primary pull-right">Import</button>
                </form>
            </div>
        </div>
    </div>
</div>
