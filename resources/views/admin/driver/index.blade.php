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
						<h2 class="content-header-title float-left mb-0">Drivers List</h2>
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
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ route('admin.drivers.index') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-md-4">
											<label for="users-list-role">Search</label>
											<fieldset class="form-group">
												<input type="text" class="form-control search" name="search" placeholder="Ex:Name, Email, Mobile, Employee Code" value="{{ app('request')->input('search') }}" id="myInputSearch" onkeyup="myFunctionSearch()">
											</fieldset>
										</div>
										<div class="col-md-3">
											<label for="users-list-status">Branch</label>
											<?php $branches = \App\Branch::where('status', '1')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 branch_id" name="branch_id">
													<option value="">Select Any</option>
													@if(count($branches) > 0)
													@foreach($branches as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('branch_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										    
										<div class="col-md-5">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('admin.drivers.index') }}" class="btn btn-warning">Reset</a>
											</fieldset>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table class="table data-list-view" id="TableSearch">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Name</th>
								<th>Mobile</th>
								<th>Branch</th>
								<!--th>Employee</th>
								<th>Email</th-->
								<th>Assign Faculties</th-->
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							@foreach($drivers as  $key => $value)
							<?php
							//echo "<pre>";print_r($value->user_branches); die;
							$department_id = $value->department_type;
							$department = \App\Department::where('id', $department_id)->first();
							?>
							<tr>
								<td>{{ $pageNumber++ }}</td>
								<td>{{ $value->name }} <span style="display:none;"> {{$value->register_id}} {{$value->email}} {{$value->email}}</span></td>
								<td class="product-price">{{ $value->mobile }}</td>
								<td>
								<?php
								$branch_names = "";
								if(isset($value->user_branches) && !empty($value->user_branches)){
									foreach($value->user_branches as $key => $val) { 
										if(!empty($val->branch->name)) {
											$branch_names .= $val->branch->name .", ";
										}
									}
								}
								echo rtrim($branch_names, ", "); 
								?>
								</td>
								
								<!--td class="product-name">
									<a href="" class="btn btn-sm btn-primary">{{ $value->register_id }}
									</a>
								</td>
								<td>{{ $value->email }}</td-->
								<td style="word-wrap:break-word; width:250px;">
								<?php
								$faculty_name ="";
								$get_data = DB::table('driver_faculties')->where('driver_id',$value->id)->first();
								if(!empty($get_data)){
									$faculties_id = $get_data->faculty_ids;
									if(!empty($faculties_id)){
										$faculties_id = json_decode($faculties_id);
										if(!empty($faculties_id)){
											foreach($faculties_id as $val){
												$faculty_detail = DB::table('users')->where('id',$val)->first();
												if(!empty($faculty_detail)){
													$faculty_name .= $faculty_detail->name. "( ".$faculty_detail->mobile." ) , ";
												}
											}
											$faculty_name = rtrim($faculty_name,', ');
										}
									}
								}
								echo $faculty_name;
								?>
								</td>
								
								
								<td class="product-action">
									
									<a href="{{ route('admin.drivers.assign', $value->id) }}" class="btn btn-sm btn-success">
										Assign Faculties
									</a>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
					<div class="d-flex justify-content-center">					
					{!! $drivers->appends($params)->links() !!}
					</div>
				</div>                   
			</section>
		</div>
	</div>
</div>


<div id="edit-sts" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title">Reason</h5>
		</div>
		<form action="{{route('admin.employee.status-by-reason')}}" method="post" id="submit_user_status_form" class="online-form">
		@csrf
		<div class="modal-body">
			<div class="form-body">
				<div class="row pt-2">
					<div class="col-md-12 col-12">
						<div class="form-label-group">
							<textarea name="reason" placeholder="Reason" class="form-control remark" required></textarea>
						</div>
					</div>
					
					<div class="col-md-12 col-12">
						<div class="form-label-group">
							<input type="date" name="reason_date" class="form-control" required>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<input type="hidden" name="p_id" id="p_id" value="">
			<input type="hidden" name="sts" id="sts" value="">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
			<button type="submit" id="timetable_online_btn" class="btn btn-primary onlinedsabl">Submit <i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i></button>
		</div>
		</form>
		
		</div>
	</div>
</div>
				
@endsection

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">

	$(".sts-data").on("click", function() { 
		var primary_id = $(this).parent('#lnk').attr("data-id"); 
		var lnk_status =  $(this).parent('#lnk').attr("data-status"); 
		$('#p_id').val(primary_id);
		$('#sts').val(lnk_status);
		$('#edit-sts').modal({
				backdrop: 'static',
				keyboard: true, 
				show: true
		});
	}); 

						
	$(document).ready(function() {
		$('.select-multiple1').select2({
			placeholder: "Select Any",
			allowClear: true
		});
		$('.select-multiple2,.select-multiple3').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
	
	$(document).ready(function() {
		$('#example').DataTable();
	});

</script>

<script>
$("body").on("click", "#download_excel", function (e) {
	var data = {};
		data.branch_id = $('.branch_id').val(),
		data.search    = $('.search').val(),
		data.role_id   = $('.role_id').val(),
		data.status    = $('.status').val(), 
	window.location.href = "<?php echo URL::to('/admin/'); ?>/employee-report-excel?" + Object.keys(data).map(function (k) {
		return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
	}).join('&');
});

function myFunctionSearch() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInputSearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("TableSearch");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}
</script>
@endsection
