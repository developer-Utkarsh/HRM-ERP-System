@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Import Advance Deduction</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">Advance Deduction
								</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
			<div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
			</div>
		</div>
		<div class="content-body">
			<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="{{ route('admin.salary.store-deduction') }}" method="post" enctype="multipart/form-data">
										@csrf
										<div class="form-body">
											<div class="row">
                                                <div class="col-md-12 col-12"><h3>Add Advance Deduction</h3></div> 
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Import File</label>
														<input type="file" class="form-control" name="import_file">
														@if($errors->has('import_file'))
														<span class="text-danger">{{ $errors->first('import_file') }}</span>
														@endif
														<br>
													</div>
												</div> 
                                                
                                                <div class="col-md-4">
                                                    <div class="form-group">
														<label for="first-name-column">Month</label>
                                                        <input type="month" class="form-control year_wise_month" name="year_wise_month" value="@if(!empty(Request::get('year_wise_month'))){{ Request::get('year_wise_month') }}@else{{ date('Y-m') }}@endif">
														@if($errors->has('year_wise_month'))
														<span class="text-danger">{{ $errors->first('year_wise_month') }}</span>
														@endif
														<br>
													</div>
                                                </div>

												<div class="col-md-4 col-12 mt-2">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Submit</button>
                                                    <a href="{{asset('laravel/public/salary_deduction.xlsx')}}" download>Download sample file</a>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
						
					
					</div>
				</div>
			</section>
		</div>

        <div class="content-body">
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ route('admin.add-deduction') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Emp Code</label>
											<fieldset class="form-group">
												<input type="text" class="form-control" name="emp_code" placeholder="Emp Code" value="{{ app('request')->input('emp_code') }}">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Month</label>
											<fieldset class="form-group">												
                                                <input type="month" class="form-control year_wise_month" name="year_wise_month" value="@if(!empty(Request::get('year_wise_month'))){{ Request::get('year_wise_month') }}@else{{ date('Y-m') }}@endif"">												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('admin.add-deduction') }}" class="btn btn-warning">Reset</a>
											</fieldset>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table class="table data-list-view">
						<thead>
							<tr>
								<th>S. No.</th>
                                <th>Name</th>
								<th>Emp Code</th>
								<th>Loan/Advance Deduction</th>
								<th>TDS Amount</th>
								<th>Remark</th>
							</tr>
						</thead>
						<tbody>
                            @if(count($salary_increment) > 0)
                                @foreach($salary_increment as  $key => $value)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td class="product-category">{{ !empty($value->name) ? $value->name : '' }}</td>
                                    <td class="product-category">{{ !empty($value->emp_code) ? $value->emp_code : '' }}</td>
                                    <td class="product-category">{{ !empty($value->loan_amount) ? $value->loan_amount : '0' }}</td>
                                    <td class="product-category">{{ !empty($value->tds_amount) ? $value->tds_amount : '0' }}</td>
                                    <td class="product-category">{{ !empty($value->deduction_remark) ? $value->deduction_remark : '--' }}</td>
                                </tr>
                                @endforeach
                            @else
                            <tr>
                                <td colspan="8" class="text-center text-danger">No Record Found</td>
                            </tr>
                            @endif    
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

<script>

$(document).on("click",".download_sample",function(){
	var course_id = $(".course_id").val();
	if(course_id!=''){
		document.location.href = "download_sample?course_id="+course_id;	
	}
	else{
		alert('Please select course');
	}
});


</script>
@endsection
