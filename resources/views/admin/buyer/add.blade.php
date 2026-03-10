@extends('layouts.admin')
@section('content')

@if (Auth::viaRemember())
    {{666}}
@else
    {{777}}
@endif
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Add Buyer</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Add Buyer</a>
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
									<form class="form" id="buyerForm" action="{{ route('admin.buyer.store') }}" method="post" enctype="multipart/form-data">
										@csrf
										<div class="form-body">
											<div class="row">
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Name  <span class="text-danger">*</span></label>
														<input type="text" class="form-control" placeholder="Name" name="name" value="{{ old('name') }}" required>
														@if($errors->has('name'))
														<span class="text-danger">{{ $errors->first('name') }} </span>
														@endif
													</div>
												</div>		

												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Contact No  <span class="text-danger">*</span></label>
														<input type="text" class="form-control" placeholder="Contact No" name="contact_no" value="{{ old('contact_no') }}" required>
														@if($errors->has('contact_no'))
														<span class="text-danger">{{ $errors->first('contact_no') }} </span>
														@endif
													</div>
												</div>	
												
													
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Email <span class="text-danger">*</span></label>
														<input type="email" class="form-control" placeholder="Email" name="email" value="{{ old('email') }}" required>
														@if($errors->has('email'))
														<span class="text-danger">{{ $errors->first('email') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">IS GST</label>
														<select class="form-control" name=""onchange="getgst(this.value)">
															<option value="">Select</option>
															<option value="1" selected>Yes</option>
															<option value="2">No</option>
														</select>
														@if($errors->has('email'))
														<span class="text-danger">{{ $errors->first('email') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">GST No <span class="text-danger">*</span></label>
														<input type="text" class="form-control" id="gst_no" placeholder="GST No" name="gst_no" value="{{ old('gst_no') }}" required>
														<small id="gst_error" class="text-danger d-none">
															GST No must be 15 alphanumeric characters.
														</small>
														@if($errors->has('gst_no'))
														<span class="text-danger">{{ $errors->first('gst_no') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Attach GST Certificate</label>
														<input type="file" class="form-control" name="gst_img" value="{{ old('gst_img') }}">
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Pan No <span class="text-danger">*</span></label>
														<input type="text" class="form-control pan_no" placeholder="Pan No" name="pan_no" value="{{ old('pan_no') }}" required>
														
														@if($errors->has('pan_no'))
														<span class="text-danger">{{ $errors->first('pan_no') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Attach Pan Certificate</label>
														<input type="file" class="form-control" name="pan_img" value="{{ old('pan_img') }}">
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
														@if($errors->has('email'))
														<span class="text-danger">{{ $errors->first('email') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-4 col-12 msme" style="display:none;">
													<div class="form-group">
														<label for="first-name-column">Attach Certificate</label>
														<input type="file" class="form-control" name="msme_uam_file" value="{{ old('email') }}">
													</div>
												</div>
												<div class="col-md-4 col-12 msme" style="display:none;">
													<div class="form-group">
														<label for="first-name-column">MSME No / UAM No</label>
														<input type="text" class="form-control" placeholder="UAM No" name="msme_uam_no" value="{{ old('email') }}">
													</div>
												</div>	
												<div class="col-md-6 col-12 other" style="display:none;">
													<div class="form-group">
														<label for="first-name-column">Attach only MSME Certificate or Declaration Form</label>
														<input type="file" class="form-control" name="declaration_form" value="{{ old('email') }}">
													</div>
												</div>
												<div class="col-md-12 col-12">
													<div class="form-group">
														<label for="first-name-column">Address <span class="text-danger">*</span></label>
														<textarea class="form-control" placeholder="Address" name="address" required>{{ old('address') }}</textarea>													
														@if($errors->has('address'))
														<span class="text-danger">{{ $errors->first('address') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Pincode <span class="text-danger">*</span></label>
														<input type="number" class="form-control" placeholder="Pincode" name="pincode" value="{{ old('pincode') }}" required>
														@if($errors->has('pincode'))
														<span class="text-danger">{{ $errors->first('pincode') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Credit Period Days <span class="text-danger">*</span></label>
														<input type="number" class="form-control" placeholder="Credit Period Days" name="credit_day" value="{{ old('credit_day') }}" required>
														@if($errors->has('credit_day'))
														<span class="text-danger">{{ $errors->first('credit_day') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-4 col-6">
													<div class="form-group">
														<label for="first-name-column">Vendor Registration Form with KYC</label>
														<input type="file" class="form-control" name="bank_proof" value="{{ old('account') }}" required>
														@if($errors->has('account'))
														<span class="text-danger">{{ $errors->first('account') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-4 col-6">
													<div class="form-group">
														<label for="first-name-column">Bank Documents <span class="text-danger">*</span></label>
														<input type="file" class="form-control" name="bank_proof_2" value="{{ old('bank_proof_2') }}" required>
														@if($errors->has('bank_proof_2'))
														<span class="text-danger">{{ $errors->first('bank_proof_2') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Vendor Type  <span class="text-danger">*</span></label>
														<select class="form-control" name="type" required>
															<option value="">Select</option>
															<option value="Fixed" selected>Fixed</option>
															<option value="Rent">Rent</option>
															<option value="Freelancer">Freelance</option>
															<option value="Others">Others</option>
														</select>
														@if($errors->has('type'))
														<span class="text-danger">{{ $errors->first('type') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">MSME Category  <span class="text-danger">*</span></label>
														<select class="form-control" name="msme_category" required>
															<option value="">Select</option>
															<option value="Micro">Micro</option>
															<option value="Small">Small</option>
															<option value="Medium">Medium</option>
														</select>
														@if($errors->has('msme_category'))
														<span class="text-danger">{{ $errors->first('msme_category') }} </span>
														@endif
													</div>
												</div>
												
												<div class="col-md-4 col-6">
													<div class="form-group">
														<label for="first-name-column">Other Documents</label>
														<input type="file" class="form-control" name="aggrement" value="{{ old('aggrement') }}">
														@if($errors->has('aggrement'))
														<span class="text-danger">{{ $errors->first('aggrement') }} </span>
														@endif
													</div>
												</div>
												</div>
												<div class="bank_details_group">
													<div class="bank_details">
														<h6>Bank Details</h6>
														<div class="row">
														<div class="col-md-6 col-12">
															<div class="form-group">
																<label for="first-name-column">Beneficiary's Name <span class="text-danger">*</span></label>
																<input type="text" class="form-control" placeholder="Beneficiary's Name" name="beneficiary[]" value="{{ old('beneficiary') }}" required>
																@if($errors->has('beneficiary'))
																<span class="text-danger">{{ $errors->first('beneficiary') }} </span>
																@endif
															</div>
														</div>												
														<div class="col-md-6 col-12">
															<div class="form-group">
																<label for="first-name-column">Bank Account No. <span class="text-danger">*</span></label>
																<input type="text" class="form-control" placeholder="Bank Account No." name="account[]" value="{{ old('account') }}" required>
																@if($errors->has('account'))
																<span class="text-danger">{{ $errors->first('account') }} </span>
																@endif
															</div>
														</div>	
														<div class="col-md-6 col-12">
															<div class="form-group">
																<label for="first-name-column">Bank Name <span class="text-danger">*</span></label>
																<input type="text" class="form-control" placeholder="Bank Name" name="bank_name[]" value="{{ old('bank_name') }}" required>
																@if($errors->has('bank_name'))
																<span class="text-danger">{{ $errors->first('bank_name') }} </span>
																@endif
															</div>
														</div>
														<div class="col-md-6 col-12">
															<div class="form-group">
																<label for="first-name-column">RTGS/NEFT IFSC CODE <span class="text-danger">*</span></label>
																<input type="text" class="form-control" placeholder="RTGS/NEFT IFSC CODE" name="ifsc[]" value="{{ old('ifsc') }}" required>
																@if($errors->has('ifsc'))
																<span class="text-danger">{{ $errors->first('ifsc') }} </span>
																@endif
															</div>
														</div>
														<div class="col-md-12 col-12">
															<div class="form-group">
																<label for="first-name-column">Bank Address <span class="text-danger">*</span></label>
																<textarea class="form-control" placeholder="Bank Address" name="bank_address[]" required></textarea>
																@if($errors->has('bank_address'))
																<span class="text-danger">{{ $errors->first('bank_address') }} </span>
																@endif
															</div>
														</div>
														</div>
													</div>
												</div>
												<div class="text-right">
													<button type="button" class="btn btn-success btn-sm mb-2" onclick="addMoreBank()">+ Add More</button>
												</div>
												<div class="col-12">
													<button type="submit" id="submitBtn" class="btn btn-primary mr-1 mb-1">Submit</button>
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
@endsection

@section('scripts')
<script src="{{asset('laravel/public/admin/js/jquery.validate.min.js')}}"></script>
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

<script>
function addMoreBank() {
    let html = `
    <div class="bank_details_section">
        <hr>
        <button type="button" class="btn btn-danger btn-sm mb-2 float-right" onclick="removeBank(this)">Remove</button>
        <h6>Bank Details </h6>
        <div class="row">
            <div class="col-md-6 col-12">
                <div class="form-group">
                    <label>Beneficiary's Name</label>
                    <input type="text" class="form-control" name="beneficiary[]" required>
                </div>
            </div>
            <div class="col-md-6 col-12">
                <div class="form-group">
                    <label>Bank Account No.</label>
                    <input type="text" class="form-control" name="account[]" required>
                </div>
            </div>
            <div class="col-md-6 col-12">
                <div class="form-group">
                    <label>Bank Name</label>
                    <input type="text" class="form-control" name="bank_name[]" required>
                </div>
            </div>
            <div class="col-md-6 col-12">
                <div class="form-group">
                    <label>RTGS/NEFT IFSC CODE</label>
                    <input type="text" class="form-control" name="ifsc[]" required>
                </div>
            </div>
            <div class="col-md-12 col-12">
                <div class="form-group">
                    <label>Bank Address</label>
                    <textarea class="form-control" name="bank_address[]" required></textarea>
                </div>
            </div>
        </div>
    </div>`;
    document.querySelector('.bank_details_group').insertAdjacentHTML('beforeend', html);
}

function removeBank(btn) {
    btn.closest('.bank_details_section').remove();
}
</script>
<script>
function getgst(val) {
    let gstInput = document.getElementById("gst_no");
	let submitBtn = document.getElementById("submitBtn");
	let gstError = document.getElementById("gst_error");
    if (val == 2) {
        gstInput.setAttribute("readonly", true);
        gstInput.removeAttribute("required"); // not required if No
        gstInput.value = "NA"; // clear value
		submitBtn.removeAttribute("disabled");
		gstError.classList.add("d-none");
    } else {
        gstInput.removeAttribute("readonly");
        gstInput.setAttribute("required", true);
		gstInput.value = ""; // clear value
		submitBtn.setAttribute("disabled", true);
		gstError.classList.remove("d-none");
    }
}


let gstInput = document.getElementById("gst_no");
let submitBtn = document.getElementById("submitBtn");
let gstError = document.getElementById("gst_error");

function validateGST() {
    let val = gstInput.value.trim();
    let regex = /^[A-Za-z0-9]{15}$/;

    if (val === "NA") {
        gstError.classList.add("d-none");
        submitBtn.removeAttribute("disabled");
    } else if (regex.test(val)) {
        gstError.classList.add("d-none");
        submitBtn.removeAttribute("disabled");
    } else {
        gstError.classList.remove("d-none");
        submitBtn.setAttribute("disabled", true);
    }
}

gstInput.addEventListener("input", validateGST);
validateGST();
</script>
@endsection
