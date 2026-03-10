
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-6">
						<h2 class="content-header-title float-left mb-0">Payment Links Create</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">Add</li>
							</ol>
						</div>
					</div>
					<div class="col-6 text-right">
						<a href="<?php echo e(route('admin.payment.index')); ?>" class="btn btn-primary">Back</a>
					</div>
				</div>
			</div>
		</div>

		<div class="content-body">
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="<?php echo e(route('admin.payment.create')); ?>" method="get" id="npf_form">
									<div class="row">
										<div class="col-lg-6 col-12 form-group">
											<label for="name" class="fw-medium">Type</label>
											<div class="pt-1">
												<input type="radio" name="type" class="type get_details" value="1" checked id="r1"> <label for="r1">NPF ID</label> &nbsp;&nbsp;&nbsp;
												<input type="radio"  name="type" class="type get_details" value="2" id="r2"> <label for="r2">Mobile Number</label>
											</div>
											<div class="error_msg text-danger fw-bold"></div>
										</div>
										<div class="col-lg-6 col-12 form-group">
											<label for="name" class="fw-medium type_head">Enter NPF ID / Mobile Number</label>
											<input type="number"  name="user_put" class="user_put get_details form-control">
											<input type="hidden"  name="npf_id" class="npf_id form-control">
											<div class="error_msg text-danger fw-bold"></div>
										</div>

										<input type="hidden" name="usersDetails" class="usersDetails">

										<div class="col-lg-4 col-12 form-group">
											<label for="phone" class="fw-medium">Student Mobile Number</label>
											<input type="number" name="mobile" class="mobile form-control" value="" readonly>
											<div class="error_msg text-danger fw-bold"></div>
										</div>

										<div class="col-lg-4 col-12 form-group">
											<label for="phone" class="fw-medium">Student Email</label>
											<input type="email" placeholder="Enter Email" name="email" value=""	class="email form-control" readonly>
											<div class="error_msg text-danger fw-bold"></div>
										</div>
										<div class="col-lg-4 col-12 form-group">
											<label for="location" class="fw-medium">Course ID</label>
											<input type="number" placeholder="Course ID" name="course_id" class="course_id form-control">
											<div class="error_msg text-danger fw-bold"></div>
										</div>

										<div class="col-lg-12 col-12 form-group">
											<label for="description" class="fw-medium">Remark</label>
											<textarea placeholder="Enter Description..." name="description" class="cremark form-control"></textarea>
											<div class="error_msg text-danger fw-bold"></div>
										</div>
										
										<div class="table-responsive course_table" style="display:none;">
											<table class="table table-vcenter card-table" id="TableSearch">
												<thead>
													<tr>
														<th>Course Name</th>
														<th>Prime Validity</th>
														<th>Standard Validity</th>
														<th style="width: 110px;">Prime</th>
														<th style="width: 110px;">Standard</th>
													</tr>
												</thead>
												<tbody>
													<tr class="ng-scope">
														<td class="course_name"></td>
														<td class="p_validity"></td>
														<td class="s_validity"></td>
														<td class="p_amount"></td>
														<td class="s_amount"></td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>          
			</section>
		</div>
	</div>
</div>
 

 <!--Pricing---start-->
