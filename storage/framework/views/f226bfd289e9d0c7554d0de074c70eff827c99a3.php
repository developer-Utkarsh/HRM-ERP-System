
<?php $__env->startSection('content'); ?>

<link href="<?php echo e(asset('laravel/public/css/sme_styles.css')); ?>" rel="stylesheet">
<div class="question-main-file batch-info-page">
	<header class="header-section">
		<div class="back-arow"> 
			<span>
				<a href="<?php echo e(route('all-reports')); ?>?user_id=<?php echo e($user_id); ?>" style="border:none;">
				<svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22.16 22.16">
					<path id="Icon_ionic-ios-arrow-dropleft-circle" data-name="Icon ionic-ios-arrow-dropleft-circle" d="M-56.77,9.19A11.08,11.08,0,0,0-67.85,20.26,11.08,11.08,0,0,0-56.77,31.34,11.08,11.08,0,0,0-45.69,20.26h0A11.08,11.08,0,0,0-56.77,9.19Zm2.31,15.4a1,1,0,0,1,0,1.45,1,1,0,0,1-.72.3,1,1,0,0,1-.73-.3l-5-5a1,1,0,0,1,0-1.42l5.08-5.1a1,1,0,0,1,1.45,0,1,1,0,0,1,0,1.46l-4.36,4.31Z" transform="translate(67.85 -9.19)"></path>
				</svg>
				<b>Request Panel</b>
				</a>
			</span>
			<a href="#" class="view-all-btn d-none">View All Request</a>
		 </div>
	</header>
	
	<div class="mid-content">
		<div class="add-request-page">
			<div class="mid-content">
				<div class="add-request-btn">
					<p>For Request Panel pdf click on below add Request</p>
					<a href="<?php echo e(route('faculty-sme-request-create')); ?>?user_id=<?php echo e($user_id); ?>" class="primary-btn">Add Request</a>
				</div>
			</div>
			<?php if(count($record) >0){ ?>
			<b class="filter-btn">Recent Request</b>			
			<?php 				 
				foreach($record as $val){ 		
					if($val->status==1){
						$status = 'Completed';
						$class = 'complete';
					}else if($val->status==2){
						$status = 'Rejected';
						$class = 'reject';
					}else if($val->status==3){
						$status = 'Picked';
						$class = 'pending';
					}else{
						$status = 'Pending';
						$class = 'pending';
					}
					
					$record_file = DB::table('faculty_sme_upload')->where('request_id',$val->request_id)->first();
			?>
			<div class="request-panel-div">
				<div class="request-status">
					<b>Req ID. <?php echo e($val->request_id); ?></b>    
					<strong class="<?php echo e(strtolower($class)); ?>-"><?php echo e($status); ?> </strong>
				</div>
				<div class="typ-msg">
					<p><?php echo e($val->message); ?></p> 
				</div>
				
				<span class="pdf-yellow-bg">
					PDF required on : <?php echo e(date("jS F, Y | h:ia", strtotime($val->date))); ?>

				</span>
				
				<?php if (!empty($record_file->subject) && !empty($record_file->topic)) { ?>
				<div class="px-1 pb-1 w-100">
					<table style="border:solid 1px #E8F4FF" cellpadding="5" cellspacing="0" width="100%">
						<tr style="background:#E8F4FF;">
							<td style="width:40%">Chapter</td>
							<td style="width:40%">Topic</td>
							<td style="width:20%">No. of Ques</td>
						</tr>
						<tr>
							<td>
								<?php 
									if (!empty($record_file->subject)) {
										$subject = json_decode($record_file->subject);
										foreach ($subject as $su) {
											echo $su->name . ',';
										}
									} else {
										echo '-';
									}
								?>
							</td>
							<td>
								<?php 
									if (!empty($record_file->topic)) {
										$topic = json_decode($record_file->topic);
										foreach ($topic as $to) {
											echo $to->name . ',';
										}
									} else {
										echo '-';
									}
								?>
							</td>
							<td><?php echo e($record_file->no_question??'-'); ?></td>
						</tr>
					</table>	
				</div>
				<?php } ?>
				
				<i class="date-section">Request on: <?php echo e(date("jS F, Y | h:ia", strtotime($val->created_at))); ?></i>
				<div class="chat-btn">
					<?php if(!empty($record_file)): ?>
						<a href="<?php echo e(asset('laravel/public/faculty_sme/' . $record_file->file)); ?>">View File</a>
					<?php endif; ?>

					<a href="<?php echo e(route('faculty-sme-request-chat')); ?>?user_id=<?php echo e($user_id); ?>&request_id=<?php echo e($val->request_id); ?>">Chat with SME   <?php if($val->chat_status_2_count >0){ ?> <i class="fa fa-circle text-danger" aria-hidden="true"></i>  <?php } ?></a>
				</div>
			</div>
			<?php } }else{ ?>
			<div class="mid-content">
				<div class="empty-page">
					<center>
						<img src="<?php echo e(asset('laravel/public/empty-pdf.svg')); ?>" alt=""/>
					</center>
					<p>Your Requested panel will be shown here</p>
				</div>
			</div>   
				
			<?php } ?>
		</div>
	</div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.without_login_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/all_reports/faculty-sme/index.blade.php ENDPATH**/ ?>