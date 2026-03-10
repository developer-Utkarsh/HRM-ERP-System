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
						<h2 class="content-header-title float-left mb-0">Expense</h2>
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
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ route('admin.expense.index') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Title</label>
											<fieldset class="form-group">
												<input type="text" class="form-control title" name="title" placeholder="Title" value="{{ app('request')->input('title') }}">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Status</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple status" name="status">
													@php $status = ['Pending', 'Approved','Reject']; @endphp
													<option value="">Select Any</option>
													@foreach($status as $key => $value)
													<option value="{{ $value }}" @if($value == app('request')->input('status')) selected="selected" @endif>{{ $value }}</option>
													@endforeach
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-6">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('admin.expense.index') }}" class="btn btn-warning">Reset</a>
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
								<th>Emp Name</th>
								<th>File</th>
								<th>Category Name</th>
								<th>Title</th>
								<th>Amount</th>
								<th>Remark</th>
								<th>Status</th>
								<th>Date</th>
							</tr>
						</thead>
						<tbody>
							@if(count($expense) > 0)
							@foreach($expense as  $key => $value)
							@php 
							$fileArray = explode('.', $value->file_name);
							$fileExt   = end($fileArray);
							@endphp
							<tr>
								<td>{{ $key + 1 }}</td>
								<td class="product-category">{{ !empty($value->user_name) ? $value->user_name : '' }}</td>
								@if(!empty($fileExt) && $fileExt == 'pdf')
								<td class="product-category"><a class="btn btn-primary btn-sm" href="{{ asset('/') }}{{ !empty($value->file_name) ? '/'.$value->file_name : '' }}" target="__blank">View</a></td>
								@else
								<td class="product-category"><img src="{{ asset('/') }}{{ !empty($value->file_name) ? '/'.$value->file_name : '/default-image.png' }}" style="width: 100px;height: 100px;"></td>
								@endif
								
								<td class="product-category">{{ !empty($value->cat_name) ? $value->cat_name : '' }}</td>
								<td class="product-category">{{ !empty($value->title) ? $value->title : '' }}</td>
								<td class="product-category">{{ !empty($value->amount) ? $value->amount : '' }}</td>
								<td class="product-category">{{ !empty($value->remark) ? $value->remark : '' }}</td>
								<td class="product-category">
									{{ !empty($value->status) ? $value->status : '' }}
										
									<?php if($value->status=='Pending'){ ?>
									<a title="Update Category" href="javascript:void(0)" data-id="{{ $value->id }}" class="get_edit_data">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
									</a>
									<?php } ?>
								</td>
								<td class="product-category">{{ !empty($value->created_at) ? date('d-m-Y',strtotime($value->created_at)) : '' }}</td>
							</tr>
							@endforeach
							@else
							<tr><td class="text-center text-primary" colspan="10">No Record Found</td></tr>	
							@endif
						</tbody>
					</table>
				</div>                   
			</section>
		</div>
	</div>
</div>



<!-- The Modal -->
<div class="modal" id="myModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Status Update</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<form method="post" action="{{ route('admin.expense.statusupdate') }}">
				{{ csrf_field() }}
				<!-- Modal body -->
				<div class="modal-body">
					<select name="estatus" class="form-control">
						<option value="">-- Select --</option>
						<option value="Pending">Pending</option>
						<option value="Approved">Approved</option>
						<option value="Reject">Reject</option>
					</select>
					
					<input type="hidden" name="expense_id" value="" class="expense_id"/>
				</div>

				<!-- Modal footer -->
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Save</button>
					<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
				</div>
			</form>
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
	
	$(".get_edit_data").on("click", function() { 
		var cat_id = $(this).attr("data-id");
		if(cat_id){
			$('.expense_id').val(cat_id);
			$('#myModal').modal({
					backdrop: 'static',
					keyboard: true, 
					show: true
			});
		}
	}); 
</script>
@endsection
