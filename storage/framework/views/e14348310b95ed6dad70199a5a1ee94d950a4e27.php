<?php
use App\User;
?>

<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">View & Update</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">View & Update
								</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
						<?php 
							$check = $_SERVER['QUERY_STRING'];
							if(!empty($check)){
								$nCheck	=	$check;
							}else{
								$nCheck	=	"";
							}
						?>
						<a href="<?php echo e(route('admin.leave.index', $nCheck)); ?>" class="btn btn-primary mr-1">Back</a>
					</div>
				</div>
			</div>
			<div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
			</div>
		</div>
		<div class="content-body">
			<!-- page users view start -->
			<section class="page-users-view">
				<div class="row">
					<!-- account start -->
					<div class="col-12">
						<div class="card">
							<div class="card-header">
								<div class="card-title">Account</div>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="users-view-image">
										<?php if(!empty($leave->image)): ?>
										<img src="<?php echo e(asset('laravel/public/profile/'. $leave->image)); ?>" class="users-avatar-shadow w-100 rounded mb-2 pr-2 ml-1" alt="avatar">
										<?php else: ?>
										<img src="<?php echo e(asset('laravel/public/images/test-image.png')); ?>" class="users-avatar-shadow w-100 rounded mb-2 pr-2 ml-1" alt="avatar">
										<?php endif; ?>
									</div>
									<div class="col-12 col-sm-9 col-md-6 col-lg-5">
										<table class="table">
											<tr>
												<td class="font-weight-bold">Employee Id</td>
												<td><?php echo e(isset($leave->register_id)?$leave->register_id:''); ?></td>
											</tr>
											<tr>
												<td class="font-weight-bold">Name</td>
												<td><?php echo e(isset($leave->name)?$leave->name:''); ?></td>
											</tr>
											<tr>
												<td class="font-weight-bold">Email</td>
												<td><?php echo e(isset($leave->email)?$leave->email:''); ?></td>
											</tr>
											<tr>
												<td class="font-weight-bold">Contact Number</td>
												<td><?php echo e(isset($leave->mobile)?$leave->mobile:''); ?></td>
											</tr>
											<!--tr>
												<td class="font-weight-bold">Leave Status</td>
												<td><?php echo e($leave->status); ?></td>
											</tr-->
											<tr>
												<td class="font-weight-bold">Leave Reason</td>
												<td><?php echo e($leave->reason); ?></td>
											</tr>
										</table>
									</div>
									
									<div class="col-12 col-sm-9 col-md-6 col-lg-5">
										<table class="table">
											<tr>
												<td class="font-weight-bold">Total Remaining PL</td>
												<td><?php echo e(!empty($pending_leaves->data) ? $pending_leaves->data->pending_pl : '0'); ?></td>
											</tr>
											<tr>
												<td class="font-weight-bold">Total Remaining CL</td>
												<td><?php echo e(!empty($pending_leaves->data) ? $pending_leaves->data->pending_cl : '0'); ?></td>
											</tr>
											
											<tr>
												<td class="font-weight-bold">Total Remaining Paternity Leave</td>
												<td><?php echo e(!empty($pending_leaves->data) ? $pending_leaves->data->pending_paternity_leave : '0'); ?></td>
											</tr>
											
											<tr>
												<td class="font-weight-bold">Total Remaining Comp Off &nbsp;&nbsp;</td>
												<td><?php echo e(!empty($pending_leaves->data) ? $pending_leaves->data->pending_comp_off : '0'); ?></td>
											</tr>
											
											<tr>
												<td class="font-weight-bold"><span style="color:red;"><?php echo e($full_name_month); ?></span> Month Total <br/>Leave Approved &nbsp;&nbsp;</td>
												<td><?php echo e($total_this_month_leave); ?></td>
											</tr>
											
										</table>
									</div>
									 
								</div>
							</div>
						</div>
					</div>
					<!-- account end -->
					<!-- information start -->
					<div class="col-12">
						<div class="card">
							<div class="card-header">
								<div class="card-title">Leave Approved/Rejected</div>
							</div>
							<div class="card-body">
							<div class="row">
								<div class="col-12 col-sm-9 col-md-12">
									<table class="table">
										<tr>
											<td class="font-weight-bold">Leave Date</td>
											<td class="font-weight-bold">Category</td>
											<td class="font-weight-bold">Type</td>
											<td class="font-weight-bold">Status</td>
											<td class="font-weight-bold">Action</td>
										</tr>
										<?php
										$leave_approve = true;
										foreach($leave_details as $val){
											if($val->status=="Approved"){
												$leave_approve = false;
											}
											?>
											<tr>
												<td><?php echo e(date("d-m-Y", strtotime($val->date))); ?></td>
												<td><?php echo e($val->category); ?></td>
												<td><?php echo e($val->type); ?></td>
												<td>
												
												<?php echo e($val->status); ?>												
												</td>
												
												<td>
												<?php if(!empty($val->emp_id) && $val->emp_id != Auth::user()->id): ?>
												<?php
												$prev_month_date = date('Y-m-01', strtotime('-1 MONTH'));
												//if($val->date >= $prev_month_date && ($val->date >= date('Y-m-01') || date('Y-m-d') < date('Y-m-10'))){
														
													if(Auth::user()->role_id==21 && strtotime(date('Y-m-d')) > strtotime($val->date) && $val->status!="Pending"){
														
													}
													/* else if(Auth::user()->role_id==24 && Auth::user()->id != '6166'){
														
													} */
													else{
													//if(Auth::user()->role_id==29 || strtotime($val->date) > strtotime(date('Y-m-d'))){
														$_display = "block";
														if(Auth::user()->role_id!=29 && Auth::user()->role_id!=24){
															$_display = "none";
														}
												?>
													<select class="leave_category" name="leave_category" required style="display:<?php echo e($_display); ?>">
														<option value=""> - Select Any - </option>
														<option value="PL" <?php echo ($val->category=="PL")?'selected':''; ?> >PL</option>
														<option value="CL" <?php echo ($val->category=="CL")?'selected':''; ?>>CL</option>
														<option value="Paternity Leave" <?php echo ($val->category=="Paternity Leave")?'selected':''; ?>>Paternity Leave</option>
														<option value="Comp Off" <?php echo ($val->category=="Comp Off")?'selected':''; ?>>Comp Off</option>
														<option value="LWP" <?php echo ($val->category=="LWP")?'selected':''; ?>>LWP</option>
													</select>
													
													<select class="leave_status" name="leave_status" required>
														<option value=""> - Select Any - </option>
														<option value="Approved" <?php echo ($val->status=="Approved")?'selected':''; ?> >Approved</option>
														<option value="Rejected" <?php echo ($val->status=="Rejected")?'selected':''; ?>>Rejected</option>
													</select>
													<span class="btn btn-success btn-sm save_status" data-leave-detail-id="<?php echo e($val->id); ?>">Submit</span>
												<?php
												//}
													}
												//}
												?>
												<?php endif; ?>
												</td>
												
											</tr>
											<?php
										}
										?>
									</table>
								</div>
							</div>
							</div>
							<?php
							//if(Auth::user()->role_id==29 || strtotime($val->date) > strtotime(date('Y-m-d'))){
								?>
								<?php if(!empty($val->emp_id) && $val->emp_id != Auth::user()->id): ?>
								<?php
								/*
								if(Auth::user()->role_id==21 && $leave_approve == false ){
												
								}
								else{
									?>
								<form class="form" action="{{ route('admin.leave.approval', $leave->leave_detail_id) }}" method="post" enctype="multipart/form-data">
								@csrf
								<input type="hidden" name="leave_id" value="{{$leave->id}}" >
								<div class="card-body">
									<div class="row">
										<div class="col-md-6 col-12">
											<div class="form-group d-flex align-items-center">
												<label class="mr-4">All Upper Leaves Status :</label>
												<select class="form-control leave_approval" name="leave_approval" required>
													<option value=""> - Select Any - </option>
													<option value="Approved" <?php //echo ($leave->status=="Approved")?'selected':''; ?> >Approved</option>
													<option value="Rejected" <?php //echo ($leave->status=="Rejected")?'selected':''; ?>>Rejected</option>
												</select>
											</div>
										</div>   
										
									</div>
									<div class="row leave_reject_reason_div" style="display:none;">
										<div class="col-md-12 col-12">
											<div class="form-group d-flex align-items-center">
												<label class="mr-2"> Reason :</label>
												<input type="text" class="form-control" name="leave_reject_reason" value="<?=$leave->leave_reason?>">
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-2 col-12">
										 &nbsp;
										</div>
										<div class="col-md-6 col-12">
											<button type="submit" class="btn btn-primary mr-1 mb-1">Submit</button>
										</div>
									</div>
								</div>
								</form>
								<?php
									}
									*/
								?>
								<?php endif; ?>
							<?php
							//}
							?>
						</div>
					</div>
					<!-- information start -->
					<!-- social links end -->
					 
					
					
				</div>
			</section>
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo e(asset('laravel/public/admin/css/app-user.css')); ?>">

