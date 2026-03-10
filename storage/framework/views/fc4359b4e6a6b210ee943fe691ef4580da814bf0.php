
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-6">
						<h2 class="content-header-title float-left mb-0">Pincode List</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-6 text-right">						 
						<button type="button" class="btn btn-success importProduct">Import</button>
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
								<form action="<?php echo e(route('admin.pincode.index')); ?>" method="get" name="filtersubmit">
									<div class="row">
										
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Pincode</label>
											<fieldset class="form-group">
												<input type="text" class="form-control" name="pincode" placeholder="Enter Pincode" value="<?php echo e(app('request')->input('pincode')); ?>">
											</fieldset>
										</div>


										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">&nbsp;</label>
											<fieldset class="form-group" style="float:right;">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="<?php echo e(route('admin.pincode.index')); ?>" class="btn btn-warning">Reset</a>
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
								<th>Pincode</th>
								<th>City</th>
								<th>Courier</th>
								<th>Zone</th>
							</tr>
						</thead>
						<tbody>
							<?php if(count($pincode) > 0): ?>
								<?php $__currentLoopData = $pincode; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td><?php echo e($pageNumber++); ?></td>
									<td><?php echo e($value->pincode); ?></td>
									<td><?php echo e($value->city); ?></td>
									<td><?php echo e($value->courier); ?></td>
									<td><?php echo e($value->zone); ?></td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php else: ?>
							<tr ><td class="text-center text-primary" colspan="7">No Record Found</td></tr>
							<?php endif; ?>	
								
						</tbody>
					</table>
			
					<div class="d-flex justify-content-center">					
					<?php echo $pincode->appends($params)->links(); ?>

					</div>
				</div>    	               
			</section>
		</div>
	</div>
</div>



<div class="modal fade" id="importModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<form method="post" action="" id="submit_import_file">
			<?php echo csrf_field(); ?>
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Import Pincode</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div>
						<input type="file" name="product_file" value="" class="form-control"/>						
						<a href="<?php echo e(asset('laravel/public/sample-pincode.xlsx')); ?>" title="Click Here" download>Sample File</a>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success">Submit</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</div>
		</form>
	</div>
</div>

 
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script src="<?php echo e(asset('laravel/public/admin/js/jquery.validate.min.js')); ?>"></script>
<script>
$(".importProduct").on("click", function() {  	
	$('#importModel').modal({
			backdrop: 'static',
			keyboard: true, 
			show: true
	});
					
}); 
</script>

<script>
	var $form = $('#submit_import_file');
	validatorprice = $form.validate({
		ignore: [],
		rules: {
			'import_file' : {
				required: true,                
			},       
		},

		/* errorElement : "span",*/
		errorClass : 'border-danger',
		errorPlacement: function(error, element) {
			if (element.is(':input') || element.is(':select')) {
				$(this).addClass('border-danger');
			}
			else {
				return true;
			}
		}
	});
	
	
	$("#submit_import_file").submit(function(e) {
		var form = document.getElementById('submit_import_file');
		var dataForm = new FormData(form); 
		e.preventDefault();
		if(validatorprice.valid()){
			$('#import_btn').attr('disabled', 'disabled');
			$.ajax({
				beforeSend: function(){
					$("#import_btn i").show();
				},
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},      
				type: "POST",
				url : '<?php echo e(route('admin.import-pincode')); ?>',
				data : dataForm,
				processData : false, 
				contentType : false,
				dataType : 'json',
				success : function(data){
					console.log(data);
					
					if(data.status == false){
						swal("Error!", data.message, "error");
					} else if(data.status == true){
						swal("Done!", data.message, "success").then(function(){ 
							location.reload();
						});						
					}
				}
			});
		}       
	});
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/pincode/index.blade.php ENDPATH**/ ?>