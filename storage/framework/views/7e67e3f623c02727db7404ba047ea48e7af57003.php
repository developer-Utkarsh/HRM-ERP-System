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
									<?php if($buyer['name']!=""){ echo $buyer['name']; }else{ echo '-'; }?> </br>
									<?=$record['address'];?> </br>
									GSTIN - <?=$record['gstin'];?></br>
									PHONE - <?=$record['phone'];?> </br>
									<?php if(!empty($record['email'])){ ?>EMAIL - <?=$record['email'];?> <?php } ?>
								</div>
								<div class="col-md-6 col-12">
									<?php 
										if(!empty($record['po_month'])){
											$po_month = $record['po_location'].'-'.$record['po_no']."/".$record['po_month'];
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
											<th width="70">S. No.</th>
											<th>Particulars</th>
											<th>UOM</th>
											<th>Qty</th>
											<th>Rate / Rs.</th>
											<th>Amount / Rs.</th>
											<th>GST Rate</th>
											<th>GST Amount</th>
											<th>Total Amount / Rs.</th>
										</tr>
									</thead>
									<tbody>
										<?php 
											$sql = DB::table('po_history')->where('asset_id', $record['request_id'])->get();
											$i = 1;
											foreach($sql as $re){
										?>
										<tr>
											<td><?=$i;?></td>
											<td><?=$re->item;?></td>
											<td><?=$re->uom;?></td>
											<td><?=$re->qty;?></td>
											<td><?=$re->rate;?></td>
											<td><?=$re->amount;?></td>
											<td><?=$re->gst_rate;?></td>
											<td><?=$re->gst_amt;?></td>
											<td><?=$re->total;?></td>
										</tr>
										<?php $i++; } ?>
										<tr>
											<td colspan="8" align="right"><b>Total Amount</b></td>
											<td ><b><?=$record['final_amt'];?></b></td>
										</tr>
									</tbody>
								</table>
							</div>
							<?php if($record['narration']!=""){ ?><div class="p-1"><b>Narration : </b> <?=$record['narration'];?></div><?php } ?>
							<?php if($record['advance']!=""){ ?><div class="p-1"><b>Advance : </b> <?=$record['advance'];?> % - (<?=$record['advance_amt'];?> /-)</div> <?php } ?>
							<div class="p-1">
								<div><b>Terms & Conditions :</b></div>
								<div>
								<?php echo nl2br($record['terms']); ?>
								</div>
							</div>
							<div class="row pt-5 text-center pb-2">	
								<div class="col-md-4 col-12 pt-3">
									<?php if($record['purchase_status']==3 || $record['purchase_status']==2){ ?><span style='font-size:20px;'><b><i class="fa fa-check text-success" aria-hidden="true"></i></b></span> Approved </br></br><?php }else{ echo '<br></br>'; } ?>
									<b>Store & Purchase Head</b>
									</br>
									(<?php if($pName['name']!=""){ echo $pName['name']; }else{ echo '-'; }?>)
										
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
									(<?php if($hodName['name']!=""){ echo $hodName['name']; }else{ echo '-'; }?>)
										
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
			<?php 
				if($record['dm_status']==0){
			?>
			<!--
			<button type="button" class="btn btn-success" onclick="poAction('<?=$record['request_id'];?>','1')">Accept</button>
			<button type="button" class="btn btn-danger" onclick="poAction('<?=$record['request_id'];?>','2')">Reject</button>
			-->
			<select class="form-control poStatus" name="poStatus" onchange="showFiled(this.value)">
				<option value="">-- Select Status -- </option>
				<option value="1">Accept</option>
				<option value="2">Reject</option>
			</select>
			<input type="hidden" name="auth_id" class="auth_id" value="<?php echo $emp_id; ?>"/>
			<div class="text-left pt-2 rejectClass" style="display:block;">
				<label>Reject Reason</label><textarea name="reject_reason" class="form-control reject_reason" placeholder="Reject Reason"></textarea>
			</div>
			<button type="button" class="btn btn-success mt-2" onclick="poAction('<?=$record['request_id'];?>')">Update Status</button>
			<?php }  ?>
			
			
			<button type="button" class="btn btn-primary mt-2" onClick="printPo()">Print</button>
		</div>
		<style type="text/css">
			@media  print {
			  .noPrint{
				display:none;
			  }
			}
		</style>
				
		<script src="<?php echo e(asset('laravel/public/admin/js/vendors.min.js')); ?>"></script>
		
		<script type="text/javascript">
			function showFiled(value){
				if(value==2){
					$('.rejectClass').show();
				}else{
					$('.rejectClass').hide();
				}
			}
			
			
			function poAction(id, value){  
				value	=	$('.poStatus').val();
				rreason	=	$('.reject_reason').val();
				auth_id	=	$('.auth_id').val();
				
				$.ajax({
					type : 'POST',
					url : '<?php echo e(route('web-view-po-status-update')); ?>',
					data : {'_token' : '<?php echo e(csrf_token()); ?>', 'id': id, 'value':value,'rreason':rreason,'auth_id':auth_id},
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
		</script>
		<script>

		function printPo(){
			window.print(); 
			// window.close();
		};
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
										<?php /**PATH /var/www/html/laravel/resources/views/po.blade.php ENDPATH**/ ?>