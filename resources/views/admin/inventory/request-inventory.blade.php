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
						<h2 class="content-header-title float-left mb-0">Request Inventory</h2>
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
				
				<div class="table-responsive">
					<table class="table data-list-view">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Name</th>
								<th>Category</th>
								<th>Sub Category</th>
								<th>Qty</th>
								<th>Status</th>
								<th>Date</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						@if(count($product) > 0)
							@foreach($product as  $key => $value)
							@php
							$productData = DB::table('products')->select(DB::raw("(SELECT name FROM category WHERE id=cat_id) AS category_name, (SELECT name FROM category WHERE id=sub_cat_id) AS sub_category_name"))->where('is_deleted', '0')->where('id', $value->product_id)->orderBy('id', 'desc')->first();
							
							//echo '<pre>'; print_r($productData);die;
							@endphp
							<tr>
								<td>{{ $key + 1 }}</td>
								<td class="product-category">{{ $value->name }}</td>
								<td class="product-category">{{ $productData->category_name }}</td>
								<td class="product-category">{{ $productData->sub_category_name }}</td>
								<td class="product-category">{{ $value->qty }}</td>
								<td class="product-category">{{ $value->status }}</td>
								<td>{{ $value->created_at->format('d-m-Y') }}</td>
								@if($value->status == 'Pending')
								<td class="product-action">
									<a title="Accept" href="{{ route('admin.transfer-inventory-update-status', [$value->id,$value->product_id,$value->qty,'Accept']) }}">
										<span class="action-transfer"><i class="feather icon-check-square"></i></span>
									</a>
									
									<a title="Reject" href="javascript:void(0)" data-toggle="modal" data-id="{{ $value->id }}" data-product-id="{{ $value->product_id }}" data-qty="{{ $value->qty }}" data-status="Reject" class="get_raise_data">
										<span class="action-transfer"><i class="feather icon-x-square"></i></span>
									</a>

									<a title="Raise a issue" href="javascript:void(0)" data-toggle="modal" data-id="{{ $value->id }}" data-product-id="{{ $value->product_id }}" data-qty="{{ $value->qty }}" data-status="Raise a issue" class="get_raise_data">
										<span class="action-transfer"><i class="feather icon-user"></i></span>
									</a>
								</td>
								@endif
							</tr>
							@endforeach
						@else
						<tr ><td class="text-center text-primary" colspan="8">No Record Found</td></tr>
						@endif
						</tbody>
					</table>
				</div>                   
			</section>
			

		</div>
	</div>
</div>

<div id="raise_edit" class="modal fade">
	<div class="modal-dialog modal-l">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title">Remark</h5>
		</div>
		<form method="get" action="" id="raise_form">
		<div class="modal-body">
			<div class="form-body">
				<div class="row pt-2">
					<div class="col-md-12 col-12">
						<div class="form-group">
							<textarea class="form-control remark" name="remark" required></textarea>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
			<button type="submit" class="btn btn-primary">Submit</button>
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
		$('.select-multiple1').select2({
			width: "100%",
			placeholder: "Select Category",
			allowClear: true
		});
	});
	
	$(".cat_id").on("change", function () {
		var cat_id = $(".cat_id option:selected").attr('value'); 
		if (cat_id) {
			$.ajax({
				type : 'POST',
				url : '{{ route('admin.product.get-sub-cat') }}',
				data : {'_token' : '{{ csrf_token() }}', 'cat_id': cat_id},
				dataType : 'html',
				success : function (data){
					$('.sub_cat_id').empty();
					$('.sub_cat_id').append(data);
				}
			});
		}
	});

	$(function() {
		$(".get_raise_data").on("click", function() { 
			$('.remark').val("");
			var transfer_id = $(this).attr("data-id");
			var product_id = $(this).attr("data-product-id");
			var qty = $(this).attr("data-qty");
			var status = $(this).attr("data-status");
			$('#raise_form').attr('action', "{{ url('admin/transfer-inventory-update-status') }}/"+transfer_id+"/"+product_id+"/"+qty+"/"+encodeURI(status));
			$('#raise_edit').modal({
					backdrop: 'static',
					keyboard: true, 
					show: true
			});
		});     
	});

</script>
@endsection
