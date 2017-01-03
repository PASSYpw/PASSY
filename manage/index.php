<?php
include __DIR__ . "/../include/user.inc.php";
if(isLoggedIn() != 1) {
    header("Location: /");
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
            <span class="navbar-brand" href="#">Passy<small>.pw</small></span>
        </div>
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                   aria-expanded="false"><i class="material-icons" id="aMenu">more_vert</i></a>
                <ul class="dropdown-menu">
                    <li><a href="#"><i class="material-icons">edit</i> Profile Settings</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="#"><i class="material-icons">exit_to_app</i> Logout</a></li>
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
        <li><a href="#">but hasnt one</a></li>
    </ul>
</div>

<!-- MODALS -->
<div class="modal fade" id="modalAdd" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content depth-5">
            <div class="modal-header">
                <h4 class="modal-title">Add password...</h4>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs nav-justified depth-2" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#tabPrivate" aria-controls="tabPrivate" role="tab" data-toggle="tab">Private password</a>
                    </li>
                    <li role="presentation">
                        <a href="#tabShared" aria-controls="tabShared" role="tab" data-toggle="tab">Shared password</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade in active" id="tabPrivate">
                        <div class="form-group">
                            <div class="text">
                                <input id="inputURL" type="text" class="form-control" title="URL" required/>
                                <label>URL</label>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="tabShared">
                        <div class="form-group">
                            <span>File</span>
                            <input id="inputFileUpload" type="file" accept="application/x-bittorrent" required/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-flat btn-danger" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-flat btn-primary">Add</button>
            </div>
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