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
						<h2 class="content-header-title float-left mb-0">Manual Invoice</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Manual Invoice</a>
								</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
			<div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
			</div>
		</div>
		
		<div class="content-body">
			<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form action="{{ route('admin.invoice-details') }}" method="post" class="form"  enctype="multipart/form-data">
										@csrf										
										<div class="">
											<div>
												<label>Invoice Date</label>
												<input type="date" class="form-control" name="invoice_date" placeholder="Invoice Date" required /> 
											</div>
											<div class="pt-2">
												<label>Invoice Number</label>
												<input type="text" class="form-control" name="invoice_number" value="" required /> 
											</div>
											<div class="pt-2">
												<label>HandOver To Accounts</label>
												<input type="date" class="form-control" name="handover_accounts" value="" required /> 
											</div>
											<div class="pt-2">
												<label>Invoice Attachment</label>
												<input type="file" class="form-control" name="attachment" value="" required /> 
											</div>
											<div class="pt-2">
												<label>Amount</label>
												<input type="text" class="form-control" name="amount" value="" required /> 
											</div>
											<div class="pt-2">
												<label>Vendor</label>
												<select name="vendor" class="form-control" required>
													<option value="">-- Select --</option>
													<?php 
														$buyer = \App\Buyer::where('is_deleted', '0')->get();
														foreach($buyer as $b){
													?>
													<option value="<?=$b->id;?>"><?=$b->name;?></option>
													<?php } ?>
												</select>
											</div>
											<div class="pt-2">
												<label>Payment Type</label>
												<select name="payment_type" class="form-control" required>
													<option value="">-- Select --</option>
													<option value="1">Credit</option>
													<option value="2">Cash</option>							
												</select>
											</div>
											<div class="pt-2">
												<label>Remark</label>
												<textarea name="remark" class="form-control" required></textarea>
											</div>
											<div class="pt-2"><button type="submit" class="btn btn-success">Submit</button></div>
										</div>
									
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<script type="text/javascript">
	$(".cat_id").on("change", function () {
		var cat_id = $(".cat_id option:selected").attr('value'); 
		if (cat_id) {
			$.ajax({
				type : 'POST',
				url : '{{ route('admin.product.get-sub-cat') }}',
				data : {'_token' : '{{ csrf_token() }}', 'cat_id': cat_id},
				dataType : 'html',
				success : function (data){
					$('.sub_cat_id').empty();
					$('.sub_cat_id').append(data);
				}
			});
		}
	});
	
	
	$(document).ready(function() {
		$(".add-more").click(function(){ 
			var html = $(".copy-fields").html();
			$(".append_div").append(html);    
		});
		$("body").on("click",".remove",function(){ 
			$(this).parents(".remove_row").remove();
		});
	});
</script>
@endsection
