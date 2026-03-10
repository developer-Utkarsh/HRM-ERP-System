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
						<h2 class="content-header-title float-left mb-0">CRM Desk Search</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
						
					</div>
					<div class="breadcrumb-wrapper col-4 text-right">
					<?php
					if(Auth::user()->id==901 || Auth::user()->id==1540){
						?>
							<a href="javascript:void(0);" class="btn btn-primary" data-toggle="modal" data-target="#myCourseModal">Add Course</a>
						<?php
					}
					?>
						<a href="javascript:void(0);" class="btn btn-primary" data-toggle="modal" data-target="#mySubModal">Add</a>
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
								<form id="crm_search" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-lg-3">
											<label for="users-list-role">Search Type</label>
											<fieldset class="form-group">
												<select class="form-control search_type" name="type" required>
													<option value="">Select Type</option>
													<option value="mobile">Mobile</option>
													<option value="email">Email</option>
													<option value="ticket">Ticket No.</option>
												</select>
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">&nbsp;</label>
											<fieldset  >
												<input type="text" class="form-control search_name" name="name" placeholder="Search..." required>
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('admin.crm-desk.search') }}" class="btn btn-warning">Reset</a>
											</fieldset>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-6">
						<div class="table-responsive">
							<table class="table data-list-view table-striped">
								<thead>
								</thead>
								<tbody class="serach_result">
									<!--tr>
										<td>Test Data Set</td>
									</tr-->
								</tbody>
							</table>
						</div>
					</div>
					<div class="col-md-6 call_activity_div" style="display:none;">
						<div>
							<a href="javascript:void(0);" class="btn btn-primary" onClick="call_activity_reply('reply')">&nbsp;&nbsp; Reply &nbsp;&nbsp;</a>
							<a href="javascript:void(0);" class="btn btn-primary" onClick="call_activity_reply('comment')">&nbsp;&nbsp; Private Comment &nbsp;&nbsp;</a>
						</div>
						<div class="table-responsive">
							<table class="table data-list-view">
								<!--thead>
									<th class="set_title"></th>
								</thead-->
								<tbody class="call_activity_set">
									
								</tbody>
							</table>
						</div>
					</div>
				</div>
				
			</section>
		</div>
	</div>
</div>


<style type="text/css">
.select2-results__option[aria-selected] {
    cursor: pointer;
    font-weight: bolder !important;
    color : #000 !important;
}
/* Important part */
.modal-dialog{
    overflow-y: initial !important
}
.modal-body{
    height: 80vh;
    overflow-y: auto;
}
</style>

