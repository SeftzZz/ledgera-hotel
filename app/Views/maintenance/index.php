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
										          	<h5 class="modal-title">Add Maintenance2</h5>
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

								<!-- edit modal form -->
				                <div class="modal fade" id="modalEditMaintenance" tabindex="-1" aria-hidden="true">
									<div class="modal-dialog modal-lg modal-dialog-centered">
									    <div class="modal-content">
										    <form id="formEditMaintenance" enctype="multipart/form-data">
										        <div class="modal-header">
										          	<h5 class="modal-title">Edit Maintenance</h5>
										          	<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
										        </div>

										        <div class="modal-body">
										          	<input type="hidden" name="id" id="edit_id">
										          	<div class="row">
								                        <div class="col-md-4 mb-3">
								                            <label class="form-label" for="edit_room">Room No.</label>
								                        	<select
															    name="room"
															    id="edit_room"
															    class="form-select select2"
															    data-placeholder="Select Rooms"
															    style="width:100%" disabled>
															    <option value=""></option>
															    <?php foreach ($rooms as $room): ?>
															        <option value="<?= $room['id'] ?>">
															            <?= esc($room['room_no']) ?>
															        </option>
															    <?php endforeach; ?>
															</select>
								                        </div>
								                        <div class="col-md-4 mb-3">
								                            <label class="form-label">Location</label>
								                        	<input type="text" class="form-control" name="location" id="edit_location" disabled>
								                        </div>
								                        <div class="col-md-4 mb-3">
								                            <label class="form-label">Issue</label>
										            		<input type="text" class="form-control" name="issue" id="edit_issue" disabled>
								                        </div>
								                    </div>
								                    <div class="row">
								                        <div class="col-md-4 mb-3">
								                            <label class="form-label">Started</label>
								                        	<input type="date" name="start" id="edit_start" class="form-control" value="<?= date('Y-m-d') ?>" id="html5-date-input" disabled />
								                        </div>
								                        <div class="col-md-4 mb-3">
								                            <label class="form-label">Date</label>
								                        	<input type="date" name="complete" class="form-control" value="<?= date('Y-m-d') ?>" id="html5-date-input" required />
								                        </div>
								                        <div class="col-md-4 mb-3">
								                            <label class="form-label">Status</label>
								                            <select name="status" id="edit_status" class="form-select" required>
							                                    <option value="open">Open</option>
							                                    <option value="in_progress">In Progress</option>
							                                    <option value="done">Done</option>
							                                    <option value="cancelled">Cancel</option>
							                                </select>
								                        </div>
								                    </div>
								                    <div class="row">
								                        <div class="col-md-12 mb-6">
								                            <label class="form-label">Note</label>
										            		<textarea class="form-control" name="note" id="edit_note" rows="3"></textarea>
								                        </div>
								                    </div>
								                    <div class="row">
								                        <div class="col-md-12 mb-6">
								                            <hr>
															<h6 class="mb-3">Sparepart Replacement</h6>

															<div id="itemsWrapper"></div>

															<button type="button" class="btn btn-sm btn-primary mb-3" id="btnAddItem">
															    <i class="ti ti-plus"></i> Add Item
															</button>
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

								<!-- detail modal -->
								<div class="modal fade" id="modalDetailMaintenance" tabindex="-1" aria-hidden="true">
								    <div class="modal-dialog modal-lg modal-dialog-centered">
								        <div class="modal-content">
								            <div class="modal-header">
								                <h5 class="modal-title">Maintenance Detail</h5>
								                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
								            </div>

								            <div class="modal-body">
								                <table class="table table-bordered">
								                    <tr><th width="30%">Room</th><td id="detail_room"></td></tr>
								                    <tr><th>Location</th><td id="detail_location"></td></tr>
								                    <tr><th>Issue</th><td id="detail_issue"></td></tr>
								                    <tr><th>Status</th><td id="detail_status"></td></tr>
								                    <tr><th>Started</th><td id="detail_started"></td></tr>
								                    <tr><th>Completed</th><td id="detail_completed"></td></tr>
								                    <tr><th>Note</th><td id="detail_note"></td></tr>
								                </table>

								                <hr>

								                <h6>Sparepart Used</h6>
								                <table class="table table-sm table-striped">
								                    <thead>
								                        <tr>
								                            <th>Item</th>
								                            <th width="100">Qty</th>
								                        </tr>
								                    </thead>
								                    <tbody id="detail_items"></tbody>
								                </table>

								                <hr>

								                <h6>Logs</h6>
								                <table class="table table-sm table-bordered">
								                    <thead>
								                        <tr>
								                            <th>Date</th>
								                            <th>Status</th>
								                            <th>Note</th>
								                        </tr>
								                    </thead>
								                    <tbody id="detail_logs"></tbody>
								                </table>
								            </div>
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
							let inventoriOptions = '';
							let itemIndex = 0;
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
							$('#modalEditMaintenance').on('shown.bs.modal', function () {
							    $('#edit_room').select2({
							        dropdownParent: $('#modalEditMaintenance'),
							        width: '100%',
							        placeholder: "Select Rooms",
							        allowClear: true
							    });
							});

							$('#modalEditMaintenance').on('hidden.bs.modal', function () {
							    // reset form
							    $('#formEditMaintenance')[0].reset();

							    // reset select2
							    $('#edit_room').val(null).trigger('change');

							    // reset item
							    $('#itemsWrapper').html('');
    							itemIndex = 0;
							});

							$(document).on('click', '.btn-edit', function () {
							    const id = $(this).data('id');

							    // ambil inventori
							    $.get("<?= base_url('maintenance/get-inventori') ?>", function(res){

							        inventoriOptions = res.data.map(i => 
							            `<option value="${i.id}">${i.sparepart} (Stock: ${i.qty})</option>`
							        ).join('');

							    });

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
							        $('#edit_location').val(d.location);
							        $('#edit_issue').val(d.issue);
							        $('#edit_start').val(d.started_at);
							        $('#edit_note').val(d.description);
							        $('#edit_status').val(d.status);

							        $('#modalEditMaintenance').modal('show');
							    }, 'json');
							});

							// ADD ITEM
							$('#btnAddItem').on('click', function () {
							    $('#itemsWrapper').append(itemRow(itemIndex++, inventoriOptions));

							    $('.select2-item').select2({
							        dropdownParent: $('#modalEditMaintenance'),
							        width: '100%'
							    });
							});

							// REMOVE ITEM
							$(document).on('click', '.btn-remove-item', function () {
							    $(this).closest('.item-row').remove();
							});

							// SELECT INVENTORI
							function itemRow(index, inventoriOptions) {
							    return `
							        <div class="row mb-2 item-row">
							            <div class="col-md-7">
							                <select name="items[${index}][id]" class="form-select select2-item" required>
							                    <option value="">Select Sparepart</option>
							                    ${inventoriOptions}
							                </select>
							            </div>
							            <div class="col-md-3">
							                <input type="number" name="items[${index}][qty]" class="form-control" placeholder="Qty" min="1" required>
							            </div>
							            <div class="col-md-2">
							                <button type="button" class="btn btn-danger btn-remove-item">
							                    <i class="ti ti-trash"></i>
							                </button>
							            </div>
							        </div>
							    `;
							}

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

							// ===============================
							// FORMAT TGL INDO
							// ===============================
							function formatDateIndo(dateStr) {
							    if (!dateStr) return '-';

							    const date = new Date(dateStr);
							    if (isNaN(date)) return '-';

							    const months = [
							        'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
							        'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
							    ];

							    const day   = String(date.getDate()).padStart(2, '0');
							    const month = months[date.getMonth()];
							    const year  = date.getFullYear();

							    return `${day} ${month} ${year}`;
							}

							function formatDateTimeIndo(dateStr) {
							    if (!dateStr) return '-';

							    const date = new Date(dateStr);
							    if (isNaN(date)) return '-';

							    return new Intl.DateTimeFormat('id-ID', {
							        day: '2-digit',
							        month: 'short',
							        year: 'numeric',
							        hour: '2-digit',
							        minute: '2-digit',
							        hour12: false
							    }).format(date).replace(',', ' -');
							}

							function capitalizeFirst(str) {
							    if (!str) return '';
							    return str.charAt(0).toUpperCase() + str.slice(1);
							}

							// ===============================
							// DETAIL
							// ===============================
							$(document).on('click', '.btn-detail', function () {
							    const id = $(this).data('id');

							    $.ajax({
							        url: "<?= base_url('maintenance/get-detail') ?>",
							        type: "POST",
							        data: {
							            id: id,
							            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
							        },
							        dataType: 'json',
							        success(res) {
							            if (!res.status) {
							                Swal.fire('Error', res.message, 'error');
							                return;
							            }

							            const d = res.data;

							            // ================= MAIN DATA =================
							            $('#detail_room').text(d.room ?? '-');
							            $('#detail_location').text(d.location ?? '-');
							            $('#detail_issue').text(d.issue);
							            $('#detail_status').text(capitalizeFirst(d.status));
							            $('#detail_started').text(formatDateIndo(d.started_at));
										$('#detail_completed').text(formatDateIndo(d.completed_at));
							            $('#detail_note').text(d.description ?? '-');

							            // ================= ITEMS =================
							            let itemsHtml = '';
							            if (d.items && d.items.length > 0) {
							                d.items.forEach(i => {
							                    itemsHtml += `
							                        <tr>
							                            <td>${i.item_name}</td>
							                            <td>${i.qty}</td>
							                        </tr>
							                    `;
							                });
							            } else {
							                itemsHtml = `<tr><td colspan="2" class="text-center">No items</td></tr>`;
							            }
							            $('#detail_items').html(itemsHtml);

							            // ================= LOGS =================
							            let logsHtml = '';
							            if (d.logs && d.logs.length > 0) {
							                d.logs.forEach(l => {
							                    logsHtml += `
							                        <tr>
							                            <td>${formatDateTimeIndo(l.created_at)}</td>
							                            <td>${capitalizeFirst(l.status)}</td>
							                            <td>${l.notes ?? '-'}</td>
							                        </tr>
							                    `;
							                });
							            } else {
							                logsHtml = `<tr><td colspan="3" class="text-center">No logs</td></tr>`;
							            }
							            $('#detail_logs').html(logsHtml);

							            $('#modalDetailMaintenance').modal('show');
							        },
							        error() {
							            Swal.fire('Error', 'Server error', 'error');
							        }
							    });
							});
						</script>
						<?= $this->endSection() ?>