<?php
include __DIR__ . "/../include/user.inc.php";
if (!isLoggedIn()) {
    header("Location: ../");
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
    <title>Passy.pw</title>

    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="../assets/css/ripple.min.css" rel="stylesheet">
    <link href="../assets/css/theme.min.css" rel="stylesheet">

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <!-- @formatter:off -->
            <span class="navbar-brand">Passy<small>.pw</small></span>
            <!-- @formatter:on -->
        </div>
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                   aria-expanded="false"><i class="material-icons" id="aMenu">more_vert</i></a>
                <ul class="dropdown-menu">
                    <li><a href="#"><i class="material-icons">edit</i> Profile Settings</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="#" id="btnLogout"><i class="material-icons">exit_to_app</i> Logout</a></li>
                </ul>
            </li>
        </ul>
    </div>
    <div class="container">
        <ul class="nav navbar-nav" style="position: static">
            <li data-page-highlight="passwords"><a href="#!p=passwords" data-to-page="passwords"><i
                            class="material-icons">lock_outline</i>
                    Passwords <span class="sr-only">(current)</span></a></li>
            <li data-page-highlight="groups"><a href="#!p=groups" data-to-page="groups"><i
                            class="material-icons">group</i> Groups</a></li>
        </ul>
    </div>
</nav>
<div class="content">
    <div class="load-spinner">
        <svg class="spinner" width="20px" height="20px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
            <circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle>
        </svg>
    </div>

    <div id="page_passwords" class="container" style="display: none">
        <div class="jumbotron">
            <div class="row">
                <div class="col-xs-12">
                    <h2 class="text-center">Passwords</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <div class="btn-group" role="group" aria-label="...">
                        <button type="button" class="btn btn-success" id="btnAdd" title="Add password..."><i
                                    class="material-icons">add</i> Add
                        </button>
                        <button type="button" class="btn btn-primary" id="btnRefresh" title="Refresh..."><i
                                    class="material-icons">refresh</i> Refresh
                        </button>
                    </div>
                </div>
            </div>
            <div class="row row-margin">
                <div class="col-xs-12">
                    <div class="table-responsive">
                        <table id="tablePasswords" class="table table-hover">
                            <thead>
                            <tr>
                                <th>Username</th>
                                <th>Password</th>
                                <th>Website</th>
                                <th>Date added</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody id="tbodyPasswords">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="page_groups" class="container" style="display: none">
        <div class="jumbotron">
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
        <li><a href="#">This context menu</a></li>
        <li><a href="#">is supposed to</a></li>
        <li><a href="#">have a function</a></li>
        <li><a href="#">but does not have one</a></li>
    </ul>
</div>

<!-- MODALS -->
<div class="modal fade" id="modalAdd" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content depth-5">
            <div class="modal-header">
                <h4 class="modal-title">Create password</h4>
            </div>
            <form method="post" action="backend/addPassword.php" id="formAddPassword">
                <div class="modal-body">
                    <p>
                        Fill the form below to create a new password entry. You will be able to view the password, edit the
                        entry and share the entry with others later.
                    </p>
                    <div class="form-group">
                        <div class="text">
                            <input type="text" class="form-control" title="Username" name="username"
                                   autocomplete="off"/>
                            <label>Username (optional)</label>
                        </div>
                        <div class="text">
                            <input type="password" class="form-control" title="Password" name="password"
                                   required autocomplete="off"/>
                            <label>Password</label>
                        </div>
                        <div class="text">
                            <input type="url" class="form-control" title="Password" name="website"
                                   autocomplete="off"/>
                            <label>Website (optional)</label>
                        </div>
                    </div>
                    <div class="alert alert-danger" id="errorDatabase" style="display: none">
                        <strong>Error!</strong> There was a problem with the database connection.
                    </div>
                    <div class="alert alert-danger" id="errorUnknown" style="display: none">
                        <strong>Error!</strong> An unhandled error occurred!
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-flat btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-flat btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../assets/js/jquery.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/ripple.min.js"></script>
<script src="../assets/js/app.main.js"></script>
<script src="../assets/js/ui.js"></script>
</body>
</html>
