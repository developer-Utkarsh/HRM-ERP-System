
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Employees List</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="<?php echo e(route('admin.employees.index')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-md-3">
											<label for="users-list-role">Search</label>
											<fieldset class="form-group">
												<input type="text" class="form-control search" name="search" placeholder="Ex:Name, Email, Mobile, Employee Code" value="<?php echo e(app('request')->input('search')); ?>" id="myInputSearch">
											</fieldset>
										</div>
										<div class="col-md-3">
											<label for="users-list-role">Darwin Code</label>
											<fieldset class="form-group">
												<input type="text" class="form-control darwin_code" name="darwin_code" placeholder="Darwin Code" value="<?php echo e(app('request')->input('darwin_code')); ?>" id="myInputSearch">
											</fieldset>
										</div>
										<div class="col-md-3">
											<label for="users-list-status">Location</label>											
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 branch_location" name="branch_location" onchange="locationBranch(this.value);">
													<option value="">Select Any</option>													
													<!--option value="jodhpur">Jodhpur</option>
													<option value="jaipur">Jaipur</option-->
													<option value="jodhpur" <?php if(!empty(app('request')->input('branch_location')) && 'jodhpur' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>Jodhpur</option>
													<option value="jaipur" <?php if(!empty(app('request')->input('branch_location')) && 'jaipur' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>Jaipur</option>
													<option value="delhi" <?php if(!empty(app('request')->input('branch_location')) && 'delhi' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>Delhi</option>
													<option value="prayagraj" <?php if(!empty(app('request')->input('branch_location')) && 'prayagraj' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>Prayagraj</option>
													<option value="indore" <?php if(!empty(app('request')->input('branch_location')) && 'indore' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>Indore</option>
												</select>												
											</fieldset>
										</div>
										<div class="col-md-3">
											<label for="users-list-status">Branch</label>
											<?php 
											//$branches = \App\Branch::where('status', '1')->orderBy('id','desc')->get();
											$branch_location = app('request')->input('branch_location');
											$branches = \App\Branch::where('status', '1'); 
											if(!empty($branch_location)){
												$branches->where('branch_location', $branch_location);
											}
											$branches = $branches->orderBy('id','desc')->get();											
											?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 branch_id" name="branch_id[]" multiple>
													<option value="">Select Any</option>
													<?php if(count($branches) > 0): ?>
													<?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" <?php if(!empty(app('request')->input('branch_id')) && in_array($value->id, app('request')->input('branch_id'))): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php endif; ?>
												</select>												
											</fieldset>
										</div>
										
										<div class="col-md-3">
											<label for="users-list-status">Role</label>
											<?php $roles = \App\Role::where('status', '1')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2 role_id" name="role_id">
													<option value="">Select Any</option>
													<?php if(count($roles) > 0): ?>
													<?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" <?php if($value->id == app('request')->input('role_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php endif; ?>
												</select>												
											</fieldset>
										</div>
										
										<div class="col-md-3">
											<div class="form-group">
												<label for="first-name-column">Department Type</label>
												<?php if(count($allDepartmentTypes) > 0): ?>
												<select class="form-control get_role select-multiple1 department_type" name="department_type" id="se_department_type">
													<option value=""> - Select Any - </option>
													<?php $__currentLoopData = $allDepartmentTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value['id']); ?>" <?php if($value['id'] == app('request')->input('department_type')): ?> selected="selected" <?php endif; ?>><?php echo e($value['name']); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>
												<?php endif; ?>
											</div>
										</div>
										<?php if(Auth::user()->role_id == 29 || Auth::user()->role_id == 24): ?>
										<div class="col-md-3">
											<label for="users-list-status">Status</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple3 status" name="status">
													<?php $status = ['Inactive', 'Active']; ?>
													<option value="">Select Any</option>
													<?php $__currentLoopData = $status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value); ?>" <?php if($value == app('request')->input('status')): ?> selected="selected" <?php endif; ?>><?php echo e($value); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>												
											</fieldset>
										</div>
										<?php endif; ?>
										<div class="col-md-2">
											<label for="users-list-role">Month</label>
											<fieldset class="form-group">
												<input type="month" class="form-control year_wise_month" name="year_wise_month" value="<?php if(!empty(Request::get('year_wise_month'))): ?><?php echo e(Request::get('year_wise_month')); ?><?php endif; ?>">
											</fieldset>
										</div>
										
										<div class="col-md-7"></div>
										<div class="col-md-5">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="<?php echo e(route('admin.employees.index')); ?>" class="btn btn-warning">Reset</a>
											<a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a>
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
								<th>Department</th>
								<th>Sub Department</th>
								<th>Branch</th>
								<th>Employee</th>
								<th>Darwin Code</th>
								<th>Email</th>
								<th>Mobile</th>
								<th>Role</th>
								<?php if( Auth::user()->role_id ==29 || Auth::user()->role_id ==30 || Auth::user()->role_id ==24){?>
								<th>DOJ</th>
								<th>DOL</th>
								<?php } ?>
								<?php if(Auth::user()->role_id == 29 || Auth::user()->role_id == 24): ?>
								<th>Status</th>
								<?php endif; ?>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php if(count($employees) > 0): ?>
								<?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<?php
								//echo "<pre>";print_r($value->user_branches); die;
								$department_id = $value->department_type;
								$department = \App\Department::where('id', $department_id)->first();
								
								$sub_department_id = $value->sub_department_type;
								$sub_department = \App\SubDepartment::where('department_id', $department_id)->where('id', $sub_department_id)->first();
								?>
								<tr>
									<td><?php echo e($pageNumber++); ?></td>
									<td><?php echo e($value->name); ?> <span style="display:none;"> <?php echo e($value->register_id); ?> <?php echo e($value->email); ?> <?php echo e($value->email); ?></span></td>
									<td><?php echo isset($department->name)?$department->name:''; ?></td>
									<td><?php echo isset($sub_department->name)?$sub_department->name:''; ?></td>
									<!--td><?php echo e(isset($value->user_details->branch->name) ? $value->user_details->branch->name : ''); ?></td-->
									<td>
									<?php
									$branch_names = "";
									if(isset($value->user_branches) && !empty($value->user_branches)){
										foreach($value->user_branches as $key => $val) { 
											if(!empty($val->branch->name)) {
												$branch_names .= $val->branch->name .", ";
											}
										}
									}
									echo rtrim($branch_names, ", "); 
									?>
									</td>
									<td class="product-name">
										<a href="<?php echo e(route('admin.employees.view', $value->id)); ?>" class="btn btn-sm btn-primary"><?php echo e($value->register_id); ?>

										</a>
									</td>
									<td><?php echo e($value->darwin_code ?? '-'); ?></td>
									<td><?php echo e($value->email); ?></td>
									<td class="product-price"><?php echo e($value->mobile); ?></td>
									<td class="product-price"><?php echo e(isset($value->role->name)?$value->role->name:''); ?></td>
									<?php if( Auth::user()->role_id ==29 || Auth::user()->role_id ==30 || Auth::user()->role_id ==24){?>
									<td class="product-price"><?php echo e(!empty($value->user_details->joining_date) ? date('d-m-Y',strtotime($value->user_details->joining_date)) : ''); ?></td>
									<td class="product-price"><?php echo e(!empty($value->reason_date) ? date('d-m-Y',strtotime($value->reason_date)) : ''); ?></td>
									<?php } ?>
									<?php if(Auth::user()->role_id == 29 || Auth::user()->role_id == 24): ?>
									<td>
										
										<!--a href="<?php echo e(route('admin.employee.status', $value->id)); ?>" <?php if(Auth::user()->role_id == 21): ?>style="display: inline-block;pointer-events: none;" <?php endif; ?>>
											<strong class="sts-data fa fa-lg <?php echo e($value->status ? 'fa-toggle-on text-success' : 'fa-toggle-off text-grey'); ?>" title="Toggle publish" ></strong>
										</a-->
										
										<a href="<?php if($value->status == 1): ?><?php echo e('javascript:void(0)'); ?> <?php else: ?> <?php echo e(route('admin.employee.status', $value->id)); ?> <?php endif; ?>" <?php if($value->status == 1){ echo 'data-toggle=modal data-id='.$value->id.' data-status='.$value->status ; } ?> id="lnk" <?php if(Auth::user()->role_id == 21): ?>style="display: inline-block;pointer-events: none;" <?php endif; ?>>
											<strong class="<?php if($value->status == 1): ?><?php echo e('sts-data'); ?><?php endif; ?> fa fa-lg <?php echo e($value->status ? 'fa-toggle-on text-success' : 'fa-toggle-off text-grey'); ?>" title="Toggle publish" ></strong>
										</a>
									</td>
									<?php endif; ?>
									<td class="product-action">
									<?php 
										$check = $_SERVER['QUERY_STRING'];
										if(!empty($check)){
											$nCheck	=	"?".$check;
										}else{
											$nCheck	=	"";
										}
									?>
									<?php
									if($value->admin_approval=="Pending"){
									?>
										<a href="<?php echo e(route('admin.employees.approval', $value->id.$nCheck)); ?>">
											<span class="action-edit"><i class="feather icon-bell" title="Pending For Approval"></i></span>
										</a>
									<?php
									}
									if(Auth::user()->role_id == 29 || Auth::user()->role_id == 24){
									?>
										<a href="<?php echo e(route('admin.employees.is-comp-off', $value->id.$nCheck)); ?>" title="Is Com Off">
											<span class="action-edit"><i class="feather icon-scissors"></i></span>
										</a>
									<?php 
									}
									?>
										<a href="<?php echo e(route('admin.employees.view', $value->id.$nCheck)); ?>"  title="Employee Details">
											<span class="action-edit"><i class="feather icon-eye"></i></span>
										</a>
										<?php if(Auth::user()->role_id == 24 || Auth::user()->role_id == 29 || Auth::user()->role_id == 30): ?>
										<a href="<?php echo e(route('admin.employee.edit', $value->id.$nCheck)); ?>" title="Edit Employee">
											<span class="action-edit"><i class="feather icon-edit"></i></span>
										</a>
										<!--
										<a href="<?php echo e(route('admin.employee.delete', $value->id)); ?>" onclick="return confirm('Are You Sure To Delete Employee')" title="Delete Employee">
											<span class="action-delete"><i class="feather icon-trash"></i></span>
										</a>
										-->
										<?php endif; ?>
									</td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php else: ?>
								<tr>
									<td class="text-center" colspan="12">No Data Found</td>
								</tr>	
							<?php endif; ?>
						</tbody>
					</table>
					<div class="d-flex justify-content-center">					
					<?php echo $employees->appends($params)->links(); ?>

					</div>
				</div>                   
			</section>
		</div>
	</div>
</div>


<div id="edit-sts" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title">Reason</h5>
		</div>
		<form action="<?php echo e(route('admin.employee.status-by-reason')); ?>" method="post" id="submit_user_status_form" class="online-form">
		<?php echo csrf_field(); ?>
		<div class="modal-body">
			<div class="form-body">
				<div class="row pt-2">
					<div class="col-md-12 col-12">
						<div class="form-label-group">
							<textarea name="reason" placeholder="Reason" class="form-control remark" required></textarea>
						</div>
					</div>
					
					<div class="col-md-12 col-12">
						<div class="form-label-group">
							<input type="date" name="reason_date" class="form-control" required>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<input type="hidden" name="p_id" id="p_id" value="">
			<input type="hidden" name="sts" id="sts" value="">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
			<button type="submit" id="timetable_online_btn" class="btn btn-primary onlinedsabl">Submit <i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i></button>
		</div>
		</form>
		
		</div>
	</div>
</div>
				
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
	function locationBranch(value){
		$.ajax({
			type : 'POST',
			url : '<?php echo e(route('admin.employee.get-branch')); ?>',
			data : {'_token' : '<?php echo e(csrf_token()); ?>', 'branch_id': value},
			dataType : 'html',
			success : function (data){
				$('.branch_id').empty();
				$('.branch_id').append(data);
			}
		});
	}
	
	
	$(".sts-data").on("click", function() { 
		var primary_id = $(this).parent('#lnk').attr("data-id"); 
		var lnk_status =  $(this).parent('#lnk').attr("data-status"); 
		$('#p_id').val(primary_id);
		$('#sts').val(lnk_status);
		$('#edit-sts').modal({
				backdrop: 'static',
				keyboard: true, 
				show: true
		});
	}); 

						
	$(document).ready(function() {
		$('.select-multiple1').select2({
			placeholder: "Select Any",
			allowClear: true
		});
		$('.select-multiple2,.select-multiple3').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
	
	$(document).ready(function() {
		$('#example').DataTable();
	});

</script>

<script>
$("body").on("click", "#download_excel", function (e) {
	var data = {};
		data.branch_id = $('.branch_id').val(),
		data.search    = $('.search').val(),
		data.role_id   = $('.role_id').val(),
		data.status    = $('.status').val(), 
		data.department_type    = $('.department_type').val(), 
		data.year_wise_month    = $('.year_wise_month').val(), 
		data.branch_location    = $('.branch_location').val(), 
	window.location.href = "<?php echo URL::to('/admin/'); ?>/employee-report-excel?" + Object.keys(data).map(function (k) {
		return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
	}).join('&');
});

function myFunctionSearch() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInputSearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("TableSearch");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/employee/index.blade.php ENDPATH**/ ?>