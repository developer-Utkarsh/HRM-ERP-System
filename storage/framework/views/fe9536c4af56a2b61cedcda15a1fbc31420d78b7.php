
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Employee Wise Free Typist Report</h2>
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
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="<?php echo e(route('admin.typist-work-report')); ?>" method="get" name="filtersubmit">
									<div class="row">
									
										<div class="col-12 col-sm-6 col-lg-3 branch_loader">
											<label for="users-list-status">Employee</label>
											<?php $employee = \App\User::where('status', '1')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 emp_id" name="emp_id" id="">
													<option value="">Select Any</option>
													<?php if(count($employee) > 0): ?>
													<?php $__currentLoopData = $employee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" <?php if($value->id == app('request')->input('emp_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php endif; ?>
												</select>
												<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
											</fieldset>
										</div>
										
										
										 <div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">From Date</label>
											<fieldset class="form-group">												
												<input type="date" name="fdate" placeholder="Date" value="<?php echo e(app('request')->input('fdate')); ?>" class="form-control StartDateClass fdate">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">To Date</label>
											<fieldset class="form-group">												
												<input type="date" name="tdate" placeholder="Date" value="<?php echo e(app('request')->input('tdate')); ?>" class="form-control EndDateClass tdate">
											</fieldset>
										</div>
									</div>
									<fieldset class="form-group" style="float:right;">		
									<button type="submit" class="btn btn-primary">Search</button>
									<a href="<?php echo e(route('admin.typist-work-report')); ?>" class="btn btn-warning">Reset</a>
									<a href="javascript:void(0)" id="download_typist_work_excel" class="btn btn-primary">Export in Excel</a>
									</fieldset>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
				<table class="table data-list-view">
					<thead>
						<tr>
							<th>S.No.</th>
							<th>Name</th>
							<th>No Of Questions</th>
							<th>OCR/panel</th>
							<th>Arrange Correction</th>
							<th>Total Page</th>
							<th>Remark</th>
							<th>Date</th>
							
						</tr>
					</thead>
					<tbody>
				<?php 
				$dataFound = 1;
				if (count($get_typist) > 0) { 
					foreach ($get_typist as $TypistArray) { //echo '<pre>'; print_r($TypistArray->employee->name); die;
								
						?>
							<tr style="">
							<td><?=$dataFound?></td>
							<!-- <td></td> -->
							<td><?php 
							if(!empty($TypistArray->employee->name)){
								echo $TypistArray->employee->name; 
							} ?>
							</td>
							<td><?php 
							if(!empty($TypistArray->number_of_questions)){
								echo $TypistArray->number_of_questions; 
							} ?>
							</td>
							<td><?php 
							if(!empty($TypistArray->ocr_panel)){
								echo $TypistArray->ocr_panel; 
							} ?>
							</td>
							<td><?php 
							if(!empty($TypistArray->arrange_correction)){
								echo $TypistArray->arrange_correction; 
							} ?>
							</td>
							<td><?php 
							if(!empty($TypistArray->total_page)){
								echo $TypistArray->total_page; 
							} ?>
							</td>
							<td><?php 
							if(!empty($TypistArray->remark)){
								echo $TypistArray->remark; 
							} ?>
							</td>
							<td><?php 
							if(!empty($TypistArray->cdate)){
								echo date('d-m-Y',strtotime($TypistArray->cdate)); 
							} ?>
							</td>
							
							
							
							
							</tr>
						 
						<?php 
						$dataFound++; 
					} 
				}
				?>
				</body>
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
		$('.select-multiple3').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
	
	$("body").on("click", "#download_typist_work_excel", function (e) {
		var data = {};
			data.emp_id = $('.emp_id').val(),
			data.fdate = $('.fdate').val(),
			data.tdate = $('.tdate').val(),
		window.location.href = "<?php echo URL::to('/admin/'); ?>/typist-work-report-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/typist_work_report/index.blade.php ENDPATH**/ ?>