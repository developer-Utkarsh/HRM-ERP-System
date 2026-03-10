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
						<h2 class="content-header-title float-left mb-0">CRM Desk Search New </h2>
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
					/* if(Auth::user()->id==901 || Auth::user()->id==1540){
						?>
							<a href="javascript:void(0);" class="btn btn-primary" data-toggle="modal" data-target="#myCourseModal">Add Course</a>
						<?php
					} */
					?>
					  <a href="javascript:void(0);" class="btn btn-primary d-none" data-toggle="modal" data-target="#IPLModal">IPL Winners</a>

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
							<a href="javascript:void(0);" class="btn btn-primary d-none" onClick="call_activity_reply('reply')">&nbsp;&nbsp; Reply &nbsp;&nbsp;</a>
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
					<div class="col-md-4 col-12">
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
					<div class="col-md-4 col-12">
						<div class="form-group">
							<label for="first-name-column">Student Name *</label>
							<input type="text" class="form-control" placeholder="" name="student_name" required>
						</div>
					</div>
					<div class="col-md-4 col-12">
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
					<div class="col-md-4 col-12">
						<div class="form-group">
							<label for="first-name-column">Registered Mobile Number on App</label>
							<input type="text" class="form-control rg_number" name="rg_number" required>
						</div>
					</div>
					<div class="col-md-4 col-12">
						<div class="form-group">
							<label for="first-name-column">Calling No. *</label>
							<input type="text" class="form-control calling_no" name="calling_no" onkeypress='validate(event)' maxlength="10" minlength="10" required>
						</div>
					</div>
					
					<div class="col-md-4 col-12">
						<div class="form-group">
							<label for="first-name-column">Email *</label>
							<input type="text" class="form-control" name="email" id="email" required>
						</div>
					</div>
					<div class="col-md-4 col-12">
						<div class="form-group">
							<label for="first-name-column">State * </label>
							<select onchange="print_city('state', this.selectedIndex);" id="sts" name ="state" class="form-control select-state" required></select>
						</div>
					</div>
					<div class="col-md-4 col-12">
						<div class="form-group">
							<label for="first-name-column">District </label>
							<select id ="state" class="form-control select-district" name="district"></select>
						</div>
					</div>

					
					<div class="col-md-12 col-12"><hr>
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

				  <!-- <div class="col-md-12 col-12 leadcategory d-none">
						<div class="form-group">
							<label for="first-name-column">Course Category New</label>
							<select class="form-control select-multiple2" name="leadcategory">
								<optiosn value=""> Select Category </option>
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
								<option value="Online Course">Online Course Related</option>
								<option value="Jodhpur Offline">Jodhpur Offline Related</option>
								<option value="Jaipur Offline">Jaipur Offline Related</option>
								<option value="Indore">MP (Indore) Offline Related</option>
								<option value="Prayagraj Offline">UP (Prayagraj) Offline Related</option>
								<option value="Bihar Offline">Bihar Offline Related</option>
								<option value="Bookshala">Bookshala Related</option>							
								<option value="Seminar">Seminar Related</option>
								<option value="Navodaya Discount Course Related">Navodaya Discount Course Related</option>
								<option value="Test Guruji">Test Guruji</option>
								<option value="Testshala">Testshala</option>
								<option value="Utkarsh Sarthi">Utkarsh Sarthi</option>
								<option value="Vidyapeeth Jodhpur">Vidyapeeth Jodhpur</option>
								<option value="Ambassador Related">Ambassador Related </option>
								<option value="Raktshala">Raktशाला Related</option>
								<option value="Nehal Virtual School">Nehal Virtual School </option>
								<option value="Gurukul">Gurukul Related</option>
								<option value="Job">Job Related</option>
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

					

					<div class="condition_hs w-100 mx-0">
						<div class="row mx-0">
							<div class="col-md-4 col-12 div_select_category_name">
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
							<div class="col-md-4 col-12 div_select_main_category">
								<div class="form-group">
									<label for="first-name-column">Category</label>
									<select class="form-control select-multiple5 select_main_category" name="main_category">
										<option value=""> Select Category </option>
									</select>
								</div>
							</div>
							<div class="col-md-4 col-12">
								<div class="form-group">
									<label for="first-name-column">Course Name * </label>
									<select class="form-control select-multiple6 select_course_name" name="course_name--" multiple>
										<option value=""> Select Course </option>
									</select>
								</div>
							</div>
						</div>
					</div>
					
					<div class="row mx-0 w-100">
						<div class="col-md-4 col-12 div_support d-none">
							<div class="row">
								<div class="col-md-12 col-12">
									<div class="form-group support_type">
										<label for="first-name-column">Support Type* </label>
										<select class="form-control select-multiple3" id="dynamicSelectBox" name="support_type">
											<option value=""> Support Type </option>
											<!--option value="App Technical Related">App Technical Related</option>
											<option value="Content Related">Content Related</option>
											<option value="Faculty Related">Faculty Related</option>
											<option value="Model Paper Related">Model Paper Related</option>
											<option value="Notes Related">Notes Related</option>
											<option value="Offline Join Student">Offline Join Student</option>
											<option value="Payment Related">Payment Related</option>
											<option value="PDF Related">PDF Related</option>
											<option value="Refund Related">Refund Related</option>
											<option value="Validity Related">Validity Related</option>
											<option value="Test Related">Test Related</option-->
											
											<option value="App-Not Opening">App-Not Opening</option>
											<option value="App-Test Attempt Issue (Laptop)">App-Test Attempt Issue (Laptop)</option>
											<option value="App-Not Working">App-Not Working</option>
											<option value="App-Download Issue">App-Download Issue</option>
											<option value="App-Test Submission Issue">App-Test Submission Issue</option>
											<option value="App-OTP Not Received">App-OTP Not Received</option>
											<option value="App-Password Reset Issue">App-Password Reset Issue</option>
											<option value="Content-Course Not Completed on Time">Content-Course Not Completed on Time</option>
											<option value="Content-Subject Classes Not Regular">Content-Subject Classes Not Regular</option>
											<option value="Content-New Topics Not Updated">Content-New Topics Not Updated</option>
											<option value="Content-Topics Missing (Recorded Course)">Content-Topics Missing (Recorded Course)</option>
											<option value="Content-Incorrect Content">Content-Incorrect Content</option>
											<option value="Content-Subject/Class Start Query">Content-Subject/Class Start Query</option>
											<option value="Content-Tests Not Conducted Regularly">Content-Tests Not Conducted Regularly</option>
											<option value="Content-Faculty Announced, No Update">Content-Faculty Announced, No Update</option>
											<option value="Course Transfer">Course Transfer</option>
											<option value="Suggestion/Demand">Suggestion/Demand</option>
											<option value="Live Class Notification Missed">Live Class Notification Missed</option>
											<option value="Faculty Not Understandable">Faculty Not Understandable</option>
											<option value="Faculty Wrong Commitment">Faculty Wrong Commitment</option>
											<option value="Model Paper Index Missing">Model Paper Index Missing</option>
											<option value="Notes Not Updated">Notes Not Updated</option>
											<option value="PDF-Classroom PDF Not Updated">PDF-Classroom PDF Not Updated</option>
											<option value="PDF-Incorrect PDF">PDF-Incorrect PDF</option>
											<option value="PDF-Out of Sequence">PDF-Out of Sequence</option>
											<option value="PDF-Panel PDF Missing">PDF-Panel PDF Missing</option>
											<option value="PDF-Download Option Missing">PDF-Download Option Missing</option>
											<option value="PDF Not Opening">PDF Not Opening</option>
											<option value="Course Removed – Need 1-Day Extension">Course Removed – Need 1-Day Extension</option>
											<option value="Validity Extension Option Missing">Validity Extension Option Missing</option>
											<option value="Refund Not Received">Refund Not Received</option>
											<option value="Refund Status Query">Refund Status Query</option>
											<option value="Test Index Not Updated">Test Index Not Updated</option>
											<option value="Test Not Live as Scheduled">Test Not Live as Scheduled</option>
											<option value="Live Class Info Not in Schedule">Live Class Info Not in Schedule</option>
											<option value="Validity Extension Price Confirmation">Validity Extension Price Confirmation</option>
											<option value="Course Start Confirmation">Course Start Confirmation</option>
											<option value="App-Laptop Wi-Fi Issue">App-Laptop Wi-Fi Issue</option>
											<option value="Student Disconnected Call">Student Disconnected Call</option>
											<option value="Mock Interview">Mock Interview</option>
											<option value="Info Call">Info Call</option>
											<option value="Meet-Up">Meet-Up</option>
											<option value="Free Course Query">Free Course Query</option>
											<option value="Standard to Prime Conversion">Standard to Prime Conversion</option>
											<option value="Navodaya Dic.Confimation">Navodaya Dic.Confimation</option>
											<option value="Emitra Related">Emitra Related</option>
											<option value="Model Paper Related">Model Paper Related</option>
											<option value="Faculty Related">Faculty Related</option>											
											<option value="DPP Related">DPP Related</option>
											<option value="Anuparti Inquiry">Anuparti Inquiry</option>
											<option value="User id & Password not received">User id & Password not received</option>
											<option value="Quiz Related">Quiz Related</option>
											<option value="Feedback">Feedback</option>
											<option value="Job related">Job related</option>
											<option value="Hostel Related">Hostel Related</option>
											<option value="Address Related">Address Related</option>											
											<option value="Course Change related">Course Change related</option>
											<option value="Demo Class Related">Demo Class Related</option>
											<option value="YouTube Class Related">YouTube Class Related</option>
											<option value="Payment done but Course not Added">Payment done but Course not Added</option>
											<option value="Class Ended issue">Class Ended issue </option>
											<option value="Live Class Time Change related">Live Class Time Change related </option>
											<option value="Live Class Cancel related">Live Class Cancel related </option>
											<option value="Live Class not on time">Live Class not on time</option>
											<option value="Other">Other</option>
											<option value="Transfer Call">Transfer Call</option>
											<option value="Syllabus Change Related">Syllabus Change Related</option>
										</select>
									</div>
								</div>							
							</div>
						</div>
						
						<div class="col-md-4 col-12 div_support">
							<div class="row">
								<div class="col-md-12 col-12">
									<div class="form-group">
										<label for="first-name-column">Course Type* </label>
										<select class="form-control select-multiple3 C_Type" id="dynamicSelectBox" name="C_Type">
											<option value=""> Course Type</option>
											<option value="Live From Studio">Live From Studio</option>											
											<option value="Live From Jodhpur Classroom">Live From Jodhpur Classroom</option>										
											<option value="Live From Jaipur Classroom">Live From Jaipur Classroom</option>											
											<option value="Live From Prayagraj Classroom">Live From Prayagraj Classroom</option>											
										</select>
									</div>
								</div>							
							</div>
						</div>
						<div class="col-md-4 col-12 div_support StuSupport" style="display:none">
							<div class="row">
								<div class="col-md-12 col-12">
									<div class="form-group">
										<label for="first-name-column">Student Support & Services</label>
										<select class="form-control select-multiple3 StudentSupport" id="dynamicSelectBox" name="StudentSupport">
											<option value=""> -- Select --</option>
											<?php foreach($StudentSupport as $ss){ ?>
												<option value="{{ $ss->title }}" data-id="{{ $ss->id }}">{{ $ss->title }}</option>											
											<?php } ?>									
										</select>
									</div>
								</div>							
							</div>
						</div>
						<div class="col-md-4 col-12 div_support StuCategory" style="display:none">
							<div class="row">
								<div class="col-md-12 col-12">
									<div class="form-group">
										<label for="first-name-column">Student Query Category </label>
										<select class="form-control select-multiple3 StudentCategory" id="dynamicSelectBox" name="StudentCategory">
											<option value=""> Course Type</option>								
										</select>
									</div>
								</div>							
							</div>
						</div>
						<div class="col-md-4 col-12 div_support user_type" style="display:none">
							<div class="row">
								<div class="col-md-12 col-12">
									<div class="form-group">
										<label for="first-name-column">User Type* </label>
										<select class="form-control select-multiple3 u_Type" id="dynamicSelectBox" name="u_Type">
											<option value="">-- Select --</option>
											<option value="New User">New User</option>											
											<option value="Existing User">Existing User</option>																						
										</select>
									</div>
								</div>							
							</div>
						</div>
						<div class="div_lead_score col-8">
							<div class="row">
								<div class="col-md-6">
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
								<div class="col-md-6">
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
											<option value="SMS">SMS</option>
											<option value="Other">Other</option>
										</select>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="first-name-column">Course Version</label>
										<select class="form-control field_course_version" name="field_course_version">
											<option value=""> Select </option>											
											<option value="Standard">Standard</option>											
											<option value="Prime">Prime</option>											
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="col-md-12 col-12">
						<div class="form-group">
							<label for="first-name-column">Description * </label>
							<textarea class="form-control" rows="5" name="description" required ></textarea>
						</div>
					</div>
					<div class="row col-12">
						<div class="col-md-6 col-12 pr_priority d-none">
							<div class="form-group">
								<label for="first-name-column">Problem Priority</label>
								<select class="form-control" name="problem_priority">
									<option value=""> Select </option>
									<option value="High">High</option>
									<option value="Medium">Medium</option>
									<option value="Low">Low</option>
								</select>
							</div>
						</div>
						<div class="col-md-6 col-12 problemStatus">
							<div class="form-group">
								<label for="company-column">Problem Status *</label>
								<div class="form-group d-flex align-items-center mt-1">															
									<label>
										<input type="radio" name="ticket_status" value="Open" required> Escalated
									</label>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<label>
										<input type="radio" name="ticket_status" value="Closed" required> Solved on Call
									</label>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-12 col-12">
						<div class="form-group">
							<label for="first-name-column">App Feedback/Suggestion</label>
							<textarea class="form-control" rows="3" name="feedback"></textarea>
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