<script>
$(document).on("change",".leave_approval", function () { 
	var status = $(this).val(); 
	$(".leave_reject_reason_div").hide();	
	if(status=="Rejected"){
		$(".leave_reject_reason_div").show();
	}
	
});

$(document).on("click",".save_status", function () {
	var thisVal = $(this);
	var leave_detail_id = $(this).attr('data-leave-detail-id'); 
	var leave_status = $(thisVal).siblings(".leave_status").val();
	var leave_category = $(thisVal).siblings(".leave_category").val();
	if(leave_status !="" && leave_category !=""){
		$.ajax({
			beforeSend: function(){
				// $(".branch_loader i").show();
			},
			type : 'POST',
			url : '<?php echo e(route('admin.leave.approve_one_by')); ?>',
			data : {'_token' : '<?php echo e(csrf_token()); ?>', 'leave_status': leave_status,"leave_detail_id" : leave_detail_id, "leave_category":leave_category},
			dataType : 'json',
			success : function (data){
				if(data.status == false){
					swal("Error!", data.message, "error");
				} 
				else if(data.status == true){
					swal("Done!", data.message, "success").then(function(){ 
						location.reload();
					});
				}
			}
		});
	}
	else{
		swal({
		  title: "Action",
		  text: "Action is required. Please select action.",
		  icon: "error",
		});
	}
	
});
</script>

<script>
document.addEventListener('contextmenu', function(e) {
		e.preventDefault();
	});
	
	document.onkeydown = function(e) {
		if(event.keyCode == 123) {
			return false;
		}
		if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {
			return false;
		}
		if(e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) {
			return false;
		}
		if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {
			return false;
		}
		if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {
			return false;
		}
	}

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/leave/view.blade.php ENDPATH**/ ?>