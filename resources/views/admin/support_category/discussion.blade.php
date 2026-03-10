@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-6">
						<h2 class="content-header-title float-left mb-0">Discussion</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-6 float-right">
						<div class="float-right">
						   <i class="filterIcon fa fa-filter text-primary" style="font-size:30px;"></i>
						</div>
					</div>
				</div>
			</div> 
		</div>

		<div class="content-body">
			<section id="data-list-view" class="data-list-view-header">
		        @include('admin.support_category.discussion-filter')
		        <div class="table-responsive">
                    @if(!empty(app('request')->input('blockUsers')))
                     <table class="table data-list-view">
						<thead>
							<tr>
								<th>Reg No</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							@if(count($blockList))
								<tr>
									@foreach($blockList as  $key => $val)
									 <td>{{$val->reg_no}}</td>
									 <td><a href='#' class='m-2 btn btn-sm btn-success deleteComment' data-id="{{$val->reg_no}}" data-type="unblock">UnBlock</a></td>
									@endforeach
								</tr>
							@else
							   <tr>
							   	<td colspan="2">No record Found</td>
							   </tr>
							@endif
						</tbody>
					</table>
                    @endif

					<table class="table data-list-view" id="TableSearch">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Name</th>
								<th>Mobile</th>
								<th>Batch</th>
								<th>Question</th>
								<th>Description</th>
								<th>Date</th>
								<th>Status</th>
								<th>ReadPending</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						@if(count($discussion) > 0)
							@foreach($discussion as  $key => $value)
							<tr class="tr-{{$value->id}}">
								<td>{{ $key + 1 }}</td>
								<td >
									{{ $value->student_name }}
									</br>
									@if(!empty($value->image_url)) - <a href="{{ $value->image_url }}" target="_blank">Preview</a>@endif
								</td>
								<td >{{ $value->mobile_no }}</td>
								<td >@if(!empty($value->batch->name)) {{ $value->batch->name }} @endif</td>
								<td >{{ $value->question }}</td>
								<td >{{ $value->description }}</td>
								<td ><?php echo date("d-m-Y",strtotime($value->created_at));?></td>
								<td >
									@if($value->status==1) <span class="text-success"> Active</span>
									@elseif($value->status==5) <span class="text-warning"> Blocked</span>
									@else <span  class="text-danger">Deleted</span> @endif</td>
								<td class="read-{{$value->id}}">
									@if($value->read_pending_count)
									<span class="text-white" style="width: 20px;height: 20px;background-color: red;border-radius: 50%;display: flex;justify-content: center;align-items: center;color: white;font-size: 13px;">{{ $value->read_pending_count }}</span> 
								    @else - @endif</td>
								<td>
									<a href="javascript:void(0)" class="btn btn-outline-info btn-sm mt-1 old_query_data" data-id="{{$value->id}}" title="Old Query" style="padding: 0.5rem 0.5rem;"> <i class="fa fa-reply"></i></a> <br><br>
									<a href='#' class='btn btn-sm btn-danger deleteComment' data-id='{{$value->id}}' data-type='post'>Delete</a>
								</td>
							</tr>
							@endforeach
							
						@else
						<tr ><td class="text-center text-primary" colspan="9">No Record Found</td></tr>
						@endif	
						</tbody>
					</table>
					<div class="col-12 mt-3 text-center">
					  {{$discussion->links()}}
					</div>
				</div>   
			</section>
		</div>
	</div>
</div>

<div id="old-query-form" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Comment List</h5>
			</div>
			
			<form method="post" action="{{ route('admin.support_discussion_reply') }}"  id="replyForm">
				@csrf
				<div class="modal-body">
					<div class="old_data"></div>
					
					<div class="modal-body">
						<div class="form-body">
							
							<h3>Reply</h3><hr>
							<textarea name="description" rows="5" class="form-control"></textarea>
						</div>
					</div>
					
				</div>
				
				<div class="modal-footer">
					<input type="hidden" name="question_id" class="question_id" value="">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary">Reply</button>
				</div>
			</form>
		</div>
	</div>
</div>

@endsection

@push('js')
<script type="text/javascript">
	$(document).on("click",".old_query_data", function() { 
		var enq_id = $(this).attr("data-id");
		$(".read-"+enq_id).text('0');
	    $.ajax({
			type : 'POST',
			url : '{{ route('admin.support-discussion-comment') }}',
			data : {'_token' : '{{ csrf_token() }}', 'enq_id': enq_id},
			dataType : 'html',
			success : function (data){
				$('.old_data').empty();
				$('.old_data').html(data);
				$(".enquiry_id").val(enq_id);
				$(".question_id").val(enq_id);
				
				$('#old-query-form').modal({
					backdrop: 'static',
					keyboard: true, 
					show: true
			    });
		    }
		});
	});
	
	$("#replyForm").submit(function(e) {
		e.preventDefault(); 
		var _this=$(this);
		var question_id=$(_this).find(".question_id").val();
		var form = document.getElementById('replyForm');
		var dataForm = new FormData(form);
	    $.ajax({
			type : 'POST',
			url : '{{ route('admin.support_discussion_reply') }}',
			data : dataForm,
			processData : false,  
			contentType : false,
			dataType : 'json',
			success : function (data){
				if(data.status){
					$(_this).trigger("reset");
					$('#old-query-form').modal('hide');
					alert(data.msg);
				}else{
					alert(data.msg);
				}
		    }
		});
	});

	$(document).on("click",".deleteComment", function(e) { 
		e.preventDefault(); 
		var _this=$(this);
		var id=$(_this).attr("data-id");
		var type=$(_this).attr("data-type");
		if(confirm("Are you sure to "+type+" ?")){
			$.ajax({
				type : 'POST',
				url : '{{ route('admin.support_discussion_delete') }}',
				data : {'_token' : '{{ csrf_token() }}','id':id,'type':type},
				dataType : 'json',
				success : function(data){
					$(_this).text(data.msg);
					$(_this).prop("disabled",true);
					alert(data.msg);
			    }
			});
		}
	});

</script>
@endpush
