<!--script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<?php if(session('success')): ?>
<script>
	swal({
	  	title: "Done!",
	  	text: "<?php echo e(session('success')); ?>",
	  	icon: "success",
	  	button: "ok",
	});
</script>
<?php endif; ?>
<?php if(session('error')): ?>
<script>
	swal({
	  	title: "Error!",
	  	text: "<?php echo e(session('error')); ?>",
	  	icon: "error",
	  	button: "ok",
	});
</script>
<?php endif; ?><?php /**PATH /var/www/html/laravel/resources/views/layouts/notification.blade.php ENDPATH**/ ?>