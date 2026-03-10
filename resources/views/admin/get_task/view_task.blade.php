@extends('layouts.admin')
@section('content')

<?php $role_id = Auth::user()->role_id; ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">View Task</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
						<a href="{{ route('admin.view-task') }}" class="btn btn-primary mr-1">Back</a>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				
				
				<div class="table-responsive">
					<table class="table data-list-view" style="background:#fff;">
						<thead style="text-align: ;">
							<tr>
								<th>S. No.</th>
								<th>Description</th>
								<th>Assigned By</th>
								<th>Assigned To</th>
								<th>Status</th>								
								<th>Remark</th>								
								<th>Action</th>
							</tr>
						</thead>
						<tbody>		
							<?php 
								if(count($task) > 0){
									$i = 1; 
									foreach($task as $t){
										$t1 = date('Y-m-d H:i:s', strtotime( $t->created_at ));
										$t2 = date('Y-m-d H:i:s', strtotime('-2 day'));
							?>
							<tr>
								<td>{{ $i }}</td>
								<td>
									<?=nl2br($t->thdescription);?>
								</td>
								<td>{{ $t->assign_name }}</td>
								<td>{{ $t->emp_name }}</td>
								<td>{{ $t->thstatus }}</td>
								<td>{{ $t->thremark }}</td>
								<td>
									<?php 
										if($t->thstatus=='Assign' || $t->thstatus=='In Progress'){ 
											if($t->emp_id == Auth::user()->id){
									?>
									<a title="Task Edit" href="javascript:void(0)" data-id="{{ $t->thid }}" class="get_edit_data">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
									</a>
									<?php 
											}	
										}
									?>
									
									<!--
									<a href="{{ route('admin.task-history', $t->thid) }}" title="Task Details">
										<span class="action-edit"><i class="feather icon-eye"></i></span>
									</a>
									-->
									
									<?php if($t->thstatus=='Dropped' || $t->thstatus=='Completed'){ echo '-'; } ?>  
									
									
									<?php 
										if($t->thstatus=='Assign' || $t->thstatus=='In Progress'){  
											if($t1 > $t2){
									?>
									<a href="{{ route('admin.task-delete', $t->thid) }}" onclick="return confirm('Are You Sure To Delete Task')">
										<span class="action-delete"><i class="feather icon-trash"></i></span>
									</a>
									<?php } } ?>
								</td>
							</tr>	
							<?php 
								$i++; }
								}else{
							?>
							<tr>
								<td colspan="10" class="text-center">No Record Found</td>
							</tr>	
							
							<?php } ?>
						</tbody>
					</table>
				</div>                   
			</section>
		</div>
	</div>
</div>


<div class="modal" id="history">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Task History</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <form method="post" action="{{ route('admin.update-task') }}">
      		{{ csrf_field() }}
	      <!-- Modal body -->
			<div class="modal-body">
				<table border="1" width="100%;" cellpadding="5" >
					<tr>
						<td><b>Status</b></td>
						<td>
							<select name="status" class="form-control taskStatus" required >
								<option value="">-- Select Status --</option>
								
							</select>
						</td>
					</tr>
					<tr>
						<td><b>Remark</b></td>
						<td>
							<input type="hidden" class="task_id form-control" name="task_id" value=""/>
							<textarea class="remark form-control" name="remark"></textarea>
						</td>
					</tr>
				</table>
			</div>

			<div class="modal-footer">
				<button type="submit" class="btn btn-primary">Update</button>
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
		placeholder: "Select Name & Code",
		allowClear: true
	});
	$('.select-multiple2').select2({
		placeholder: "Select Status",
		allowClear: true
	});
	
	$('.select-multiple3').select2({
		placeholder: "Select Department",
		allowClear: true
	});
});


$('.taskStatus').on("change", function(){
	value = $(this).val();
	
	if(value == "In Progress" || value == "Dropped"){
		$('.remark').attr('required', true);
	}else{
		$('.remark').attr('required', false);
	}
});



$(".get_edit_data").on("click", function() { 
		var task_id = $(this).attr("data-id");
		if(task_id){
			
			$('#history').modal({
					backdrop: 'static',
					keyboard: true, 
					show: true
			});
			
			
			$.ajax({
				type : 'POST',
				url : '{{ route('admin.edit-task') }}',
				data : {'_token' : '{{ csrf_token() }}', 'task_id': task_id},
				dataType : 'html',
				success : function (data){
					
					var data= JSON.parse(data);
					$.each(data, function(k, v) {
						// $('.date').html(v.date);
						$('.task_id').val(v.id);
						// $('.emp_id').html(v.emp_id);
						// $('.assign_id').html(v.assign_id);
						// $('.title').html(v.title);
						// $('.plan').html(v.plan);
						// $('.description').html(v.description);
						$('.spent').val(v.spent);
						$('.remark').html(v.remark);
						
						//$('.status').html(v.status);
						//$('.voice').html(v.status);
						$('.taskStatus').find('option').remove();
						
						
						//let selectValues=['Pending','Not Started','Assign', 'In Progress', 'Completed', 'Dropped','Acknowledge'];
						let selectValues=['Assign', 'In Progress', 'Completed', 'Dropped'];
						   selectValues.push(v.status);
						let selectValuess = [...new Set(selectValues)];
						$.each(selectValuess, function(key, value) {  
                          if(v.status==value){						
						   $('.taskStatus').append($("<option></option>").attr("selected","selected").attr("value",value).text(value));
						  }else{
							 $('.taskStatus').append($("<option></option>").attr("value", value).text(value));  
						  }
						  
						});
						
						
						
						// $(".voice").attr("src", "<?php echo URL::to('/laravel/public/task_audio'); ?>/" +v.voice);
						// $(".voice").attr("href", "<?php echo URL::to('/laravel/public/task_audio'); ?>/" +v.voice);
					});
					
				}
			});
			
		}
		else{
			$('#history').modal({
					backdrop: 'static',
					keyboard: true, 
					show: true
			});
		}		
	}); 
	
	
	$("body").on("click", "#download_pdf", function (e) {
			
			var data = {};
			data.emp_id = $('.emp_id').val(),
			data.department_type = $('.department_type').val(),
			data.status = $('.status').val(),
			data.fdate = $('.fdate').val(),
			data.tdate = $('.tdate').val(),
			
			// alert($('.emp_id').val());
			
			// /*
			window.open("<?php echo URL::to('/admin/'); ?>/get-task-report-pdf?" + Object.keys(data).map(function (k) {
				return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
			}).join('&'));
		});

</script>


@endsection
