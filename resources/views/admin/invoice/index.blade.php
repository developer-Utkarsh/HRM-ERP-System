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
						<h2 class="content-header-title float-left mb-0">Invoice Reports</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
					</div>
				</div>
			</div>
		</div>
		
		<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ route('admin.store-message') }}" method="post">
								@csrf
									<h3>Send Message FROM(ID) to TO(ID)</h3>
									<div class="row">
										
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-verified">From(ID)</label>
											<input type="text" class="form-control" name="fid" value="{{ !empty($start_message_id->id) ? $start_message_id->id : '' }}" required>
											
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-verified">To(ID)</label>
											<fieldset class="form-group">
												<input type="text" class="form-control" name="tid" value="{{ !empty($start_message_id->id) ? $start_message_id->id : '' }}" required>
											</fieldset>
										</div>
										<div class="col-12 col-sm-2 col-lg-2">
											<button type="submit" class="btn btn-primary mt-2">Send</button>
										</div>
									</div>
								</form>
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
								<form action="{{ route('admin.invoice.index') }}" method="get" name="filtersubmit">
									<div class="row">
										
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-verified">From</label>
											<fieldset class="form-group">
												<input type="date" name="fdate" class="form-control" value="{{ app('request')->input('fdate') }}">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-verified">To</label>
											<fieldset class="form-group">
												<input type="date" name="tdate" class="form-control" value="{{ app('request')->input('tdate') }}">
											</fieldset>
										</div>
										<div class="col-12 col-sm-2 col-lg-2">
											<button type="submit" class="btn btn-primary mt-2">Search</button>
										</div>
										<div class="col-12 col-sm-2 col-lg-2">
											<a href="{{ route('admin.invoice.index') }}" class="btn btn-warning mt-2">Reset</a>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<form method="POST" action="{{ route('admin.invoice-multi-download') }}">
						@csrf
						<div class="col-12 col-sm-6 col-lg-3">
							<button type="submit" class="btn btn-primary mt-1">PDF</button>
						</div>
								
					<div class="table-responsive">
						<table class="table data-list-view" id="attendanceTable">
							<thead>
								<tr>
									<th><input type="checkbox" name="check_all" id="check-all"></th>
									<th>ID</th>
									<th>Name</th>
									<th>Invoice No</th>
									<th>Order No</th>
									<th>Contact</th>
									<th>Date</th>
									<th>Amount</th>
									<th>Is Message</th>
									<th>Is Download</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
													
							</tbody>
						</table>
					</div>
				</form>   
			</div>	                
			</section>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
	$(document).ready(function(){
		$('#check-all').on('click',function(){
			if(this.checked){
				$('.checkbox').each(function(){
					this.checked = true;
				});
			}else{
				$('.checkbox').each(function(){
					this.checked = false;
				});
			}
		});
	});
	
	$("body").on("click", "#download_pdf", function (e) {

		var data = {};

			var invoice_id = $(this).siblings('.invoice_id').val();
			// data.invoice_id = $('.invoice_id').val(),

		window.location.href = "<?php echo URL::to('/admin/'); ?>/invoice-report-pdf/" + invoice_id;
		
		/* window.location.href = "<?php echo URL::to('/admin/'); ?>/invoice-report-pdf/" + Object.keys(data).map(function (k) {

			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])

		}).join('&'); */

	});
	
</script>
 

<script>
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
        var attendanceTable = $('#attendanceTable').DataTable({
			"searching": false, 
			"info": false,
			"ordering": false,
			"lengthChange": false,
			"pageLength": 50,  
            "processing": true,
            "serverSide": true,
            "ajax":{
		     "url": "{{ route('admin.invoice.invoice-detail') }}",
		     "dataType": "json",
		     "type": "GET",
			 "data": function(data){
				 Object.assign(data, $('[name="filtersubmit"]').serializeObject());
				 return data;
			 },
		    },
			preDrawCallback: function(settings) {
				if ($.fn.DataTable.isDataTable('#attendanceTable')) {
					var dt = $('#attendanceTable').DataTable();

					//Abort previous ajax request if it is still in process.
					var settings = dt.settings();
					if (settings[0].jqXHR) {
						settings[0].jqXHR.abort();
					}
				}
			},
			"createdRow": function(row, data, dataIndex){
				$('td:eq(6)', row).attr('colspan', 2);
				// $('td:eq(4)', row).remove();
			},
	    	"columns": [
				  { "data": null, render:function(data){
					  var timeHtml = '<input type="checkbox" class="checkbox" name="id[]" value="'+data.id+'">';
					  return timeHtml;
				  } },
		          { "data": null, orderable: false, render: function(data, type, row, meta){
					  return meta.row + meta.settings._iDisplayStart + 1;
				  } },
		          { "data": "name" },
				  { "data": "invoice_no" },
				  { "data": "order_number" },
		          { "data": "contact" },
				  { "data": "date" },				   
				  { "data": "amount" },				   
				  { "data": null , render:function(data){
					 if(data.is_send_mail==0){
						var txt = 'Not Send';
						var clr = 'danger';
					 }
					 else{
						var txt = 'Send';
						var clr = 'success';
					 }
					  return '<button type="button" class="btn btn-sm btn-'+clr+' waves-effect waves-light" style="cursor: default;">'+txt+'</button>';
				  }
				  },
				  { "data": null , render:function(data){
					 if(data.is_download==0){
						var d_txt = 'Not Download';
						var d_clr = 'danger';	
					 }
					 else{
						var d_txt = 'Download';
						var d_clr = 'success';
					 }
					  return '<button type="button" class="btn btn-sm btn-'+d_clr+' waves-effect waves-light" style="cursor: default;">'+d_txt+'</button>';
				  }
				  },			   
				  { "data": null , render:function(data){
					var html = '<a href="javascript:void(0)" id="download_pdf"><span class="action-edit"><i class="feather icon-file-text"></i></span></a>';
						html +='<input type="hidden" name="invoice_id" class="invoice_id" value="'+data.id+'">';
					  return html;
				  }
				  },	
		       ]	 

	    });
    });
</script>
@endsection
