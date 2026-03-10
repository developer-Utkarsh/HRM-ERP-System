<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
date_default_timezone_set('Asia/Kolkata');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
}); 

// schedule, getrescheduleclass, scheduleswap, getcancelclass

Route::post('/users/check/mobile', 'UsersController@checkMobile');
Route::post('/users/faculty/schedule', 'UsersController@getFacultySchedule'); // Faculty TimeTable
Route::post('/users/faculty/links', 'UsersController@links'); // tt for faculty timetable

Route::post('/users/pw-uc-employee-add', 'UsersController@pw_uc_employee_add'); // tt for faculty timetable
Route::post('/users/pw-uc-employee-inactive', 'UsersController@pw_uc_employee_inactive'); // tt for faculty timetable


Route::post('/users/active-mobile-email', 'UsersController@checkMobile_email'); //Active user check 
Route::post('/users/active-username-password', 'UsersController@checkUsername_password'); //Active user check 

Route::post('/users/allnotifications', 'UsersController@getAllNotification');

Route::post('/users/profile/update', 'UsersController@ProfileUpdate');
Route::post('/users/timtable_duration_update', 'TimetableController@timtable_duration_update');
Route::post('/users/faculty/dashboard', 'UsersController@Dashboard');
Route::post('/users/studio-assistant/dashboard', 'UsersController@Dashboard');//dk
Route::post('/users/studio-manager/dashboard', 'UsersController@Dashboard'); //dk
Route::post('/users/super-admin/dashboard', 'UsersController@Dashboard'); //dk

Route::post('/users/faculty/reschedule', 'ClassRequestController@facultyreschedule');
Route::post('/users/faculty/getrescheduleclass', 'ClassRequestController@getFacultyRescheduleClass');

Route::get('/users/getfaculty', 'ClassRequestController@getFaculty');
Route::post('/users/getsinglefacultyschedule', 'ClassRequestController@getSingleFacultySchedule');

Route::post('/users/faculty/swap', 'ClassRequestController@swap');
Route::post('/users/faculty/scheduleswap', 'ClassRequestController@scheduleswap');

Route::post('/users/faculty/cancelclass', 'ClassRequestController@cancelclass');
Route::post('/users/faculty/getcancelclass', 'ClassRequestController@GetCancelClass');

Route::post('/users/facluty/class/report', 'ReportController@getFacultyClassReport');

Route::post('/users/get/profile', 'UsersController@getProfile'); //dk


//Old Task API - Dinesh Sir
Route::post('/users/get_task', 'TaskController@get_task'); //dk
Route::post('/users/update_task', 'TaskController@update_task'); //dk
Route::post('/users/add_task', 'TaskController@add_task'); //dk
Route::post('/users/delete_task', 'TaskController@delete_task'); //dk
Route::post('/users/delete_task_ios', 'TaskController@delete_task_ios'); //dk

//New Task API - Padama Ram Ji Sir
Route::post('/users/new_add_task', 'TaskController@new_add_task'); //PR  
Route::post('/users/new_update_task', 'TaskController@new_update_task'); //PR
Route::post('/users/new_delete_task', 'TaskController@new_delete_task'); //PR
Route::post('/users/new_get_task', 'TaskController@new_get_task'); //PR


//New Task API - Chetan
Route::post('/users/hrm_add_task', 'GetTaskController@hrm_add_task'); 
Route::post('/users/hrm_update_task', 'GetTaskController@hrm_update_task');
Route::post('/users/hrm_delete_task', 'GetTaskController@hrm_delete_task'); 
Route::post('/users/hrm_get_task', 'GetTaskController@hrm_get_task'); 
Route::post('/users/hrm_task_history', 'GetTaskController@hrm_task_history');
Route::post('/users/update_spent_hour', 'GetTaskController@update_spent_hour');
Route::post('/users/hrm_status_update', 'GetTaskController@hrm_status_update');
Route::post('/users/hrm_user_list', 'GetTaskController@hrm_user_list');
Route::post('/users/hrm_get_department', 'GetTaskController@hrm_get_department');
//End


Route::post('/users/task_history', 'TaskController@task_history'); //Ak
Route::post('/users/task_history_detail', 'TaskController@task_history_detail'); //Ak

Route::post('/users/getemployeelist', 'UsersController@getemployeelist'); //dk
Route::post('/users/getemployee', 'UsersController@getemployee'); //Ak

Route::post('/users/add_leave', 'LeaveController@add_leave'); //dk
Route::post('/users/get_leave', 'LeaveController@get_leave'); //dk
Route::post('/users/update_leave', 'LeaveController@update_leave'); //dk
Route::post('/users/delete_leave', 'LeaveController@delete_leave'); //dk

