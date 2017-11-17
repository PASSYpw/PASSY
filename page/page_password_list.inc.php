<div id="page_password_list" data-apply-page-scope="logged_in" class="container" style="display: none">
	<div class="jumbotron">
		<div class="row">
			<div class="col-xs-12">
				<h2 class="text-center">Passwords</h2>
				<p>Add new passwords or view your saved passwords.</p>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<div class="text">
					<input type="text" class="form-control" title="Search" data-search-in="#tablePasswords"
					       autocomplete="on"/>
					<label>Search</label>
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
							<th>Description</th>
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
	<button class="btn btn-fab" id="btnAdd" title="Add password" data-toggle="modal"
	        data-target="#page_password_list_modal_add">
		<i class="material-icons">add</i>
	</button>

	<!-- MODALS -->
	<div class="modal fade" id="page_password_list_modal_add" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content depth-5">
				<div class="modal-header">
					<h4 class="modal-title">Create password</h4>
				</div>
				<form method="post" action="action.php" id="page_password_list_form_add" autocomplete="off">
					<input type="hidden" name="a" value="password/create" readonly style="display: none">
					<div class="modal-body">
						<p>
							Fill the form below to create a new password entry. You will be able to edit the password
							afterwards.
						</p>
						<div class="text">
							<input type="text" tabindex="1" class="form-control" title="Username" name="username"
							       autocomplete="off"/>
							<label>Username</label>
						</div>
						<div class="text hasbtn">
							<input type="password" tabindex="2" id="addPasswordInput" class="form-control"
							       title="Password (required)"
							       name="password" required autocomplete="off"/>
							<label>Password <span class="text-danger">*</span></label>
							<button class="btn-input" data-random-value="#addPasswordInput" tabindex="-1"
							        type="button">
								<i class="material-icons">shuffle</i>
							</button>
						</div>
						<div class="text">
							<input type="text" tabindex="3" class="form-control" title="Description"
							       name="description"
							       autocomplete="off"/>
							<label>Description</label>
						</div>
					</div>
					<div class="modal-footer clearfix">
						<span class="text-danger pull-left">* Required</span>
						<button type="button" class="btn btn-flat btn-danger" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-flat btn-primary">Add</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="page_password_list_modal_edit" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content depth-5">
			<div class="modal-header">
				<h4 class="modal-title">Edit password</h4>
			</div>
			<form method="post" action="action.php" id="page_password_list_form_edit" autocomplete="off">
				<input type="hidden" name="id" value=""/>
				<input type="hidden" name="a" value="password/edit" readonly style="display: none"/>
				<div class="modal-body">
					<p>
						Update your password details.
					</p>
					<div class="form-group">
						<div class="text">
							<input type="text" tabindex="1" class="form-control" title="Username" name="username"
							       autocomplete="off"/>
							<label>Username</label>
						</div>
						<div class="text">
							<input type="password" tabindex="2" class="form-control" title="Password (required)"
							       name="password" required autocomplete="off"/>
							<label>Password <span class="text-danger">*</span></label>
						</div>
						<div class="text">
							<input type="text" tabindex="3" class="form-control" title="Description"
							       name="description"
							       autocomplete="off"/>
							<label>Description</label>
						</div>
					</div>
				</div>
				<div class="modal-footer clearfix">
					<span class="text-danger pull-left">* Required</span>
					<button type="button" class="btn btn-flat btn-danger" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-flat btn-primary">Save</button>
				</div>
			</form>
		</div>
	</div>
</div>
