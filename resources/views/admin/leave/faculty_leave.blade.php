@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Faculty Leave</h2>
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
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ route('admin.leave.faculty-leave') }}" method="get" name="filtersubmit">
									<div class="row">
									
										<!--div class="col-12 col-md-3 branch_loader">
											<label for="users-list-status">Branch</label>
											<?php $branches = \App\Branch::where('status', '1')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 select_branch_id" name="branch_id" id="">
													<option value="">Select Any</option>
													@if(count($branches) > 0)
													@foreach($branches as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('branch_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>
												<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
											</fieldset>
										</div-->
										<div class="col-12 col-md-3">
											<label for="users-list-status">Date</label>
											<fieldset class="form-group">												
												<input type="date" name="fdate" placeholder="Date" value="{{ (app('request')->input('fdate'))?app('request')->input('fdate') :date('Y-m-d') }}" class="form-control StartDateClass fdate">
											</fieldset>
										</div>
										<div class="col-12 col-md-6">
											<label for="users-list-status">&nbsp;</label>
											<fieldset class="form-group" >		
												<button type="submit" class="btn btn-primary">Search</button>
												<a href="{{ route('admin.leave.faculty-leave') }}" class="btn btn-warning">Reset</a>
												<a href="javascript:void(0)" id="download_pdf" class="btn btn-primary">Export</a>
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
							<th style="width:5%;">S.No.</th>
							<th style="width:20%;">Name</th>
							<th style="width:10%;">Mobile</th>
							<th>Action</th>
							<th>Delete</th>
							
						</tr>
					</thead>
					<tbody>
				<?php 
				$dataFound = 1;
				if (count($get_data) > 0) { 
					foreach ($get_data as $faculty) {
								
						?>
							<tr style="" class="rowdata">
							<td><?=$dataFound?></td>
							<td><?php echo $faculty->name?></td>
							<td><?php echo $faculty->mobile?></td>
							 
							<td>
							<div class="assistant_form">
							
								<input type="hidden" class="faculty_leave_id" value="<?=$faculty->id?>" />
								<textarea class="form-control reason" style="float: left;width: 75%;"><?=$faculty->reason?></textarea> &nbsp;
								 
								<?php
								if(empty(app('request')->input('fdate')) || strtotime(app('request')->input('fdate'). " +15 days") >= strtotime(date('Y-m-d'))){
									?>
										<strong name="" class="submit_reason btn btn-success" style="margin-top: 23px;" value="" >Submit </strong>
									<?php
								}
								?>
							</div>
							
							</td>
							
							<td>
								<a href="javascript:void(0);" class="btn btn-danger delete_faculty_leave" data-id="<?=$faculty->id?>" style="float:right;">Delete</a>
							</td>
							
							
							
							
							</tr>
						 
						<?php 
						$dataFound++; 
					} 
				}
				?>
				</tbody>
				</table>
					 
				</div>       

			</section>
		</div>
	</div>
</div>

<style>
.table tbody td {
    border: solid 1px #ccc !important;
    font-size: 12px;
}
.table thead th {
    border: solid 1px #ccc !important;
    font-size: 14px;
    background: #ededed;
}
</style>
@endsection

@section('scripts')
<script type="text/javascript">

$(".assistant_id").on("change",function(e) {
	$(this).siblings('.change_assistant').show();
})
$(".submit_reason").on("click",function(e) {
		e.preventDefault();
		/* if (!confirm("Do you want change assistant")){
		  return false;
		} */
		var faculty_leave_id = $(this).siblings('.faculty_leave_id').val();
		var reason = $(this).siblings('.reason').val();
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},      
			type: "POST",
			url : '{{ route('admin.faculty_leave_update') }}',
			data : {'faculty_leave_id':faculty_leave_id,'reason':reason,'action':'update'},
			success : function(data){
				if(data.status == false){
					swal("Error!", data.message, "error");
				} else if(data.status == true){					
					swal("Done!", data.message, "success").then(function(){ 
						//location.reload();
					});
				}
			}
		});
	});	
	
	$(".delete_faculty_leave").on("click",function(e) {
		e.preventDefault();
		if (!confirm("Do you want delete")){
		  return false;
		}
		var $_this = $(this);
		var faculty_leave_id = $(this).data('id');
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},      
			type: "POST",
			url : '{{ route('admin.faculty_leave_update') }}',
			data : {'faculty_leave_id':faculty_leave_id,'action':'delete'},
			success : function(data){
				if(data.status == false){
					swal("Error!", data.message, "error");
				} else if(data.status == true){
					$_this.parent('td').parent('tr').remove();
					swal("Done!", data.message, "success").then(function(){ 
						//location.reload();
					});
				}
			}
		});
	});	
	
	$("body").on("click", "#download_pdf", function (e) {
		var data = {};
			data.fdate = $('.fdate').val(),
			window.open("<?php echo URL::to('/admin/'); ?>/faculty-leave-download?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&'));
	});
</script>

@endsection
