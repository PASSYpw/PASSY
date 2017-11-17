<?php
include_once __DIR__ . "/../config.inc.php";
?>
<div id="page_register" data-apply-page-scope="logged_out" class="container" style="display: none">
	<div class="jumbotron depth-1">
		<div class="row">
			<div class="col-xs-12">
				<h2 class="text-center">Register</h2>
				<p>Your very own account is just some clicks away.</p>
			</div>
		</div>
		<div class="row row-margin">
			<div class="col-xs-12">
				<form id="page_register_form_register" method="POST" action="action.php">
					<input type="hidden" name="a" value="user/register" readonly style="display: none">
					<div class="form-group">
						<div class="text">
							<input type="text" class="form-control" title="Username (required)" minlength="3" name="username"
							       required/>
							<label>Username <span class="text-danger">*</span></label>
						</div>
						<div class="text">
							<input type="password" class="form-control" title="Password (required)" minlength="8" name="password"
							       required/>
							<label>Password <span class="text-danger">*</span></label>
						</div>
						<div class="text">
							<input type="password" class="form-control" title="Repeat Password (required)" name="password2"
							       required/>
							<label>Password (again) <span class="text-danger">*</span></label>
						</div>
						<?php
						if ($generalConfig["recaptcha"]["enabled"]) {
							echo '<div class="g-recaptcha" data-sitekey="' . $generalConfig["recaptcha"]["website_key"] . '"></div>';
						}
						?>
					</div>
					<div class="form-group">
						<span class="text-danger">* Required</span>
						<button type="submit" class="btn btn-primary pull-right">Register</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
