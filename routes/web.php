<?php

use Illuminate\Support\Facades\Route;
// use Illuminate\Support\Facades\Mail;
// use App\Mail\TestMail;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

date_default_timezone_set('Asia/Kolkata');

// Route::get('/send-mail', function () {
//     Mail::to('sumitkhowal1003@gmail.com')->send(new AccessRequestMail());
//     return 'Mail sent successfully!';
// });

Route::get('logout', 'Controller@userLogout')->name('logout');
Route::get('download-invoice/{slug}', 'DownloadInvoiceController@index')->name('download-invoice');
Route::get('download-invoice-credit/{slug}', 'DownloadInvoiceController@credit')->name('download-invoice-credit');
Route::get('/invoice-report-pdf/{id}', 'Admin\InvoiceController@download_pdf')->name('invoice-report-pdf');
Route::get('/invoice-report-pdf-credit/{id}', 'Admin\InvoiceController@credit_download_pdf')->name('invoice-report-pdf-credit');

/* Start Links Routs */
Route::get('/all-reports', 'Admin\AllReportsController@index')->name('all-reports'); // WebView Report List

Route::get('/all-reports/cleanliness-report', 'Admin\AllReportsController@cleanlinessReport')->name('cleanliness-report'); // webview cleanliness report sumit
Route::post('/all-reports/cleanliness-report-submit','Admin\AllReportsController@cleanlinessStore')->name('cleanliness-report.submit');
Route::get('/all-reports/view-cleanliness-report', 'Admin\AllReportsController@cleanlinessReportView')->name('view-cleanliness-report'); // view in hrm cleanliness report sumit
Route::get('/all-reports/app-view-cleanliness-report/{user_id}', 'Admin\AllReportsController@cleanlinessReportViewApp')->name('app-view-cleanliness-report'); // view in hrm cleanliness report sumit
Route::post('/all-reports/update-cleanliness-status/{id}', 'Admin\AllReportsController@cleanlinessReportViewAppUpdate')->name('update-cleanliness-status'); // view in hrm cleanliness report sumit

Route::get('/all-reports/complaint-cleanliness', 'Admin\AllReportsController@complaintCleanliness')->name('complaint-cleanliness'); // complaint cleanliness report
Route::post('/all-reports/complaint-cleanliness-submit', 'Admin\AllReportsController@complaintCleanlinessStore')->name('complaint-cleanliness.submit'); // complaint cleanliness report
Route::get('/all-reports/complaint-cleanliness-view/{user_id}', 'Admin\AllReportsController@complaintCleanlinessViewApp')->name('app-view-complaint'); // complaint cleanliness report
Route::get('/all-reports/complaint-cleanliness-report/{user_id}', 'Admin\AllReportsController@complaintCleanlinessReportApp')->name('app-view-complaint-report'); // complaint cleanliness report
Route::post('/all-reports/complaint-report-update/{id}', 'Admin\AllReportsController@complaintReportUpdateStatus')->name('complaint-report-update'); // complaint cleanliness report
Route::get('/all-reports/view-complaint', 'Admin\AllReportsController@complaintWebView')->name('complaint-view');


Route::get('/all-reports/faculty-invoice-add', 'Admin\AllReportsController@facultyinvoiceadd')->name('faculty-invoice-add');
Route::post('/all-reports/faculty-invoice-save', 'Admin\AllReportsController@facultyinvoicesave')->name('faculty-invoice-save');
Route::get('/all-reports/faculty-invoice-list/{emp_id}', 'Admin\AllReportsController@facultyinvoicelist')->name('faculty-invoice-list');

//Course planner New
Route::get('/faculty-planner-verification', 'Admin\AllReportsController@faculty_planner_verification')->name('faculty-planner-verification');
Route::get('/faculty-assign-time', 'Admin\AllReportsController@faculty_assign_time')->name('faculty-assign-time');
Route::post('faculty-planner', 'Admin\AllReportsController@faculty_planner')->name('faculty-planner');
Route::post('faculty-add-time', 'Admin\AllReportsController@faculty_add_time')->name('faculty-add-time');
Route::post('faculty-add-remark', 'Admin\AllReportsController@faculty_add_remark')->name('faculty-add-remark');
//Course planner New END

Route::get('/chanakya', 'Admin\AllReportsController@chanakya_reports')->name('chanakya');
Route::get('/faculty-hours-reports', 'Admin\FacultyHoursReportsController@list_reports')->name('faculty-hours-reports'); // WebView Report List
Route::get('/faculty-hours-reports-new', 'Admin\FacultyHoursReportsController@index')->name('faculty-hours-reports-new');

Route::get('/faculty-reports', 'Admin\FacultyReportsController@index')->name('faculty-reports'); //dk
Route::get('/faculty-reports-two', 'Admin\FacultyReportsController@index_two')->name('faculty-reports-two'); //chetan
Route::get('/faculty-reports/delay-reason/{id}', 'Admin\FacultyReportsController@delayReason')->name('faculty-reports.delay-reason');

Route::get('/planerDetail/{tt_id?}', 'Admin\FacultyReportsController@plannerDetails')->name('planerDetail'); //pc
Route::post('/scheduleNextTopic',    'Admin\FacultyReportsController@scheduleNextTopic')->name('scheduleNextTopic'); //pc
Route::post('raise_issue_on_topic', 'Admin\FacultyReportsController@raise_issue_on_topic')->name('raise_issue_on_topic');//pc
Route::post('get_topic_class', 'Admin\FacultyReportsController@get_topic_class')->name('get_topic_class');//pc


Route::get('admin/course-planner/batchSubjectReports/{batch_id?}', 'Admin\CoursePlannerController@batchSubjectReports')->name('admin.course-planner.batchSubjectReports');
Route::get('npf-callback', 'Admin\PaymentController@npfCallBack')->name('payment.npf-callback');


Route::get('/nps-reports', 'Admin\AllReportsController@nps_reports')->name('nps-reports');
Route::get('/nps-reports-new', 'Admin\AllReportsController@nps_reports_new')->name('nps-reports-new');
Route::get('/nps-reports-five', 'Admin\AllReportsController@nps_reports_five')->name('nps-reports-five');
Route::get('/nps-reports-six', 'Admin\AllReportsController@nps_reports_six')->name('nps-reports-six');
Route::get('/nps-reports-seven', 'Admin\AllReportsController@nps_reports_seven')->name('nps-reports-seven');

Route::get('/online-nps-reports', 'Admin\AllReportsController@online_nps_reports')->name('online-nps-reports');


Route::get('/faculty-leave-report', 'Admin\AllReportsController@faculty_leave_report')->name('faculty-leave-report');

//Employee Complaint Web View & CEO Reply
Route::get('/employee-complaint', 'Admin\AllReportsController@employee_complaint')->name('employee-complaint'); 
Route::post('/employee-complaint-store', 'Admin\AllReportsController@employee_complaint_store')->name('employee-complaint-store');
Route::get('/acge/{isFaculty?}', 'Admin\AllReportsController@ceo_complaint_view')->name('acge'); 
Route::get('/ceo-sss', 'Admin\SupportCategoryController@ceoSSS')->name('ceoSSS'); 
Route::post('/complaint-read', 'Admin\AllReportsController@complaint_read')->name('complaint-read');
Route::get('/employee-complaint-history/{id?}', 'Admin\AllReportsController@complaint_history')->name('employee-complaint-history');
Route::post('/employee-complaint-reply', 'Admin\AllReportsController@employee_complaint_reply')->name('employee-complaint-reply');
Route::get('/employee-complaint-delete/{id?}', 'Admin\AllReportsController@employee_complaint_delete')->name('employee-complaint-delete');

///
Route::get('admin/anupriti-attendence', 'Admin\Anupriti@anupritiAttendence')->name('admin.std-attendence-anupriti');
Route::post('admin/anupriti-attendence-detail', 'Admin\Anupriti@anupritiAttendenceDetail')->name('admin.std-anupriti-detail');
Route::post('admin/anupriti-attendence-export', 'Admin\Anupriti@anupritiAttendenceExport')->name('admin.std-anupriti-export');

//End

// App Enquiry Web View
Route::get('/employee-enquiry', 'Admin\AppenquiryController@employee_enquiry')->name('employee-enquiry'); 
Route::post('/employee-enquiry-store', 'Admin\AppenquiryController@employee_enquiry_store')->name('employee-enquiry-store');
Route::get('/employee-enquiry-history/{id?}', 'Admin\AppenquiryController@enquiry_history')->name('employee-enquiry-history');



Route::get('/studio-reports', 'Admin\StudioReportsController@index')->name('studio-reports'); //dk
Route::get('/studio-reports-assistant', 'Admin\StudioReportsController@assistant_report')->name('studio-reports-assistant'); //dk
Route::get('/faculty-reports-driver', 'Admin\FacultyReportsController@driver_timetable')->name('faculty-reports-driver'); //dk

Route::post('/get-branchwise-studio', 'Admin\BranchController@get_branchwise_studio')->name('get-branchwise-studio'); //dk
Route::post('/get-branchwise-assistant', 'Admin\StudioController@get_branchwise_assistant')->name('get-branchwise-assistant');

Route::get('/batch-reports', 'Admin\BatchReportsController@index')->name('batch-reports');
Route::post('/get-location-wise-branch', 'Admin\StudioReportsController@get_locationwise_branch')->name('get-location-wise-branch');
Route::get('/batch-report-pdf', 'Admin\BatchReportsController@download_pdf')->name('batch-report-pdf');

Route::get('/batch-report-report-excel', 'Admin\BatchReportsController@download_excel')->name('batch-report-report-excel');
Route::get('/studio-report-report-excel', 'Admin\StudioReportsController@download_excel')->name('studio-report-report-excel');

Route::get('/knowledge-based/{emp_id}/{id?}', 'KnowledgeBasedController@add_knowledge_based')->name('knowledge-based');
Route::post('/store-knowledge-based/{emp_id}/{id?}', 'KnowledgeBasedController@storeKnowledgeBased')->name('store-knowledge-based');

Route::get('/mobile-detail', 'MaterialRequisitionController@index')->name('mobile-detail');
Route::post('/material-send-otp', 'MaterialRequisitionController@materialSendOtp')->name('material-send-otp');
Route::post('/material-access-otp', 'MaterialRequisitionController@materialAccessOtp')->name('material-access-otp');
Route::get('/employee-details/{emp_id?}/{type?}', 'MaterialRequisitionController@employeeDetails')->name('employee-details');
Route::post('/store-material-requisition/{emp_id?}/{type?}', 'MaterialRequisitionController@storeMaterialRequisition')->name('store-material-requisition');
Route::get('/employee-id', 'Admin\EmployeeController@employeeId')->name('employee-id');
Route::post('/get-sub-cat', 'MaterialRequisitionController@getSubCat')->name('get-sub-cat');

Route::get('/employee-requisition-list/{emp_id?}/{type?}', 'MaterialRequisitionController@employee_requisition')->name('employee-requisition-list');
Route::get('/po-approval-list/{emp_id?}/{type?}', 'MaterialRequisitionController@po_approval_list')->name('po-approval-list');
Route::post('web-view-product-accept', 'MaterialRequisitionController@web_view_product_accept')->name('web-view-product-accept');
Route::post('web-view-po', 'MaterialRequisitionController@web_view_po')->name('web-view-po');
Route::post('web-view-po', 'MaterialRequisitionController@po_wo_status_update')->name('po-wo-status');
Route::get('web-view-poprint/{id}/{emp_id?}', 'MaterialRequisitionController@web_view_poprint')->name('web-view-poprint');
Route::post('web-view-po-status-update', 'MaterialRequisitionController@web_view_po_status_update')->name('web-view-po-status-update'); 

Route::post('web-view-product-return', 'MaterialRequisitionController@web_view_product_return')->name('request.web-view-product-return');


Route::get('/po-created-history/{buyer_id?}', 'MaterialRequisitionController@po_created_history')->name('po-created-history');

//Web View
Route::get('/admin/mobile-add-task/', 'Admin\GetTaskController@mobile_task_add')->name('mobile-add-task'); 
Route::get('/admin/mobile-view-task/', 'Admin\GetTaskController@mobile_view_task')->name('admin.mobile-view-task');
Route::get('/admin/mobile-view-task-history/{id?}/{logged?}', 'Admin\GetTaskController@mobile_view_task_history')->name('admin.mobile-view-task-history');	 //New 

Route::post('/admin/mobile-task-store', 'Admin\GetTaskController@mobile_task_store')->name('admin.mobile-task-store');	
Route::post('/admin/mobile-edit-task', 'Admin\GetTaskController@mobile_edit_task')->name('admin.mobile-edit-task');
Route::post('/admin/mobile-update-task', 'Admin\GetTaskController@mobile_update_task')->name('admin.mobile-update-task');
Route::get('/admin/mobile-task_history/{task_id}', 'Admin\GetTaskController@mobile_task_history')->name('admin.mobile-task-history');
Route::get('/admin/mobile-task-delete/{id}', 'Admin\GetTaskController@mobile_destroy')->name('admin.mobile-task-delete');

Route::post('/admin/mobile-update-spent-hour', 'Admin\GetTaskController@mobile_update_spent_task')->name('admin.mobile-update-spent-hour');

/* End Links Routs */ 

// Web view Test Report

Route::get('/test-report', 'Admin\TestReportController@test_report')->name('test-report');
Route::get('/test-report-view', 'Admin\TestReportController@test_report_view')->name('test-report-view');
Route::get('/test-report-update', 'Admin\TestReportController@test_report_update')->name('test-report-update');
Route::get('/test-report-download', 'Admin\TestReportController@test_report_download')->name('test-report-download');
Route::post('/test_report_save', 'Admin\TestReportController@test_report_save')->name('test_report_save');

Route::get('/mentor-report-batch-list', 'Admin\TestReportController@mentor_report_batch_list')->name('mentor-report-batch-list');
Route::get('/mentor-report-batch-detail', 'Admin\TestReportController@mentor_report_batch_detail')->name('mentor-report-batch-detail');
Route::get('/employee-leave-detail', 'Admin\TestReportController@employee_leave_detail')->name('employee-leave-detail');


//Faculty SME
Route::get('/faculty-sme-request-index', 'Admin\FacultySmeController@faculty_sme_request_index')->name('faculty-sme-request-index');
Route::get('/faculty-sme-request-create', 'Admin\FacultySmeController@faculty_sme_request_create')->name('faculty-sme-request-create');
Route::get('/faculty-sme-request-chat', 'Admin\FacultySmeController@faculty_sme_request_chat')->name('faculty-sme-request-chat');
Route::post('/faculty-sme-request-submit', 'Admin\FacultySmeController@faculty_sme_request_submit')->name('faculty-sme-request-submit');
Route::post('/faculty-sme-request-chat-submit', 'Admin\FacultySmeController@faculty_sme_request_chat_submit')->name('faculty-sme-request-chat-submit');