<div class="modal" id="IPLModal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">IPL Winners</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <!-- Modal body -->
		  <div class="modal-body" style="height:auto;">
		  	<iframe src="https://form.utkarsh.com/UC-System-2024/IPL-Test-Series/support.php" height="1000px" width="100%"></iframe>
		  </div>
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
		tags: false,
		placeholder: "--Select--",
		allowClear: true
	});
	
	$(".select-multiple6").select2({
		tags: false,
		placeholder: "--Select--",
		allowClear: true
	});

	let all_courses=<?php echo json_encode($all_courses); ?>;
	$.each(all_courses,function(key,val){
     //console.log(val);
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
			
			$(".pr_priority").css('display','none');
			$(".problemStatus").css('display','none');
			
						
			$("select[name='student_type']").change(function(){
				var value=$(this).val();
				if(value=='Online Course' || value=='Jodhpur Offline' || value=='Jaipur Offline' || value=='Indore' || value=='Prayagraj Offline' || value=='Bihar Offline'){
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
			
			$(".pr_priority").css('display','block');
			$(".problemStatus").css('display','block');
		}
	});

	$("select[name='support_type']").change(function(){
		var value=$(this).val();
		var data="";
		if(value=="App Technical Related"){
			data="Main Book id & Course Name :- \n Device Name:- \n Version:- \n Network:- \n issue :- \n Priority:-";
			$("textarea[name='description']").val(data);
		}else if(value=="App-Not Opening" || value=="App-Test Attempt Issue (Laptop)" || value=="App-Not Working" || value=="App-Download Issue" || value=="App-Test Submission Issue" || value=="App-OTP Not Received" || value=="App-Password Reset Issue"){
			data="Main Book id & Course Name :-\n Device Name:- \n Version:- \n Network:- \n issue :-";
			$("textarea[name='description']").val(data);
		}else{
			data="Main Book id & Course Name :-\n Package Book id & Subject \n Topic id & Name :- \n Part No, Name & id:- \n issue :- \n Priority:-";
			$("textarea[name='description']").val(data);
		}
	});
	
	$("select[name='student_type']").change(function(){
		$('.condition_hs').css('display','block');
		call_relate	=	$("input[name='call_related']:checked").val();
		console.log(call_relate);
		if(call_relate=='Inquiry'){
			$(".div_select_category_name").css('display','none');
			$(".div_select_main_category").css('display','none');
		}else{
			$(".div_select_category_name").css('display','block');
			$(".div_select_main_category").css('display','block');
		}
		

		
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
		}else if(value=='Navodaya Discount Course Related'){
			$('.condition_hs').css('display','none');
		}else if(value=='Test Guruji'){
			$('.condition_hs').css('display','none');
		}else if(value=='Testshala'){
			$('.condition_hs').css('display','none');
		}else if(value=='Utkarsh Sarthi'){
			$('.condition_hs').css('display','none');
		}else if(value=='Ambassador Related'){
			$('.condition_hs').css('display','none');
		}else if(value=='Raktshala'){
			$('.condition_hs').css('display','none');
		}else if(value=='Gurukul'){
			$('.condition_hs').css('display','none');
		}else if(value=='Job'){
			$('.condition_hs').css('display','none');
		}else if(value=='Seminar'){
			$('.condition_hs').css('display','none');
		}else if(value != 'Bookshala' && value != 'Test Guruji'){
			const dynamicSelectBox = document.getElementById('dynamicSelectBox');
      dynamicSelectBox.innerHTML = `<option value="" > --Support Type-- </option>
			<option value="App-Not Opening">App-Not Opening</option>
			<option value="App-Test Attempt Issue (Laptop)">App-Test Attempt Issue (Laptop)</option>
			<option value="App-Not Working">App-Not Working</option>
			<option value="App-Download Issue">App-Download Issue</option>
			<option value="App-Test Submission Issue">App-Test Submission Issue</option>
			<option value="App-OTP Not Received">App-OTP Not Received</option>
			<option value="App-Password Reset Issue">App-Password Reset Issue</option>
			<option value="Content-Course Not Completed on Time">Content-Course Not Completed on Time</option>
			<option value="Content-Subject Classes Not Regular">Content-Subject Classes Not Regular</option>
			<option value="Content-New Topics Not Updated">Content-New Topics Not Updated</option>
			<option value="Content-Topics Missing (Recorded Course)">Content-Topics Missing (Recorded Course)</option>
			<option value="Content-Incorrect Content">Content-Incorrect Content</option>
			<option value="Content-Subject/Class Start Query">Content-Subject/Class Start Query</option>
			<option value="Content-Tests Not Conducted Regularly">Content-Tests Not Conducted Regularly</option>
			<option value="Content-Faculty Announced, No Update">Content-Faculty Announced, No Update</option>
			<option value="Course Transfer">Course Transfer</option>
			<option value="Suggestion/Demand">Suggestion/Demand</option>
			<option value="Live Class Notification Missed">Live Class Notification Missed</option>
			<option value="Faculty Not Understandable">Faculty Not Understandable</option>
			<option value="Faculty Wrong Commitment">Faculty Wrong Commitment</option>
			<option value="Model Paper Index Missing">Model Paper Index Missing</option>
			<option value="Notes Not Updated">Notes Not Updated</option>
			<option value="PDF-Classroom PDF Not Updated">PDF-Classroom PDF Not Updated</option>
			<option value="PDF-Incorrect PDF">PDF-Incorrect PDF</option>
			<option value="PDF-Out of Sequence">PDF-Out of Sequence</option>
			<option value="PDF-Panel PDF Missing">PDF-Panel PDF Missing</option>
			<option value="PDF-Download Option Missing">PDF-Download Option Missing</option>
			<option value="PDF Not Opening">PDF Not Opening</option>
			<option value="Course Removed – Need 1-Day Extension">Course Removed – Need 1-Day Extension</option>
			<option value="Validity Extension Option Missing">Validity Extension Option Missing</option>
			<option value="Refund Not Received">Refund Not Received</option>
			<option value="Refund Status Query">Refund Status Query</option>
			<option value="Test Index Not Updated">Test Index Not Updated</option>
			<option value="Test Not Live as Scheduled">Test Not Live as Scheduled</option>
			<option value="Live Class Info Not in Schedule">Live Class Info Not in Schedule</option>
			<option value="Validity Extension Price Confirmation">Validity Extension Price Confirmation</option>
			<option value="Course Start Confirmation">Course Start Confirmation</option>
			<option value="App-Laptop Wi-Fi Issue">App-Laptop Wi-Fi Issue</option>
			<option value="Student Disconnected Call">Student Disconnected Call</option>
			<option value="Mock Interview">Mock Interview</option>
			<option value="Info Call">Info Call</option>
			<option value="Meet-Up">Meet-Up</option>
			<option value="Free Course Query">Free Course Query</option>
			<option value="Standard to Prime Conversion">Standard to Prime Conversion</option>
			<option value="Navodaya Dic.Confimation">Navodaya Dic.Confimation</option>
			<option value="Emitra Related">Emitra Related</option>
			<option value="Model Paper Related">Model Paper Related</option>
			<option value="Faculty Related">Faculty Related</option>											
			<option value="DPP Related">DPP Related</option>
			<option value="Anuparti Inquiry">Anuparti Inquiry</option>
			<option value="User id & Password not received">User id & Password not received</option>
			<option value="Quiz Related">Quiz Related</option>
			<option value="Feedback">Feedback</option>
			<option value="Job related">Job related</option>
			<option value="Hostel Related">Hostel Related</option>
			<option value="Address Related">Address Related</option>											
			<option value="Course Change related">Course Change related</option>
			<option value="Demo Class Related">Demo Class Related</option>
			<option value="YouTube Class Related">YouTube Class Related</option>
			<option value="Payment done but Course not Added">Payment done but Course not Added</option>
			<option value="Class Ended issue">Class Ended issue </option>
			<option value="Live Class Time Change related">Live Class Time Change related </option>
			<option value="Live Class Cancel related">Live Class Cancel related </option>
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
			<option value="Transfer Call">Transfer Call</option>
			<option value="Payment Confirmation Related">Payment Confirmation Related</option>
			<option value="Request for Validity Extension">Request for Validity Extension</option>
			<option value="Offline Test Result Related">Offline Test Result Related</option>
			<option value="Syllabus Change Related">Syllabus Change Related</option>`

			$(".select_category_name").prop("required",false);
		}else if(value=='Bookshala'){
			const dynamicSelectBox = document.getElementById('dynamicSelectBox');
      dynamicSelectBox.innerHTML = `<option value=""selected="true">--Select--</option>
			<option value="Books Related - Wrong Book Delivered">Books Related - Wrong Book Delivered</option>
			<option value="Books Related - Missing Quantity Book Delivered">Books Related - Missing Quantity Book Delivered</option>
			<option value="Books Related - Defective Book Delivered">Books Related - Defective Book Delivered</option>
			<option value="Books Related - Content Missing ">Books Related - Content Missing </option>
			<option value="Books Related - Wrong Content">Books Related - Wrong Content</option>
			<option value="Books Related - Wrong Medium (Hindi/ english )Delivered">Books Related - Wrong Medium (Hindi/ english )Delivered</option>
			<option value="Books Related - Page missing">Books Related - Page missing</option>
			<option value="Books Related - QR code not scanning">Books Related - QR code not scanning</option>
			<option value="Order Issue - City/ PIN not available ">Order Issue - City/ PIN not available </option>
			<option value="Order Issue - Coupon not applicable">Order Issue - Coupon not applicable</option>
			<option value="Order Issue - Payment deducted but order not reflected in Apps">Order Issue - Payment deducted but order not reflected in Apps</option>
			<option value="Order Issue - Book not available in stock">Order Issue - Book not available in stock</option>
			<option value="Order Issue - Book not listed ">Order Issue - Book not listed </option>
			<option value="Order Issue - Book Returned and Reshipped">Order Issue - Book Returned and Reshipped</option>
			<option value="Delivery Issue - Pickup fail">Delivery Issue - Pickup fail</option>
			<option value="Delivery Issue - OFD Lock">Delivery Issue - OFD Lock</option>
			<option value="Delivery Issue - RTO Lock">Delivery Issue - RTO Lock</option>
			<option value="Delivery Issue - OTP issue at delivery time">Delivery Issue - OTP issue at delivery time</option>
			<option value="Delivery Issue - Courier Boy refused to deliver at door step">Delivery Issue - Courier Boy refused to deliver at door step</option>
			<option value="Delivery Issue - Courier Boy asked for extra Money">Delivery Issue - Courier Boy asked for extra Money</option>
			<option value="Delivery Issue - India Post Tracking ID not activated">Delivery Issue - India Post Tracking ID not activated</option>
			<option value="Delivery Issue - India Post Delivery status not found">Delivery Issue - India Post Delivery status not found</option>
			<option value="Refund Issue - Order Cancel by student but payment not recieved">Refund Issue - Order Cancel by student but payment not recieved</option>
			<option value="Refund Issue - Order Cancel by Team but payment not recieved">Refund Issue - Order Cancel by Team but payment not recieved</option>
			<option value="Refund Issue - Prime Order Cancel and Payment refund issue">Refund Issue - Prime Order Cancel and Payment refund issue</option>
			<option value="Refund Issue - FBT Order Cancel and Payment refund issue">Refund Issue - FBT Order Cancel and Payment refund issue</option>
			<option value="Refund Issue - Refund process but student not recieved the payment">Refund Issue - Refund process but student not recieved the payment</option>
			<option value="Refund Issue - Book Returned by Student and Courier Payment not given to Student">Refund Issue - Book Returned by Student and Courier Payment not given to Student</option>
			<option value="Book status">Book status</option>
			<option value="Book exchange/Wrong item received">Book exchange/Wrong item received</option>
			<option value="Tech Issue ( Order failed, not reflected )">Tech Issue ( Order failed, not reflected )</option>
			<option value="New book purchase-related inquiry">New book purchase-related inquiry</option>
			<option value="Payment related inquiry">Payment related inquiry</option>
			<option value="Prime Order related">Prime Order related</option>
			<option value="Book Return ( Prime )">Book Return ( Prime )</option>
			<option value="Transfer Call">Transfer Call</option>
			<option value="Syllabus Change Related">Syllabus Change Related</option>`;
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
			<option value="Transfer Call">Transfer Call</option>`
		  $(".select_category_name").prop("required",false);
		}else if(value=='Online Course'){
	    const dynamicSelectBox = document.getElementById('dynamicSelectBox');
      dynamicSelectBox.innerHTML = `<option value=""selected="true">--Select--</option>
			<option value="Admit Card Related">Admit Card Related</option>
			<option value="Center Change Related">Center Change Related</option>
			<option value="Center Location Related">Center Location Related</option>
			<option value="Offline to Online Convert Related">Offline to Online Convert Related</option>
			<option value="Registration Confirmation Related">Registration Confirmation Related</option>
			<option value="Test Guruji Book not Show in App">Test Guruji Book not Show in App</option>
			<option value="Test Video Solution Related">Test Video Solution Related</option>
			<option value="Time & Schudule Related">Time & Schudule Related</option>
			<option value="Transfer Call">Transfer Call</option>`
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
	
	

	// console.log(queryString);
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
	   data['field_program'] = $(".select_course_name").val()[0];
	   //data['field_product_code_id_dropdown'] =$(".select_course_name").val();
	   
	   data['field_inquiry_lead_score'] =$("select[name='lead_score']").val();
	   data['field_inquiry_source'] =$("select[name='lead_source']").val();
	   data['field_description_remark'] =$("textarea[name='description']").val();
	   data['field_user_activity_status'] ='HRM Form Created';
	   data['field_user_activity_date'] =new Date();
	   data['field_course_version'] =$(".field_course_version").val();
	   
	   
	   // console.log($('.select_course_name option:selected', this).data('id'));
	   // console.log($('.select_course_name option:selected', this).data('price'));
	   
	   data['cf_program_code'] = $('.select_course_name option:selected', this).data('id');
	   data['cf_amount_total'] = $('.select_course_name option:selected', this).data('price');
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
});

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


$(document).on("change", ".C_Type", function () {
    var c_type = $(this).val();
    $('.StuSupport').hide();
    if (c_type !== '') {
        $('.StuSupport').show();
    }
});



$(document).on("change",".StudentSupport",function(){
	var ctype_name = $(this).val();

	var ctype_id = $('option:selected', this).data('id');
	
	$('.user_type').hide();
	if(ctype_id==1){
		$('.user_type').show();
	}
		
	var _this = $(this);
	if(ctype_name){
		$("#overlay_loader").css('display','block');
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},      
			type: "POST",
			url : '{{ route('admin.crm-desk.get_course_type') }}',
			data : {'ctype_name': ctype_name,'ctype_id':ctype_id},
			dataType : 'json',			
			success : function(data){
				$("#overlay_loader").css('display','none');
				if(data.status == false){
					
				}else if(data.status == true){		
					$('.StuCategory').show();
					$(".StudentCategory").html(data.html);
				}
			}
		});   
	}
});


</script>
@endsection
