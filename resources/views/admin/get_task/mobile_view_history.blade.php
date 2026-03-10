<!DOCTYPE html>
<html class="loading" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="">
    <meta name="keywords" content="">   

	<link href="{{url('../laravel/public/logo.png')}}" rel="icon" type="image/ico" />

    <title>{{ config('app.name') }} - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/vendors.min.css') }}">
   
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/colors.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/components.css') }}">
 
</head>
<body class="vertical-layout vertical-menu-modern 2-columns  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="2-columns" style="background-image: linear-gradient(to top, #f38800, #f39300, #f29e00, #f1a900, #f0b400, #efbc02, #eec506, #edcd0e, #edd60e, #ecde11, #eae716, #e8f01c);">

	<?php $role_id = Input::get('role_id') ?>
	<div class="app-content content" style="margin:0;">
	
		<div class="content-wrapper" style="margin:0;">
			<div class="content-header row">
				<div class="content-header-left col-md-9 col-12 mb-2">
					<div class="row breadcrumbs-top">
						<div class="col-6">
							<h2 class="content-header-title float-left mb-0">Task View</h2>
						</div>
						
						<div class="col-6">
							<a href="{{ route('admin.mobile-view-task', ['logged_id'=> $logged_id]) }}" class="btn btn-primary float-right ">View Task</a>
						</div>
					</div>
				</div>
			</div>
			<div class="content-body">
				<div class="content-body">
				<!-- Data list view starts -->
				<section id="data-list-view" class="data-list-view-header">
					
					
					<div class="table-responsive">
						<?php 
							if(count($task) > 0){
								$i = 1; 
								foreach($task as $t){
									$t1 = date('Y-m-d H:i:s', strtotime( $t->created_at ));
									$t2 = date('Y-m-d H:i:s', strtotime('-2 day'));
						?>
						<div style="background:#fff;padding:10px;margin:5px;">
							<div>
								<b>Description : </b> {{ $t->thdescription }}
							</div>
							<div><b>Assigned By : </b>	{{ $t->assign_name }}</div>
							<div><b>Assigned To : </b> {{ $t->emp_name }}</div>
							<div><b>Status : </b> {{ $t->thstatus }}</div>
							<?php if($t->thremark!=''){ ?><div><b>Remark : </b> {{ $t->thremark }}</div> <?php } ?>
							<div style="font-size:18px;text-align:right">
								<?php 
									if($t->thstatus=='Assign' || $t->thstatus=='In Progress'){ 
										if($t->assign_id == $logged_id && $t->emp_id == $logged_id){
								?>
								<a title="Task Edit" href="javascript:void(0)" data-id="{{ $t->thid }}" class="get_edit_data">
									<span class="action-edit"><i class="feather icon-edit"></i></span>
								</a>
								<?php 
										}	
									}
								?>
																	
								<?php if($t->thstatus=='Dropped' || $t->thstatus=='Completed'){ echo '<span style="color:red">Not Editable</span>'; } ?>  
								
								
								<?php 
									if($t->thstatus=='Assign' || $t->thstatus=='In Progress'){  
										if($t1 > $t2){
								?>
								&nbsp;&nbsp;
								<a href="{{ route('admin.task-delete', $t->thid) }}" onclick="return confirm('Are You Sure To Delete Task')">
									<span class="action-delete"><i class="feather icon-trash"></i></span>
								</a>
								<?php } } ?>
							</div>
						</div>	
						<?php 
							$i++; }
							}else{
						?>
						
						<div colspan="10" class="text-center">No Record Found</div>
						
						
						<?php } ?>
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

      <form method="post" action="{{ route('admin.mobile-update-task') }}">
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


    <script src="{{ asset('laravel/public/admin/js/vendors.min.js') }}"></script>

    
	<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>

	
	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
	<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple1').select2({
			placeholder: "Select Any",
			allowClear: true
		});
		$('.select-multiple2').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
		
		
	$('.taskStatus').on("change", function(){
		value = $(this).val();
		
		if(value == "In-progress" || value == "Dropped"){
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
					url : '{{ route('admin.mobile-edit-task') }}',
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
							
							
						let selectValues=['Pending','Not Started','Assign', 'In Progress', 'Completed', 'Dropped','Deleted','Acknowledge'];
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

	</script>
	
	</body>
</html>