/*Admin Routes*/
Route::middleware(['auth', 'admin'])->group(function () {
	Route::prefix('admin')->group(function () {
		Route::name('admin.')->group(function () {
			Route::namespace ('Admin')->group(function () {
				Route::get('/', 'AdminController@index')->name('index');
				Route::get('/dashboard/{id?}', 'AdminController@index')->name('dashboard');
				Route::get('/employee', 'EmployeeController@index')->name('employee');
				Route::resources([					
					'/roles' => 'RolesController',
					'/branch' => 'BranchController',
					'/employees' => 'EmployeeController',
					'/drivers' => 'DriverController',
					'/studios' => 'StudioController',
					'/timetable' => 'TimeTableController',
					'/subjects' => 'SubjectController',
					'/chapters' => 'ChapterController',
					'/topics' => 'TopicController',
					'/course' => 'CourseController',
					'/batch' => 'BatchController',
					'/classchangerequest' => 'ClassChangeRequestController',
					'/reports' => 'ReportController',
					'/notification' => 'NotificationController',
					'/task' => 'TaskController',
					'/attendance' => 'AttendanceController',
					'/leave' => 'LeaveController',
					'/invoice' => 'InvoiceController',
					'/invoice' => 'InvoiceController',
					'/staff' => 'StaffmovementsystemController',
					'/newtask' => 'NewTaskController',
					'/department' => 'DepartmentController',
					'/designation' => 'DesignationController',
					'/document' => 'EmployeeDocumentController',
					'/salary' => 'SalaryController',
					'/category' => 'CategoryController',
					'/buyer' => 'BuyerController',
					'/product' => 'ProductController',
					'/inventory' => 'InventoryController', 
					'/asset' => 'AssetController',
					'/asset_pro' => 'AssetProController',
					'/timetables' => 'TimetablesController',
					'/holiday' => 'HolidayController',
					'/dppsystem' => 'DppSystemController',
					'/location' => 'LocationController',
					'/knowledge_based' => 'KnowledgeBasedController',
					'/knowledge_based_category' => 'KnowledgeBasedCategoryController',
					'/training_video' => 'TrainingVideoController',
					'/training_video_category' => 'TrainingVideoCategoryController',
					'/meeting' => 'MeetingController',
					'/sub_department' => 'SubDepartmentController',
					'/expense' => 'ExpenseController',
					'/expense_category' => 'ExpenseCategoryController',
					'/material-requisition' => 'MaterialRequisitionFormController',
					'/request' => 'AssetRequestController',
					'/appraisal' => 'AppraisalController',
					'/support_category' => 'SupportCategoryController',
					'/support_user' => 'SupportUserController',
					'/enquiry' => 'EnquiryController',
					'/training_pdf' => 'TrainingPdfController',
					'/onlinecourses' => 'OnlineCoursesController',
					'/course_category' => 'CourseCategoryController',
					'/batch_holiday' => 'BatchHolidayController',
					'/freelancer' => 'FreelancerController',
					'/discountApprovel' => 'discountApprovelCtr',
					'/payment' => 'PaymentController',
					'/pincode' => 'PincodeController',
					'/discount-role-wise' => 'DiscountRoleWiseController',
					'/faculty-sme' => 'FacultySmeController',
					'/newcoupon' => 'NewCouponController',
					
				]);
				Route::post('addcoupon', 'NewCouponController@addcoupon')->name('coupon.addcoupon');
				Route::post('addRemark', 'NewCouponController@addRemark')->name('coupon.addRemark');
				Route::get('coupon_history', 'NewCouponController@historyList')->name('newcoupon.historyList');
				Route::post('updateStatus', 'NewCouponController@updateStatus')->name('newcoupon.updateStatus');
				
				Route::get('/profile', 'AdminController@profile')->name('profile');
				Route::post('/profile/update', 'AdminController@profile_update')->name('profile.update');
				
				Route::post('fchange-status', 'FreelancerController@change_status')->name('freelancer.fchange-status');
				Route::post('freelancer-invoice', 'FreelancerController@freelancer_invoice')->name('freelancer.freelancer-invoice');
				Route::get('freelancer-invoice-download/{id?}', 'FreelancerController@freelancer_invoice_download')->name('freelancer.freelancer-invoice-download');
				
				Route::get('freelancer-invoice-history', 'FreelancerController@freelancer_invoice_history')->name('freelancer.freelancer-invoice-history');
				
				Route::get('faculty-invoice-history', 'FreelancerController@faculty_invoice')->name('freelancer.faculty-invoice-history');
				Route::post('invoice-stauts-update', 'FreelancerController@invoicestautsupdate')->name('freelancer.invoice-stauts-update');

				Route::post('import-pincode', 'PincodeController@import_pincode')->name('import-pincode');
				
				//Payment
				Route::post('npf-student-details', 'PaymentController@npf_student_details')->name('payment.npf-student-details');
				Route::post('npf-course-details', 'PaymentController@npf_course_details')->name('payment.npf-course-details');
				Route::post('npf-sendpaymentlink', 'PaymentController@sendpaymentlink')->name('payment.npf-sendpaymentlink');
				Route::post('apply-coupon', 'PaymentController@apply_coupon')->name('payment.apply_coupon');
				
				//SME
				Route::get('faculty-sme-chat', 'FacultySmeController@faculty_sme_chat')->name('faculty-sme.faculty-sme-chat');
				Route::get('faculty-sme-reuse', 'FacultySmeController@faculty_sme_reuse')->name('faculty-sme.faculty-sme-reuse');
				
				Route::post('reuse-request', 'FacultySmeController@reuse_request')->name('faculty-sme.reuse-request');
				
				Route::post('faculty-sme-chat-submit', 'FacultySmeController@faculty_sme_chat_submit')->name('faculty-sme-chat-submit');
				Route::get('faculty-sme-uploadfile', 'FacultySmeController@faculty_sme_uploadfile')->name('faculty-sme-uploadfile');
				Route::post('faculty-sme-uploadfile-submit', 'FacultySmeController@faculty_sme_uploadfile_submit')->name('faculty-sme-uploadfile-submit');
				Route::post('get_cat_exam', 'FacultySmeController@get_cat_exam')->name('faculty-sme.get_cat_exam');
				Route::post('get_exam_subject', 'FacultySmeController@get_exam_subject')->name('faculty-sme.get_exam_subject');
				Route::post('get_subject_chapter', 'FacultySmeController@get_subject_chapter')->name('faculty-sme.get_subject_chapter');
				Route::get('faculty-sme-request-pick', 'FacultySmeController@faculty_sme_request_pick')->name('faculty-sme.faculty-sme-request-pick');
				Route::post('faculty-sme-update-stauts', 'FacultySmeController@faculty_sme_update_stauts')->name('faculty-sme.faculty-sme-update-stauts');
				
				Route::get('faculty-sme-assistant', 'FacultySmeController@faculty_sme_assistant')->name('faculty-sme.faculty-sme-assistant');
				Route::post('faculty-sme-download-pdf', 'FacultySmeController@faculty_sme_download_pdf')->name('faculty-sme.faculty-sme-download-pdf');
				
				Route::get('sme-visibility', 'FacultySmeController@sme_visibility')->name('faculty-sme.sme-visibility');
				//END
 
				Route::get('/password', 'AdminController@change_password')->name('password');
				Route::post('/password/update', 'AdminController@update_password')->name('password.update');

				Route::get('/role/delete/{id}', 'RolesController@destroy')->name('role.delete');
				Route::get('/employee/delete/{id}', 'EmployeeController@destroy')->name('employee.delete');
				
				Route::get('/studio/delete/{id}', 'StudioController@destroy')->name('studio.delete');
				Route::get('/notifications/delete/{id}', 'NotificationController@destroy')->name('notifications.delete');
				Route::get('/notifications/list_all', 'NotificationController@list_all')->name('notifications.list_all');

				Route::get('/course/delete/{id}', 'CourseController@destroy')->name('course.delete');
				Route::get('/subject/delete/{id}', 'SubjectController@destroy')->name('subject.delete');
				Route::get('/branch/delete/{id}', 'BranchController@destroy')->name('branch.delete');
				Route::get('/batch/delete/{id}', 'BatchController@destroy')->name('batch.delete');
				Route::get('/chapter/delete/{id}', 'ChapterController@destroy')->name('chapter.delete');
				Route::get('/topic/delete/{id}', 'TopicController@destroy')->name('topic.delete');

				Route::get('/courses/import', 'CourseController@import')->name('courses.import');
				Route::get('/courses/planner_view/{planner_id}/{id}', 'CourseController@planner_view')->name('courses.planner_view');
 
				Route::post('/courses/import/store', 'CourseController@import_store')->name('courses.import.store');
				
				Route::post('/invoice/store', 'InvoiceController@import_store')->name('invoice.store');
				Route::get('/invoice-detail', 'InvoiceController@invoic_detail')->name('invoice.invoice-detail');

				Route::get('/employee/view/{id}', 'EmployeeController@show')->name('employees.view');
				Route::get('/employee/status/{id}', 'EmployeeController@togglePublish')->name('employee.status');

				Route::post('/ckeditor/upload', 'NewsController@uploadckeditorImage')->name('ckeditor.upload');

				Route::post('/get-batch-subject', 'BatchController@get_batch_subject')->name('get-batch-subject');

				Route::post('/get-batch', 'TimeTableController@get_batch')->name('get-batch');

				Route::post('/get-course', 'TimeTableController@get_course')->name('get-course');

				Route::post('/get-class-batch-subject', 'TimeTableController@get_batch_subject')->name('get-class-batch-subject');

				Route::post('/get-remark', 'TimeTableController@get_remark')->name('get-remark');

				Route::post('/get-chapter', 'TimeTableController@get_chapter')->name('get-chapter');

				Route::post('/get-topic', 'TimeTableController@get_topic')->name('get-topic');
				
				Route::post('/get-batch-by-subject', 'TimeTableController@get_batch_by_subject')->name('get-batch-by-subject');
				
				Route::post('/get-topic-by-chapter', 'TimeTableController@get_topic_by_chapter')->name('get-topic-by-chapter');

				Route::post('/get-swap-faculty', 'TimeTableController@get_swap_faculty')->name('get-swap-faculty');

				Route::post('/get-swap-faculty-timetable', 'TimeTableController@get_swap_faculty_timetable')->name('get-swap-faculty-timetable');

				Route::get('/timetables/export', 'TimeTableController@timetable_export')->name('timetables.export');

				Route::post('/export/data', 'TimeTableController@export_data')->name('export.data');

				Route::post('/getassistantexits', 'StudioController@getassistantexits')->name('getassistantexits');

				Route::post('/get-branchwise-assistant', 'StudioController@get_branchwise_assistant')->name('get-branchwise-assistant');

				Route::get('/reschedule/edit/{id}', 'ClassChangeRequestController@edit_reschedule')->name('reschedule.edit');
				Route::patch('/reschedule/update/{id}', 'ClassChangeRequestController@update_reschedule')->name('reschedule.update');

				Route::get('/swap/edit/{id}', 'ClassChangeRequestController@edit_swap')->name('swap.edit');

				Route::patch('/swap/update/{id}', 'ClassChangeRequestController@update_swap')->name('swap.update');

				Route::get('/cancelclass/edit/{id}', 'ClassChangeRequestController@edit_cancelclass')->name('cancelclass.edit');

				Route::patch('/cancelclass/update/{id}', 'ClassChangeRequestController@update_cancelclass')->name('cancelclass.update');

				Route::post('/reschedule/store', 'TimeTableController@reschedule_store')->name('reschedule.store');
				Route::post('/cancelclass/store', 'TimeTableController@cancelclass_store')->name('cancelclass.store');
				Route::post('/swap/store', 'TimeTableController@swap_store')->name('swap.store');

				Route::post('/subject/report', 'ReportController@subject_report')->name('subject.report');
				Route::post('/report/export', 'ReportController@report_export')->name('report.export');
				
				Route::post('/branch/upload-image', 'BranchController@upload_image')->name('branch.upload-image'); //dk
				
				//DK
				Route::get('/roles/status/{id}', 'RolesController@togglePublish')->name('roles.status'); //dk
				Route::get('/branch/status/{id}', 'BranchController@togglePublish')->name('branch.status'); //dk
				Route::get('/studios/status/{id}', 'StudioController@togglePublish')->name('studios.status'); //dk
				Route::get('/course/status/{id}', 'CourseController@togglePublish')->name('course.status'); //dk
				Route::get('/subject/status/{id}', 'SubjectController@togglePublish')->name('subject.status'); //dk
				Route::get('/chapter/status/{id}', 'ChapterController@togglePublish')->name('chapter.status'); //dk
				Route::get('/topics/status/{id}', 'TopicController@togglePublish')->name('topics.status'); //dk
				
				Route::get('/reschedule/phpinfo', 'ClassChangeRequestController@phpinfo')->name('reschedule.phpinfo');//dk
				Route::get('/courses/download_sample', 'CourseController@download_sample')->name('courses.download_sample');// dk
				Route::get('/course/view/{id}', 'CourseController@view')->name('course.view');//dk
				Route::get('/course/export_csv/{id}', 'CourseController@export_csv')->name('course.export_csv');// dk
				Route::get('/employee/approval/{id}', 'EmployeeController@approval')->name('employees.approval');//dk
				Route::post('/employee/approval_update/{id}', 'EmployeeController@approval_update')->name('employees.approval_update');//dk
				Route::get('/studio-reports', 'StudioReportsController@index')->name('studio-reports'); //dk
				Route::get('/studio-report-pdf', 'StudioReportsController@download_pdf')->name('studio-report-pdf'); //dk
				Route::post('/studio-reports/changeMark', 'StudioReportsController@changeMark')->name('studio-reports.changeMark'); //dk

				Route::get('/task/view/{id}', 'TaskController@view')->name('task.view');//dk
				Route::get('/task/task_detail_delete/{task_id}/{task_detail_id}', 'TaskController@task_detail_delete')->name('task.task_detail_delete');//dk
				Route::get('/task/task_delete/{task_id}', 'TaskController@task_delete')->name('task.task_delete');//dk
				Route::get('/task/edit/{task_id}', 'TaskController@edit')->name('task.edit');//dk
				Route::patch('/task/update/{task_id}', 'TaskController@update')->name('task.update');//dk
				Route::get('/task/task_history/{task_id}/{task_detail_id}', 'TaskController@task_history')->name('task.task_history');//dk
				Route::get('/task/create', 'TaskController@create')->name('task.create');//dk
				Route::post('/get-branchwise-employee', 'TaskController@get_branchwise_employee')->name('get-branchwise-employee'); //dk
				Route::post('/get-batch-subject-data', 'BatchController@get_batch_subject_data')->name('get-batch-subject-data');//dk
				Route::get('/batch/view/{id?}', 'BatchController@view')->name('batch.view');//dk
				Route::post('/get-planner', 'BatchController@get_planner')->name('get-planner');
				
				Route::post('/get-branchwise-studio', 'BranchController@get_branchwise_studio')->name('get-branchwise-studio'); //dk
				Route::post('/get-branchwise-faculty', 'BranchController@get_branchwise_faculty')->name('get-branchwise-faculty'); //dk
				Route::get('/faculty-reports', 'FacultyReportsController@index')->name('faculty-reports'); //dk
				Route::get('/timetable-history-reports', 'FacultyReportsController@timetable_history_reports')->name('timetable-history-reports'); //dk
				Route::get('/faculty-report-pdf', 'FacultyReportsController@download_pdf')->name('faculty-report-pdf'); //dk
				Route::get('/attendance/create', 'AttendanceController@create')->name('attendance.create');//dk
				Route::get('/attendance/edit/{emp_id}/{date}', 'AttendanceController@edit')->name('attendance.edit');//dk
				Route::patch('/attendance/update', 'AttendanceController@update')->name('attendance.update');//dk
				Route::get('/attendance/gallery', 'AttendanceController@gallery')->name('attendance.gallery');//dk
				Route::get('/leave/view/{leave_id}/{leave_detail_id}', 'LeaveController@view')->name('leave.view');//dk
				Route::post('/leave/approval/{id}', 'LeaveController@approval')->name('leave.approval');//dk
				Route::get('/subject-reports', 'SubjectReportsController@index')->name('subject-reports'); //dk
				Route::get('/subject-report-excel', 'SubjectReportsController@download_excel')->name('subject-report-excel'); //dk
				Route::get('/leave-report-excel', 'LeaveController@download_excel')->name('leave-report-excel');//dk
				Route::get('/task-report-excel', 'TaskController@download_excel')->name('task-report-excel');//dk
				Route::get('/attendance-report-excel', 'AttendanceController@download_excel')->name('attendance-report-excel');//dk
				Route::post('/get-faculty', 'TimeTableController@get_faculty')->name('get-faculty'); //dk
				Route::get('/free-faculty-reports', 'FreeFacultyReportsController@index')->name('free-faculty-reports'); //ak

				Route::get('/free-faculty-report-excel', 'FreeFacultyReportsController@download_excel')->name('free-faculty-report-excel'); //ak

				Route::get('/free-assistant-reports', 'FreeAssistantReportsController@index')->name('free-assistant-reports'); //ak
				Route::get('/free-assistant-report-excel', 'FreeAssistantReportsController@download_excel')->name('free-assistant-report-excel'); //ak
				Route::get('/typist-work-report', 'TypistReportsController@index')->name('typist-work-report'); //ak
				Route::get('/typist-work-report-excel', 'TypistReportsController@download_excel')->name('typist-work-report-excel'); //ak
				
				Route::get('/it-deo-work-report', 'TypistReportsController@it_deo_work')->name('it-deo-work-report'); 
				Route::get('/add-it-deo-work-report', 'TypistReportsController@add_it_deo_work_report')->name('add-it-deo-work-report'); 
				Route::post('save-it-deo-work', 'TypistReportsController@save_it_deo_work')->name('save-it-deo-work'); 
				
				Route::post('/invoice/store', 'InvoiceController@import_store')->name('invoice.store');
				Route::get('/invoice-report-pdf/{id}', 'InvoiceController@download_pdf')->name('invoice-report-pdf'); 
				Route::post('/invoice-multi-download', 'InvoiceController@multi_download_pdf')->name('invoice-multi-download'); 
				Route::post('/store-message', 'InvoiceController@store_message')->name('store-message');
				
				Route::post('/get-data-by-topic', 'TimeTableController@get_data_by_topic')->name('get-data-by-topic');
				
				Route::post('/get-chapter-and-topic', 'TimeTableController@get_chapter_and_topic')->name('get-chapter-and-topic'); //dk
				
				Route::post('/timetable/delete_class', 'TimeTableController@delete_class')->name('timetable.delete_class'); //dk
				
				Route::get('/newtask/task_history/{task_id}', 'NewTaskController@task_history')->name('newtask.task_history');
				Route::get('/newtask/task_delete/{task_id}', 'NewTaskController@task_delete')->name('newtask.task_delete');
				Route::get('/newtask/view/{id}', 'NewTaskController@view')->name('newtask.view');
				Route::get('/newtask/edit/{user_id}/{date}', 'NewTaskController@edit')->name('newtask.edit');
				Route::get('/open-task', 'NewTaskController@open_task')->name('newtask.open-task');
				
				Route::post('/invoice/credit-store', 'InvoiceController@import_credit_store')->name('invoice.credit-store');
				Route::get('/credit-note', 'InvoiceController@credit_note')->name('invoice.credit-note');
				Route::post('/store-credit-message', 'InvoiceController@store_credit_message')->name('store-credit-message');
				Route::get('/credit-report-pdf/{id}', 'InvoiceController@credit_download_pdf')->name('credit-report-pdf'); 
				Route::post('/credit-multi-download', 'InvoiceController@credit_multi_download_pdf')->name('credit-multi-download'); 
				
				Route::get('/department/status/{id}', 'DepartmentController@togglePublish')->name('department.status');
				Route::get('/department/delete/{id}', 'DepartmentController@destroy')->name('department.delete');
				Route::get('/designation/status/{id}', 'DesignationController@togglePublish')->name('designation.status');
				Route::get('/designation/delete/{id}', 'DesignationController@destroy')->name('designation.delete');
				
				Route::get('/attendance/users/absent', 'AttendanceController@absentuser')->name('attendance.absentuser');//dk
				Route::get('/absentuser-download-report-excel', 'AttendanceController@absentuser_download_excel')->name('absentuser-download-report-excel');//dk
				Route::get('/faculty-reports/subjects', 'FacultyReportsController@subjects')->name('faculty-reports.subjects'); //dk
				
				Route::post('/get-studio-edit', 'TimeTableController@get_studio_edit')->name('get-studio-edit');
				
				Route::post('/edit-online-store', 'TimeTableController@edit_online_store')->name('timetable.edit-online-store');
				
				Route::post('/get-open-task-detail', 'NewTaskController@get_open_task_detail')->name('newtask.get-open-task-detail');
				
				Route::get('/leave/edit/{id}', 'LeaveController@edit_leave')->name('leave.edit');
				Route::post('/leave/edit-leave-store', 'LeaveController@edit_leave_store')->name('leave.edit-leave-store');
				Route::get('/leave/delete/{id}', 'LeaveController@delete_leave')->name('leave.delete');
				
				Route::get('/employee-document-pdf/{id}/{type}', 'EmployeeDocumentController@download_document')->name('employee-document-pdf');
				Route::get('/newtask-report-excel', 'NewTaskController@download_excel')->name('newtask-report-excel');
				Route::get('/salary-report-excel', 'SalaryController@download_excel')->name('salary-report-excel');
				
				
				
				
				Route::post('/edit-category', 'CategoryController@edit_category')->name('edit-category');
				Route::get('/category/delete/{id}', 'CategoryController@destroy')->name('category.delete');
				Route::post('/sub-category-store', 'CategoryController@subCategoryStore')->name('sub-category-store');
				Route::get('/subcategory/{id}', 'CategoryController@subcategory')->name('subcategory');
				//Route::get('/category/sub-category-delete/{id}', 'CategoryController@subCategoryDestroy')->name('category.sub-category-delete');
				Route::post('/sub-category-edit-store', 'CategoryController@subCategoryEditStore')->name('sub-category-edit-store');
				Route::post('/edit-sub-category', 'CategoryController@editSubCategory')->name('edit-sub-category');
				
				Route::get('/buyer/delete/{id}', 'BuyerController@destroy')->name('buyer.delete');
				Route::get('/buyer/add-bill/{id}', 'BuyerController@addBill')->name('buyer.add-bill');
				Route::get('/buyer/bill/{id}', 'BuyerController@bill')->name('buyer.bill');
				Route::post('/buyer/bill-store', 'BuyerController@billStore')->name('buyer.bill-store');
				Route::get('/buyer/edit-bill/{id}/{pid?}', 'BuyerController@addBill')->name('buyer.edit-bill');
				Route::get('/buyer/delete-bills/{id}', 'BuyerController@deleteBill')->name('buyer.delete-bills');
				Route::get('buyer-report-excel', 'BuyerController@buyer_report_excel')->name('buyer-report-excel'); 
				Route::get('vendor-view/{id}', 'BuyerController@vendor_view')->name('buyer.vendor-view'); 
				Route::get('vendor-new-list', 'BuyerController@vendor_new_list')->name('buyer.vendor-new-list'); 
				Route::get('vendor-changes-history/{id}', 'BuyerController@vendor_changes_history')->name('buyer.vendor-changes-history'); 
				
				Route::post('/product/get-sub-cat', 'ProductController@getSubCat')->name('product.get-sub-cat');
				Route::post('/product/get-sub-product', 'ProductController@getSubProduct')->name('product.get-sub-product');
				
				Route::get('/product/delete/{id}', 'ProductController@destroy')->name('product.delete');
				Route::get('/product/transfer-product/{product_id}', 'ProductController@transferProduct')->name('product.transfer-product');
				Route::get('/product/add-transfer-product/{product_id}', 'ProductController@addTransferProduct')->name('product.add-transfer-product');
				Route::post('/product/transfer-product-store', 'ProductController@transferProductStore')->name('product.transfer-product-store');
				Route::get('/product/edit-transfer-product/{product_id}/{primary_id?}', 'ProductController@addTransferProduct')->name('product.edit-transfer-product');
				Route::get('/product/delete-transfer-product/{id}', 'ProductController@deleteTransferProduct')->name('product.delete-transfer-product');
				
				Route::get('warehouse-to-branch-report-excel', 'ProductController@warehouse_branch_report_excel')->name('warehouse-to-branch-report-excel'); //Chetan
				
				Route::post('/salary-access-otp', 'SalaryController@salary_access_otp')->name('salary-access-otp');
				Route::post('/salary-send-otp', 'SalaryController@salary_send_otp')->name('salary-send-otp');
				
				Route::post('/product/get-bill', 'ProductController@getBill')->name('product.get-bill');
				Route::get('product-history/{product_id?}', 'ProductController@product_history')->name('product.product-history'); 
				
				Route::get('rp-attendance', 'AttendanceController@rpattendance')->name('attendance.rpattendance');
				Route::get('rp-attendance-detail', 'AttendanceController@rpAttendanceDetail')->name('attendance.rp-attendance-detail');
				Route::get('/new-attendance-report-excel', 'AttendanceController@new_download_excel')->name('new-attendance-report-excel');
				
				Route::get('rp-not-present-attendance', 'AttendanceController@rpnotpresentattendance')->name('attendance.rpnotpresentattendance');
				Route::get('rp-not-present-attendance-detail', 'AttendanceController@rpNotPresentAttendanceDetail')->name('attendance.rp-not-present-attendance-detail');
				Route::get('new-not-attendance-report-excel', 'AttendanceController@new_not_attdence_download_excel')->name('new-not-attendance-report-excel');     
				
				Route::get('/employee-report-excel', 'EmployeeController@download_excel')->name('employee-report-excel');
				Route::get('/employee-probation-excel', 'EmployeeController@download_probation_excel')->name('employee-probation-excel');
				Route::get('/employee-birthday-excel', 'EmployeeController@download_birthday_excel')->name('employee-birthday-excel');
				Route::get('/employee-work-anniversary-excel', 'EmployeeController@download_work_anniversary_excel')->name('employee-work-anniversary-excel');
				Route::get('attendance-detail', 'AttendanceController@attendanceDetail')->name('attendance.attendance-detail');
				
				Route::get('/employee/late-employee-list', 'EmployeeController@late_emp_list')->name('employee.late-employee-list');
				
				Route::get('full-attendence', 'AttendanceController@fullattendence')->name('attendance.fullattendence');
				Route::get('full-attendence-detail', 'AttendanceController@fullAttendanceDetail')->name('attendance.full-attendence-detail');
				Route::get('full-attendance-report-excel', 'AttendanceController@full_download_excel')->name('full-attendance-report-excel');
				
				Route::get('full-attendence-new', 'AttendanceController@fullattendence_new')->name('attendance.fullattendence_new');
				Route::get('full-attendence-detail-new', 'AttendanceController@fullAttendanceDetail_new')->name('attendance.full-attendence-detail-new');
				
				Route::get('absent-full-attendence', 'AttendanceController@absentfullattendence')->name('attendance.absentfullattendence');
				Route::get('absent-full-attendence-detail', 'AttendanceController@absentFullAttendanceDetail')->name('attendance.absent-full-attendence-detail');
				Route::get('absent-full-attendance-report-excel', 'AttendanceController@absent_full_download_excel')->name('absent-full-attendance-report-excel');
				
				
				Route::get('branch-product-inventory', 'ProductController@branch_inventory')->name('branch-product-inventory');
				Route::get('product-inventory', 'ProductController@inventory')->name('product-inventory');
				Route::get('transfer-inventory/{product_id?}/{branch_id?}', 'ProductController@transferInventory')->name('transfer-inventory');
				Route::get('add-transfer-inventory/{product_id}', 'ProductController@addTransferInventory')->name('add-transfer-inventory');
				Route::post('transfer-inventory-store', 'ProductController@transferInventoryStore')->name('transfer-inventory-store');
				Route::get('edit-transfer-inventory/{product_id}/{primary_id?}', 'ProductController@addTransferInventory')->name('edit-transfer-inventory');
				Route::get('delete-transfer-inventory/{id}', 'ProductController@deleteTransferProduct')->name('delete-transfer-inventory');
				Route::get('transfer-inventory-history/{product_id}', 'ProductController@transferInventoryHistory')->name('transfer-inventory-history');
				Route::get('request-inventory', 'ProductController@requestInventory')->name('request-inventory');
				Route::get('transfer-inventory-update-status/{id}/{product_id?}/{qty?}/{status?}', 'ProductController@transferInventoryUpdateStatus')->name('transfer-inventory-update-status');
				Route::get('transfer-branch-inventory', 'ProductController@branchInventory')->name('admin.transfer-branch-inventory');
				Route::get('transfer-branch-inventory/{product_id?}/{branch_id?}', 'ProductController@transferBranchInventory')->name('transfer-branch-inventory');
				Route::post('transfer-branch-inventory-store', 'ProductController@transferBranchInventoryStore')->name('transfer-branch-inventory-store');
				Route::get('request-branch-inventory', 'ProductController@requestBranchInventory')->name('request-branch-inventory');
				
				Route::get('product-autocomplete', 'ProductController@show')->name('product-autocomplete');
				Route::get('branch-report-excel', 'ProductController@branch_report_excel')->name('branch-report-excel'); //Chetan
				
				Route::get('attendence-record', 'AttendanceRecordController@attendencerecord')->name('attendance.attendencerecord');
				Route::post('attendence-record-detail', 'AttendanceRecordController@attendencerecorddetail')->name('attendance.attendence-record-detail');
				Route::get('attendence-record-report-excel', 'AttendanceRecordController@attendencerecord_download_excel')->name('attendence-record-report-excel');
				
				Route::post('/employee/status-by-reason', 'EmployeeController@statusByReason')->name('employee.status-by-reason');
				
				Route::get('asset-autocomplete', 'AssetController@show')->name('asset-autocomplete');
				Route::get('asset-detail', 'AssetController@assetDetail')->name('asset.asset-detail');
				Route::post('assigned_asset_to_employee', 'AssetController@assignedAssetToEmployee')->name('assigned_asset_to_employee');
				Route::get('employee-asset', 'AssetController@employeeAsset')->name('asset.employee-asset');
				Route::get('update-asset-status/{id}/{emp_id}', 'AssetController@updateAssetStatus')->name('asset.update-asset-status');
				
				Route::get('asset-product-child/{id}', 'AssetController@asset_product_child')->name('asset.asset-product-child');
				Route::post('child-store', 'AssetController@child_store')->name('asset.child-store');
				
				Route::get('asset-pro-detail', 'AssetProController@assetDetail')->name('asset_pro.asset-pro-detail');
				Route::get('add-transfer-asset-pro/{id}', 'AssetProController@addTransferAsset')->name('asset_pro.add-transfer-asset-pro');
				Route::post('store-transfer-asset-pro/{id}', 'AssetProController@storeTransferAsset')->name('asset_pro.store-transfer-asset-pro');
				Route::get('transfer-asset-history-pro/{id}', 'AssetProController@transferAssetHistory')->name('asset_pro.transfer-asset-history-pro');
				Route::get('employee-asset-pro', 'AssetProController@employeeAsset')->name('asset_pro.employee-asset-pro');
				Route::get('update-asset-status-pro/{id}/{emp_id}/{action}', 'AssetProController@updateAssetStatus')->name('asset_pro.update-asset-status-pro');
				Route::post('remaining_asset_employee', 'AssetProController@remaining_asset_employee')->name('remaining_asset_employee');
				
				
				Route::get('degination-report-excel', 'DesignationController@download_excel')->name('degination-report-excel');
				Route::get('department-report-excel', 'DepartmentController@download_excel')->name('department-report-excel');
				Route::get('staff-movement-system-report-excel', 'StaffmovementsystemController@download_excel')->name('staff-movement-system-report-excel');
				
				Route::get('full-attendance-edit/{emp_id}/{date}/{table_name}', 'AttendanceController@fullAttendanceEdit')->name('full-attendance-edit');
				Route::post('update-full-attendence', 'AttendanceController@updateFullAttendence')->name('attendance.update-full-attendence');
				Route::get('late-employee-report-excel', 'EmployeeController@late_employee_download_excel')->name('late-employee-report-excel');
				
				Route::post('/import-chapter', 'BatchController@import_chapter')->name('import-chapter');
				Route::post('get-studio-by-branch', 'TimeTableController@getStudioByBranch')->name('get-studio-by-branch');
				Route::post('delete-timetable', 'TimeTableController@destroy')->name('delete-timetable');
				Route::post('edit-timetable', 'TimeTableController@update')->name('edit-timetable');
				Route::post('copy-timetable', 'TimeTableController@copyTimetable')->name('copy-timetable');
				
				Route::get('import-salary', 'SalaryController@import_salary')->name('import-salary');
				Route::post('store-salary', 'SalaryController@storeSalary')->name('store-salary');
				Route::get('salary-record-detail', 'SalaryController@salaryrecorddetail')->name('salary.salary-record-detail');
				
				Route::post('submit-salary-adjusment', 'SalaryController@submit_salary_adjusment')->name('submit-salary-adjusment');
				Route::get('/employee-salary-excel', 'SalaryController@download_salary_excel')->name('employee-salary-excel');
				
				Route::post('/startclass/store', 'TimetablesController@store_class')->name('startclass.store');
				Route::post('/endclass/update', 'TimetablesController@update_class')->name('endclass.update');
				Route::post('/partially_end_class_data', 'TimetablesController@partially_end_class_data')->name('partially_end_class_data'); //dk
				
				Route::get('/holiday/delete/{id}', 'HolidayController@destroy')->name('holiday.delete');
				
				Route::post('edit-start-class', 'FacultyReportsController@editStartClass')->name('edit-start-class');
				Route::post('update-start-class', 'FacultyReportsController@updateStartClass')->name('update-start-class');
				Route::get('/faculty-hours-reports', 'FacultyHoursReportsController@index')->name('faculty-hours-reports');
				Route::get('/faculty-hours-report-pdf', 'FacultyHoursReportsController@download_pdf')->name('faculty-hours-report-pdf');
				
				Route::get('/faculty-agreement-hours', 'FacultyHoursReportsController@agreement_hours')->name('faculty-agreement-hours'); //dk
				Route::get('/faculty-agreement-hours-report-excel', 'FacultyHoursReportsController@agreement_download_excel')->name('faculty-agreement-hours-report-excel'); //dk
				
				Route::post('/employee/get_leave_month', 'EmployeeController@get_leave_month')->name('employee.get_leave_month');
				
				Route::post('leave/save', 'LeaveController@save')->name('leave.save');
				Route::post('leave/check_pl', 'LeaveController@check_pl')->name('leave.check_pl');
				Route::get('update_manual_emp_id', 'LeaveController@update_manual_emp_id')->name('update_manual_emp_id');
				
				Route::get('update_manual_emp_attendance_notification', 'AttendanceController@update_manual_emp_attendance_notification')->name('update_manual_emp_attendance_notification');
				
				Route::get('update_manual_leave_record_leaves', 'ManualController@update_manual_leave_record_leaves')->name('update_manual_leave_record_leaves');
				
				Route::post('leave/append_leave_date', 'LeaveController@append_leave_date')->name('leave.append_leave_date');
				Route::get('manual_leave_records', 'LeaveController@manual_leave_records')->name('manual_leave_records');
				Route::get('manual_leave_records_month_wise', 'LeaveController@manual_leave_records_month_wise')->name('manual_leave_records_month_wise');
				
				Route::post('leave/approve_one_by', 'LeaveController@approve_one_by')->name('leave.approve_one_by');
				
				Route::get('/employee/emp_supervisorid_update_manual', 'EmployeeController@emp_supervisorid_update_manual')->name('employees.emp_supervisorid_update_manual');
					
				Route::get('/role/edit/{id}', 'RolesController@edit')->name('role.edit'); //Chetan
				Route::post('/role/update/{id}', 'RolesController@update')->name('role.update'); //Chetan
				Route::get('/notifications/edit/{id}', 'NotificationController@edit')->name('notifications.edit'); //Chetan
				Route::post('/notifications/update/{id}', 'NotificationController@update')->name('notifications.update'); //Chetan
				Route::get('/branch/edit/{id}', 'BranchController@edit')->name('branch.edit'); //Chetan
				Route::post('/branch/update/{id}', 'BranchController@update')->name('branch.update'); //Chetan
				Route::post('/batch/update/{id}', 'BatchController@update')->name('batch.update'); //Chetan
				Route::get('/employee/edit/{id}', 'EmployeeController@edit')->name('employee.edit'); //Chetan
				Route::post('/employee/update/{id}', 'EmployeeController@update')->name('employee.update'); //Chetan
				Route::get('/studios/edit/{id}', 'StudioController@edit')->name('studios.edit'); //Chetan
				Route::post('/studios/update/{id}', 'StudioController@update')->name('studios.update'); //Chetan
				Route::get('/course/edit/{id}', 'CourseController@edit')->name('course.edit'); //Chetan
				Route::post('/course/update/{id}', 'CourseController@update')->name('course.update'); //Chetan
				Route::post('/task/update/{id}', 'TaskController@update')->name('task.update'); //Chetan
				// Route::post('/attendance/update/{emp_id}/{date}', 'AttendanceController@update')->name('attendance.update');//Chetan
				Route::post('/staff/update/{id?}/', 'StaffmovementsystemController@togglePublish')->name('staff.update');  //Chetan
				Route::post('/department/update/{id?}', 'DepartmentController@update')->name('department.update');  //Chetan
				Route::get('/designation/edit/{id?}', 'DesignationController@edit')->name('designation.edit');  //Chetan
				Route::post('/designation/update/{id?}', 'DesignationController@update')->name('designation.update');  //Chetan
				Route::get('/buyer/delete-bill/{id}', 'BuyerController@edit')->name('buyer.edit'); //Chetan
				Route::post('/buyer/update/{id}', 'BuyerController@update')->name('buyer.update'); //Chetan
				Route::get('/product/edit/{id}', 'ProductController@edit')->name('product.edit'); //Chetan
				Route::post('/product/update/{id}', 'ProductController@update')->name('product.update'); //Chetan
				Route::get('/holiday/edit/{id}', 'HolidayController@edit')->name('holiday.edit');  //Chetan
				Route::post('/holiday/update/{id}', 'HolidayController@update')->name('holiday.update');  //Chetan
				
				Route::get('/driver/assign/{id}', 'DriverController@assign')->name('drivers.assign'); //DK
				Route::post('/driver/update/{id}', 'DriverController@update')->name('drivers.update'); //DK
				Route::post('/driver/update_driver', 'DriverController@update_driver')->name('driver.update_driver'); //DK
				
				Route::get('/links/faculty', 'LinksController@index')->name('links.faculty'); //DK
				Route::post('/links/faculty_link', 'LinksController@faculty_link')->name('links.faculty_link'); //DK
				Route::post('/links/manager_link', 'LinksController@manager_link')->name('links.manager_link'); //DK
				Route::post('/links/assistant_link', 'LinksController@assistant_link')->name('links.assistant_link'); //DK
				Route::post('/links/driver_link', 'LinksController@driver_link')->name('links.driver_link'); //DK
				Route::get('update_manual_send_link', 'ManualController@update_manual_send_link')->name('update_manual_send_link');
				Route::get('check_remaining_leave_manual', 'ManualController@check_remaining_leave_manual')->name('check_remaining_leave_manual');
				Route::get('is_extra_working_salary', 'ManualController@is_extra_working_salary')->name('is_extra_working_salary');
				
				Route::post('/get-location-wise-branch', 'StudioReportsController@get_locationwise_branch')->name('get-location-wise-branch');
				Route::post('branch-wise-timetable', 'TimeTableController@branch_wise_timetable')->name('branch-wise-timetable'); //pr
				Route::post('publish-timetable', 'TimeTableController@publish_timetable')->name('publish-timetable');
				Route::post('unpublish-timetable', 'TimeTableController@unpublish_timetable')->name('unpublish-timetable');
				
				Route::get('/location/delete/{id}', 'LocationController@destroy')->name('location.delete');
				Route::get('/location/status/{id}', 'LocationController@togglePublish')->name('location.status');
				
				Route::post('/employee/get-branch', 'EmployeeController@getBranch')->name('employee.get-branch'); //Chetan
				Route::post('/attendance/get-branch', 'AttendanceRecordController@getBranch')->name('attendance.get-branch'); //Chetan
				
				Route::get('incomplete-attendence', 'AttendanceController@incompleteattendence')->name('attendance.incompleteattendence');  //Chetan
				Route::get('incomplte-attendence-detail', 'AttendanceController@incomplteAttendanceDetail')->name('attendance.incomplte-attendence-detail'); //Chetan
				Route::get('incomplete-attendance-report-excel', 'AttendanceController@incomplete_download_excel')->name('incomplete-attendance-report-excel');	//Chetan
				Route::get('incomplete-attendance-edit/{emp_id}/{date}/{table_name}', 'AttendanceController@fullAttendanceEdit')->name('full-attendance-edit'); //Chetan
				
				Route::get('leave-count-view', 'LeaveController@leavecount')->name('leave.leavecount');  //Chetan
				Route::get('get_pending_comp_off_all_users', 'LeaveController@get_pending_comp_off_all_users')->name('leave.get_pending_comp_off_all_users');  //DK
				
				Route::get('attendence-record-single-day', 'AttendanceRecordSingleController@attendencerecordsingle')->name('attendance.attendence-record-single-day');
				Route::get('attendence-record-detail-single', 'AttendanceRecordSingleController@attendencerecorddetailsingle')->name('attendance.attendence-record-detail-single');

				
				Route::get('/studio-availability', 'BatchReportsController@studio_availability')->name('studio-availability'); //pr

				Route::get('/batch-reports', 'BatchReportsController@index')->name('batch-reports'); //dk
				Route::get('/batch-report-pdf', 'BatchReportsController@download_pdf')->name('batch-report-pdf');
				
				Route::get('leave-count-all', 'LeaveController@leavecountall')->name('leave.leavecountall');  //Chetan
				Route::get('full-attendance-report-excel-two', 'AttendanceController@full_download_excel_two')->name('full-attendance-report-excel-two');  //Chetan
				
				Route::get('/links/all_send/{id}', 'LinksController@all_send')->name('links.all_send'); //DK

				

				Route::get('approved-leave', 'LeaveController@approved_leave')->name('leave.approved-leave');
				Route::post('update-approved-leave', 'LeaveController@update_approved_leave')->name('leave.update-approved-leave');
				
				Route::get('/batch-report-report-excel', 'BatchReportsController@download_excel')->name('batch-report-report-excel');
				Route::get('/studio-report-report-excel', 'StudioReportsController@download_excel')->name('studio-report-report-excel');
				Route::get('/salary-slip-report-pdf', 'SalaryController@download_pdf')->name('salary-slip-report-pdf');
				
				Route::get('add-increment', 'SalaryController@add_increment')->name('add-increment');
				Route::post('store-increment', 'SalaryController@store_increment')->name('salary.store-increment');
				Route::post('salary-store-increment', 'SalaryController@salary_store_increment')->name('salary.salary-store-increment');
				
				Route::get('/employee/esic-no-detail', 'EmployeeController@esic_no_detail')->name('employees.esic-no-detail');
				Route::get('/employee/uan-no-detail', 'EmployeeController@uan_no_detail')->name('employees.uan-no-detail');
				
				Route::get('add-deduction', 'SalaryController@add_deduction')->name('add-deduction');
				Route::post('store-deduction', 'SalaryController@store_deduction')->name('salary.store-deduction');
				
				Route::get('/knowledge_based/status/{id}', 'KnowledgeBasedController@togglePublish')->name('knowledge_based.status');
				Route::get('/knowledge_based/delete/{id}', 'KnowledgeBasedController@destroy')->name('knowledge_based.delete');
				Route::get('/knowledge_based/edit/{id?}', 'KnowledgeBasedController@edit')->name('knowledge_based.edit');  
				Route::post('/knowledge_based/update/{id?}', 'KnowledgeBasedController@update')->name('knowledge_based.update');
				Route::post('/edit-knowledge-based-category', 'KnowledgeBasedCategoryController@edit_category')->name('edit-knowledge-based-category');
				Route::get('/knowledge_based_category/delete/{id}', 'KnowledgeBasedCategoryController@destroy')->name('knowledge_based_category.delete');
				Route::get('/knowledge_based_category/status/{id}', 'KnowledgeBasedCategoryController@togglePublish')->name('knowledge_based_category.status');

				Route::get('/training_video/status/{id}', 'TrainingVideoController@togglePublish')->name('training_video.status');
				Route::get('/training_video/delete/{id}', 'TrainingVideoController@destroy')->name('training_video.delete');
				Route::get('/training_video/edit/{id?}', 'TrainingVideoController@edit')->name('training_video.edit');  
				Route::post('/training_video/update/{id?}', 'TrainingVideoController@update')->name('training_video.update');
				Route::post('/edit-training-video_-ategory', 'TrainingVideoCategoryController@edit_category')->name('edit-training-video-category');
				Route::get('/training_video_category/delete/{id}', 'TrainingVideoCategoryController@destroy')->name('training_video_category.delete');
				Route::get('/training_video_category/status/{id}', 'TrainingVideoCategoryController@togglePublish')->name('training_video_category.status');
				
				Route::get('/meeting-places', 'MeetingController@meeting_places')->name('meeting-places');
				Route::get('/meeting-places/create', 'MeetingController@create')->name('meeting-places.create');
				Route::post('/meeting-places/store', 'MeetingController@store')->name('meeting-places.store');
				Route::get('/meeting-places/status/{id}', 'MeetingController@togglePublish')->name('meeting-places.status');
				Route::get('/meeting-places/delete/{id}', 'MeetingController@destroy')->name('meeting-places.delete');
				Route::get('/meeting-places/edit/{id?}', 'MeetingController@edit')->name('meeting-places.edit');  
				Route::post('/meeting-places/update/{id?}', 'MeetingController@update')->name('meeting-places.update');
				Route::get('/meeting-add/{id?}', 'MeetingController@meeting_store')->name('meeting-add');
				Route::post('/get-place', 'MeetingController@get_place')->name('get-place');
				Route::post('/add-meeting', 'MeetingController@add_meeting')->name('add-place');
				Route::post('/add-key-point', 'MeetingController@add_key_points')->name('add-key-point');
				Route::get('/meeting-history/{id?}', 'MeetingController@meeting_history')->name('meeting-history');
				
				
				Route::get('/appointment', 'MeetingController@index')->name('appointment');
				Route::post('/get-appointment-status', 'MeetingController@get_appointment_status')->name('get-appointment-status');
				Route::post('/update-appointment-status', 'MeetingController@update_appointment_status')->name('update-appointment-status');
				Route::post('/cancel-appointment-status', 'MeetingController@cancel_appointment_status')->name('cancel-appointment-status');
				
				Route::get('/faculty-hours-report-excel', 'FacultyHoursReportsController@download_excel')->name('faculty-hours-report-excel');
				
				Route::get('knowledge-based-report-excel', 'KnowledgeBasedController@download_excel')->name('knowledge-based-report-excel');
				Route::get('training-video-report-excel', 'TrainingVideoController@download_excel')->name('training-video-report-excel');
				Route::get('appointment-report-excel', 'MeetingController@download_excel')->name('appointment-report-excel');
				
				//Route::get('/change-department', 'DepartmentController@changeDepartment')->name('change-department');
				//Route::post('/store-change-department', 'DepartmentController@storeChangeDepartment')->name('store-change-department');
				//Route::post('/store-change-employee-department', 'DepartmentController@storeChangeEmployeeDepartment')->name('store-change-employee-department');
				
				Route::get('/sub_department/status/{id}', 'SubDepartmentController@togglePublish')->name('sub_department.status');
				Route::get('/sub_department/delete/{id}', 'SubDepartmentController@destroy')->name('sub_department.delete');
				Route::post('/sub_department/update/{id?}', 'SubDepartmentController@update')->name('sub_department.update');
				Route::get('sub-department-report-excel', 'SubDepartmentController@download_excel')->name('sub-department-report-excel');
				
				Route::post('/get-sub-department', 'EmployeeController@get_sub_department')->name('get-sub-department');
				
				Route::get('/employee/add-supervisor', 'EmployeeController@addSupervisor')->name('employees.add-supervisor');
				Route::post('/store-supervisor-by-department', 'EmployeeController@storeSupervisorByDepartment')->name('store-supervisor-by-department');
				Route::post('/store-supervisor-by-employee', 'EmployeeController@storSupervisorByEmployee')->name('store-supervisor-by-employee');
				Route::post('/store-supervisor-by-branch', 'EmployeeController@storeSupervisorByBranch')->name('store-supervisor-by-branch');
				
				Route::get('/employee/remove-supervisor', 'EmployeeController@removeSupervisor')->name('employees.remove-supervisor');
				Route::post('/remove-supervisor-by-department', 'EmployeeController@removeSupervisorByDepartment')->name('remove-supervisor-by-department');
				Route::post('/remove-supervisor-by-employee', 'EmployeeController@removeSupervisorByEmployee')->name('remove-supervisor-by-employee');
				Route::post('/remove-supervisor-by-branch', 'EmployeeController@removeSupervisorByBranch')->name('remove-supervisor-by-branch');
				
				Route::get('/employee/job-role', 'EmployeeController@jobRole')->name('employees.job-role');
				Route::get('/employee/add-job-role/{id?}', 'EmployeeController@addJobRole')->name('employees.add-job-role');
				Route::post('/store-job-role/{id?}', 'EmployeeController@storeJobRole')->name('store-job-role');
				Route::get('/job_role/status/{id}', 'EmployeeController@jobRoleTogglePublish')->name('job_role.status');
				Route::get('/job_role/delete/{id}', 'EmployeeController@jobRoleDestroy')->name('job_role.delete');
				Route::get('/employee/view-job-role', 'EmployeeController@viewJobRole')->name('employees.view-job-role');
				
				Route::get('/employee/probation-month', 'EmployeeController@probationMonth')->name('employees.probation-month');
				Route::get('/employee/birthday', 'EmployeeController@birthday')->name('employees.birthday');
				Route::get('/employee/work-anniversary', 'EmployeeController@workAnniversary')->name('employees.work-anniversary');
				
				Route::get('/expense/status/{id}', 'ExpenseController@togglePublish')->name('expense.status');
				Route::get('/expense/delete/{id}', 'ExpenseController@destroy')->name('expense.delete');
				Route::get('/expense/edit/{id?}', 'ExpenseController@edit')->name('expense.edit');  
				Route::post('/expense/update/{id?}', 'ExpenseController@update')->name('expense.update');
				Route::post('/expense/statusupdate', 'ExpenseController@status_update')->name('expense.statusupdate');

				Route::post('/edit-expense-category', 'ExpenseCategoryController@edit_category')->name('edit-expense-category');
				Route::get('/expense_category/delete/{id}', 'ExpenseCategoryController@destroy')->name('expense_category.delete');
				Route::get('/expense_category/status/{id}', 'ExpenseCategoryController@togglePublish')->name('expense_category.status');
				
				
				//Asset Request
				Route::get('add-request', 'AssetRequestController@addRequestAsset')->name('request.add-request'); 
				Route::get('/request/delete/{id}', 'AssetRequestController@destroy')->name('request.delete');	
				Route::get('/request/edit/{id}', 'AssetRequestController@edit')->name('request.edit');
				Route::post('/request/update/{id}', 'AssetRequestController@update')->name('request.update');
				Route::get('requisition-request', 'AssetRequestController@requisitionList')->name('request.requisition-request');
				Route::get('request-approval', 'AssetRequestController@requestApprovalList')->name('request.request-approval');
				Route::get('po/{id}', 'AssetRequestController@poprint')->name('request.poprint');
				Route::get('po-edit/{id}', 'AssetRequestController@poedit')->name('request.poedit');
				Route::post('product-accept', 'AssetRequestController@product_accept')->name('request.product-accept');
				Route::post('po-request', 'AssetRequestController@po_request')->name('request.po-request');
				Route::post('po-request-edit', 'AssetRequestController@po_request_edit')->name('request.po-request-edit');
				Route::post('get-request-data', 'AssetRequestController@get_request_data')->name('request.get-request-data');
				Route::post('po-status-update', 'AssetRequestController@po_status_update')->name('request.po-status-update'); 
				Route::get('po-list', 'AssetRequestController@po_list')->name('request.po-list'); 
				Route::get('po-report-excel', 'AssetRequestController@po_download_excel')->name('po-report-excel');
				Route::get('po-payment-excel', 'AssetRequestController@po_payment_excel')->name('po-payment-excel');
				Route::get('vendor-invoice', 'AssetRequestController@vendor_invoice')->name('request.vendor-invoice'); 
				Route::post('accounts-invoice-update', 'AssetRequestController@accounts_invoice_update')->name('accounts-invoice-update'); 
				Route::post('/request/status', 'AssetRequestController@togglePublish')->name('request.status');
				Route::get('manual-invoice', 'AssetRequestController@manual_invoice')->name('request.manual-invoice');
				
				
				Route::post('get-product-item', 'AssetRequestController@get_product_item')->name('request.get-product-item');
				Route::get('maintenance-list', 'AssetRequestController@maintenance_list')->name('request.maintenance-list');
				Route::post('request-transfer', 'AssetRequestController@request_transfer')->name('request.request-transfer');
				Route::post('inventory-product-accept', 'AssetRequestController@inventory_product_accept')->name('request.inventory-product-accept');				
				
				
				Route::post('/request/status-update', 'AssetRequestController@statusUpdate')->name('request.status-update'); 
				Route::get('/request/edit-requisition/{id?}/{type?}', 'AssetRequestController@editRequisition')->name('request.edit-requisition');
				Route::post('/request/update-requisition/{id}/{type?}', 'AssetRequestController@updateRequisition')->name('request.update-requisition');
				Route::post('/show-requisition', 'AssetRequestController@showRequisition')->name('show-requisition');
				Route::post('/quotation-add', 'AssetRequestController@quotation_add')->name('quotation-add');		//Inventory Team Add Route
				Route::post('/invoice-details', 'AssetRequestController@invoice_details')->name('invoice-details');		//Inventory Team Add Route
				Route::get('/quotation-view', 'AssetRequestController@quotation_view')->name('quotation-view');		
				Route::post('/quotation-upload', 'AssetRequestController@quotation_upload')->name('quotation-upload');	//Purchase Team Add Route
				Route::post('/get-company-details', 'AssetRequestController@getcompany_details')->name('get-company-details');
				Route::post('/check-product-quantity', 'AssetRequestController@check_product_quantity')->name('check-product-quantity');
				Route::post('/copy-mrl', 'AssetRequestController@copy_mrl')->name('copy-mrl'); //Copy MRL
				Route::get('/request-pending-accept', 'AssetRequestController@request_pending_accept')->name('request-pending-accept'); //Pending Accept
				Route::get('request-pending-accept-excel', 'AssetRequestController@request_pending_accept_excel')->name('request-pending-accept-excel');
				Route::get('store-purchase-dashboard', 'AssetRequestController@store_purchase_dashboard')->name('store-purchase-dashboard');
				Route::post('mrl-rollback', 'AssetRequestController@mrl_rollback')->name('mrl-rollback');
				Route::post('get-mrl-details', 'AssetRequestController@getmrl_details')->name('get-mrl-details');
				Route::get('requested-asset-by-hr', 'AssetRequestController@requested_asset_by_hr')->name('request.requested-asset-by-hr');
				
				
				Route::get('inventory-valuation', 'AssetRequestController@inventory_valuation')->name('request.reports.inventory-valuation');
				Route::get('location-request-list', 'AssetRequestController@location_request_list')->name('request.reports.location-request-list');
				Route::get('monthly-master-data', 'AssetRequestController@monthly_master_data')->name('request.reports.monthly-master-data');
				
				Route::post('product-return', 'AssetRequestController@product_return')->name('request.product-return');
				Route::get('return-product-list', 'AssetRequestController@return_product_list')->name('request.return-product-list');
				Route::post('mrl-import', 'AssetRequestController@mrl_import')->name('request.mrl-import');
				Route::get('auto-reject-po', 'AssetRequestController@auto_reject_po')->name('request.auto-reject-po');
				
				Route::get('add-transfer-asset/{id}', 'AssetController@addTransferAsset')->name('asset.add-transfer-asset');
				Route::post('store-transfer-asset/{id}', 'AssetController@storeTransferAsset')->name('asset.store-transfer-asset');
				Route::get('transfer-asset-history/{id}', 'AssetController@transferAssetHistory')->name('asset.transfer-asset-history');
				
				Route::get('/batch-reports-shiftwise', 'BatchReportsController@batchReportsShiftwise')->name('batch-reports-shiftwise');
				Route::get('/batch-report-shiftwise-pdf', 'BatchReportsController@download_pdf_shiftwise')->name('batch-report-shiftwise-pdf');
				
				Route::get('/appraisal/status/{id}', 'AppraisalController@togglePublish')->name('appraisal.status');
				Route::get('appraisal-user-list', 'AppraisalController@appraisalUserList')->name('appraisal-user-list');
				Route::get('appraisal-user-question-list/{id}/{emp_id}', 'AppraisalController@appraisalUserQuestionList')->name('appraisal-user-question-list');
				Route::get('appraisal-user-question-response/{id}/{emp_id}', 'AppraisalController@appraisalUserQuestionResponse')->name('appraisal-user-question-response');
				Route::post('store-appraisal-user-question-response', 'AppraisalController@storeAppraisalUserQuestionResponse')->name('store-appraisal-user-question-response');
				
				Route::get('/task-report-pdf', 'TaskController@download_pdf')->name('task-report-pdf'); //Chetan
				
				Route::post('/get-multi-location-wise-branch', 'HolidayController@get_multi_locationwise_branch')->name('get-multi-location-wise-branch');
				
				Route::get('/employee/is-comp-off/{id}', 'EmployeeController@isCompOff')->name('employees.is-comp-off');
				Route::post('/employee/store-comp-off/{id}', 'EmployeeController@storeCompOff')->name('employees.store-comp-off');
				
				
				Route::get('/final-attendence', 'AttendanceController@finalAttendence')->name('attendance.final-attendence');
				Route::post('/store-final-attendence', 'AttendanceController@storeFinalAttendence')->name('store-final-attendence');
				Route::post('final-attendence-detail', 'AttendanceController@finalAttendenceDetail')->name('attendance.final-attendence-detail');
				
				Route::get('leave-wages', 'LeaveController@leaveWages')->name('leave.leave-wages');
				Route::get('leave-wages-tab', 'LeaveController@leaveWagesTab')->name('leave.leave-wages-tab');
				
				Route::post('/edit-support-category', 'SupportCategoryController@edit_category')->name('edit-support-category');
				Route::get('/support_category/delete/{id}', 'SupportCategoryController@destroy')->name('support_category.delete');
				Route::get('/support_category/status/{id}', 'SupportCategoryController@togglePublish')->name('support_category.status');
				
				Route::post('/edit-support-user', 'SupportUserController@edit_support_user')->name('edit-support-user');
				Route::get('/support_user/delete/{id}', 'SupportUserController@destroy')->name('support_user.delete');
				Route::get('/support_user/status/{id}', 'SupportUserController@togglePublish')->name('support_user.status');
				
				Route::post('store-enquiry-description', 'EnquiryController@store_enquiry_description')->name('store-enquiry-description');
				
				Route::get('support-dashboard', 'SupportCategoryController@support_dashboard')->name('support-dashboard');
				Route::get('support-enquiry', 'SupportCategoryController@enquiry')->name('support-enquiry');		
				Route::post('support-enquiryReply', 'SupportCategoryController@enquiryReply')->name('support-enquiryReply');
				Route::get('support-discussion', 'SupportCategoryController@discussion')->name('support-discussion');		
				Route::post('/support-discussion-comment', 'SupportCategoryController@support_discussion_comment')->name('support-discussion-comment');
				Route::post('/support-discussion-delete', 'SupportCategoryController@support_discussion_delete')->name('support_discussion_delete');
				Route::post('support_discussion_reply', 'SupportCategoryController@support_discussion_reply')->name('support_discussion_reply');
				Route::get('/support-discussion-batch', 'SupportCategoryController@support_discussion_batch')->name('support-discussion-batch');	
				
				Route::get('forum-dashboard', 'ForumController@forumDashboard')->name('forum-dashboard');
				Route::post('/get-old-query', 'EnquiryController@get_old_query')->name('get-old-query');
				Route::get('/enquiry/destroy/{id}', 'EnquiryController@destroy')->name('enquiry.destroy');
				Route::post('store-enquiry-status', 'EnquiryController@store_enquiry_status')->name('store-enquiry-status');
				
				Route::get('/training_pdf/status/{id}', 'TrainingPdfController@togglePublish')->name('training_pdf.status');
				Route::get('/training_pdf/delete/{id}', 'TrainingPdfController@destroy')->name('training_pdf.delete');
				Route::get('/training_pdf/edit/{id?}', 'TrainingPdfController@edit')->name('training_pdf.edit');  
				Route::post('/training_pdf/update/{id?}', 'TrainingPdfController@update')->name('training_pdf.update');
				Route::get('training-pdf-report-excel', 'TrainingPdfController@download_excel')->name('training-pdf-report-excel');				
								
				Route::get('/attendance-lock/add', 'AttendancelockController@add')->name('attendance-lock.add');//dk
				Route::post('/attendance-lock/add_save', 'AttendancelockController@add_save')->name('attendance-lock.add_save');//dk
				Route::get('/attendance-lock/edit/{id}', 'AttendancelockController@edit')->name('attendance-lock.edit');//dk
				Route::post('/attendance-lock/update/{id?}', 'AttendancelockController@update')->name('attendance-lock.update');
				Route::get('/attendance-lock/index', 'AttendancelockController@index')->name('attendance-lock.index');//dk
				
				Route::get('/faculty-monthly-hours-reports', 'FacultyReportsController@facultyMonthlyHoursReports')->name('faculty-monthly-hours-reports'); 
				Route::get('/faculty-topic', 'FacultyReportsController@faculty_topic')->name('faculty-topic');
				
				Route::get('/faculty-monthly-hours-report-excel', 'FacultyReportsController@download_excel')->name('faculty-monthly-hours-report-excel');
				
				Route::get('/assigned-assistants', 'AssignedAssistantsController@assigned_assistants')->name('assigned-assistants'); //DK
				Route::post('/assigned_assistant_update', 'AssignedAssistantsController@assigned_assistant_update')->name('assigned_assistant_update'); //DK
				
				Route::get('/assigned-incharge', 'AssignedAssistantsController@assigned_incharge')->name('assigned-incharge'); //DK
				Route::post('/assigned_incharge_update', 'AssignedAssistantsController@assigned_incharge_update')->name('assigned_incharge_update'); //DK
				
				Route::post('/get-course-list-by-mobile', 'OnlineCoursesController@get_course_list_by_mobile')->name('get-course-list-by-mobile');
				Route::post('/delete-course', 'OnlineCoursesController@delete_course')->name('delete-course');
				
				Route::get('/batch-hours-reports', 'BatchReportsController@batch_hours_reports')->name('batch-hours-reports'); 
				
				
				Route::get('/faculty-early-delay-reports', 'FacultyEarlyDelayReportsController@index')->name('faculty-early-delay-reports');
			    Route::post('/delayWhatsapp', 'FacultyEarlyDelayReportsController@delayWhatsapp')->name('delayWhatsapp');
				Route::get('/timetable-change-counts', 'FacultyEarlyDelayReportsController@ttChangeCounts')->name('timetable-change-counts');

				
				Route::get('/faculty-early-delay-report-excel', 'FacultyEarlyDelayReportsController@download_excel')->name('faculty-early-delay-report-excel');
				
				Route::post('/get-class-batch-subject-by-faculty', 'TimeTableController@get_batch_subject_by_faculty')->name('get-class-batch-subject-by-faculty');
				
				Route::get('/assigned-assistants-excel', 'AssignedAssistantsController@download_excel')->name('assigned-assistants-excel');
				
				//Roles Permission
				Route::get('permission-add/', 'PermissionController@permission_add')->name('permission-add');  		//Add Page Show URL
				Route::post('permission-store', 'PermissionController@store')->name('permission-store');	   		//Insert Route
				
				Route::get('permission-list', 'PermissionController@permission_list')->name('permission-list');		//List View
				Route::get('permission-edit/{id?}/{title?}', 'PermissionController@edit')->name('permission-edit');	//Edit Page Show URL				
				
				Route::post('permission-update/{id?}', 'PermissionController@update')->name('permission-update');	//Update Route
				Route::get('permission-delete/{id}', 'PermissionController@destroy')->name('permission-delete');	//Permission Delete
				
				Route::post('/edit-course-category', 'CourseCategoryController@edit_category')->name('edit-course-category');
				Route::get('/course_category/delete/{id}', 'CourseCategoryController@destroy')->name('course_category.delete');
				Route::get('/course_category/status/{id}', 'CourseCategoryController@togglePublish')->name('course_category.status');   
				
				Route::post('update-cancel-class', 'FacultyReportsController@updateCancelClass')->name('update-cancel-class');
				
				Route::get('/chapter/import', 'ChapterController@import')->name('chapter.import');
				Route::post('/chapter/import/store', 'ChapterController@import_store')->name('chapter.import.store');
				
				
				//New Task Module
				Route::get('add-task/', 'GetTaskController@task_add')->name('task-add');  	
				Route::post('task-store', 'GetTaskController@task_store')->name('task-store');	
				Route::get('view-task', 'GetTaskController@view_task')->name('view-task');	
				Route::get('view-task-history/{id?}', 'GetTaskController@view_task_history')->name('view-task-history');	 //New 
				Route::post('edit-task', 'GetTaskController@edit_task')->name('edit-task');
				Route::post('update-spent-hour', 'GetTaskController@update_spent_task')->name('update-spent-hour');
				Route::post('update-task', 'GetTaskController@update_task')->name('update-task');
				Route::get('/get-task-report-pdf', 'GetTaskController@download_pdf')->name('get-task-report-pdf'); //Chetan
				Route::get('/task_history/{task_id}', 'GetTaskController@task_history')->name('task-history'); //Chetan
				Route::get('/delete/{id}', 'GetTaskController@destroy')->name('task-delete');
				
				//
				Route::get('/batch-test-report', 'TestReportController@batch_test_report')->name('batch-test-report'); //dk
				Route::post('/batch_subject_status_update', 'BatchController@batch_subject_status_update')->name('batch_subject_status_update'); //DK
				Route::get('faculty-leave', 'FacultyLeaveController@faculty_leave')->name('faculty-leave');
				Route::get('faculty-leave-add', 'FacultyLeaveController@faculty_leave_add')->name('faculty-leave-add');
				Route::post('faculty_leave_add_save', 'FacultyLeaveController@faculty_leave_add_save')->name('faculty_leave_add_save');
				Route::post('/faculty_leave_update', 'FacultyLeaveController@faculty_leave_update')->name('faculty_leave_update'); //DK
				Route::get('faculty-leave-download', 'FacultyLeaveController@faculty_leave_download')->name('faculty-leave-download');
				
				Route::get('/batch-test-report-new', 'TestReportController@batch_test_report_new')->name('batch-test-report-new'); //CM
				Route::get('/send-faculty-pdf-view', 'TestReportController@send_faculty_pdf_view')->name('send-faculty-pdf-view'); //CM
				Route::post('/send-faculty-pdf-add', 'TestReportController@send_faculty_pdf_add')->name('send-faculty-pdf-add'); //CM
				
				
				//Feedback
				Route::get('/feedback-form', 'FeedbackFormController@index')->name('feedback-form'); 
				Route::get('/feedback-form-add', 'FeedbackFormController@form_add')->name('feedback-form-add'); 
				Route::get('/feedback-form-delete/{id?}', 'FeedbackFormController@form_destroy')->name('feedback-form-delete');
				Route::get('/feedback-form-edit/{id?}', 'FeedbackFormController@form_edit')->name('feedback-form-edit'); 
				Route::post('/feedback-form-store', 'FeedbackFormController@feedback_form_store')->name('feedback-form-store'); 
				Route::post('/feedback-form-update/{id?}', 'FeedbackFormController@form_update')->name('feedback-form-update'); 
				
				Route::get('/feedback-question', 'FeedbackQuestionController@index')->name('feedback-question'); 
				Route::get('/feedback-question-add', 'FeedbackQuestionController@question_add')->name('feedback-question-add'); 
				Route::get('/feedback-question-edit/{id?}', 'FeedbackQuestionController@question_edit')->name('feedback-question-edit'); 
				Route::post('/feedback-question-update/{id?}', 'FeedbackQuestionController@question_update')->name('feedback-question-update'); 
				Route::post('/feedback-question-store', 'FeedbackQuestionController@feedback_store')->name('feedback-question-store'); 
				Route::get('/feedback-question-delete/{id?}', 'FeedbackQuestionController@question_destroy')->name('feedback-question-delete'); 
				
				Route::get('/employee-complaint-view', 'FeedbackFormController@employee_complaint_view')->name('employee-complaint-view');
				
				Route::get('leave-full-detail', 'LeaveController@leave_full_detail')->name('leave.leave-full-detail'); // dk
				Route::get('leave-full-detail-new', 'LeaveController@leave_full_detail_new')->name('leave.leave-full-detail-new'); // dk
				Route::post('/leave-full-detail-history', 'LeaveController@leave_full_detail_history')->name('leave-full-detail-history'); //DK
				
				Route::get('/sendsms-textlocal', 'SendsmsController@sendsms_textlocal')->name('sendsms_textlocal');
				Route::post('/sendsms-textlocal-save', 'SendsmsController@sendsms_textlocal_save')->name('sendsms_textlocal_save');
				Route::get('/sendsms-templates-add', 'SendsmsController@sendsms_templates_add')->name('sendsms_templates_add');
				Route::post('/sendsms_templates_save', 'SendsmsController@sendsms_templates_save')->name('sendsms_templates_save');
				Route::get('/sendsms-templates', 'SendsmsController@sendsms_templates')->name('sendsms_templates');
				Route::get('/sendsms_template_delete/{id}', 'SendsmsController@sendsms_template_delete')->name('sendsms_template_delete');
				
				
				Route::get('/inventory', 'InventoryController@index')->name('inventory.index');
				Route::get('/inventory/product-inventory-list/{product_id}', 'InventoryController@product_inventory_list')->name('inventory.product-inventory-list');
				
				Route::get('/inventory/product-dead-status/{product_id}', 'InventoryController@product_dead_list')->name('inventory.product-dead-status');
				
				Route::post('/inventory/update/{id}', 'InventoryController@update')->name('inventory.update');
				Route::get('/inventory/inventory-transfer/{product_id}', 'InventoryController@inventory_transfer')->name('inventory.inventory-transfer');
				Route::post('/inventory/inventory-transfer-store', 'InventoryController@inventory_transfer_store')->name('inventory.inventory-transfer-store');
				Route::get('/inventory/inventory-transfer-list/{product_id}', 'InventoryController@inventory_transfer_list')->name('inventory.inventory-transfer-list');
				Route::get('/inventory/delete/{id}', 'InventoryController@destroy')->name('inventory.delete');
				Route::get('warehouse-report-excel', 'InventoryController@warehouse_report_excel')->name('warehouse-report-excel'); //Chetan
				
				Route::get('/crm-desk/search', 'CrmdeskController@search')->name('crm-desk.search');
				Route::post('/crm-desk/search_result', 'CrmdeskController@search_result')->name('crm-desk.search_result');
				Route::post('/crm-desk/call_activity', 'CrmdeskController@call_activity')->name('crm-desk.call_activity');
				Route::post('/crm-desk/activity_reply', 'CrmdeskController@activity_reply')->name('crm-desk.activity_reply');
				Route::post('/crm-desk/assign_agent', 'CrmdeskController@assign_agent')->name('crm-desk.assign_agent');
				Route::post('/crm-desk/get_main_cat', 'CrmdeskController@get_main_cat')->name('crm-desk.get_main_cat');
				Route::post('/crm-desk/get_course_name', 'CrmdeskController@get_course_name')->name('crm-desk.get_course_name');
				Route::post('/crm-desk/save_course', 'CrmdeskController@save_course')->name('crm-desk.save_course');
				Route::post('/crm-desk/sendToNpf', 'CrmdeskController@sendToNpf')->name('crm-desk.sendToNpf');
				
				Route::post('/crm-desk/get_course_type', 'CrmdeskController@get_course_type')->name('crm-desk.get_course_type');
				
				Route::post('/enquiry/update/{id}', 'EnquiryController@update')->name('enquiry.update'); //DK
				
				Route::get('/student-attendance', 'StudentAttendanceController@index')->name('student-attendance'); //CM
				
				
				//New API Testing
				Route::get('/crm-desk/search-new', 'CrmdeskNewController@search_new')->name('crm-desk.search-new'); 
				Route::post('/crm-desk/search_result_new', 'CrmdeskNewController@search_result_new')->name('crm-desk.search_result_new');
				Route::post('/crm-desk/call_activity_new', 'CrmdeskNewController@call_activity_new')->name('crm-desk.call_activity_new');
				Route::post('/crm-desk/activity_reply_new', 'CrmdeskNewController@activity_reply_new')->name('crm-desk.activity_reply_new');
				Route::post('/crm-desk/assign_agent_new', 'CrmdeskNewController@assign_agent_new')->name('crm-desk.assign_agent_new');
				Route::post('/crm-desk/get_main_cat_new', 'CrmdeskNewController@get_main_cat_new')->name('crm-desk.get_main_cat_new');
				Route::post('/crm-desk/get_course_name_new', 'CrmdeskNewController@get_course_name_new')->name('crm-desk.get_course_name_new');
				Route::post('/crm-desk/save_course_new', 'CrmdeskNewController@save_course_new')->name('crm-desk.save_course_new');
				
				
				//Route::post('/crm-desk/get_course_type', 'CrmdeskNewController@get_course_type')->name('crm-desk.get_course_type');
				
				
				//DK
				Route::get('/batchinventory/index', 'BatchInventoryController@index')->name('batchinventory.index'); 
				Route::get('/batchinventory/add', 'BatchInventoryController@add')->name('batchinventory.add'); 
				Route::post('/batchinventory/save', 'BatchInventoryController@save')->name('batchinventory.save');
				Route::get('/batchinventory/edit/{id}', 'BatchInventoryController@edit')->name('batchinventory.edit');
				Route::post('/batchinventory/update/{id}', 'BatchInventoryController@update')->name('batchinventory.update');
				Route::get('/batchinventory/delete/{id}', 'BatchInventoryController@destroy')->name('batchinventory.delete');				 
				Route::get('/attendance-dashboard', 'BatchInventoryController@attendance_dashboard')->name('attendance-dashboard'); 
				Route::get('/student-attendance-view/{id?}/{fdate?}/{tdate?}/{type?}', 'BatchInventoryController@student_attendance_view')->name('student-attendance-view'); 
				Route::get('/inventory-dashboard', 'BatchInventoryController@inventory_dashboard')->name('inventory-dashboard'); 
				Route::post('/get-batch', 'BatchInventoryController@getBatch')->name('get-batch'); //Chetan
				Route::get('/student-inventory-view/{batch_id?}/{id?}/{type?}', 'BatchInventoryController@student_inventory_view')->name('student-inventory-view'); 
				Route::post('/studentSearch', 'BatchInventoryController@studentSearch')->name('batchinventory.studentSearch'); //pc
				Route::post('/get-batch-inventory', 'BatchInventoryController@get_batch_inventory')->name('batchinventory.get-batch-inventory'); //pc
				Route::post('/batchinventory/save-attendance-notification', 'BatchInventoryController@save_attendance_notification')->name('batchinventory.save-attendance-notification');
				
				Route::get('/student-get-inventory', 'BatchInventoryController@student_get_inventory')->name('student-get-inventory'); 
				Route::get('/student-assign-inventory/{reg_no?}/{id?}', 'BatchInventoryController@student_assign_inventory')->name('student-assign-inventory'); 
				
				Route::get('/studnet-attendance-notification', 'BatchInventoryController@studnet_attendance_notification')->name('studnet-attendance-notification'); 
				Route::get('student-attendence-record', 'BatchInventoryController@attendencerecord')->name('student-attendence-record');
				Route::post('student-attendence-record-detail', 'BatchInventoryController@studnet_attendencerecorddetail')->name('student-attendence-record-detail');
				
				Route::get('anuprati-dashboard', 'BatchInventoryController@anuprati_dashboard')->name('anuprati-dashboard');
				Route::get('anuprati-report-excel', 'BatchInventoryController@anuprati_report_excel')->name('anuprati-report-excel');
				Route::get('student-invalid-punch', 'BatchInventoryController@stu_invlid_punch')->name('student-invalid-punch');
				Route::get('student-inventory-track', 'BatchInventoryController@student_inventory_track')->name('student-inventory-track');
				
				Route::get('/class-type-wise', 'ClassTypeWiseController@index')->name('class-type-wise'); //dk
				Route::get('/class-type-wise-pdf', 'ClassTypeWiseController@download_pdf')->name('class-type-wise-pdf'); //dk
				
				Route::get('paternity-leave', 'LeaveController@paternity_leave')->name('leave.paternity_leave');
				Route::post('store-paternity-leave', 'LeaveController@storPaternityLeave')->name('store-paternity-leave');
				Route::get('paternity-leave-list', 'LeaveController@paternity_leave_list')->name('leave.paternity_leave_list');
				
				Route::get('/employee/time_update', 'EmployeeController@time_update')->name('employee.time_update');// multiple employee shift time change with employee code
				Route::get('/attendance/one-time-attendance-updated/updated', 'AttendanceController@one_time_attendance_updated')->name('attendance.one-time-attendance-updated.updated'); // multiple employee Attendance INSERTED with employee code
				Route::post('/attendance/store_attendance', 'AttendanceController@store_attendance')->name('attendance.store_attendance');
				
				
				//Batch HolidayController
				Route::get('/batch-holiday/edit/{id}', 'BatchHolidayController@edit')->name('batch_holiday.edit');  //Chetan
				Route::post('/batch-holiday/update/{id}', 'BatchHolidayController@update')->name('batch_holiday.update');  //Chetan
				Route::post('/batch-get-multi-location-wise-branch', 'BatchHolidayController@get_multi_locationwise_branch')->name('batch-get-multi-location-wise-branch');
				Route::get('/batch-holiday/delete/{id}', 'BatchHolidayController@destroy')->name('batch_holiday.delete');

				Route::prefix('course-planner')->name('course-planner.')->group(function () {
					Route::get('batch-reports', 'CoursePlannerController@batchReports')->name('batchReports');
					Route::get('issue-raise-reports', 'CoursePlannerController@issue_raise_reports')->name('issue-raise-reports');
					//Route::get('batchSubjectReports/{batch_id?}', 'CoursePlannerController@batchSubjectReports')->name('batchSubjectReports');
				});
				
				
				Route::get('academic/index', 'AcademicController@index')->name('academic.index');
				Route::get('academic/attendance', 'AcademicController@attendance')->name('academic.attendance');
				Route::post('get-coursewise-batch', 'AcademicController@get_coursewise_batch')->name('get-coursewise-batch');
				Route::get('academic/student', 'AcademicController@get_academic_student')->name('academic.get-academic-student');
				Route::post('academic/fee-chart', 'AcademicController@fee_chart')->name('academic.fee-chart');
				Route::post('academic/attendance-chart-mom', 'AcademicController@attendancechartmom')->name('academic.attendancechartmom');
				
				Route::get('/due-student-report-excel', 'AcademicController@due_student_report_excel')->name('due-student-report-excel');
				
				Route::get('/batch-subject-topic-pdf', 'BatchController@batch_subject_topic_pdf')->name('batch-subject-topic-pdf'); //dk
				
				Route::get('discount-category-role-add', 'DiscountRoleWiseController@discount_category_role_add')->name('discount-category-role-add');
				Route::post('store-discount-category-role', 'DiscountRoleWiseController@store_discount_category_role')->name('store-discount-category-role'); 
								
				Route::get('/academic-faculty-report', 'AcademicReportsController@index')->name('academic-faculty-report');
				Route::get('/prediction-report', 'AcademicReportsController@prediction')->name('prediction-report');
				Route::get('/faculty-utilization-dashboard', 'AcademicReportsController@faculty_utilization_dashboard')->name('faculty-utilization-dashboard');
				Route::get('/subject-utilization-dashboard', 'AcademicReportsController@subject_utilization_dashboard')->name('subject-utilization-dashboard');				
				Route::get('/faculty-plan', 'AcademicReportsController@faculty_plan')->name('faculty-plan');
				Route::get('/proposed-plan', 'AcademicReportsController@proposed_plan')->name('proposed-plan');
				
				//New Course Planner
				Route::prefix('multi-course-planner')->name('multi-course-planner.')->group(function () {
					Route::get('multi-planner-request', 'MultiCoursePlannerController@multiplannerrequest')->name('multi-planner-request');
					Route::get('planner-request-view', 'MultiCoursePlannerController@plannerrequestview')->name('planner-request-view');
					Route::post('save-planner-request', 'MultiCoursePlannerController@save_planner_request')->name('save-planner-request');
					Route::get('multi-planner-summary/{id}', 'MultiCoursePlannerController@multiplannersummary')->name('multi-planner-summary');
					Route::post('save-planner-summary', 'MultiCoursePlannerController@save_planner_summary')->name('save-planner-summary');
					
					Route::get('edit-multi-planner-request/{id?}', 'MultiCoursePlannerController@editmultiplannerrequest')->name('edit-multi-planner-request');
					
					Route::get('subject-assign/{id}', 'MultiCoursePlannerController@subject_assign')->name('subject-assign');
					
					Route::post('save-subject-assign', 'MultiCoursePlannerController@save_subject_assign')->name('save-subject-assign');
					Route::post('get-sub-topic', 'MultiCoursePlannerController@get_sub_topic')->name('get-sub-topic');					
					Route::post('show-work-status', 'MultiCoursePlannerController@show_work_status')->name('show-work-status');
					Route::post('upload-planner', 'MultiCoursePlannerController@upload_planner')->name('upload-planner');
					
					//Topic 
					Route::get('topic/{id?}', 'MultiCoursePlannerController@topic')->name('topic');
					Route::get('add-topic/{id}', 'MultiCoursePlannerController@add_topic')->name('add-topic');
					Route::post('save-topic-master', 'MultiCoursePlannerController@save_topic_master')->name('save-topic-master');
					Route::get('delete-topic/{id}', 'MultiCoursePlannerController@delete_topic')->name('delete-topic');
					Route::get('edit-topic/{id}/{subject_id}', 'MultiCoursePlannerController@edit_topic')->name('edit-topic');
					Route::post('upload-topics', 'MultiCoursePlannerController@upload_topics')->name('upload-topics');
					
					//Sub Topic
					Route::get('sub-topic/{id?}', 'MultiCoursePlannerController@sub_topic')->name('sub-topic');
					Route::get('add-sub-topic/{id}', 'MultiCoursePlannerController@add_sub_topic')->name('add-sub-topic');
					Route::post('save-sub-topic-master', 'MultiCoursePlannerController@save_sub_topic_master')->name('save-sub-topic-master');
					Route::get('delete-sub-topic/{id}', 'MultiCoursePlannerController@delete_sub_topic')->name('delete-sub-topic');
					Route::get('edit-sub-topic/{id}', 'MultiCoursePlannerController@edit_sub_topic')->name('edit-sub-topic');
					
				});
			}); 
		});
	});
}); 


