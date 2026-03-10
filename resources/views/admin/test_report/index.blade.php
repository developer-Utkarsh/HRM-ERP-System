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
						<h2 class="content-header-title float-left mb-0">Test Batch Report</h2>
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
								<form action="{{ route('admin.batch-test-report') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-md-3">
											<label for="users-list-status">Batch</label>
											<?php $batchs = \App\Batch::where('status', '1')->where('is_deleted', '0')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2 batch_id" name="batch_id">
													<option value="">Select Any</option>
													@if(count($batchs) > 0)
													@foreach($batchs as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('batch_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										
										<div class="col-12 col-md-4 mt-2">
										<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('admin.batch-test-report') }}" class="btn btn-warning">Reset</a>
										</fieldset>
									</div>
									</div>
									
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
				
				<?php 
				$dataFound = 0;
				if(!empty($get_batches)){
				$batchArray = $get_batches;
						?>
						<table class="table data-list-view" style=''>
							<head>
								<tr style="">
									<th colspan="3"><h3>Batch Name : <?php echo $batchArray->name; ?> </h3></th>
								</tr>
							</head>
							<body>
							<tr style="">
							<td style="border: 1px solid;">
						
						<table class="table data-list-view" style=''>
						 
							<head>
								<tr style="">
									<th scope="col">Title</th>
									<?php
									$questions = array(
													'batch'=>'1. बैच का स्थान व बैच का नाम',
													'subject'=>'2. टेस्ट का विषय',
													'date'=>'3. टेस्ट लेने की दिनांक- ',
													'q1'=>'4. बैच में विद्यार्थियों की कुल संख्या-',
													'q2'=>'5. टेस्ट देने वालों की संख्या ',
													'q3'=>'6. इस टेस्ट की सूचना कौनसी तारीख़ को दी गई व किसने',
													'q5'=>'7. टेस्ट में गलत प्रश्न उत्तर, सिलेबस के बाहर के प्रश्न, क्रमांक में ग़लतियाँ थी क्या ? विस्तार से बतायें',
													'q6'=>'8. टेस्ट का समय व प्रश्नों की संख्या? ',
													'q7'=>'9. क्या टेस्ट द्विभाषी था ',
													'q8'=>'10. टेस्ट का रिज़ल्ट कौनसी तारीख़ को घोषित करेंगे -',
													'q9'=>'11. इस टेस्ट के बारे में विद्यार्थियों के सुझाव या शिकायत हो तो बतायें ',
													'q10'=>'12. इस बैच में इससे पहले कितने टेस्ट हो चुके है',
													'q11'=>'13. क्या ये टेस्ट ऑनलाइन विद्यार्थियों को भी दिया गया ठीक समय पर',
													'q12'=>'14. टेस्ट की विस्तृत व्याख्या व विडियो हल एप में उपलब्ध करवाया ?',
													'q13'=>'15. टेस्ट के समय कक्षा में कौन-कौन स्टाफ़ व टीम लीडर उपस्थित थे-',
													'q14'=>'16. प्रश्न पुस्तिका की कितनी सीरिज थी',
													'q15'=>'17. टेस्ट पेपर में कुल कितने पेज थे',
													'q16'=>'18. गलत प्रश्न/सिलेबस से बाहर के प्रश्न का स्क्रीन शॉट शेअर करें।',
													'q17'=>'19. इस बैच में विद्यार्थियों को टेस्ट देने के लिए व ईमानदारी से देने के लिए तथा टेस्ट की महत्ता बताने के लिए कभी कोई टीम लीडर आये ? (कौन आये व कौनसी तारीख़ को आये) ',
													'q18'=>'20. क्या टेस्ट में व्यापकता का गुण था ? अर्थात् जितने पाठ्यक्रम में से टेस्ट लेने के लिए कहा गया उनमें से सभी टॉपिक्स में से प्रश्न आये टेस्ट में',
													'q19'=>'21. क्या टेस्ट में कोई प्रश्न रिपीट हुआ ? (अगर हुआ तो विस्तार से बतायें)',
													'q20'=>'22. इस टेस्ट पेपर को किसने फाईनल करके फोटोकॉपी/प्रेस में भेजा',
												);
									$dates = array();
									foreach ($batchArray->batch_timetables as $value) {
										$question_ans = array();
										$get_test_report = DB::table('test_report')
													->select('test_report.*')
													->where('tt_id', $value->id)
													->first();
										if(!empty($get_test_report)){
											$question_ans = array(
																'batch'=>$batchArray->name,
																'subject'=>$value->subject->name,
																'date'=>isset($value->cdate) ?  date('d-F-Y',strtotime($value->cdate)) : '',
																'q1'=>$get_test_report->q1,
																'q2'=>$get_test_report->q2,
																'q3'=>$get_test_report->q3,
																'q4'=>$get_test_report->q4,
																'q5'=>$get_test_report->q5,
																'q6'=>$get_test_report->q6,
																'q7'=>$get_test_report->q7,
																'q8'=>$get_test_report->q8,
																'q9'=>$get_test_report->q9,
																'q10'=>$get_test_report->q10,
																'q11'=>$get_test_report->q11,
																'q12'=>$get_test_report->q12,
																'q13'=>$get_test_report->q13,
																'q14'=>$get_test_report->q14,
																'q15'=>$get_test_report->q15,
																'q16'=>$get_test_report->q16,
																'q17'=>$get_test_report->q17,
																'q18'=>$get_test_report->q18,
																'q19'=>$get_test_report->q19,
																'q20'=>$get_test_report->q20,
																);
										}
										
										$dates[$value->cdate] = $question_ans;
									}
									?>
									
									<?php
									// echo "<pre>"; print_R($dates); die;
									if(!empty($dates)){
										foreach($dates as $key=>$datesVal){
										?>
											<th scope="col">{{$key}}</th>
										<?php 
										} 
									} ?>
									
								</tr>
							</head>
							<body>
								<?php
								if(!empty($dates)){
									
									?>
									
									<?php
										foreach($questions as $key=>$questionVal){
											// print_R($datesVal); die; 	
										?>
											
										<tr>
											<td><?=$questionVal?></td>
											<?php
											foreach($dates as $date_key=>$val){
												?>
												<td>
												<?php
												if($key!='q16')	{
													echo isset($dates[$date_key][$key])?$dates[$date_key][$key]:''; 
												}
												
												if($key=='q3'){
													echo isset($dates[$date_key]['q4'])? " , ".$dates[$date_key]['q4']:'';
												}
												else if($key=='q16'){
													if(!empty($dates[$date_key]['q16'])){
														$screenKey = 0;
														foreach(json_decode($dates[$date_key]['q16']) as $key=>$screenval){
															$screenKey++;
															$asset = asset("laravel/public/timetable_test/$screenval");
															echo "<a target='_blank' href='$asset' class='btn btn-sm btn-primary'>Screen $screenKey</a>&nbsp;";
														}
													}
												}
												?>
												
												</td>
												<?php
											}
											?>
											
										
										</tr>
										<?php 
										}
									?>
									
									<?php
								} ?>
							</body>
						
						</table>
				<p><hr/></p>
						
				</td>
				</tr>
				</body>
				</table>
				<?php
				}
				else{
					if(empty(app('request')->input('batch_id'))){
						?>
						 
						<?php
					}
					else{
						?>
						<p style="text-align:center;"><h3>Data not found.</h3></p>
						<?php
					}
				}?>
		<style>
		hr{background:#000;}
		</style>
					 
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
	});
	
	$("body").on("click", "#download_excel", function (e) {
		var data = {};
		data.branch_location = $('.branch_location').val(),
		data.studio_id = $('.studio_id').val(),
		data.branch_id = $('.branch_id').val(),
		data.batch_id = $('.batch_id').val(),
		data.assistant_id = $('.assistant_id').val(),
		data.fdate = $('.fdate').val(),
		data.tdate = $('.tdate').val(),
		data.type = $('.type').val(),
		window.location.href = "<?php echo URL::to('/admin/'); ?>/batch-report-report-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});
	
	$("body").on("click", "#download_pdf", function (e) {
		/* if ($userTable.data().count() == 0) {
			swal("Warning!", "Not have any data!", "warning");
			return;
		} */
		var data = {};
			data.branch_location = $('.branch_location').val(),
			data.studio_id = $('.studio_id').val(),
			data.branch_id = $('.branch_id').val(),
			data.batch_id = $('.batch_id').val(),
			data.assistant_id = $('.assistant_id').val(),
			data.fdate = $('.fdate').val(),
			data.tdate = $('.tdate').val(),
			data.type = $('.type').val(),
			window.open("<?php echo URL::to('/admin/'); ?>/batch-report-pdf?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&'));
		/* window.location.href = "<?php echo URL::to('/admin/'); ?>/studio-report-pdf?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&'); */
	});
</script>
<script type="text/javascript">
	$(".branch_id").on("change", function () {
		var branch_id = $(".branch_id option:selected").attr('value');
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
					$('.studio_id').empty();
					$('.studio_id').append(data);
				}
			});
			
			$.ajax({
				beforeSend: function(){
					// $(".branch_loader i").show();
				},
				type : 'POST',
				url : '{{ route('admin.get-branchwise-assistant') }}',
				data : {'_token' : '{{ csrf_token() }}', 'branch_id': branch_id, 'assistant_id': assistant_id},
				dataType : 'html',
				success : function (data){
					// $(".branch_loader i").hide();
					$('.assistant_id').empty();
					$('.assistant_id').append(data);
				}
			});
			
			
		}
	});
	
	$(document).ready(function() {
		var branch_id = $(".branch_id option:selected").attr('value');
		var assistant_id = $(".assistant_id_get").val();
		if (branch_id) {
			$.ajax({
				beforeSend: function(){
					// $(".branch_loader i").show();
				},
				type : 'POST',
				url : '{{ route('admin.get-branchwise-assistant') }}',
				data : {'_token' : '{{ csrf_token() }}', 'branch_id': branch_id, 'assistant_id': assistant_id},
				dataType : 'html',
				success : function (data){
					// $(".branch_loader i").hide();
					$('.assistant_id').empty();
					$('.assistant_id').append(data);
				}
			});
		}
	});
	
	$(".branch_location").on("change", function () {
		var b_location = $(this).val();
		if (b_location) {
			$.ajax({
				beforeSend: function(){
					// $(".branch_loader i").show();
				},
				type : 'POST',
				url : '{{ route('admin.get-location-wise-branch') }}',
				data : {'_token' : '{{ csrf_token() }}', 'b_location': b_location},
				dataType : 'html',
				success : function (data){
					// $(".branch_loader i").hide();
					$('.branch_id').empty();
					$('.branch_id').append(data);
				}
			});
			
		}
	});
	
</script>
@endsection
