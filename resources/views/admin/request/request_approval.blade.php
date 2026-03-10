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
						<h2 class="content-header-title float-left mb-0">InStock Request Approval List</h2>
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
								<form action="" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-verified">Req. No.</label>
											<fieldset class="form-group">
												<input type="text" class="form-control rnumber" name="rnumber" value="{{ app('request')->input('rnumber') }}">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-verified">Po / Wo No. </label>
											<fieldset class="form-group">
												<input type="text" class="form-control pwnumber" name="pwnumber" value="{{ app('request')->input('pwnumber') }}">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-verified">Product Title</label>
											<fieldset class="form-group">
												<input type="text" class="form-control pname" name="pname" value="{{ app('request')->input('pname') }}">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-verified">User Name </label>
											<fieldset class="form-group">
												<input type="text" class="form-control uname" name="uname" value="{{ app('request')->input('uname') }}">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Department</label>
											<?php 
											$department_id = app('request')->input('department_id');
											$department = \App\Department::where('is_deleted', '0'); 
											if(!empty($department_id)){
												$department->where('id', $department_id);
											}
											$department = $department->orderBy('id','asc')->get();											
											?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 department_id" name="department_id">
													<option value="">Select Any</option>
													@if(count($department) > 0)
													@foreach($department as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('department_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Branch</label>
											<?php 
											$branch_location = app('request')->input('branch_id');
											$branches = \App\Branch::where('status', '1'); 
											if(!empty($branch_location)){
												$branches->where('id', $branch_location);
											}
											$branches = $branches->orderBy('id','desc')->get();											
											?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 branch_id" name="branch_id">
													<option value="">Select Any</option>
													@if(count($branches) > 0)
													@foreach($branches as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('branch_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-verified">From</label>
											<fieldset class="form-group">
												<input type="date" name="fdate" class="form-control StartDateClass fdate" value="{{ app('request')->input('fdate') }}" id="">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-verified">To</label>
											<fieldset class="form-group">
												<input type="date" name="tdate" class="form-control EndDateClass tdate" value="{{ app('request')->input('tdate') }}" id="">
											</fieldset>
										</div>
										<?php 										
											if( Auth::user()->role_id ==29 || Auth::user()->role_id ==31 || Auth::user()->id ==8799 || Auth::user()->id == 6859){
										?>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">PO Status</label>
											<fieldset class="form-group">												
												<select class="form-control po_status" name="po_status">
													<option value="">Select</option>													
													<option value="0">Pending</option>
													<option value="1">Approved</option>
													<option value="2">Reject</option>
												</select>												
											</fieldset>
										</div>
										<?php } ?>
										
										<div class="col-12 text-right">
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('admin.request.requisition-request') }}" class="btn btn-warning">Reset</a>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form method="get" name="filtersubmit">
									<div class="row">
										<div class="col-md-12">
											<strong for="users-list-role">D.H. : Department Head, &nbsp;&nbsp;</strong>
											<strong for="users-list-role">I.T. : Inventory Team, &nbsp;&nbsp;</strong>
											<strong for="users-list-role">P.T. : Purchase Team, &nbsp;&nbsp;</strong>
											<strong for="users-list-role">D.M. : Decision Maker / PO Approval Team, &nbsp;&nbsp;</strong>
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
								<th>Created Date</th>	
								<th>Location</th>	
								<th>Requirements</th>	
								<th>MRL Status</th>	
								<th>Action</th>								
							</tr>
						</thead>
						<tbody>			
							@if(count($notification) > 0)
								@foreach($notification as  $key => $value)
									<?php 
										$approval = DB::table('users')->where('approval_id',Auth::user()->id)->get();
										
										if((Auth::user()->id == 8799 || Auth::user()->id==6859) && $value->po_important=='Yes'){
											$bgClass = 'background-color:#fff1f1';
										}else{
											$bgClass = '';
										}
										
										$qstatus=$qstatus2=$qstatus3=$qstatus4=$qstatus5 = array();
										if(Auth::user()->user_details->degination == 'CENTER HEAD' && $value->dname == 'ADMIN'){ 											 
												$qstatus=DB::table('asset_request')
												->leftjoin('asset_request_notification','asset_request_notification.request_id','asset_request.id')
												->where('asset_request_notification.status',0)->where('asset_request.unique_no',$value->unique_no)->get();
										}
										
										if( Auth::user()->role_id ==21 || !empty($approval)){ 											 
											$qstatus2=DB::table('asset_request')
											->leftjoin('asset_request_notification','asset_request_notification.request_id','asset_request.id')
											->where('asset_request_notification.status',0)->where('asset_request.unique_no',$value->unique_no)->get();
										}
										
										
										if( Auth::user()->role_id ==31){
											$qstatus3=DB::table('asset_request')
											->leftjoin('asset_request_notification','asset_request_notification.request_id','asset_request.id')
											->where('asset_request_notification.it_status','=',2)
											->where('asset_request_notification.purchase_status','!=',2)
											->where('asset_request_notification.purchase_status','!=',4)
											->where('asset_request_notification.purchase_status','!=',5)
											->where('asset_request_notification.purchase_status','!=',6)
											->where('asset_request.unique_no',$value->unique_no)->get();
										}
										
										
										if( Auth::user()->user_details->degination == 'MANAGER-PURCHASE & STORE'){
											$qstatus7=DB::table('asset_request')
											->leftjoin('asset_request_notification','asset_request_notification.request_id','asset_request.id')
											// ->where('asset_request_notification.purchase_status',3)
											->where('asset_request_notification.dm_status',0)
											->whereRaw("
												(
													(asset_request_notification.purchase_status = '3' 
														 OR asset_request_notification.purchase_status = '2')
														AND (asset_request_notification.company != 'na' 
															 AND asset_request_notification.company != '-')
												)
											")
											->where('asset_request.unique_no',$value->unique_no)->get();
										}
										
										if( Auth::user()->id ==8799 || Auth::user()->id==6859){
											$qstatus4=DB::table('asset_request')
											->leftjoin('asset_request_notification','asset_request_notification.request_id','asset_request.id')
											// ->where('asset_request_notification.purchase_status',3)
											->where('asset_request_notification.dm_status',0)
											->whereRaw("
												(
													asset_request_notification.it_status = 5 AND asset_request.type = 'Asset'
													OR 
													(
														(asset_request_notification.purchase_status = '3' 
														 OR asset_request_notification.purchase_status = '2')
														AND (asset_request_notification.company != 'na' 
															 AND asset_request_notification.company != '-')
													)
												)
											")
											->where('asset_request.unique_no',$value->unique_no)->get();
										}
										
										
										if( Auth::user()->role_id ==25){
											$qstatus5=DB::table('asset_request')
											->leftjoin('asset_request_notification','asset_request_notification.request_id','asset_request.id')
											->where('asset_request_notification.purchase_status',0)
											->whereRaw("(asset_request_notification.it_status = 0 OR asset_request_notification.it_status = 5 )")
											->where('asset_request_notification.status', 1)
											->where('asset_request.unique_no',$value->unique_no)->get();
										}
										
										
										if( Auth::user()->role_id ==33){
											$qstatus6=DB::table('asset_request')
											->leftjoin('asset_request_notification','asset_request_notification.request_id','asset_request.id')
											->where('asset_request_notification.purchase_status',0)
											->where('asset_request_notification.it_status', 4)
											->where('asset_request_notification.status', 1)
											->where('asset_request.unique_no',$value->unique_no)->get();
										}
									?>
								
								<tr style="<?=$bgClass;?>"> 
									<td>{{ $pageNumber++ }}</td>
									<td>{{ date('d-m-Y h:i:s', strtotime($value->created_at)) }}</td>
									<td>{{ $value->bname }}</td>
									<td>
										<b>( REQ-{{ $value->unique_no }} - {{ $value->dname }} )</b> - {{ $value->message }} 
										<a href="javascript:void(0)"  data-id="{{ $value->unique_no }}" class="get_edit_data text-primary">View</a>										
									</td>
									<td>
										<?php
											//Center Head
											$mrlMsg = '';
											if(Auth::user()->user_details->degination == 'CENTER HEAD' && $value->dname == 'ADMIN'){ 
												if(count($qstatus) >= 1){ 
													$mrlMsg = 'Partial Approved';
												}else{ 
													$mrlMsg = '<span class="text-success">Approved</span>'; 
												} 
											} 
										?>
										
										
										<?php 	
											//D.H.	
											if(Auth::user()->role_id ==21 || !empty($approval)){ 
												if(count($qstatus2) >= 1){ 
													$mrlMsg = 'Partial Approved';
												}else{ 
													$mrlMsg = '<span class="text-success">Approved</span>';  
												} 
											} 
										?>	
										
										<?php 
											//Purchase Team		
											if( Auth::user()->role_id ==31){
												if(count($qstatus3) >= 1){ 
													$mrlMsg = 'Partial Approved';
												}else{ 
													$mrlMsg = '<span class="text-success">Approved</span>'; 
												} 
											}
										?>
										
										<?php 
											//Purchase Manager
											if(Auth::user()->user_details->degination=='MANAGER-PURCHASE & STORE'){ 	
												if(count($qstatus7) >= 1){ 
													$mrlMsg = 'Partial Approved';
												}else{ 
													$mrlMsg = '<span class="text-success">Approved</span>'; 
												} 
											}
										?>
										
										<?php 
											//PO Team
											if( Auth::user()->id ==8799 || Auth::user()->id==6859){												
												if(count($qstatus4) >= 1){ 
													$mrlMsg = 'Partial Approved';
												}else{ 
													$mrlMsg = '<span class="text-success">Approved</span>'; 
												} 
											}
										?>
										
										<?php 
											//Inventory		
											if( Auth::user()->role_id ==25){
												if(count($qstatus5) >= 1){ 
													$mrlMsg = 'Partial Approved';
												}else{ 
													$mrlMsg = '<span class="text-success">Approved</span>'; 
												} 
											}	
										?>
										
										<?php 
											//Networking		
											if( Auth::user()->role_id ==33){
												if(count($qstatus6) >= 1){ 
													$mrlMsg = 'Partial Approved';
												}else{ 
													$mrlMsg = '<span class="text-success">Approved</span>'; 
												} 
											}	
											
											echo $mrlMsg;
										?>
									</td>
									<td>
										<?php
											$actionErr = "";
											//Center Head
											if(Auth::user()->user_details->degination == 'CENTER HEAD' && $value->dname == 'ADMIN'){ 
												if(Auth::user()->id != $value->user_id){
													if(count($qstatus) >= 1 ){
														$actionErr = "";
										?> 
										<a href="{{ route('admin.request.edit-requisition', $value->unique_no) }}">
											<span class="action-edit"><i class="feather icon-edit"></i></span>
										</a>
										<?php 		
													}else{ $actionErr = 'Not Editable'; }
											}else{ $actionErr = 'Not Editable'; }} ?>
										
										
										<?php 	
											//D.H.	
											if(Auth::user()->role_id ==21 || !empty($approval)){ 
											if(count($qstatus2) >= 1){ 
												$actionErr = "";
										?> 
										<a href="{{ route('admin.request.edit-requisition', $value->unique_no) }}">
											<span class="action-edit"><i class="feather icon-edit"></i></span>
										</a>
											<?php }else{ $actionErr = 'Not Editable'; } } ?>	
										
										<?php 
											//Purchase Team		
											if( Auth::user()->role_id ==31){
												if(count($qstatus3) >= 1){ 
													$actionErr = "";
										?>										
										<a href="{{ route('admin.request.edit-requisition', [ $value->unique_no, 1 ]) }}">
											<span class="action-edit"><i class="feather icon-edit"></i></span>
										</a>										
										<?php }else{ $actionErr = 'Not Editable'; } }?>
										
										
										
										<?php 
											//Purchase Manager Team
											if(Auth::user()->user_details->degination=='MANAGER-PURCHASE & STORE'){ 	
												if(count($qstatus7) >= 1){ 
												$actionErr = "";
										?>										
										<a href="{{ route('admin.request.edit-requisition', [ $value->unique_no, 4 ]) }}">
											<span class="action-edit"><i class="feather icon-edit"></i></span>
										</a>										
										<?php }else{ $actionErr = 'Not Editable'; } } ?>
										
										<?php 
											//PO Team
											if( Auth::user()->id ==8799 || Auth::user()->id==6859){
											if(count($qstatus4) >= 1){ 
												$actionErr = "";
										?>										
										<a href="{{ route('admin.request.edit-requisition', [ $value->unique_no, 2 ]) }}">
											<span class="action-edit"><i class="feather icon-edit"></i></span>
										</a>										
										<?php }else{ $actionErr = 'Not Editable'; } } ?>
										
										<?php 
											//Inventory		
											 if( Auth::user()->role_id ==25){
												if(count($qstatus5) >= 1){ 
													$actionErr = "";
										?>		
											<a href="{{ route('admin.request.edit-requisition', [ $value->unique_no, 3 ]) }}">
												<span class="action-edit"><i class="feather icon-edit"></i></span>
											</a>
										<?php 
												}else{ $actionErr = 'Not Editable'; } 
											}	
										?>	

										<?php 
											//Inventory		
											 if( Auth::user()->role_id ==33){
												if(count($qstatus6) >= 1){ 
													$actionErr = "";
										?>		
											<a href="{{ route('admin.request.edit-requisition', [ $value->unique_no, 3 ]) }}">
												<span class="action-edit"><i class="feather icon-edit"></i></span>
											</a>
										<?php 
												}else{ $actionErr = 'Not Editable'; } 
											}	
											
											
											echo $actionErr;
										?>	
										
										
									</td>
								</tr>
								@endforeach			
							@else
								<tr>
									<td class="text-center" colspan="12">No Data Found</td>
								</tr>	
							@endif
						</tbody>
					</table>
					<div class="d-flex justify-content-center">					
					{!! $notification->appends($params)->links() !!}
					<?php //die(); ?>
					</div>
				</div>
				                  
			</section>
		</div>
	</div>
</div>

<div class="modal fade bd-example-modal-xl" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Requisition</h5>
				&nbsp;&nbsp;<button type="button" id="downloadPDF">Print</button>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body fill-name">
				
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="reqQuotation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<form action="{{ route('admin.quotation-add') }}" method="post" class="form"  enctype="multipart/form-data">
		@csrf
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Request Quotation</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body fill-name">
					<input type="hidden" name="appointment_id" value="" class="appointment_id"/> 
					<textarea class="form-control" name="quotation" rows="4" placeholder="Quotation"></textarea> 
					</br>
					<button type="submit" class="btn btn-success">Submit</button>
				</div>
			</div>
		</div>
	</form>
</div>




@if(isset($_GET['po']))
    <div class="alert alert-success">        
		<script>window.open('po/'+<?=$_GET['id'];?>, '_blank');</script>
    </div>
@endif


<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form action="{{ route('admin.copy-mrl') }}" method="post">
				@csrf
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Requisition</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="col-12">
						<select class="form-control" name="rNumber" required>
							<option value="">-- Select MRL Number --</option>
							<?php 
								$aRequest = array(); //DB::table('asset_request')->orderBy('unique_no','desc')->get();
								foreach($aRequest as $ar){
									if(!empty($ar->unique_no)){
							?>
								<option value="<?=$ar->id;?>">REQ-<?=$ar->unique_no;?> - (<?=$ar->title;?>)</option>
							<?php } } ?>
						</select>
					</div>
					<div class="col-12 pt-2">
						 <select class="form-control" name="rUser" required>
							<option value="">-- Select User --</option>
							<?php 
								$user = DB::table('users')->where('mrl_raise','0')->get();
								foreach($user as $u){
							?>
								<option value="<?=$u->id;?>"><?=$u->name;?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Save</button>
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
			placeholder: "Select Any",
			allowClear: true
		});
	});


	
	$(".get_edit_data").on("click", function() {  
		var request_id = $(this).attr("data-id"); 
		
		$('#myModal').modal({
				backdrop: 'static',
				keyboard: true, 
				show: true
		});
		
				
		$.ajax({
			type : 'POST',
			url : '{{ route('admin.show-requisition') }}',
			data : {'_token' : '{{ csrf_token() }}', 'request_id': request_id},
			dataType : 'html',
			success : function (data){
				$('.fill-name').empty();
				
				$('.fill-name').html(data);
			}
		});		
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
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
// document.getElementById("downloadPDF").addEventListener("click", function () {
	// const originalContent = document.querySelector(".fill-name");
	// const clone = originalContent.cloneNode(true);
	// clone.id = "pdf-clone";
	// document.body.appendChild(clone);

	// clone.style.overflow = 'visible';
	// clone.style.width = 'max-content';
	// clone.style.color = 'black';
	// clone.style.background = 'white';

	// clone.querySelectorAll('.remove-column').forEach(el => el.remove());

	// html2canvas(clone, { scale: 2 }).then(canvas => {
		// const imgData = canvas.toDataURL("image/png");

		// const pdf = new jspdf.jsPDF('p', 'mm', 'a4');
		// const pageWidth = pdf.internal.pageSize.getWidth();
		// const pageHeight = pdf.internal.pageSize.getHeight();

		// const canvasWidth = canvas.width;
		// const canvasHeight = canvas.height;

		// const ratio = pageWidth / canvasWidth;
		// const imgHeight = canvasHeight * ratio;

		// let position = 0;

		// while (position < imgHeight) {
			// const pageCanvas = document.createElement("canvas");
			// pageCanvas.width = canvas.width;
			// pageCanvas.height = Math.min(canvas.height, (pageHeight / ratio));

			// const ctx = pageCanvas.getContext("2d");
			// ctx.drawImage(canvas, 0, position / ratio, canvas.width, pageCanvas.height, 0, 0, canvas.width, pageCanvas.height);

			// const pageData = pageCanvas.toDataURL("image/png");
			// if (position > 0) pdf.addPage();
			// pdf.addImage(pageData, 'PNG', 0, 0, pageWidth, (pageCanvas.height * ratio));

			// position += pageHeight;
		// }

		// pdf.save("requisition.pdf");
		// document.body.removeChild(clone);
	// });
// });


/* Live
document.getElementById("downloadPDF").addEventListener("click", function () {
	const originalContent = document.querySelector(".fill-name");
	const clone = originalContent.cloneNode(true);
	clone.id = "pdf-clone";
	document.body.appendChild(clone);

	Object.assign(clone.style, {
		overflow: 'visible',
		width: 'max-content',
		color: 'black',
		fontSize: '20px',
		margin: '10px',
		padding: '0',
		boxSizing: 'border-box',
		background: 'white'
	});

	clone.querySelectorAll('.remove-column').forEach(el => el.remove());

	html2canvas(clone, { scale: 2 }).then(canvas => {
		const imgData = canvas.toDataURL("image/png");
		const pdf = new jspdf.jsPDF('l', 'mm', 'a4');
		const pdfWidth = pdf.internal.pageSize.getWidth();
		const pdfHeight = (canvas.height * pdfWidth) / canvas.width;

		// Set X and Y to 0 to eliminate starting gap in PDF
		pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
		pdf.save("requisition.pdf");
		document.body.removeChild(clone);
	});
});
*/

document.getElementById("downloadPDF").addEventListener("click", function () {
	const originalContent = document.querySelector(".fill-name");
	const clone = originalContent.cloneNode(true);
	clone.id = "pdf-clone";
	document.body.appendChild(clone);

	Object.assign(clone.style, {
		overflow: 'visible',
		width: 'max-content',
		color: 'black',
		fontSize: '20px',
		margin: '10px',
		padding: '0',
		boxSizing: 'border-box',
		background: 'white',
		position: 'absolute',
		left: '-9999px', // Move off-screen so it doesn’t show visually
	});

	clone.querySelectorAll('.remove-column').forEach(el => el.remove());

	html2canvas(clone, { scale: 2 }).then(canvas => {
		const imgData = canvas.toDataURL("image/png");
		const pdf = new jspdf.jsPDF('p', 'mm', 'a4');

		const pdfWidth = pdf.internal.pageSize.getWidth();
		const pdfHeight = pdf.internal.pageSize.getHeight();

		const imgWidth = pdfWidth;
		const imgHeight = (canvas.height * imgWidth) / canvas.width;

		let heightLeft = imgHeight;
		let position = 0;

		// First page
		pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
		heightLeft -= pdfHeight;

		// More pages
		while (heightLeft > 0) {
			position -= pdfHeight;
			pdf.addPage();
			pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
			heightLeft -= pdfHeight;
		}

		pdf.save("requisition.pdf");
		document.body.removeChild(clone);
	});
});

</script>
@endsection