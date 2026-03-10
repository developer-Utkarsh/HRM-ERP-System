
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-6">
						<h2 class="content-header-title float-left mb-0">Freelancer</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-6 text-right">
						<a href="<?php echo e(route('admin.freelancer.create')); ?>" class="btn btn-primary">Add</a>
						
						<a href="<?php echo e(route('admin.freelancer.freelancer-invoice-history')); ?>" class="btn btn-primary">History</a>
						<button type="button" class="btn btn-outline-info waves-effect waves-light" data-toggle="modal" data-target="#GetInvoice">Create Invoice</button>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="<?php echo e(route('admin.freelancer.index')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Month</label>
											<fieldset class="form-group">
												<input type="month" class="form-control" name="month" placeholder="Month" value="<?php echo e(app('request')->input('month')); ?>">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Month</label>
											<fieldset class="form-group">
												<input type="month" class="form-control" name="month" placeholder="Month" value="<?php echo e(app('request')->input('month')); ?>">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">&nbsp;</label>
											<fieldset class="form-group" style="float:right;">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="<?php echo e(route('admin.freelancer.index')); ?>" class="btn btn-warning">Reset</a>
											</fieldset>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

				<div class="table-responsive">
					<table class="table data-list-view" id="">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Title</th>
								<th>Message</th>
								<th>Date</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								if(count($record) > 0){
								$i =1; 
									foreach($record as $r){ 
							?>
							<tr>
								<td><?php echo e($i); ?></td>
								<td><?php echo e($r->tittle); ?></td>
								<td><?php echo e($r->msg); ?></td>
								<td><?php echo e($r->created_at); ?></td>
								<td><?php echo e($r->status); ?></td>
								<td>
									<?php if($r->status=='Pending'){ ?>
									<button class="edit-btn" data-id="<?php echo e($r->id); ?>">
										Edit
									</button>
									
									<select class='form-select status-dropdown d-none' data-id="<?php echo e($r->id); ?>">
                                        <option value="" disabled selected>Select Status</option>
                                        <option value="Approved">Approved</option>
                                        <option value="Rejected">Rejected</option>
                                    </select>
									<?php }else{ echo '-'; } ?>
								</td>
							</tr>
							
							<?php $i++; } 
								}else{
							?>			
							<tr>
								<td class="text-center" colspan="6">No record found</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
					
				</div>                   
			</section>
		</div>
	</div>
</div>
 

 <!--GetInvoice---start-->
<div class="modal fade" id="GetInvoice" tabindex="-1" aria-labelledby="GetInvoiceLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header justify-content-between">
				<div>
					<h5 class="modal-title" id="GetInvoiceLabel">Get Invoice</h5>
				</div>
				<div>
					
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
			</div>
			<div class="modal-body">
				<form id="invoiceForm" method="post">
					<div class="row g-2">
						<!-- Personal Details -->
						<div class="col-lg-6 col-12 form-group">
							<label for="name" class="fw-medium">Name</label>
							<input type="text" placeholder="Enter Name..." name="name" id="name"
								class="form-control">
							<div class="error_msg text-danger fw-bold"></div>
						</div>
						<div class="col-lg-6 col-12 form-group">
							<label for="phone" class="fw-medium">Phone</label>
							<input type="number" placeholder="Enter Phone Number..." name="phone" id="phone"
								class="form-control">
							<div class="error_msg text-danger fw-bold"></div>
						</div>
						<div class="col-lg-6 col-12 form-group">
							<label for="location" class="fw-medium">Location</label>
							<input type="text" placeholder="Enter Location..." name="location" id="location"
								class="form-control">
							<div class="error_msg text-danger fw-bold"></div>
						</div>
						<div class="col-lg-6 col-12 form-group">
							<label for="month" class="fw-medium">Select Month of Invoice</label>
							<input type="date" id="dataPicker" onclick="this.showPicker()" name="month"
								class="form-control">
							<div class="error_msg text-danger fw-bold"></div>
						</div>


						<div class="col-lg-6 col-12 form-group">
							<label for="description" class="fw-medium">Description</label>
							<textarea placeholder="Enter Description..." name="description" id="description"
								class="form-control"></textarea>
							<div class="error_msg text-danger fw-bold"></div>
						</div>
						<div class="col-lg-6 col-12 form-group">
							<label for="amount" class="fw-medium">Amount</label>
							<input type="number" placeholder="Enter Amount..." name="amount" id="amount"
								class="form-control">
							<div class="error_msg text-danger fw-bold"></div>
						</div>

						<!-- Bank Details -->
						<div class="col-lg-6 col-12 form-group">
							<label for="pan" class="fw-medium">PAN No.</label>
							<input type="text" placeholder="Enter PAN No..." name="pan" id="pan"
								class="form-control">
							<div class="error_msg text-danger fw-bold"></div>
						</div>
						<div class="col-lg-6 col-12 form-group">
							<label for="accountName" class="fw-medium">Account Holder Name</label>
							<input type="text" placeholder="Enter Account Holder Name..." name="accountName"
								id="accountName" class="form-control">
							<div class="error_msg text-danger fw-bold"></div>
						</div>
						<div class="col-lg-6 col-12 form-group">
							<label for="accountNo" class="fw-medium">Account Number</label>
							<input type="number" placeholder="Enter Account Number..." name="accountNo"
								id="accountNo" class="form-control">
							<div class="error_msg text-danger fw-bold"></div>
						</div>
						<div class="col-lg-6 col-12 form-group">
							<label for="ifsc" class="fw-medium">IFSC Code</label>
							<input type="text" placeholder="Enter IFSC Code..." name="ifsc" id="ifsc"
								class="form-control">
							<div class="error_msg text-danger fw-bold"></div>
						</div>
					</div>
					<div class="submit-btn text-end">
						<button type="submit" id="submitInvoice" class="btn btn-dark px-2">Generate Invoice</button>
						<div class="process text-center fw-bold text-warning"></div>
						<div class="responsemsgModal text-success text-center fw-bold"></div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!--GetInvoice--end-->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script src="<?php echo e(asset('laravel/public/admin/js/jquery.validate.min.js')); ?>"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple1').select2({
			placeholder: "Select",
			allowClear: true
		});
		$('.select-multiple2').select2({
			placeholder: "Select",
			allowClear: true
		});
	});
	
	$(document).ready(function() {
		$('#example').DataTable();
	});			
	
	$(document).on('click', '.edit-btn', function () {
		const row = $(this).closest('tr');
		row.find('.current-status').toggleClass('d-none');
		row.find('.edit-btn').toggleClass('d-none');
		row.find('.status-dropdown').toggleClass('d-none');
	});
	
	
	$(document).on('change', '.status-dropdown', function () {
		const id = $(this).data('id');
		const newStatus = $(this).val();

		$.ajax({
			type : 'POST',
			url : '<?php echo e(route('admin.freelancer.fchange-status')); ?>',
			data : {'_token' : '<?php echo e(csrf_token()); ?>', 'id': id,'status':newStatus},			
			success: function (response) {
				alert("Status updated!!");
				location.reload(); 
			},
			error: function () {
				alert('An error occurred while updating the status');
			}
		});
	});
	
	
	
	$(document).ready(function () {
		$('#submitInvoice').click(function (e) {
			e.preventDefault();
			var name = $('#name').val();
			var phone = $('#phone').val();
			var location = $('#location').val();
			var month = $('#dataPicker').val();
			var description = $('#description').val();
			var amount = $('#amount').val();
			var pan = $('#pan').val();
			var accountName = $('#accountName').val();
			var accountNo = $('#accountNo').val();
			var ifsc = $('#ifsc').val();

			let isValid = true;

			function showError(input, message) {
				input.next('.error_msg').text(message);
				input.addClass('is-invalid');
				isValid = false;
				setTimeout(() => {
					input.next('.error_msg').text('');
					input.removeClass('is-invalid');
				}, 3000);
			}

			$('.error_msg').text('');
			$('.form-control').removeClass('is-invalid');

			if (name === '') {
				showError($('#name'), 'Name is required');
			}
			if (phone === '' || !/^\d{10}$/.test(phone)) {
				showError($('#phone'), 'Valid phone number is required');
			}
			if (location === '') {
				showError($('#location'), 'Location is required');
			}
			if (month === '') {
				showError($('#month'), 'Month is required');
			}
			if (description === '') {
				showError($('#description'), 'Description is required');
			}
			if (amount === '' || isNaN(amount) || amount <= 0) {
				showError($('#amount'), 'Valid amount is required');
			}
			if (pan === '') {
				showError($('#pan'), 'Valid PAN is required');
			}
			if (accountName === '') {
				showError($('#accountName'), 'Account holder name is required');
			}
			if (accountNo === '') {
				showError($('#accountNo'), 'Valid account number is required');
			}
			if (ifsc === '') {
				showError($('#ifsc'), 'Valid IFSC code is required');
			}

			if (isValid) {
				$.ajax({
					url : '<?php echo e(route('admin.freelancer.freelancer-invoice')); ?>',
					type: 'POST',
					data : {
						'_token' : '<?php echo e(csrf_token()); ?>',
						'name': name,
						'phone': phone,
						'location': location,
						'month': month,
						'description': description,
						'amount': amount,
						'pan': pan,
						'accountName': accountName,
						'accountNo': accountNo,
						'ifsc': ifsc
					},
					beforeSend: function () {
						$('.process').html('<img src="refresh.gif" alt="Processing..." style="width: 50px; height: 50px;">');
					 },
					success: function (response) {
						let data = JSON.parse(response);
						if (data.status === 'success') {
							$('.process').empty();
							$('.responsemsgModal').html("Downloading...");
							$('#GetInvoice').modal('hide');
							$('#invoiceForm')[0].reset();
							
							window.location.href = "<?php echo e(route('admin.freelancer.freelancer-invoice-download', '/insert_id')); ?>".replace('/insert_id', data.insert_id);

						} else { 
							$('.responsemsgModal').html(data.message);
						}
					},
					error: function (xhr, status, error) {
						console.error("AJAX error:", error);
						$('.responsemsgModal').html("An error occurred. Please try again.");
					}
				});
			} else {
				console.log("Form validation failed");
			}
		});
	});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/freelancer/index.blade.php ENDPATH**/ ?>