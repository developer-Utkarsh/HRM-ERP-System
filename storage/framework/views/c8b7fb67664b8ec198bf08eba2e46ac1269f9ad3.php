
<?php $__env->startSection('content'); ?>
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
								<?php if(Auth::user()->role_id != 32){ ?>
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a></li>
								<?php } ?>
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
								<form action="<?php echo e(route('admin.attendance-dashboard')); ?>" method="get" name="filtersubmit">
									<?php if(Auth::user()->role_id != 32){ ?>
									<div class="col-12 text-right"> 
										<a  href="javascript:void(0);" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#stdwAttendance">Student Wise Attendance</a>
									</div>
									<?php } ?>
									<div class="row">
										<?php if(Auth::user()->role_id != 32){ ?>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Location</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple branch_location" name="branch_location" onchange="locationBranch(this.value);">
													<?php $branch_location = ['Jodhpur', 'Jaipur','Prayagraj','Indore','Delhi']; ?>
													<option value="">Select Any</option>
													<?php $__currentLoopData = $branch_location; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value); ?>" <?php if($value == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>><?php echo e($value); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
													<?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" <?php if($value->id == app('request')->input('branch_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
												<select class="form-control select-multiple batch_id" name="batch_id[]" multiple>
													<option value="">Select Any</option>
													<?php $__currentLoopData = $batch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->batch_code); ?>" <?php if(in_array($value->batch_code,$batch_id)): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>												
											</fieldset>
										</div>										
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Anuprati</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple anuprati_id" name="anuprati_id" >
													<?php $anuprati = ['Anuprati Yojana','Anupriti Yojna-2022-23','Anupriti Yojna-2023-24','Anupriti Yojna-2023-24 (Ph-2)','Anuprati Yojna-2024-25']; ?>
													<option value="">Select Any</option>
													<?php $__currentLoopData = $anuprati; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value); ?>" <?php if($value == app('request')->input('anuprati_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>												
											</fieldset>
										</div>
										<?php } ?>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-verified">From</label>
											<fieldset class="form-group">
												<input type="date" name="fdate" class="form-control StartDateClass fdate" value="<?php echo e(app('request')->input('fdate')); ?>" id="">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-verified">To</label>
											<fieldset class="form-group">
												<input type="date" name="tdate" class="form-control EndDateClass tdate" value="<?php echo e(app('request')->input('tdate')); ?>" id="">
											</fieldset>
										</div>
										
										<div class="col-lg-8 text-right">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
												<button type="submit" class="btn btn-primary" name="search" value="search">Search</button>
												<a href="<?php echo e(route('admin.attendance-dashboard')); ?>" class="btn btn-warning">Reset</a>
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
								<th>Anuprati</th>
								<th>Male</th>
								<th>Female</th>
								<th>Persent</th>
								<th>Absent</th>
								<th>Date</th>
								<th>Persent %</th>
								<th>Absent %</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							if(count($query) > 0){
								$i = 1;
							
									foreach($query as $key => $value){										
									$checkPersent = DB::table('student_attendance')
										->select(DB::raw("count(DISTINCT reg_no) as persentstudent"),DB::raw('Date(date) as pdate'))
										->where('batch_id', $value->batch_id)
										->groupBy(DB::raw('Date(date)'));
									
									if (!empty($fdate) && !empty($tdate)) {  
										$checkPersent->whereRaw(DB::raw("DATE(date) >= '".$fdate."' AND DATE(date) <= '".$tdate."'"));
									}else{
										$checkPersent->whereRaw(DB::raw("DATE(date) = '".date('Y-m-d')."'"));
									}
									
									if($anuprati_id=='Yes'){
										$checkPersent->whereRaw(DB::raw("cast like '%Anupriti Yojna-2023-24%'"));
									}
									
									$persent	  	= 	$checkPersent->get();		

									$total_female 	= 	$value->total_admission - $value->total_male;
									$persentstudent	=	0;
									$absent			=	$value->total_admission;
									$pdate			=	"00:00:00";
									
									if(count($persent) > 0){
										foreach($persent as $key => $pvalue){
											$absent 		= 	$value->total_admission - $pvalue->persentstudent;
											$persentstudent	=	$pvalue->persentstudent;
											$pdate=date('Y-m-d', strtotime($pvalue->pdate));
											
											
											$persentPercent = round(($persentstudent/$value->total_admission)*100,2);											
											$absentPercentage		=	round(($absent/$value->total_admission)*100,2);
							?>
							<tr>
								<td><?php echo e($i); ?></td>
								<td><?php echo e($value->batch_name); ?></td>
								<td class="sum_total_admission"><?php echo e($value->total_admission); ?> </td>
								<td class="sum_total_anuprati"><?php echo e($value->total_anuprati); ?></td>
								<td class="sum_total_male"><?php echo e($value->total_male); ?> </td>
								<td class="sum_total_female"><?php echo e($total_female); ?> </td>
								<td class="sum_persentstudent"><a href="<?php echo e(route('admin.student-attendance-view',[$value->batch_id,$pdate,1])); ?>" title="Click Here"><?php echo e($persentstudent); ?></a></td>
								<td class="sum_absent"><a href="<?php echo e(route('admin.student-attendance-view',[$value->batch_id,$pdate,2])); ?>" title="Click Here"><?php echo e($absent); ?></a></td>
								<td><?php echo e($pdate); ?></td>
								<td><?php echo e($persentPercent); ?></td>
								<td><?php echo e($absentPercentage); ?></td>
							</tr>
							<?php
											$i++; 
										}
									}
									// }else{
							
									// }
								}								
							}else{ ?>
							<tr>
								<td colspan="12" class="text-center">No Attendance Found</td>
							</tr>	
							<?php } ?>
							<tr style="background: red;color: white;font-weight: bold;">
								<td><b><?php echo e(app('request')->input('branch_location')); ?></b></td>
								<td><b>Total</b></td>
								<td class="total_student"><b>0</b></td>
								<td class="total_anuprati"><b>0</b></td>
								<td class="total_male">-</td>
								<td class="total_female">-</td>
								<td class="total_persentstudent"><b>0</b></td>
								<td class="total_absent"><b>0</b></td>
								<td>-</td>
								<td colspan="2" class="text-center"><b>Percentage</b></td>
							</tr>
							
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
        <?php echo e(csrf_field()); ?>

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
						<tr>
			    			<th>RFID Number</th>
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
	function locationBranch(value){
		$("#overlay_loader").css('display','block');
		$.ajax({
			type : 'POST',
			url : '<?php echo e(route('admin.employee.get-branch')); ?>',
			data : {'_token' : '<?php echo e(csrf_token()); ?>', 'branch_id': value},
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
		var fdate = $('.fdate').val();
		var tdate = $('.tdate').val();
		$.ajax({
			type : 'POST',
			url : '<?php echo e(route('admin.get-batch')); ?>',
			data : {'_token' : '<?php echo e(csrf_token()); ?>', 'branch_id': value, 'fdate': fdate, 'tdate': tdate},
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
				url : '<?php echo e(route('admin.batchinventory.studentSearch')); ?>',
				data : {'reg_no':reg_no,'month':month},
				dataType : 'json',
				success : function(data){
					$("#overlay_loader").css('display','none');
					if(data.status==true){
						var st=data.data.student;
						var atd=data.data.st_attendance;
						var invt=data.data.st_inventory_pending;
						// alert(data.message);
						var stSData=`
							<div class="col-md-6 pt-3">
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
							<tr>
				    			<th>RFID Number</th>
				    			<td>`+st.rfid_no+`</td>
			    			</tr>
			    		</table><br>
			    		
			    		<h4>Pending Inventory Details</h4>
			    		<table class="table table-responsive">
			    			<tr>
				    			<th>Inventory Name</th>
				    			<th>Assgined Status</th>
			    			</tr>`;
              for(var i = 0; i<invt.length; i++) {
				  if(invt[i]['is_assgined']=='Pending'){
              	stSData+=`<tr>
				    			<td>`+invt[i]['name']+`</td>
				    			<td>`+invt[i]['is_assgined']+`</td>
			    			</tr>`;
				  } }
			    	  stSData+=`</table>
					  <h4>Assigned Inventory Details</h4>
					  <table class="table table-responsive">
			    			<tr>
				    			<th>Inventory Name</th>
				    			<th>Assgined Status</th>
			    			</tr>`;
              for(var i = 0; i<invt.length; i++) {
				  if(invt[i]['is_assgined']=='Assigned'){
              	stSData+=`<tr>
				    			<td>`+invt[i]['name']+`</td>
				    			<td>`+invt[i]['is_assgined']+`</td>
			    			</tr>`;
			  } }
			    	  stSData+=`</table>
			    	</div>

			    	<div class="col-md-6 pt-3" >
			    		<div>
							<h4>Attendance Details  <button onClick="ExportToExcel2()" class="btn btn-primary">Excel</button>
							<button onClick="printDiv('print_in_excel')" class="btn btn-primary">PDF</button></h4>
						</div>
						<div id="print_in_excel">
			    		<table class="table table-responsive" >
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

			    	stSData+=`</table></div>
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
	
	function ExportToExcel2(){
	   var htmltable= document.getElementById('print_in_excel');
	   var html = htmltable.outerHTML;
	   window.open('data:application/vnd.ms-excel,' + encodeURIComponent(html));
	}
	
	
	function printDiv(divName) {
		 var printContents = document.getElementById(divName).innerHTML;
		 var originalContents = document.body.innerHTML;
		
		 document.body.innerHTML = printContents;

		 window.print();

		 document.body.innerHTML = originalContents;
	}


	$(document).ready(function() {

    function sumTotalAdmission(getclass,targetclass) {
        var elements = document.getElementsByClassName(getclass);
        var sum = 0;
        for (var i = 0; i < elements.length; i++) {
            sum += parseFloat(elements[i].textContent.trim()) || 0;
        }
        //console.log("Sum of total admission: " + sum);

        var inselements = document.getElementsByClassName(targetclass)[0].innerText=sum;
    }

   	  sumTotalAdmission('sum_total_admission','total_student');
   	  sumTotalAdmission('sum_total_anuprati','total_anuprati');
			sumTotalAdmission('sum_total_male','total_male');
			sumTotalAdmission('sum_total_female','total_female');
			sumTotalAdmission('sum_persentstudent','total_persentstudent');   	
			sumTotalAdmission('sum_absent','total_absent');

     
});



</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/batchinventory/attendance-dashboard.blade.php ENDPATH**/ ?>