Route::post('/users/add_attendance', 'AttendanceController@add_attendance'); //dk
Route::post('/users/get_attendance', 'AttendanceController@get_attendance'); //dk

Route::post('/users/get_open_task', 'TaskController@get_open_task'); //dk
Route::post('/users/update_leave_status', 'LeaveController@update_leave_status'); //dk

Route::post('/users/get_birthdays', 'BirthdaysController@get_birthdays'); //dk

Route::post('/users/add_typist_work_report', 'TypistworkreportController@add_typist_work_report'); //dk
Route::post('/users/get_typist_work_report', 'TypistworkreportController@get_typist_work_report'); //dk
Route::post('/users/delete_typist_work_report', 'TypistworkreportController@delete_typist_work_report'); //dk
Route::post('/users/AppVersion', 'AppVersion@getVersion'); //padmaramji
Route::post('/users/master/master_data', 'MasterController@master_data'); //Ak
Route::post('/users/timetable/class_start_end', 'TimetableController@class_start_end'); //DK
Route::post('/users/studio/get_list', 'StudioController@get_list'); //DK
Route::post('/users/studio/get_slot_available', 'StudioController@get_slot_available'); //Ak
Route::get('/users/cron/cron_job', 'CronJobController@cron_job'); //Ak
Route::post('/users/send_wish', 'BirthdaysController@send_wish'); //Ak

Route::post('/users/add_staff_movement_system', 'StaffmovementsystemController@add_staff_movement_system'); //dk
Route::post('/users/get_staff_movement_system', 'StaffmovementsystemController@get_staff_movement_system'); //dk
Route::post('/users/update_staff_movement_system', 'StaffmovementsystemController@update_staff_movement_system'); //dk
Route::post('/users/delete_staff_movement_system', 'StaffmovementsystemController@delete_staff_movement_system'); //dk

Route::post('/users/faculty/update_reschedule', 'ClassRequestController@update_reschedule');

Route::post('/users/salary', 'SalaryController@salary');
Route::post('/users/add-attendence', 'SalaryController@addAttendence');

Route::post('/users/biometric-attendence', 'AttendanceController@biometricttendence');
Route::post('/users/manual-biometric-attendence', 'AttendanceController@manual_biometricttendence');
Route::post('/leave/manual_get_pending_leave', 'LeaveController@manual_get_pending_leave'); //  for minus leaves get testing

Route::post('/users/get-timetable-by-batch', 'UsersController@get_timetable_by_batch'); //  OffLine App in show timetable
Route::post('/users/get-timetable-by-batch-dk', 'UsersController@get_timetable_by_batch_dk'); //  OffLine App in show timetable
Route::post('/users/get-student-attendance', 'UsersController@get_student_attendance'); //  OffLine App student attendance
Route::post('/users/get-student-inventory', 'UsersController@get_student_inventory'); //  OffLine App GET Inventory 

Route::post('/users/get_attendance_rfid', 'AttendanceController@get_attendance_rfid');
Route::post('/users/employee-asset', 'AssetController@employeeAsset');

Route::post('/users/model-paper/model-paper-list', 'ModelPaperController@modelPaperList');
Route::post('/users/model-paper/sub-model-paper-list', 'ModelPaperController@subModelPaperList'); 

Route::get('/users/cron/not-punch-leave', 'CronController@notPunchLeave');
Route::get('/users/cron/first-half-leave', 'CronController@firstHalfLeave');
Route::get('/users/cron/not-punch-out-yesterday', 'CronController@notPunchOutYesterday');
Route::get('/users/cron/not-punch-leave-yesterday', 'CronController@notPunchLeaveYesterday');
Route::get('/users/cron/not-add-task', 'CronController@notAddTask');
Route::get('/users/cron/not-add-task-yesterday', 'CronController@notAddTaskYesterday');

Route::post('/users/model-paper/upload-remark', 'ModelPaperController@uploadRemark');
Route::post('/users/model-paper/upload-status', 'ModelPaperController@uploadStatus');

Route::post('/users/user-string-update', 'UsersController@userStringCheck');

Route::post('/users/meetings', 'MeetingController@index');

Route::post('/users/studio-assistant/dashboard_new', 'UsersController@dashboard_new');//dk - Studio Assistant TimeTable


Route::post('/users/leave_types', 'LeaveController@leave_types'); //dk
Route::post('/users/leave_types_for_approval', 'LeaveController@leave_types_for_approval'); //dk

Route::get('/users/cron/send_faculty_link', 'CronJobController@send_faculty_link'); //DK
Route::get('/users/cron/send_manager_link', 'CronJobController@send_manager_link'); //DK
Route::get('/users/cron/send_assistant_link', 'CronJobController@send_assistant_link'); //DK
Route::get('/users/cron/send_driver_link', 'CronJobController@send_driver_link'); //DK
Route::get('/users/cron/update_manual_send_link', 'CronJobController@update_manual_send_link'); //DK
Route::get('/users/cron/faculty_leave', 'CronJobController@faculty_leave'); //DK cron time evening 8PM
Route::get('/users/cron/earn-leave', 'CronJobController@earn_leave');

