
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Edit Employee Approval</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">View Employee</a>
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
						<a href="<?php echo e(route('admin.employees.index', $nCheck)); ?>" class="btn btn-primary mr-1">Back</a>
					</div>
				</div>
			</div>
			<div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
			</div>
		</div>
		<?php
		$user_details = $employee->user_details;
		$user_details_pending = $employee_pending->user_details_pending;
		?>
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
										<?php if(!empty($employee->image)): ?>
										<img src="<?php echo e(asset('laravel/public/profile/'. $employee->image)); ?>" class="users-avatar-shadow w-100 rounded mb-2 pr-2 ml-1" alt="avatar">
										<?php else: ?>
										<img src="<?php echo e(asset('laravel/public/images/test-image.png')); ?>" class="users-avatar-shadow w-100 rounded mb-2 pr-2 ml-1" alt="avatar">
										<?php endif; ?>
										<?php
										if($employee->image!=$employee_pending->image){
											?>
											<span class="" style="background-color:yellow;">Image Change</span>
											<?php
										}
										?>
									</div>
									<div class="col-12 col-sm-9 col-md-6 col-lg-5">
										<table>
											<tr>
												<td class="font-weight-bold">Employee Id</td>
												<td><?php echo e($employee->register_id); ?></td>
											</tr>
											<tr>
												<td class="font-weight-bold">Name</td>
												<td>
												<span><?php echo e($employee->name); ?></span>
												<?php
												if($employee->name!=$employee_pending->name){
													?>
													<span class="" style="background-color:yellow;"><?=$employee_pending->name?></span>
													<?php
												}
												?>
												</td>
											</tr>
											<tr>
												<td class="font-weight-bold">Email</td>
												<td>
												<?php echo e($employee->email); ?>

												<?php
												if($employee->email!=$employee_pending->email){
													?>
													<span class="" style="background-color:yellow;"><?=$employee_pending->email?></span>
													<?php
												}
												?>
												</td>
											</tr>
											<tr>
												<td class="font-weight-bold">Contact Number</td>
												<td>
												<?php echo e($employee->mobile); ?>

												<?php
												if($employee->mobile!=$employee_pending->mobile){
													?>
													<span class="" style="background-color:yellow;"><?=$employee_pending->mobile?></span>
													<?php
												}
												?>
												</td>
											</tr>
										</table>
									</div>
									<div class="col-12 col-md-12 col-lg-5">
										<table class="ml-0 ml-sm-0 ml-lg-0">
											<tr>
												<td class="font-weight-bold">Role</td>
												<td><?php echo e($employee->role->name); ?></td>
											</tr>
											<tr>
												<td class="font-weight-bold">Branch</td>
												<td>
													<?php
													$branch_names = "";
													if(isset($employee->user_branches) && !empty($employee->user_branches)){
														foreach($employee->user_branches as $key => $val) { 
															if(!empty($val->branch->name)) {
																$branch_names .= $val->branch->name .", ";
															}
														}
													}
													echo rtrim($branch_names, ", "); 
													?>
												</td>
											</tr>
											<tr>
												<td class="font-weight-bold">Designation</td>
												<td><?php echo e(isset($employee->user_details->degination) ?  $employee->user_details->degination : ''); ?>

												<?php
													if($user_details->degination != $user_details_pending->degination){
													?>
														<span class="" style="background-color:yellow;"><?=$user_details_pending->degination?></span>
													<?php
													}
												?>
												</td>
											</tr>
											<tr>
												<td class="font-weight-bold">Employee type</td>
												<td><?php echo e(isset($employee->user_details->employee_type) ?  $employee->user_details->employee_type : ''); ?>

												<?php
													if($user_details->employee_type != $user_details_pending->employee_type){
													?>
														<span class="" style="background-color:yellow;"><?=$user_details_pending->employee_type?></span>
													<?php
													}
												?>
												</td>
											</tr>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- account end -->
					
					<!-- information start -->
					<div class="col-md-6 col-12 ">
						<div class="card">
							<div class="card-header">
								<div class="card-title mb-2">Information</div>
							</div>
							<div class="card-body">
								<table>
									<tr>
										<td class="font-weight-bold">Birth Date </td>
										<td>
										<?php 
										if(!empty($user_details->dob)){
											$dob = date("d F Y", strtotime($user_details->dob)); 
											echo $dob;
										}
										?>
										<?php
										if(isset($user_details->dob) && isset($user_details_pending->dob)){
											if($user_details->dob != $user_details_pending->dob){
											?>
												<span class="" style="background-color:yellow;"><?=$user_details_pending->dob?></span>
											<?php
											}
										}
										?>
										</td>
									</tr>
									<tr>
										<td class="font-weight-bold">Alternate Number</td>
										<td>
										<?php echo e(isset($employee->user_details->alternate_contact_number) ?  $employee->user_details->alternate_contact_number : ''); ?>

										<?php
											if($user_details->alternate_contact_number != $user_details_pending->alternate_contact_number){
											?>
												<span class="" style="background-color:yellow;"><?=$user_details_pending->alternate_contact_number?></span>
											<?php
											}
										?>
										</td>
									</tr>
									<tr>
										<td class="font-weight-bold">Alternate Email</td>
										<td>
										<?php echo e(isset($employee->user_details->alternate_email) ?  $employee->user_details->alternate_email : ''); ?>

										<?php
											if($user_details->alternate_email != $user_details_pending->alternate_email){
											?>
												<span class="" style="background-color:yellow;"><?=$user_details_pending->alternate_email?></span>
											<?php
											}
										?>
										</td>
									</tr>
									<tr>
										<td class="font-weight-bold">Father's Name</td>
										<td>
										<?php echo e(isset($employee->user_details->fname) ?  $employee->user_details->fname : ''); ?>

										<?php
											if($user_details->fname != $user_details_pending->fname){
											?>
												<span class="" style="background-color:yellow;"><?=$user_details_pending->fname?></span>
											<?php
											}
										?>
										</td>
									</tr>
									<tr>
										<td class="font-weight-bold">Mother's Name</td>
										<td>
										<?php echo e(isset($employee->user_details->mname) ?  $employee->user_details->mname : ''); ?>

										<?php
											if($user_details->mname != $user_details_pending->mname){
											?>
												<span class="" style="background-color:yellow;"><?=$user_details_pending->mname?></span>
											<?php
											}
										?>
										</td>
									</tr>
									<tr>
										<td class="font-weight-bold">Gender</td>
										<td>
										<?php echo e(isset($employee->user_details->gender) ?  $employee->user_details->gender : ''); ?>

										<?php
											if($user_details->gender != $user_details_pending->gender){
											?>
												<span class="" style="background-color:yellow;"><?=$user_details_pending->gender?></span>
											<?php
											}
										?>
										</td>
									</tr>
									<tr>
										<td class="font-weight-bold">Marital Status</td>
										<td>
										<?php echo e(isset($employee->user_details->material_status) ?  $employee->user_details->material_status : ''); ?>

										<?php
											if($user_details->material_status != $user_details_pending->material_status){
											?>
												<span class="" style="background-color:yellow;"><?=$user_details_pending->material_status?></span>
											<?php
											}
										?>
										</td>
									</tr>
									<tr>
										<td class="font-weight-bold">Current Addres</td>
										<td>
										<?php echo e(isset($employee->user_details->c_address) ?  $employee->user_details->c_address : ''); ?>

										<?php
											if($user_details->c_address != $user_details_pending->c_address){
											?>
												<span class="" style="background-color:yellow;"><?=$user_details_pending->c_address?></span>
											<?php
											}
										?>
										</td>
									</tr>
									<tr>
										<td class="font-weight-bold">Permanent Address</td>
										<td>
										<?php echo e(isset($employee->user_details->p_address) ?  $employee->user_details->p_address : ''); ?>

										<?php
											if($user_details->p_address != $user_details_pending->p_address){
											?>
												<span class="" style="background-color:yellow;"><?=$user_details_pending->p_address?></span>
											<?php
											}
										?>
										</td>
									</tr>
									<tr>
										<td class="font-weight-bold">Blood Group</td>
										<td>
										<?php echo e(isset($employee->user_details->blood_group) ?  $employee->user_details->blood_group : ''); ?>

										<?php
											if($user_details->blood_group != $user_details_pending->blood_group){
											?>
												<span class="" style="background-color:yellow;"><?=$user_details_pending->blood_group?></span>
											<?php
											}
										?>
										</td>
									</tr>
									<tr>
										<td class="font-weight-bold">Joining Date</td>
										<td>
										<?php echo e(isset($employee->user_details->joining_date) ?  $employee->user_details->joining_date : ''); ?>

										<?php
											if($user_details->joining_date != $user_details_pending->joining_date){
											?>
												<span class="" style="background-color:yellow;"><?=$user_details_pending->joining_date?></span>
											<?php
											}
										?>
										</td>
									</tr>
									
								</table>
							</div>
						</div>
					</div>
					<!-- information start -->
					<!-- social links end -->
					<div class="col-md-6 col-12 ">
						<div class="card">
							<div class="card-header">
								<div class="card-title mb-2">Account Information</div>
							</div>
							<div class="card-body">
								<table>
									<tr>
										<td class="font-weight-bold">Account Number</td>
										<td>
										<?php echo e(isset($employee->user_details->account_number) ?  $employee->user_details->account_number : ''); ?>

										<?php
											if($user_details->account_number != $user_details_pending->account_number){
											?>
												<span class="" style="background-color:yellow;"><?=$user_details_pending->account_number?></span>
											<?php
											}
										?>
										</td>
									</tr>
									<tr>
										<td class="font-weight-bold">Bank Name</td>
										<td>
										<?php echo e(isset($employee->user_details->bank_name) ?  $employee->user_details->bank_name : ''); ?>

										<?php
											if($user_details->bank_name != $user_details_pending->bank_name){
											?>
												<span class="" style="background-color:yellow;"><?=$user_details_pending->bank_name?></span>
											<?php
											}
										?>
										</td>
									</tr>
									<tr>
										<td class="font-weight-bold">IFSC Code</td>
										<td>
										<?php echo e(isset($employee->user_details->ifsc_code) ?  $employee->user_details->ifsc_code : ''); ?>

										<?php
											if($user_details->ifsc_code != $user_details_pending->ifsc_code){
											?>
												<span class="" style="background-color:yellow;"><?=$user_details_pending->ifsc_code?></span>
											<?php
											}
										?>
										</td>
									</tr>
									<tr>
										<td class="font-weight-bold">Branch</td>
										<td>
										<?php echo e(isset($employee->user_details->bank_branch) ?  $employee->user_details->bank_branch : ''); ?>

										<?php
											if($user_details->bank_branch != $user_details_pending->bank_branch){
											?>
												<span class="" style="background-color:yellow;"><?=$user_details_pending->bank_branch?></span>
											<?php
											}
										?>
										</td>
									</tr>
									<tr>
										<td class="font-weight-bold">Gross Salary</td>
										<td>
										<?php echo e(isset($employee->user_details->net_salary) ?  $employee->user_details->net_salary : ''); ?>

										<?php
											if($user_details->net_salary != $user_details_pending->net_salary){
											?>
												<span class="" style="background-color:yellow;"><?=$user_details_pending->net_salary?></span>
											<?php
											}
										?>
										</td>
									</tr>
									<tr>
										<td class="font-weight-bold">TDS</td>
										<td>
										<?php echo e(isset($employee->user_details->tds) ?  $employee->user_details->tds : ''); ?>

										<?php
											if($user_details->tds != $user_details_pending->tds){
											?>
												<span class="" style="background-color:yellow;"><?=$user_details_pending->tds?></span>
											<?php
											}
										?>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					
					<div class="col-12">
						<div class="card">
							<div class="card-header">
								<div class="card-title">Approved/Rejected</div>
							</div>
							<form class="form" action="<?php echo e(route('admin.employees.approval_update', $employee->id)); ?>" method="post" enctype="multipart/form-data">
							<?php echo csrf_field(); ?>
							<div class="card-body">
								<div class="row">
									<div class="col-md-6 col-12">
										<div class="form-group d-flex align-items-center">
											<label class="mr-2"> Status :</label>
											<select class="form-control" name="admin_approval">
												<option value=""> - Select Any - </option>
												<option value="Approved">Approved</option>
												<option value="Rejected">Rejected</option>
											</select>
										</div>
									</div>   
									<div class="col-md-6 col-12">
										<button type="submit" class="btn btn-primary mr-1 mb-1">Submit</button>
									</div>
								</div>
							</div>
							</form>
						</div>
					</div>
					
					
				</div>
			</section>
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo e(asset('laravel/public/admin/css/app-user.css')); ?>">
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/employee/approval.blade.php ENDPATH**/ ?>