<div class="modal" id="mySubModal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Form</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

    <form method="post" action="javascript:void(0)" id="form_submit">
      {{ csrf_field() }}
		  
		  <!-- Modal body -->
		  <div class="modal-body">
				<div class="row">
					<input type="hidden" name="agent_name" class="form-control agent_name" value="{{$logged_name}}" required>
					<div class="col-md-6 col-12">
						<div class="form-group">
							<label for="first-name-column">Department  * </label>
							<select class="form-control select-multiple" name="department" required>
								<option value=""> Student Type </option>
								<option value="Support inbound">Support inbound</option>
								<option value="Facebook Team">Facebook Team</option>
								<option value="YouTube Team">YouTube Team</option>
								<option value="WebChat Team">WebChat Team</option>
								<option value="Content Team">Content Team</option>
								<option value="PlayStore Team">PlayStore Team</option>
								<option value="Zoho Team">Zoho Team</option>
								<option value="Outbound Team">Outbound Team</option>
							</select>
						</div>
					</div>
					<div class="col-md-6 col-12">
						<div class="form-group">
							<label for="first-name-column">Student Name *</label>
							<input type="text" class="form-control" placeholder="" name="student_name" required>
						</div>
					</div>
					<div class="col-md-6 col-12">
						<div class="form-group">
							<label for="company-column">Gender *</label>
							<div class="form-group d-flex align-items-center mt-1">															
								<label>
									<input type="radio" name="gender" value="Male" required>
									Male
								</label>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<label>
									<input type="radio" name="gender" value="Female" required>
									Female
								</label>
							</div>
						</div>
					</div>
					<div class="col-md-6 col-12">
						<div class="form-group">
							<label for="first-name-column">Calling No. *</label>
							<input type="text" class="form-control calling_no" name="calling_no" onkeypress='validate(event)' maxlength="10" minlength="10" required>
						</div>
					</div>
					<div class="col-md-6 col-12">
						<div class="form-group">
							<label for="first-name-column">Registered Mobile Number on App</label>
							<input type="text" class="form-control rg_number" name="rg_number" required>
						</div>
					</div>
					<div class="col-md-6 col-12">
						<div class="form-group">
							<label for="first-name-column">Email *</label>
							<input type="text" class="form-control" name="email" id="email" required>
						</div>
					</div>
					<div class="col-md-6 col-12">
						<div class="form-group">
							<label for="first-name-column">State * </label>
							<select onchange="print_city('state', this.selectedIndex);" id="sts" name ="state" class="form-control select-state" required></select>
						</div>
					</div>
					<div class="col-md-6 col-12">
						<div class="form-group">
							<label for="first-name-column">District </label>
							<select id ="state" class="form-control select-district" name="district"></select>
						</div>
					</div>

					<div class="col-md-12 col-12">
						&nbsp;
					</div>
					
					<div class="col-md-12 col-12"><hr>
						<div class="col-md-12 col-12 mx-auto">
							<div class="form-group">
								<label for="company-column">Call Related *</label>
								<div class="form-group d-flex align-items-center mt-1">															
									<label>
										<input type="radio" name="call_related" value="Support related" class="" required>
										Support related
									</label>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<label>
										<input type="radio" name="call_related" value="Inquiry" class="" required >
										Course Related
									</label>
								</div>
							</div>
						</div>
				  </div>

				  <!-- <div class="col-md-12 col-12 leadcategory d-none">
						<div class="form-group">
							<label for="first-name-column">Course Category New</label>
							<select class="form-control select-multiple2" name="leadcategory">
								<option value=""> Select Category </option>
								<option value="Agriculture">Agriculture</option>
								<option value="Bihar Civil Services">Bihar Civil Services</option>
								<option value="Bihar One Day">Bihar One Day</option>
								<option value="Central Civil Services">Central Civil Services</option>
								<option value="Central One Day">Central One Day</option>
								<option value="CLAT">CLAT</option>
								<option value="CUET">CUET</option>
								<option value="Defence">Defence</option>
								<option value="Engineering">Engineering</option>
								<option value="Haryana One Day">Haryana One Day</option>
								<option value="Judicial Services">Judicial Services</option>
								<option value="Madhya Pradesh Civil Services">Madhya Pradesh Civil Services</option>
								<option value="Madhya Pradesh One Day">Madhya Pradesh One Day</option>
								<option value="Memory Course">Memory Course</option>
								<option value="NEET / JEE">NEET / JEE</option>
								<option value="Nursing">Nursing</option>
								<option value="Rajasthan Civil Services">Rajasthan Civil Services</option>
								<option value="Rajasthan One Day">Rajasthan One Day</option>
								<option value="School">School</option>
								<option value="Subject Wise Courses">Subject Wise Courses</option>
								<option value="Uttar Pradesh Civil Services">Uttar Pradesh Civil Services</option>
								<option value="Uttar Pradesh One Day">Uttar Pradesh One Day</option>
							</select>
						</div>
					</div> -->

					<div class="col-md-6 col-12">
						<div class="form-group">
							<label for="first-name-column">Student Type * </label>
							<select class="form-control select-multiple2" name="student_type" required>
								<option value=""> Student Type </option>
								<option value="Online Course">Online Course</option>
								<option value="Jaipur Offline">Jaipur Offline</option>
								<option value="Jodhpur Offline">Jodhpur Offline</option>
								<option value="Bihar Offline">Bihar Offline</option>
								<option value="Prayagraj Offline">Prayagraj Offline</option>
								<option value="Indore">MP (Indore) Offline</option>
								<option value="Vidyapeeth Jodhpur">Vidyapeeth Jodhpur</option>
								<option value="Nehal Virtual School">Nehal Virtual School </option>
								<option value="Testshala">Testshala</option>
								<option value="Test Guruji">Test Guruji</option>
								<option value="Utkarsh Sarthi">Utkarsh Sarthi</option>
								<option value="Bookshala">Bookshala</option>
								<option value="Seminar">Seminar</option>
								<option value="Other">Other</option>
							</select>
						</div>
					</div>

					<div class="col-md-6 col-12">
						<div class="form-group">
							<label for="first-name-column">Medium </label>
							<select class="form-control" name="medium">
								<option value="Hindi" selected>Hindi</option>
								<option value="English">English</option>
							</select>
						</div>
					</div>

					<div class="col-md-12 col-12 div_support">
						<div class="row">
							<div class="col-md-6 col-12">
								<div class="form-group support_type">
									<label for="first-name-column">Support * </label>
									<select class="form-control select-multiple3" id="dynamicSelectBox" name="support_type">
										<option value=""> Support Type </option>
										<option value="App Technical Related">App Technical Related</option>
										<option value="Content Related">Content Related</option>
										<option value="Emitra Related">Emitra Related</option>
										<option value="Notes Related">Notes Related</option>
										<option value="PDF Related">PDF Related</option>
										<option value="Model Paper Related">Model Paper Related</option>
										<option value="Payment Related">Payment Related</option>
										<option value="Refund Related">Refund Related</option>
										<option value="Faculty Related">Faculty Related</option>
										<option value="Validity Related">Validity Related</option>
										<option value="Test Related">Test Related</option>
										<option value="DPP Related">DPP Related</option>
										<option value="Anuparti Inquiry">Anuparti Inquiry</option>
										<option value="User id & Password not received">User id & Password not received</option>
										<option value="Quiz Related">Quiz Related</option>
										<option value="Feedback">Feedback</option>
										<option value="Job related">Job related</option>
										<option value="Hostel Related">Hostel Related</option>
										<option value="Address Related">Address Related</option>
										<option value="Offline Join Student">Offline Join Student</option>
										<option value="Course Change related">Course Change related</option>
										<option value="Demo Class Related">Demo Class Related</option>
										<option value="YouTube Class Related">YouTube Class Related</option>
										<option value="Payment done but Course not Added">Payment done but Course not Added</option>
										<option value="Class Ended issue">Class Ended issue </option>
										<option value="Live Class Time Change related">Live Class Time Change related </option>
										<option value="Live Class Cancle related">Live Class Cancle related </option>
										<option value="Live Class not on time">Live Class not on time</option>
										<option value="Other">Other</option>
									</select>
								</div>
							</div>
							<div class="col-md-6 col-12">
								<div class="form-group">
									<label for="company-column">Problem Status *</label>
									<div class="form-group d-flex align-items-center mt-1">															
										<label>
											<input type="radio" name="ticket_status" value="Open" required> Pending
										</label>
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<label>
											<input type="radio" name="ticket_status" value="Closed" required> Solved on Call
										</label>
									</div>
								</div>
							</div>
						</div>
					</div>

					
					<div class="col-md-12 col-12 div_select_category_name">
						<div class="form-group">
							<label for="first-name-column">Main Category</label>
							<select class="form-control select-multiple4 select_category_name" name="category_name" required>
								<option value=""> Select Category </option>
								<?php
								if(!empty($category_name)){
									foreach($category_name as $val){
										?>
										{{-- <option value="{{$val['category name']}}">{{$val['category name']}}</option> --}}
										<option value="{{$val}}">{{$val}}</option>
										<?php
									}
								} 
								?>
							</select>
						</div>
					</div>
					<div class="col-md-12 col-12 div_select_main_category">
						<div class="form-group">
							<label for="first-name-column">Category</label>
							<select class="form-control select-multiple5 select_main_category" name="main_category">
								<option value=""> Select Category </option>
							</select>
						</div>
					</div>
					<div class="col-md-12 col-12">
						<div class="form-group">
							<label for="first-name-column">Course Name * </label>
							<select class="form-control select-multiple6 select_course_name" name="course_name--" required multiple>
								<option value=""> Select Course </option>
							</select>
						</div>
					</div>
          
          <div class="div_lead_score row col-12">
						<div class="col-md-4">
							<div class="form-group">
								<label for="first-name-column">Inquiry lead score * </label>
								<select class="form-control" name="lead_score">
									<option value=""> Select </option>
									<option value="Hot lead (Admission in 1 to 2 days)">Hot lead (Admission in 1 to 2 days)</option>
									<option value="Warm lead (Admission in a week)">Warm lead (Admission in a week)</option>
									<option value="Semi Lead (Admission in 10 to 15 days)">Semi Lead (Admission in 10 to 15 days)</option>
									<option value="Cold lead (Course or batch not avaialble)">Cold lead (Course or batch not avaialble)</option>
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="first-name-column">Inquiry Source</label>
								<select class="form-control" name="lead_source">
									<option value=""> Select </option>
									<option value="Newspaper">Newspaper</option>
									<option value="Hoardings">Hoardings</option>
									<option value="YouTube">YouTube</option>
									<option value="Instagram">Instagram</option>
									<option value="Facebook">Facebook</option>
									<option value="Telegram">Telegram</option>
									<option value="Friends">Friends</option>
									<option value="Teachers">Teachers</option>
									<option value="Marketing Team">Marketing Team</option>
									<option value="Existing Student">Existing Student</option>
									<option value="UTKARSH APP">UTKARSH APP</option>
									<option value="Google">Google</option>
									<option value="Centre Visit">Centre Visit</option>
									<option value="Pamphlets">Pamphlets</option>
									<option value="Helpline No">Helpline No</option>
									<option value="Parents">Parents</option>
									<option value="Other">Other</option>
								</select>
							</div>
						</div>
					</div>
					
					<div class="col-md-12 col-12">
						<div class="form-group">
							<label for="first-name-column">Description * </label>
							<textarea class="form-control" rows="5" name="description" required ></textarea>
						</div>
					</div>
				</div>

				<!-- Modal footer -->
	      <div class="modal-footer row">
	      	<div class="col-md-12  text-center">
			      <strong class="please_wait text-danger"></strong>
	      	  <button type="submit" class="btn btn-primary btn_submit">Submit</button>
	        </div>
	      </div>

		  </div>
    </form>

    </div>
  </div>
