@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-md-8">
						<h2 class="content-header-title float-left mb-0">Fee Recovery Dashboard</h2>
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
			<section id="multiple-column-form">
				<div class="match-height">
					<form action="{{ route('admin.academic.index') }}" method="get">
						@csrf
						<div class="row mx-0 p-1">
							<?php $query_category=''; if($designation=='CATEGORY HEAD'){ ?>
								<div class="col-2">
									<select class="form-control select_category_name select-multiple1" name="category">
										<option value=""> -- Select All -- </option>
										<?php
										$query_category=$_GET['category']??'';
										if(!empty($erp_category) && is_array($erp_category)){ 
											for($i=0;$i<count($erp_category);$i++){?>
												<option value="{{$erp_category[$i]}}" @if($erp_category[$i]==$query_category) selected @endif>{{$erp_category[$i]}}</option>
										    <?php }
										} 
										?>
									</select>
								</div>
							<?php } ?>
								
							<div class="col-2">
								<select class="form-control f_year" name="f_year">
									<option value=""> -- Select -- </option>
									<option value="2024-04-01&2025-03-31" @if($f_year=='2024-04-01&2025-03-31') selected @endif > 2024-2025 </option>
									<option value="2023-04-01&2024-03-31" @if($f_year=='2023-04-01&2024-03-31') selected @endif > 2023-2024 </option>
									<option value="2022-04-01&2023-03-31" @if($f_year=='2022-04-01&2023-03-31') selected @endif > 2022-2023 </option>
									<option value="2021-04-01&2022-03-31" @if($f_year=='2021-04-01&2022-03-31') selected @endif > 2021-2022 </option>
									<option value="2020-04-01&2021-03-31" @if($f_year=='2020-04-01&2021-03-31') selected @endif > 2020-2021 </option>
									<option value="2019-04-01&2020-03-31" @if($f_year=='2019-04-01&2020-03-31') selected @endif > 2019-2020 </option>
									<option value="2018-04-01&2019-03-31" @if($f_year=='2018-04-01&2019-03-31') selected @endif > 2018-2019 </option>
								</select>
							</div>

							<div class="col-4">
								<button type="submit" class="btn btn-primary">Search</button>
								<a href="{{ route('admin.academic.index') }}" class="btn btn-warning">Reset</a>
							</div>
						</div>
					</form>
					
					<div class="">						
						<!-- First -->
						
						<div class="card @if($is_cxo==0) d-none @endif">
							<div class="card-content">
								<div class="card-body">
									<div class="table-responsive">
										<table class="table data-list-view">
											<thead>
												<tr>
													<th>City</th>
													<th>Enrolled</th>
													<th>Fee Booked</th>
													<th>Fee Collected</th>
													<th>Total Dues Fee</th>
													<th>Total Due Student</th>
													<th>Over Dues Fee</th>
													<th>Over Dues Student</th>

												</tr>
											</thead>
											<tbody>
												
												<?php 
													function convert_rupee($amount){
														//return $amount;
														$length = strlen($amount);
														if($length>=4 && $length <=5){
															$amount=round($amount/1000,4).' K';
														}else if($length>=6 && $length <=7){
															$amount=round($amount/100000,4).' Lac(s)';
														}else if($length>=8 && $length <=9){
															$amount=round($amount/10000000,4).' Cr.';
														}else if($length>=10 && $length <=15){
															$amount=round($amount/1000000000,4).' B.';
														}
														
														return $amount;
													}
													
													$enrolled=$fee_booked=$fee_collected=$total_due=$total_due_student = 0;
													$over_due_amount=$over_due_student=0;
													$pecentage_collected = 1;
													foreach($data as $val){
												?>
												<tr>
													<td style="background:#FFE9D4">{{ $val->branch??'' }}</td>
													<td>{{ convert_rupee($val->enrolled??'') }}</td>
													<td>{{ convert_rupee($val->fee_booked)??'' }}</td>
													<td>{{ convert_rupee($val->fee_collected??'') }}</td>
													<td>{{ convert_rupee($val->due_amount) }}</td>
													<td>{{ convert_rupee($val->total_due_student??'') }}</td>

													<td>{{ convert_rupee($val->overdue_due_amount) }}</td>
													<td>{{ convert_rupee($val->overdue_due_student) }}</td>
												</tr>
												<?php 
													$enrolled += $val->enrolled;
													$fee_booked += $val->fee_booked;
													$fee_collected += $val->fee_collected;
													$total_due	+= $val->fee_booked-$val->fee_collected;
													$total_due_student += $val->total_due_student;
													$over_due_amount += $val->overdue_due_amount;
													$over_due_student += $val->overdue_due_student;
													
												} 
													
													$fee_booked=$fee_booked>0?$fee_booked:1;
													
													$pecentage_collected = round(($fee_collected/$fee_booked)*100,2);
												?>
												<tr style="background: #E9E9FF 0% 0% no-repeat padding-box;">
													<td>Overall</td>
													<td>{{ convert_rupee($enrolled) }}</td>
													<td>{{ convert_rupee($fee_booked) }}</td>
													<td>{{ convert_rupee($fee_collected) }}</td>
													<td>{{ convert_rupee($total_due) }}</td>
													<td>{{ convert_rupee($total_due_student) }}</td>
													<td>{{ convert_rupee($over_due_amount) }}</td>
													<td>{{ convert_rupee($over_due_student) }}</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
						
						<?php if($is_cxo==0){ ?>
						<div class="row text-center dashboard">
							<div class="col card m-1 p-2" style="background:#E6E4FF">
								<b>{{ convert_rupee($enrolled) }}</b>
								Total Enrolled
							</div>
							<div class="col card m-1 p-2" style="background:#FEFFE9">
								<b>{{ convert_rupee($fee_booked) }}</b>
								FEE Booked
							</div>
							<div class="col card m-1 p-2" style="background:#F1F7FF">
								<b>{{ convert_rupee($fee_collected) }}</b>
								FEE Collected
							</div>
							<div class="col card m-1 p-2" style="background:#FFD9D9">
								<b>{{ $pecentage_collected }} %</b>
								Percentage of Collection
							</div>
							<div class="col card m-1 p-2" style="background:#DDFCE6">
								<b>{{ convert_rupee($total_due) }}</b>
								Total Dues Fees
							</div>
							<div class="col card m-1 p-2" style="background:#EFE5FF">
								<b>{{ convert_rupee($total_due_student) }}</b>
								Total Due Students
							</div>
						</div>
						<?php } ?>
						
						<!-- Second -->
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<div class="pb-1"><b>DUE FEE DETAILS</b></div>
									<div class="table-responsive">
										<table class="table data-list-view text-center">
											<thead>
												<tr>
													@if($designation!='CITY HEAD')
													  <th rowspan="2">City</th>
													@endif

													@if($designation!='CATEGORY HEAD')
													  <th rowspan="2">Category</th>
													@endif

													<th colspan="2">0-10 Days</th>
													<th colspan="2">10-30 Days</th>
													<th colspan="2">30-60 Days</th>
													<th colspan="2">60+ Days</th>													
												</tr>
												<tr>
													<th>Student</th>
													<th>Amount</th>	
													<th>Student</th>
													<th>Amount</th>
													<th>Student</th>
													<th>Amount</th>
													<th>Student</th>
													<th>Amount</th>												
												</tr>
											</thead>
											<tbody>
												<?php 
												$slab_1_count=$slab_1_amount=$slab_2_count=$slab_2_amount=$slab_3_count=$slab_3_amount=$slab_4_count=$slab_4_amount=0;
												// Process data to determine row spans
												$rowspans = [];
												$location_counts=[];
												foreach($data2 as $row) {
												    $rowspans[$row->branch][] = $row;
												}

												foreach($rowspans as $branch => $categories){ 
													$rowspan = count($categories);
													$first_row = true;

													$location_counts[$branch]['slab_1_count']=0;
													$location_counts[$branch]['slab_1_amount']=0;
													$location_counts[$branch]['slab_2_count']=0;
													$location_counts[$branch]['slab_2_amount']=0;
													$location_counts[$branch]['slab_3_count']=0;
													$location_counts[$branch]['slab_3_amount']=0;
													$location_counts[$branch]['slab_4_count']=0;
													$location_counts[$branch]['slab_4_amount']=0;

													foreach($categories as $da){?>
														<tr>
															@if($designation!='CITY HEAD')
															<?php if($first_row) { ?>
													           <td style="background:#FFE9D4" rowspan='{{$rowspan}}'>{{$da->branch}}</td>
													        <?php $first_row = false; } ?>
													        @endif

													        @if($designation!='CATEGORY HEAD')
															 <td style="background:#FFE9D4">{{ $da->category }}</td>
															@endif

															@if($designation=='CATEGORY HEAD')
															  <?php $da->category=$query_category;?>
															@endif

															@if($designation=='CITY HEAD')
															 <?php $da->branch='';?>
															@endif

															<td><a href="{{ route('admin.academic.get-academic-student') }}?branch={{ $da->branch }}&category={{ $da->category }}&slab=1&type=pastdays">{{ $da->slab_1_count }}</a></td>
															<td>{{ $da->slab_1_amount }}</td>
															<td><a href="{{ route('admin.academic.get-academic-student') }}?branch={{ $da->branch }}&category={{ $da->category }}&slab=2&type=pastdays">{{ $da->slab_2_count }}</a></td>
															<td>{{ $da->slab_2_amount }}</td>
															<td><a href="{{ route('admin.academic.get-academic-student') }}?branch={{ $da->branch }}&category={{ $da->category }}&slab=3&type=pastdays">{{ $da->slab_3_count }}</a></td>
															<td>{{ $da->slab_3_amount }}</td>
															<td><a href="{{ route('admin.academic.get-academic-student') }}?branch={{ $da->branch }}&category={{ $da->category }}&slab=4&type=pastdays">{{ $da->slab_4_count }}</a></td>
															<td>{{ $da->slab_4_amount }}</td>
														</tr>
														<?php 
														$slab_1_count +=  $da->slab_1_count;
														$slab_1_amount +=  $da->slab_1_amount;
														$slab_2_count +=  $da->slab_2_count;
														$slab_2_amount +=  $da->slab_2_amount;
														$slab_3_count +=  $da->slab_3_count;
														$slab_3_amount +=  $da->slab_3_amount;
														$slab_4_count +=  $da->slab_4_count;
														$slab_4_amount +=  $da->slab_4_amount;

														$location_counts[$branch]['slab_1_count']+=$da->slab_1_count;
														$location_counts[$branch]['slab_1_amount']+=$da->slab_1_amount;
														$location_counts[$branch]['slab_2_count']+=$da->slab_2_count;
														$location_counts[$branch]['slab_2_amount']+=$da->slab_2_amount;
														$location_counts[$branch]['slab_3_count']+=$da->slab_3_count;
														$location_counts[$branch]['slab_3_amount']+=$da->slab_3_amount;
														$location_counts[$branch]['slab_4_count']+=$da->slab_4_count;
														$location_counts[$branch]['slab_4_amount']+=$da->slab_4_amount;
													} ?>
                                                    
                                                    @if($is_cxo==1)
														<tr style="background: #E9E9FF 0% 0% no-repeat padding-box;font-weight:bold;">
															<td>{{$branch}}</td>
													        <td>OverAll</td>
															<td><a href="{{ route('admin.academic.get-academic-student') }}?branch={{ $branch }}&slab=1&type=pastdays">{{ $location_counts[$branch]['slab_1_count']??'' }}</a></td>
															<td>{{ $location_counts[$branch]['slab_1_amount'] }}</td>

															<td><a href="{{ route('admin.academic.get-academic-student') }}?branch={{ $branch }}&slab=2&type=pastdays">{{ $location_counts[$branch]['slab_2_count']??'' }}</a></td>
															<td>{{ $location_counts[$branch]['slab_2_amount'] }}</td>

															<td><a href="{{ route('admin.academic.get-academic-student') }}?branch={{ $branch }}&slab=3&type=pastdays">{{ $location_counts[$branch]['slab_3_count']??'' }}</a></td>
															<td>{{ $location_counts[$branch]['slab_3_amount'] }}</td>

															<td><a href="{{ route('admin.academic.get-academic-student') }}?branch={{ $branch }}&slab=4&type=pastdays">{{ $location_counts[$branch]['slab_4_count']??'' }}</a></td>
															<td>{{ $location_counts[$branch]['slab_4_amount'] }}</td>
														</tr>
													@endif
												<?php } ?>
												
												<tr style="background: #E9E9FF 0% 0% no-repeat padding-box;font-weight:bold;">
													<td colspan="@if($is_cxo==1) 2 @endif">Overall</td>
													<td>{{ convert_rupee($slab_1_count) }}</td>
													<td>{{ convert_rupee($slab_1_amount) }}</td>
													<td>{{ convert_rupee($slab_2_count) }}</td>
													<td>{{ convert_rupee($slab_2_amount) }}</td>
													<td>{{ convert_rupee($slab_3_count) }}</td>
													<td>{{ convert_rupee($slab_3_amount) }}</td>
													<td>{{ convert_rupee($slab_4_count) }}</td>
													<td>{{ convert_rupee($slab_4_amount) }}</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
						
						<!-- Upcomming -->
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<div class="pb-1"><b>UPCOMING DUE FEE DETAILS</b></div>
									<div class="table-responsive">
										<table class="table data-list-view text-center">
											<thead>
												<tr>
													@if($designation!='CITY HEAD')
													  <th rowspan="2">City</th>
													@endif

													@if($designation!='CATEGORY HEAD')
													  <th rowspan="2">Category</th>
													@endif

													<th colspan="2">In Next 3 Days</th>
													<th colspan="2">In Next 7 Days</th>
													<th colspan="2">In Next 15 Days</th>
													<th colspan="2">In Next 30 Days</th>													
												</tr>
												<tr>
													<th>Student</th>
													<th>Amount</th>	
													<th>Student</th>
													<th>Amount</th>
													<th>Student</th>
													<th>Amount</th>
													<th>Student</th>
													<th>Amount</th>												
												</tr>
											</thead>
											<tbody>
												
												<?php 
												$slab_1_count=$slab_1_amount=$slab_2_count=$slab_2_amount=$slab_3_count=$slab_3_amount=$slab_4_count=$slab_4_amount=0;
												// Process data to determine row spans
												$rowspans = [];
												$location_counts=[];
												foreach($data3 as $row) {
												    $rowspans[$row->branch][] = $row;
												}

												foreach($rowspans as $branch => $categories){ 
													$rowspan = count($categories);
													$first_row = true;

													$location_counts[$branch]['slab_1_count']=0;
													$location_counts[$branch]['slab_1_amount']=0;
													$location_counts[$branch]['slab_2_count']=0;
													$location_counts[$branch]['slab_2_amount']=0;
													$location_counts[$branch]['slab_3_count']=0;
													$location_counts[$branch]['slab_3_amount']=0;
													$location_counts[$branch]['slab_4_count']=0;
													$location_counts[$branch]['slab_4_amount']=0;

													foreach($categories as $da){ ?>
														<tr>
															@if($designation!='CITY HEAD')
																<?php if($first_row) { ?>
														           <td style="background:#FFE9D4" rowspan='{{$rowspan}}'>{{$da->branch}}</td>
														        <?php $first_row = false; } ?>
														    @endif

														    @if($designation!='CATEGORY HEAD')
															  <td style="background:#FFE9D4">{{ $da->category }}</td>
															@endif

															@if($designation=='CATEGORY HEAD')
															 <?php $da->category=$query_category;?>
															@endif

															@if($designation=='CITY HEAD')
															 <?php $da->branch='';?>
															@endif

															<td><a href="{{ route('admin.academic.get-academic-student') }}?branch={{ $da->branch }}&category={{ $da->category }}&slab=1&type=upcomming">{{ $da->slab_1_count }}</a></td>
															<td>{{ $da->slab_1_amount }}</td>
															<td><a href="{{ route('admin.academic.get-academic-student') }}?branch={{ $da->branch }}&category={{ $da->category }}&slab=2&type=upcomming">{{ $da->slab_2_count }}</a></td>
															<td>{{ $da->slab_2_amount }}</td>
															<td><a href="{{ route('admin.academic.get-academic-student') }}?branch={{ $da->branch }}&category={{ $da->category }}&slab=3&type=upcomming">{{ $da->slab_3_count }}</a></td>
															<td>{{ $da->slab_3_amount }}</td>
															<td><a href="{{ route('admin.academic.get-academic-student') }}?branch={{ $da->branch }}&category={{ $da->category }}&slab=4&type=upcomming">{{ $da->slab_4_count }}</a></td>
															<td>{{ $da->slab_4_amount }}</td>
														</tr>

														<?php 
														$slab_1_count +=  $da->slab_1_count;
														$slab_1_amount +=  $da->slab_1_amount;
														$slab_2_count +=  $da->slab_2_count;
														$slab_2_amount +=  $da->slab_2_amount;
														$slab_3_count +=  $da->slab_3_count;
														$slab_3_amount +=  $da->slab_3_amount;
														$slab_4_count +=  $da->slab_4_count;
														$slab_4_amount +=  $da->slab_4_amount;

														$location_counts[$branch]['slab_1_count']+=$da->slab_1_count;
														$location_counts[$branch]['slab_1_amount']+=$da->slab_1_amount;
														$location_counts[$branch]['slab_2_count']+=$da->slab_2_count;
														$location_counts[$branch]['slab_2_amount']+=$da->slab_2_amount;
														$location_counts[$branch]['slab_3_count']+=$da->slab_3_count;
														$location_counts[$branch]['slab_3_amount']+=$da->slab_3_amount;
														$location_counts[$branch]['slab_4_count']+=$da->slab_4_count;
														$location_counts[$branch]['slab_4_amount']+=$da->slab_4_amount;
													} ?>
                                                    
                                                    @if($is_cxo==1)
														<tr style="background: #E9E9FF 0% 0% no-repeat padding-box;font-weight:bold;">
															<td>{{$branch}}</td>
													        <td>OverAll</td>
															<td><a href="{{ route('admin.academic.get-academic-student') }}?branch={{ $branch }}&slab=1&type=upcomming">{{ $location_counts[$branch]['slab_1_count']??'' }}</a></td>
															<td>{{ $location_counts[$branch]['slab_1_amount'] }}</td>

															<td><a href="{{ route('admin.academic.get-academic-student') }}?branch={{ $branch }}&slab=2&type=upcomming">{{ $location_counts[$branch]['slab_2_count']??'' }}</a></td>
															<td>{{ $location_counts[$branch]['slab_2_amount'] }}</td>

															<td><a href="{{ route('admin.academic.get-academic-student') }}?branch={{ $branch }}&slab=3&type=upcomming">{{ $location_counts[$branch]['slab_3_count']??'' }}</a></td>
															<td>{{ $location_counts[$branch]['slab_3_amount'] }}</td>

															<td><a href="{{ route('admin.academic.get-academic-student') }}?branch={{ $branch }}&slab=4&type=upcomming">{{ $location_counts[$branch]['slab_4_count']??'' }}</a></td>
															<td>{{ $location_counts[$branch]['slab_4_amount'] }}</td>
														</tr>
													@endif
												<?php } ?>

												<tr style="background: #E9E9FF 0% 0% no-repeat padding-box;font-weight:bold;">
													<td colspan="@if($is_cxo==1) 2 @endif">Overall</td>
													<td>{{ convert_rupee($slab_1_count) }}</td>
													<td>{{ convert_rupee($slab_1_amount) }}</td>
													<td>{{ convert_rupee($slab_2_count) }}</td>
													<td>{{ convert_rupee($slab_2_amount) }}</td>
													<td>{{ convert_rupee($slab_3_count) }}</td>
													<td>{{ convert_rupee($slab_3_amount) }}</td>
													<td>{{ convert_rupee($slab_4_count) }}</td>
													<td>{{ convert_rupee($slab_4_amount) }}</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
						
						<div class="row chartAppend">
							<div class="col-lg-6 d-none">
								<div class="card p-1">
									<div class="d-flex pb-2">
										<h5>Fee Due and FEE Recovered X1 (WOW)</h5>
										
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<select name="" class="border-0" style="border-bottom:solid 1px #000 !important">
											<option>Select</option>
										</select>
									</div>
									<canvas id=""></canvas>
								</div>
							</div>
							
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>
</div>