<div class="pricing-modal modal modal-blur fade" id="modal-report" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="post" class="card" id="verify_contact">
				<input type="hidden" class="is_fbt_restricted" name="is_fbt_restricted"/>
				<input type="hidden" class="email"        name="email" value="">
				<input type="hidden" class="mobile"       name="mobile" value="">
				<input type="hidden" class="course_id"    name="course_id" value="">
				<input type="hidden" class="user_id"      name="user_id" value="">
				<input type="hidden" class="npf_id"       name="npf_id" value="">
				<input type="hidden" class="usersDetails" name="users_details" value="">
				<input type="hidden" class="remark" name="remark" value="">

				<div class="price-with-btn">
					<div class="cr-name">
						<b><span class="course_name_set" style="color:#206bc4;"></span></b>
						<em>खरीदने के लिए विधार्थी के Contact Number की पुष्टि करें।</em>
                    </div>

                    <a class="rice-btn prime_price_show" href="javascript:void(0);">
						<span>Price: <i>₹ <span class="cut_prime_price">10,000</span></i></span>
						<b>₹<span class="gst_prime_price">1000</span> <em>(GST Included)</em></b>
                    </a>

					<a class="rice-btn standard_price_show" href="javascript:void(0);">
						<span>Price: <i>₹ <span class="cut_standard_price">10,000</span></i></span>
						<b>₹<span class="gst_standard_price">1000</span> <em>(GST Included)</em></b>
                    </a>
                </div>

				<div class="modal-body">
					<div class="row mx-0" style="max-height: 300px;overflow: auto;">
						<div class="pricing-section">
                            <div class="thumb_img_set"></div>
							<div class="description_set"></div>
							<div class="plan-detail">                             
								<div class="plan-check">
									<b>Choose a Plan</b>
									<div class="plan-rad"> 
										<ul class="nav nav-tabs">
											<li class="prime_choose_plan">
												<a class="prime_offering" href="javascript:void(0);">
													<span class="prime">
														<input type="radio" class="prime_radio" name="plan_type" value="1">
														<em>Prime </em>
													</span>
												</a>
											</li>
											<li class="standard_choose_plan">
												<a class="standard_offering" href="javascript:void(0);">
													<span>
														<input type="radio" class="standard_radio" name="plan_type" value="0">
														<em>Standard</em>
													</span>
												</a>
											</li>    
										</ul>
									</div>
									<div class="tab-content">
										<div id="prime_offering" class="tab-pane fade in active">
											<div class="plan-list">
												<ul class="prime_offering_content">
													
												</ul>
											</div>
										</div>									
										<div id="standard_offering" class="tab-pane fade">
											<div class="plan-list second-tab">
												<ul class="standard_offering_content">
												
												</ul>
											 </div>
										</div>
									</div>
								</div>                             
							</div>                        
						</div>
                        <p class="notext note_for_prime">
                            <b>Note : </b>Classroom notes included with prime course for delivery notes enter address on below field.
                        </p>
					</div>

					<div class="my-2 row">
						<div class="col-6">
							<label class="form-label">Coupon Code</label>
							<input type="text" class="form-control coupon_code" name="coupon_code">
						</div>
						<div class="col-2">
							<label class="form-label"></label>
							<button type="button" class="mt-2 btn btn-sm btn-warning" id="coupon_apply">Apply</button>
						</div>
						<div class="col-12 my-2">
							<input type="hidden" name="coupon_id" class="coupon_id" value="0">
							<input type="hidden" name="discount"  class="discount" value="0">
							<p class="coupon_discount text-danger"></p>
						</div>
					</div>
					  
					<div class="my-2">
						<label class="form-label">विधार्थी का उत्कर्ष एप पर पंजीकृत मोबाईल नंबर दर्ज करें -</label>
						<input type="text" class="form-control mobile" name="contact_no" placeholder="Student Contact Number" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required readonly>
					</div>
					
					<div class="my-2 variant_area">
						<label class="form-label">Select Your Printed Book's Variant From Here-</label>
						<select class="form-control product_id" name="product_id" style="width:100%;"> 
							<option value="">--Select--</option>
						</select>
					</div>
					
					<div class="row show_for_prime_restricted">
						<div class="col-md-12 mb-1"><label class="form-label"><b>Personal Details</b></label></div>
						<div class="col-md-4 mb-1">
							<label class="form-label">Name<span class="text-danger">*</span></label>
							<input type="text" class="form-control" name="address[name]" placeholder="Student Name">
						</div>
						<div class="col-md-4 mb-1">
							<label class="form-label">Pin Code<span class="text-danger">*</span></label>
							<input type="number" class="form-control" name="address[pincode]" placeholder="Pin Code" min="100000" max="999999" oninput="this.value = this.value.slice(0, 6)">
						</div>
						<div class="col-md-4 mb-1">
							<label class="form-label">State<span class="text-danger">*</span></label>
							<input type="text" class="form-control" name="address[state]" placeholder="State">
						</div>
						<div class="col-md-4 mb-1">
							<label class="form-label">District<span class="text-danger">*</span></label>
							<input type="text" class="form-control" name="address[district]" placeholder="District">
						</div>
						<div class="col-md-4 mb-1">
							<label class="form-label">City<span class="text-danger">*</span></label>
							<input type="text" class="form-control" name="address[city]" placeholder="City">
						</div>
						<div class="col-md-4 mb-1">
							<label class="form-label">House No, Bulding Name<span class="text-danger">*</span></label>
							<input type="text" class="form-control" name="address[houseNo]" placeholder="House No, Bulding Name">
						</div>
						<div class="col-md-4 mb-1">
							<label class="form-label">Road Name, Area, Colony<span class="text-danger">*</span></label>
							<input type="text" class="form-control" name="address[roadName]" placeholder="Road Name, Area, Colony">
						</div>
						<div class="col-md-4 mb-1">
							<label class="form-label">Landmark<span class="text-danger">*</span></label>
							<input type="text" class="form-control" name="address[landMark]" placeholder="Landmark">
						</div>
					</div>
				</div>

				<div class="modal-footer">
					<p class="text-danger txt"></p>
					<a href="#" class="btn btn-link link-secondary hide" data-dismiss="modal">Cancel</a>
					<button type="submit" id="course_submit" class="btn btn-primary hide">Send Link</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!--GetInvoice--end-->
