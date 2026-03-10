
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Batch Details</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('studiomanager.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">Batch Details
								</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
						<a href="<?php echo e(route('studiomanager.batch.index')); ?>" class="btn btn-primary mr-1">Back</a>
					</div>
				</div>
			</div>
			<div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
			</div>
		</div>
		<div class="content-body">
			<!-- page users view start -->
			<section class="page-users-view">
				<div class="row">
					<div class="col-12">
						<div class="card">
							<div class="card-header">
							
							</div>
							<div class="card-body">
								<div class="row">
									 
									<div class="col-12 col-sm-9 col-md-6 col-lg-5">
										<table>
											<tr>
												<td class="font-weight-bold">Batch Name</td>
												<td><?php echo e($batch->name); ?></td>
											</tr>
											<tr>
												<td class="font-weight-bold">Course Name</td>
												<td><?php echo e($batch->course->name); ?></td>
											</tr>
											
											<tr>
												<td class="font-weight-bold">Start Date</td>
												<td><?php echo e(date('d-m-Y',strtotime($batch->start_date))); ?></td>
											</tr>
											
										</table>
									</div>
									 
								</div>
							</div>
						</div>
					</div>
					<!-- information start -->
					<div class="col-md-12 col-12 ">
						<div class="card">
							<div class="table-responsive">
								<table class="table data-list-view">
									<thead>
										<tr>
											<th>S.No.</th>
											<!--th>Faculty</th-->
											<th>Subject</th>
											<th>Duration</th>
											<!--th>Chapter</th>
											<th>Topic</th>
											
											<th>Schedule Date</th>
											<th>Spent Hour</th>
											<th>Status</th>
											<th>Remark</th-->
											<th>Status</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<?php
										/*$i = 1;
										foreach($batch->batch_relations as  $key => $value){
										
										$chapter = \App\Chapter::where('course_id', $batch->course->id)->where('subject_id', $value->subject->id)->first();
										?>
										<tr>
											<td class="product-category">{{ $i++ }}</td>
											<td class="product-category">{{ $value->user->name }}</td>
											<td class="product-category">{{ $value->subject->name }}</td>
											<td class="product-category">{{ $chapter->name }}</td>
											 
										</tr>
										<?php }*/ ?>	
										
										<?php
										$i = 1;
										foreach($send_array as  $key => $value){
										?>
										<tr>
											<td class="product-category"><?=$value['s_no']?></td>
											<!--td class="product-category"><?php //echo $value['faculty_name']?></td-->
											<td class="product-category"><?=$value['subject_name']?></td>
											<td class="product-category"><?php echo $value['duration']?></td>
											<!--td class="product-category"><?php //echo $value['chapter_name']?></td-->
											<!--td class="product-category"><?php //echo $value['topic_name']?></td-->
											
											<!--td class="product-category"><?php //echo $value['schedule_date']?></td-->
											<!--td class="product-category"><?php //echo $value['spent_hour']?></td>
											<!--td class="product-category"><?php //echo $value['status']?></td-->
											<!--td class="product-category"><?php //echo $value['remark']?></td-->
											<td class="product-category">
												<strong class="text-<?php echo e(($value['subject_status']=='Complete')?'primary':'danger'); ?>">
												<?php 
												//echo $value['subject_status'];
												if($value['subject_status']=='Complete'){
													echo " (".date('d-m-Y',strtotime($value['complete_date'])).")";
												}else if($value['subject_status']=='Uncomplete'){
													echo 'Incomplete';
												}
												?>
												</strong>
											</td>
											<td class="product-category">
												<?php
												if($value['subject_status']=='Complete'){
													?>
													<input type="hidden" class="batch_relation_id" value="<?=$value['batch_relation_id']?>" />
													<a href="Javascript:void(0);" class="uncomplete_click btn btn-sm btn-danger">Incomplete</a>
													<?php
												}
												else{
													?>
														<a href="Javascript:void(0);" class="complete_click btn btn-sm btn-primary" >Complete</a>
														<div method="post" class="complete_div" style="display:none;">
															<input type="hidden" class="batch_relation_id" value="<?=$value['batch_relation_id']?>" />
															<input type="date" class="complete_date" />
															<button class="btn btn-sm btn-primary complete_submit" >Submit</button>
														</div>
													<?php
												}
												?>

												<?php if($value['duration'] > 0){ ?>
												<button type="button" class="btn btn-transparent p-1 viewStatus" style="border:solid 1px #000;font-size:12px;color:#000;padding:8px !important" data-toggle="modal" data-target="#statusModel" data-batch-id="<?=$value['batch_id']?>" data-subject-id="<?=$value['subject_id']?>"><b>View Status</b></button>
												<button type="button" id="download_pdf" data-id="<?php echo e($value['subject_id']); ?>" data-course-id="<?php echo e($value['course_id']); ?>" data-subject-name="<?php echo e($value['subject_name']); ?>" data-batch-name="<?php echo e($batch->name); ?>" data-course-name="<?php echo e($batch->course->name); ?>" data-start-date="<?php echo e(date('d-m-Y',strtotime($batch->start_date))); ?>" class="btn-success btn-sm">PDF</button>
												<?php } ?>
												
												
											</td>
											 
										</tr>
										<?php } ?>										
									</tbody>
								</table>
							</div>    
						</div>
					</div>
					<!-- information start -->
					<!-- social links end -->
					 
					
					
				</div>
			</section>
		</div>
	</div>
</div>



<!-- Batch Planner List -->
<div class="modal fade" id="statusModel" tabindex="-1" role="dialog" a>
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Batch Status</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body chapter_topic_list p-2">
				<div>
					<select class="form-control">
						<option value="">Select Batch</option>
					</select>
				</div>
				<div class="py-1" style="color:#FF6F0E">REET 2nd Grade B-02 Batch</div>
				<div class="row mx-0 pb-2">
					<div class="float-left p-1" style="background:#F3FEFF;border:solid 1px #00CEE3;border-radius:10px;width:45%">TOTAL: 59</div>
					<div style="width:5%;"></div>
					<div class="float-left  p-1" style="background:#DEFFED;border:solid 1px #28C66F;border-radius:10px;width:45%">Completed: 6</div>
					<div class="float-left p-1 mt-2" style="background:#EEECFF;border:solid 1px #7367EF;border-radius:10px;width:45%">Partially Completed : 59</div>
					<div style="width:5%;"></div>
					<div class="float-left p-1 mt-2" style="background:#FFDEDE;border:solid 1px #E55658;border-radius:10px;width:45%">Pending: 59</div>
				</div>
				<div>
					<table class="table">
						<thead class="thead-light">
							<tr>
								<th scope="col">Topic Name</th>
								<!--th scope="col">Sub Topic</th-->
								<th scope="col">Status</th>
							</tr>
						</thead>
						<tbody>
							<?php for($i=1;$i<=5;$i++){ ?>
							<tr>
								<th scope="row">General Information सामान्य जानकारी</th>
								<!--td>Status Extension || स्थिति - विस्तार - पार्ट</td-->
								<td><span class="text-danger">Pending</span></td>	
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End Batch Planner List -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo e(asset('laravel/public/admin/css/app-user.css')); ?>">
<script>
$(document).on("click",".complete_click", function () {
	$(this).hide();
	$(this).siblings(".complete_div").show();
});

$(".complete_submit").on("click",function(e) {
	e.preventDefault();
	if (!confirm("Do you want complete subject")){
	  return false;
	}
	var batch_relation_id = $(this).siblings('.batch_relation_id').val();
	var complete_date = $(this).siblings('.complete_date').val();
	$.ajax({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},      
		type: "POST",
		url : '<?php echo e(route('studiomanager.batch_subject_status_update')); ?>',
		data : {'batch_relation_id':batch_relation_id,'complete_date':complete_date,'status':'Complete'},
		success : function(data){
			if(data.status == false){
				swal("Error!", data.message, "error");
			} else if(data.status == true){					
				swal("Done!", data.message, "success").then(function(){ 
					location.reload();
				});
			}
		}
	});
});

$(".uncomplete_click").on("click",function(e) {
	e.preventDefault();
	if (!confirm("Do you want Incomplete subject")){
	  return false;
	}
	var batch_relation_id = $(this).siblings('.batch_relation_id').val();
	$.ajax({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},      
		type: "POST",
		url : '<?php echo e(route('studiomanager.batch_subject_status_update')); ?>',
		data : {'batch_relation_id':batch_relation_id,'status':'Uncomplete'},
		success : function(data){
			if(data.status == false){
				swal("Error!", data.message, "error");
			} else if(data.status == true){					
				swal("Done!", data.message, "success").then(function(){ 
					location.reload();
				});
			}
		}
	});
});
 