Auth::routes();
Route::middleware(['auth'])->group(function () {
// Routes for Request Access System
Route::get('request-access','Admin\RequestAccessController@index')->name('request-access'); //sumit
Route::get('request-access/create','Admin\RequestAccessController@create')->name('request-access.create'); //sumit
Route::post('request-access/store','Admin\RequestAccessController@store')->name('request-access.store'); //sumit
Route::post('request-access/update', 'Admin\RequestAccessController@update')->name('request-access.update'); //sumit




//Routes for Software Management
Route::get('software-management','Admin\SoftwareManagementController@index')->name('software-management'); //sumit
Route::get('software-management/create','Admin\SoftwareManagementController@create')->name('software-management.create'); //sumit
Route::post('software-management/store','Admin\SoftwareManagementController@store')->name('software-management.store'); //sumit
Route::get('software-management/{id}/edit','Admin\SoftwareManagementController@edit')->name('software-management.edit'); //sumit
Route::put('software-management/{id}','Admin\SoftwareManagementController@update')->name('software-management.update'); //sumit
});

use App\Http\Controllers\Admin\CouponController;

Route::middleware(['auth'])->prefix('coupon')->name('coupon.')->group(function () {
    Route::get('/', [CouponController::class, 'index'])->name('index');
	Route::get('admin/assign', [CouponController::class, 'assign'])->name('assign');
	Route::post('/store', [CouponController::class, 'store'])->name('store');
	Route::get('/export', [CouponController::class, 'export'])->name('export');
});


