<?php

use Illuminate\Support\Facades\Route;

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

Route::get('logout', 'Controller@userLogout')->name('logout');

/*Admin Routes*/
Route::middleware(['auth', 'admin'])->group(function () {
	Route::prefix('admin')->group(function () {
		Route::name('admin.')->group(function () {
			Route::namespace ('Admin')->group(function () {
				Route::get('/', 'AdminController@index')->name('index');
				Route::get('/dashboard', 'AdminController@index')->name('dashboard');
				Route::resources([					
					'/roles' => 'RolesController',
					'/branch' => 'BranchController',
					'/employees' => 'EmployeeController',
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
				]);

				Route::get('/profile', 'AdminController@profile')->name('profile');
				Route::post('/profile/update', 'AdminController@profile_update')->name('profile.update');

				Route::get('/password', 'AdminController@change_password')->name('password');
				Route::post('/password/update', 'AdminController@update_password')->name('password.update');

				Route::get('/role/delete/{id}', 'RolesController@destroy')->name('role.delete');
				Route::get('/employee/delete/{id}', 'EmployeeController@destroy')->name('employee.delete');
				Route::get('/studio/delete/{id}', 'StudioController@destroy')->name('studio.delete');
				Route::get('/notifications/delete/{id}', 'NotificationController@destroy')->name('notifications.delete');

				Route::get('/course/delete/{id}', 'CourseController@destroy')->name('course.delete');
				Route::get('/subject/delete/{id}', 'SubjectController@destroy')->name('subject.delete');
				Route::get('/branch/delete/{id}', 'BranchController@destroy')->name('branch.delete');
				Route::get('/batch/delete/{id}', 'BatchController@destroy')->name('batch.delete');
				Route::get('/chapter/delete/{id}', 'ChapterController@destroy')->name('chapter.delete');
				Route::get('/topic/delete/{id}', 'TopicController@destroy')->name('topic.delete');

				Route::get('/courses/import', 'CourseController@import')->name('courses.import');

				Route::post('/courses/import/store', 'CourseController@import_store')->name('courses.import.store');

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
				Route::get('/task/view/{id}', 'TaskController@view')->name('task.view');//dk
				Route::get('/task/task_detail_delete/{task_id}/{task_detail_id}', 'TaskController@task_detail_delete')->name('task.task_detail_delete');//dk
				Route::get('/task/task_delete/{task_id}', 'TaskController@task_delete')->name('task.task_delete');//dk
				Route::get('/task/edit/{task_id}', 'TaskController@edit')->name('task.edit');//dk
				Route::patch('/task/update/{task_id}', 'TaskController@update')->name('task.update');//dk
				Route::get('/task/task_history/{task_id}/{task_detail_id}', 'TaskController@task_history')->name('task.task_history');//dk
				Route::get('/task/create', 'TaskController@create')->name('task.create');//dk
				Route::post('/get-branchwise-employee', 'TaskController@get_branchwise_employee')->name('get-branchwise-employee'); //dk
				Route::post('/get-batch-subject-data', 'BatchController@get_batch_subject_data')->name('get-batch-subject-data');//dk
				Route::get('/batch/view/{id}', 'BatchController@view')->name('batch.view');//dk
				Route::post('/get-branchwise-studio', 'BranchController@get_branchwise_studio')->name('get-branchwise-studio'); //dk
				Route::post('/get-branchwise-faculty', 'BranchController@get_branchwise_faculty')->name('get-branchwise-faculty'); //dk
				Route::get('/faculty-reports', 'FacultyReportsController@index')->name('faculty-reports'); //dk
				Route::get('/faculty-report-pdf', 'FacultyReportsController@download_pdf')->name('faculty-report-pdf'); //dk
			});
		});
	});
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
					'/timetable' => 'TimetableController',
					'/classchangerequest' => 'ClassChangeRequestController',
				]);

				Route::post('/getassistantexits', 'StudioController@getassistantexits')->name('getassistantexits');

				Route::post('/get-branchwise-assistant', 'StudioController@get_branchwise_assistant')->name('get-branchwise-assistant');

				Route::get('/course/delete/{id}', 'CourseController@destroy')->name('course.delete');
				Route::get('/subject/delete/{id}', 'SubjectController@destroy')->name('subject.delete');
				Route::get('/branch/delete/{id}', 'BranchController@destroy')->name('branch.delete');
				Route::get('/batch/delete/{id}', 'BatchController@destroy')->name('batch.delete');
				Route::get('/chapter/delete/{id}', 'ChapterController@destroy')->name('chapter.delete');
				Route::get('/topic/delete/{id}', 'TopicController@destroy')->name('topic.delete');
				Route::get('/studio/delete/{id}', 'StudioController@destroy')->name('studio.delete');

				Route::post('/get-batch-subject', 'BatchController@get_batch_subject')->name('get-batch-subject');

				Route::post('/get-subject', 'TimetableController@get_subject')->name('get-subject');

				Route::post('/get-chapter', 'TopicController@get_chapter')->name('get-chapter');

				Route::post('/get-topic', 'TimetableController@get_topic')->name('get-topic');

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
			});
		});
	});
});


Auth::routes();

// Route::get('/sitemap.xml', 'SitemapController@index');
// Route::get('/rss.xml', 'HomeController@rss_feed');
// Route::get('/googlenewssitemap.xml', 'SitemapController@googlenewssitemap');
// Route::get('/news-sitemap-index.xml', 'SitemapController@articles_sitemap_index');
// Route::get('/categories-sitemap-index.xml', 'SitemapController@categories_sitemap_index');

Route::get('/', 'HomeController@index');
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
