<!DOCTYPE html>
<html class="loading" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="">
    <meta name="keywords" content="">   

   <link href="{{url('../laravel/public/logo.png')}}" rel="icon" type="image/ico" />
    <title>{{ config('app.name') }} - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/colors.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/components.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/vertical-menu.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/dashboard-analytics.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css">
	<style>
		.m_body {
			width: 50%;
		}

		@media only screen and (max-width: 600px) {
			.m_body {
				width: 100%;
			}
		}
	</style>
</head>

<body class="vertical-layout vertical-menu-modern 2-columns  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">

<div class="app-content content m_body" style="margin: 0 auto;">
	<div class="content-wrapper">
		<div class="content-body">
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								
								<form id="generate_salary_otp" method="post">
									<div class="row">
										<div class="col-md-6">
											<label for="users-list-role"></label>
											 <input type="text" class="form-control" placeholder="Enter Mobile No" name="mobile_no">
										</div>

										<div class="col-md-6">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Submit</button>
											</fieldset>
										</div>
									</div>
								</form>
								
								<form id="check_salary_otp" method="post" style="display:none;">
									<p class="msg-success text-success">Plesae check OTP on your registered Mobile Number.</p>
									<div class="row">
										
									    <div class="col-md-6">
											<label for="users-list-role"></label>
											 <input type="text" class="form-control" placeholder="Enter OTP" name="otp" value="">
										</div>
										
										
										<div class="col-md-6">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Submit</button>
											</fieldset>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				
				  		
			</section>
		</div>
	</div>

<script src="{{ asset('laravel/public/admin/js/vendors.min.js') }}"></script>
<script src="{{ asset('laravel/public/admin/js/app-menu.js') }}"></script>
<script src="{{ asset('laravel/public/admin/js/app.js') }}"></script>
<script src="{{ asset('laravel/public/admin/js/components.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>	
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">

	$("#generate_salary_otp").submit(function(e) { 
		var form = document.getElementById('generate_salary_otp');
		var dataForm = new FormData(form);
		e.preventDefault();
		$.ajax({
			beforeSend: function(){
				
			},
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},      
			type: "POST",
			url : '{{ route('material-send-otp') }}',
			data : dataForm,
			processData : false, 
			contentType : false,
			dataType : 'json',
			success : function(data){
				if(data.status == false){
					swal("Error!", data.message, "error");
				} 
				else if(data.status == true){
					$("#generate_salary_otp").css('display','none');
					$("#check_salary_otp").css('display','block');
				}
			}
		}); 
	});
	
	$("#check_salary_otp").submit(function(e) {
		$('.msg-success').text('');
		var form1 = document.getElementById('generate_salary_otp');
		var form = document.getElementById('check_salary_otp');
		var dataForm1 = new FormData(form1);
		var dataForm = new FormData(form);
		dataForm.append('mobile_no', dataForm1.get('mobile_no'));
		e.preventDefault();
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},      
			type: "POST",
			url : '{{ route('material-access-otp') }}',
			data : dataForm,
			processData : false, 
			contentType : false,
			dataType : 'json',
			success : function(data){ 
				if(data.status == false){
					swal("Error!", data.message, "error");
				}else if(data.status == true){	 
					window.location.href = "{{ route('employee-details') }}/"+data.material_id+"/web";
				}
			}
		}); 
	});
</script>
@include('layouts.notification')
</body>
</html>