Route::get('/users/is_extra_working_salary', 'CronJobController@is_extra_working_salary');

Route::post('/users/leave_types_all', 'LeaveController@leave_types_all'); //dk
Route::post('/users/get-kb-category', 'KnowledgeBasedController@getKbCategory');
Route::post('/users/add-knowledge-based', 'KnowledgeBasedController@addKnowledgeBased'); 
Route::post('/users/get-knowledge-based', 'KnowledgeBasedController@getKnowledgeBased'); 

Route::post('/users/get-training-category', 'KnowledgeBasedController@getTrainingCategory'); 
Route::post('/users/get-training-video', 'KnowledgeBasedController@getTrainingVideo'); 
Route::post('/users/get-all-users', 'UsersController@get_all_users');
Route::post('/users/get-meeting-places', 'UsersController@get_meeting_places');

Route::post('/users/add-appointment', 'AppointmentController@add_appointment');
Route::post('/users/appointment-list', 'AppointmentController@appointment_list');
Route::post('/users/appointment-status', 'AppointmentController@appointment_status');
Route::post('/users/edit-appointment', 'AppointmentController@edit_appointment');
Route::post('/users/branch-list', 'AppointmentController@branch_list');
Route::post('/users/get-appointment-group', 'AppointmentController@get_appointment_group');
Route::post('/users/add-key-points', 'AppointmentController@add_key_points');
Route::post('/users/get-attendees', 'AppointmentController@get_attendees');
Route::post('/users/appointment-details', 'AppointmentController@get_appointment_details');
Route::post('/users/appointment-group-destroy', 'AppointmentController@group_destroy');
				

Route::post('/users/edit-knowledge-based', 'KnowledgeBasedController@editKnowledgeBased'); 
Route::post('/users/get-anniversary', 'AnniversaryController@get_anniversary'); 
Route::post('/users/send-anniversary', 'AnniversaryController@send_anniversary');

Route::post('/users/get-expense-category', 'ExpenseController@getExpenseCategory');
Route::post('/users/add-expense', 'ExpenseController@addExpense');
Route::post('/users/edit-expense', 'ExpenseController@editExpense');
Route::post('/users/delete-expense', 'ExpenseController@deleteExpense');
Route::post('/users/expense-list', 'ExpenseController@expenseList');

Route::post('/users/appraisal/questions', 'AppraisalController@questions'); //DK
Route::post('/users/appraisal/questions_submit', 'AppraisalController@questions_submit'); //DK
Route::post('/users/appraisal/user_list', 'AppraisalController@user_list'); //DK
Route::post('/users/appraisal/edit', 'AppraisalController@edit'); //DK
Route::post('/users/appraisal/need_discussed', 'AppraisalController@need_discussed'); //DK
Route::post('/users/appraisal/edit_submit', 'AppraisalController@edit_submit'); //DK
Route::post('/users/appraisal/employee_accept', 'AppraisalController@employee_accept'); //DK

Route::get('/users/cron/auto_reject_leave', 'CronJobController@auto_reject_leave');  
Route::get('/users/cron/auto_reject_leave_with_date/{date}', 'CronJobController@auto_reject_leave_with_date');  

Route::post('/users/update-user-record', 'UsersController@updateUserRecord');
Route::post('/users/studio/studio_reports', 'StudioController@studio_reports');

Route::get('/users/all-reports', 'AllReportsController@all_reports_link');

Route::prefix('erp')->name('erp.')->group(function () {
  Route::post('/subject',          'ErpContentCtr@subject');
  //http://hrm-stag.utkarshupdates.com/index.php/api/erp/chapter-push-erp
  Route::get('/chapter-push-erp', 'ErpContentCtr@chapter_push_erp');
  Route::post('/timetable-course-sync', 'ErpContentCtr@timetable_course_sync');
  Route::post('/timetable-topic-check', 'ErpContentCtr@timetable_topic_check');


  Route::get('/approvel-list',      'ErpContentCtr@approvelList');
  Route::post('/discount-approvel', 'ErpContentCtr@discountApprovel');
  Route::post('/approvel-check',    'ErpContentCtr@approvelCheck');
});


Route::prefix('crm')->name('crm.')->group(function () {
  Route::any('/mcube','CrmCtr@mcube');
});

Route::get('/master/branches', 'MasterController@branches');

Route::get('/topicSelectionReminder','CronFacultyCtr@topicSelectionReminder');

