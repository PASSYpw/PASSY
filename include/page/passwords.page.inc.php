<div id="page_passwords" data-apply-page-scope="logged_in" class="container" style="display: none">
    <div class="jumbotron">
        <div class="row">
            <div class="col-xs-12">
                <h2 class="text-center">Passwords</h2>
                <p>Add new passwords or view your saved passwords.</p>
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
    <button class="btn btn-fab" id="btnAdd" title="Add password..."><i class="material-icons">add</i></button>

    <!-- MODALS -->
    <div class="modal fade" id="modalAdd" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content depth-5">
                <div class="modal-header">
                    <h4 class="modal-title">Create password</h4>
                </div>
                <form method="post" action="backend/addPassword.php" id="formAddPassword" autocomplete="off">
                    <div class="modal-body">
                        <p>
                            Fill the form below to create a new password entry. You will be able to view the password, edit
                            the entry and share the entry with others later.
                        </p>
                        <div class="form-group">
                            <div class="text">
                                <input type="text" class="form-control" title="Username" name="username"
                                       autocomplete="off"/>
                                <label>Username (optional)</label>
                            </div>
                            <div class="text">
                                <input type="password" class="form-control" title="Password" name="password" required
                                       autocomplete="off"/>
                                <label>Password</label>
                            </div>
                            <div class="text">
                                <input type="text" class="form-control" title="Website" name="website" autocomplete="off"/>
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

    <div class="modal fade" id="modalEdit" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content depth-5">
                <div class="modal-header">
                    <h4 class="modal-title">Edit password</h4>
                </div>
                <form method="post" action="backend/editPassword.php" id="formEditPassword" autocomplete="off">
                    <input id="formEditPasswordId" type="hidden" name="id" value="" style="display: none"/>
                    <div class="modal-body">
                        <p>
                            Put text here
                        </p>
                        <div class="form-group">
                            <div class="text">
                                <input id="formEditPasswordUsername" type="text" class="form-control" title="Username"
                                       name="username"
                                       autocomplete="off"/>
                                <label>Username (optional)</label>
                            </div>
                            <div class="text">
                                <input id="formEditPasswordPassword" type="password" class="form-control" title="Password"
                                       name="password" required
                                       autocomplete="off"/>
                                <label>Password</label>
                            </div>
                            <div class="text">
                                <input id="formEditPasswordWebsite" type="text" class="form-control" title="Website"
                                       name="website" autocomplete="off"/>
                                <label>Website (optional)</label>
                            </div>
                        </div>
                        <div class="alert alert-danger" id="errorEditDatabase" style="display: none">
                            <strong>Error!</strong> There was a problem with the database connection.
                        </div>
                        <div class="alert alert-danger" id="errorEditUnknown" style="display: none">
                            <strong>Error!</strong> An unhandled error occurred!
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-flat btn-danger" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-flat btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
