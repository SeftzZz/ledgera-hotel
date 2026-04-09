						<?= $this->extend('layouts/main') ?>
						<?= $this->section('content') ?>

						<div class="container-xxl flex-grow-1 container-p-y">
						    <div class="card">
						        <div class="card-datatable table-responsive pt-0">
						            <table class="dtMaintenance table table-striped">
						                <thead>
						                    <tr>
						                        <th></th>
						                        <th>No.</th>
						                        <th>Room</th>
						                        <th>Location</th>
						                        <th>Issue</th>
						                        <th>Status</th>
						                        <th>Started</th>
						                        <th>Completed</th>
						                        <th>Action</th>
						                    </tr>
						                </thead>
						            </table>
						        </div>

						        <!-- add modal form -->
						        <div class="modal fade" id="modalAddMaintenance" tabindex="-1" aria-hidden="true">
									<div class="modal-dialog modal-lg modal-dialog-centered">
									    <div class="modal-content">
										    <form id="formAddMaintenance" enctype="multipart/form-data">
										        <div class="modal-header">
										          	<h5 class="modal-title">Add Maintenance</h5>
										          	<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
										        </div>
										        <div class="modal-body">
										          	<div class="row">
								                        <div class="col-md-6 mb-3">
								                            <label class="form-label">Room No.</label>
										            		<select
															    name="room"
															    id="add_room"
															    class="form-select select2"
															    data-placeholder="Select Rooms"
															    style="width:100%">
															    <option value=""></option>
															    <?php foreach ($rooms as $room): ?>
															        <option value="<?= $room['id'] ?>">
															            <?= esc($room['room_no']) ?>
															        </option>
															    <?php endforeach; ?>
															</select>
								                        </div>
								                        <div class="col-md-6 mb-3">
								                            <label class="form-label">Location</label>
								                        	<input type="text" class="form-control" name="location">
								                        </div>
								                    </div>
								                    <div class="row">
								                        <div class="col-md-6 mb-3">
								                            <label class="form-label">Issue</label>
										            		<input type="text" class="form-control" name="issue" required>
								                        </div>
								                        <div class="col-md-6 mb-3">
								                            <label class="form-label">Started</label>
								                        	<input type="date" name="start" class="form-control" value="<?= date('Y-m-d') ?>" id="html5-date-input" required />
								                        </div>
								                    </div>
								                    <div class="row">
								                        <div class="col-md-12 mb-6">
								                            <label class="form-label">Note</label>
										            		<textarea class="form-control" name="note" rows="3"></textarea>
								                        </div>
								                    </div>
										        </div>
										        <div class="modal-footer">
										        	<input type="hidden" name="hotelid" value="<?= session()->get('hotel_id') ?>">
										        	<button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
										          	<button type="submit" class="btn btn-primary">Save</button>
										        </div>
										    </form>
									    </div>
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
							'use strict';
							$(function () {
							    let dt_tableMaintenance = $('.dtMaintenance'), dt_maintenance;
							    if (dt_tableMaintenance.length) {
							        dt_maintenance = dt_tableMaintenance.DataTable({
							            processing: true,
							            serverSide: true,
							            responsive: true,
							            ajax: {
							                url: "<?= base_url('maintenance/datatable') ?>",
							                type: "POST",
							                data: d => {
							                    d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
							                }
							            },
							            columns: [
							                { data: null },
							                { data: 'no_urut' },
							                { data: 'room' },
							                { data: 'location' },
							                { data: 'issue' },
							                { data: 'status' },
							                { data: 'started_at' },
							                { data: 'completed_at' },
							                { data: 'action' }
							            ],
							            columnDefs: [
							                {
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
							                    className: 'btn btn-label-primary dropdown-toggle me-2',
							                    text: '<i class="ti ti-file-export me-sm-1"></i> Export',
							                    buttons: [
							                        {
							                            extend: 'print',
							                            text: 'Print',
							                            className: 'dropdown-item'
							                        },
							                        {
							                            extend: 'csv',
							                            text: 'CSV',
							                            className: 'dropdown-item'
							                        },
							                        {
							                            extend: 'pdf',
							                            text: 'PDF',
							                            className: 'dropdown-item'
							                        }
							                    ]
							                },
							                {
							                    text: '<i class="ti ti-plus"></i> Add Maintenance',
							                    className: 'btn btn-primary',
							                    action: function () {
							                        $('#modalAddMaintenance').modal('show');
							                    }
							                }
							            ],
							            responsive: {
							                details: {
							                    display: $.fn.dataTable.Responsive.display.modal({
							                        header: function (row) {
							                            let data = row.data();
							                            return 'Maintenance Detail - Room ' + data.room;
							                        }
							                    }),
							                    type: 'column',
							                    renderer: function (api, rowIdx, columns) {
							                        let data = $.map(columns, function (col) {
							                            return col.title !== ''
							                                ? `<tr>
							                                        <td>${col.title}:</td>
							                                        <td>${col.data}</td>
							                                   </tr>`
							                                : '';
							                        }).join('');

							                        return data
							                            ? $('<table class="table"><tbody /></table>').append(data)
							                            : false;
							                    }
							                }
							            }
							        });
							        $('div.head-label').html('<h5 class="card-title mb-0">Maintenance List</h5>');
							    }
							});

							// ===============================
							// ADD MAINTENANCE
							// ===============================
							$('#modalAddMaintenance').on('shown.bs.modal', function () {
							    // INIT SELECT2
							    $('#add_room').select2({
							        dropdownParent: $('#modalAddMaintenance'),
							        width: '100%',
							        placeholder: "Select Jobs",
							        allowClear: true
							    });
							});

							$('#modalAddMaintenance').on('hidden.bs.modal', function () {
							    // reset form
							    $('#formAddMaintenance')[0].reset();

							    // reset select2
							    $('#add_room').val(null).trigger('change');
							});

							$('#formAddMaintenance').on('submit', function (e) {
							    e.preventDefault();

							    let formData = new FormData(this);
							    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

							    Swal.fire({
							        title: 'Add Maintenance?',
							        icon: 'question',
							        showCancelButton: true,
							        confirmButtonText: 'Yes, save'
							    }).then(result => {
							        if (result.isConfirmed) {
							            $.ajax({
							                url: "<?= base_url('maintenance/store') ?>",
							                type: "POST",
							                data: formData,
							                processData: false,
							                contentType: false,
							                dataType: 'json',
							                success(res) {
							                    if (res.status) {
							                        Swal.fire('Success', res.message, 'success');
							                        $('#modalAddMaintenance').modal('hide');
							                        $('.dtMaintenance').DataTable().ajax.reload(null, false);
							                    } else {
							                        Swal.fire('Failed', res.message, 'error');
							                    }
							                }
							            });
							        }
							    });
							});


							// ===============================
							// EDIT
							// ===============================
							$(document).on('click', '.btn-edit', function () {
							    const id = $(this).data('id');

							    $.post("<?= base_url('maintenance/get') ?>", {
							        id: id,
							        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
							    }, function (res) {

							        if (!res.status) {
							            Swal.fire('Error', res.message, 'error');
							            return;
							        }

							        const d = res.data;

							        $('#edit_id').val(d.id);
							        $('#edit_room').val(d.room_id);
							        $('#edit_issue').val(d.issue);
							        $('#edit_priority').val(d.priority);
							        $('#edit_status').val(d.status);

							        $('#modalEditMaintenance').modal('show');

							    }, 'json');
							});


							// ===============================
							// UPDATE
							// ===============================
							$('#formEditMaintenance').on('submit', function (e) {
							    e.preventDefault();

							    let formData = new FormData(this);
							    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

							    Swal.fire({
							        title: 'Update data?',
							        icon: 'question',
							        showCancelButton: true,
							        confirmButtonText: 'Yes'
							    }).then(result => {
							        if (result.isConfirmed) {
							            $.ajax({
							                url: "<?= base_url('maintenance/update') ?>",
							                type: "POST",
							                data: formData,
							                processData: false,
							                contentType: false,
							                dataType: 'json',
							                success(res) {
							                    if (res.status) {
							                        Swal.fire('Success', res.message, 'success');
							                        $('#modalEditMaintenance').modal('hide');
							                        $('.dtMaintenance').DataTable().ajax.reload(null, false);
							                    } else {
							                        Swal.fire('Failed', res.message, 'error');
							                    }
							                }
							            });
							        }
							    });
							});


							// ===============================
							// DELETE
							// ===============================
							$(document).on('click', '.btn-delete', function () {
							    const id = $(this).data('id');

							    Swal.fire({
							        title: 'Delete?',
							        icon: 'warning',
							        showCancelButton: true,
							        confirmButtonText: 'Yes'
							    }).then(result => {
							        if (result.isConfirmed) {
							            $.ajax({
							                url: "<?= base_url('maintenance/delete') ?>",
							                type: "POST",
							                dataType: "json",
							                data: {
							                    id: id,
							                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
							                },
							                success(res) {
							                    if (res.status) {
							                        Swal.fire('Deleted', res.message, 'success');
							                        $('.dtMaintenance').DataTable().ajax.reload(null, false);
							                    } else {
							                        Swal.fire('Failed', res.message, 'error');
							                    }
							                }
							            });
							        }
							    });
							});
							</script>
						<?= $this->endSection() ?>