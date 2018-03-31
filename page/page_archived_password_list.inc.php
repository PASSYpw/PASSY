<div id="page_archived_password_list" data-apply-page-scope="logged_in" class="container" style="display: none">
    <div class="jumbotron">
        <div class="row">
            <div class="col-xs-12">
                <h2 class="text-center">Archived Passwords</h2>
                <p>The passwords listed here have been archived. You can restore them here if they have been archived by
                    accident.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="text">
                    <input type="text" class="form-control" title="Search" data-search-in="#tableArchivedPasswords"
                           autocomplete="on"/>
                    <label>Search</label>
                </div>
            </div>
        </div>
        <div class="row row-margin">
            <div class="col-xs-12">
                <div class="table-responsive">
                    <table id="tableArchivedPasswords" class="table table-hover">
                        <thead>
                        <tr>
                            <th>Username</th>
                            <th>Password</th>
                            <th>Description</th>
                            <th>Date archived</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody id="tbodyArchivedPasswords">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
