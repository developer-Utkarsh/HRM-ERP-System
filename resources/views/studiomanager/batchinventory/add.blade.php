@extends('layouts.studiomanager')
@section('content')


<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Add Student Inventory</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('studiomanager.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Add Student Inventory</a>
								</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
			<div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
			</div>
		</div>
		
		<div class="content-body">
			<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									
									<form class="form" action="{{ route('studiomanager.batchinventory.save') }}" method="post" enctype="multipart/form-data">
										@csrf
										<div class="form-body">
											<div class="row">
												<div class="col-md-8 col-4">
													<div class="form-group">
														<label for="first-name-column">Type</label>
														 <select class="form-control select-multiple2 type" name="type" required>
															<option value="">Select Type</option>
															<option value="all">All</option>
															<option value="batch">Batch</option>
														</select>
														@if($errors->has('type'))
														<span class="text-danger">{{ $errors->first('type') }} </span>
														@endif
													</div>	
												</div>
												
												<div class="col-md-8 col-4 batch_div" style="display:;">
													<div class="form-group">
														<label for="first-name-column">Batch</label>
														 <select class="form-control select-multiple1 batch_code" name="batch_code">
															<option value="">Select Batch</option>
															<?php
															$url="https://utkarshpublications.com/soft/apis/offlineapp-liveapis/registered-student.php";
															$curl = curl_init($url);
															curl_setopt($curl, CURLOPT_URL, $url);
															curl_setopt($curl, CURLOPT_POST, true);
															curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
															$headers = array(
															   "Content-Type: application/x-www-form-urlencoded",
															);
															curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
															$data="query=running-batches";
															curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
															curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
															curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
															$resp = curl_exec($curl);
															curl_close($curl);

															$batches=json_decode($resp,true);
															if(!empty($batches)){
																foreach($batches['data'] as $key => $value) {
																	echo "<option value='".$value['Bat_id']."&&&".$value['batch_name']."'>".$value['batch_name']."</option>";
																}
															}
															?>
														</select>
														@if($errors->has('batch_code'))
														<span class="text-danger">{{ $errors->first('batch_code') }} </span>
														@endif
													</div>	
												</div>
												<div class="col-md-8 col-12">
													<div class="form-group">
														<label for="first-name-column">Inventory Name</label>
														<input type="text" class="form-control" placeholder="Inventory Name" id="" name="name" value="" required>
														@if($errors->has('name'))
														<span class="text-danger">{{ $errors->first('name') }} </span>
														@endif
													</div>
												</div>
												
												<div class="col-md-8 col-12">
													<div class="form-group">
														<label for="first-name-column">Inventory Quantity</label>
														<input type="number" class="form-control" placeholder="Inventory Quantity" id="" name="quantity" value="" required min="0">
														@if($errors->has('quantity'))
														<span class="text-danger">{{ $errors->first('quantity') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-8 col-4">
													<div class="form-group">
														<label for="first-name-column">Inventory Type</label>
														<select class="form-control inventory_type" name="inventory_type" required>
															<option value="">Select Type</option>
															<option value="Notes">Notes</option>
															<option value="DPP">DPP</option>
															<option value="Other">Other</option>
														</select>
														@if($errors->has('inventory_type'))
														<span class="text-danger">{{ $errors->first('inventory_type') }} </span>
														@endif
													</div>	
												</div>
												
																						
												<div class="col-md-12 mt-2">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Submit</button>
												</div>
											</div>
										</div>
									</form>
									
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

<script type="text/javascript">
		$('.select-multiple1').select2({
			placeholder: "Select Batch",
			allowClear: true
		});
		$('.select-multiple2').select2({
			placeholder: "Select Type",
			allowClear: true
		});
		$(document).on("change",'.type',function(){
			var this_v = $(this).val();
			if(this_v=='batch'){
				$(".batch_div").css('display','block');
				$(".batch_code").attr('required',true);
			}
			else{
				$(".batch_div").css('display','none');
				$(".batch_code").attr('required',false);
			}
		});
</script>
@endsection
