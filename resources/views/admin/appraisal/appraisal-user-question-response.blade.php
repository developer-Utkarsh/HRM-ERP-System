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
						<h2 class="content-header-title float-left mb-0">Appraisal User Question Response</h2>
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
					@if(count($appraisal_question_response_result) > 0)
					<div class="col-md-12">
						<div class="card">
							<div class="card-header">
								<div class="card-title">Question</div>
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<form action="{{ route('admin.store-appraisal-user-question-response') }}" method="POST">
									@csrf
										<table class="table data-list-view">
											<thead>
												<tr>
													<th>Question</th>
													<th>Employee Mark</th>
													<th>Employee Remark</th>
													<th>Head Mark</th>
													<th>Head Remark</th>
												</tr>
											</thead>
											<tbody>
												@if(count($appraisal_question_response_result) > 0)
												@foreach($appraisal_question_response_result as  $key => $value)
												<tr>
													<td class="product-category">{{ !empty($value->question) ? $value->question : '' }}
													<input type="hidden" name="id[]" value="{{$value->id}}">
													</td>
													<td class="product-category">{{ !empty($value->marks) ? $value->marks : '' }}</td>
													<td class="product-category">{{ !empty($value->remark) ? $value->remark : '' }}</td>
													<td class="product-category">
														<input type="text" name="head_marks[]" class="form-control" value="{{ old('head_marks', $value->head_marks) }}" placeholder="Marks">
													</td>
													<td class="product-category">
														<textarea name="head_remark[]" class="form-control" placeholder="Remark">{{ old('head_remark', $value->head_remark) }}</textarea>
													</td>
												</tr>
												@endforeach
												<tr>
													<td class="product-category" colspan="1"><span>Overall Remark : </span> {{ !empty($appraisal_user_question_response_result->overall_remark) ? $appraisal_user_question_response_result->overall_remark : '' }}</td>

													<td class="product-category" colspan="4">
														<textarea name="head_overall_remark" class="form-control" placeholder="Overall Remark">{{ old('head_overall_remark', $appraisal_user_question_response_result->head_overall_remark) }}</textarea>
													</td>
												</tr>
													@if($appraisal_user_question_response_result->is_submitted == '0' || $appraisal_user_question_response_result->is_submitted == '2')	
													<tr>
														<td class="product-category" colspan="5">
															<input type="hidden" name="appraisal_id" value="{{$appraisal_user_question_response_result->id}}">
															<button type="submit" class="btn btn-primary mr-1 mb-1 float-right">Submit</button>
														</td>
													</tr>
													@endif
												@endif
											</tbody>
										</table>
									</form>
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
