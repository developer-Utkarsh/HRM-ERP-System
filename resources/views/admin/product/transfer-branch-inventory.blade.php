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
						<h2 class="content-header-title float-left mb-0">Branch Transfer Details</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Branch Transfer Details</a>
								</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-2"><a href="{{ route('admin.transfer-branch-inventory') }}" class="btn btn-outline-primary float-right">Back</a></div>
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
									<form class="form" action="{{ route('admin.transfer-branch-inventory-store') }}" method="post" enctype="multipart/form-data">
										@csrf
										<?php 
										
										?>
										<h3>Add Transfer</h3>
										<div class="form-body">
											<div class="row">
												<div class="col-md-3 col-12">
													<div class="form-group">
														<label for="first-name-column">Quantity</label>
														<input type="number" class="form-control" placeholder="Quantity" name="qty" value="{{ !empty($get_product_transfer_detail->qty) ? $get_product_transfer_detail->qty : old('qty') }}">
														@if($errors->has('qty'))
														<span class="text-danger">{{ $errors->first('qty') }} </span>
														@endif
													</div>
												</div>	
												
												<div class="col-md-3 col-12">
													<div class="form-group">
														<label for="first-name-column">Transfer To</label>
														<?php $branches = \App\Branch::where('status', '1')->orderBy('id','desc')->get(); ?>
														<select class="form-control select-multiple1 transfer_to" name="transfer_to">
															<option value="">Select Any</option>
															@if(count($branches) > 0)
															@foreach($branches as $key => $value)
															<option value="{{ $value->id }}">{{ $value->name }}</option>
															@endforeach
															@endif
														</select>		
														@if($errors->has('transfer_to'))
														<span class="text-danger">{{ $errors->first('transfer_to') }} </span>
														@endif
													</div>
												</div>
												
												<div class="col-md-3 col-12 mt-2">
											    	<input type="hidden" name="product_id" value="{{ $product_id }}">
													<input type="hidden" name="branch_id" value="{{ $branch_id }}">
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
			<!-- // Basic Floating Label Form section end -->
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