</div>


<div class="modal" id="mySubModal_iframe" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" style="height: 100%;">
    <div class="modal-content" style="height: 100%;">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Form</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
 
	      
	     <div class="modal-body" style="height: 100%;">
			   <iframe src="" style="width: 100%;height: 100%;" class="iframe_url"></iframe>
	      </div> 

    </div>
  </div>
</div>


<div class="modal" id="mySubModal_reply_comment">
  <div class="modal-dialog modal-lg" style="height: 100%;">
    <div class="modal-content">
		<div class="modal-header">
			<h4 class="modal-title reply_modal_text">Message</h4>
			<button type="button" class="close" data-dismiss="modal">&times;</button>
		</div> 
	    <form method="post" action="javascript:void(0)" id="form_reply_submit">
			<input type="hidden" class="reply_type" name="reply_type" />
			<input type="hidden" class="ticket_id" name="ticket_id" />
			<input type="hidden" class="ticket_email" name="ticket_email" />
			<div class="modal-body" style="height: 100%;">
				<div class="col-md-12 col-12">
					<div class="form-group">
						<label for="first-name-column">Message *</label>
						<textarea class="form-control reply_description" rows="3" name="description" required ></textarea>
					</div>
				</div>
			</div> 
			<div class="modal-footer">
				<strong class="please_wait_reply text-danger"></strong>
				<button type="submit" class="btn btn-primary btn_submit_reply">Submit</button>
			</div>
		</form>
    </div>
  </div>
