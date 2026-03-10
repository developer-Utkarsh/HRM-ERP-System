@extends('layouts.studiomanager')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-9">
						<h2 class="content-header-title float-left mb-0">Student Attendance Dashboard</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('studiomanager.dashboard') }}">Home</a>
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
								<form action="{{ route('studiomanager.attendance-dashboard') }}" method="get" name="filtersubmit">
									<div class="col-12 text-right"> 
										<a  href="javascript:void(0);" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#stdwAttendance">Student Wise Attendance</a>
									</div>
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Location</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple branch_location" name="branch_location" onchange="locationBranch(this.value);">
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
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-verified">From</label>
											<fieldset class="form-group">
												<input type="date" name="fdate" class="form-control StartDateClass fdate" value="{{ app('request')->input('fdate') }}" id="">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-verified">To</label>
											<fieldset class="form-group">
												<input type="date" name="tdate" class="form-control EndDateClass tdate" value="{{ app('request')->input('tdate') }}" id="">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-9">
										</div>
										<div class="col-lg-12 text-right">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
												<button type="submit" class="btn btn-primary" name="search" value="search">Search</button>
												<a href="{{ route('studiomanager.attendance-dashboard') }}" class="btn btn-warning">Reset</a>
												<button onClick="ExportToExcel()" class="btn btn-primary">Excel</button>
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
								<th>Batch Name</th>
								<th>Total Student</th>
								<th>Male</th>
								<th>Female</th>
								<th>Persent</th>
								<th>Absent</th>
								<th>Date</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								if(count($query) > 0){
								$i = 1;
									foreach($query as $key => $value){										
										$checkPersent = DB::table('student_attendance')->select(DB::raw("count(id) as persentstudent"),DB::raw('Date(date) as pdate'))->where('batch_id', $value->batch_id) ->groupBy(DB::raw('Date(date)'));
										
										if (!empty($fdate) && !empty($tdate)) {  
											$checkPersent->whereRaw(DB::raw("DATE(date) >= '".$fdate."' AND DATE(date) <= '".$tdate."'"));
										}else{
											$checkPersent->whereRaw(DB::raw("DATE(date) = '".date('Y-m-d')."'"));
										}
										
										$persent	  = $checkPersent->get();
										
										$total_female = $value->total_admission - $value->total_male;
										foreach($persent as $key => $pvalue){
											$absent = $value->total_admission - $pvalue->persentstudent;
										
							?>
							<tr>
								<td>{{ $i }}</td>
								<td>{{ $value->batch_name }}</td>
								<td>{{ $value->total_admission }} </td>
								<td>{{ $value->total_male }} </td>
								<td>{{ $total_female }} </td>
								<td><a href="{{ route('studiomanager.student-attendance-view',[$value->batch_id,$pvalue->pdate,1]) }}" title="Click Here"><?=$pvalue->persentstudent;?></a></td>
								<td><a href="{{ route('studiomanager.student-attendance-view',[$value->batch_id,$pvalue->pdate,2]) }}" title="Click Here"><?=$absent;?></a></td>
								<td>{{ date('d-m-Y', strtotime($pvalue->pdate)) }}</td>
							</tr>
							<?php 	
								$i++; 
										}
									}								
								}else{
							?>
							<tr>
								<td colspan="8" class="text-center">No Attendance Found</td>
							</tr>	
							<?php } ?>
						</tbody>
					</table>
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