$(".viewStatus").on("click",function(){
	var batch_id=$(this).attr("data-batch-id");
	var subject_id=$(this).attr("data-subject-id");
	$.ajax({
		type: "GET",
		url : '<?php echo e(route('planerDetail')); ?>/0?batch_id='+batch_id+'&subject_id='+subject_id,
		dataType : 'json',
		success : function(data){
				
				$(".chapter_topic_list").html("");
				
				var batches=`<div class="row text-dark text-bold text-left d-none">`; 
				for(var i=0;i<data.length;i++){
					var batch_id=data[i]['batch_id'];
					var batch_name=data[i]['batch_name'];
					
					if(i==0){
					 batches+=`<div class="col-6"><label><input type="radio" name="batche_planner" class=" batche_planner" value="`+batch_id+`" checked="checked">&nbsp; `+batch_name+`</label></div>`;
					}else{
					  batches+=`<div class="col-6"><label><input type="radio" name="batche_planner" class=" batche_planner" value="`+batch_id+`">&nbsp; `+batch_name+`</label></div>`;	
					}

				}

				batches+=`</div>`;
				
				$(".chapter_topic_list").append(batches);
				
				if(data.length==0){
					$(".chapter_topic_list").append('No Record Found');
					return;
				}

				for(var i=0;i<data.length;i++){

					var batch_id=data[i]['batch_id'];
					var batch_name=data[i]['batch_name'];
					var chapters=data[i]['chapters'];
					
					batch_id=i!=0?batch_id+" d-none":batch_id;
					var chapter_topic_status=`<div class="row planner_detail planner_`+batch_id+`">`;

					var chapter_topic_list=`<div class="py-1" style="color:#FF6F0E">`+batch_name+`</div>`;

					chapter_topic_list+='<table class="table">';

					chapter_topic_list+='<tr>';
					chapter_topic_list+='<th>Topic Name</th>';
					// chapter_topic_list+='<th>Sub Topic</th>';
					chapter_topic_list+='<th>Status </th>';
					chapter_topic_list+='</tr>';
					var topic_pending=topic_completed=topic_partially=0;
					$.each(chapters,function(key){
						chapter_topic_list+=`<tr>`;
						chapter_topic_list+=`<td>`+chapters[key]['chapter_name']+`</td>`;
						// chapter_topic_list+=`<td >`+chapters[key]['topic_name']+`</td>`;
						var status='';
						if(chapters[key]['status']==null || chapters[key]['status']==0){
							status="<span class='text-danger'><b>Pending</b></span>";
							topic_pending++;
						}else if(chapters[key]['status']==1){
						   status="<span class='text-success'><b>Completed</b></span>";
							topic_completed++;
						}else if(chapters[key]['status']==2 || chapters[key]['status']==7){
						   status="<span class='text-primary'><b>Partially Completed</b></span>";
							topic_partially++;
						}

						chapter_topic_list+=`<td>`+status+`</td>`;
						chapter_topic_list+=`</tr>`;
					});

					chapter_topic_list+='</table>';
					
					var topic_total=topic_completed+topic_pending+topic_partially;
					chapter_topic_status+=class_count(topic_total,topic_completed,topic_partially,topic_pending);

					

					chapter_topic_list=chapter_topic_status+chapter_topic_list;
					chapter_topic_list+="</div>";
					
					$(".chapter_topic_list").append(chapter_topic_list);
				}
		}
	});
});