/*Faculty Routes*/
Route::middleware(['auth', 'faculty'])->group(function () {
	Route::prefix('faculty')->group(function () {
		Route::name('faculty.')->group(function () {
			Route::namespace ('Faculty')->group(function () {
				Route::get('/', 'UserController@index')->name('index');
				Route::get('/dashboard', 'UserController@index')->name('dashboard');
				Route::resources([					
					'/timetable' => 'TimetableController',					
				]);

				Route::get('/profile', 'UserController@profile')->name('profile');
				Route::post('/profile/update', 'UserController@profile_update')->name('profile.update');

				Route::get('/password', 'UserController@change_password')->name('password');
				Route::post('/password/update', 'UserController@update_password')->name('password.update');

				Route::post('/reschedule/store', 'TimetableController@reschedule_store')->name('reschedule.store');
				Route::post('/cancelclass/store', 'TimetableController@cancelclass_store')->name('cancelclass.store');
			});
		});
	});
});


/*studioassistant Routes*/
Route::middleware(['auth', 'studioassistant'])->group(function () {
	Route::prefix('studioassistant')->group(function () {
		Route::name('studioassistant.')->group(function () {
			Route::namespace ('StudioAssistant')->group(function () {
				Route::get('/', 'UserController@index')->name('index');
				Route::get('/dashboard', 'UserController@index')->name('dashboard');
				Route::resources([					
					'/timetable' => 'TimetableController',
				]);

				Route::get('/profile', 'UserController@profile')->name('profile');
				Route::post('/profile/update', 'UserController@profile_update')->name('profile.update');

				Route::get('/password', 'UserController@change_password')->name('password');
				Route::post('/password/update', 'UserController@update_password')->name('password.update');

				Route::post('/startclass/store', 'TimetableController@store_class')->name('startclass.store');

				Route::post('/endclass/update', 'TimetableController@update_class')->name('endclass.update');
				
				Route::post('/get-course', 'TimetableController@get_course')->name('get-course');
				Route::post('/get-class-batch-subject', 'TimetableController@get_batch_subject')->name('get-class-batch-subject');
				Route::post('/get-chapter', 'TimetableController@get_chapter')->name('get-chapter');
				Route::post('/get-topic', 'TimetableController@get_topic')->name('get-topic');
				
				Route::post('/partially_end_class_data', 'TimetableController@partially_end_class_data')->name('partially_end_class_data');
			});
		});
	});
});

