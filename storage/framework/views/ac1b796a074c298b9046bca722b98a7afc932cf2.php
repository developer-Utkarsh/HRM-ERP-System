
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Monthly PO/WO VS Invoice</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
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
		<div class="content-body">			
			<section id="data-list-view" class="data-list-view-header">	
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="<?php echo e(route('admin.request.reports.monthly-master-data')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-2">
											<fieldset class="form-group">
												<label for="users-list-status">Month</label>
												<input type="month" class="form-control" name="fmonth" placeholder="Month" value="<?php echo e(app('request')->input('fmonth')??date('Y-m')); ?>">
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
												<select class="form-control select-multiple branch_id" name="branch_id">
													<option value="">Select Any</option>
													<?php if(count($branches) > 0): ?>
													<?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" <?php if($value->id == app('request')->input('branch_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php endif; ?>
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Location</label>
											<?php 
											$location = app('request')->input('location_id');
											$blocation = \App\Branch::where('status', '1'); 
											if(!empty($location)){
												$blocation->where('branch_location', $location);
											}
											$blocation = $blocation->orderBy('id','desc')->groupby('branch_location')->get();											
											?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple location_id" name="location_id">
													<option value="">Select Any</option>
													<?php if(count($blocation) > 0): ?>
													<?php $__currentLoopData = $blocation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->branch_location); ?>" <?php if($value->branch_location == app('request')->input('location_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value->branch_location); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php endif; ?>
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-5 pt-2">
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="<?php echo e(route('admin.request.reports.monthly-master-data')); ?>" class="btn btn-warning">Reset</a>
											<button type="button" class="btn btn-primary" id="downloadBtn">Export</a>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive" id="tableData">
					<table class="table data-list-view">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Date</th>
								<th>Req. Type</th>
								<th>Request No.</th>
								<th>Employee</th>
								<th>Category</th>
								<th>Sub Category</th>
								<th>Product</th>
								<th>Description</th>
								<th>UOM</th>
								<th>Qty</th>								
								<th>Rate</th>
								<th>Amount</th>										
								<th>GST</th>		
								<th>GST Amt</th>		
								<th>Total Amt</th>
								<th>PO No</th>		
								<th>PO Amount</th>
								<th>PO Vendor</th>	
								<th>Payment Type</th>		
								<th>Date Of Invoice</th>		
								<th>Invoice No</th>	
								<th>Invoice Amount</th>	
								<th>Remark</th>	
								<th>Invoice Vendor</th>
								<th>Branch</th>	
								<th>City</th>	
								<th>GRN</th>	
							</tr>
						</thead>
						<tbody>	
							<?php 
								$i = 1; 
								foreach($record as $re){ 
							?>
							<tr>
								<td><?php echo e($i); ?></td>
								<td><?php echo e(date('d-m-Y', strtotime($re->created_at))); ?></td>
								<td><?php if($re->request_type=='1'){ echo 'WRL'; }else{ echo 'MRL';} ?></td>
								<td>REQ-<?php echo e($re->unique_no); ?></td>
								<td><?php echo e($re->name); ?></td>
								<td><?php echo e($re->cname); ?></td>
								<td><?php echo e($re->sname); ?></td>
								<td><?php echo e($re->pname); ?></td>
								<td><?php echo e($re->requirement); ?></td>
								
								<td><?php echo e($re->uom); ?></td>
								<td><?php echo e($re->qty); ?></td>
								<td><?php echo e($re->rate); ?></td>
								<td><?php echo e($re->amount); ?></td>
								<td><?php echo e($re->gst_rate); ?></td>
								<td><?php echo e($re->gst_amt); ?></td>
								<td><?php echo e($re->total); ?></td>
								
								<td><?php if($re->po_no!=0){ echo 'UTKPO-'.$re->po_location.'-'.$re->po_no.'/'.$re->po_month; } ?></td>
								<td><?php echo e($re->po_amt); ?></td>
								<td><?php echo e($re->po_vendor); ?></td>								
								<td>
									<?php 
										if($re->type==1){ 
											echo 'Credit'; 
										}else if($re->type==2){ 
											echo 'Cash'; 
										}
									?>
								</td>
								<td><?php echo e($re->date_of_invoice); ?></td>
								<td><?php echo e($re->invoice_no); ?></td>
								<td><?php echo e($re->Invoive_Amt); ?></td>
								<td><?php echo e($re->remark); ?></td>
								<td><?php echo e($re->invoice_vendor); ?></td>
								<td><?php echo e($re->branch); ?></td>
								<td><?php echo e(ucwords($re->branch_location)); ?></td>
								<td>
									<?php 
										if($re->emp_grn!=0){ 
											$name = $re->branch;
											$words = explode(" ", $name);
											$firstLetters = "";
		
											foreach ($words as $word) {
												$firstLetters .= substr($word, 0, 1);
											}
											
											if($re->request_type==1){
												$ctext = "SRN";
											}else{
												$ctext = "GRN";
											}
											
											echo '<b>'.$ctext.' :</b> '.$re->short_name.'/UTK/'.$firstLetters.'/'.date('d-m-Y',strtotime($re->created_at)).'/'.$re->emp_grn;   
										} 
									?>
								</td>
							</tr>
							<?php $i++; } ?>
						</tbody>
					</table>
				</div>
				                  
			</section>
		</div>
	</div>
</div>

<style type="text/css">
	.table tbody td {
		word-break: break-word;
	}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple').select2({
			placeholder: "Select",
			allowClear: true,
			width: '100%'
		});
	});
	
	$(document).ready(function() {
		$('#downloadBtn').click(function() {
			var table = document.getElementById('tableData');
			var wb = XLSX.utils.table_to_book(table, {sheet: "Sheet1"});
			var wbout = XLSX.write(wb, {bookType: 'xlsx', type: 'binary'});
			
			function s2ab(s) {
				var buf = new ArrayBuffer(s.length);
				var view = new Uint8Array(buf);
				for (var i = 0; i < s.length; i++) {
					view[i] = s.charCodeAt(i) & 0xFF;
				}
				return buf;
			}

			saveAs(new Blob([s2ab(wbout)], {type: "application/octet-stream"}), 'table_data.xlsx');
		});
	});

	// Include FileSaver.js (https://github.com/eligrey/FileSaver.js)
	function saveAs(blob, fileName) {
		var a = document.createElement('a');
		a.href = URL.createObjectURL(blob);
		a.download = fileName;
		a.click();
		URL.revokeObjectURL(a.href);
	}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/request/reports/monthly_master_data.blade.php ENDPATH**/ ?>