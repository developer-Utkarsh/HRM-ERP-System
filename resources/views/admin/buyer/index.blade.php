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
						<h2 class="content-header-title float-left mb-0">Buyer</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-md-4"> <a href="{{ route('admin.buyer.create') }}" data-id="" class="btn btn-outline-primary float-right">Add Buyer</a></div>
				</div>
			</div>
		</div>
		<div class="content-body">
		<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="{{ route('admin.buyer.index') }}" method="get" enctype="multipart/form-data">
										@csrf
										<div class="form-body">
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label>Name, Contact No, GST</label>
														<input type="text" class="form-control search" placeholder="Ex. Name, Contact No, GST .." name="search" value="{{ app('request')->input('search') }}">
														@if($errors->has('name'))
														<span class="text-danger">{{ $errors->first('name') }} </span>
														@endif
													</div>
												</div>	
												<div class="col-md-4">
													<div class="form-group">
														<label>IS MEME</label>
														<select name="msme" class="msme form-control">
															<option value="">Select</option>
															<option value="1" {{ request('msme') == '1' ? 'selected' : '' }}>Yes</option>
															<option value="2" {{ request('msme') == '2' ? 'selected' : '' }}>No</option>
														</select>

														@if($errors->has('name'))
														<span class="text-danger">{{ $errors->first('name') }} </span>
														@endif
													</div>
												</div>	
												<div class="col-md-4">
													<div class="form-group">
														<label>Vendor Code</label>
														<input type="text" class="form-control" placeholder="Vendor Code" name="vcode" value="{{ app('request')->input('vcode') }}">
														@if($errors->has('name'))
														<span class="text-danger">{{ $errors->first('name') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label>Pan Number</label>
														<input type="text" class="form-control" placeholder="Pan Number" name="pan" value="{{ app('request')->input('pan') }}">
														@if($errors->has('name'))
														<span class="text-danger">{{ $errors->first('name') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label>Status</label>
														<select name="status" class="status form-control">
															<option value="">Select</option>
															<option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
															<option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
														</select>

														@if($errors->has('status'))
														<span class="text-danger">{{ $errors->first('status') }} </span>
														@endif
													</div>
												</div>	
												<div class="col-md-8">
													
													<fieldset class="form-group">		
														<button type="submit" class="btn btn-primary">Search</button>
														<a href="{{ route('admin.buyer.index') }}" class="btn btn-warning">Reset</a>
														<a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a>
													</fieldset>
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
			<section id="data-list-view" class="data-list-view-header">
				
				<div class="table-responsive">
					<table class="table data-list-view">
						<thead>
							<tr>
								<th>Action</th>
								<th>S. No.</th>
								<th>Code</th>
								<th>Name</th>
								<th>Contact No</th>
								<th>Address</th>
								<th>Pincode</th>
								<th>Credit Period Days</th>
								<th>GST No</th>
								<th>Pan No</th>
								<th>Email</th>
								<th>MSME Category</th>
								<th>MSME / UAM File</th>
								<th>MSME / UAM No</th>
								<th>Declaration Form</th>
								<th>Beneficiary's Name</th>
								<th>VR Form</th>
								<th>Bank Document</th>
								<th>Other Document</th>
								<th>Bank Account No.</th>
								<th>Bank Name</th>
								<th>RTGS/NEFT IFSC CODE</th>
								<th>Bank Address</th>
								<th>Created</th>
								<th>Status</th>
								<th>Type</th>
							</tr>
						</thead>
						<tbody>
						@if(count($buyer) > 0)
							@foreach($buyer as  $key => $value)
							<tr>
								<td class="product-action">
									<a  class="mr-1" title="Update Buyer" href="{{ route('admin.buyer.edit', $value->id) }}">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
									</a>
									
									<a class="mr-1" title="Add Bill" href="{{ route('admin.buyer.bill', $value->id) }}">
										<span class="action-edit"><i class="feather icon-printer"></i></span>
									</a>
									
									<a class="" title="Delete Buyer" href="{{ route('admin.buyer.delete', $value->id) }}" onclick="return confirm('Are You Sure To Delete Buyer')">
										<span class="action-delete"><i class="feather icon-trash"></i></span>
									</a>
									
									<?php if(Auth::user()->id == 901 || Auth::user()->id == 5362){ ?>
									<a class="mr-1" title="Add Bill" href="{{ route('admin.buyer.vendor-changes-history', $value->id) }}">
										<span class="action-edit"><i class="fa fa-history" style="color:red"></i></span>
									</a>
									<?php } ?>
								</td>
								<td>{{ $pageNumber++ }}</td>
								<td>V{{$value->id}}</td>
								<td class="product-category"><a href="{{ route('admin.buyer.vendor-view',[$value->id]) }}">{{ $value->name }}</a></td>
								<td class="product-category">{{ $value->contact_no }}</td>
								<td class="product-category">{{ $value->address }}</td>
								<td class="product-category">{{ $value->pincode }}</td>
								<td class="product-category">{{ $value->credit_day }}</td>
								<td class="product-category">
									{{ $value->gst_no }}
									
									<?php if($value->gst_img!=''){ ?>
										</br><a href="{{ asset('laravel/public/buyer/' . $value->gst_img) }}" download>Preview</a>
									<?php } ?>
								</td>
								<td class="product-category">
									{{ $value->pan_no }}
									
									<?php if($value->pan_img!=''){ ?>
										</br><a href="{{ asset('laravel/public/buyer/' . $value->pan_img) }}" download>Preview</a>
									<?php } ?>
								</td>
								<td class="product-category">{{ $value->email }}</td>
								<td class="product-category">{{ $value->msme_category }}</td>
								<td class="product-category">
									<?php if($value->msme_uam_file!='-'){ ?>
										<a href="{{ asset('laravel/public/buyer/' . $value->msme_uam_file) }}" download>Preview</a>
									<?php }else{ echo 'No'; } ?>
								</td>
								<td class="product-category">
									<?php if($value->msme_uam_no!='-'){ ?>
										{{ $value->msme_uam_no }}
									<?php }else{ echo 'No'; } ?>
								</td>
								<td class="product-category">
									<?php if($value->declaration_form!='-'){ ?>
										<a href="{{ asset('laravel/public/buyer/' . $value->declaration_form) }}" download>Preview</a>
									<?php }else{ echo 'No'; } ?>
								</td>
								<td class="product-category">{{ $value->beneficiary }}</td>
								<td class="product-category">
									<?php if($value->bank_proof!='-'){ ?>
										<a href="{{ asset('laravel/public/buyer/' . $value->bank_proof) }}" download>Preview</a>
									<?php }else{ echo 'No'; } ?>
								</td>
								<td class="product-category">
									<?php if($value->bank_proof_2!=''){ ?>
										<a href="{{ asset('laravel/public/buyer/' . $value->bank_proof_2) }}" download>Preview</a>
									<?php }else{ echo 'No'; } ?>
								</td>
								<td class="product-category">
									<?php if($value->aggrement!=''){ ?>
										<a href="{{ asset('laravel/public/buyer/' . $value->aggrement) }}" download>Preview</a>
									<?php }else{ echo 'No'; } ?>
								</td>
								<td class="product-category">{{ $value->account }}</td>
								<td class="product-category">{{ $value->bank_name }}</td>
								<td class="product-category">{{ $value->ifsc }}</td>
								<td class="product-category">{{ $value->bank_address }}</td>
								<td>{{ $value->created_at->format('d-m-Y') }}</td>
								<td>{{  $value->status }}</td>
								<td>{{  $value->type }}</td>
								
							</tr>
							@endforeach
						@else
						<tr ><td class="text-center text-primary" colspan="7">No Record Found</td></tr>
						@endif	
						</tbody>
					</table>
					
					<div class="d-flex justify-content-center">					
					{!! $buyer->appends($params)->links() !!}
					</div>
				</div>                   
			</section>
			

		</div>
	</div>
</div>


@endsection
@section('scripts')

<script type="text/javascript">
	$("body").on("click", "#download_excel", function (e) {
		var data = {};					
		data.search  = $('.search').val(),
		data.msme 	 = $('.msme').val(),
		data.status 	 = $('.status').val(),
		window.location.href = "<?php echo URL::to('/admin/'); ?>/buyer-report-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	}); 
</script>
@endsection
