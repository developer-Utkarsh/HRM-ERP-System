@extends('layouts.admin')
@section('content')
<style>
.select2-selection--single {height: 44px !important; padding: 8px;}
</style>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-md-6">
						<h2 class="content-header-title float-left mb-0">Training Video Category</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-md-6"> <a href="javascript:void(0)" data-id="" class="btn btn-outline-primary get_edit_data float-right">Add Category</a></div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ route('admin.training_video_category.index') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Name</label>
											<fieldset class="form-group">
												<input type="text" class="form-control" name="name" placeholder="Name" value="{{ app('request')->input('name') }}">
											</fieldset>
										</div>
										
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('admin.training_video_category.index') }}" class="btn btn-warning">Reset</a>
											</fieldset>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table class="table data-list-view" id="TableSearch">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Name</th>
								<th>Status</th>
								<th>Created</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						@if(count($category) > 0)
							@foreach($category as  $key => $value)
							<tr>
								<td>{{ $key + 1 }}</td>
								<td class="product-category">{{ $value->name }}</td>
								<td class="product-category">
									<a href="{{route('admin.training_video_category.status', $value->id)}}">
										<strong class="fa fa-lg {{$value->status == 'Active' ? 'fa-toggle-on text-success' : 'fa-toggle-off text-grey'}}" title="Toggle publish"></strong>
									</a>
								</td>
								<td>{{ $value->created_at->format('d-m-Y') }}</td>
								<td class="product-action">
									<a title="Update Category" href="javascript:void(0)" data-id="{{ $value->id }}" class="get_edit_data">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
									</a>
									
									<a title="Delete Category" href="{{ route('admin.training_video_category.delete', $value->id) }}" onclick="return confirm('Are You Sure To Delete Category')">
										<span class="action-delete"><i class="feather icon-trash"></i></span>
									</a>
									
									
								</td>
							</tr>
							@endforeach
							
						@else
						<tr ><td class="text-center text-primary" colspan="5">No Record Found</td></tr>
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
        <h4 class="modal-title">Add Category</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <form method="post" action="{{ route('admin.training_video_category.store') }}">
      		{{ csrf_field() }}
	      <!-- Modal body -->
	      <div class="modal-body fill-name">
	      	<label>Category:</label>
	        <input type="text" name="name" class="form-control" required><br>
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

<script src="{{ asset('laravel/public/admin/js/jquery.validate.min.js') }}"></script>
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
</script>
<script type="text/javascript">
	$(".get_edit_data").on("click", function() { 
		var cat_id = $(this).attr("data-id");
		if(cat_id){
			
			$('#myModal').modal({
					backdrop: 'static',
					keyboard: true, 
					show: true
			});
			
			$.ajax({
				type : 'POST',
				url : '{{ route('admin.edit-training-video-category') }}',
				data : {'_token' : '{{ csrf_token() }}', 'cat_id': cat_id},
				dataType : 'html',
				success : function (data){
					$('.fill-name').empty();
					
					$('.fill-name').html(data);
				}
			});
			
		}
		else{
			$('.fill-name').empty();
			$('#myModal').modal({
					backdrop: 'static',
					keyboard: true, 
					show: true
			});
			$('.fill-name').html('<label>Category:</label><input type="text" name="name" class="form-control" required><br>');
		}		
	}); 
</script>
@endsection
