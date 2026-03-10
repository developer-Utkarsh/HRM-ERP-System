
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Leave</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">Leave View</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php 
		$display = "";
		if( Auth::user()->role_id ==20 || Auth::user()->role_id ==27){
			$display = "none";
		}
		?>
		<div class="content-body">
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="<?php echo e(route('admin.leave.index')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-2" style="display:<?=$display?>">
											<label for="users-list-role">Employee Name</label>
											<fieldset class="form-group">
												<input type="text" class="form-control name" name="name" placeholder="Name, EMP Code" value="<?php echo e(app('request')->input('name')); ?>">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2" style="display:<?=$display?>">
											<label for="users-list-status">Branch</label>
											<?php $branches = \App\Branch::where('status', '1')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 branch_id" name="branch_id">
													<option value="">Select Any</option>
													<?php if(count($branches) > 0): ?>
													<?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" <?php if($value->id == app('request')->input('branch_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php endif; ?>
												</select>												
											</fieldset>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label for="first-name-column">Department Type</label>
												<?php if(count($allDepartmentTypes) > 0): ?>
												<select class="form-control get_role select-multiple1 department_type" name="department_type">
													<option value=""> - Select Any - </option>
													<?php $__currentLoopData = $allDepartmentTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value['id']); ?>" <?php if($value['id'] == app('request')->input('department_type')): ?> selected="selected" <?php endif; ?>><?php echo e($value['name']); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>
												<?php endif; ?>
											</div>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Status</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 status" name="status">
													<option value="">Select Any</option>
													<option value="Pending" <?php if('Pending' == app('request')->input('status')): ?> selected="selected" <?php endif; ?>>Pending</option>
													<option value="Approved" <?php if('Approved' == app('request')->input('status')): ?> selected="selected" <?php endif; ?>>Approved</option>
													<option value="Rejected" <?php if('Rejected' == app('request')->input('status')): ?> selected="selected" <?php endif; ?>>Rejected</option>
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-verified">From</label>
											<fieldset class="form-group">
												<input type="date" name="fdate" class="form-control StartDateClass fdate" value="<?php echo e(app('request')->input('fdate')); ?>" id="">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-verified">To</label>
											<fieldset class="form-group">
												<input type="date" name="tdate" class="form-control EndDateClass tdate" value="<?php echo e(app('request')->input('tdate')); ?>" id="">
											</fieldset>
										</div>
									</div>
									<fieldset class="form-group" style="float:right;">		
									<button type="submit" class="btn btn-primary">Search</button>
									<a href="<?php echo e(route('admin.leave.index')); ?>" class="btn btn-warning">Reset</a>
									<a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a>
									</fieldset>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table class="table data-list-view" style="background:#fff;">
						<thead style="text-align: ;">
							<tr>
								<th>S. No.</th>
								<th>Employee Code</th>
								<th>Employee Name</th>
								<th>Branch Name</th>
								<th>Department</th>
								<th>Designation</th>
								<th>Apply Date</th>
								<th>Date</th>
								<th>Category</th>
								<th>Type</th>
								<th>Reason</th>
								<th>Status</th>
								<?php if(Auth::user()->role_id ==29 || Auth::user()->role_id ==24 || Auth::user()->role_id ==21 || Auth::user()->role_id ==20 || Auth::user()->role_id ==27 || Auth::user()->role_id ==28){ ?>
								<th>Action</th>
								<?php } ?>
							</tr>
						</thead>
						<tbody >
						<?php
							if(count($leave_array) > 0){
						$i = 1;
						//foreach($leave_array as $leavArray){ //echo date('Y-m-d', strtotime("-1 days"));die;
							foreach($leave_array as  $key => $value){ 
								$branch_name = "";
								if(isset($value->user->user_branches) && !empty($value->user->user_branches)){
									$user_branches = $value->user->user_branches;
									foreach($user_branches as $branchDetails){
										$branch_data = \App\Branch::where('id', $branchDetails->branch_id)->first();
										if(!empty($branch_data)){
											$branch_name .= $branch_data->name.", ";
										}
										
									}
									$branch_name = rtrim($branch_name,", ");
								}
								
								//Department
								$department = \App\Department::where('id', $value->user->department_type)->first();
								
								
								if(!empty($value->user->id)){
									if(!empty($value->leave_details) && count($value->leave_details) > 0){
										foreach($value->leave_details as $leave_details){ 
										?>
										<tr >
											<td><?=$i++;?></td>
											<td class="product-category"><?php echo e(isset($value->user->register_id) ?  $value->user->register_id : ''); ?></td>
											<td class="product-category"><?php echo e(isset($value->user->name) ?  $value->user->name : ''); ?></td>
											<td class="product-category"><?php echo e($branch_name); ?></td>
											<td><?php echo isset($department->name)?$department->name:''; ?></td>
											<td><?php echo e($value->user_details->degination); ?></td>
											<td class="product-category"><?php echo e(isset($leave_details->created_at) ?  date("d-m-Y", strtotime($leave_details->created_at)) : ''); ?></td>
											<td class="product-category"><?php echo e(isset($leave_details->date) ?  date("d-m-Y", strtotime($leave_details->date)) : ''); ?></td>
											<td class="product-category">
											 <?=$leave_details->category?>
											</td>
											<td class="product-category">
											 <?=$leave_details->type?>
											</td>
											<td class="product-category">
											 <?=$value->reason?>
											</td>
											<td class="product-category">
											 <?=$leave_details->status?>
											</td>
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
												//if($value->date==date('Y-m-d')){
												?>
												<!--a href="<?php echo e(route('admin.task.edit', $value->id)); ?>">
													<span class="action-edit"><i class="feather icon-edit"></i></span>
												</a>
												<a href="<?php echo e(route('admin.task.task_delete', $value->id)); ?>" onclick="return confirm('Are You Sure To Delete Task')">
													<span class="action-delete"><i class="feather icon-trash"></i></span>
												</a-->
												<?php
												//}
												?>
												<?php 
												$prev_month_date = date('Y-m-27', strtotime('-1 MONTH')); 
												if($leave_details->date >= $prev_month_date){
											
													
													if(Auth::user()->role_id ==29 || Auth::user()->role_id ==24){ 
															//Auth::user()->id == '6166'
															//if($leave_details->date <= date('Y-m-d')){
															
													?>
														<a href="<?php echo e(route('admin.leave.edit', $leave_details->id.$nCheck)); ?>">
															<span class="action-edit"><i class="feather icon-edit"></i></span>
														</a>												
													<?php 
																
															//}
													}
													else if(Auth::user()->role_id ==20 || Auth::user()->role_id ==27 || Auth::user()->role_id ==24 || Auth::user()->role_id ==30 || Auth::user()->role_id ==21  || Auth::user()->role_id ==28){
														if($leave_details->status=="Pending"){
														?>
															<!--a href="<?php echo e(route('admin.leave.edit', $leave_details->id.$nCheck)); ?>">
																<span class="action-edit"><i class="feather icon-edit"></i></span>
															</a-->
															
															<a href="<?php echo e(route('admin.leave.delete', $leave_details->id)); ?>">
																<span class="action-edit"><i class="feather icon-trash"></i></span>
															</a>
														<?php 
														}
														else{ 
															echo 'Not Editable'; 
														}
													} 
												}
												?>
												
												
												<?php 
													if(Auth::user()->role_id ==29 || Auth::user()->role_id ==24 || Auth::user()->role_id ==21 || Auth::user()->role_id ==30 || Auth::user()->id == '6166'  || Auth::user()->role_id ==28){ 
														
												?>
												<a href="<?php echo e(route('admin.leave.view', ['leave_id'=>$value->id,'leave_detail_id'=>$leave_details->id.$nCheck])); ?>">
													<span class="action-edit"><i class="feather icon-eye"></i></span>
												</a>
												<?php } ?>
											</td>
										</tr>
										<?php
										}
									}
								}
							}
						//}
						}else{
							?>
							<tr><td class="text-center text-primary" colspan="11">No Record Found</td></tr>
							<?php } ?>	
							
							
						</tbody>
					</table>
				</div>                   
			</section>
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$('.select-multiple1').select2({
		placeholder: "Select Any",
		allowClear: true
	});
	$('.select-multiple2').select2({
		placeholder: "Select Any",
		allowClear: true
	});
});

$("body").on("click", "#download_excel", function (e) {
	/* if ($userTable.data().count() == 0) {
		swal("Warning!", "Not have any data!", "warning");
		return;
	} */
	var data = {};
		data.branch_id = $('.branch_id').val(),
		data.name = $('.name').val(),
		data.status = $('.status').val(),
		data.fdate = $('.fdate').val(),
		data.tdate = $('.tdate').val(),
		data.department_type = $('.department_type').val(),
	window.location.href = "<?php echo URL::to('/admin/'); ?>/leave-report-excel?" + Object.keys(data).map(function (k) {
		return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
	}).join('&');
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/leave/index.blade.php ENDPATH**/ ?>