</div>

<div class="modal" id="myCourseModal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Form</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

    <form method="post" action="javascript:void(0)" id="form_crm_submit">
      {{ csrf_field() }}
		  
		  <!-- Modal body -->
		  <div class="modal-body" style="height:auto;">
				<div class="row">
					<input type="hidden" name="agent_name" class="form-control agent_name" value="{{$logged_name}}" required>
					<div class="col-md-6 col-12">
						<div class="form-group">
							<label for="first-name-column">Course Type  * </label>
							<select class="form-control select-multiple" name="course_type" required>
								<option value=""> Select Course Type </option>
								<option value="Jaipur Offline">Jaipur Offline</option>
								<option value="Jodhpur Offline">Jodhpur Offline</option>
								<option value="Bihar Offline">Bihar Offline</option>
								<option value="Prayagraj Offline">Prayagraj Offline</option>
								<option value="MP (Indore) Offline">MP (Indore) Offline</option>
								<option value="Vidyapeeth Jodhpur">Vidyapeeth Jodhpur</option>
								<option value="Nehal Virtual School">Nehal Virtual School </option>
							</select>
						</div>
					</div>
					<div class="col-md-6 col-12">
						<div class="form-group">
							<label for="first-name-column">Course Name *</label>
							<input type="text" class="form-control" placeholder="Course Name" name="crm_course_name" required>
						</div>
					</div>
				</div>

				<!-- Modal footer -->
	      <div class="modal-footer row">
	      	<div class="col-md-12  text-center">
			      <strong class="please_wait text-danger"></strong>
	      	  <button type="submit" class="btn btn-primary btn_submit">Submit</button>
	        </div>
	      </div>

		  </div>
    </form>

    </div>
  </div>
</div>

<div id="overlay_loader">
	<div>
		<span>Please Wait.. Request Is In Processing.</span><br>
		<i class="fa fa-refresh fa-spin fa-5x"></i>
	</div>
</div>


<style>
#overlay_loader {
  position: fixed;
	display: none;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.8);
    z-index: 10000;
    cursor: pointer;
}
#overlay_loader div {
    position: absolute;
    top: 50%;
    left: 50%;
    font-size: 40px;
    text-align: center;
    color: white;
    transform: translate(-50%, -50%);
    -ms-transform: translate(-50%, -50%);
    width: 100%;
}

.select2.select2-container{
	width:100% !important;
}
</style>

<style>

table {
	border-collapse: unset !important;
}

.table tbody td {
    border: solid 1px #ccc !important;
    font-size: 12px;
}
.table thead th {
    border: solid 1px #ccc !important;
    font-size: 14px;
    background: #ededed;
}

table a {
	color : #8d83ee !important;
}
</style>
@endsection

