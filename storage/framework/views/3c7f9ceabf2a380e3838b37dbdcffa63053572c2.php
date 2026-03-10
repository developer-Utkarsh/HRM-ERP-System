
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
			  <a href="<?php echo e(route('faculty-sme-request-index')); ?>?user_id=<?php echo e($user_id); ?>" class="view-all-btn">View All Request</a>
			</div>
		</header>
		<div class="mid-content">
			<?php $check = DB::table('faculty_sme_request')->where('request_id',$request_id)->first();	?>
			<div class="request-panel-div">
				<div class="request-status">
					<b>Req ID. <?php echo e($request_id); ?></b>    
					<?php if($check->status==2){ ?><strong class="reject-">Rejected</strong><?php } ?>
				</div>
				
				<?php if($check->status==2){ ?>
				<div class="red-msg">
				   <p><?php echo e($check->reason); ?></p>
				</div>
				<?php } ?>
				
				<?php 
					foreach($record as $re){ 
						if($re->user_id ==  $user_id ){
				?>
					<div class="request-msg">
						<b><?php echo e($re->name); ?> <i><?php echo e(date("jS F, Y | h:ia", strtotime($re->created_at))); ?></i></b>
						<p><?php echo e($re->message); ?></p>
					</div>
				<?php }else{ ?>
					<div class="request-msg">
						<b><i><?php echo e(date("jS F, Y | h:ia", strtotime($re->created_at))); ?></i> <strong> <?php echo e($re->name); ?></strong></b> 
						<p><?php echo e($re->message); ?></p>
					</div>
				<?php } } ?>
				
					  
			</div>
		</div>
		<footer class="footer-chat-section">
			<form action="<?php echo e(route('faculty-sme-request-chat-submit')); ?>" method="post">
				<?php echo csrf_field(); ?>
				<input type="text" name="chat_msg" value="" placeholder="Type your message here">
				<input type="hidden" name="user_id" value="<?php echo e($user_id); ?>">
				<input type="hidden" name="request_id" value="<?php echo e($request_id); ?>">
				<button type="submit"><img src="<?php echo e(asset('laravel/public/send-icon.svg')); ?>" alt=""/> Send</button>
			</form>
		</footer>
	</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.without_login_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/all_reports/faculty-sme/chat.blade.php ENDPATH**/ ?>