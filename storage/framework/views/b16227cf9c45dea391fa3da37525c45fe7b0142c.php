<html>
	<head>
		 <!-- BEGIN: Theme CSS-->
		 <link rel="stylesheet" type="text/css" href="<?php echo e(asset('laravel/public/admin/css/vendors.min.css')); ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo e(asset('laravel/public/admin/css/bootstrap.css')); ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo e(asset('laravel/public/admin/css/bootstrap-extended.css')); ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo e(asset('laravel/public/admin/css/colors.css')); ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo e(asset('laravel/public/admin/css/components.css')); ?>">
		<style type="text/css">
			body {
				margin :0;
			}
			
			input {
				border: 0;
				border-bottom: solid 1px;
				width: 57%;
			}
			
			input:focus-visible{
				outline: navajowhite;
			} 
			
			.po {
				color : #000;
			}
			
			.po .border-top{
				border-top: solid 1px #000 !important;
			}
			
			.po .border-bottom{
				border-bottom: solid 1px #000 !important;
			}
			
			.table  {
				color : #000 !important;	
			}
			
		
		</style>
	</head>
	<body>
		
		
		<form action="<?php echo e(route('admin.request.po-request-edit')); ?>" method="post" enctype="multipart/form-data" class="po_request_new">
			<?php echo csrf_field(); ?>
			<input type="hidden" name="request_id" value="<?=$id?>"/>
			<?php 
				if($record['request_type']=='1'){ 
					$powoText = 'WO'; 
					$powoHead = 'Work';
				}else{	
					$powoText = 'PO'; 
					$powoHead = 'Purchase';
				} 
			?>
			<div class="po px-2"> 
				<div class="row match-height">
					<div class="col-12">
						<div class="card mb-0" style="border:solid 1px #000">
							<div class="card-content border">
								<div class="text-center py-1">
									<img src="<?php echo e(asset('laravel/public/logo.png')); ?>" width="80"/>
									<h2 class="text-primary pt-1"><b>Utkarsh Classes & Edutech Pvt. Ltd.</b></h2>
									<p>
										<?=$record['po_address'];?>		
									</p>
								</div>
								<div class="row text-center pb-1 font-weight-bold">													
									<div class="col-md-6 col-12">
										Phone: 7849906549
									</div>
									<div class="col-md-6 col-12">
										E-mail: accounts@utkarsh.com		
									</div>
								</div>
								<div class="border-top border-bottom p-1">
									<div class="text-center"><h3 class="text-primary"><b><?=$powoHead;?> Order</b></h3></div>
									<div class="text-center">GST No. - <?=$record['po_gst'];?></div>
								</div>
								
								<div class="row py-2">													
									<div class="col-md-6 col-12 pl-4">									
										<b>To,</b></br>
										<select name="company" class="select-multiple2 form-control blankvalue" onChange="getCompanydetails(this.value)">
											<option value="">-- Select --</option>
											<?php 
												foreach($buyer as $b){ 
													if($b->id == $record['company']){
														$selected = 'selected';
													}else{
														$selected = '';
													}
											?>
												<option value="<?php echo e($b->id); ?>" <?=$selected;?>><?php echo e($b->name); ?></option>
											<?php } ?>
										</select>
										<div class="fill-name">												
											<textarea name='address' placeholder='Address' readonly class="form-control"><?=$record['address'];?></textarea> </br>
											GSTIN - <input type="text" name="gstin" value="<?=$record['gstin'];?>" readonly /> </br>
											PHONE - <input type="text" name="phone" value="<?=$record['phone'];?>" readonly /> </br>													
											Email - <input type="text" name="email" value="<?=$record['email'];?>" readonly /> 											
										</div>
									</div>
									<div class="col-md-6 col-12">
										<?php 
											if(!empty($record['po_month'])){
												$po_month = $record['po_location']."-".$record['po_no']."/".$record['po_month'];
											}else{
												$po_month = $record['po_no'];
											}
										?> 
										</br>
										<?=$powoHead;?> Order Date -	<?php echo date("d-m-Y", strtotime($record['pdate']));?></br>
										<?=$powoText;?> No.:  UTK<?=$powoText;?>-<?=$po_month;?> </br>
										Location - <?php if(!empty($branch['name'])){ echo $branch['name']; }else{ echo '-'; }?>

									</div>
								</div>
								<div class="p-1 border-top border-bottom">
									Dear Sir,
									With reference to your quotation we have pleasure in confirming our <?=$powoHead;?> Order for the following as per the terms & conditions stated hereunder.	
								</div>
								<div class="">
									<table class="table table-bordered">
										<thead>
											<tr>
												<!--<th width="20">S. No.</th>-->
												<th>Particulars</th>
												<th width="90">UOM</th>
												<th width="100">Qty</th>
												<th width="80">Rate / Rs.</th>
												<th width="30">Amount / Rs.</th>
												<th width="70">GST %</th>
												<th width="30">GST Amount</th>
												<th width="100">Total Amount / Rs.</th>
											</tr>
										</thead>
										
										<tbody class="appenthtml">	
											<div class="cRecord" style="display:none;">
												<?php 
													echo $cRecord = '1';
												?>
											</div>
											<?php 
												$sql = DB::table('po_history')->where('asset_id', $record['request_id'])->where('status','active')->get();
												foreach($sql as $re){
											?>										
											<tr class="gethtml1">
												<!--<td><input type="text" name="" value="1" class="w-100"/></td>-->
												<td>
													<select class="w-25 mr-2 options requiId1 blankvalue" name="requiId[]" data-rowno="1">
														<option value="">-- Select --</option>
													</select>
													
													<input type="text" name="item[]" value="<?=$re->item;?>" class="item1 float-right" style="width:70%"/>
												</td>
												<td>
													<!--<input type="text" name="" value="" class="w-100"/>-->
													<select name="uom[]" class="w-100 uom1">
														<option value="">Select</option>
														<?php $uom = DB::table('uom')->get(); ?>
														<?php $__currentLoopData = $uom; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($val->code); ?>" <?php echo e(($re->uom == $val->code) ? 'selected' : ''); ?>>
																<?php echo e($val->code); ?>

															</option>
														<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													</select>
												</td>
												<td><input type="text" name="qty[]" value="<?=$re->qty;?>" placeholder="0" class="w-100 qty1" onblur="getRate(this.value)"/></td>
												<td><input type="text" name="rate[]" value="<?=$re->rate;?>" placeholder="0" class="w-100 rate1 blankvalue" onblur="getRate(this.value)"/></td>
												<td><input type="text" name="amount[]" value="<?=$re->amount;?>" placeholder="0" class="w-100 amount1 blankvalue" readonly /></td>
												<td>
													<select name="gstrate[]" class="w-100 gstrate1 blankvalue" onblur="getgstRate(this.value)">
														<option value="0" <?= ($re->gst_rate == 0) ? 'selected' : '' ?>>0</option>
														<option value="5" <?= ($re->gst_rate == 5) ? 'selected' : '' ?>>5</option>
														<option value="12" <?= ($re->gst_rate == 12) ? 'selected' : '' ?>>12</option>
														<option value="18" <?= ($re->gst_rate == 18) ? 'selected' : '' ?>>18</option>
														<option value="28" <?= ($re->gst_rate == 28) ? 'selected' : '' ?>>28</option>
													</select>
												</td>
												<td><input type="text" name="gstamt[]" value="<?=$re->gst_amt;?>" placeholder="0" class="gstamt1 w-100 blankvalue" readonly /></td>
												<td><input type="text" name="totalamt[]" value="<?=$re->total;?>" placeholder="0" class="totalamt1 w-100 blankvalue" readonly /></td>
											</tr>
											<?php } ?>
										</tbody>
										
										<tfoot>
											<tr>
												<td colspan="7" align="right"><b>Total Amount</b></td>
												<td>
													<input type="text" name="finalAmt" value="<?=$record['final_amt'];?>" class="w-100 blankvalue finalAmt" style="font-size:12px;"/> 
												</td>
											</tr>
											<tr>
												<td colspan="10">
													<button type="button" onclick="rowAppend()">+</button>
													<button type="button" onclick="rowRemove()">-</button>
												</td>
											</tr>
										</tfoot>
									</table>
								</div>
								
								
								
								<div class="p-1">
									<div><b>Narration :</b></div>
									<textarea name="narration" class="form-control blankvalue" placeholder="Narration"> <?=$record['narration'];?></textarea>
								</div>
								<div class="p-1">
									<div><b>Advance :</b></div>
									<div class="row mx-0">
										<div class="float-left w-75">
											<input type="number"  value="<?=$record['advance'];?>" name="advance" class="form-control blankvalue" placeholder="Advance" onBlur="getAdvanceAmt(this.value)">
										</div>
										<div class="float-right w-25 pl-2">
											%   <input type="number"  value="<?=$record['advance_amt'];?>" name="advanceAmt" class="blankvalue advanceAmt" placeholder="Advance Amount" readonly> 
										</div>
									</div>
								</div>
								
								<div class="p-1">
									<div><b>Terms & Conditions :</b></div>
									<div>
										<textarea name="terms" class="form-control terms" rows="8"><?php echo $record['terms']; ?></textarea>
									</div>
								</div>
								
								<div class="p-1">
									<div class="form-group">
										<label>Quotation 1</label>
										<input type="file" name="quotation_one" value="" class="form-control blankvalue"/>
										
										<a href="<?php echo e(asset('laravel/public/po_upload')); ?>/<?=$record['quotation_one'];?>" target="_blank"><?=$record['quotation_one'];?></a>
									</div>
									<div class="form-group">
										<label>Quotation 2</label>
										<input type="file" name="quotation_two" value="" class="form-control blankvalue"/>
										
										<a href="<?php echo e(asset('laravel/public/po_upload')); ?>/<?=$record['quotation_two'];?>" target="_blank"><?=$record['quotation_two'];?></a>
									</div>
									<div class="form-group">
										<label>Quotation 3</label>
										<input type="file" name="quotation_three" value="" class="form-control blankvalue"/>
										
										<a href="<?php echo e(asset('laravel/public/po_upload')); ?>/<?=$record['quotation_three'];?>" target="_blank"><?=$record['quotation_three'];?></a>
									</div>
								</div>
								
								
								
								<div class="row text-center pb-2">	
									<div class="col-md-4 col-12 pt-3">
										<?php if($record['purchase_status']==3 || $record['purchase_status']==2){ ?><span style='font-size:20px;'><b><i class="fa fa-check text-success" aria-hidden="true"></i></b></span> Approved </br></br><?php }else{ echo '<br></br>'; } ?>
										<b>Store & Purchase Head</b>
										</br>
										(<?php if(!empty($pName['name'])){ echo $pName['name']; }else{ echo '-'; }?>)
											
										<?php 
											$pt_updated = date('d-m-Y h:i:s', strtotime($record->pt_updated));
											if($pt_updated != '01-01-1970 05:30:00'){
												$ptUpdate = " </br> ( ".$pt_updated." )";
											}else{
												$ptUpdate = " ";
											}
											echo $ptUpdate;
										?>
									</div>
									<div class="col-md-4 col-12 pt-3">
										<?php if($record['status']==1){ ?><span style='font-size:20px;'><b><i class="fa fa-check text-success" aria-hidden="true"></i></b></span> Approved </br></br><?php }else{ echo '<br></br>'; }  ?>
										<b>HOD</b>
										</br>
										(<?php if(!empty($hodName['name'])){ echo $hodName['name']; }else{ echo '-'; }?>)
										<?php 
											$lastupdate = date('d-m-Y h:i:s', strtotime($record->updated_at));
											if($lastupdate != '01-01-1970 05:30:00'){
												$dUpdate = " </br>( ".$lastupdate." )";
											}else{
												$dUpdate = " ";
											}
											
											echo $dUpdate;
										?>
										
									</div>
									<div class="col-md-4 col-12 pt-3">
										<?php if($record['dm_status']==1){ ?>
											<span style='font-size:20px;'><b><i class="fa fa-check text-success" aria-hidden="true"></i></b></span> Approved </br></br>
										<?php }else if($record['dm_status']==2){ ?>
											<span style='font-size:20px;'><b><i class="fa fa-times text-danger" aria-hidden="true"></i></b></span> Rejected </br></br>
										<?php }else{ echo '<br></br>'; } ?>
										<b>DIRECTOR / CFO / FO</b>
										</br>
										(<?php if(!empty($cfoName['name'])){ echo $cfoName['name']; }else{ echo '-'; }?>)
											
										<?php 
											$dmupdate = date('d-m-Y h:i:s', strtotime($record->dm_updated));
											if($dmupdate != '01-01-1970 05:30:00'){
												$poUpdate = " </br>( ".$dmupdate." )";
											}else{
												$poUpdate = " ";
											}
											
											echo $poUpdate;
										?>
									</div>
								</div>
								<div class="row text-left mx-0">	
									<div class="col-md-6 col-6 pl-4 border-top py-1">
										<b>Accepted By :</b> <?=$record['approved'];?>
									</div>
									<div class="col-md-6 col-6 pl-4 border-top py-1">
										<b>MRL Number :</b>  REQ-<?=$record['unique_no'];?>
									</div>
								</div>
								<div class="row text-left mx-0">	
									<div class="col-md-12 col-12 pl-4 border-top py-1">
										<b>Mention the name of relevant person who approved this requisition. :</b>
										<?php if(is_numeric($record['remark'])){ echo $record['dhname']; }else{ echo $record['remark']; } ?>
									</div>								
								</div>
								<div class="row text-left mx-0">	
									<div class="col-md-12 col-12 pl-4 border-top py-1">
										<b>For which Category the asset is requested :</b>
										<?php if(!empty($record['material_category'])){ echo $record['material_category']; }else{ echo '-'; } ?>
									</div>								
								</div>
								<?php if(!empty($buyer_account->beneficiary)){ ?>
								<div class="row text-left mx-0">	
									<div class="col-md-6 col-6 pl-4 border-top py-1">									
										<?php if(!empty($buyer['pan_no'])){ ?>
											<b>PAN Number :</b> <?=$buyer['pan_no'];?> </br>
										<?php } ?>
										
										<?php if(!empty($buyer['msme_uam_no'])){ ?>
											<b>MSME No / UAM No :</b> <?=$buyer['msme_uam_no'];?> </br>
										<?php } ?>
										<b>Beneficiary's Name :</b> <?=$buyer_account->beneficiary;?> </br>
										<b>Bank Account No. :</b> <?=$buyer_account->account;?></br>
										<b>Bank Name :</b> <?=$buyer_account->bank_name;?></br>
										<b>RTGS/NEFT IFSC CODE :</b> <?=$buyer_account->ifsc;?></br>
										<b>Bank Address :</b> <?=$buyer_account->bank_address;?>
									</div>
									<div class="col-md-6 col-6 pl-4 border-top py-1">
										
									</div>
								</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			
			<div class="text-center w-100 noPrint p-2">
				<div class="px-2 text-right">
					<button type="submit" class="btn btn-primary">Submit</button>
					<a href="<?php echo e(route('admin.request.poprint',[$id])); ?>" class="btn btn-warning">Back</a>
				</div>							
			</div>
		</form>
				
		
		<style type="text/css">
			@media  print {
			  .noPrint{
				display:none;
			  }
			}
		</style>
				
		<script src="<?php echo e(asset('laravel/public/admin/js/vendors.min.js')); ?>"></script>
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				$('.select-multiple1').select2({
					placeholder: "Select",
					allowClear: true
				});
				
				
				$('.select-multiple2').select2({
					placeholder: "Select",
					allowClear: true
				});
				
			});	
			
			function showFiled(value){
				if(value==2){
					$('.rejectClass').show();
				}else{
					$('.rejectClass').hide();
				}
			}
			
			
			function poAction(id){  				
				value	=	$('.poStatus').val();
				rreason	=	$('.reject_reason').val();
				
				$.ajax({
					type : 'POST',
					url : '<?php echo e(route('admin.request.po-status-update')); ?>',
					data : {'_token' : '<?php echo e(csrf_token()); ?>', 'id': id, 'value':value,'rreason':rreason},
					dataType : 'json',
					success : function (data){
						if(data.status == false){
							swal("Error!", data.message, "error");
						} else if(data.status == true){		
							swal("Done!", data.message, "success").then(function(){  		
								location.reload();
							});
						}
					}
				});
			}; 	
			
			
			function getRate(value){
				rowCount  = $(".appenthtml tr").length;
				
				for (i = 1; i <= rowCount; ++i) {					
					qty 	=	$('.qty'+i).val();
					rate 	=	$('.rate'+i).val();
					
					amount 	=	(qty*rate).toFixed(2);
					
					// alert(amount);
					
					$('.amount'+i).val(amount);
				}
			}
			
			
			function getgstRate(value){
				rowCount  = $(".appenthtml tr").length;
				newAmt = 0;
				for (i = 1; i <= rowCount; ++i) {		
					amount = $('.amount'+i).val();
					gstrate = $('.gstrate'+i).val();
					
					gstAmt = ((amount*gstrate)/100).toFixed(2);
					
					finalAmt = parseFloat(amount) + parseFloat(gstAmt);		
					$('.gstamt'+i).val(gstAmt);
					
					finalAmt = finalAmt.toFixed(2);
					$('.totalamt'+i).val(finalAmt);
					
					
					newAmt = (parseFloat(finalAmt) + parseFloat(newAmt)).toFixed(2);
				}
				
				$('.finalAmt').val(newAmt);
			}
			
			function getAdvanceAmt(value){
				finalAmt = $('.finalAmt').val();
				adAmt    = (finalAmt*value)/100;
				
				$('.advanceAmt').val(adAmt);
			}
		</script>
		<script>

		function printPo(){
			window.print(); 
			// window.close();
		};
		
		function getCompanydetails(id){
			$.ajax({
				type : 'POST',
				url : '<?php echo e(route('admin.get-company-details')); ?>',
				data : {'_token' : '<?php echo e(csrf_token()); ?>', 'id': id},
				dataType : 'html',
				success : function (data){
					$('.fill-name').empty();
					
					$('.fill-name').html(data);
				}
			});		
		}
		
		
		function rowAppend(){	
			cRecord =	$('.cRecord').html();		
			options =	$('.options').html();		
			i 		=	parseInt(cRecord)+1;
			
			
			dsfd = '<td><input type="text" name="item[]" value="" class="blankvalue item'+i+'  float-right"  style="width:70%"/><select class="w-25 mr-2 blankvalue options requiId'+i+'" data-rowno="'+i+'" name="requiId[]">	'+options+'</select></td><td><select name="uom[]" class="w-100 uom'+i+'"><option value="">Select</option><?php $uom = DB::table("uom")->get(); ?>	<?php $__currentLoopData = $uom; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>	<option value="<?php echo e($val->code); ?>"><?php echo e($val->code); ?></option>	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></td><td><input type="text" name="qty[]" value="" placeholder="0" class="blankvalue w-100 qty'+i+'" onblur="getRate(this.value)"/></td><td><input type="text" name="rate[]" value="" placeholder="0" class="blankvalue w-100 rate'+i+'" onblur="getRate(this.value)"/></td><td><input type="text" name="amount[]" value="" placeholder="0" class="blankvalue w-100 amount'+i+'" readonly /></td><td><select name="gstrate[]" class="blankvalue w-100 gstrate'+i+'" onblur="getgstRate(this.value)"><option value="0" selected>0</option><option value="5">5</option><option value="12">12</option><option value="18">18</option><option value="28">28</option></select></td><td><input type="text" name="gstamt[]" value="" placeholder="0" class="blankvalue gstamt'+i+' w-100" readonly /></td><td><input type="text" name="totalamt[]" value="" placeholder="0" class="blankvalue totalamt'+i+' w-100" readonly /></td>';
			
			
			 $(".appenthtml").append('<tr class="gethtml'+i+'">'+dsfd+'</tr>');
			 
			 $('.cRecord').html(i);
		}
		
		function rowRemove(){
			cRecord =	$('.cRecord').html();		
			
			if(cRecord > 1){
				$('.gethtml'+cRecord).remove();
				
				i 	= cRecord - 1;
				$('.cRecord').html(i);
			}
		}
		
		
		$(document).ready(function() {
			
			$(".options").each(function() {
				$(this).find('option').remove();
			});
			
			var request_id = <?=$id;?>; 
					
			$('.request_id').val(request_id);			
			$.ajax({
				type : 'POST',
				url : '<?php echo e(route('admin.request.get-request-data')); ?>',
				data : {'_token' : '<?php echo e(csrf_token()); ?>', 'request_id': request_id,'is_edit':'1'},
				dataType : 'json',
				success : function (data){
					if(data.status == true){
						$('.options').append(data.options);
					}
				}
			});
		});

		</script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<?php if(session('success')): ?>
<script>
	swal({
	  	title: "Done!",
	  	text: "<?php echo e(session('success')); ?>",
	  	icon: "success",
	  	button: "ok",
	});
</script>
<?php endif; ?>
<?php if(session('error')): ?>
<script>
	swal({
	  	title: "Error!",
	  	text: "<?php echo e(session('error')); ?>",
	  	icon: "error",
	  	button: "ok",
	});
</script>
<?php endif; ?>
	</body>
</html>
										<?php /**PATH /var/www/html/laravel/resources/views/admin/request/edit_po.blade.php ENDPATH**/ ?>