@section('scripts')
<script src="{{asset('/laravel/public/js/cities.js')}}"></script>
<script language="javascript">print_state("sts");</script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script>
$(document).ready(function() {
	$('.select-multiple,.select-multiple2,.select-multiple3,.select-multiple4,.select-multiple5,.select-state,.select-district').select2({
		placeholder: "--Select--",
		allowClear: true
	});
	
	$(".select-multiple6").select2({
		tags: true,
		placeholder: "--Select--",
		allowClear: true
	});

	$("input[name='call_related']").change(function(){
		var call_related=$(this).val();
		if(call_related=='Inquiry'){
			$(".div_support").css('display','none');
			$(".div_select_main_category").css('display','block');
			$(".div_select_category_name").css('display','block');
			$(".div_lead_score").css('display','block');
			$(".select_category_name").prop("required",true);
			$('input[name="ticket_status"]').prop('required', false);
			//$(".select_main_category").prop("required",true);

			$("select[name='student_type']").change(function(){
				var value=$(this).val();
				console.log(value);
				if(value=='Online Course'){
					$(".div_select_main_category").css('display','block');
					$(".div_select_category_name").css('display','block');
					$(".select_category_name").prop("required",true);
				}
			});
			// $('select[name="leadcategory"]').prop('required', true);
			// $(".leadcategory").css('display','block');		
		}else{
			$(".div_support").css('display','block');
			$(".div_select_main_category").css('display','block');
			$(".div_select_category_name").css('display','block');
			$(".div_lead_score").css('display','none');
			$(".select_category_name").prop("required",false);
			$(".select_main_category").prop("required",false);
			$('input[name="ticket_status"]').prop('required', true);		  
			// $(".leadcategory").css('display','none');
		}
	});

	$("select[name='support_type']").change(function(){
		var value=$(this).val();
		var data="";
		if(value=="App Technical Related"){
			data="Main Book id & Course Name :- \n Device Name:- \n Version:- \n Network:- \n issue :- \n Priority:-";
			$("textarea[name='description']").val(data);
		}else{
			data="Main Book id & Course Name :-\n Package Book id & Subject \n Topic id & Name :- \n Part No, Name & id:- \n issue :- \n Priority:-";
			$("textarea[name='description']").val(data);
		}
	});
	
	

	$("select[name='student_type']").change(function(){
		call_relate	=	$("input[name='call_related']:checked").val();
		console.log(call_relate);
		if(call_relate=='Inquiry'){
			$(".div_select_category_name").css('display','none');
			$(".div_select_main_category").css('display','none');
		}else{
			$(".div_select_category_name").css('display','block');
			$(".div_select_main_category").css('display','block');
		}
		
		// $(".div_select_category_name").css('display','none');
		// $(".div_select_main_category").css('display','none');
		
		var value=$(this).val();
		var js_array=[];

		if(value=='Other'){
			$('select[name^="support_type"] option:selected').attr("selected",null);
			$('select[name^="support_type"] option[value="Other"]').attr("selected","selected");
			$('.select-multiple3').select2();
			$("input[name=ticket_status][value=Closed]").prop('checked', true);
		}else if(value=='Jaipur Offline'){
			js_array =<?php echo json_encode($offlinejaipur);?>;
			$(".select_category_name").prop("required",false); 
		}else if(value=='Jodhpur Offline'){
			js_array =<?php echo json_encode($offlinejodhpur);?>;
			$(".select_category_name").prop("required",false);
		}else if(value=='Bihar Offline'){
			js_array =<?php echo json_encode($offlinebihar);?>;
			$(".select_category_name").prop("required",false);
		}else if(value=='Prayagraj Offline'){
			js_array=<?php echo json_encode($offlineprayagraj);?>;
			$(".select_category_name").prop("required",false);
		}else if(value=='Vidyapeeth Jodhpur'){
			js_array=<?php echo json_encode($vidyapeethjodhpur);?>;
			$(".select_category_name").prop("required",false);
		}else if(value=='Nehal Virtual School'){
			js_array=<?php echo json_encode($nvs);?>;
			$(".select_category_name").prop("required",false);
		}else if(value=='Indore'){
			js_array=<?php echo json_encode($mpindore);?>;
			$(".select_category_name").prop("required",false);
		}else if(value != 'Bookshala' && value != 'Test Guruji'){
			const dynamicSelectBox = document.getElementById('dynamicSelectBox');
				
            dynamicSelectBox.innerHTML = `

            				<option value="" > --Support Type-- </option>
										<option value="App Technical Related">App Technical Related</option>
										<option value="Content Related">Content Related</option>
										<option value="Emitra Related">Emitra Related</option>
										<option value="Notes Related">Notes Related</option>
										<option value="PDF Related">PDF Related</option>
										<option value="Model Paper Related">Model Paper Related</option>
										<option value="Payment Related">Payment Related</option>
										<option value="Refund Related">Refund Related</option>
										<option value="Faculty Related">Faculty Related</option>
										<option value="Validity Related">Validity Related</option>
										<option value="Test Related">Test Related</option>
										<option value="DPP Related">DPP Related</option>
										<option value="Anuparti Inquiry">Anuparti Inquiry</option>
										<option value="User id & Password not received">User id & Password not received</option>
										<option value="Quiz Related">Quiz Related</option>
										<option value="Feedback">Feedback</option>
										<option value="Job related">Job related</option>
										<option value="Hostel Related">Hostel Related</option>
										<option value="Address Related">Address Related</option>
										<option value="Offline Join Student">Offline Join Student</option>
										<option value="Course Change related">Course Change related</option>
										<option value="Demo Class Related">Demo Class Related</option>
										<option value="YouTube Class Related">YouTube Class Related</option>
										<option value="Payment done but Course not Added">Payment done but Course not Added</option>
										<option value="Class Ended issue">Class Ended issue </option>
										<option value="Live Class Time Change related">Live Class Time Change related </option>
										<option value="Live Class Cancle related">Live Class Cancle related </option>
										<option value="Live Class not on time">Live Class not on time</option>
										<option value="Video Download option not updated">Video Download option not updated</option>
										<option value="Video Black Screen">Video Black Screen</option>
										<option value="Video not Complete">Video not Complete</option>
										<option value="Video play back failed">Video play back failed</option>
										<option value="Video not play properly">Video not play properly</option>
										<option value="Video Voice issue">Video Voice issue</option>
										<option value="Video not sequence">Video not sequence</option>
										<option value="Video previous class not updated">Video previous class not updated</option>
										<option value="Other">Other</option>
            `

				 $(".select_category_name").prop("required",false);
			}else if(value=='Bookshala'){
				 
				const dynamicSelectBox = document.getElementById('dynamicSelectBox');
				
            dynamicSelectBox.innerHTML = `<option value=""selected="true">--Select--</option><option value="After Payment Order Not Show In App">After Payment Order Not Show In App</option>
<option value="Book Payment Related">Book Payment Related</option>
<option value="Book Purchase Inq">Book Purchase Inq</option>
<option value="Book Status Information">Book Status Information</option>
<option value="Tracking Related Issue">Tracking Related Issue</option>
<option value="Order Cancel And Refund">Order Cancel And Refund</option>
<option value="Order Not Fixable">Order Not Fixable</option>
<option value="Book Defective">Book Defective</option>
<option value="Book Missing">Book Missing</option>
<option value="Book Exchange">Book Exchange</option>
<option value="Address And Number Update Releted">Address And Number Update Releted</option>
<option value="Address Not Fill In Order">Address Not Fill In Order</option>
<option value="Book Content Issue">Book Content Issue</option>
<option value="New Book Inq">New Book Inq</option>
<option value="Tech Issue In App And Order (Erp Issue)">Tech Issue In App And Order (Erp Issue)</option>
<option value="Book At Delivery Center But Not Delivered To Students">Book At Delivery Center But Not Delivered To Students</option>
<option value="Qr Code Scanning Related">Qr Code Scanning Related</option>
<option value="Otp Issue At Delivery Time">Otp Issue At Delivery Time</option>
<option value="Book Offline Purchase">Book Offline Purchase</option>
<option value="Book Return">Book Return</option>`;

								/*<option value="Address not Accepted (pin code issue , Tech.issue in App)">Address not Accepted (pin code issue , Tech.issue in App)</option>
								<option value="Book Buy Related (How to Buy From Utkarsh App)">Book Buy Related (How to Buy From Utkarsh App)</option>
								<option value="Book  dispatch related (When will be dispatched)">Book  dispatch related (When will be dispatched)</option>
								<option value="Book Available in Offline Center or not">Book Available in Offline Center or not</option>
								<option value="Book Buy From Iphone">Book Buy From Iphone</option>
								<option value="Book Buy Related (in English Medium)">Book Buy Related (in English Medium)</option>
								<option value="Book Content Related issue">Book Content Related issue</option>
								<option value="Book Damaged">Book Damaged</option>
								<option value="Book Language related inquiry">Book Language related inquiry</option>
								<option value="Book Misprint">Book Misprint</option>
								<option value="Book Missmatch (set not complete)">Book Missmatch (set not complete)</option>
								<option value="Book Not recived">Book Not recived</option>
								<option value="Book Return">Book Return</option>
								<option value="Book Tracking issue (Tech.issue in App)">Book Tracking issue (Tech.issue in App)</option>
								<option value="taking Extra payment from Dilivary boy">taking Extra payment from Dilivary boy</option>
								<option value="Book Dispatch Given information on call">Book Dispatch Given information on call</option>
								<option value="Notes Buy Related (How to Buy From Utkarsh App)">Notes Buy Related (How to Buy From Utkarsh App)</option>
								<option value="Notes Dispatch Related (When will be dispatched)">Notes Dispatch Related (When will be dispatched)</option>
								<option value="Number Update Related">Number Update Related</option>
								<option value="Order cancle & Refund related">Order cancle & Refund related</option>
								<option value="Payment Confimation related">Payment Confimation related</option>
								<option value="Payment Done But Not Show in My Order (Tech.issue in App) ">Payment Done But Not Show in My Order (Tech.issue in App) </option>
								<option value="Payment Done But Show Failed">Payment Done But Show Failed</option>
								<option value="Suggestion">Suggestion</option>
								<option value="Wrong Book Delivered">Wrong Book Delivered</option>
								<option value="OTP issue">OTP issue</option>*/
				 $(".select_category_name").prop("required",false);
			}else if(value=='Test Guruji'){
				 
				const dynamicSelectBox = document.getElementById('dynamicSelectBox');
				
            dynamicSelectBox.innerHTML = `
            	  <option value=""selected="true">--Select--</option>
                <option value="Admit Card Related">Admit Card Related</option>
								<option value="Center Change Related">Center Change Related</option>
								<option value="Center Location Related">Center Location Related</option>
								<option value="Offline to Online Convert Related">Offline to Online Convert Related</option>
								<option value="Registration Confirmation Related">Registration Confirmation Related</option>
								<option value="Test Guruji Book not Show in App">Test Guruji Book not Show in App</option>
								<option value="Test Video Solution Related">Test Video Solution Related</option>
								<option value="Time & Schudule Related">Time & Schudule Related</option>
                `
				 $(".select_category_name").prop("required",false);
			}else if(value=='Online Course'){
				 
				const dynamicSelectBox = document.getElementById('dynamicSelectBox');
				
            dynamicSelectBox.innerHTML = `
            	  <option value=""selected="true">--Select--</option>
                <option value="Admit Card Related">Admit Card Related</option>
								<option value="Center Change Related">Center Change Related</option>
								<option value="Center Location Related">Center Location Related</option>
								<option value="Offline to Online Convert Related">Offline to Online Convert Related</option>
								<option value="Registration Confirmation Related">Registration Confirmation Related</option>
								<option value="Test Guruji Book not Show in App">Test Guruji Book not Show in App</option>
								<option value="Test Video Solution Related">Test Video Solution Related</option>
								<option value="Time & Schudule Related">Time & Schudule Related</option>
                `
				 $(".select_category_name").prop("required",false);
			}else{
        
	      var call_related=$("input[name='call_related']:checked").val();
	      if(call_related=='Inquiry' || call_related=='Support related'){
					$(".div_select_category_name").css('display','block');
					$(".div_select_main_category").css('display','block');
				}

      	console.log(value);
      	if (value=='Online Course') {
      		$(".select_category_name").prop("required",true);
      	}

			}

	    var data="<option value''>--Select Course--</option>";
	    $.each(js_array, function( key, value ) {
	      data+="<option value='"+value.course+"'>"+value.course+"</option>";
	    });
			$(".select_course_name").html(data);
			//console.log(data);
	});

  $("#form_submit").find("label").css("font-size","18px").css("font-weight","bold");
  $("option").css("font-size","16px").css("font-weight","normal");	
})