<style>
	.dashboard b{
		font-size:22px;
		padding-bottom:10px;
	}
	
	.daywise thead th{
		font-size:11px !important; 
	}
</style>
@endsection
@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

<script type="text/javascript">
$(document).ready(function() {
	$('.select-multiple1').select2({
		width: '100%',
		placeholder: "Select",
		allowClear: true
	});
});

$(".course_id").change(function(){
	course_id = $(this).val();
	$.ajax({
		type : 'POST',
		url : '{{ route('admin.get-coursewise-batch') }}',
		data : {'_token' : '{{ csrf_token() }}', 'course_id': course_id},
		dataType : 'html',
		success : function (data){
			$('.fill-name').empty();
			
			$('.fill-name').html(data);
		}
	});	
});


$(document).ready(function() {
	var query_category='<?=$query_category?>';
	$(document).on('change','.branchChart',function(){
		var chartid=$(this).data('chart');
	    $("#"+chartid).remove();

	    $(".chartArea-"+chartid).after('<canvas id="'+chartid+'"><canvas>');	

		getchartdata(chartid,$(this).val(),$(this).data('filter_key'),$(this).data('filter_value'));
	});
	
	getchartdata();
	function getchartdata(chartid='',month='',filter_key='',filter_value=''){		
		$.ajax({
			type : 'POST',
			url : '{{ route('admin.academic.fee-chart') }}',
			data : {'_token' : '{{ csrf_token() }}',category:query_category,'month':month,'filter_key':filter_key,'filter_value':filter_value},
			dataType : 'json',
			success : function (data){
				var chart_data=data.chart_data;
				var monthList=data.monthList;

				if(chart_data.length==0 && chartid!=""){
					//$("#"+chartid).html("No Data found");
					const canvas = document.getElementById(chartid);
					const ctx = canvas.getContext("2d");
					ctx.font = "30px Arial";
					ctx.fillText("No Data found",10,80);
				}

				$.each(chart_data,function(i,item){
					//console.log(item);
					//console.log(i);
					if(chartid==''){
						var html = `<div class="col-lg-6">
										<div class="card p-1">
											<div class="d-flex pb-2">
												<h5>Fee Due and FEE Recovered `+item[0]['title']+`</h5>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`;
												if(item[0]['filter']=='Yes'){
													html+=`<select class="border-0 branchChart" style="border-bottom:solid 1px #000 !important" data-filter_key="`+item[0]['filter_key']+`" data-filter_value="`+item[0]['filter_value']+`" data-chart="myChart`+i+`">
														<option value=''>Select</option>`;
														for(var m =0; m<monthList.length;m++) {
															if(m==0){
	                                                         html+=`<option val="`+monthList[m]+`" Selected>`+monthList[m]+`</option>"`;
															}else{	
															 html+=`<option val="`+monthList[m]+`">`+monthList[m]+`</option>"`;
															}
														}

													html+=`</select>`;
												}

											html+=`</div>

											<div class="chartArea-myChart`+i+`"></div>

											<canvas id="myChart`+i+`"></canvas>
										</div>
									</div>`;
						
						$('.chartAppend').append(html);
						//console.log(html);
					}

					var week=[];
                    var due_fee=[];
                    var paid_fee=[];
					$.each(item,function(j,val){
					   week[j]=val['week'];
                       due_fee[j]=val['due_fee'];
                       paid_fee[j]=val['paid_fee'];
                       console.log(val);
					});

					/*console.log(paid_fee);
					console.log(due_fee);
					console.log(week);*/
					if(chartid==''){
					    chartwow("myChart"+i,week,due_fee,paid_fee);
					}else if(chartid=="myChart"+i){
						chartwow(chartid,week,due_fee,paid_fee);
					}	
				})
				
			}
		});	
	}
});
</script>
<script>
function chartwow(chartid,week,due,recover,destroy){
	Chart.register(ChartDataLabels);
	// Define the labels and data
	//const dailySalesLabels = ["Week 1","Week 2","Week 3","Week 4"];
	const dailySalesLabels = week;
	//console.log(week);
	const dailySalesData = {
	   labels: dailySalesLabels,
	   datasets: [
		   {
			   label: 'Recovered Fee',
			   backgroundColor: '#7869ea',
			   borderRadius:true,
			   data: recover,
			   stack: 'Stack 0',
			   borderRadius:8,
			   barThickness:50
		   }, {
			   label: 'Due fee',
			   backgroundColor: '#ff9f43',
			   data: due,
			   stack: 'Stack 0',
				borderRadius:8,
			   barThickness:50
		   }
	   ]
	};

	// Define the totalizer plugin
	const totalizer = {
	 id: 'totalizer',
	 beforeUpdate: chart => {
	   let totals = {}
	   let utmost = 0
	   chart.data.datasets.forEach((dataset, datasetIndex) => {
		 if (chart.isDatasetVisible(datasetIndex)) {
		   utmost = datasetIndex
		   dataset.data.forEach((value, index) => {
			 totals[index] = (totals[index] || 0) + value
		   })
		 }
	   })
	   chart.$totalizer = {
		 totals: totals,
		 utmost: utmost
	   }
	 }
	}

	// Define the chart configuration
	const dailySalesConfig = {
	   type: 'bar',
	   data: dailySalesData,
	   options: {
			plugins: {
			   datalabels: {
				   color: 'white',
				   display: true,
				   anchor: 'center',
				   align: 'top',
				    formatter: (value, context) => {
                        return convert_rupee(value);
		                //return value+'%';
		            }
			   },
			   legend: {
					position: 'bottom',
					align:'center',
					labels: {
					  boxWidth:10
					},
			   }
		   },
			
		   responsive: false,
		   maintainAspectRatio: false,
		   element : {
				bar : {
					borderSkipped : 'start',
					
				}
		   },
		   scales: {
			   x: {
				   stacked: true,
				   grid: {
					  display: false
				   }
			   },
			   y: {
				   stacked: true,
				   ticks: {
					  display:true
				   },
				   grid: {
					  display:true
				   },

				    ticks: {
		                stepSize: 1000000,
		                callback: function(label, index, values) {
			              	return convert_rupee(label);
		                }
		            }
			   }
		   }
	   },
	};

	// JS - Destroy exiting Chart Instance to reuse <canvas> element
	let chartStatus = Chart.getChart(chartid); // <canvas> id
	if(chartStatus != undefined) {
	  chartStatus.destroy();
	 // alert('dddd');
	}

	// Create the chart
	const dailySalesChart = new Chart(document.getElementById(chartid), {
	 plugins: [ChartDataLabels, totalizer],
	 ...dailySalesConfig
	});
}


function convert_rupee(label){
	lno = label.toString().length;
	if(lno>=4 && lno <=5){
		label=parseFloat(label/1000).toFixed(2)+' K';
	}else if(lno>=6 && lno <=7){
		label=parseFloat(label/100000).toFixed(2)+' L';
	}else if(lno>=8 && lno <=9){
		label=parseFloat(label/10000000).toFixed(2)+' Cr.';
	}else if(lno>=10 && lno <=15){
		label=parseFloat(label/1000000000).toFixed(2)+' Cr.';
	}
	return label;
}
</script>
@endsection
