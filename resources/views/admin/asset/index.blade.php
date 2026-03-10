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
						<h2 class="content-header-title float-left mb-0">Asset</h2>
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
		<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="{{ route('admin.asset.index') }}" method="get" name="filtersubmit">
										@csrf
										<div class="form-body">
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<input type="text" class="form-control" placeholder="Name" id="asset_name" name="name" value="{{ app('request')->input('name') }}">
														@if($errors->has('name'))
														<span class="text-danger">{{ $errors->first('name') }} </span>
														@endif
													</div>
												</div>		                                
												<div class="col-md-8">
													
													<fieldset class="form-group">		
														<a href="{{ route('admin.asset.index') }}" class="btn btn-warning">Reset</a>
													</fieldset>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
			<section id="data-list-view" class="data-list-view-header">
				
				<div class="table-responsive">
					<table class="table data-list-view" style="background:#fff;width:100%" id="assetTable">
						<thead>
							<tr>
								<th width="20%">S. No.</th>
								<th width="20%">Name</th>
								<th width="20%">Quantity</th>
								<th width="20%">In Stock</th>
								<th width="20%">Action</th>
							</tr>
						</thead>
						<tbody>
						
						</tbody id="select-2">
					</table>
				</div>                   
			</section>
			

		</div>
	</div>
</div>


@endsection
@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
function selectRefresh() {
						
	$('.select-multiple').select2({
		width: '100%',
		placeholder: "Select Assign To",
		allowClear: true
	});
	
}


$.fn.serializeObject = function() {
	var o = {};
	var a = this.serializeArray();
	$.each(a, function() {
		if (o[this.name]) {
			if (!o[this.name].push) {
				o[this.name] = [o[this.name]];
			}
			o[this.name].push(this.value || '');
		} else {
			o[this.name] = this.value || '';
		}
	});
	return o;
};
$(document).ready(function () { 
	
        var assetTable = $('#assetTable').DataTable({
			"searching": false, 
			"info": false,
			"ordering": false,
			"lengthChange": false,
			"pageLength": 50,  
            "processing": true,
            "serverSide": true,
            "ajax":{
		     "url": "{{ route('admin.asset.asset-detail') }}",
		     "dataType": "json",
		     "type": "GET",
			 "data": function(data){ 
				 Object.assign(data, $('[name="filtersubmit"]').serializeObject());
				 return data;
			 },
		    },
			preDrawCallback: function(settings) {
				if ($.fn.DataTable.isDataTable('#assetTable')) {
					var dt = $('#assetTable').DataTable();

					//Abort previous ajax request if it is still in process.
					var settings = dt.settings();
					if (settings[0].jqXHR) {
						settings[0].jqXHR.abort();
					}
				}
			},
	    	"columns": [
					{ "data": null, orderable: false, render: function(data, type, row, meta){
					  return meta.row + meta.settings._iDisplayStart + 1;
					} },
					{ "data": null, render:function(data){ 
						var route = '<?php echo url('admin/asset-product-child'); ?>'+'/'+data.id;
						var actionHtml ='';		
						actionHtml = '<a href="'+route+'" class="btn btn-primary btn-sm" title="Asset">Add</a>';
					
						return data.name+' '+actionHtml; 
					} },
					{ "data": "qty" },					
					{ "data": null, render:function(data){   
					   return data.qty - data.transfer_qty;
					}  },
					{ "data": null, render:function(data){ 
						var route = '<?php echo url('admin/add-transfer-asset'); ?>'+'/'+data.id;
						var route2 = '<?php echo url('admin/transfer-asset-history'); ?>'+'/'+data.id;


						var actionHtml ='';		
						actionHtml = '<a href="'+route+'" class="" title="Transfer Asset"><span class="action-edit"><i class="feather icon-repeat"></i></span></a><a href="'+route2+'" class="m-1" title="Transfer Asset History"><span class="action-edit"><i class="feather icon-clock"></i></span></a>';
					
						return actionHtml; 
					} },
		       ]	 
	    });
	
		$("body").on("input change","#asset_name",function(e){
			e.preventDefault();
			assetTable.ajax.reload();
		});
    });
	
	$(document).on("change",".asset_emp", function () { 
		var thisVal= $(this).val();
		
		if (thisVal) {
			var confirm_val = confirm('Are you sure to assigned asset to this employee?'); 
			if(confirm_val){
				$.ajax({
					type : 'POST',
					url : '{{ route('admin.assigned_asset_to_employee') }}',
					data : {'_token' : '{{ csrf_token() }}', 'asset_id': thisVal},
					dataType : 'json',
					success : function (data){ console.log(data.status);		
						if(data.status == false){
							swal("Error!", data.message, "error");
						} else if(data.status == true){		
							swal("Done!", data.message, "success").then(function(){  		
								location.reload();
							});
						}
					}
				});
			}
			else{
				$('.asset_emp').prop('selectedIndex',0);
			}
		}
	});
</script>
@endsection
