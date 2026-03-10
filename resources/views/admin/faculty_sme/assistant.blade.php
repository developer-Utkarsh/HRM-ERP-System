@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-6">
						<h2 class="content-header-title float-left mb-0">Assistant Request</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-6 text-right">
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
								<form action="" method="post" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-4">
											<label for="users-list-role">Request ID</label>
											<fieldset class="form-group">
												<input type="text" class="form-control request_id" name="request_id" placeholder="Request ID" value="{{ app('request')->input('request_id') }}">
											</fieldset>
											
											<div class="pdf_text"></div>
										</div>
										<div class="col-12 col-sm-6 col-lg-4">
											<label for="users-list-role">Today Class</label>
											<fieldset class="form-group">
												<select class="form-control class_id select-multiple" name="class_id">
													<option value="">Select</option>
													<option value="1">Other</option>
													@foreach ($today_class as $tc)
														<option value="{{ $tc->id }}">{{ $tc->name }} ({{ $tc->from_time }} - {{ $tc->to_time }})</option>
													@endforeach
												</select>
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-4">
											<label for="users-list-role">&nbsp;</label>
											<fieldset class="form-group" style="float:right;">		
											<button type="button" class="get_pdf btn btn-primary">Submit</button>
											<a href="{{ route('admin.faculty-sme.faculty-sme-assistant') }}" class="btn btn-warning">Back</a>
											</fieldset>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

				<div class="table-responsive">
					<table class="table data-list-view" id="">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Request ID</th>
								<th>File</th>
								<th>Class</th>
								<th>Used Date</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								if(count($record) > 0){
								$i = 1;
								foreach($record as $re){
							?>
							<tr>
								<td>{{ $i++ }}</td>
								<td>{{ $re->request_id }}</td>
								<td><a href="{{ asset('laravel/public/faculty_sme/'.$re->file) }}">Download</a></td>
								<td>
									<?php 
										if($re->timatable_id==1){ 
											echo 'Other'; 
										}else{ 
											echo $re->name.'('.$re->from_time.' - '.$re->to_time.')'; 
										}
									?>
								</td>
								<td>{{ date('d-m-Y h:i:s',strtotime($re->created_at)) }}</td>
							</tr>
							<?php } }else{?>
							<tr>
								<td colspan="5" class="text-center">No Record Found</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>					
				</div>                   
			</section>
		</div>
	</div>
</div>

@endsection

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

<script src="{{ asset('laravel/public/admin/js/jquery.validate.min.js') }}"></script>
<script type="text/javascript">	
	$(document).ready(function() {
		$('.select-multiple').select2({
			placeholder: "Select",
			allowClear: true
		});
	});
	
	
	function getStatus(val){
		if(val==2){
			$('.rejectFiled').show();
			$('.rejectFiled textarea').prop('required',true);
		}else{
			$('.rejectFiled').hide();
			$('.rejectFiled textarea').prop('required',false);
		}
	}
	
	$(document).on("click", ".status-model", function() {
		let dataId = $(this).attr("data-id"); 
		$(".pop_rid").val(dataId); 
		$("#exampleModalLong").modal("show"); 
	});

	
	$(document).on("click",".get_pdf",function(){
		var request_id = $('.request_id').val();
		var class_id = $('.class_id').val();
		
		if(request_id){
			$("#overlay_loader").css('display','block');
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},      
				type: "POST",
				url : '{{ route('admin.faculty-sme.faculty-sme-download-pdf') }}',
				data : {'request_id': request_id,'class_id' : class_id},
				dataType : 'json',			
				success : function(data){
					$("#overlay_loader").css('display','none');
					$('pdf_text').html('');
					$('pdf_text').text('');
					if(data.status == false){
						$(".pdf_text").text(data.msg);
					}
					else if(data.status == true){					
						$(".pdf_text").html(data.html);
					}
				}
			});   
		}
	})
</script>
@endsection
