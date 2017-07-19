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
							<input type="text" class="form-control" title="Username" name="username"
							       required/>
							<label>Username</label>
						</div>
						<div class="text">
							<input type="password" class="form-control" title="Password" name="password"
							       required/>
							<label>Password</label>
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
										You may enter your recovery code, which will disable two-factor-authentication.
									</p>
									<div class="form-group clearfix">
										<div class="text">
											<input type="text" class="form-control"
											       title="Authentication Code" name="2faCode"
											       autocomplete="off" disabled/>
											<label>Authentication Code</label>
										</div>
									</div>
									<div class="alert alert-danger" id="errorWrongCode" style="display: none">
										<strong>Error!</strong> The entered two-factor-authentication code is invalid!
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-flat btn-danger" data-dismiss="modal">Cancel
									</button>
									<button type="submit" class="btn btn-flat btn-primary">Login</button>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
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
