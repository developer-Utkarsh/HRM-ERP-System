<?php //echo phpinfo(); die() ?>


<?php $__env->startSection('content'); ?>

<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0"><?php echo e($heading); ?> Role & Permission</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				
				<div class="table-responsive">
					<div class="container-fluid" style="background-color: #fff;">
						<div class="text-left" >							
							<form action="<?php echo e($url); ?>" method="post" style="padding:0;">						<?php echo csrf_field(); ?>
								<div style="padding:10px;">
									<div class="py-1"><input type="checkbox" onchange="checkAll(this)" name="chk[]" value=""/>  Check All Permission</div>
									<div class="py-1">
										<input type="text" name="name" value="<?=$title;?>" placeholder="Please Enter Your Role Name" style="width:100%;padding:5px;" <?php if(!empty($urlName)){ echo 'readonly'; }?>/>
									</div>
									<?php 
									$permission=DB::table('access_permission')->select('module')->groupBy('module')->get();		
									foreach($permission as $pe){
									?>					
									<div class="py-2 border-top">
										<div style="float:left;font-weight:bold" class="w-25"><?php echo e(ucwords($pe->module)); ?></div>	
										<div style="float:right;" class="w-75">
											<?php 
												$module=DB::table('access_permission')->where('module', $pe->module)->get();

												if(!empty($permission_ids)){
												  $permissionIds=explode(",",$permission_ids);
												}	

												foreach($module as $m){	
													$selected = '';
													if(!empty($permission_ids)){
														if(in_array($m->id,$permissionIds)){
															$selected='checked';
														}
													}
											?>
											<div style="float:left;" class="w-50">
												<input type="checkbox" name="permissions[]" value="<?php echo e($m->id); ?>" <?=$selected;?>/>
												<?php echo e(ucwords($m->permission)); ?>

											</div>	
											<?php } ?>
										</div>
										<div style="clear:both"></div>
									</div>
																		
									<?php } ?>									
								</div>
								<div class="text-right">
									<button type="submit" class="btn btn-primary mr-1 mb-1">Submit</button>
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
<script type="text/javascript">
function checkAll(ele) {
     var checkboxes = document.getElementsByTagName('input');
     if (ele.checked) {
         for (var i = 0; i < checkboxes.length; i++) {
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = true;
             }
         }
     } else {
         for (var i = 0; i < checkboxes.length; i++) {
             console.log(i)
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = false;
             }
         }
     }
 }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/roles_permission/permission.blade.php ENDPATH**/ ?>