<!--script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
@if (session('success'))
<script>
	swal({
	  	title: "Done!",
	  	text: "{{ session('success') }}",
	  	icon: "success",
	  	button: "ok",
	});
</script>
@endif
@if (session('error'))
<script>
	swal({
	  	title: "Error!",
	  	text: "{{ session('error') }}",
	  	icon: "error",
	  	button: "ok",
	});
</script>
@endif