
<?php $__env->startSection('content'); ?>
<style>
.select2-selection--single {height: 44px !important; padding: 8px;}
</style>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-md-6">
						<h2 class="content-header-title float-left mb-0">Category</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-md-3"> <a href="javascript:void(0)" data-id="" class="btn btn-outline-primary get_edit_data float-right">Add Category</a></div>
					<div class="col-md-3">  <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#mySubModal">Add Sub Category</button></div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="<?php echo e(route('admin.category.index')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<!--
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Name</label>
											<fieldset class="form-group">
												<input type="text" class="form-control" name="name" placeholder="Name" value="<?php echo e(app('request')->input('name')); ?>">
											</fieldset>
										</div>
										-->
										
										<div class="col-md-4 col-12">
											<div class="form-group mb-0">
												 <select class="form-control select-multiple10 product_id" name="name">
													<option value="">Select Category</option>
													<?php if(count($category) > 0): ?>
													<?php $__currentLoopData = $category; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($val->id); ?>" <?php if($val->id == app('request')->input('name')): ?> selected="selected" <?php endif; ?>><?php echo e($val->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php endif; ?>
												</select>
												<?php if($errors->has('name')): ?>
												<span class="text-danger"><?php echo e($errors->first('name')); ?> </span>
												<?php endif; ?>
											</div>
										</div>
										
										<div class="col-12 col-sm-6 col-lg-3">
											<fieldset class="form-group mb-0">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="<?php echo e(route('admin.category.index')); ?>" class="btn btn-warning">Reset</a>
											</fieldset>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table class="table data-list-view" id="TableSearch">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Name</th>
								<th>Created</th>
								<th>Sub Category</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						<?php if(count($category) > 0): ?>
							<?php $__currentLoopData = $category; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr>
								<td><?php echo e($key + 1); ?></td>
								<td class="product-category"><?php echo e($value->name); ?></td>
								
								<td><?php echo e($value->created_at->format('d-m-Y')); ?></td>
								<td><a title="Update Sub Category" href="<?php echo e(url('admin/subcategory', $value->id)); ?>" class="btn btn-sm btn-primary waves-effect waves-light">Sub Category</a></td>
								<td class="product-action">
									<a title="Update Category" href="javascript:void(0)" data-id="<?php echo e($value->id); ?>" class="get_edit_data">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
									</a>
									
									<!--
									<a title="Delete Category" href="<?php echo e(route('admin.category.delete', $value->id)); ?>" onclick="return confirm('Are You Sure To Delete Category')">
										<span class="action-delete"><i class="feather icon-trash"></i></span>
									</a>
									-->
									
								</td>
							</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							
						<?php else: ?>
						<tr ><td class="text-center text-primary" colspan="5">No Record Found</td></tr>
						<?php endif; ?>	
						</tbody>
					</table>
				</div>                   
			</section>
			

		</div>
	</div>
</div>


<div class="modal" id="mySubModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Add Sub Category</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <form method="post" action="<?php echo e(route('admin.sub-category-store')); ?>">
      		<?php echo e(csrf_field()); ?>

	      <!-- Modal body -->
	      <div class="modal-body">
			<label>Category:</label>
	        <select class="form-control select-multiple1" name="parent" required>
	        	<option value="">Select Category</option>
				<?php if(count($category) > 0): ?>
				<?php $__currentLoopData = $category; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoryvalue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<option value="<?php echo e($categoryvalue->id); ?>"><?php echo e($categoryvalue->name); ?></option>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				<?php endif; ?>
	        </select>
			<br>
	      	<label>Sub Category:</label>
	        <input type="text" name="name" class="form-control" required>
			
	      </div>

	      <!-- Modal footer -->
	      <div class="modal-footer">
	      	<button type="submit" class="btn btn-primary">Save</button>
	        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
	      </div>
      </form>

    </div>
  </div>
</div>


      
<!-- The Modal -->
<div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Add Category</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <form method="post" action="<?php echo e(route('admin.category.store')); ?>">
      		<?php echo e(csrf_field()); ?>

	      <!-- Modal body -->
	      <div class="modal-body fill-name">
	      	<label>Category:</label>
	        <input type="text" name="name" class="form-control" required><br>
	      </div>

	      <!-- Modal footer -->
	      <div class="modal-footer">
	      	<button type="submit" class="btn btn-primary">Save</button>
	        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
	      </div>
      </form>

    </div>
  </div>
</div>


<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>

<script src="<?php echo e(asset('laravel/public/admin/js/jquery.validate.min.js')); ?>"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple1').select2({
			width: "100%",
			placeholder: "Select Category",
			allowClear: true
		});
	});
	
	$(document).ready(function() {
		$('.select-multiple10').select2({
			width: "100%",
			placeholder: "Select Category",
			allowClear: true
		});
	});
</script>
<script type="text/javascript">
	$(".get_edit_data").on("click", function() { 
		var cat_id = $(this).attr("data-id");
		if(cat_id){
			
			$('#myModal').modal({
					backdrop: 'static',
					keyboard: true, 
					show: true
			});
			
			$.ajax({
				type : 'POST',
				url : '<?php echo e(route('admin.edit-category')); ?>',
				data : {'_token' : '<?php echo e(csrf_token()); ?>', 'cat_id': cat_id},
				dataType : 'html',
				success : function (data){
					$('.fill-name').empty();
					
					$('.fill-name').html(data);
				}
			});
			
		}
		else{
			$('.fill-name').empty();
			$('#myModal').modal({
					backdrop: 'static',
					keyboard: true, 
					show: true
			});
			$('.fill-name').html('<label>Category:</label><input type="text" name="name" class="form-control" required><br>');
		}		
	}); 
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/category/index.blade.php ENDPATH**/ ?>