function class_count(total,complete,partially,pending){
	var html=`<div class="row mx-0 pb-2">
		<div class="float-left p-1" style="background:#F3FEFF;border:solid 1px #00CEE3;border-radius:10px;width:45%">TOTAL:`+total+`</div>
		<div style="width:5%;"></div>
		<div class="float-left  p-1" style="background:#DEFFED;border:solid 1px #28C66F;border-radius:10px;width:45%">Completed: `+complete+`</div>
		<div class="float-left p-1 mt-2" style="background:#EEECFF;border:solid 1px #7367EF;border-radius:10px;width:45%">Partially Completed : `+partially+`</div>
		<div style="width:5%;"></div>
		<div class="float-left p-1 mt-2" style="background:#FFDEDE;border:solid 1px #E55658;border-radius:10px;width:45%">Pending:`+pending+`</div>
	</div>`;
	return html;
}


</script>
<script type="text/javascript">	
 
$("body").on("click", "#download_pdf", function (e) {
		var data = {};
		data.subject_id=$(this).attr("data-id"),
		data.course_id=$(this).attr("data-course-id"),
		data.subject_name=$(this).attr("data-subject-name"),
		data.batch_name=$(this).attr("data-batch-name"),
		data.course_name=$(this).attr("data-course-name"),
		data.start_date=$(this).attr("data-start-date"),
		window.open("<?php echo URL::to('/studiomanager/'); ?>/batch-subject-topic-pdf?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&'));
	});
</script>



<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.studiomanager', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/studiomanager/batch/view.blade.php ENDPATH**/ ?>