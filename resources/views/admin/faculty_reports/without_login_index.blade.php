@extends('layouts.without_login_admin')
@section('content')

<?php 
 $helper=new App\Helper();
?>
<div class="app-content content" style="margin: 0px !important;">
	<div class="content-wrapper" style="margin-top: 0px !important;">
		<div class="content-body">
		  <div class="">
			  <div class="">
				  <form action="{{ route('faculty-reports') }}" method="get" name="filtersubmit">
						<div class="row">
							<input type="hidden" name="faculty_id" class="faculty_id_get" name="" value="{{ app('request')->input('faculty_id') }}">
							
							<div class="float-left col-9 pr-0">											
								<label for="users-list-status" style="color:#000 !important;"><b>Select Date</b></label>			
								<fieldset class="form-group">
									<input type="date" name="fdate" placeholder="Date" value="{{ $selectFromDate }}" class="py-2 form-control StartDateClass fdate" style="border-radius:0;border:solid 1px #c0c0c0;border-top-left-radius:8px;border-bottom-left-radius:8px;">	
								</fieldset>	
							</div>								
							<div class="float-left  col-3  pl-0"  >
								<label for="users-list-status">&nbsp;</label>		
						 		<fieldset class="form-group" style="">		
								  <button type="submit" class="w-100 btn px-0 py-1" style="background:#F2F2F2;border-radius:0;border:solid 1px #c0c0c0;border-top-right-radius:8px;border-bottom-right-radius:8px;color:#000 !important;"><b>Search</b></button>
							  </fieldset>					
							</div>		
						</div>
					</form>
				</div>
			</div>
			<div class="pb-5">
			<?php 
				if(!empty($selectFromDate) && !empty($get_faculty) && count($get_faculty)>0) {
					foreach ($get_faculty as $get_faculty_value) {
			?>
			  <div class="text-dark d-none">
			   Faculty Name : <?php echo $get_faculty_value->faculty_name??'-' ?>
			  </div>

			  <?php 
	               $whereCond = '1=1';
				   $whereCond .= ' AND timetables.cdate = "'.$selectFromDate.'" ';
				   $get_faculty_timetable = DB::table('timetables')
					  ->selectRAW("timetables.*,studios.name as studios_name,branches.name as branches_name,branches.id as branches_id,batch.name as batch_name,subject.name as subject_name,start_classes.status as start_classes_status,start_classes.start_time as start_classes_start_time,start_classes.end_time as start_classes_end_time,start_classes.topic_name,users_assistant.name as assistant_name,users_assistant.mobile as assistant_mobile,batch.course_planer_enable,batch.master_planner")
					  ->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
					  ->leftJoin('branches', 'branches.id', '=', 'studios.branch_id')
					  ->leftJoin('batch', 'batch.id', '=', 'timetables.batch_id')
					  ->leftJoin('subject', 'subject.id', '=', 'timetables.subject_id')
					  ->leftJoin('start_classes', 'start_classes.timetable_id', '=', 'timetables.id')
					  ->leftJoin('users as users_assistant', 'users_assistant.id', '=', 'timetables.assistant_id')
					  ->where('timetables.faculty_id', $get_faculty_value->faculty_id)
					  ->where('timetables.time_table_parent_id', '0')
					  ->where('timetables.is_deleted', '0')
					  ->where('timetables.is_publish', '1')
					  ->whereRaw($whereCond)
					  ->orderBy('timetables.from_time', 'ASC')->get();

					$spent_base_time = new DateTime('00:00');
					$spent_total     = new DateTime('00:00');
					
					$total_base_schedule = new DateTime('00:00');
					$total_schedule = new DateTime('00:00');

					foreach($get_faculty_timetable as $val){
						if($val->is_cancel != '1'){
							$first_date = new DateTime($val->start_classes_start_time);
							$second_date = new DateTime($val->start_classes_end_time);
							$interval = $first_date->diff($second_date);
							$duration = $interval->format('%H : %I Hours');
							$spent_base_time->add($interval); 
						}else{
							$duration = 'Cancelled Classes';
						}
						
						
						
						$from_time         = new DateTime($val->from_time);
						$to_time           = new DateTime($val->to_time);
						$schedule_interval = $from_time->diff($to_time);
						$schedule_duration = $schedule_interval->format('%H : %I Hours');
						$total_base_schedule->add($schedule_interval);
						
						// Duration in total minutes as integer
                        $class_duration = ($schedule_interval->h * 60) + $schedule_interval->i;
			  ?>
				
				<div class="mb-4" style="font-size:12px; border:solid 1px #c0c0c0;border-radius:15px;">
					<div class="row mx-0 p-2" style="background:#FCDD10;border-top-left-radius:15px;border-top-right-radius:15px">

						<?php
						$planer=DB::table('timetables')
						->select('batch.course_planer_enable','batch.name as batch_name','timetables.batch_id as batch_id')
						->leftJoin('batch','batch.id','timetables.batch_id')
						->whereRAW("(timetables.id=".$val->id." OR time_table_parent_id=".$val->id.")")
						->where('timetables.is_publish','1')
						->orderby('course_planer_enable','desc')
						->get();
						$batch_name="";
						$batch_id="";
						$course_planer_enable=0;
						foreach($planer as $p){
                            if($p->course_planer_enable==1){
                            	$course_planer_enable=1;
                            }

                            $batch_name.=$p->batch_name.", ";
                            $batch_id .=$p->batch_id .", ";
						}
						?>


						<div class="float-left w-75 pr-2">
							<div>Batch Name</div>
						<div><b>{{$batch_name}}</b></div>
						</div>
                        
                        @if($course_planer_enable==1)
						    <div class="float-left w-25 text-right">
								<button type="button" class="btn btn-transparent p-1 viewStatus viewStatus_{{$val->id}}" style="border:solid 1px #000;font-size:12px;color:#000;padding:8px !important" data-id="{{$val->id}}" data-class_duration="{{$class_duration}}"><b>View Status</b></button>
							</div>
						@endif
					</div>
					<div class="row mx-0 p-2" style="background:#E9FEFF">
						<div class="float-left w-75 pr-2">
							Schedule Time</br>
							<b> 
								<?php echo date("h:i A", strtotime($val->from_time)) ?>
								to 
								<?php echo date("h:i A", strtotime($val->to_time)) ?>
							  (<?=$schedule_duration?>)</b>
						</div>
						<div class="float-left w-25 text-left">
							Spent Time</br>
							<b>{{$duration}}</b>
						</div>
					</div>


					<div class="px-2 pb-2">
						<div class="py-1">
							<div><b><?php echo $val->subject_name??'Subject';?></b></div>
							<?php 
								$batch_id = rtrim($batch_id, ',');
								$batch_id = rtrim($batch_id, ", \t\n\r\0\x0B"); 
								
								$nBatchID   = array_values(array_filter(explode(',', $batch_id)));
								$nBatchName = array_values(array_filter(explode(',', $batch_name)));

								if (!empty($nBatchID)) {
									foreach ($nBatchID as $key => $id) { 
										if ($id === '') continue;

										$plan = $helper->subject_plan_spent_time($id, $val->subject_id, $val->faculty_id);
							?>
							<div>
								<div class="pt-2"><b>Batch : {{ $id }} </b> <?= $nBatchName[$key] ?? $id; ?></div>
								<div>
									Plan Hours: <?= $plan['plan_time']; ?> 
									(Spent Hours: <?= $plan['spent_time']; ?>, 
									Remaining Hours: <?= $plan['remaining_hours'] ?? 0; ?> )
								</div>
							</div>
							<?php 									
									} 
								}
							?>
							
							<?php /*
							<div>
								<?php $plan=$helper->subject_plan_spent_time($val->batch_id,$val->subject_id,$val->faculty_id);?> 
						    Plan Hours: <?=$plan['plan_time'];?> (Spent Hours :<?=$plan['spent_time'];?>, Remaining Hours : <?=$plan['remaining_hours']??0;?> )</div>
							*/ ?>
						</div>
						<hr class="m-0">
						<div class="py-1">	
								
							<b>
							<div>
								<?php echo $val->branches_name??'';?> - 
								<?php echo $val->studios_name??'';?> 
							</div>
							<div><?php echo  $dd=$helper->get_center_head($val->branches_id);?></div>
							<div>Class Assistant - 
								<?php echo $val->assistant_name??''; ?> 
								(<?php echo $val->assistant_mobile??'';?>)</div>
							</b>
						</div>
						<hr class="m-0">
						
						<!-- <div class="pt-2"> <b>Today's Topic</b></div> -->
						<div class="acc-container" style="max-width:100%;">
							<div class="acc">
								<?php $topic=$helper->class_topics($val->id,$val->master_planner); ?>
								@if(!empty($topic) && count($topic))
									<div class="acc-head">							
										<div><b>Today's Topic</b></div>
									</div>
									<?php $topic=$helper->class_topics($val->id,$val->master_planner); ?>
									@php $i=1; @endphp
									@foreach($topic as $t)
										<div class="acc-content">
											<div class="acc-head=">							
												<div><b><?php echo $t['chapter']??"";?></b></div>
											</div>
											<hr class="my-1">
											{{-- 	
											@if(!empty($t["topic"]) && count($t["topic"]))
												<div class="pl-1 pb-1 acc-topic" style="color:#FF6F0E;"><b>Sub Topic</b></div>
												@foreach($t["topic"] as $topic)
													<div class="pl-1 pb-1">
														<?php echo $topic["topic_name"]??""?>
													  <div>Status :
													    @if($topic["status"]==1) 
													  	 <b class="text-success">Completed</b>
													  	@elseif($topic["status"]==2)
													  	 <b class="text-primary">Partially Completed</b>
													  	@endif
													  </div>
													</div>
												@endforeach
												<hr class="my-1">
											@endif
											--}}
										</div>
									@endforeach
									@php $i++; @endphp
								@else
								  <b class="mt-2">Topic</b><br>
								 <?php echo $val->topic_name??""; ?>
								@endif
							</div>
						</div>
						 
						<div class="text-right">
							<button type="button" class="btn btn-transparent p-1 text-primary raise_issue" style="border:solid 1px #c0c0c0;font-size:12px;padding:8px !important" data-toggle="modal" data-target="#modalRaiseIssue" data-id="{{$val->id}}">
								Raise Issue <i class="fa fa-arrow-right" aria-hidden="true"></i>
							</button>
							@if(!empty($topic) && count($topic)) @endif
						</div> 
					</div>
				</div>

			<?php } ?>
			</div>
			<div class="row  mx-0 py-1 text-center" style="background:#F6F6F6;position:fixed;bottom:0;right:0;left:0;font-size:12px;">
				<div class="float-left w-50" width="50%">Schedule Time: 
				<b><?php
				$totalDays = $total_schedule->diff($total_base_schedule)->format("%a");
				$totalHours = $total_schedule->diff($total_base_schedule)->format("%H");
				$totalMinute = $total_schedule->diff($total_base_schedule)->format("%I");
				echo ($totalDays*24)+$totalHours. ":" . $totalMinute;
				?> Hours
				</b>
				</div> 
				<div class="float-left  w-50"  width="50%">Spent Time:
				<b>
				<?php
				$baseDays = $spent_total->diff($spent_base_time)->format("%a");
				$baseHours = $spent_total->diff($spent_base_time)->format("%H");
				$baseMinute = $spent_total->diff($spent_base_time)->format("%I");
				echo ($baseDays*24)+$baseHours. ":" . $baseMinute;
				?> 
				Hours
				</b> 
				</div> 
			</div>	
		  <?php } } ?>


		</div>
	</div>