function validate(evt) {
  var theEvent = evt || window.event;

  // Handle paste
  if (theEvent.type === 'paste') {
	  key = event.clipboardData.getData('text/plain');
  } else {
  // Handle key press
	  var key = theEvent.keyCode || theEvent.which;
	  key = String.fromCharCode(key);
  }
  var regex = /[0-9]|\./;
  if( !regex.test(key) ) {
	theEvent.returnValue = false;
	if(theEvent.preventDefault) theEvent.preventDefault();
  }
}

$("#crm_search").submit(function(e) {
	$("#overlay_loader").css('display','block');
	$(".serach_result").html('');
	$(".call_activity_set").html('');
	$(".call_activity_div").css('display','none');
	// var form = document.getElementById('crm_search');
	// var dataForm = new FormData(form);
	var search_type = $(".search_type").val();
	var search_name = $(".search_name").val();
	e.preventDefault(); 
	$.ajax({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},      
		type: "POST",
		url : '{{ route('admin.crm-desk.search_result') }}',
		data : {'type': search_type,'name':search_name},
		// processData : false,  
		// contentType : false,
		dataType : 'json',
		success : function(data){
			$("#overlay_loader").css('display','none');
			if(data.status == false){
				$(".serach_result").html("");
				swal("Error!", data.message, "error");
			} else if(data.status == true){						
				 $(".serach_result").html(data.html);
			}
		}
	});   
});

