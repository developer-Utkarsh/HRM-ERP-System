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
						<h2 class="content-header-title float-left mb-0">Knowledge Base</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-md-6">
						<a href="{{ route('admin.knowledge_based.create') }}" class="btn btn-primary float-right ">Add Knowledge Base</a>
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
								<form action="{{ route('admin.knowledge_based.index') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Title</label>
											<fieldset class="form-group">
												<input type="text" class="form-control title" name="title" placeholder="Title" value="{{ app('request')->input('title') }}">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Category</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple cat_id" name="cat_id">
													<option value="">Select Any</option>
													@if(count($kb_category) > 0)
														@foreach($kb_category as $key => $value)
														<option value="{{ $value->id }}" @if($value->id == app('request')->input('cat_id')) selected="selected" @endif>{{ $value->name }}</option>
														@endforeach]
													@endif
												</select>
												@if($errors->has('cat_id'))
												<span class="text-danger">{{ $errors->first('cat_id') }} </span>
												@endif												
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
										<div class="col-12 col-sm-12 col-lg-12 text-right">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('admin.knowledge_based.index') }}" class="btn btn-warning">Reset</a>
											<a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a>
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
								<th>Employee Name</th>
								<th>Category Name</th>
								<th>Title</th>
								<th>Description</th>
								<th>Reference Link</th>
								<th>Status</th>
								<th>Created</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							@if(count($knowledge_based) > 0)
							@foreach($knowledge_based as  $key => $value)
							<tr>
								<td>{{ $key + 1 }}</td>
								<td class="product-category">{{ !empty($value->user_name) ? $value->user_name : '' }}</td>
								<td class="product-category">{{ !empty($value->cat_name) ? $value->cat_name : '' }}</td>
								<td class="product-category">{{ !empty($value->title) ? $value->title : '' }}</td>
								<td class="product-category">
									<button class="btn btn-primary btn-sm description_view" data-id="{{ $key + 1 }}">View</button>
									
									<div class="kb_description{{ $key + 1 }} d-none " ">{!!$value->description!!}</div>
								</td>
								<td class="product-category">{{ !empty($value->reference_link) ? $value->reference_link : '' }}</td>
								<td class="product-category">{{ !empty($value->status) ? $value->status : '' }}</td>
								<td>{{ $value->created_at->format('d-m-Y') }}</td>
								<td class="product-action">
									
									<?php if( Auth::user()->role_id ==29){ ?>
									<a href="{{ route('admin.knowledge_based.edit', $value->id) }}">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
									</a>
									<a href="{{ route('admin.knowledge_based.delete', $value->id) }}" onclick="return confirm('Are You Sure To Delete Knowledge Based')">
										<span class="action-delete"><i class="feather icon-trash"></i></span>
									</a>
									<?php }else if(Auth::user()->role_id !=29 && $value->status=='Pending'){ ?>
									<a href="{{ route('admin.knowledge_based.edit', $value->id) }}">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
									</a>
									<a href="{{ route('admin.knowledge_based.delete', $value->id) }}" onclick="return confirm('Are You Sure To Delete Knowledge Based')">
										<span class="action-delete"><i class="feather icon-trash"></i></span>
									</a>
									<?php } ?>
								</td>
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

<!-- Button trigger modal -->
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Description</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
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
	
	$("body").on("click", "#download_excel", function (e) {
		var data = {};
			data.title   = $('.title').val(),
			data.status = $('.status').val(), 
			window.location.href = "<?php echo URL::to('/admin/'); ?>/knowledge-based-report-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});
	
	$(".description_view").on("click", function() {
		var request_id = $(this).attr("data-id"); 
			
		var des = $('.kb_description'+request_id).html(); 
		
		
		$('.modal-body').html(des);
		
		$('#exampleModal').modal({
				backdrop: 'static',
				keyboard: true, 
				show: true
		});
	}); 
</script>
@endsection
