@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Quotation List</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
						<a href="{{ route('admin.request.requisition-request') }}" class="btn btn-primary float-right ">Requisition Request</a>
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
								<th>Employee Requirement</th>		
								<th>Quotation</th>		
								<th>Attachment</th>		
								<?php if(Auth::user()->role_id==31){ ?><th>Action</th><?php } ?>
							</tr>
						</thead>
						<tbody>			
							<?php 
								$i = 1;
								foreach($quotation as $q){
							?>	
							<tr>
								<td><?=$i;?></td>
								<td><?=$q->requirement;?></td>		
								<td><?=$q->request;?></td>		
								<td>
									<?php if($q->attachment!=""){ ?>
										<a href="{{ asset('laravel/public/quotation/'. $q->attachment) }}" title="Click Here" download>Download Quotation</a>
									<?php }else{ ?>
										---
									<?php } ?>
								</td>		
								<?php if(Auth::user()->role_id==31){ ?>
									<td>
										<?php if($q->attachment==""){ ?>
											<a href="javascript:void(0)"  data-id="{{ $q->id }}" class="send_quotation btn-success" style="padding:3px;">Upload Quotation</a>
										<?php }else{ echo 'Not Editable'; } ?>
										
									</td>
								<?php } ?>
							</tr>
							<?php $i++; } ?>
						</tbody>
					</table>
				</div>				                  
			</section>
		</div>
	</div>
</div>



<div class="modal fade" id="reqQuotation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<form action="{{ route('admin.quotation-upload') }}" method="post" class="form"  enctype="multipart/form-data">
		@csrf
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Upload Quotation</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body fill-name">
					<input type="hidden" name="quotation_id" value="" class="appointment_id"/> 
					<div class="row">
						<div class="col-9">
							<input type="file" name="attachment" value="" class="form-control"/> 
						</div>
						<div class="col-3">
							<button class="btn-info add-more border-0" type="button" data-row="0">Add More</button>
						</div>
					</div>
					<div class="append_div">
					</div>
					</br>
					<button type="submit" class="btn btn-success">Submit</button>
				</div>
			</div>
		</div>
	</form>
	
	<!-- Hide Row -->
	<div class="copy-fields" style="display:none;">
		<div class="row remove_row pt-2">
			<div class="col-9">
				<input type="file" name="attachment" value="" class="form-control"/> 
			</div>
			<div class="col-3">
				<button class="btn-danger remove border-0" type="button" style="margin-top:18px;">Remove</button>
			</div>
		</div>
	</div>
</div>
@endsection


@section('scripts')
<script type="text/javascript">	
	$('.add-more').click(function() {
		var html = $(".copy-fields").html();
		$(".append_div").append(html);    
		
	});
	
	
	$("body").on("click",".remove",function(){ 
		$(this).parents(".remove_row").remove();
	});
	
	
	$(".send_quotation").on("click", function() {  
		var request_id = $(this).attr("data-id"); 
		
		$('#reqQuotation').modal({
				backdrop: 'static',
				keyboard: true, 
				show: true
		});
				
		$('.appointment_id').val(request_id);		
	}); 
	

	$('#quotation').change(function () {
		var fileName=this.value;
		var ext =fileName.substr(fileName.lastIndexOf('.') + 1);// this.value.match(/\.(.+)$/)[1];
		ext=ext.toLowerCase();
		switch (ext) {
			case 'jpg':
			case 'jpeg':
			case 'png':
			case 'gif':
			case 'pdf':
			break;
			default:
			alert('This is not an allowed file type.');
			this.value = '';
		}
	});
</script>
@endsection