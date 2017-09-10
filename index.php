<?php
require_once __DIR__ . "/config.inc.php";
require_once __DIR__ . "/meta.inc.php";
if ($generalConfig["redirect_ssl"] && isset($_SERVER["HTTPS"]) && $_SERVER['HTTPS'] == "off") {
	$redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	header('Location: ' . $redirect);
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
	<title><?= $customizationConfig["title"] ?></title>
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="assets/css/material-icons.min.css" rel="stylesheet">
	<link href="assets/css/ripple.min.css" rel="stylesheet">
	<link href="assets/css/app.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-default navbar-fixed-top">
	<div class="container">
		<div class="navbar-header">
			<!-- @formatter:off -->
            <span class="navbar-brand"><?= $customizationConfig["title"] ?></span>
            <!-- @formatter:on -->
		</div>
		<ul class="nav navbar-nav navbar-right">
			<li>
				<a href="#" id="btnLogout" data-page-scope="logged_in" style="display: none;">
					<i class="material-icons">exit_to_app</i>
				</a>
			</li>
			<li class="dropdown" data-page-scope="logged_in" style="display: none;">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
				   aria-expanded="false">
					<i class="material-icons" id="aMenu">more_vert</i>
				</a>
				<ul class="dropdown-menu">
					<li style="animation-delay: 100ms">
						<a href="#!p=user_settings" data-to-page="user_settings">
							<i class="material-icons">edit</i> User Settings
						</a>
					</li>

					<?php
					if ($generalConfig["login_history"]["enabled"]) {
						?>
						<li>
							<a href="#!p=login_history" data-to-page="login_history">
								<i class="material-icons">list</i> Login History
							</a>
						</li>
						<?php
					}
					?>
				</ul>
			</li>
		</ul>
	</div>
	<div class="container">
		<ul class="nav navbar-nav">
			<li data-page-highlight="login" data-page-scope="logged_out">
				<a href="#!p=login" data-to-page="login">
					<i class="material-icons">person</i> Login
				</a>
			</li>

			<?php
			if ($generalConfig["registration"]["enabled"]) {
				?>
				<li data-page-highlight="register" data-page-scope="logged_out">
					<a href="#!p=register" data-to-page="register">
						<i class="material-icons">person_pin_circle</i> Register
					</a>
				</li>
				<?php
			}
			?>

			<li data-page-highlight="password_list" data-page-scope="logged_in" style="display: none">
				<a href="#!p=password_list" data-to-page="password_list">
					<i class="material-icons">lock_outline</i> Passwords
				</a>
			</li>

			<li data-page-highlight="archived_password_list" data-page-scope="logged_in" style="display: none">
				<a href="#!p=archived_password_list" data-to-page="archived_password_list">
					<i class="material-icons">archive</i> Archive
				</a>
			</li>
		</ul>
	</div>
</nav>

<div class="statusMessageContainer" style="display: none">
	<div class="statusMessage text-center col-xs-11 col-sm-5 col-md-4 col-lg-3">
		<h3 class="statusMessageText"></h3>
		<button class="btn btn-flat btn-primary statusMessageButton"></button>
	</div>
</div>

<div class="content">
	<div class="load-spinner">
		<svg class="spinner" width="20px" height="20px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
			<circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle>
		</svg>
	</div>

	<?php
	include_once __DIR__ . "/page/page_login.inc.php";

	if ($generalConfig["registration"]["enabled"])
		include_once __DIR__ . "/page/page_register.inc.php";

	include_once __DIR__ . "/page/page_password_list.inc.php";

	include_once __DIR__ . "/page/page_archived_password_list.inc.php";

	if ($generalConfig["login_history"]["enabled"])
		include_once __DIR__ . "/page/page_login_history.inc.php";

	include_once __DIR__ . "/page/page_user_settings.inc.php";
	?>
</div>

<!-- CONTEXTMENU -->
<div class="dropdown contextmenu" id="dropdownContextMenu">
	<ul class="dropdown-menu">
		<li>
			<a href="#" data-to-page="refresh">
				<i class="material-icons">refresh</i> Refresh
			</a>
		</li>
	</ul>
</div>

<div class="footer">
	<div class="container">
		<div class="row">
			<div class="col-sm-6 col-xs-12">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 569.2 952.76" width="50px" height="50px">
					<path d="M500,41.24c-157.12,0-284.49,127.37-284.49,284.49h0V692.16a35.28,35.28,0,0,0,35.28,35.28h0A35.28,35.28,0,0,0,286,692.16V513.25l.57.66A283.8,283.8,0,0,0,500,610.23c157.12,0,284.49-127.37,284.49-284.49S657.11,41.24,500,41.24Zm0,497.86c-117.84,0-213.37-95.53-213.37-213.37S382.15,112.37,500,112.37s213.37,95.53,213.37,213.37S617.83,539.1,500,539.1Z"
					      transform="translate(-215.5 -41.24)"></path>
					<text transform="translate(0 901.39)"
					      style="font-size:190px; font-family:Roboto-Regular, Roboto, serif; letter-spacing:-0.07em">
						<tspan>PASSY</tspan>
					</text>
				</svg>
				<span class="text-muted hidden-xs" title="Build <?= PASSY_BUILD ?>"><?= PASSY_VERSION ?></span>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6 col-xs-12">
				<p class="text-muted">
					Store your passwords securely.
				</p>
			</div>
			<div class="col-sm-6 col-xs-12 text-right">
				<ul class="list-inline">
					<li><a href="<?= PASSY_REPO ?>" target="_blank">GitHub</a></li>
					<li><a href="<?= PASSY_BUGTRACKER ?>" target="_blank">Bug report</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<script src="assets/js/jquery.min.js "></script>
<script src="assets/js/bootstrap.min.js "></script>
<script src="assets/js/ripple.min.js"></script>
<script src="assets/js/passy.js"></script>
<?php
if ($generalConfig["recaptcha"]["enabled"]) {
	?>
	<script src='https://www.google.com/recaptcha/api.js'></script>
	<?php
}
?>
</body>
</html>
