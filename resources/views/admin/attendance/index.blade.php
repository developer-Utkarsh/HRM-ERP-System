@extends('layouts.admin')
@section('content')
<style>
#attendanceTable tbody tr td:nth-child(7){display:none !important;}
</style>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">App Attendance</h2>
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
								<form action="{{ route('admin.attendance.index') }}" method="get" name="filtersubmit">
									<div class="row">
									    @if(Auth::user()->role_id != 20)
										<div class="col-md-3">
											<label for="users-list-role">Employee Name</label>
											<fieldset class="form-group">
												<input type="text" class="form-control name" name="name" id="se_name" placeholder="Name, EMP Code" value="{{ app('request')->input('name') }}" id="myInputSearch" onkeyup="myFunctionSearch()">
											</fieldset>
										</div>
										@endif
										@if(Auth::user()->role_id == 24 || Auth::user()->role_id == 29 || Auth::user()->role_id == 21)
										<div class="col-md-3">
											<div class="form-group">
												<label for="first-name-column">Branch</label>
												@if(count($allBranches) > 0)
												<select class="form-control get_role select-multiple1 branch_id" name="branch_id" id="se_branch_id">
													<option value=""> - Select Any - </option>
													@foreach($allBranches as $value)
													<option value="{{ $value['id'] }}" @if($value['id'] == app('request')->input('branch_id')) selected="selected" @endif>{{ $value['name'] }}</option>
													@endforeach
												</select>
												@endif
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label for="first-name-column">Department Type</label>
												@if(count($allDepartmentTypes) > 0)
												<select class="form-control get_role select-multiple1 department_type" name="department_type" id="se_department_type">
													<option value=""> - Select Any - </option>
													@foreach($allDepartmentTypes as $value)
													<option value="{{ $value['id'] }}" @if($value['id'] == app('request')->input('department_type')) selected="selected" @endif>{{ $value['name'] }}</option>
													@endforeach
												</select>
												@endif
											</div>
										</div>
										
										@endif
										<div class="col-md-2">
											<label for="users-list-verified">From</label>
											<fieldset class="form-group">
												<input type="date" name="fdate" class="form-control StartDateClass fdate" value="{{ app('request')->input('fdate') }}"  id="se_fdate">
											</fieldset>
										</div>
										<div class="col-md-2">
											<label for="users-list-verified">To</label>
											<fieldset class="form-group">
												<input type="date" name="tdate" class="form-control EndDateClass tdate" value="{{ app('request')->input('tdate') }}" id="se_tdate">
											</fieldset>
										</div>
									</div>
									<fieldset class="form-group" style="float:right;">		
									<button type="submit" class="btn btn-primary">Search</button>
									<a href="{{ route('admin.attendance.index') }}" class="btn btn-warning">Reset</a>
									<a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a>
									</fieldset>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table class="table data-list-view" style="background:#fff;width:100%" id="attendanceTable">
						<thead style="text-align: ;">
							<tr>
								<th>S. No.</th>
								<th>Employee Name</th>
								<th>Branch</th>
								<th>Department Type</th>
								<th>Date</th>
								<th>In Time</th>
								<th>Out Time</th>
								<th>Total Duration</th>
								<?php if(Auth::user()->role_id == 24 || Auth::user()->role_id == 29 || Auth::user()->role_id == 21){ ?>
								<th>Action</th>
								<?php } ?>
							</tr>
						</thead>
						<tbody >
						
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<?php 
$current_date = date('j');
$current_date = $current_date -1;
 ?>
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
	
	$('#example').DataTable();
});

$("body").on("click", "#download_excel", function (e) {

	var data = {};
	<?php if(Auth::user()->role_id != 20){ ?>
		data.name = $('.name').val(),
	<?php } ?>
	
	<?php if(Auth::user()->role_id == 24 || Auth::user()->role_id == 29){ ?>
		data.branch_id = $('.branch_id').val(),
		data.department_type = $('.department_type').val(),
	<?php } ?>	
		data.fdate = $('.fdate').val(),
		data.tdate = $('.tdate').val(),
		 
	window.location.href = "<?php echo URL::to('/admin/'); ?>/attendance-report-excel?" + Object.keys(data).map(function (k) {
		return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
	}).join('&');
});


$.fn.serializeObject = function() {
	var o = {};
	var a = this.serializeArray();
	$.each(a, function() {
		if (o[this.name]) {
			if (!o[this.name].push) {
				o[this.name] = [o[this.name]];
			}
			o[this.name].push(this.value || '');
		} else {
			o[this.name] = this.value || '';
		}
	});
	return o;
};

