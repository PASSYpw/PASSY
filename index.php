<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ff5722">
    <title>Passy.pw</title>

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
<div class="content">
    <nav class="navbar navbar-default depth-2">
        <div class="container">
            <div class="navbar-header">
                <span class="navbar-brand" href="#">Passy<small>.pw</small></span>
            </div>
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false"><i class="material-icons" id="aMenu">more_vert</i></a>
                    <ul class="dropdown-menu">
                        <li><a href="#"><i class="material-icons">info_outline</i> Not registered?</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="#"><i class="material-icons">track_changes</i> Forgot password?</a></li>
                    </ul>
                </li>
            </ul>
        </div>
        <div class="container">
            <ul class="nav navbar-nav">
                <li data-page-highlight="login"><a href="#!p=login" data-to-page="login"><i
                            class="material-icons">lock_open</i>
                        Login</a></li>
                <li data-page-highlight="login"><a href="#!p=login" data-to-page="login"><i
                            class="material-icons">fiber_new</i>
                        Register</a></li>
            </ul>
        </div>
    </nav>

    <div id="page_login" class="container" style="display: none">
        <div class="jumbotron depth-1">
            <div class="row">
                <div class="col-xs-12">
                    <h2 class="text-center">Login</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">

                </div>
            </div>
            <div class="row row-margin">
                <div class="col-xs-12">
                    <div class="table-responsive">
                        <table id="tablePasswords" class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th>Username</th>
                                <th>Password</th>
                                <th>Website</th>
                                <th>Date added</th>
                                <th>Added by</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr data-password-id="234324324">
                                <td>dummyname</td>
                                <td><a href="#" class="btn btn-default btn-flat btn-block"><i class="material-icons">lock</i></a>
                                </td>
                                <td><a href="https://dummy.me/">https://dummy.me/</a></td>
                                <td>12.3.2016</td>
                                <td>admin</td>
                            </tr>
                            <tr data-password-id="4353453455">
                                <td>tux@scrumplex.net</td>
                                <td><a href="#" class="btn btn-default btn-flat btn-block"><i class="material-icons">lock</i></a>
                                </td>
                                <td><a href="https://git.scrumplex.net/">https://git.scrumplex.net/</a></td>
                                <td>12.4.2016</td>
                                <td>admin</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="page_forgotpass" class="container" style="display: none">
        <div class="jumbotron depth-1">
            <div class="row">
                <div class="col-xs-12">
                    <h2 class="text-center">Groups</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CONTEXTMENU -->
<div class="dropdown contextmenu" id="dropdownContextMenu">
    <ul class="dropdown-menu">
        <li><a onclick="location.reload();"><i class="material-icons">refresh</i> Reload</a></li>
        <li><a><i class="material-icons">help</i> Help</a></li> <!-- //TODO: Add Help-Function -->
    </ul>
</div>

<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/ripple.min.js"></script>
<script src="assets/js/app.login.js"></script>
<script src="assets/js/ui.js"></script>
</body>
</html>