<div class="modal" id="stdwAttendance" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Student Attendance Search</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

    <form method="post" action="javascript:void(0)" id="studentSearch">
        {{ csrf_field() }}
	    <div class="modal-body" style="height:100%;">
				<div class="row">
					<div class="col-md-6 col-12">
						<div class="form-group">
							<label for="first-name-column">Reg No. *</label>
							<input type="number" class="form-control" placeholder="Reg No." name="reg_no" required>
						</div>
					</div>
					<div class="col-md-6 col-12">
						<div class="form-group">
							<label for="first-name-column">Select Month. *</label>
							<input type="month" class="form-control year_wise_month" placeholder="Month" name="month" required>
						</div>
					</div>
					<div class="col-md-12  text-center">
					  <strong class="please_wait text-danger"></strong>
			      <button type="submit" class="btn btn-primary btn_submit">Submit</button>
			    </div>
			  </div>

		    <div class="stSData row">
		    	<div class="col-md-6">
		    		<h4>Student Details</h4>
		    		<table class="table table-responsive">
		    			<tr>
			    			<th>Name</th>
			    			<td></td>
		    			</tr>
		    			<tr>
			    			<th>Father Name</th>
			    			<td></td>
		    			</tr>
		    			<tr>
			    			<th>Batch</th>
			    			<td></td>
		    			</tr>
		    			<tr>
			    			<th>Duedate</th>
			    			<td></td>
		    			</tr>
		    		</table>
		    	</div>
		    	<div class="col-md-6">
		    		<h4>Inventory Details</h4>
		    		<table class="table table-responsive">
		    			<tr>
			    			<th>Inventory Name</th>
			    			<th>Assgined Status</th>
		    			</tr>
		    			<tr>
			    			<td></td>
			    			<td></td>
		    			</tr>
		    		</table>
		    	</div>

		    	<div class="col-md-12">
		    		<h4>Attendance Details</h4>
		    		<table class="table table-responsive">
		    			<tr>
			    			<th>Date</th>
			    			<th>Attendance Time</th>
			    			<td>Status</td>
		    			</tr>
		    		</table>
		    	</div>
		    </div>

				</div>
	    </div>
    </form>
    </div>
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

.table thead th {
	font-size:16px !important;
}

.table tbody td{
	font-size:16px !important;
}

</style>
@endsection

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
	function locationBranch(value){
		$("#overlay_loader").css('display','block');
		$.ajax({
			type : 'POST',
			url : '{{ route('studiomanager.employee.get-branch') }}',
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
			url : '{{ route('studiomanager.get-batch') }}',
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
	
	
	$(document).ready(function() {
		$('.select-multiple').select2({
			placeholder: "Select Any",
			allowClear: true
		});

		$("#studentSearch").submit(function(e) {
			var reg_no=$(this).find("input[name='reg_no']").val();
			var month=$(this).find("input[name='month']").val();
		//	$("#overlay_loader").css('display','block');
			e.preventDefault(); 
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},      
				type: "POST",
				url : '{{ route('studiomanager.batchinventory.studentSearch') }}',
				data : {'reg_no':reg_no,'month':month},
				dataType : 'json',
				success : function(data){
					$("#overlay_loader").css('display','none');
					if(data.status==true){
						var st=data.data.student;
						var atd=data.data.st_attendance;
						var invt=data.data.st_inventory;
						alert(data.message);
						var stSData=`
							<div class="col-md-6">
			    		<h4>Student Details</h4>
			    		<table class="table table-responsive">
			    			<tr>
				    			<th>Name</th>
				    			<td>`+st.s_name+`</td>
			    			</tr>
			    			<tr>
				    			<th>Father Name</th>
				    			<td>`+st.f_name+`</td>
			    			</tr>
			    			<tr>
				    			<th>Batch</th>
				    			<td>`+st.batch+`</td>
			    			</tr>
			    			<tr>
				    			<th>Duedate</th>
				    			<td>`+st.duedate+`</td>
			    			</tr>
			    		</table><br>
			    		
			    		<h4>Inventory Details</h4>
			    		<table class="table table-responsive">
			    			<tr>
				    			<th>Inventory Name</th>
				    			<th>Assgined Status</th>
			    			</tr>`;
              for(var i = 0; i<invt.length; i++) {
              	stSData+=`<tr>
				    			<td>`+invt[i]['name']+`</td>
				    			<td>`+invt[i]['is_assgined']+`</td>
			    			</tr>`;
			    		}
			    	  stSData+=`</table>
			    	</div>

			    	<div class="col-md-6">
			    		<h4>Attendance Details</h4>
			    		<table class="table table-responsive">
			    			<tr>
				    			<th>Date</th>
				    			<th>Attendance Time</th>
				    			<td>Status</td>
			    			</tr>`;
			    		for(var i=0; i<atd.length; i++) {
              	stSData+=`<tr>
				    			<td>`+atd[i]['pdate']+`</td>
				    			<td>`+atd[i]['date']+`</td>
				    			<td>`+atd[i]['status']+`</td>
			    			</tr>`;
			    		}

			    	stSData+=`</table>
			    	</div>`;
						$(".stSData").html(stSData);
					}else if(data.status == false){
						alert(data.message);
					}
				}
			});   
		});
	});
	
	function ExportToExcel(){
	   var htmltable= document.getElementById('my-table-id');
	   var html = htmltable.outerHTML;
	   window.open('data:application/vnd.ms-excel,' + encodeURIComponent(html));
	}
</script>
@endsection
