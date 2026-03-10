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
						<h2 class="content-header-title float-left mb-0">Edit Sub Department</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Edit Sub Department</a>
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
									<form class="form" action="{{ route('admin.sub_department.update', $sub_department->id) }}" method="post" enctype="multipart/form-data">
										<!-- @method('PATCH') -->
										@csrf
										<div class="form-body">
											<div class="row">
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Department</label>
														<select class="form-control select-multiple department_id" name="department_id">
															
															<option value="">Select Department</option>
															@if(count($department_list) > 0)
																@foreach($department_list as $value)
																	<option value="{{$value->id}}"  @if($value->id == old('department_id') || (!empty($sub_department->department_id) && $sub_department->department_id == $value->id)) selected="selected" @endif>{{$value->name}}</option>
																@endforeach
															@endif
														</select>
														@if($errors->has('department_id'))
														<span class="text-danger">{{ $errors->first('department_id') }} </span>
														@endif
													</div>
												</div>

												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Sub Department</label>
														<input type="text" class="form-control" placeholder="Sub Department" name="name" value="{{ old('name', $sub_department->name) }}">
														@if($errors->has('name'))
														<span class="text-danger">{{ $errors->first('name') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group d-flex align-items-center mt-2">
														<label class="mr-2">Status :</label>
														<label>
															<input type="radio" name="status" value="1" {{ ($sub_department->status == 'Active') ? "checked" : ""}}>
															Active
														</label>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="status" value="0" {{ ($sub_department->status == 0 && $sub_department->status == '') ? "checked" : ""}}>
															Inactive
														</label>
													</div>
												</div>                                       
												<div class="col-12">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Update</button>
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
			<!-- // Basic Floating Label Form section end -->
		</div>
	</div>
</div>
@endsection

@section('scripts')
@endsection