</div>

<!-- Batch Planner List -->
<div class="modal fade" id="statusModel" tabindex="-1" role="dialog">
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
								<th scope="col">Sub Topic</th>
								<th scope="col">Status</th>
							</tr>
						</thead>
						<tbody>
							<?php for($i=1;$i<=5;$i++){ ?>
							<tr>
								<th scope="row">General Information सामान्य जानकारी</th>
								<td>Status Extension || स्थिति - विस्तार - पार्ट</td>
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

<!-- Submit Issue on topic -->
<div class="modal fade" id="modalRaiseIssue" tabindex="-1" role="dialog" a>
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" style="color:#FF6F0E">Raise Issue</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			
			<input type="hidden" name="timetable_id" class="timetable_id">
			
			<div class="modal-body">
				<textarea name="topic_issue" class="form-control topic_issue" rows="4" placeholder="Type your issue here" style="resize:none;"></textarea>
				<span class="text-secondary topic_issue_chr_limit">0 /200</span>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn w-100 raise_issue_btn text-dark" style="background:#DEDEDE"><b>Submit Now</b></button>
			</div>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
				<button type="button" class="close" id="closeModalBtn">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<table class="table table-bordered">
					<thead class="plannerhead">
						<tr>
							<th scope="col">Title</th>
							<th scope="col">Class Date</th>
							<th scope="col">Start Time</th>
							<th scope="col">End Time</th>
							<th scope="col">Duration</th>
						</tr>
					</thead>
					<tbody class="classData">
						<tr>
							<th scope="row">-</th>
							<td>-</td>
							<td>-</td>
							<td>-</td>
							<td>-</td>
						</tr>						
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>


<!-- Custom Confirmation Modal -->
<div class="modal fade" id="classActionModal" tabindex="-1" role="dialog" aria-labelledby="classActionModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h5 class="modal-title" id="classActionModalLabel">Confirm Action</h5>
				<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				Are you sure you want to mark all previous pending chapter as <strong>Completed</strong> or <strong>Dropped</strong>?
				
				<div class="append_chapter"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" id="markCompleted">✅ Completed</button>
				<button type="button" class="btn btn-warning" id="markDropped">⚠️ Dropped</button>
			</div>
		</div>
	</div>
</div>


<style>
	.plannerhead{
		background:#ea5455 !important;
		color:#fff;
	}
	
	.plannerhead th{
		background:#ea5455 !important;
		color:#fff;
	}
	
	body {
		background:#FFFEF5 !important; 
		color: #000 !important;
	}

	.acc-container {
		max-width: 700px;
		width: 100%;
	}

	.acc {
		margin-bottom: 10px;
	}

	.acc-head {
		padding: 10px 0px;
		font-size: 16px;
		position: relative;
		cursor: pointer;
	}

	.acc-head::before,
	.acc-head::after {
		content: "";
		position: absolute;
		top: 50%;
		background-color: #000;
		transition: all 0.3s;
	}

	.acc-head::before {
		right: 30px;
		width: 2px;
		height: 10px;
		margin-top: -6px;
	}

	.acc-head::after {
		right: 26px;
		width: 10px;
		height: 2px;
		margin-top: -2px;

	}

	.acc-head p {
		color: #000;
		font-weight: bold;
	}

	.acc-content {
		display: none;
		color: #000;
	}

	.acc-head.active::before {
		transform: rotate(90deg);
	}


	.accordion-button {
		background-color: #FFC800;
		color: #000;
	}
	
	.get_topic_class {
		cursor: pointer;
		color: #8c6060;
	}
	
	.green {
		background: #76ff76;
		color:#000;
	}
	
	.red {
		background: #ff0000;
		color: #fff;
	}
	
	.yellow {
		background: #FFFF00;
		color:#000;
	}
</style>
@endsection

@section('scripts')
<script type="text/javascript">
	$(document).ready(function () {
		$(".acc-container .acc:nth-child(1) .acc-head").addClass("active");
		$(".acc-container .acc:nth-child(1) .acc-content").slideDown();
		$(".acc-head").on("click", function () {
			if ($(this).hasClass("active")) {
				$(this).siblings(".acc-content").slideUp();
				$(this).removeClass("active");
				$(this).siblings('.acc-topic').css("display",'none');
			} else {
				$(".acc-content").slideUp();
				$(".acc-head").removeClass("active");
				$(this).siblings(".acc-content").slideToggle();
				$(this).toggleClass("active");
				$(this).siblings('.acc-topic').css("display",'block');
			}
		});
	});

	$(".viewStatus").on("click",function(){
		$(".chapter_topic_list").html("");
		var tt_id=$(this).attr("data-id");
		var class_duration=$(this).attr("data-class_duration");
		console.log(class_duration);
		
        $.ajax({
			type: "GET",
			url : '{{ route('planerDetail') }}/'+tt_id,
			dataType : 'json',
			success : function(data){
				    $("#statusModel").modal('show');
				    var batches=`<div class="row text-dark text-bold text-left">`; 
                    var batch_ids="";
                    for(var i=0;i<data.length;i++){
                    	var batch_id=data[i]['batch_id'];
                    	var batch_name=data[i]['batch_name'];
                    	var master_planner=data[i]['master_planner'];
                    	var comman_batch_id=data[i]['comman_batch_id'];
                    	var master_planner_id=data[i]['master_planner_id'];

                    	batch_ids+=batch_id+",";
                    	
						if (master_planner == 0) {
							var tclr = 'text-primary';
						} else {
							var tclr = '';
						}
						
                    	if(i==0){
                    	 batches+=`<div class="col-6"><label class="${tclr}"><input type="radio" name="batche_planner[]" class=" batche_planner" value="`+batch_id+`" checked="checked">&nbsp; `+batch_name+`</label></div>`;
                    	}else{
                    	  batches+=`<div class="col-6"><label class="${tclr}"><input type="radio" name="batche_planner[]" class=" batche_planner" value="`+batch_id+`">&nbsp; `+batch_name+`</label></div>`;	
                    	}

                    }

                    batches+=`</div>`;
                    
                    $(".chapter_topic_list").append(batches);

                    for(var i=0;i<data.length;i++){
                    	var subject_id=data[i]['subject_id'];
                    	var batch_id=data[i]['batch_id'];
                    	var batch_name=data[i]['batch_name'];
                    	var chapters=data[i]['chapters'];
                    	var comman_chapters=data[i]['comman_chapters'];
						
						console.log(comman_chapters);
						
						// var tt_id = data[i]['timetable_id'];

                        
                        //batch_id=i!=0?batch_id+" d-none":batch_id;
                        //batch_id=i!=0?batch_id+" ":batch_id;
                    	var chapter_topic_status=`<div class="row table-responsive planner_detail planner_`+batch_id+` ${i!=0?'d-none':''}">`;

                        var chapter_topic_list = `<div class="py-1" style="color:#FF6F0E">`+batch_name+`</div>`;

						chapter_topic_list += '<table class="table" id="yourTableId">';
						chapter_topic_list+='<tr><td colspan="4"><input type="text" class="form-control search_now" placeholder="Search Topic"></td></tr>';
						chapter_topic_list += '<tr>';
						chapter_topic_list += '<th>Topic Name</th>';
						chapter_topic_list+='<th>Plan Duration (In Minutes)</th>';
						chapter_topic_list+='<th>Spent Hours (In Minutes)</th>';
						chapter_topic_list += '<th>Status </th>';
						chapter_topic_list += '</tr>';

						var topic_pending=topic_completed=topic_partially=0;
						
						
						//Common Chapter
						let order2 = [1,7,0,2, null];
						comman_chapters.sort((a, b) => {
							return order2.indexOf(a.status) - order2.indexOf(b.status);
						});
						
						
						let scheduled = [];
						$.each(comman_chapters,function(key){
							if (key > 0) {
								for (let i = key; i > 0; i--) {
									let chapter = comman_chapters[i - 1];
									if (chapter['status'] != 1 && chapter['status'] != 8) {
										let chapterName = comman_chapters[i - 1]['chapter_name'];
										let chapterId = comman_chapters[i - 1]['chapter_id'];

										// Avoid duplicates
										let exists = scheduled.some(item => item.chapter_name === chapterName);
										if (!exists) {
											scheduled.push({
												chapter_name: chapterName,
												cid: chapterId
											});
										}
									}
								}
							}
						
							chapter_topic_list+=`<tr style="background:#f1fdff" data-batch-id="${batch_id}" data-status="${comman_chapters[key]['status']}" class="tr_chapter_${comman_chapters[key]['chapter_id']}">`;
							
							chapter_topic_list += `
							  <td>
								<span data-id="${comman_chapters[key]['chapter_id']}" data-title="${comman_chapters[key]['chapter_name']}" class="get_topic_class">
								  ${comman_chapters[key]['chapter_name']}
								</span>
							  </td>`;
							
							chapter_topic_list+=`<td>`+comman_chapters[key]['duration']+`</td>`;
							
							
							let duration = parseFloat(comman_chapters[key]['duration']) || 0;
							let spent_hours = parseFloat(comman_chapters[key]['spent_hours']) || 0;

							let tdClass = '';
							
							if (spent_hours === 0) {
								tdClass = '';
							} 
							else if (spent_hours <= duration) {
								tdClass = 'green';
							} else {
								let percentDiff = ((spent_hours - duration) / duration) * 100;

								if (percentDiff <= 10) {
									tdClass = 'yellow';
								} else if (percentDiff > 10) {
									tdClass = 'red';
								}
							}

							chapter_topic_list += `<td class="${tdClass}">` + comman_chapters[key]['spent_hours'] + `</td>`;
							
							
							var status='';
							if(comman_chapters[key]['status']==null || comman_chapters[key]['status']==0){
								status="<span class='btn btn-sm btn-danger mb-2 nextClass' data-action='schedule' data-tt-id='"+tt_id+"' data-topic-id='"+comman_chapters[key]['id']+"' data-chapter-id='"+comman_chapters[key]['chapter_id']+"' data-batch-id='"+batch_id+"' data-subject-id='"+subject_id+"' data-batch-ids='"+batch_ids+"' data-cbatch-ids='"+comman_batch_id+"' data-master-ids='"+master_planner_id+"' data-class_duration='"+class_duration+"' data-schedule-length='"+JSON.stringify(scheduled)+"'><b>Schedule in Nextclass</b></span><br>";
								status+="<span class='text-danger'><b>Pending</b></span>";
								topic_pending++;
							}else if(comman_chapters[key]['status']==1){
								status="<span class='text-success'><b>Completed</b></span>";
								topic_completed++;
							}else if(comman_chapters[key]['status']==2){
								status="<span class='btn btn-sm btn-outline-dark mb-2 nextClass' data-action='schedule' data-tt-id='"+tt_id+"' data-topic-id='"+comman_chapters[key]['id']+"' data-chapter-id='"+comman_chapters[key]['chapter_id']+"' data-batch-id='"+batch_id+"' data-subject-id='"+subject_id+"' data-batch-ids='"+batch_ids+"' data-cbatch-ids='"+comman_batch_id+"' data-master-ids='"+master_planner_id+"' data-class_duration='"+class_duration+"' data-schedule-length='"+JSON.stringify(scheduled)+"'><b>Schedule in Nextclass</b></span><br>";
								status+="<span class='text-warning'><b>Partially Completed</b></span>";
								topic_partially++;
							}else if(comman_chapters[key]['status']==7){
								if(comman_chapters[key]['timetable_id']==tt_id){
									status="<span class='text-primary'><b>Scheduled for Nextclass</b></span>";
									status+="<span class='d-none btn btn-sm btn-outline-danger mt-2 nextClass' data-action='cancel' data-tt-id='"+tt_id+"' data-topic-id='"+comman_chapters[key]['id']+"' data-chapter-id='"+comman_chapters[key]['chapter_id']+"' data-batch-id='"+batch_id+"' data-subject-id='"+subject_id+"' data-batch-ids='"+batch_ids+"' data-cbatch-ids='"+comman_batch_id+"' data-master-ids='"+master_planner_id+"' data-schedule-length=''><b>Cancel Topic</b></span><br>";
									topic_partially++;
								}else{
									status="<span class='btn btn-sm btn-danger mb-2 nextClass' data-action='schedule' data-tt-id='"+tt_id+"' data-topic-id='"+comman_chapters[key]['id']+"' data-chapter-id='"+comman_chapters[key]['chapter_id']+"' data-batch-id='"+batch_id+"' data-subject-id='"+subject_id+"' data-batch-ids='"+batch_ids+"' data-cbatch-ids='"+comman_batch_id+"' data-master-ids='"+master_planner_id+"' data-class_duration='"+class_duration+"' data-schedule-length='"+JSON.stringify(scheduled)+"'><b>Schedule in Nextclass</b></span><br>";
									status+="<span class='text-danger'><b>Partially Completed</b></span>";
									topic_pending++;
								}
							}else if(comman_chapters[key]['status']==8){
								status="<span class='btn btn-sm btn-outline-dark mb-2 nextClass' data-action='schedule' data-tt-id='"+tt_id+"' data-topic-id='"+comman_chapters[key]['id']+"' data-chapter-id='"+comman_chapters[key]['chapter_id']+"' data-batch-id='"+batch_id+"' data-subject-id='"+subject_id+"' data-batch-ids='"+batch_ids+"' data-cbatch-ids='"+comman_batch_id+"' data-master-ids='"+master_planner_id+"' data-class_duration='"+class_duration+"' data-schedule-length='"+JSON.stringify(scheduled)+"'><b>Schedule in Nextclass (Dropped)</b></span><br>";
								status+="<span class='text-warning'><b>Partially Completed</b></span>";
							}
							
							if (key === comman_chapters.length - 1 && chapters.length==0  && comman_chapters[key]['status']==7) {
								status+="<span class='btn btn-sm btn-outline-dark mb-2 nextClass' data-action='completed' data-tt-id='"+tt_id+"' data-topic-id='"+comman_chapters[key]['id']+"' data-chapter-id='"+comman_chapters[key]['chapter_id']+"' data-batch-id='"+batch_id+"' data-subject-id='"+subject_id+"' data-batch-ids='"+batch_ids+"' data-cbatch-ids='"+comman_batch_id+"' data-master-ids='"+master_planner_id+"' data-class_duration='"+class_duration+"' data-schedule-length='"+JSON.stringify(scheduled)+"'><b>Mark Completed</b></span><br>";
								
							}
							
							
							
							chapter_topic_list+=`<td>`+status+`</td>`;
							chapter_topic_list+=`</tr>`;						
						});


						//Old Process
						let order = [1,7,0,2, null]; // 1=completed,0=pending,2=Partially Completed,7=NextClass Schedule,null no schedule
						chapters.sort((a, b) => {
							return order.indexOf(a.status) - order.indexOf(b.status);
						});
						
				
						$.each(chapters,function(key){
							if (key > 0) {
								// Push current and previous chapters (with status check)
								for (let i = key; i > 0; i--) {
									let chapter = chapters[i - 1];
									// Only include if status is not 1 or 8
									if (chapter['status'] != 1 && chapter['status'] != 8) {
										let chapterName = chapter['chapter_name'];
										let chapterId = chapter['chapter_id'];

										// Avoid duplicates
										let exists = scheduled.some(item => item.chapter_name === chapterName);
										if (!exists) {
											scheduled.push({
												chapter_name: chapterName,
												cid: chapterId
											});
										}
									}
								}
							}
							
							chapter_topic_list+=`<tr data-batch-id="${batch_id}" data-status="${chapters[key]['status']}" class="tr_chapter_${chapters[key]['chapter_id']}">`;
							
							
							chapter_topic_list += `
							  <td>
								<span data-id="${chapters[key]['chapter_id']}" data-title="${chapters[key]['chapter_name']}" class="get_topic_class">
								  ${chapters[key]['chapter_name']}
								</span>
							  </td>`;
							
							chapter_topic_list+=`<td>`+chapters[key]['duration']+`</td>`;
							
							let duration = parseFloat(chapters[key]['duration']) || 0;
							let spent_hours = parseFloat(chapters[key]['spent_hours']) || 0;

							let tdClass = '';

							if (spent_hours === 0) {
								tdClass = '';
							} 
							else if (spent_hours <= duration) {
								tdClass = 'green';
							} else {
								let percentDiff = ((spent_hours - duration) / duration) * 100;
								if (percentDiff <= 10) {
									tdClass = 'yellow';
								} else {
									tdClass = 'red';
								}
							}

							chapter_topic_list += `<td class="${tdClass}">` + chapters[key]['spent_hours'] + `</td>`;

							
							
							var status='';
							if(chapters[key]['status']==null || chapters[key]['status']==0){
								status="<span class='btn btn-sm btn-danger mb-2 nextClass' data-action='schedule' data-tt-id='"+tt_id+"' data-topic-id='"+chapters[key]['id']+"' data-chapter-id='"+chapters[key]['chapter_id']+"' data-batch-id='"+batch_id+"' data-subject-id='"+subject_id+"' data-batch-ids='"+batch_ids+"' data-class_duration='"+class_duration+"' data-schedule-length='"+JSON.stringify(scheduled)+"' data-cbatch-ids='0'><b>Schedule in Nextclass</b></span><br>";
								status+="<span class='text-danger'><b>Pending</b></span>";
								topic_pending++;
							}else if(chapters[key]['status']==1){
								status="<span class='text-success'><b>Completed</b></span>";
								topic_completed++;
							}else if(chapters[key]['status']==2){
								status="<span class='btn btn-sm btn-outline-dark mb-2 nextClass' data-action='schedule' data-tt-id='"+tt_id+"' data-topic-id='"+chapters[key]['id']+"' data-chapter-id='"+chapters[key]['chapter_id']+"' data-batch-id='"+batch_id+"' data-subject-id='"+subject_id+"' data-batch-ids='"+batch_ids+"' data-class_duration='"+class_duration+"' data-schedule-length='"+JSON.stringify(scheduled)+"' data-cbatch-ids='0'><b>Schedule in Nextclass</b></span><br>";
								status+="<span class='text-warning'><b>Partially Completed</b></span>";
								topic_partially++;
							}else if(chapters[key]['status']==7){
								if(chapters[key]['timetable_id']==tt_id){
									status="<span class='text-primary'><b>Scheduled for Nextclass</b></span><br>";
									// status+="<span class='btn btn-sm btn-outline-danger mt-2 nextClass' data-action='cancel' data-tt-id='"+tt_id+"' data-topic-id='"+chapters[key]['id']+"' data-chapter-id='"+chapters[key]['chapter_id']+"' data-batch-id='"+batch_id+"' data-subject-id='"+subject_id+"' data-batch-ids='"+batch_ids+"' data-schedule-length=''><b>Cancel Topic</b></span>";
								
									topic_partially++;
								}else{
									status="<span class='btn btn-sm btn-danger mb-2 nextClass' data-action='schedule' data-tt-id='"+tt_id+"' data-topic-id='"+chapters[key]['id']+"' data-chapter-id='"+chapters[key]['chapter_id']+"' data-batch-id='"+batch_id+"' data-subject-id='"+subject_id+"' data-batch-ids='"+batch_ids+"' data-class_duration='"+class_duration+"' data-schedule-length='"+JSON.stringify(scheduled)+"' data-cbatch-ids='0'><b>Schedule in Nextclass</b></span><br>";
									status+="<span class='text-danger'><b>Partially Completed</b></span>";
									topic_pending++;
								}
							}else if(chapters[key]['status']==8){
								status="<span class='btn btn-sm btn-outline-dark mb-2 nextClass' data-action='schedule' data-tt-id='"+tt_id+"' data-topic-id='"+chapters[key]['id']+"' data-chapter-id='"+chapters[key]['chapter_id']+"' data-batch-id='"+batch_id+"' data-subject-id='"+subject_id+"' data-batch-ids='"+batch_ids+"' data-class_duration='"+class_duration+"' data-schedule-length='"+JSON.stringify(scheduled)+"' data-cbatch-ids='0'><b>Schedule in Nextclass (Dropped)</b></span><br>";
								status+="<span class='text-warning'><b>Partially Completed</b></span>";
							}
							
							if (key === chapters.length - 1  && chapters[key]['status']== 7) {
								status+="<span class='btn btn-sm btn-outline-dark mb-2 nextClass' data-action='Completed' data-tt-id='"+tt_id+"' data-topic-id='"+chapters[key]['id']+"' data-chapter-id='"+chapters[key]['chapter_id']+"' data-batch-id='"+batch_id+"' data-subject-id='"+subject_id+"' data-batch-ids='"+batch_ids+"' data-class_duration='"+class_duration+"' data-schedule-length='"+JSON.stringify(scheduled)+"' data-cbatch-ids='0'><b>Mark Completed</b></span><br>";
								
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

	$(document).on("change",".batche_planner",function(){
        $(".planner_detail").addClass("d-none");
        $(".planner_"+$(this).val()).removeClass("d-none");
	});

	$(document).on("keypress",".topic_issue",function(){
        if($(this).val().length>10){
        	$(".raise_issue_btn").attr("disabled",false);
        	$(".raise_issue_btn").addClass("btn-primary");
        	$(".raise_issue_btn").removeClass("btn-outline-dark");
        }else{
        	$(".raise_issue_btn").attr("disabled",true);
        	$(".raise_issue_btn").removeClass("btn-primary");
        	$(".raise_issue_btn").addClass("btn-outline-dark");
        	
        }

        $(".topic_issue_chr_limit").text($(this).val().length+"/200");
	});
	

	$(document).on("click",".nextClass",function(){
		var _this=$(this);
		var action=$(this).attr('data-action');
		var timetable_id=$(this).attr('data-tt-id');
		var topic_id=$(this).attr('data-topic-id');
		var chapter_id=$(this).attr('data-chapter-id');
		var batch_id=$(this).attr('data-batch-id');
		var subject_id=$(this).attr('data-subject-id');
		
       
		var batch_ids=$(this).attr('data-batch-ids');
		batch_ids=str = batch_ids.replace(/,\s*$/, "");
		batch_ids=0;
		var master_planner_id=$(this).attr('data-master-ids');
		if (!master_planner_id) {
			master_planner_id = 0;
		}
	   
		var cbatch_id=$(this).attr('data-cbatch-ids');
		if (cbatch_id && cbatch_id.trim() !== "") {
			batch_ids = cbatch_id;
		}
		
		
		if(action=='schedule'){
			var dataAttr = $(this).attr('data-schedule-length');
			
			var $button = $(this);

			if (dataAttr && dataAttr.trim() !== '') {
				var schedule_length = JSON.parse(dataAttr);
				if (schedule_length.length > 0) {
					$('#classActionModal').modal('show');

					let html = '<ul style="padding: 15px 15px 0px 15px;margin: 0;">';
					schedule_length.forEach(function(item) {
						html += `<li>${item.chapter_name} (ID: ${item.cid})</li>`;
					});
					html += '</ul>';
					$('#classActionModal .append_chapter').html(html);
					
					
					var newhtml = `<button type="button" class="btn btn-success mb-2 nextClass" data-action="completed" data-tt-id="${timetable_id}" data-topic-id="${topic_id}" data-chapter-id="${chapter_id}" data-batch-id="${batch_id}" data-subject-id="${subject_id}" data-batch-ids="${batch_ids}" data-cbatch-ids="${cbatch_id}" data-master-ids="${master_planner_id}" data-class_duration="0">✅ Completed</button>
						<button type="button" class="btn btn-warning mb-2 nextClass" data-action="dropped" data-tt-id="${timetable_id}" data-topic-id="${topic_id}" data-chapter-id="${chapter_id}" data-batch-id="${batch_id}" data-subject-id="${subject_id}" data-batch-ids="${batch_ids}" data-cbatch-ids="${cbatch_id}" data-master-ids="${master_planner_id}" data-class_duration="0">⚠️ Dropped</button>`;

					$('#classActionModal .modal-footer').html(newhtml);
					
					return;
				}
			}
		}
       
       console.log(timetable_id+topic_id+'-'+batch_id+'-'+subject_id);
       //return;
       var $button = $(this);

        $.ajax({
        	headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}, 
			type: "POST",
			url : '{{ route('scheduleNextTopic') }}',
			data:{action,timetable_id,topic_id,chapter_id,subject_id,batch_id,batch_ids,master_planner_id},
			dataType : 'json',
			success : function(data){
				$('#classActionModal').modal('hide');
				
				if(action=='schedule'){
					$button.attr('data-action', 'cancel');
					$button.html('<b>Cancel Schedule</b>');
					$(_this).closest('td').html($button[0].outerHTML+"<br><span class='text-primary mt-2'>Scheduled for Next Class</span>");
				}else{
					$button.attr('data-action', 'schedule');
					$button.html('<b>Schedule in Nextclass</b>');
					$(_this).closest('td').html($button[0].outerHTML+"<br><span class='text-danger mt-2'>Pending</span>");
				}
				
				if(data.status){
					$("#statusModel").modal('hide');
					$("#statusModel").one('hidden.bs.modal', function() {
						$(".viewStatus_" + timetable_id).trigger('click');
					});
				    $(".viewStatus_"+timetable_id).click();
			    	swal("Success", data.message, "success");
			    	//window.location.reload();
			    }else{
			    	swal("Error!", data.message, "error");
			    }
			}
		});
	});


	$(document).on("click",".raise_issue",function(){
        var id=$(this).attr("data-id");
        $("#modalRaiseIssue .timetable_id").val(id);
	});

	$(".raise_issue_btn").on("click",function(){
		var timetable_id=$("#modalRaiseIssue .timetable_id").val();
		var topic_issue=$("#modalRaiseIssue .topic_issue").val();
		
		if(topic_issue.length<10 || topic_issue.length>300){
           alert("Please Enter Proper Issue why topic is wrong.");
          return;
		}

        $.ajax({
        	headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}, 
			type: "POST",
			url : '{{ route('raise_issue_on_topic') }}',
			data:{"timetable_id":timetable_id,"topic_issue":topic_issue},
			dataType : 'json',
			success : function(data){
			    if(data.status){
			    	swal("Success", data.message, "success");
			    	$('#modalRaiseIssue').modal('toggle');
			    	$("#modalRaiseIssue .topic_issue").val('');
			    }else{
			    	swal("Error!", data.message, "error");
			    }
			}
		});
	});
	
	// On typing in search box
	$(document).on('keyup', '.search_now', function () {
	    var value = $(this).val().toLowerCase();
	    var $table = $(this).closest('table'); // current table

	    $table.find("tbody tr").each(function (index) {
	        // skip 1st and 2nd rows (search + heading)
	        if (index > 1) {
	            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
	        }
	    });
	});
	
	
	$(document).on("click", ".get_topic_class", function () {
		let chapterId = $(this).data("id"); 
		let batchID = $(this).closest('tr').data('batch-id');
		let dataTitle = $(this).data("title"); 
				
		$('#exampleModal .modal-title').text(dataTitle);
		$("#exampleModal").modal("show");
		
		$.ajax({
        	headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}, 
			type: "POST",
			url : '{{ route('get_topic_class') }}',
			data:{"chapterId":chapterId,"batchID":batchID},
			dataType : 'json',
			success : function(data){
				console.log(data);
			    $('.classData').html(data.html);
			}
		});
	});
	
	$(document).on("click", "#closeModalBtn", function() {
		$("#exampleModal").modal("hide");
	});

</script>
@endsection
