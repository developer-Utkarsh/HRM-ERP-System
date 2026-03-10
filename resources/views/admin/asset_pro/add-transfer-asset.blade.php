@extends('layouts.admin')
@section('content')

@if (Auth::viaRemember())
    {{666}}
@else
    {{777}}
@endif
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-10 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Asset Transfer Details</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Asset Transfer Details</a>
								</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-2"><a href="{{ route('admin.asset_pro.index') }}" class="btn btn-outline-primary float-right"><i class="feather icon-arrow-left"></i></a></div>
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
									<form class="form" action="{{ route('admin.asset_pro.store-transfer-asset-pro', $id) }}" method="post" enctype="multipart/form-data">
										@csrf
										<h3>Asset Transfer</h3>
										<div class="form-body">
											<div class="row">
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Quantity</label>
														<input type="number" class="form-control" placeholder="Quantity" name="qty" value="{{ !empty($get_product_transfer_detail->qty) ? $get_product_transfer_detail->qty : old('qty') }}" required>
														@if($errors->has('qty'))
														<span class="text-danger">{{ $errors->first('qty') }} </span>
														@endif
													</div>
												</div>	
												
												<div class="col-md-4">
													<label for="users-list-status">Employee</label>
													<fieldset class="form-group">												
														<select class="form-control select-multiple1 emp_id" name="emp_id" required>
															<option value="">Select Any</option>
															@if(count($user) > 0)
															@foreach($user as $key => $value)
															<option value="{{ $value['id'] }}" {{ old('emp_id') == $value['id'] ? 'selected' : '' }}>{{ $value['name'] }}@if(!empty($value['register_id'])){{ ' - ('.$value['register_id'].')' }}@endif</option>
															@endforeach
															@endif
														</select>
														@if($errors->has('emp_id'))
														<span class="text-danger">{{ $errors->first('emp_id') }} </span>
														@endif												
													</fieldset>
												</div>
												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Remark</label>
														<textarea class="form-control" placeholder="Remark" name="remark" required>{{ !empty($get_product_transfer_detail->remark) ? $get_product_transfer_detail->remark : old('remark') }}</textarea>
														@if($errors->has('remark'))
														<span class="text-danger">{{ $errors->first('remark') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-6 col-12">&nbsp;
												</div>
												
											
												<div class="col-md-3 col-12 mt-2">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Submit</button>
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
	});

</script>
@endsection
