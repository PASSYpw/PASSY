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
				<form id="form_change_password" action="action.php" method="post">
					<input type="hidden" name="a" value="user/changePassword">
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
					<button type="submit" class="btn btn-primary pull-right">Apply</button>
				</form>
			</div>
			<div class="col-xs-12">
				<h3>Change username</h3>
				<form id="form_change_username" action="action.php" method="post">
					<input type="hidden" name="a" value="user/changeUsername">
					<div class="text">
						<input type="text" class="form-control" title="New username" name="new_username" required
						       autocomplete="off"/>
						<label>New username</label>
					</div>
					<div class="alert alert-danger" id="error_username_exists" style="display: none">
						<strong>Error!</strong> User with username already exists.
					</div>
					<button type="submit" class="btn btn-primary pull-right">Apply</button>
				</form>
			</div>
            <div class="col-xs-12">
                <h3>Export</h3>
                <br />
                <p>Export passwords</p>
                <small>This will initialize a download of a file!</small>
                <form action="action.php" method="post">
                    <input type="hidden" name="a" value="misc/export">
                    <button type="submit"  class="btn btn-primary">Export</button>
                </form>

            </div>
            <div class="col-xs-12">
                <h3>Import</h3>
                <p>Import passwords</p>
                <small>From here you can easy import passwords, exported otherwhere</small>
                <form id="form_import" action="action.php", enctype="multipart/form-data" method="post">
                    <input type="hidden" name="a" value="misc/import">
                    <input type="file" name="parse-file">
                    <button type="submit" class="btn btn-primary">Import</button>
                </form>
                <div class="alert alert-success" id="successImported" style="display: none">
                    <strong>Success!</strong> Passwords have been imported.
                </div>
                <div class="alert alert-danger" id="errorImported" style="display: none">
                    <strong>Error!</strong> An error Occurred.
                </div>
            </div>
		</div>
	</div>
</div>