function call_activity(e){
	$("#overlay_loader").css('display','block');
	$(".call_activity_div").css('display','block');
	$(".call_activity_set").html('');
	var id = $(e).data('id');
	var email = $(e).data('email');
	if(id){
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},      
			type: "POST",
			url : '{{ route('admin.crm-desk.call_activity') }}',
			data : {'id': id},
			dataType : 'json',			
			success : function(data){
				$("#overlay_loader").css('display','none');
				if(data.status == false){
					$(".call_activity_div").css('display','none');
					$(".call_activity_set").html("");
					swal("Error!", data.message, "error");
				} else if(data.status == true){						
					//$(".call_activity_set").html(data.html);
					$(".call_activity_set").html(data.html);
					$(".ticket_id").val(id);
					$(".ticket_email").val(email);
					$(".call_activity_div").css('display','block');
				}
			}
		});   
	}
}
 
$("#form_submit").submit(function(e) {
	e.preventDefault(); 
	var queryString = $('#form_submit').serialize();
	var agent_name = $(".agent_name").val();
	var department_name = $(".department_name").val();
	var calling_no = $(".calling_no").val();
   
	var course_name=$(".select_course_name").val();
	course_name=encodeURIComponent(course_name);
	queryString+="&course_name="+course_name;
	
	

	console.log(queryString);
	var call_related=$('input[name="call_related"]:checked').val();

	if(call_related=='Inquiry'){
		//crm url
	  var url="https://forms.zohopublic.in/utkarsh/form/CRMEnquiryAddfromCallCenterTeam2/formperma/9r89ebp4llOANFhnT5JVdzWnj3f7d-eGH20wLgOtWeo?"+queryString;
    
    var mobile = $(".rg_number").val();
    let data={};
	   data['name'] =$("input[name='student_name']").val();
	   data['email'] =$("input[name='email']").val();
	   data['source'] =$("select[name='lead_source']").val();
	   data['field_student_state'] =$("select[name='state']").val();
	   data['field_student_district'] =$("select[name='district']").val();
	   data['field_student_type'] =$("select[name='student_type']").val();
	   data['field_medium'] =$("select[name='medium']").val();
	   
	   data['field_main_category'] =$(".select_category_name").val();
	   data['field_sub_category'] =$(".select_main_category").val();
	   data['field_program'] =$(".select_course_name").val();
	   data['field_product_code_id_dropdown'] =122;//$(".select_course_name").val();
	   
	   data['field_inquiry_lead_score'] =$("select[name='lead_score']").val();
	   data['field_inquiry_source'] =$("select[name='lead_source']").val();
	   data['field_description_remark'] =$("textarea[name='description']").val();
	   data['field_user_activity_status'] ='HRM Form Created';
	   data['field_user_activity_date'] =new Date();
	   sendToNpf(mobile,data);
	}else{
		// desk url
		var url="https://forms.zohopublic.in/utkarsh/form/DESKEnquiryAddfromCallCenterTeam/formperma/QKuTwwnvrjP9i5CL7ZbNmJzx-uiBQSSONq6luzd53sY?"+queryString;
	}

	$(".iframe_url").attr('src',url);
	//$("#mySubModal").modal("hide");
	$("#mySubModal_iframe").modal("show");

	$('#form_submit').trigger("reset");
	
	$("select").select2({
    placeholder: "Select",
    allowClear: true,
    tags: true,
  });

});