/*StudioManager Routes*/
Route::middleware(['auth', 'studiomanager'])->group(function () {
	Route::prefix('studiomanager')->group(function () {
		Route::name('studiomanager.')->group(function () {
			Route::namespace ('StudioManager')->group(function () {
				Route::get('/', 'UserController@index')->name('index');
				Route::get('/dashboard', 'UserController@index')->name('dashboard');
				Route::resources([					
					'/studios' => 'StudioController',					
					'/subjects' => 'SubjectController',
					'/chapters' => 'ChapterController',
					'/topics' => 'TopicController',
					'/course' => 'CourseController',
					'/batch' => 'BatchController',
					'/timetable' => 'TimeTableController',
					'/classchangerequest' => 'ClassChangeRequestController',
					'/reports' => 'ReportController',
					'/timetables' => 'TimetablesController',
					'/employees' => 'EmployeeController',
					'/enquiry' => 'EnquiryController',
					'/onlinecourses' => 'OnlineCoursesController',					
					'/support_category' => 'SupportCategoryController',
					'/support_user' => 'SupportUserController',
				]);

				Route::post('/getassistantexits', 'StudioController@getassistantexits')->name('getassistantexits');

				Route::post('/get-branchwise-assistant', 'StudioController@get_branchwise_assistant')->name('get-branchwise-assistant');

				Route::get('/course/delete/{id}', 'CourseController@destroy')->name('course.delete');
				Route::get('/subject/delete/{id}', 'SubjectController@destroy')->name('subject.delete');
				Route::get('/branch/delete/{id}', 'BranchController@destroy')->name('branch.delete');

				Route::post('/batch/update/{id}', 'BatchController@update')->name('batch.update'); 
				Route::post('/get-planner', 'BatchController@get_planner')->name('get-planner');
				
				Route::get('/course/chapter-topic-view/{id}', 'CourseController@chapter_topic_view')->name('course.chapter-topic-view');
				Route::post('/course/chapter-topic-update', 'CourseController@chapter_topic_update')->name('course.chapter-topic-update');
				
				Route::get('/batch/delete/{id}', 'BatchController@destroy')->name('batch.delete');
				Route::get('/batch-subject-topic-pdf', 'BatchController@batch_subject_topic_pdf')->name('batch-subject-topic-pdf'); //dk
				
				 
				Route::get('/chapter/delete/{id}', 'ChapterController@destroy')->name('chapter.delete');
				Route::get('/topic/delete/{id}', 'TopicController@destroy')->name('topic.delete');
				Route::get('/studio/delete/{id}', 'StudioController@destroy')->name('studio.delete');

				Route::post('/get-batch-subject', 'BatchController@get_batch_subject')->name('get-batch-subject');

				Route::post('/get-subject', 'TimeTableController@get_subject')->name('get-subject');
				
				//Testing new Work
				Route::get('/index-cm', 'TimeTableController@indexcm')->name('index-cm');
				Route::post('get-studio-by-branch-cm', 'TimeTableController@getStudioByBranchCm')->name('get-studio-by-branch-cm');
				
				Route::post('/get-subject-history', 'TimeTableController@get_subject_history')->name('get-subject-history');
				//End

				// Route::post('/get-chapter', 'TopicController@get_chapter')->name('get-chapter');

				// Route::post('/get-topic', 'TimeTableController@get_topic')->name('get-topic');

				Route::get('/profile', 'UserController@profile')->name('profile');
				Route::post('/profile/update', 'UserController@profile_update')->name('profile.update');

				Route::get('/password', 'UserController@change_password')->name('password');
				Route::post('/password/update', 'UserController@update_password')->name('password.update');

				Route::get('/reschedule/edit/{id}', 'ClassChangeRequestController@edit_reschedule')->name('reschedule.edit');
				Route::patch('/reschedule/update/{id}', 'ClassChangeRequestController@update_reschedule')->name('reschedule.update');

				Route::get('/swap/edit/{id}', 'ClassChangeRequestController@edit_swap')->name('swap.edit');

				Route::patch('/swap/update/{id}', 'ClassChangeRequestController@update_swap')->name('swap.update');

				Route::get('/cancelclass/edit/{id}', 'ClassChangeRequestController@edit_cancelclass')->name('cancelclass.edit');

				Route::patch('/cancelclass/update/{id}', 'ClassChangeRequestController@update_cancelclass')->name('cancelclass.update');
				
				Route::post('/get-batch', 'TimeTableController@get_batch')->name('get-batch');

				Route::post('/get-course', 'TimeTableController@get_course')->name('get-course');

				Route::post('/get-class-batch-subject', 'TimeTableController@get_batch_subject')->name('get-class-batch-subject');

				Route::post('/get-remark', 'TimeTableController@get_remark')->name('get-remark');

				Route::post('/get-chapter', 'TimeTableController@get_chapter')->name('get-chapter');

				Route::post('/get-topic', 'TimeTableController@get_topic')->name('get-topic');
				
				Route::post('/get-swap-faculty', 'TimeTableController@get_swap_faculty')->name('get-swap-faculty');

				Route::post('/get-swap-faculty-timetable', 'TimeTableController@get_swap_faculty_timetable')->name('get-swap-faculty-timetable');

				Route::get('/timetables/export', 'TimeTableController@timetable_export')->name('timetables.export');

				Route::post('/export/data', 'TimeTableController@export_data')->name('export.data');
				
				Route::post('/reschedule/store', 'TimeTableController@reschedule_store')->name('reschedule.store');
				Route::post('/cancelclass/store', 'TimeTableController@cancelclass_store')->name('cancelclass.store');
				Route::post('/swap/store', 'TimeTableController@swap_store')->name('swap.store');
				Route::post('/get-faculty', 'TimeTableController@get_faculty')->name('get-faculty'); //dk
				
				Route::get('/courses/import', 'CourseController@import')->name('courses.import');//dk
				Route::post('/courses/import/store', 'CourseController@import_store')->name('courses.import.store');//dk
				Route::get('/course/status/{id}', 'CourseController@togglePublish')->name('course.status'); //dk
				Route::get('/courses/download_sample', 'CourseController@download_sample')->name('courses.download_sample');// dk
				Route::get('/course/view/{id}', 'CourseController@view')->name('course.view');//dk
				Route::get('/course/export_csv/{id}', 'CourseController@export_csv')->name('course.export_csv');// dk
				Route::post('/subject/report', 'ReportController@subject_report')->name('subject.report');//dk
				Route::post('/report/export', 'ReportController@report_export')->name('report.export');//dk
				Route::get('/studio-reports', 'StudioReportsController@index')->name('studio-reports'); //dk
				Route::get('/studio-report-pdf', 'StudioReportsController@download_pdf')->name('studio-report-pdf'); //dk
				Route::get('/faculty-reports', 'FacultyReportsController@index')->name('faculty-reports'); //dk
				Route::get('/timetable-history-reports', 'FacultyReportsController@timetable_history_reports')->name('timetable-history-reports'); 
				Route::get('/faculty-report-pdf', 'FacultyReportsController@download_pdf')->name('faculty-report-pdf'); //dk
				Route::get('/subject-reports', 'SubjectReportsController@index')->name('subject-reports'); //dk
				Route::get('/subject-report-excel', 'SubjectReportsController@download_excel')->name('subject-report-excel'); //dk
				Route::get('/free-faculty-reports', 'FreeFacultyReportsController@index')->name('free-faculty-reports'); //ak
				Route::get('/free-faculty-report-excel', 'FreeFacultyReportsController@download_excel')->name('free-faculty-report-excel'); //ak
				Route::get('/free-assistant-reports', 'FreeAssistantReportsController@index')->name('free-assistant-reports'); //ak
				Route::get('/free-assistant-report-excel', 'FreeAssistantReportsController@download_excel')->name('free-assistant-report-excel'); //ak
				Route::get('/typist-work-report', 'TypistReportsController@index')->name('typist-work-report'); //ak
				Route::get('/typist-work-report-excel', 'TypistReportsController@download_excel')->name('typist-work-report-excel'); //ak
				Route::post('/get-branchwise-studio', 'BranchController@get_branchwise_studio')->name('get-branchwise-studio'); //dk
				Route::post('/get-branchwise-faculty', 'BranchController@get_branchwise_faculty')->name('get-branchwise-faculty'); //dk
				Route::post('/get-batch-subject-data', 'BatchController@get_batch_subject_data')->name('get-batch-subject-data');//dk
				Route::get('/batch/view/{id?}', 'BatchController@view')->name('batch.view');//dk
				Route::get('/subject/status/{id}', 'SubjectController@togglePublish')->name('subject.status'); //dk
				Route::get('/chapter/status/{id}', 'ChapterController@togglePublish')->name('chapter.status'); //dk
				Route::get('/topics/status/{id}', 'TopicController@togglePublish')->name('topics.status'); //dk
				Route::post('/get-remark', 'TimeTableController@get_remark')->name('get-remark');
				
				Route::get('/studios/status/{id}', 'StudioController@togglePublish')->name('studios.status'); //dk
				Route::post('/timetable/delete_class', 'TimeTableController@delete_class')->name('timetable.delete_class'); //dk
				Route::get('/faculty-reports/subjects', 'FacultyReportsController@subjects')->name('faculty-reports.subjects'); //dk
				
				Route::post('/get-topic-by-chapter', 'TimeTableController@get_topic_by_chapter')->name('get-topic-by-chapter');
				Route::post('/get-chapter-and-topic', 'TimeTableController@get_chapter_and_topic')->name('get-chapter-and-topic'); //dk
				Route::post('/get-data-by-topic', 'TimeTableController@get_data_by_topic')->name('get-data-by-topic');
				Route::post('/get-batch-by-subject', 'TimeTableController@get_batch_by_subject')->name('get-batch-by-subject');
				Route::post('/edit-online-store', 'TimeTableController@edit_online_store')->name('timetable.edit-online-store');
				Route::post('/get-studio-edit', 'TimeTableController@get_studio_edit')->name('get-studio-edit');
				
				//Start/End Class
				Route::post('/startclass/store', 'TimetablesController@store_class')->name('startclass.store');
				Route::post('/endclass/update', 'TimetablesController@update_class')->name('endclass.update');
				Route::post('/partially_end_class_data', 'TimetablesController@partially_end_class_data')->name('partially_end_class_data'); //dk
				
				Route::post('/import-chapter', 'BatchController@import_chapter')->name('import-chapter');
				Route::post('get-studio-by-branch', 'TimeTableController@getStudioByBranch')->name('get-studio-by-branch');
				Route::post('delete-timetable', 'TimeTableController@destroy')->name('delete-timetable');
				Route::post('edit-timetable', 'TimeTableController@update')->name('edit-timetable');
				//Route::get('copy-timetable/{date}', 'TimeTableController@copyTimetable')->name('copy-timetable');
				Route::post('copy-timetable', 'TimeTableController@copyTimetable')->name('copy-timetable');
				Route::post('get-studio', 'TimetablesController@get_studio')->name('get-studio');
				Route::post('get-obs-studio', 'TimeTableController@get_obs_studio')->name('get-obs-studio');
				
				Route::post('edit-start-class', 'FacultyReportsController@editStartClass')->name('edit-start-class');
				Route::post('update-start-class', 'FacultyReportsController@updateStartClass')->name('update-start-class');
				Route::get('/faculty-hours-reports', 'FacultyHoursReportsController@index')->name('faculty-hours-reports');
				Route::get('/faculty-hours-report-pdf', 'FacultyHoursReportsController@download_pdf')->name('faculty-hours-report-pdf');
				
				Route::get('/faculty-agreement-hours', 'FacultyHoursReportsController@agreement_hours')->name('faculty-agreement-hours'); //dk
				Route::get('/faculty-agreement-hours-report-excel', 'FacultyHoursReportsController@agreement_download_excel')->name('faculty-agreement-hours-report-excel'); //dk
				
				Route::get('/employee/delete/{id}', 'EmployeeController@destroy')->name('employee.delete');
				Route::get('/employee/view/{id}', 'EmployeeController@show')->name('employees.view');
				Route::get('/employee/status/{id}', 'EmployeeController@togglePublish')->name('employee.status');
				Route::get('/employee/edit/{id}', 'EmployeeController@edit')->name('employee.edit');
				Route::post('/employee/update/{id}', 'EmployeeController@update')->name('employee.update');
				Route::post('/employee/get_leave_month', 'EmployeeController@get_leave_month')->name('employee.get_leave_month');
				Route::get('/employee/approval/{id}', 'EmployeeController@approval')->name('employees.approval');//dk
				Route::post('/employee/approval_update/{id}', 'EmployeeController@approval_update')->name('employees.approval_update');
				Route::post('/employee/status-by-reason', 'EmployeeController@statusByReason')->name('employee.status-by-reason');
				Route::post('/employee/get-branch', 'EmployeeController@getBranch')->name('employee.get-branch'); //Chetan
				
				Route::post('/get-location-wise-branch', 'StudioReportsController@get_locationwise_branch')->name('get-location-wise-branch');
				Route::post('branch-wise-timetable', 'TimeTableController@branch_wise_timetable')->name('branch-wise-timetable'); //pr
				Route::post('publish-timetable', 'TimeTableController@publish_timetable')->name('publish-timetable');
				
				Route::get('incomplete-attendence', 'AttendanceController@incompleteattendence')->name('attendance.incompleteattendence');  
				Route::get('incomplte-attendence-detail', 'AttendanceController@incomplteAttendanceDetail')->name('attendance.incomplte-attendence-detail');
				
				Route::get('/batch-reports', 'BatchReportsController@index')->name('batch-reports'); 
				Route::get('/batch-report-pdf', 'BatchReportsController@download_pdf')->name('batch-report-pdf');
				
				Route::get('/employee-report-excel', 'EmployeeController@download_excel')->name('employee-report-excel');
				Route::get('/batch-report-report-excel', 'BatchReportsController@download_excel')->name('batch-report-report-excel');
				Route::get('/studio-report-report-excel', 'StudioReportsController@download_excel')->name('studio-report-report-excel');
				
				Route::get('/faculty-hours-report-excel', 'FacultyHoursReportsController@download_excel')->name('faculty-hours-report-excel');
				
				Route::get('/batch-reports-shiftwise', 'BatchReportsController@batchReportsShiftwise')->name('batch-reports-shiftwise');
				Route::get('/batch-report-shiftwise-pdf', 'BatchReportsController@download_pdf_shiftwise')->name('batch-report-shiftwise-pdf');
				
				Route::get('/links/faculty', 'LinksController@index')->name('links.faculty'); //DK
				Route::post('/links/faculty_link', 'LinksController@faculty_link')->name('links.faculty_link'); //DK
				Route::post('/links/manager_link', 'LinksController@manager_link')->name('links.manager_link'); //DK
				Route::post('/links/assistant_link', 'LinksController@assistant_link')->name('links.assistant_link'); //DK
				Route::post('/links/driver_link', 'LinksController@driver_link')->name('links.driver_link'); //DK
				Route::get('/links/all_send/{id}', 'LinksController@all_send')->name('links.all_send'); //DK
				
				
				
				Route::post('/get-class-batch-subject-by-faculty', 'TimeTableController@get_batch_subject_by_faculty')->name('get-class-batch-subject-by-faculty');
				
				Route::get('/faculty-early-delay-reports', 'FacultyEarlyDelayReportsController@index')->name('faculty-early-delay-reports');
				Route::get('/faculty-early-delay-report-excel', 'FacultyEarlyDelayReportsController@download_excel')->name('faculty-early-delay-report-excel');
				
				Route::post('update-cancel-class', 'FacultyReportsController@updateCancelClass')->name('update-cancel-class');
				
				Route::get('/chapter/import', 'ChapterController@import')->name('chapter.import');
				Route::post('/chapter/import/store', 'ChapterController@import_store')->name('chapter.import.store');

				Route::get('/studio-availability', 'BatchReportsController@studio_availability')->name('studio-availability'); //pr

				Route::post('/get-course-list-by-mobile', 'OnlineCoursesController@get_course_list_by_mobile')->name('get-course-list-by-mobile');
				Route::post('/delete-course', 'OnlineCoursesController@delete_course')->name('delete-course');
				
				Route::get('/batch-hours-reports', 'BatchReportsController@batch_hours_reports')->name('batch-hours-reports'); 
				Route::get('/batch-hours-reports-new', 'BatchReportsController@batch_hours_reports_new')->name('batch-hours-reports-new'); 
				
				Route::get('/batch-test-report', 'TestReportController@batch_test_report')->name('batch-test-report'); //dk
				Route::post('/batch_subject_status_update', 'BatchController@batch_subject_status_update')->name('batch_subject_status_update'); //DK
				Route::get('faculty-leave', 'FacultyLeaveController@faculty_leave')->name('faculty-leave');
				Route::get('faculty-leave-add', 'FacultyLeaveController@faculty_leave_add')->name('faculty-leave-add');
				Route::post('faculty_leave_add_save', 'FacultyLeaveController@faculty_leave_add_save')->name('faculty_leave_add_save');
				Route::post('/faculty_leave_update', 'FacultyLeaveController@faculty_leave_update')->name('faculty_leave_update'); //DK
				Route::get('faculty-leave-download', 'FacultyLeaveController@faculty_leave_download')->name('faculty-leave-download');
				
				Route::get('leave-full-detail', 'LeaveController@leave_full_detail')->name('leave.leave-full-detail'); // dk
				Route::post('/leave-full-detail-history', 'LeaveController@leave_full_detail_history')->name('leave-full-detail-history'); //DK
				
				
				
				 
				//Student Batch Inventory And Attendance
				Route::get('/batchinventory/index', 'BatchInventoryController@index')->name('batchinventory.index'); 
				Route::get('/batchinventory/add', 'BatchInventoryController@add')->name('batchinventory.add'); 
				Route::post('/batchinventory/save', 'BatchInventoryController@save')->name('batchinventory.save');
				Route::get('/batchinventory/edit/{id}', 'BatchInventoryController@edit')->name('batchinventory.edit');
				Route::post('/batchinventory/update/{id}', 'BatchInventoryController@update')->name('batchinventory.update');
				Route::get('/batchinventory/delete/{id}', 'BatchInventoryController@destroy')->name('batchinventory.delete');				 
				Route::get('/attendance-dashboard', 'BatchInventoryController@attendance_dashboard')->name('attendance-dashboard'); 
				Route::get('/student-attendance-view/{id?}/{fdate?}/{tdate?}/{type?}', 'BatchInventoryController@student_attendance_view')->name('student-attendance-view'); 
				Route::get('/inventory-dashboard', 'BatchInventoryController@inventory_dashboard')->name('inventory-dashboard'); 
				Route::post('/get-batch', 'BatchInventoryController@getBatch')->name('get-batch'); //Chetan
				Route::get('/student-inventory-view/{batch_id?}/{id?}/{type?}', 'BatchInventoryController@student_inventory_view')->name('student-inventory-view'); 
				Route::post('/studentSearch', 'BatchInventoryController@studentSearch')->name('batchinventory.studentSearch'); //pc
				
			
				
				//Support 
				Route::post('/edit-support-category', 'SupportCategoryController@edit_category')->name('edit-support-category');
				Route::get('/support_category/delete/{id}', 'SupportCategoryController@destroy')->name('support_category.delete');
				Route::get('/support_category/status/{id}', 'SupportCategoryController@togglePublish')->name('support_category.status');
				
				Route::post('/edit-support-user', 'SupportUserController@edit_support_user')->name('edit-support-user');
				Route::get('/support_user/delete/{id}', 'SupportUserController@destroy')->name('support_user.delete');
				Route::get('/support_user/status/{id}', 'SupportUserController@togglePublish')->name('support_user.status');
				
				Route::post('store-enquiry-description', 'EnquiryController@store_enquiry_description')->name('store-enquiry-description');
				
				Route::get('support-dashboard', 'SupportCategoryController@support_dashboard')->name('support-dashboard');
				Route::get('support-enquiry', 'SupportCategoryController@enquiry')->name('support-enquiry');				
				Route::post('support-enquiryReply', 'SupportCategoryController@enquiryReply')->name('support-enquiryReply');

				Route::get('forum-dashboard', 'ForumController@forumDashboard')->name('forum-dashboard');
				Route::post('/get-old-query', 'EnquiryController@get_old_query')->name('get-old-query');
				Route::get('/enquiry/destroy/{id}', 'EnquiryController@destroy')->name('enquiry.destroy');
				Route::post('store-enquiry-status', 'EnquiryController@store_enquiry_status')->name('store-enquiry-status');
				
				Route::get('paternity_leave', 'LeaveController@paternity_leave')->name('leave.paternity_leave');
				Route::post('store-paternity-leave', 'LeaveController@storPaternityLeave')->name('store-paternity-leave');
				Route::get('paternity-leave-list', 'LeaveController@paternity_leave_list')->name('leave.paternity_leave_list');
				
				
				Route::get('/faculty-batch-reports', 'BatchReportsController@faculty_batch_reports')->name('faculty-batch-reports'); 
				Route::get('/faculty-topic', 'FacultyReportsController@faculty_topic')->name('faculty-topic');
				
			});
		});
	});
});




