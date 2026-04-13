						<?= $this->extend('layouts/main') ?>
						<?= $this->section('content') ?>

						<div class="container-xxl flex-grow-1 container-p-y">
						    <div class="card">
						        <div class="card-datatable table-responsive pt-0">
						            <table class="dtRoom table table-striped">
						                <thead>
						                    <tr>
						                        <th></th>
						                        <th>No.</th>
						                        <th>Branch</th>
						                        <th>Room No.</th>
						                        <th>Action</th>
						                    </tr>
						                </thead>
						            </table>
						        </div>

						        <!-- add modal form -->
						        <div class="modal fade" id="modalAddRoom" tabindex="-1" aria-hidden="true">
									<div class="modal-dialog modal-lg modal-dialog-centered">
									    <div class="modal-content">
										    <form id="formAddRoom" enctype="multipart/form-data">
										        <div class="modal-header">
										          	<h5 class="modal-title">Add Room</h5>
										          	<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
										        </div>
										        <div class="modal-body">
										          	<div class="row">
								                        <div class="col-md-12 mb-6">
								                            <label class="form-label">Room No</label>
								                        	<input type="text" class="form-control" name="roomno" required>
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
				                <div class="modal fade" id="modalEditRoom" tabindex="-1" aria-hidden="true">
									<div class="modal-dialog modal-lg modal-dialog-centered">
									    <div class="modal-content">
										    <form id="formEditRoom" enctype="multipart/form-data">
										        <div class="modal-header">
										          	<h5 class="modal-title">Edit Room</h5>
										          	<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
										        </div>

										        <div class="modal-body">
										          	<input type="hidden" name="id" id="edit_id">
										          	<div class="row">
								                        <div class="col-md-12 mb-6">
								                            <label class="form-label">Room No.</label>
								                        	<input type="text" class="form-control" name="roomno" id="edit_roomno" required>
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
							// DataTables Rooms
						    'use strict';
						    $(function () {
						        let dt_tableRoom = $('.dtRoom'), dt_skill;
						        if (dt_tableRoom.length) {
						        	dt_skill = dt_tableRoom.DataTable({
						        		processing: true,
					                	serverSide: true,
					                	responsive: true,
						                ajax: {
						                    url: "<?= base_url('maintenance/datatableroom') ?>",
						                    type: "POST",
						                    data: d => {
						                        d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
						                    }
						                },
						                columns: [
						                    { data: null },          // responsive control
						                    { data: 'no_urut' },
						                    { data: 'branch' },
						                    { data: 'room' },
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
					                    		// Posisi yang dibutuhkan
								          		targets: 2,
								          		responsivePriority: 1,
								        	},
						                    {
						                        // Actions
						                        targets: -1,
						                        title: 'Actions',
						                        orderable: false,
						                        searchable: false
						                    }
					                	],
					                	order: [[3, 'desc']],
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
						                                exportOptions: { columns: [1,2,3] }
						                            },
						                            {
						                                extend: 'csv',
						                                text: '<i class="ti ti-file-text me-1"></i>Csv',
						                                className: 'dropdown-item',
						                                exportOptions: { columns: [1,2,3] }
						                            },
						                            {
						                                extend: 'pdf',
						                                text: '<i class="ti ti-file-description me-1"></i>Pdf',
						                                className: 'dropdown-item',
						                                exportOptions: { columns: [1,2,3] }
						                            }
						                        ]
						                    },
						                    {
						                        text: '<i class="ti ti-plus me-sm-1"></i> <span class="d-sm-inline-block">Add New Room</span>',
						                        className: 'create-new btn btn-primary waves-effect waves-light',
						                        action: function () {
										            $('#modalAddRoom').modal('show');
										        }
						                    }
						                ],
						                responsive: {
						                    details: {
						                        display: $.fn.dataTable.Responsive.display.modal({
						                            header: function (row) {
						                                let data = row.data();
						                                return 'Details of ' + data.name;
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
									$('div.head-label').html('<h5 class="card-title mb-0">Rooms List</h5>');
						        }
						    });

							$('#modalAddRoom').on('hidden.bs.modal', function () {
							    // reset form
							    $('#formAddRoom')[0].reset();
							});

							// Submit form insert data
							$('#formAddRoom').on('submit', function (e) {
							    e.preventDefault();
							    let formData = new FormData(this);
							    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
							    Swal.fire({
							        title: 'Add New Room?',
							        icon: 'question',
							        showCancelButton: true,
							        reverseButtons: true,
							        confirmButtonText: 'Yes, save',
							        cancelButtonText: 'No'
							    }).then(result => {
							        if (result.isConfirmed) {
							            $.ajax({
							                url: "<?= base_url('maintenance/storeroom') ?>",
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

							                        $('#modalAddRoom').modal('hide');
							                        $('.dtRoom').DataTable().ajax.reload(null, false);
							                    } else {
							                        Swal.fire({
							                            icon: 'error',
							                            title: 'Failed',
							                            text: res.message
							                        });
							                    }
							                }
							            });
							        }
							    });
							});

							$('#modalEditRoom').on('hidden.bs.modal', function () {
							    // reset form
							    $('#formEditRoom')[0].reset();
							});

							// Submit form edit data
							$(document).on('click', '.btn-edit', function () {
						        const id = $(this).data('id');
						        $.post("<?= base_url('maintenance/getroom') ?>", {
						            id: id,
						            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
						        }, function (res) {

						            if (!res.status) {
						                Swal.fire('Error', res.message, 'error');
						                return;
						            }

						            const d = res.data;
									$('#modalEditRoom').modal('show');
									setTimeout(function () {
									    $('#edit_id').val(d.id);
									    $('#edit_roomno').val(d.room_no);
									}, 200);
						        }, 'json');
						    });

							$('#formEditRoom').on('submit', function (e) {
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
									        url: "<?= base_url('maintenance/updateroom') ?>",
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

									                $('#modalEditRoom').modal('hide');
									                $('.dtRoom').DataTable().ajax.reload(null, false);
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
							                url: "<?= base_url('maintenance/deleteroom') ?>",
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

							                        $('.dtRoom').DataTable().ajax.reload(null, false);
							                    } else {
							                        Swal.fire('Failed', res.message, 'error');
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