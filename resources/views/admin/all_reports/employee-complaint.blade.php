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
	<div class="content-wrapper" style="margin-top: 2px;">
		<div class="content-body">
			<section id="data-list-view" class="data-list-view-header">
				<!--
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								
								<div class="table-responsive">
									<table class="table data-list-view">
										<tr>
											<th>Name</th>
											<td>{{ !empty($user_details->name) ? $user_details->name : '' }}</td>
										</tr>
										<tr>
											<th>Emp Code</th>
											<td>{{ !empty($user_details->register_id) ? $user_details->register_id : '' }}</td>
										</tr>
										<tr>
											<th>Mobile No</th>
											<td>{{ !empty($user_details->mobile) ? $user_details->mobile : '' }}</td>
										</tr>
										<tr>
											<th>Department</th>
											<td>{{ !empty($user_details->departments_name) ? $user_details->departments_name : '' }}</td>
										</tr>
										<tr>
											<th>Center name</th>
											<td>{{ !empty($user_details->branches_name) ? $user_details->branches_name : '' }}</td>
										</tr>
									</table>
								</div> 
								

							</div>
						</div>
					</div>
				</div>
				-->
				
				<div class="text-right pb-2">
					<a href="{{ route('employee-complaint-history',[$user_id]) }}" class="btn-dark p-1" style="border-radius:3px;">History</a>
				</div> 
				<div class="card">
					<div class="card-content">
						
						<div class="card-body">
							<div>
								<b>नमस्कार, </b></br>
								<p style="font-size:13px;">
									आप यहॉं पर मुझे किसी भी प्रकार की सलाह/समस्या/विचार/सुझाव/आईडियाज से अवगत करवा सकते हैं।ये मैसेज रोजाना मैं स्वयं देखता हूँ तथा सभी प्रकार के मैसेज पर तुरंत उचित कार्रवाई करता हूँ।
									निवेदन हैं कि यहॉं पर कोई भी सलाह/समस्या/विचार/सुझाव/आईडियाज लिखने से पहले सुनिश्चित कर लेवें कि - 
								</p>
								<p style="font-size:13px;">
									1. ये मैसेज सीधे मुझे भेजने लायक है तथा आप मुझे भेजने से पहले अपने टीम लीडर/डिपार्टमेंट हेड को अवगत करवा चुके हैं ?  </br>
									2. कोई ऐसी समस्या हो जो आपके अलावा भी संस्थान में कईयों को हो सकती है तथा आपकी समस्या के समाधान से कईयों की समस्या का समाधान स्वतः हो जायेगा।तो ऐसी समस्या जरूर बतायें। 
								</p>

								<p style="font-size:13px;">अब आप निडर व ज़िम्मेदार होकर बिना पूर्वाग्रहों से ग्रसित होकर अपनी सलाह/समस्या/विचार/सुझाव/आईडियाज नीचे दिए गए कॉलम में विस्तार से लिखें।</p>
							</div>
							<hr>
							<form class="form" action="{{ route('employee-complaint-store') }}" method="post" enctype="multipart/form-data">
								@csrf
								<div class="form-body">
									<div class="row">
										<div class="col-md-12 col-12 modal-body">
											<label><b>Message To CEO</b></label>
											<textarea name="message" class="form-control" rows="5" required> </textarea>
											<input type="hidden" name="user_id" class="form-control" value="<?=$user_id;?>"/>
										</div>
										<!--
										<div class="col-md-12 col-12 modal-body">
											<label><b>क्या आपके कोई सुझाव हैं जो आप सीधा मैनेजमेंट को बताना चाहते हैं? </b></label>
											<textarea name="suggestion" class="form-control" rows="2" required > </textarea>
											<input type="hidden" name="user_id" class="form-control" value="<?=$user_id;?>"/>
										</div>
										<div class="col-md-12 col-12 modal-body">
											<label><b>क्या आपके कोई समस्या हैं जो आप सीधा मैनेजमेंट को बताना चाहते हैं?</b> </label>
											<textarea name="management_issue" class="form-control" rows="2" required> </textarea>
										</div>
										<div class="col-md-12 col-12 modal-body">
											<label><b>क्या उत्कर्ष के किसी भी स्टाफ के बारे में कोई बात मैनेजमेंट से साझा करना चाहते हैं?</b></label>
											<div class="row mx-0">
												<div class="float-left w-50 pt-1"><input type="radio" name="emp_issue" value="1" class="input" onChange="getissue(this.value)" id="rad1"/> <label for="rad1">हाँ</label></div>
												<div class="float-left w-50 pt-1"><input type="radio" name="emp_issue" value="2" class="input" checked onChange="getissue(this.value)" id="rad2"/><label for="rad2"> नहीं</label></div>
											</div>												
										</div>
										
										<div style="display:none" class="w-100" id="employee">
											<div class="col-md-12 col-12 modal-body" >
												<label><b>नाम</b></label>
												<input type="text"	name="name" class="form-control" value=""/>
												
											</div>
											<div class="col-md-12 col-12 modal-body">
												<label><b>डिपार्टमेंट</b></label>
												<input type="text"	name="department" class="form-control" value=""/>
											</div>
											<div class="col-md-12 col-12 modal-body">
												<label><b>समस्या </b></label>
												<textarea name="issue" class="form-control" rows="2"> </textarea>
											</div>
										</div>
										-->                     
										<div class="col-md-12 mt-2">
											<button type="submit" class="btn btn-dark mr-1 mb-1 w-100">Submit</button>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>

			</section>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
		</div>
	</div>

<style type="text/css">
	.input {
		position: relative;
		bottom: -3px;
	}
</style>
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
	function getissue(id){
		if(id==1){
			$('#employee').show();
		}else{
			$('#employee').hide();
		}
	}
	
</script>
@include('layouts.notification')
</body>
</html>