// Route::get('/sitemap.xml', 'SitemapController@index');
// Route::get('/rss.xml', 'HomeController@rss_feed');
// Route::get('/googlenewssitemap.xml', 'SitemapController@googlenewssitemap');
// Route::get('/news-sitemap-index.xml', 'SitemapController@articles_sitemap_index');
// Route::get('/categories-sitemap-index.xml', 'SitemapController@categories_sitemap_index');

Route::get('/', 'HomeController@index');
Route::get('/about-us', 'HomeController@about_us')->name('about-us');
Route::post('/checkUser', 'HomeController@check_user')->name('checkUser');;

// Route::get('/about-us', 'HomeController@about_us')->name('about-us');
// Route::get('/contact-us', 'HomeController@contact_us')->name('contact-us');
// Route::get('/team', 'HomeController@team')->name('team');
// Route::get('/join-us', 'HomeController@join_us')->name('join-us');
// Route::get('/privacy', 'HomeController@privacy')->name('privacy');
// Route::get('/terms', 'HomeController@terms')->name('terms');
// Route::get('/legal-info', 'HomeController@legal_info')->name('legal-info');
// Route::get('/topic/{slug}', 'HomeController@show_tags')->name('topic');
// Route::get('/user/{slug}', 'HomeController@show_user_news')->name('user');
// Route::get('/{slug1}/{slug2}', 'HomeController@news_detail')->name('blog.single');

/**
 * @var BlogCategory Routes
 */
// $categories = App\Category::select('slug')->where('status', '1')->get();
// if ($categories) {
// 	foreach ($categories as $key => $value) {
// 		Route::get($value->slug, 'HomeController@show_category')->name($value->slug);
// 	}
// }