var roleid = {{Auth::user()->id}};
	
 $(document).ready(function () {
        var attendanceTable = $('#attendanceTable').DataTable({
			"searching": false, 
			"info": false,
			"ordering": false,
			"lengthChange": false,
			"pageLength": 50,  
            "processing": true,
            "serverSide": true,
            "ajax":{
		     "url": "{{ route('admin.attendance.attendance-detail') }}",
		     "dataType": "json",
		     "type": "GET",
			 "data": function(data){
				 Object.assign(data, $('[name="filtersubmit"]').serializeObject());
				 return data;
			 },
		    },
			preDrawCallback: function(settings) {
				if ($.fn.DataTable.isDataTable('#attendanceTable')) {
					var dt = $('#attendanceTable').DataTable();

					//Abort previous ajax request if it is still in process.
					var settings = dt.settings();
					if (settings[0].jqXHR) {
						settings[0].jqXHR.abort();
					}
				}
			},
			"createdRow": function(row, data, dataIndex){
				$('td:eq(5)', row).attr('colspan', 2);
				// $('td:eq(4)', row).remove();
			},
	    	"columns": [
		          { "data": null, orderable: false, render: function(data, type, row, meta){
					  return meta.row + meta.settings._iDisplayStart + 1;
				  } },
		          { "data": "name" },
				  { "data": "branch" },
				  { "data": "department" },
		          { "data": "date" },
		          { "data": null, render:function(data){
					  var timeHtml = '<table class="table data-list-view" style="background: #f7f7f73d;">';
					  if(data.time.length > 0){
						  $.each(data.time, function(i,v){
							  timeHtml +='<tr><td style="width:200px;">';
							  timeHtml +=`${v.in_time}`;
							  if(v.in_location){
								  timeHtml +='&nbsp;&nbsp;<i class="fa fa-map-marker click_location" data-location="'+v.in_location+'" style="cursor: pointer;" title=""'+v.in_location+'></i>';
							  }
							  timeHtml +='</td><td>';
							  timeHtml +=`${v.out_time}`;
							  if(v.out_location){
								timeHtml += '&nbsp;&nbsp;<i class="fa fa-map-marker click_location" data-location="'+v.out_location+'" style="cursor: pointer;" title=""'+v.out_location+'></i>';
							  }
							  timeHtml += '</td></tr>';
						  });
					  }
					  timeHtml += "</table>";
					  return timeHtml;
				  } },
				  { "data": null },
				  { "data": "total_hours" },
				  <?php 
				  /* if(Auth::user()->role_id == 24 || Auth::user()->role_id == 29 || Auth::user()->id == 1522 || Auth::user()->id == 1216 || Auth::user()->id == 1050 || Auth::user()->id == 1117 || Auth::user()->id == 1215 || Auth::user()->id == 1812 || Auth::user()->id == 1732 || Auth::user()->id == 5552 || Auth::user()->id == 1912 || Auth::user()->id == 1004){ */
				if(Auth::user()->role_id == 24 || Auth::user()->role_id == 29){
				  ?>
				  { "data": null, render:function(data){ 
					  var route = '<?php echo url('admin/attendance/edit'); ?>'+'/'+data.emp_id+'/'+data.date;
						var actionHtml ='';
						// if(moment(data.date).isSameOrAfter(moment().subtract(<?=$current_date?>, 'days').format("YYYY-MM-DD")) || roleid == 901){			
							actionHtml = '<a href="'+route+'" class=""><span class="action-edit"><i class="feather icon-edit"></i></span></a>';
						// }
					  return actionHtml; 
				  } },
				<?php 
				}
				/*else if(Auth::user()->role_id == 21){
					?>
					{ "data": null, render:function(data){ 
					  var route = '<?php echo url('admin/attendance/edit'); ?>'+'/'+data.emp_id+'/'+data.date;
						var actionHtml ='';
						if(data.date == "<?=date('Y-m-d')?>"){
						//if(moment(data.date).isSameOrAfter(moment().subtract(<?=$current_date?>, 'days').format("YYYY-MM-DD"))){			
							actionHtml = '<a href="'+route+'" class=""><span class="action-edit"><i class="feather icon-edit"></i></span></a>';
						//}
						}
					  return actionHtml; 
				  } },
					<?php
				}*/
				?>
		         
		       ]	 

	    });
		// attendanceTable.column(4).visible(false);
		$("body").on("input change","#se_name, #se_department_type, #se_branch_id, #se_fdate, #se_tdate",function(e){
			e.preventDefault();
			attendanceTable.ajax.reload();
		});
    });
</script>

<script>
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

$(document).on("click",".click_location",function(){
	// alert($(this).attr('data-location'));
	swal($(this).attr('data-location'));
});
</script>
@endsection
