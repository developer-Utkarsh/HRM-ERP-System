@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Assigned Assistants</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
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
								<form action="{{ route('admin.assigned-assistants') }}" method="get" name="filtersubmit">
									<div class="row">
									
										<div class="col-12 col-md-3 branch_loader">
											<label for="users-list-status">Branch</label>
											<?php $branches = \App\Branch::where('status', '1')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 select_branch_id" name="branch_id" id="">
													<option value="">Select Any</option>
													@if(count($branches) > 0)
													@foreach($branches as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('branch_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>
												<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
											</fieldset>
										</div>
										
										<div class="col-12 col-md-3">
											<label for="users-list-status">Studio</label>
											<?php $studios = \App\Studio::where('status', '1');
											if(app('request')->input('branch_id')){
												$studios->where('branch_id',app('request')->input('branch_id'));
											}
											$studios = $studios->orderBy('id','desc')->get();
											?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2 select_studio_id" name="studio_id" id="">
													<option value="">Select Any</option>
													@if(count($studios) > 0)
													@foreach($studios as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('studio_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										
										<div class="col-12 col-md-3">
											<label for="users-list-status">Assistant</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple3 select_assistant_id_" name="assistant_id">
													<option value="">Select Any</option>
													@if(count($get_assistant) > 0)
													@foreach($get_assistant as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('assistant_id')) selected="selected" @endif>{{ $value->name }} ({{ $value->register_id }})</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-md-3">
											<label for="users-list-status">Date</label>
											<fieldset class="form-group">												
												<input type="date" name="fdate" placeholder="Date" value="{{ (app('request')->input('fdate'))?app('request')->input('fdate') :date('Y-m-d') }}" class="form-control StartDateClass fdate">
											</fieldset>
										</div>
									</div>
									
									<fieldset class="form-group" style="float:right;">		
									<button type="submit" class="btn btn-primary">Search</button>
									<a href="{{ route('admin.assigned-assistants') }}" class="btn btn-warning">Reset</a>
									<a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a>
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
							<th>Branch Name</th>
							<th>Studio Name</th>
							<th>Assistant Name</th>
							
						</tr>
					</thead>
					<tbody>
				<?php 
				$dataFound = 1;
				if (count($get_data) > 0) { 
					foreach ($get_data as $AssistantArray) { //echo '<pre>'; print_r($FacultyArray); die;
								
						?>
							<tr style="">
							<td><?=$dataFound?></td>
							<td><?php 
							if(!empty($AssistantArray->branch_name)){
								echo $AssistantArray->branch_name; 
							} ?>
							</td>
							<td><?php 
							if(!empty($AssistantArray->studio_name)){
								echo $AssistantArray->studio_name; 
							} ?>
							</td>
							<td>
							<form method="post" class="assistant_form">
								<input type="hidden" class="branch_id" value="<?=$AssistantArray->branch_id?>" />
								<input type="hidden" class="studio_id" value="<?=$AssistantArray->studio_id?>" />
								<select class="assistant_id select-multiple4" >
									<option value="">Select</option>
									<?php
										foreach($get_assistant as $assistant_val){
										?>
											<option value="<?=$assistant_val->id?>" <?=($assistant_val->id==$AssistantArray->assistant_id)?'selected':'';?>> <?=$assistant_val->name?> (<?=$assistant_val->register_id?>) </option>
										<?php
										}
									?>
								</select>
								<?php 
								if(!empty($AssistantArray->assistant_name)){
									//echo $AssistantArray->assistant_name; 
								}
								?>
								<?php
								if(empty(app('request')->input('fdate')) || strtotime(app('request')->input('fdate')) >= strtotime(date('Y-m-d'))){
									?>
										<button name="" class="change_assistant btn btn-success" value="" >Submit </button>
									<?php
								}
								?>
							</form>
							
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
@endsection

@section('scripts')
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
	$('.select-multiple4').select2({
		placeholder: "Select Any",
		allowClear: true
	});
});

$(".select_branch_id").on("change", function () {
		var branch_id = $(".select_branch_id option:selected").attr('value');
		var assistant_id = $("input[name=assistant_id]").val();
		if (branch_id) {
			$.ajax({
				beforeSend: function(){
					// $(".branch_loader i").show();
				},
				type : 'POST',
				url : '{{ route('admin.get-branchwise-studio') }}',
				data : {'_token' : '{{ csrf_token() }}', 'branch_id': branch_id},
				dataType : 'html',
				success : function (data){
					// $(".branch_loader i").hide();
					$('.select_studio_id').empty();
					$('.select_studio_id').append(data);
				}
			});			
			
		}
	});
	
$(".assistant_id").on("change",function(e) {
	$(this).siblings('.change_assistant').show();
})
$(".change_assistant").on("click",function(e) {
		e.preventDefault();
		if (!confirm("Do you want change assistant")){
		  return false;
		}
		var fdate = $('.fdate').val();
		var assistant_id = $(this).siblings('.assistant_id').val();
		var studio_id = $(this).siblings('.studio_id').val();
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},      
			type: "POST",
			url : '{{ route('admin.assigned_assistant_update') }}',
			data : {'studio_id':studio_id,'assistant_id':assistant_id,'fdate':fdate},
			success : function(data){
				if(data.status == false){
					swal("Error!", data.message, "error");
				} else if(data.status == true){					
					swal("Done!", data.message, "success").then(function(){ 
						//location.reload();
					});
				}
			}
		});
	});
	
$("body").on("click", "#download_excel", function (e) {
	var data = {};
	data.branch_id = $('.select_branch_id').val(),
	data.studio_id = $('.select_studio_id').val(),
	data.assistant_id_ = $('.select_assistant_id_').val(),
	data.fdate = $('.fdate').val(),
	window.location.href = "<?php echo URL::to('/admin/'); ?>/assigned-assistants-excel?" + Object.keys(data).map(function (k) {
		return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
	}).join('&');
});		
</script>

@endsection
