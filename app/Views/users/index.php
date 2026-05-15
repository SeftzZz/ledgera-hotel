						<?= $this->extend('layouts/main') ?>
                        <?= $this->section('content') ?>
	                        <?php 
								$sessionCompanyId = session()->get('company_id');
								$sessionBranchId = session()->get('branch_id');
								$isSuperAdmin = $sessionCompanyId == 0;
							?>
                            <div class="container-xxl flex-grow-1 container-p-y">
                                <div class="card">
                                    <div class="card-datatable table-responsive pt-0">
						                <table class="dtUser table table-striped">
						                    <thead>
						                      	<tr>
							                        <th></th>
							                        <th>No.</th>
							                        <th>Name</th>
							                        <th>Company</th>
							                        <th>Branch</th>
							                        <th>Email</th>
							                        <th>Hp</th>
							                        <th>Status</th>
							                        <th>Action</th>
						                      	</tr>
						                    </thead>
						                    <tbody></tbody>
						                </table>
					                </div>
                                </div>

                                <!-- add modal form -->
				                <div class="modal fade" id="modalAddUser" tabindex="-1" aria-hidden="true">
									<div class="modal-dialog modal-lg modal-dialog-centered">
									    <div class="modal-content">
										    <form id="formAddUser" enctype="multipart/form-data">
										        <div class="modal-header">
										          	<h5 class="modal-title">Add New User</h5>
										          	<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
										        </div>
										        <div class="modal-body">
										          	<div class="row">
								                        <div class="col-md-4 mb-3">
								                            <label class="form-label">Full Name *</label>
										            		<input type="text" class="form-control" name="name_user" required>
								                        </div>
								                        <div class="col-md-4 mb-3">
														    <label class="form-label">Company *</label>

														    <select
														        name="company_user"
															    id="add_company_user"
														        class="form-select select2"
														        data-placeholder="Select Company"
														        <?= !$isSuperAdmin ? 'disabled' : '' ?>>

														        <?php foreach ($companies as $company): ?>

														            <?php if (
														                $isSuperAdmin ||
														                $company['id'] == $sessionCompanyId
														            ): ?>

														                <option
														                    value="<?= $company['id'] ?>"
														                    <?= (
														                        $company['id'] == $sessionCompanyId
														                    ) ? 'selected' : '' ?>>

														                    <?= esc($company['company_name']) ?>

														                </option>

														            <?php endif; ?>

														        <?php endforeach; ?>

														    </select>
														</div>

														<div class="col-md-4 mb-3">
														    <label class="form-label">Branch *</label>

														    <select
														        name="branch_user"
															    id="add_branch_user"
														        class="form-select select2"
														        data-placeholder="Select Branch"
														        <?= !$isSuperAdmin ? 'disabled' : '' ?>>

														        <?php foreach ($branches as $branch): ?>

														            <?php if (
														                $isSuperAdmin ||
														                $branch['id'] == $sessionBranchId
														            ): ?>

														                <option
														                    value="<?= $branch['id'] ?>"
														                    <?= (
														                        $branch['id'] == $sessionBranchId
														                    ) ? 'selected' : '' ?>>

														                    <?= esc($branch['branch_name']) ?>

														                </option>

														            <?php endif; ?>

														        <?php endforeach; ?>

														    </select>
														</div>
								                    </div>
								                    <div class="row">
								                    	<div class="col-md-4 mb-3">
														    <label class="form-label">Role *</label>
														    <select
														        name="role_user"
														        id="add_role_user"
														        class="form-select select2"
														        data-placeholder="Select Role">
														        <option value="">Select Role</option>
														    </select>
														</div>
								                        <div class="col-md-4 mb-3">
								                            <label class="form-label">Email *</label>
										            		<input type="email" class="form-control" name="email_user" required>
								                        </div>
								                        <div class="col-md-4 mb-3">
								                            <label class="form-label">Hp. *</label>
								                            <div class="input-group">
									                            <span class="input-group-text">+62</span>
									                            <input
									                              type="text"
									                              name="hp_user"
									                              class="form-control phone-number-mask"
									                              placeholder="812 3456 7890" required />
									                        </div>
								                        </div>
								                    </div>
								                    <div class="row">
								                        <div class="col-md-6 mb-3">
								                            <label class="form-label">Status</label>
								                            <select name="status_user" class="form-control required">
								                            	<option value="active" selected>Active</option>
							                                    <option value="inactive">Inactive</option>
							                                </select>
								                        </div>
								                        <div class="col-md-6 mb-3">
								                            <label class="form-label">Password</label>
										            		<input type="text" class="form-control" name="pass_user" required>
								                        </div>
								                    </div>
								                    <div class="row">
								                        <div class="col-md-12 mb-12">
								                        	<label class="form-label">Photo</label>
													    	<input type="file" class="form-control" name="foto_user" accept="image/*">
								                        </div>
								                    </div>
										        </div>
										        <div class="modal-footer">
										        	<button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
										          	<button type="submit" class="btn btn-primary">Save</button>
										        </div>
										    </form>
									    </div>
									</div>
								</div>

				                <!-- edit modal form -->
				                <div class="modal fade" id="modalEditUser" tabindex="-1" aria-hidden="true">
									<div class="modal-dialog modal-lg modal-dialog-centered">
									    <div class="modal-content">
										    <form id="formEditUser" enctype="multipart/form-data">
										        <div class="modal-header">
										          	<h5 class="modal-title">Edit User</h5>
										          	<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
										        </div>

										        <div class="modal-body">
										          	<input type="hidden" name="id" id="edit_id">
										          	<div class="row">
								                        <div class="col-md-12 mb-12">
								                            <label class="form-label" for="edit_name_user">Full Name *</label>
										            		<input type="text" class="form-control" name="name_user" id="edit_name_user" required>
								                        </div>
								                    </div>
								                    <div class="row">
								                    	<div class="col-md-6 mb-3">
								                            <label class="form-label" for="edit_company_user">Company *</label>
								                        	<select
															   	name="company_user"
															    id="edit_company_user"
															    class="form-select select2"
															    <?= !$isSuperAdmin ? 'disabled' : '' ?>>
															    <option value=""></option>
															    <?php foreach ($companies as $company): ?>
															        <option value="<?= $company['id'] ?>">
															            <?= esc($company['company_name']) ?>
															        </option>
															    <?php endforeach; ?>
															</select>
								                        </div>
								                        <div class="col-md-6 mb-3">
								                            <label class="form-label" for="edit_hp_user">Hp. *</label>
								                            <div class="input-group">
									                            <span class="input-group-text">+62</span>
									                            <input
									                              type="text"
									                              name="hp_user"
									                              id="edit_hp_user"
									                              class="form-control phone-number-mask"
									                              placeholder="812 3456 7890" required />
									                        </div>
								                        </div>
								                    </div>
								                    <div class="row">
								                    	<div class="col-md-6 mb-3">
								                            <label class="form-label" for="edit_status_user">Status *</label>
								                            <select name="status_user" id="edit_status_user" class="form-control required">
							                                    <option value="active">Active</option>
							                                    <option value="inactive">Inactive</option>
							                                </select>
								                        </div>
								                        <div class="col-md-6 mb-3">
								                            <label class="form-label" for="edit_pass_user">Password *</label>
										            		<input type="text" class="form-control" name="pass_user" id="edit_pass_user">
										            		<small class="text-muted">
														        Leave blank if you don't want to change
														    </small>
								                        </div>
								                    </div>
								                    <div class="mb-3">
													    <label class="form-label" for="edit_foto_user">Photo</label>
													    <div class="mb-2">
													        <img id="preview_foto" src="" class="img-thumbnail" style="max-height:120px">
													    </div>
													    <input type="file" class="form-control" name="foto_user" id="edit_foto_user" accept="image/*">
													    <small class="text-muted">
													        Leave blank if you don't want to change
													    </small>
													</div>
										        </div>

										        <div class="modal-footer">
										        	<input type="hidden" name="company_user" id="hidden_edit_company_user">
										        	<button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
										          	<button type="submit" class="btn btn-primary">Save</button>
										        </div>
										    </form>
									    </div>
									</div>
								</div>
                            </div>
                        <?= $this->endSection() ?>

                        <?= $this->section('scripts') ?>
                        <!-- DataTables -->
				        <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') ?>" />
				        <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') ?>" />
				        <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css') ?>" />
				        <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') ?>" />
		                <script src="<?= base_url('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') ?>"></script>
		                <!-- select2 -->
		                <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/select2/select2.css') ?>" />
		                <script src="<?= base_url('assets/vendor/libs/select2/select2.js') ?>"></script>

		                <script>
						    // DataTables Users
						    'use strict';
						    $(function () {
						        let dt_tableUser = $('.dtUser'), dt_user;
						        if (dt_tableUser.length) {
						        	dt_user = dt_tableUser.DataTable({
						        		processing: true,
					                	serverSide: true,
					                	responsive: true,
						                ajax: {
						                    url: "<?= base_url('users/datatable') ?>",
						                    type: "POST",
						                    data: d => {
						                        d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
						                    }
						                },
						                columns: [
						                    { data: null },          // responsive control
						                    { data: 'no_urut' },
						                    { data: 'name_user' },
						                    { data: 'company_user' },
						                    { data: 'branch_user' },
						                    { data: 'email_user' },
						                    { data: 'hp_user' },
						                    { data: 'status_user' },
						                    { data: 'action' }       // actions (HTML from backend)
						                ],
						                columnDefs: [
						                    {
						                        // Responsive control
						                        className: 'control',
						                        orderable: false,
						                        searchable: false,
						                        responsivePriority: 2,
						                        targets: 0,
						                        render: function () {
						                            return '';
						                        }
						                    },
						                    {
									          	targets: 1,
									          	orderable: false,
									          	searchable: false
									        },
					                    	{
					                    		// Name User
								          		targets: 2,
								          		responsivePriority: 1,
								          		render: function (data, type, full) {
									            	var $user_img = full['photo_user'], $name = full['name_user'];
									            	if ($user_img) {
									              		// For Avatar image
									             		var $output = '<img src="' + "uploads/profiles/" + $user_img + '" class="rounded-circleColor">';
									            	} else {
									              		// For Avatar badge
									              		var stateNum = Math.floor(Math.random() * 6);
									              		var states = ['success', 'danger', 'warning', 'info', 'primary', 'secondary'];
									              		var $state = states[stateNum],
									                	$name = full['name_user'],
									                	$initials = $name.match(/\b\w/g) || [];
									              		$initials = (($initials.shift() || '') + ($initials.pop() || '')).toUpperCase();
									              		$output = '<span class="avatar-initial rounded-circle bg-label-' + $state + '">' + $initials + '</span>';
									            	}
									            	
									            	// Creates full output for row
									            	var $row_output =
									              		'<div class="d-flex justify-content-start align-items-center user-name">' +
									              		'<div class="avatar-wrapper">' +
									              		'<div class="avatar me-2">' +
									              		$output +
									              		'</div>' +
									              		'</div>' +
									              		'<div class="d-flex flex-column">' +
									              		'<span class="emp_name text-truncate">' +
									              		$name +
									              		'</span>' +
									              		'</div>' +
									              		'</div>';
									            	return $row_output;
									          	}
								        	},
						                    {
						                        // Actions
						                        targets: -1,
						                        title: 'Actions',
						                        orderable: false,
						                        searchable: false
						                    }
					                	],
					                	order: [[2, 'asc']],
					                	dom:
					                    	'<"card-header flex-column flex-md-row"' +
					                        '<"head-label text-center">' +
					                        '<"dt-action-buttons text-end pt-3 pt-md-0"B>' +
					                    	'>' +
					                    	'<"row"' +
					                        '<"col-sm-12 col-md-6"l>' +
					                        '<"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>' +
					                    	'>' +
					                    	't' +
					                    	'<"row"' +
					                        '<"col-sm-12 col-md-6"i>' +
					                        '<"col-sm-12 col-md-6"p>' +
					                    	'>',
					                    displayLength: 10,
						                lengthMenu: [10, 25, 50, 100],
						                buttons: [
						                    {
						                        extend: 'collection',
						                        className: 'btn btn-label-primary dropdown-toggle me-2 waves-effect waves-light',
						                        text: '<i class="ti ti-file-export me-sm-1"></i> <span class="d-none d-sm-inline-block">Export</span>',
						                        buttons: [
						                            {
						                                extend: 'print',
						                                text: '<i class="ti ti-printer me-1"></i>Print',
						                                className: 'dropdown-item',
						                                exportOptions: { columns: [1,2,3,4,5,6] }
						                            },
						                            {
						                                extend: 'csv',
						                                text: '<i class="ti ti-file-text me-1"></i>Csv',
						                                className: 'dropdown-item',
						                                exportOptions: { columns: [1,2,3,4,5,6] }
						                            },
						                            {
						                                extend: 'pdf',
						                                text: '<i class="ti ti-file-description me-1"></i>Pdf',
						                                className: 'dropdown-item',
						                                exportOptions: { columns: [1,2,3,4,5,6] }
						                            }
						                        ]
						                    },
						                    {
						                        text: '<i class="ti ti-plus me-sm-1"></i> <span class="d-sm-inline-block">Add New User</span>',
						                        className: 'create-new btn btn-primary waves-effect waves-light',
						                        action: function () {
										            $('#modalAddUser').modal('show');
										        }
						                    }
						                ],
						                responsive: {
						                    details: {
						                        display: $.fn.dataTable.Responsive.display.modal({
						                            header: function (row) {
						                                let data = row.data();
						                                return 'Details of ' + data.name_user;
						                            }
						                        }),
						                        type: 'column',
						                        renderer: function (api, rowIdx, columns) {
						                            let data = $.map(columns, function (col) {
						                                return col.title !== ''
						                                    ? '<tr>' +
						                                          '<td>' + col.title + ':</td>' +
						                                          '<td>' + col.data + '</td>' +
						                                      '</tr>'
						                                    : '';
						                            }).join('');

						                            return data
						                                ? $('<table class="table"><tbody /></table>').append(data)
						                                : false;
						                        }
						                    }
						                }
						        	});
									$('div.head-label').html('<h5 class="card-title mb-0">Users List</h5>');
						        }
						    });

							$(document).ready(function () {
							    const sessionCompany = "<?= session()->get('company_id') ?>";
							    const isSuperAdmin = sessionCompany == "0";

							    // INIT SELECT2 
							    function initCompanySelect2(selector, modal) {
							        if ($(selector).hasClass('select2-hidden-accessible')) {
							            $(selector).select2('destroy');
							        }
							        $(selector).select2({
							            placeholder: 'Select Companies',
							            allowClear: true,
							            dropdownParent: modal
							        });
							    }

							    async function loadRoles() {

								    const companyId =
								        $('#add_company_user').val();

								    const branchId =
								        $('#add_branch_user').val();

								    const roleSelect =
								        $('#add_role_user');

								    roleSelect.html(`
								        <option value="">
								            Loading...
								        </option>
								    `);

								    try {

								        const response = await fetch(
								            `/users/get-roles?company_id=${companyId}&branch_id=${branchId}`
								        );

								        const result =
								            await response.json();

								        let html = `
								            <option value="">
								                Select Role
								            </option>
								        `;

								        if (
								            result.status &&
								            result.data.length
								        ) {

								            result.data.forEach(role => {

								                html += `
								                    <option value="${role.id}">
								                        ${role.name}
								                    </option>
								                `;
								            });
								        }

								        roleSelect.html(html);

								        roleSelect.trigger('change');

								    } catch (err) {

								        console.error(err);

								        roleSelect.html(`
								            <option value="">
								                Failed load roles
								            </option>
								        `);
								    }
								}

								// =====================================
								// CHANGE COMPANY / BRANCH
								// =====================================
								$(document).on(
								    'change',
								    '#add_company_user, #add_branch_user',
								    function () {
								        loadRoles();
								    }
								);

								// =========================================
								// EVENT
								// =========================================
								$(document).on(
								    'change',
								    '#add_company_user, #add_branch_user',
								    function () {
								        loadRoles();
								    }
								);

								// =========================================
								// INITIAL LOAD
								// =========================================

							    $('#modalAddUser').on('shown.bs.modal', function () {
								    initCompanySelect2('#add_company_user', $(this));
								    if (!isSuperAdmin) {
								        $('#add_company_user')
								            .val(sessionCompany)
								            .trigger('change')
								            .prop('disabled', true);

								        // Tambahkan hidden input agar tetap terkirim
								        if ($('#hidden_company_user').length === 0) {
								            $('<input>').attr({
								                type: 'hidden',
								                id: 'hidden_company_user',
								                name: 'company_user',
								                value: sessionCompany
								            }).appendTo('#formAddUser');
								        }
								    }
								});

								// RESET FORM ADD USER jika di tutup
							    $('#modalAddUser').on('hidden.bs.modal', function () {
							        const $form = $('#formAddUser');

							        // reset form native
							        $form[0].reset();

							        // reset company select2
							        const $companySelect = $('#add_company_user');
							        if ($companySelect.hasClass('select2-hidden-accessible')) {
							            $companySelect.val('').trigger('change');
							        }
							        $('#hidden_company_user').remove();

							        // unlock company select (untuk admin)
							        unlockAddCompany();

							        // hapus error / validation jika ada
							        $form.find('.is-invalid').removeClass('is-invalid');
							        $form.find('.invalid-feedback').remove();
							    });

							    $('#modalEditUser').on('shown.bs.modal', function () {
							        initCompanySelect2('#edit_company_user', $(this));
								    if (!isSuperAdmin) {
								         $('#edit_company_user').prop('disabled', true);
								    }
							    });

							    // Auto Set Company Add User
							    function lockAddCompany() {
							        $('#add_company_user')
							            .val(sessionCompany)
							            .trigger('change')
							            .on('select2:opening select2:selecting', e => e.preventDefault());
							    }

							    function unlockAddCompany() {
							        $('#add_company_user')
							            .off('select2:opening select2:selecting');
							    }

							    // Auto Set Company Edit User
							    function lockEditCompany() {
							        $('#edit_company_user')
							            .val(sessionCompany)
							            .trigger('change')
							            .on('select2:opening select2:selecting', e => e.preventDefault());

							        $('#companyHelpEdit').removeClass('d-none');
							    }

							    function unlockEditCompany() {
							        $('#edit_company_user')
							            .off('select2:opening select2:selecting');

							        $('#companyHelpEdit').addClass('d-none');
							    }

							    $(document).on('click', '.btn-edit', function () {
							        const id = $(this).data('id');
							        $.post("<?= base_url('users/get') ?>", {
							            id: id,
							            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
							        }, function (res) {

							            if (!res.status) {
							                Swal.fire('Error', res.message, 'error');
							                return;
							            }

							            const d = res.data;

							            $('#edit_id').val(d.id);
							            $('#edit_name_user').val(d.name);
							            $('#edit_hp_user').val(d.phone);
							            $('#edit_status_user').val(d.is_active);
							            $('#edit_company_user').val(d.company_id).trigger('change');
										$('#hidden_edit_company_user').val(d.company_id);

							            if (d.photo) {
							                $('#preview_foto').attr('src', "<?= base_url('uploads/profiles/') ?>" + d.photo).show();
							            } else {
							                $('#preview_foto').hide();
							            }

							            $('#modalEditUser').modal('show');
							        }, 'json');
							    });

							    // PREVIEW FOTO
							    $('#edit_foto').on('change', function () {
							        const file = this.files[0];
							        if (file) {
							            $('#preview_foto').attr('src', URL.createObjectURL(file));
							        }
							    });
							});

							// Submit form insert data
							$('#formAddUser').on('submit', function (e) {
							    e.preventDefault();
							    let formData = new FormData(this);
							    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
							    Swal.fire({
							        title: 'Add new user?',
							        icon: 'question',
							        showCancelButton: true,
							        reverseButtons: true,
							        confirmButtonText: 'Yes, save',
							        cancelButtonText: 'No'
							    }).then(result => {
							        if (result.isConfirmed) {
							            $.ajax({
							                url: "<?= base_url('users/store') ?>",
							                type: "POST",
							                data: formData,
							                processData: false,
							                contentType: false,
							                dataType: 'json',
							                success(res) {
							                    if (res.status) {
							                        Swal.fire({
							                            icon: 'success',
							                            title: 'Saved',
							                            text: res.message,
							                            timer: 1500,
							                            showConfirmButton: false
							                        });

							                        $('#modalAddUser').modal('hide');
							                        $('.dtUser').DataTable().ajax.reload(null, false);
							                    } else {
							                        Swal.fire('Failed', res.message, 'error');
							                    }
							                }
							            });
							        }
							    });
							});

							// Submit form edit data
							$('#formEditUser').on('submit', function (e) {
							    e.preventDefault();
							    let formData = new FormData(this);
							    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
							    Swal.fire({
							        title: 'Are you sure?',
							        icon: 'question',
							        showCancelButton: true,
							        showDenyButton: false,
							        confirmButtonText: 'Yes, update',
							        cancelButtonText: 'No',
							        reverseButtons: true
							    }).then((result) => {
							        if (result.isConfirmed) {
							            $.ajax({
									        url: "<?= base_url('users/update') ?>",
									        type: "POST",
									        data: formData,
									        processData: false,
									        contentType: false,
									        dataType: 'json',
									        success: function (res) {
									            if (res.status) {
									                Swal.fire({
									                    icon: 'success',
									                    title: 'Succeed',
									                    text: res.message,
									                    timer: 1500,
									                    showConfirmButton: false
									                });

									                $('#modalEditUser').modal('hide');
									                $('.dtUser').DataTable().ajax.reload(null, false);
									            } else {
									                Swal.fire('Failed', res.message, 'error');
									            }
									        },
									        error: function () {
									            Swal.fire('Error', 'Server error', 'error');
									        }
									    });
							        }
							    });
							});

							// Delete Soft
							$(document).on('click', '.btn-delete', function () {
							    const id = $(this).data('id');
							    Swal.fire({
							        title: 'Are you sure!!!',
							        text: 'Data will be deleted',
							        icon: 'warning',
							        showCancelButton: true,
							        confirmButtonText: 'Yes, delete it!',
							        cancelButtonText: 'Cancel',
							        reverseButtons: true
							    }).then((result) => {
							        if (result.isConfirmed) {
							            $.ajax({
							                url: "<?= base_url('users/delete') ?>",
							                type: "POST",
							                dataType: "json",
							                data: {
							                    id: id,
							                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
							                },
							                success: function (res) {
							                    if (res.status) {
							                        Swal.fire({
							                            icon: 'success',
							                            title: 'Success',
							                            text: res.message,
							                            timer: 1500,
							                            showConfirmButton: false
							                        });

							                        $('.dtUser').DataTable().ajax.reload(null, false);
							                    } else {
							                        Swal.fire('Gagal', res.message, 'error');
							                    }
							                },
							                error: function () {
							                    Swal.fire('Error', 'Terjadi kesalahan server', 'error');
							                }
							            });
							        }
							    });
							});
						</script>
                        <?= $this->endSection() ?>