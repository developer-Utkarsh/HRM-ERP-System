@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-9">
						<h2 class="content-header-title float-left mb-0">Anuprati Dashboard</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
								
							</ol>
							
						</div>
					</div>
					<div class="col-3 text-right">
						
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ route('admin.anuprati-dashboard') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-verified">Reg. No.</label>
											<fieldset class="form-group">
												<input type="text" name="reg_no" class="form-control reg_no" value="{{ app('request')->input('reg_no') }}" id="">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Location</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple branch_location" name="branch_location" onchange="locationBranch(this.value);" required>
													@php $branch_location = ['Jodhpur', 'Jaipur','Prayagraj','Indore','Delhi']; @endphp
													<option value="">Select Any</option>
													@foreach($branch_location as $key => $value)
													<option value="{{ $value }}" @if($value == app('request')->input('branch_location')) selected="selected" @endif>{{ $value }}</option>
													@endforeach
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Branch</label>
											<?php
											$branch_location = app('request')->input('branch_location');
											$branches = \App\Branch::where('status', '1'); 
											if(!empty($branch_location)){
												$branches->where('branch_location', $branch_location);
											}
											$branches = $branches->orderBy('id','desc')->get();											
											?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple branch_id" name="branch_id" onchange="locationBatch(this.value);">
													<option value="">Select Any</option>
													@foreach($branches as $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('branch_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
												</select>												
											</fieldset>
											<input type="hidden" name="allbatch_ids" class="allbatch_ids"/>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Batch</label>
											<?php
											$ttdate=date('Y-m-d',strtotime(date('Y-m-d').' -30 day'));
											$batch = DB::table('batch')
												->select('batch.id','batch.name','batch.batch_code','tt.branch_id')
												->leftjoin('timetables as tt','tt.batch_id','batch.id')
												->where('batch.status', '1')
												->where('tt.is_deleted', '0')
												->where('tt.is_publish', '1')
												->where('tt.is_cancel', 0)
												->where('tt.cdate','>',$ttdate)
												->where('batch.batch_code','!=',0);
												if(!empty(app('request')->input('branch_id'))){
													$batch->where('tt.branch_id',app('request')->input('branch_id'));
												}
											$batch= $batch->groupby('batch.batch_code')->get();
											?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple batch_id" name="batch_id">
													<option value="">Select Any</option>
													@foreach($batch as $value)
													<option value="{{ $value->batch_code }}" @if($value->batch_code == app('request')->input('batch_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
												</select>												
											</fieldset>
										</div>										
										<div class="col-lg-12 text-right">
											<fieldset class="form-group mb-0">		
												<button type="submit" class="btn btn-primary" name="search" value="search">Search</button>
												<a href="{{ route('admin.anuprati-dashboard') }}" class="btn btn-warning">Reset</a>
												<a href="javascript:void(0)" id="download_report" class="btn btn-primary">Export</a>
											</fieldset>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table class="table data-list-view" id="my-table-id">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Reg. No.</th>
								<th>Student</th>
								<th>Category</th>
								<th>Batch Code</th>
								<th>Batch Name</th>
								<th>Percentage</th>								
							</tr>
						</thead>
						<tbody>
							@if(count($student) > 0)
							@php $i = 1; @endphp
							@foreach($student as $key => $st)	
								@php
									$custom_where = "";
									if(Auth::user()->id!=901){
										$custom_where = " AND user_id != 901";
									}
									
									
								    $query = 'SELECT  count(DISTINCT DATE_FORMAT(date, "%Y-%m-%d"))  as total_present FROM `student_attendance` where batch_id='.$st->batch_id.' AND reg_no='.$st->reg_number.' '.$custom_where.' group BY reg_no';
									$persent = DB::select($query);
									
								
									$reg_date	=	date('Y-m-d', strtotime($st->reg_date));
									$batch_date	=	date('Y-m-d', strtotime($st->batch_date));
									
									if($reg_date > $batch_date){
										$batch_date = $reg_date;
									}
									
									$query = 'SELECT  batch_id,count(DISTINCT DATE_FORMAT(date, "%Y-%m-%d"))  as total_present FROM `student_attendance` where batch_id='.$st->batch_id.' '.$custom_where.' group BY batch_id';
									$total = DB::select($query);
									
									if(count($total) > 0 && count($persent) > 0){
										$total=$total[0]->total_present;
										$persent=$persent[0]->total_present;
										$percent = ceil(($persent*100)/$total);
									}else{
										$percent = 0;
									}
								@endphp
							<tr>
								<td>{{ $pageNumber++ }}</td>
								<td>{{ $st->reg_number }}</td>
								<td>{{ $st->s_name }}</td>
								<td>{{ $st->cast }}</td>
								<td>{{ $st->batch_id }}</td>
								<td>{{ $st->batch }}</td>
								<td>{{ $percent }}%</td>
							</tr>
							@endforeach
							
							@else 
							<tr>
								<td colspan="6" class="text-center">No Record Found</td>
							</tr>
							@endif
						</tbody>
					</table>
					<div class="d-flex justify-content-center">					
					{!! $student->appends($params)->links() !!}
					</div>
				</div>                   
			</section>
		</div>
	</div>
</div>

<div id="overlay_loader">
	<div>
		<span>Please Wait.. Request Is In Processing.</span><br>
		<i class="fa fa-refresh fa-spin fa-5x"></i>
	</div>
</div>


<style>
#overlay_loader {
    position: fixed;
	display: none;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.8);
    z-index: 10000;
    cursor: pointer;
}
#overlay_loader div {
    position: absolute;
    top: 50%;
    left: 50%;
    font-size: 40px;
    text-align: center;
    color: white;
    transform: translate(-50%, -50%);
    -ms-transform: translate(-50%, -50%);
    width: 100%;
}
</style>
@endsection

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
	
	
	function locationBranch(value){
		$("#overlay_loader").css('display','block');
		$.ajax({
			type : 'POST',
			url : '{{ route('admin.employee.get-branch') }}',
			data : {'_token' : '{{ csrf_token() }}', 'branch_id': value},
			dataType : 'html',
			success : function (data){
				$('.branch_id').empty();
				$('.branch_id').append(data);
				$("#overlay_loader").css('display','none');
			}
		});
	}
	
	function locationBatch(value){
		$("#overlay_loader").css('display','block');
		$.ajax({
			type : 'POST',
			url : '{{ route('admin.get-batch') }}',
			data : {'_token' : '{{ csrf_token() }}', 'branch_id': value},
			dataType : 'json',
			success : function (data){
				if(data.status){
					$('.batch_id').empty();
					$('.batch_id').append(data.batches);
					$('.allbatch_ids').val(data.allbatch_ids);
					$("#overlay_loader").css('display','none');
				}
			}
		});
	}
	
	
	$("body").on("click", "#download_report", function (e) {
		var data = {};					
		data.reg_no 		 = $('.reg_no').val(),
		data.branch_location = $('.branch_location').val(),
		data.branch_id  	 = $('.branch_id').val(),
		data.allbatch_ids  	 = $('.allbatch_ids').val(),
		data.batch_id  		 = $('.batch_id').val(),
		window.location.href = "<?php echo URL::to('/admin/'); ?>/anuprati-report-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});	
</script>
@endsection
