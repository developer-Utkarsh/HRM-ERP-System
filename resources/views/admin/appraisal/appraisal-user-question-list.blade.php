@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-md-6">
						<h2 class="content-header-title float-left mb-0">Appraisal User Question List</h2>
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

				<div class="row">
					@if(!empty($appraisal_user_question_result))
					<div class="col-md-6">
						<div class="card">
							<div class="card-header">
								<div class="card-title">Employee</div>
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table data-list-view">
										@if(!empty($appraisal_user_question_result->user_name))
											<tr>
												<th>Name :</th>
												<td>{{$appraisal_user_question_result->user_name}}</td>
											</tr>
										@endif
										@if(!empty($appraisal_user_question_result->overall_remark))
											<tr>
												<th>Remark :</th>
												<td>{{$appraisal_user_question_result->overall_remark}}</td>
											</tr>
										@endif
										@if(!empty($appraisal_user_question_result->date))
											<tr>
												<th>Date :</th>
												<td>{{date('d-m-Y', strtotime($appraisal_user_question_result->date))}}</td>
											</tr>
										@endif
									</table>
								</div>  
							</div>  
						</div>  
					</div> 

					<div class="col-md-6">
						<div class="card">
							<div class="card-header">
								<div class="card-title">Head</div>
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table data-list-view">
										@if(!empty($appraisal_user_question_result->head_name))
											<tr>
												<th>Name :</th>
												<td>{{$appraisal_user_question_result->head_name}}</td>
											</tr>
										@endif
										@if(!empty($appraisal_user_question_result->head_overall_remark))
											<tr>
												<th>Remark :</th>
												<td>{{$appraisal_user_question_result->head_overall_remark}}</td>
											</tr>
										@endif
										@if(!empty($appraisal_user_question_result->head_date))
											<tr>
												<th>Date :</th>
												<td>{{date('d-m-Y', strtotime($appraisal_user_question_result->head_date))}}</td>
											</tr>
										@endif
									</table>
								</div>  
							</div>  
						</div>  
					</div> 

					@endif

					<div class="col-md-12">
						<div class="card">
							<div class="card-header">
								<div class="card-title">Question</div>
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table data-list-view">
										<thead>
											<tr>
												<th>S. No.</th>
												<th>Question</th>
												<th>Employee Mark</th>
												<th>Employee Remark</th>
												<th>Head Mark</th>
												<th>Head Remark</th>
											</tr>
										</thead>
										<tbody>
											@if(count($appraisal_question_result) > 0)
											@foreach($appraisal_question_result as  $key => $value)
											<tr>
												<td>{{ $key + 1 }}</td>
												<td class="product-category">{{ !empty($value->question) ? $value->question : '' }}</td>
												<td class="product-category">{{ !empty($value->marks) ? $value->marks : '' }}</td>
												<td class="product-category">{{ !empty($value->remark) ? $value->remark : '' }}</td>
												<td class="product-category">{{ !empty($value->head_marks) ? $value->head_marks : '' }}</td>
												<td class="product-category">{{ !empty($value->head_remark) ? $value->head_remark : '' }}</td>
											</tr>
											@endforeach
											@else
											<tr><td class="text-center text-primary" colspan="10">No Record Found</td></tr>	
											@endif
										</tbody>
									</table>
								</div>  
							</div>  
						</div>  
					</div> 
					@php
						$is_submitted_array = array('Employee Submitted','Head Submitted','Employee Need Discussed','Head Resubmitted','Employee Accepted');

						$check_role = \App\User::where('id',$appraisal_user_question_result->user_id)->first();

					@endphp
					@if($appraisal_user_question_result->is_submitted >= 0 && $check_role->role_id == 21)
					<div class="col-md-12">
						<div class="card">
							<div class="card-header">
								<div class="card-title">Current Status</div>
							</div> 
							<div class="card-body">
								<div class="table-responsive">
									<table class="table data-list-view">
										<tr>
											@for ($i = 0; $i <= $appraisal_user_question_result->is_submitted; $i++)
											<td><span><i class="feather icon-check-circle text-success"></i></span> {{$is_submitted_array[$i]}}</td>
											@endfor
										</tr>
									</table>
								</div>  
							</div> 
						</div>  
					</div> 
					@endif
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
		$('.select-multiple').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
</script>
@endsection
