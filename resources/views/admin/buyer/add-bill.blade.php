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
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Bill Details</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Bill Details</a>
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
					<div class="col-md-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="{{ route('admin.buyer.bill-store') }}" method="post" enctype="multipart/form-data">
										@csrf
										<?php 
										
										?>
										<h3>{{ $bill_txt }} Bill</h3>
										<div class="form-body">
											<div class="row">
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Bill No</label>
														<input type="text" class="form-control" placeholder="Bill No" name="bill_no" value="{{ !empty($get_bill_detail->bill_no) ? $get_bill_detail->bill_no : old('bill_no') }}">
														@if($errors->has('bill_no'))
														<span class="text-danger">{{ $errors->first('bill_no') }} </span>
														@endif
													</div>
												</div>	

												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Bill File</label>
														<input type="file" class="form-control" name="bill_file">
														@if($errors->has('bill_file'))
														<span class="text-danger">{{ $errors->first('bill_file') }} </span>
														@endif
													</div>
												</div>													

												                                    
												<div class="col-md-4 col-12 mt-2">
											    	<input type="hidden" name="buyer_id" value="{{ $buyer_id }}">
													<input type="hidden" name="bill_id" value="{{ !empty($get_bill_detail->id) ? $get_bill_detail->id : '' }}">
													<input type="hidden" name="prev_bill_file" value="{{ !empty($get_bill_detail->bill_file) ? $get_bill_detail->bill_file : '' }}">
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
@endsection