<?php $__env->stopSection(); ?>

<style>
            
.pricing-modal .modal-dialog.modal-lg.modal-dialog-centered {
    max-width: 100%;
    width: 800px;
}

.price-with-btn {
    border-bottom: solid 1px #ddd;
    padding: 10px 15px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.cr-name {
    display: flex;
    flex-direction: column;
}

.cr-name b {
    color: #206BC4;
    font-size: 17px;
    line-height: normal;
}

.cr-name em {
    font-style: normal;
    font-weight: bold;
    font-size: 14px;
    line-height: 30px;
    display: block;
}

.price-with-btn a.rice-btn {
    display: flex;
    box-shadow: 0px 2px 8px #00000029;
    border: 1px dashed #FFE334;
    border-radius: 11px;
    flex-direction: column;
    background-color: #01B1FF;
    padding: 5px 20px;
    color: #dfff;
    line-height: 20px;
    white-space: nowrap;
}

.price-with-btn a.rice-btn span {
    font-size: 13px;
}

.price-with-btn a.rice-btn span i {
    font-style: normal;
    text-decoration: line-through;
}

a.rice-btn b em {
    font-style: normal;
    font-size: 13px;
    font-weight: 400;
}

a.rice-btn b {
    font-size: 16px;
}

.pricing-modal .modal-content { 
    border-radius: 18px; 
}

.pricing-modal .card {
    box-shadow: none;
    border: 1px solid rgba(101,109,119,.16);
    background-color: transparent;
    border: 0;
}

.pricing-section {
    display: flex;
    flex-direction: row;
    gap: 10px;
    width: 100%;
}

.plan-check b {
    color: #FF0000;
    margin: 0 0 5px 0;
    display: block;
    font-size: 15px;
}

.plan-check {
    width: 100%;
}

.pricing-section .thumb_img_set img {
    border-radius: 15px;
    height: auto !important;
}

.pricing-section .thumb_img_set {
    width: 250px;
}

.plan-rad span {
    position: relative;
    display: flex;
}

.plan-rad span input {
    position: absolute;
    bottom: 0;
    left: 0;
    opacity: 0;
    right: 0;
    top: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
    z-index: 9;
}

.plan-rad ul {
    gap: 15px;
    display: flex;
    flex-direction: row;
    border: 0;
}

.plan-rad span em {
    padding: 8px 30px;
    background: #F1F9FF 0% 0% no-repeat padding-box;
    border: 0.5px solid #3DACFF;
    gap: 10px;
    border-radius: 5px;
    font-style: normal;
    font-size: 16px;
    display: flex;
    align-items: center;
}
            
.plan-rad span.prime em {
    background: transparent linear-gradient(101deg, #FFDF13 0%, #FCF8DE 23%, #F5DC38 52%, #FFFBDE 78%, #FFDE0C 100%) 0% 0% no-repeat padding-box;
    border-color: #FFE334;
    color: #000 !important;
}

.plan-rad span em:before {
    content: "";
    position: absolute;
    bottom: -8px;
    left: 0;
    width: 15px;
    height: 15px;
    background-color: #fff;
    border-radius: 50%;
    margin: auto;
    right: 0;
    border: solid 1px #ddd;
    z-index: 9;
	pointer-events:none;
}

.plan-rad span em:after {
    content: "";
    position: absolute;
    bottom: -10px;
    left: 0;
    width: 20px;
    height: 20px;
    background-color: #fff;
    border-radius: 50%;
    margin: auto;
    right: 0;
	pointer-events:none;
}

.plan-rad span input:checked ~ em:before {
    /*background-image: url("images/check.svg");*/
    background-size: contain;
    background-repeat: no-repeat;
    background-color: #0da772;
}

.plan-rad span input:checked ~ em{background-color: #3DACFF;color:#fff;}

.plan-detail {
    width: 100%;
}

.plan-list li {
    list-style: none;
    padding: 0;
}

.plan-list ul {
    padding: 0;
    display: flex;
    flex-wrap: wrap;
    background: transparent linear-gradient(113deg, #FEF0B4 0%, #FCF5DA 63%, #FFFFFF 100%) 0% 0% no-repeat padding-box;
    border: 1px solid #F7E25E;
    padding: 15px 15px;
    border-radius: 10px;
    margin: 20px 0 0 0;
}

.plan-list ul li {
    width: 50%;
}

.plan-list ul li {
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
}

.plan-list ul li img {
    width: 15px;
    margin: 0 7px 0 0;
}

.tab-content>.active {
    display: block;
    opacity: 1;
}

a:hover { 
    text-decoration: none;
}

.plan-list.second-tab ul {
    background: #F1F9FF 0% 0% no-repeat padding-box;
    border: 0.20000000298023224px solid #3DACFF;
}

.enter-mob {
    display: flex;
    gap: 15px;
}

.enter-mob input {
    border-color: #707070;
    padding: 15px 15px;
    font-size: 18px;
    border-radius: 10px;
}

.enter-mob button {
    background-color: red;
    border: 0;
    background: #206BC4 0% 0% no-repeat padding-box;
    border-radius: 8px;
    color: #fff;
    padding: 0 40px;
    font-size: 18px;
}

p.notext {
    display: flex;
    flex-direction: column;
    font-size: 15px;
}

p.notext b {
    color: red;
}

.np-form {
    border-top: solid 1px #ddd;
    padding: 15px 20px 0 20px;
}

.pricing-modal .modal-body {
    padding-bottom: 0;
}

.price-btn span {
    border: solid 1px #ddd;
    border-radius: 5px;
    padding: 5px 10px;
    font-size: 13px;
    vertical-align: middle;
    color: #3DACFF;
    background-color: #fff;
    font-weight: 600;
    cursor: pointer;
}

.price-btn.prime span {
    color: #FF8000;
}

.price-btn span em {
    border-left: solid 1px #ddd;
    padding: 0 0 0 5px;
}

.price-btn span em img {
    width: 7px;
}

.blink {
	animation: blinkAnimation 1s steps(2, start) infinite;
	color: red; /* Adjust color as needed */
}

@keyframes  blinkAnimation {
	to {
		visibility: hidden;
	}
}
            
</style>

<?php $__env->startSection('scripts'); ?>
<script src="<?php echo e(asset('laravel/public/admin/js/jquery.validate.min.js')); ?>"></script>
<script type="text/javascript">		
	$(document).on('change', '.get_details', function () {
		const npfid = $('.user_put').val();
		const type = $('.type:checked').val();
		
		if(type==2){
			$('.type_head').val('Enter Mobile Number');
		}else{
			$('.type_head').val('Enter NPF ID');
		}
		
		$.ajax({
			type : 'POST',
			url : '<?php echo e(route('admin.payment.npf-student-details')); ?>',
			data : {'_token' : '<?php echo e(csrf_token()); ?>', 'npfid': npfid,type:type},			
			success: function (response) {
				var record = JSON.parse(response);
				if(record.status=='OK'){
					$('.mobile').val(record['usersDetails'][0]['mobile']);
					$('.email').val(record['usersDetails'][0]['email']);
					$('.user_id').val(record['usersDetails'][0]['id']);
					$('.npf_id').val(record['usersDetails'][0]['meritto_id']);
					// $('.npf_id').val(npfid);
					$('.usersDetails').val(JSON.stringify(record['usersDetails'][0]));
				}else{
					alert('User Not found');
					$('.mobile').val('');
					$('.email').val('');
					$('.npf_id').val('');
					$('.user_put').val('');
				}
			}
		});
	});
	
	
	$(document).on('change', '.course_id', function () {
		const course_id = $(this).val();
		$.ajax({ 
			type : 'POST',
			url : '<?php echo e(route('admin.payment.npf-course-details')); ?>',
			data : {'_token' : '<?php echo e(csrf_token()); ?>', 'course_id': course_id},			
			success: function (response) {
							
				var rsp = JSON.parse(response);
				if(rsp.Status=="OK"){
					
					var record=rsp.data;
					
					if(record.id == 19725){
						alert('Not Allowed!!');
						return;
					}else if(record.title==undefined  || record.publish!=1 || record.course_sp<=0){
						alert('Invalid, unpublished, or free course ID');
						return;
					}
					
					$('.course_table').show();	
					$('.course_name').text(record.title);
					$('.p_validity').text(record.pro_validity);
					$('.s_validity').text(record.validity);
					
					var p_btn = (record.is_pro==1 && record.pro_course_sp>0)?`<button type="button" class="letproceed" data-course-id="${record.id}" data-course-amount="${record.pro_course_sp}" data-plan_type="prime">${record.pro_course_sp}</button>`:'';
					$('.p_amount').html(p_btn);
					
					var s_btn=record.course_sp>0?`<button type="button" class="letproceed" data-course-id="${record.id}" data-course-amount="${record.course_sp}" data-plan_type="standard">${record.course_sp}</button>`:'';
					$('.s_amount').html(s_btn);
				}else{
					alert(rsp.message);
					$('.course_table').hide();
				}
			}
		});
	});

	var is_fbt_restricted = 0;
	$(document).on('click', '.letproceed', function () {
		$('.pricing-modal').modal({backdrop: 'static',keyboard: true,show: true});
		var _this = $(this);
		$('.txt').text('');
		$('.hide').show();
		$('#verify_contact').show();
		var course_type = _this.attr('data-plan_type');
		var course_id     = $(this).attr("data-course-id");
		
		var remark = $('.cremark').val();
		$(".coupon_id").val(0);
        $(".discount").val(0);
		$("#verify_contact").find(".course_id").val(course_id);
		$.ajax({
			type : 'POST',
			url : '<?php echo e(route('admin.payment.npf-course-details')); ?>',
			data : {'_token' : '<?php echo e(csrf_token()); ?>', 'course_id': course_id},
			success : function(response){
				var record = JSON.parse(response);
				if(record.Status=="OK"){
					var description = record.data.description_data_2;
					is_fbt_restricted = record.data.is_fbt_restricted;
					$(".is_fbt_restricted").val(is_fbt_restricted);
					var cut_standard_price = record.data.mrp;
					var cut_prime_price =  record.data.pro_mrp;
					var prime_price = record.data.pro_course_sp;
					var standard_price =  record.data.course_sp;
					
					var hide_choose_plan = "";
					
					if(prime_price==0){
						$(".prime_choose_plan").css("display","none");
					}else{
						$(".prime_choose_plan").css("display","");
					}

					if(standard_price==0){
						$(".standard_choose_plan").css("display","none");
					}else{
						$(".standard_choose_plan").css("display","");
					}

					$(".cut_prime_price").text(cut_prime_price);
					$(".gst_prime_price").text(prime_price);
					$(".cut_standard_price").text(cut_standard_price);
					$(".gst_standard_price").text(standard_price);

					$(".prime_offering_content").html('');
					$(".standard_offering_content").html('');

					var originalUrl = record.data.cover_image;
				    var updatedUrl = originalUrl.replace(/https:\/\/.*?\/admin_v1/, "https://apps-s3-prod.utkarshapp.com/admin_v1");
					$(".thumb_img_set").html(`<img width="100%" src="`+updatedUrl+`">`);

					var course_id     = record.data.id;
					var course_name   = record.data.title;
					var course_amount = _this.attr("data-course-amount");
					if(course_id){
						$(".course_id").val(course_id);
						$(".course_name").val(course_name);
						$(".course_amount").val(course_amount);
						$(".course_amount_set").text(course_amount);
						$(".course_name_set").text(course_name);
						$(".remark").val(remark);
						hide_show_type(course_type,is_fbt_restricted);
					}
	
					$(".product_id").empty();
					$.each(record.data.fbt_restricted,function(index,item){
						const option = document.createElement('option');
						option.value = item.product_id; 
						option.textContent = item.title; 
						$(".product_id").append(option);
					});
				}
			}
		});		
	});
	
</script>


<script>
	function hide_show_type(course_type,is_restricted){
		if(course_type=="prime"){
			$("#standard_offering").removeClass("active");
			$("#prime_offering").addClass("active");
			$(".prime_price_show").css("display","");
			$(".standard_price_show").css("display","none");
			$(".prime_choose_plan").addClass("active");
			$(".standard_choose_plan").removeClass("active");
			$(".prime_radio").prop("checked", true);
			$(".note_for_prime").css("display","");
			$(".prime_choose_plan").css("display","");
			var gst_prime_price = $(".gst_prime_price").text();
			$(".course_amount").val(gst_prime_price);
			$(".course_amount_set").text(gst_prime_price);
		}else{
			$("#standard_offering").addClass("active");
			$("#prime_offering").removeClass("active");
			$(".prime_price_show").css("display","none");
			$(".standard_price_show").css("display","");
			$(".prime_choose_plan").removeClass("active");
			$(".standard_choose_plan").addClass("active");
			$(".standard_radio").prop("checked", true);
			$(".note_for_prime").css("display","none");
			$(".standard_choose_plan").css("display","");
			var gst_standard_price = $(".gst_standard_price").text();
			$(".course_amount").val(gst_standard_price);
			$(".course_amount_set").text(gst_standard_price);
		}
		
		if(course_type=="prime" && is_restricted==1){
			$(".show_for_prime_restricted").css("display","");
			$(".variant_area").css("display","");
			 $('input').prop('required', true);
			 $('.coupon_code').prop('required', false);
		}
		else{
			$(".show_for_prime_restricted").css("display","none");
			$(".variant_area").css("display","none");
			$('input').prop('required', false);
		}
		
	}
	
	$(".prime_offering").click(function(){
		hide_show_type("prime",is_fbt_restricted);
	});

	$(".standard_offering").click(function(){
		hide_show_type("standard",is_fbt_restricted);
	});

	$("#verify_contact").submit(function(e) {
	    e.preventDefault(); 
		const userResponse = prompt("Type 'YES' to confirm sending the request:");
		if(userResponse.toUpperCase()!= 'YES') {
			return;
		}

		$('.hide').hide();
		$('.txt').text('Please Wait..');
		var form = document.getElementById('verify_contact');
		var dataForm = new FormData(form);
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},      
			type: "POST",
			url : '<?php echo e(route('admin.payment.npf-sendpaymentlink')); ?>',
			data : dataForm,
			processData : false, 
			contentType : false,
			dataType : 'json',
			success : function(data){

				swal({
					title: data.status?"Success":"Error!",
					text: data.message,
					html: true,
					type: data.status?"success":"error"
				});

				if(data.status){
				    $('#verify_contact').hide();
					$('.pricing-modal').modal('hide');

				}
			}
		});     
    });

    
    $("#coupon_apply").on("click",function(){
	    $('.txt').text('Please Wait..');
		var form = document.getElementById('verify_contact');
		var course_id = form.elements['course_id'].value;
        var user_id = form.elements['user_id'].value;
        var coupon_code = form.elements['coupon_code'].value;
        var plan_type = form.querySelector('input[name="plan_type"]:checked');
        plan_type = plan_type ? plan_type.value : 0;

		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},      
			type: "POST",
			url : '<?php echo e(route('admin.payment.apply_coupon')); ?>',
			data : {course_id,user_id,coupon_code,plan_type},
			dataType : 'json',
			success : function(rsp){
				console.log(rsp);
				$('.txt').text('');
				swal({
					title: rsp.status?"Success":"Error!",
					text: rsp.message,
					html: true,
					type: rsp.status?"success":"error"
				});

				if(rsp.status){
					$(".coupon_discount").text("You Saved Amount:"+rsp.data.coupon.discount+", Final Price:"+rsp.data.final_mrp);
					$(".coupon_id").val(rsp.data.coupon.id);
                    $(".discount").val(rsp.data.coupon.discount);
				}else{
                   $(".coupon_id").val(0);
                   $(".discount").val(0);
				}
			}
		});     
    });
	
</script>
<script>
document.addEventListener('contextmenu', function(e) {
	e.preventDefault();
});

/*
document.onkeydown = function(e) {
	if(event.keyCode == 123) {
		return false;
	}
	if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {
		return false;
	}
	if(e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) {
		return false;
	}
	if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {
		return false;
	}
	if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {
		return false;
	}
}*/

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/payment/coupon.blade.php ENDPATH**/ ?>