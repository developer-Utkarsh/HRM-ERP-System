
<?php $__env->startSection('content'); ?>
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
						<h2 class="content-header-title float-left mb-0">RFID Attendance</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
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
								<form action="<?php echo e(route('admin.attendance.rpattendance')); ?>" method="get" name="filtersubmit">
									<div class="row">
									    <?php if(Auth::user()->role_id != 20): ?>
										<div class="col-md-3">
											<label for="users-list-role">Employee Name</label>
											<fieldset class="form-group">
												<input type="text" class="form-control name" id="se_name" name="name" placeholder="Name, EMP Code">
											</fieldset>
										</div>
										<?php endif; ?>
										<?php if(Auth::user()->role_id == 24 || Auth::user()->role_id == 29 || Auth::user()->role_id == 21): ?>
										<div class="col-md-3">
											<div class="form-group">
												<label for="first-name-column">Branch</label>
												<?php if(count($allBranches) > 0): ?>
												<select class="form-control get_role select-multiple1 branch_id"  id="se_branch_id" name="branch_id">
													<option value=""> - Select Any - </option>
													<?php $__currentLoopData = $allBranches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value['id']); ?>" <?php if($value['id'] == app('request')->input('branch_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value['name']); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>
												<?php endif; ?>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label for="first-name-column">Department Type</label>
												<?php if(count($allDepartmentTypes) > 0): ?>
												<select class="form-control get_role select-multiple1 department_type" id="se_department_type" name="department_type">
													<option value=""> - Select Any - </option>
													<?php $__currentLoopData = $allDepartmentTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value['id']); ?>" <?php if($value['id'] == app('request')->input('department_type')): ?> selected="selected" <?php endif; ?>><?php echo e($value['name']); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>
												<?php endif; ?>
											</div>
										</div>
										
										<?php endif; ?>
										<div class="col-md-2">
											<label for="users-list-verified">From</label>
											<fieldset class="form-group">
												<input type="date" name="fdate" class="form-control StartDateClass fdate" id="se_fdate" value="<?php echo e(app('request')->input('fdate')); ?>" id="">
											</fieldset>
										</div>
										<div class="col-md-2">
											<label for="users-list-verified">To</label>
											<fieldset class="form-group">
												<input type="date" name="tdate" class="form-control EndDateClass tdate" id="se_tdate" value="<?php echo e(app('request')->input('tdate')); ?>" id="">
											</fieldset>
										</div>
									</div>
									<fieldset class="form-group" style="float:right;">		
									<!--button type="submit" class="btn btn-primary">Search</button-->
								
									<a href="<?php echo e(route('admin.attendance.rpattendance')); ?>" class="btn btn-warning">Reset</a>
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
								<th>Total Duration (Mintues)</th>
								<th>Type</th>
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
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
		//data.department_type = $('.department_type').val(),
	window.location.href = "<?php echo URL::to('/admin/'); ?>/new-attendance-report-excel?" + Object.keys(data).map(function (k) {
		return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
	}).join('&');
});
</script>

<script>
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
		     "url": "<?php echo e(route('admin.attendance.rp-attendance-detail')); ?>",
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
							  timeHtml +=`<tr><td style="width:200px;">${v.in_time}</td><td>${v.out_time}</td></tr>`;
						  });
					  }
					  timeHtml += "</table>";
					  return timeHtml;
				  } },
				  { "data": null },
				  { "data": "total_hours" },
				{ "data": "attendance_type" },
		         
		       ]	 

	    });
		// attendanceTable.column(4).visible(false);
		$("body").on("input change","#se_name, #se_department_type, #se_branch_id, #se_fdate, #se_tdate",function(e){
			e.preventDefault();
			attendanceTable.ajax.reload();
		});
    });
function myFunctionSearch() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInputSearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("attendanceTable");
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/attendance/rpattendence.blade.php ENDPATH**/ ?>