function sendToNpf(mobile,data){
	$.ajax({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},      
		type: "POST",
		url : '{{ route('admin.crm-desk.sendToNpf') }}',
		data : {'mobile': mobile,'data':data},
		// processData : false,  
		// contentType : false,
		dataType : 'json',
		success : function(data){
		
		}
	});
}

function call_activity_reply(e){
	$(".reply_type").val(e);
	$(".reply_modal_text").text(e);
	$("#mySubModal_reply_comment").modal("show");
}

$("#form_reply_submit").submit(function(e) {
	$("#overlay_loader").css('display','block');
	$(".btn_submit_reply").attr('disabled',true);
	var form = document.getElementById('form_reply_submit');
	var dataForm = new FormData(form);
	var ticket_id = $(".ticket_id").val();
	e.preventDefault(); 
	$.ajax({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},      
		type: "POST",
		url : '{{ route('admin.crm-desk.activity_reply') }}',
		data : dataForm,
		processData : false,  
		contentType : false,
		dataType : 'json',
		success : function(data){
			$("#overlay_loader").css('display','none');
			$(".btn_submit_reply").attr('disabled',false);
			if(data.status == false){
				alert(data.message);
				// swal("Error!", data.message, "error");
			} else if(data.status == true){
				$("#mySubModal_reply_comment").modal("hide");
				$(".reply_description").val('');
				// alert(data.message);
				$("#"+ticket_id).click();
			}
		}
	});   
});

$(document).on("change",".agent_assign",function(){
	var agent_id = $(this).val();
	var ticket_id = $(this).data('id');
	var agent_name = $(this).find(':selected').text();
	var _this = $(this);
	if(ticket_id && agent_id){
		$("#overlay_loader").css('display','block');
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},      
			type: "POST",
			url : '{{ route('admin.crm-desk.assign_agent') }}',
			data : {'ticket_id': ticket_id,'agent_id':agent_id},
			dataType : 'json',			
			success : function(data){
				$("#overlay_loader").css('display','none');
				if(data.status == false){
					alert(data.message)
				} else if(data.status == true){						
					_this.parent('span').siblings('.assigned_name').text(agent_name);
					_this.closest('td').find('.ticket_status').text('Opened Now');
				}
			}
		});   
	}
})

$(document).on("change",".select_category_name",function(){
	var category_name = $(this).val();
	var _this = $(this);
	if(category_name){
		$("#overlay_loader").css('display','block');
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},      
			type: "POST",
			url : '{{ route('admin.crm-desk.get_main_cat') }}',
			data : {'category_name': category_name},
			dataType : 'json',			
			success : function(data){
				$("#overlay_loader").css('display','none');
				if(data.status == false){
					
				}
				else if(data.status == true){					
					$(".select_main_category").html(data.html);
				}
			}
		});   
	}
})

$(document).on("change",".select_main_category",function(){
	var main_category_name = $(this).val();

	var main_category_id = $('option:selected', this).data('id');
	console.log(main_category_id);

	var _this = $(this);
	if(main_category_name){
		$("#overlay_loader").css('display','block');
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},      
			type: "POST",
			url : '{{ route('admin.crm-desk.get_course_name') }}',
			data : {'main_category_name': main_category_name,'main_category_id':main_category_id},
			dataType : 'json',			
			success : function(data){
				$("#overlay_loader").css('display','none');
				if(data.status == false){
					
				}else if(data.status == true){					
					$(".select_course_name").html(data.html);
				}
			}
		});   
	}
})

$("#form_crm_submit").submit(function(e) {
	$("#overlay_loader").css('display','block');
	var form = document.getElementById('form_crm_submit');
	var dataForm = new FormData(form);
	e.preventDefault(); 
	$.ajax({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},      
		type: "POST",
		url : '{{ route('admin.crm-desk.save_course') }}',
		data : dataForm,
		processData : false,  
		contentType : false,
		dataType : 'json',
		success : function(data){
			$("#overlay_loader").css('display','none');
			if(data.status == false){
				alert(data.message);
				// swal("Error!", data.message, "error");
			}else if(data.status == true){
				alert(data.message);
				location.reload();
			}
		}
	});   
});
</script>
@endsection
