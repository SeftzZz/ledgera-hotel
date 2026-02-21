						<?= $this->extend('layouts/main') ?>
                        <?= $this->section('content') ?>
	                        <?php 
								$sessionCompanyId = session()->get('company_id');
								$isSuperAdmin = $sessionCompanyId == 0;
							?>
                            <div class="container-xxl flex-grow-1 container-p-y">
                                <div class="card">
                                    <div class="card-datatable table-responsive pt-0">
						                <table class="dtCoa table table-striped">
						                    <thead>
						                      	<tr>
							                        <th></th>
							                        <th>No.</th>
							                        <th>Company</th>
							                        <th>Code</th>
							                        <th>Name</th>
							                        <th>Type</th>
							                        <th>Induk Code</th>
							                        <th>Cash Flow</th>
							                        <th>Status</th>
							                        <th>Action</th>
						                      	</tr>
						                    </thead>
						                    <tbody></tbody>
						                </table>
					                </div>
                                </div>

                                <!-- add modal form -->
                                <div class="modal fade" id="modalAddCoa" tabindex="-1" aria-hidden="true">
									<div class="modal-dialog modal-lg modal-dialog-centered">
									    <div class="modal-content">
										    <form id="formAddCoa" enctype="multipart/form-data">
										        <div class="modal-header">
										          	<h5 class="modal-title">Add New COA</h5>
										          	<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
										        </div>
										        <div class="modal-body">
										          	<div class="row">
								                        <div class="col-md-6 mb-3">
								                            <label class="form-label">Company *</label>
								                        	<select
															    name="kantor_coa"
															    id="add_kantor_coa"
															    class="form-select select2"
															    data-placeholder="Select Company"
															    <?= !$isSuperAdmin ? 'disabled' : '' ?>>
															    <?php foreach ($companies as $company): ?>
																    <?php if ($isSuperAdmin || $company['id'] == $sessionCompanyId): ?>
																        <option value="<?= $company['id'] ?>"
																            <?= $company['id'] == $sessionCompanyId && !$isSuperAdmin ? 'selected' : '' ?>>
																            <?= esc($company['company_name']) ?>
																        </option>
																    <?php endif; ?>
																<?php endforeach; ?>
															</select>
								                        </div>
								                        <div class="col-md-6 mb-3">
								                            <label class="form-label">Account Code *</label>
										            		<input type="text" class="form-control" name="kode_coa" required>
								                        </div>
								                    </div>
								                    <div class="row">
								                        <div class="col-md-6 mb-3">
								                            <label class="form-label">Account Name *</label>
										            		<input type="text" class="form-control" name="nama_coa" required>
								                        </div>
								                        <div class="col-md-6 mb-3">
								                            <label class="form-label">Account Type *</label>
										            		<input type="text" class="form-control" name="tipe_coa" required>
								                        </div>
								                    </div>
								                    <div class="row">
								                        <div class="col-md-6 mb-3">
								                            <label class="form-label">Parent Code</label>
										            		<input type="text" class="form-control" name="induk_coa">
								                        </div>
								                        <div class="col-md-6 mb-3">
								                            <label class="form-label">Cashflow Type</label>
										            		<input type="text" class="form-control" name="aruskas_coa">
								                        </div>
								                    </div>
								                    <div class="row">
								                        <div class="col-md-6 mb-3">
								                            <label class="form-label">Status</label>
								                            <select name="status_coa" class="form-control required">
								                            	<option value="1" selected>Active</option>
							                                    <option value="0">Inactive</option>
							                                </select>
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
				                <div class="modal fade" id="modalEditCoa" tabindex="-1" aria-hidden="true">
									<div class="modal-dialog modal-lg modal-dialog-centered">
									    <div class="modal-content">
										    <form id="formEditCoa" enctype="multipart/form-data">
										        <div class="modal-header">
										          	<h5 class="modal-title">Edit COA</h5>
										          	<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
										        </div>

										        <div class="modal-body">
										          	<input type="hidden" name="id" id="edit_id">
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
								                            <label class="form-label" for="edit_kode_coa">Account Code *</label>
										            		<input type="text" class="form-control" name="kode_coa" id="edit_kode_coa" required>
								                        </div>
								                    </div>
								                    <div class="row">
								                    	<div class="col-md-6 mb-3">
								                            <label class="form-label" for="edit_nama_coa">Account Name *</label>
										            		<input type="text" class="form-control" name="nama_coa" id="edit_nama_coa" required>
								                        </div>
								                        <div class="col-md-6 mb-3">
								                            <label class="form-label" for="edit_tipe_coa">Account Type *</label>
										            		<input type="text" class="form-control" name="tipe_coa" id="edit_tipe_coa" required>
								                        </div>
								                    </div>
								                    <div class="row">
								                    	<div class="col-md-6 mb-3">
								                            <label class="form-label" for="edit_induk_coa">Parent Code</label>
										            		<input type="text" class="form-control" name="induk_coa" id="edit_induk_coa" required>
								                        </div>
								                        <div class="col-md-6 mb-3">
								                            <label class="form-label" for="edit_aruskas_coa">Cashflow Type</label>
										            		<input type="text" class="form-control" name="aruskas_coa" id="edit_aruskas_coa" required>
								                        </div>
								                    </div>
								                    <div class="row">
								                    	<div class="col-md-12 mb-12">
								                            <label class="form-label" for="edit_status_coa">Status *</label>
								                            <select name="status_coa" id="edit_status_coa" class="form-control required">
							                                    <option value="1">Active</option>
							                                    <option value="0">Inactive</option>
							                                </select>
								                        </div>
								                    </div>
										        </div>

										        <div class="modal-footer">
										        	<input type="hidden" name="kantor_coa" id="hidden_edit_kantor_coa">
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
						    // DataTables Coa
						    'use strict';
						    $(function () {
						        let dt_tableCoa = $('.dtCoa'), dt_coa;
						        if (dt_tableCoa.length) {
						        	dt_coa = dt_tableCoa.DataTable({
						        		processing: true,
					                	serverSide: true,
					                	responsive: true,
						                ajax: {
						                    url: "<?= base_url('coa/datatable') ?>",
						                    type: "POST",
						                    data: d => {
						                        d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
						                    }
						                },
						                columns: [
						                    { data: null },          // responsive control
						                    { data: 'no_urut' },
						                    { data: 'kantor_coa' },
						                    { data: 'kode_coa' },
						                    { data: 'nama_coa' },
						                    { data: 'tipe_coa' },
						                    { data: 'induk_coa' },
						                    { data: 'aruskas_coa' },
						                    { data: 'status_coa' },
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
									            	var $name = full['kantor_coa'];
									            	// Creates full output for row
									            	var $row_output =
									              		'<div class="d-flex justify-content-start align-items-center user-name">' +
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
					                	order: [[3, 'asc']],
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
						                                exportOptions: { columns: [1,2,3,4,5,6,7,8] }
						                            },
						                            {
						                                extend: 'csv',
						                                text: '<i class="ti ti-file-text me-1"></i>Csv',
						                                className: 'dropdown-item',
						                                exportOptions: { columns: [1,2,3,4,5,6,7,8] }
						                            },
						                            {
						                                extend: 'pdf',
						                                text: '<i class="ti ti-file-description me-1"></i>Pdf',
						                                className: 'dropdown-item',
						                                exportOptions: { columns: [1,2,3,4,5,6,7,8] }
						                            }
						                        ]
						                    },
						                    {
						                        text: '<i class="ti ti-plus me-sm-1"></i> <span class="d-sm-inline-block">Add New COA</span>',
						                        className: 'create-new btn btn-primary waves-effect waves-light',
						                        action: function () {
										            $('#modalAddCoa').modal('show');
										        }
						                    }
						                ],
						                responsive: {
						                    details: {
						                        display: $.fn.dataTable.Responsive.display.modal({
						                            header: function (row) {
						                                let data = row.data();
						                                return 'Details of ' + data.nama_coa;
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
									$('div.head-label').html('<h5 class="card-title mb-0">COA List</h5>');
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

							    $('#modalAddCoa').on('shown.bs.modal', function () {
								    initCompanySelect2('#add_kantor_coa', $(this));
								    if (!isSuperAdmin) {
								        $('#add_kantor_coa')
								            .val(sessionCompany)
								            .trigger('change')
								            .prop('disabled', true);

								        // Tambahkan hidden input agar tetap terkirim
								        if ($('#hidden_kantor_coa').length === 0) {
								            $('<input>').attr({
								                type: 'hidden',
								                id: 'hidden_kantor_coa',
								                name: 'kantor_coa',
								                value: sessionCompany
								            }).appendTo('#formAddCoa');
								        }
								    }
								});

								// RESET FORM ADD USER jika di tutup
							    $('#modalAddCoa').on('hidden.bs.modal', function () {
							        const $form = $('#formAddCoa');

							        // reset form native
							        $form[0].reset();

							        // reset company select2
							        const $companySelect = $('#add_kantor_coa');
							        if ($companySelect.hasClass('select2-hidden-accessible')) {
							            $companySelect.val('').trigger('change');
							        }
							        $('#hidden_kantor_coa').remove();

							        // unlock company select (untuk admin)
							        unlockAddCompany();

							        // hapus error / validation jika ada
							        $form.find('.is-invalid').removeClass('is-invalid');
							        $form.find('.invalid-feedback').remove();
							    });

							    $('#modalEditCoa').on('shown.bs.modal', function () {
							        initCompanySelect2('#edit_kantor_coa', $(this));
								    if (!isSuperAdmin) {
								         $('#edit_kantor_coa').prop('disabled', true);
								    }
							    });

							    // Auto Set Company Add Coa
							    function lockAddCompany() {
							        $('#add_kantor_coa')
							            .val(sessionCompany)
							            .trigger('change')
							            .on('select2:opening select2:selecting', e => e.preventDefault());
							    }

							    function unlockAddCompany() {
							        $('#add_kantor_coa')
							            .off('select2:opening select2:selecting');
							    }

							    // Auto Set Company Edit Coa
							    function lockEditCompany() {
							        $('#edit_kantor_coa')
							            .val(sessionCompany)
							            .trigger('change')
							            .on('select2:opening select2:selecting', e => e.preventDefault());

							        $('#companyHelpEdit').removeClass('d-none');
							    }

							    function unlockEditCompany() {
							        $('#edit_kantor_coa')
							            .off('select2:opening select2:selecting');

							        $('#companyHelpEdit').addClass('d-none');
							    }

							    $(document).on('click', '.btn-edit', function () {
							        const id = $(this).data('id');
							        $.post("<?= base_url('coa/get') ?>", {
							            id: id,
							            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
							        }, function (res) {

							            if (!res.status) {
							                Swal.fire('Error', res.message, 'error');
							                return;
							            }

							            const d = res.data;

							            $('#edit_id').val(d.id);
							            $('#edit_kantor_coa').val(d.company_id).trigger('change');
							            $('#edit_kode_coa').val(d.account_code);
							            $('#edit_nama_coa').val(d.account_name);
							            $('#edit_tipe_coa').val(d.account_type);
							            $('#edit_induk_coa').val(d.parent_id);
							            $('#edit_aruskas_coa').val(d.cashflow_type);
							            $('#edit_status_coa').val(d.is_active);
										$('#hidden_edit_kantor_coa').val(d.company_id);

							            $('#modalEditCoa').modal('show');
							        }, 'json');
							    });
							});

							// Submit form insert data
							$('#formAddCoa').on('submit', function (e) {
							    e.preventDefault();
							    let formData = new FormData(this);
							    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
							    Swal.fire({
							        title: 'Add new COA?',
							        icon: 'question',
							        showCancelButton: true,
							        reverseButtons: true,
							        confirmButtonText: 'Yes, save',
							        cancelButtonText: 'No'
							    }).then(result => {
							        if (result.isConfirmed) {
							            $.ajax({
							                url: "<?= base_url('coa/store') ?>",
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

							                        $('#modalAddCoa').modal('hide');
							                        $('.dtCoa').DataTable().ajax.reload(null, false);
							                    } else {
							                        Swal.fire('Failed', res.message, 'error');
							                    }
							                }
							            });
							        }
							    });
							});

							// Submit form edit data
							$('#formEditCoa').on('submit', function (e) {
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
									        url: "<?= base_url('coa/update') ?>",
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

									                $('#modalEditCoa').modal('hide');
									                $('.dtCoa').DataTable().ajax.reload(null, false);
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
							                url: "<?= base_url('coa/delete') ?>",
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

							                        $('.dtCoa').DataTable().ajax.reload(null, false);
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