
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Edit Buyer</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Edit Buyer</a>
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
									<form class="form" id="buyerForm" action="<?php echo e(route('admin.buyer.update', $buyer->id)); ?>" method="post" enctype="multipart/form-data">
										<!-- <?php echo method_field('PATCH'); ?> -->
										<?php echo csrf_field(); ?>
										<div class="form-body">
											<div class="row">
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Name</label>
														<input type="text" class="form-control" placeholder="Name" name="name" value="<?php echo e(old('name', $buyer->name)); ?>">
														<?php if($errors->has('name')): ?>
														<span class="text-danger"><?php echo e($errors->first('name')); ?> </span>
														<?php endif; ?>
													</div>
												</div>		

												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Contact No</label>
														<input type="text" class="form-control" placeholder="Contact No" name="contact_no" value="<?php echo e(old('contact_no', $buyer->contact_no)); ?>">
														<?php if($errors->has('contact_no')): ?>
														<span class="text-danger"><?php echo e($errors->first('contact_no')); ?> </span>
														<?php endif; ?>
													</div>
												</div>	
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Email</label>
														<input type="text" class="form-control" placeholder="Email" name="email" value="<?php echo e(old('email', $buyer->email)); ?>">
														<?php if($errors->has('email')): ?>
														<span class="text-danger"><?php echo e($errors->first('email')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">GST No</label>
														<input type="text" class="form-control" placeholder="GST No" name="gst_no" value="<?php echo e(old('gst_no', $buyer->gst_no)); ?>">
														<?php if($errors->has('gst_no')): ?>
														<span class="text-danger"><?php echo e($errors->first('gst_no')); ?> </span>
														<?php endif; ?>
													</div>
												</div>	
												<div class="col-md-4 col-12" style="display:<?php if($buyer->gst_img!='-'){ echo 'block'; }else{ echo 'none';}?>;">
													<div class="form-group">
														<label for="first-name-column">Attach GST Certificate</label>
														<input type="file" class="form-control" name="gst_img" value="<?php echo e(old('gst_img')); ?>">
														
														<?php if($buyer->gst_img!='-'){ ?>
															<a href="<?php echo e(asset('laravel/public/buyer/' . $buyer->gst_img)); ?>" download>Preview</a>
														<?php } ?>
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Pan No</label>
														<input type="text" class="form-control" placeholder="Pan No" name="pan_no" value="<?php echo e(old('pan_no', $buyer->pan_no)); ?>" required>
														<?php if($errors->has('pan_no')): ?>
														<span class="text-danger"><?php echo e($errors->first('pan_no')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-4 col-12" style="display:<?php if($buyer->pan_img!='-'){ echo 'block'; }else{ echo 'none';}?>;">
													<div class="form-group">
														<label for="first-name-column">Attach Pan Certificate</label>
														<input type="file" class="form-control" name="pan_img" value="<?php echo e(old('pan_img')); ?>">
														
														<?php if($buyer->pan_img!='-'){ ?>
															<a href="<?php echo e(asset('laravel/public/buyer/' . $buyer->pan_img)); ?>" download>Preview</a>
														<?php } ?>
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">IS MSME Certificate / UAM No</label>
														<select class="form-control" name=""onchange="getMuo(this.value)">
															<option value="">Select</option>
															<option value="1">MSME Certificate / UAM No</option>
															<option value="2">Declaration Form</option>
														</select>
														<?php if($errors->has('email')): ?>
														<span class="text-danger"><?php echo e($errors->first('email')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												
												
												<div class="col-md-4 col-12 msme" style="display:<?php if($buyer->msme_uam_file!='-'){ echo 'block'; }else{ echo 'none';}?>;">
													<div class="form-group">
														<label for="first-name-column">Attach Certificate</label>
														<input type="file" class="form-control" name="msme_uam_file" value="<?php echo e(old('msme_uam_file')); ?>">
														<?php if($buyer->msme_uam_file!='-'){ ?>
															<a href="<?php echo e(asset('laravel/public/buyer/' . $buyer->msme_uam_file)); ?>" download>Preview</a>
														<?php }else{ echo '-'; } ?>
													</div>
												</div>
												
												
												
												<div class="col-md-4 col-12 msme" style="display:<?php if($buyer->msme_uam_no!='-'){ echo 'block'; }else{ echo 'none';}?>;">
													<div class="form-group">
														<label for="first-name-column">MSME No / UAM No</label>
														<input type="text" class="form-control" placeholder="UAM No" name="msme_uam_no" value="<?php echo e(old('email',$buyer->msme_uam_no)); ?>">
													</div>
												</div>	
												
												
												<div class="col-md-4 col-12 other" style="display:<?php if($buyer->declaration_form!='-'){ echo 'block'; }else{ echo 'none';}?>;">
													<div class="form-group">
														<label for="first-name-column">Attach only MSME Certificate or Declaration Form</label>
														<input type="file" class="form-control" name="declaration_form" value="<?php echo e(old('declaration_form')); ?>">
														<?php if($buyer->declaration_form!='-'){ ?>
															<a href="<?php echo e(asset('laravel/public/buyer/' . $buyer->declaration_form)); ?>" download>Preview</a>
														<?php }else{ echo '-'; } ?>
													</div>
												</div>
												
												<?php if(Auth::user()->register_id==2255){ ?>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Status</label>
														<select class="form-control" name="status">
															<option value="">Select</option>
															<option value="Active" <?php if('Active'==$buyer->status){ echo 'selected'; }?>>Active</option>
															<option value="Inactive" <?php if('Inactive'==$buyer->status){ echo 'selected'; }?>>Inactive</option>
														</select>
														<?php if($errors->has('status')): ?>
														<span class="text-danger"><?php echo e($errors->first('status')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<?php } ?>
												<div class="col-md-12 col-12">
													<div class="form-group">
														<label for="first-name-column">Address</label>
														<textarea class="form-control" placeholder="Address" name="address"><?php echo e(old('address', $buyer->address)); ?></textarea>													
														<?php if($errors->has('address')): ?>
														<span class="text-danger"><?php echo e($errors->first('address')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Pincode</label>
														<input type="number" class="form-control" placeholder="Pincode" name="pincode" value="<?php echo e(old('pincode', $buyer->pincode)); ?>" required>
														<?php if($errors->has('pincode')): ?>
														<span class="text-danger"><?php echo e($errors->first('pincode')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Credit Period Days</label>
														<input type="number" class="form-control" placeholder="Credit Period Days" name="credit_day" value="<?php echo e(old('credit_day', $buyer->credit_day)); ?>" required>
														<?php if($errors->has('credit_day')): ?>
														<span class="text-danger"><?php echo e($errors->first('credit_day')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-4 col-12 other">
													<div class="form-group">
														<label for="first-name-column">Vendor Registration Form with KYC</label>
														<input type="file" class="form-control" name="bank_proof" value="<?php echo e(old('bank_proof')); ?>">
														<?php if($buyer->bank_proof!='-'){ ?>
															<a href="<?php echo e(asset('laravel/public/buyer/' . $buyer->bank_proof)); ?>" download>Preview</a>
														<?php }else{ echo '-'; } ?>
													</div>
												</div>
												
												<div class="col-md-4 col-6">
													<div class="form-group">
														<label for="first-name-column">Bank Documents</label>
														<input type="file" class="form-control" name="bank_proof_2" value="<?php echo e(old('bank_proof_2')); ?>" required>
														<?php if($errors->has('bank_proof_2')): ?>
														<span class="text-danger"><?php echo e($errors->first('bank_proof_2')); ?> </span>
														<?php endif; ?>
														<?php if($buyer->bank_proof_2!=''){ ?>
															<a href="<?php echo e(asset('laravel/public/buyer/' . $buyer->bank_proof_2)); ?>" download>Preview</a>
														<?php }else{ echo '-'; } ?>
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Vendor Type</label>
														<select class="form-control" name="type">
															<option value="">Select</option>
															<option value="Fixed" <?php if('Fixed'==$buyer->type){ echo 'selected'; }?>>Fixed</option>
															<option value="Rent" <?php if('Rent'==$buyer->type){ echo 'selected'; }?>>Rent</option>
															<option value="Freelancer" <?php if('Freelancer'==$buyer->type){ echo 'selected'; }?>>Freelancer</option>
															<option value="Others" <?php if('Others'==$buyer->type){ echo 'selected'; }?>>Others</option>
														</select>
														<?php if($errors->has('type')): ?>
														<span class="text-danger"><?php echo e($errors->first('type')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">MSME Category  <span class="text-danger">*</span></label>
														<select class="form-control" name="msme_category" required>
															<option value="">Select</option>
															<option value="Micro" <?php if('Micro'==$buyer->msme_category){ echo 'selected'; }?>>Micro</option>
															<option value="Small" <?php if('Small'==$buyer->msme_category){ echo 'selected'; }?>>Small</option>
															<option value="Medium" <?php if('Medium'==$buyer->msme_category){ echo 'selected'; }?>>Medium</option>
														</select>
														<?php if($errors->has('msme_category')): ?>
														<span class="text-danger"><?php echo e($errors->first('msme_category')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-4 col-6">
													<div class="form-group">
														<label for="first-name-column">Other Documents</label>
														<input type="file" class="form-control" name="aggrement" value="<?php echo e(old('aggrement')); ?>">
														<?php if($errors->has('aggrement')): ?>
														<span class="text-danger"><?php echo e($errors->first('aggrement')); ?> </span>
														<?php endif; ?>
														
														<?php if($buyer->aggrement!=''){ ?>
															<a href="<?php echo e(asset('laravel/public/buyer/' . $buyer->aggrement)); ?>" download>Preview</a>
														<?php }else{ echo '-'; } ?>
													</div>
												</div>
												
												</div>
												<div id="bank-details-container">
												<?php $__currentLoopData = $bank_details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $bank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<div class="bank_details_group">
													<div class="bank_details">
													<h6>Bank Details - <?php echo e($index + 1); ?></h6>
													<input type="hidden" name="bank_details[<?php echo e($index); ?>][id]" value="<?php echo e($bank->id); ?>">
														<div class="row">
														<div class="col-md-6 col-12">
															<div class="form-group">
																<label for="first-name-column">Beneficiary's Name</label>
																<input type="text" class="form-control" name="bank_details[<?php echo e($index); ?>][beneficiary]" value="<?php echo e(old('beneficiary.' . $index, $bank->beneficiary)); ?>" required>																<?php if($errors->has('beneficiary')): ?>
																<span class="text-danger"><?php echo e($errors->first('beneficiary')); ?> </span>
																<?php endif; ?>
															</div>
														</div>												
														<div class="col-md-6 col-12">
															<div class="form-group">
																<label for="first-name-column">Bank Account No.</label>
																<input type="text" class="form-control" name="bank_details[<?php echo e($index); ?>][account]" value="<?php echo e(old('account.' . $index, $bank->account)); ?>" required>
																<?php if($errors->has('account')): ?>
																<span class="text-danger"><?php echo e($errors->first('account')); ?> </span>
																<?php endif; ?>
															</div>
														</div>	
														<div class="col-md-4 col-12">
															<div class="form-group">
																<label for="first-name-column">Bank Name</label>
																<input type="text" class="form-control"  name="bank_details[<?php echo e($index); ?>][bank_name]" value="<?php echo e(old('bank_name.' . $index,$bank->bank_name)); ?>" required>
																<?php if($errors->has('bank_name')): ?>
																<span class="text-danger"><?php echo e($errors->first('bank_name')); ?> </span>
																<?php endif; ?>
															</div>
														</div>
														<div class="col-md-4 col-12">
															<div class="form-group">
																<label for="first-name-column">RTGS/NEFT IFSC CODE</label>
																<input type="text" class="form-control"  name="bank_details[<?php echo e($index); ?>][ifsc]" value="<?php echo e(old('ifsc.' . $index,$bank->ifsc)); ?>" required>
																<?php if($errors->has('ifsc')): ?>
																<span class="text-danger"><?php echo e($errors->first('ifsc')); ?> </span>
																<?php endif; ?>
															</div>
														</div>
														<div class="col-md-4 col-12">
															<div class="form-group">
																<label for="first-name-column">Bank Status</label>
																<select class="form-control" name="bank_details[<?php echo e($index); ?>][bnk_status]">
																	<option value="">Select</option>
																	<option value="Active" class="text-success" <?php if('Active'==$bank->bnk_status){ echo 'selected'; }?>>Active</option>
																	<option value="Inactive" <?php if('Inactive'==$bank->bnk_status){ echo 'selected'; }?>>Inactive</option>
																</select>
																<?php if($errors->has('bnk_status')): ?>
																<span class="text-danger"><?php echo e($errors->first('bnk_status')); ?> </span>
																<?php endif; ?>
															</div>
														</div>
														<div class="col-md-12 col-12">
															<div class="form-group">
																<label for="first-name-column">Bank Address</label>
																<textarea class="form-control" name="bank_details[<?php echo e($index); ?>][bank_address]" required><?php echo e($bank->bank_address); ?></textarea>
																<?php if($errors->has('bank_address')): ?>
																<span class="text-danger"><?php echo e($errors->first('bank_address')); ?> </span>
																<?php endif; ?>
															</div>
														</div>
														<!-- <div class="text-right w-100 p-1 mb-2">
															<button type="button" class="btn btn-danger btn-sm removeBank">Remove</button>
														</div> -->
														</div>
													</div>
												</div>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</div>

												<!-- add-more button temp -->
												<template id="bank-template">
												<div class="bank_details_group">
													<div class="bank_details">
													<h6>Bank Details - <span class="bank-count"></span></h6>
														<div class="row">
														<div class="col-md-6 col-12">
															<div class="form-group">
																<label for="first-name-column">Beneficiary's Name</label>
																<input type="text" class="form-control" placeholder="Beneficiary's Name" name="bank_details[new][beneficiary]" required>																
																<?php if($errors->has('beneficiary')): ?>
																<span class="text-danger"><?php echo e($errors->first('beneficiary')); ?> </span>
																<?php endif; ?>
															</div>
														</div>												
														<div class="col-md-6 col-12">
															<div class="form-group">
																<label for="first-name-column">Bank Account No.</label>
																<input type="text" class="form-control" placeholder="Bank Account No." name="bank_details[new][account]" required>
																<?php if($errors->has('account')): ?>
																<span class="text-danger"><?php echo e($errors->first('account')); ?> </span>
																<?php endif; ?>
															</div>
														</div>	
														<div class="col-md-6 col-12">
															<div class="form-group">
																<label for="first-name-column">Bank Name</label>
																<input type="text" class="form-control" placeholder="Bank Name"  name="bank_details[new][bank_name]" required>
																<?php if($errors->has('bank_name')): ?>
																<span class="text-danger"><?php echo e($errors->first('bank_name')); ?> </span>
																<?php endif; ?>
															</div>
														</div>
														<div class="col-md-6 col-12">
															<div class="form-group">
																<label for="first-name-column">RTGS/NEFT IFSC CODE</label>
																<input type="text" class="form-control" placeholder="RTGS/NEFT IFSC CODE"  name="bank_details[new][ifsc]" required>
																<?php if($errors->has('ifsc')): ?>
																<span class="text-danger"><?php echo e($errors->first('ifsc')); ?> </span>
																<?php endif; ?>
															</div>
														</div>
														<div class="col-md-12 col-12">
															<div class="form-group">
																<label for="first-name-column">Bank Address</label>
																<textarea class="form-control" placeholder="Bank Address" name="bank_details[new][bank_address]" required></textarea>
																<?php if($errors->has('bank_address')): ?>
																<span class="text-danger"><?php echo e($errors->first('bank_address')); ?> </span>
																<?php endif; ?>
															</div>
														</div>
														<div class="text-right w-100 p-1 mb-2">
															<button type="button" class="btn btn-danger btn-sm removeBank">Remove</button>
														</div>
														</div>
													</div>
												</div>
												</template>
												<div class="text-right mb-2">
    												<button type="button" class="btn btn-success btn-sm" onclick="addMoreBank()">+ Add More</button>
												</div>
												<div class="col-12">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Update</button>
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
			<!-- // Basic Floating Label Form section end -->
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="<?php echo e(asset('laravel/public/admin/js/jquery.validate.min.js')); ?>"></script>

<script>
	function getMuo(val){
		$('.msme').hide(); 
		$('.uam').hide(); 
		$('.other').hide(); 
		
		if(val==1){
			$('.msme').show();
			$('.uam').show();
		}else if(val==2){
			$('.other').show();
		}
	}
</script>
<script>
	function updateBankCounts() {
		document.querySelectorAll('.bank_details h6').forEach((el, index) => {
			el.innerText = 'Bank Details - ' + (index + 1);
		});
	}

	function addMoreBank() {
		const template = document.getElementById('bank-template').content.cloneNode(true);
		document.getElementById('bank-details-container').appendChild(template);
		updateBankCounts();
	}

	document.addEventListener('click', function (e) {
		if (e.target && e.target.classList.contains('removeBank')) {
			e.target.closest('.bank_details_group').remove();
			updateBankCounts();
		}
	});

	$(document).ready(function() {
	  $("#buyerForm").validate({
	    rules: {
	      contact_no: {
		    required: true,
		    digits: true,
		    minlength: 10,
		    maxlength: 10
		  },
	      pan_no: {
	        required: true,
	        minlength: 10,
	        maxlength: 10,
	        pattern: /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/  // PAN format
	      },
	      gst_no: {
	        required: true,
	        minlength: 15,
	        maxlength: 15,
	        pattern: /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/ // GSTIN format
	      },
	      "ifsc[]": {
	        required: true,
	        minlength: 11,
	        maxlength: 11,
	        pattern: /^[A-Z]{4}0[A-Z0-9]{6}$/  // IFSC format
	      }
	    },
	    messages: {
	    	contact_no: {
			    required: "Please enter contact number",
			    digits: "Only digits are allowed",
			    minlength: "Contact number must be exactly 10 digits",
			    maxlength: "Contact number must be exactly 10 digits"
			},
		    pan_no: {
		        required: "Please enter PAN number",
		        minlength: "PAN must be 10 characters",
		        maxlength: "PAN must be 10 characters",
		        pattern: "Enter a valid PAN (ABCDE1234F)"
		    },
	      gst_no: {
	        required: "Please enter GST number",
	        minlength: "GST must be 15 characters",
	        maxlength: "GST must be 15 characters",
	        pattern: "Enter a valid GST (15 digits alphanumeric)"
	      },
	      "ifsc[]": {
	        required: "Please enter IFSC code",
	        minlength: "IFSC must be 11 characters",
	        maxlength: "IFSC must be 11 characters",
	        pattern: "Enter a valid IFSC (e.g. SBIN0001234)"
	      }
	    },
	    errorElement: "span",
	    errorPlacement: function(error, element) {
	      error.css("color", "red");
	      error.insertAfter(element);
	    }
	  });
	});
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/buyer/edit.blade.php ENDPATH**/ ?>