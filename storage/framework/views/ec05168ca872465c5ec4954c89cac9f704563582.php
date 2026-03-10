<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto">
                <a class="navbar-brand" href="#">
                    <h2 class="brand-text mb-0" style="padding-left:1rem;">St. Manager</h2>
                </a>
            </li>
             <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i><i class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block collapse-toggle-icon primary" data-ticon="icon-disc"></i></a></li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class="nav-item <?php echo e(Request::is('studiomanager') || Request::is('studiomanager/dashboard') ? 'active' : ''); ?>">
                <a href="<?php echo e(route('studiomanager.dashboard')); ?>"><i class="feather icon-home"></i><span class="menu-title" data-i18n="Dashboard">Dashboard</span>
                </a>
            </li>
			
			<?php if(Auth::user()->id == 1665){ ?>
			<li class="<?php echo e(Request::is('admin/faculty-early-delay-reports') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.faculty-early-delay-reports')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Faculty Early/Delay Reports</span></a>
			<?php } ?>
			
			<?php if(Auth::user()->id==8937){ ?>
			<li class="nav-item"><a href="#"><i class="feather icon-file"></i><span class="menu-title" data-i18n="User">Time Table Management</span></a>
				<ul class="menu-content">
					<li class="<?php echo e(Request::is('studiomanager/timetable') ? 'active' : ''); ?>">
						<a href="<?php echo e(route('studiomanager.timetable.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
					</li>
					<!--
					<li class="<?php echo e(Request::is('studiomanager/timetable-history-reports') ? 'active' : ''); ?>">
						<a href="<?php echo e(route('studiomanager.timetable-history-reports')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Changes History</span></a>
					</li>
					
					<li class="<?php echo e(Request::is('studiomanager/timetables') ? 'active' : ''); ?>">
						<a href="<?php echo e(route('studiomanager.timetables.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Start/End Class</span></a>
					</li>
					-->
					
				</ul>
			</li>
			<?php }else{ ?>
			
			<li class="nav-item"><a href="#"><i class="feather icon-user"></i><span class="menu-title" data-i18n="User">Faculty</span></a>
				<ul class="menu-content">
					 
					<li class="<?php echo e(Request::is('studiomanager/employees/create') ? 'active' : ''); ?>"><a href="<?php echo e(route('studiomanager.employees.create')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Faculty</span></a>
					</li>
					 
					<li class="<?php echo e(Request::is('studiomanager/employees') ? 'active' : ''); ?>"><a href="<?php echo e(route('studiomanager.employees.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
					</li>                       
				</ul>
			</li>
            <li class="nav-item"><a href="#"><i class="feather icon-file"></i><span class="menu-title" data-i18n="User">Studios</span></a>
                <ul class="menu-content">
                    <li class="<?php echo e(Request::is('studiomanager/studios/create') ? 'active' : ''); ?>"><a href="<?php echo e(route('studiomanager.studios.create')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Studio</span></a>
                    </li>
                    <li class="<?php echo e(Request::is('studiomanager/studios') ? 'active' : ''); ?>"><a href="<?php echo e(route('studiomanager.studios.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
                    </li>                       
                </ul>
            </li>
			<?php
			if(Auth::user()->id!=5862){
			?>
				<li class="nav-item"><a href="#"><i class="feather icon-file"></i><span class="menu-title" data-i18n="User">Time Table Management</span></a>
					<ul class="menu-content">
						<li class="<?php echo e(Request::is('studiomanager/timetable') ? 'active' : ''); ?>">
							<a href="<?php echo e(route('studiomanager.timetable.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
						</li>
						<li class="<?php echo e(Request::is('studiomanager/timetable-history-reports') ? 'active' : ''); ?>">
							<a href="<?php echo e(route('studiomanager.timetable-history-reports')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Changes History</span></a>
						</li>
						<!--
						<li class="<?php echo e(Request::is('studiomanager/timetables') ? 'active' : ''); ?>">
							<a href="<?php echo e(route('studiomanager.timetables.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Start/End Class</span></a>
						</li>
						-->
						
					</ul>
				</li>
			<?php } ?>
    <li class="nav-item">
        <a href="<?php echo e(route('studiomanager.classchangerequest.index')); ?>"><i class="feather icon-file"></i><span class="menu-title" data-i18n="User">Class Change Request</span>
        </a>
    </li>
	<li class="nav-item"><a href="#"><i class="feather icon-file"></i><span class="menu-title" data-i18n="User">Reports</span></a>
    <ul class="menu-content">
		<li class="<?php echo e(Request::is('studiomanager/studio-reports') ? 'active' : ''); ?>"><a href="<?php echo e(route('studiomanager.studio-reports')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Studio Reports</span></a>
		</li>
		<li class="<?php echo e(Request::is('studiomanager/batch-reports') ? 'active' : ''); ?>"><a href="<?php echo e(route('studiomanager.batch-reports')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Batch Reports</span></a>
        </li>
		<li class="<?php echo e(Request::is('studiomanager/batch-reports-shiftwise') ? 'active' : ''); ?>"><a href="<?php echo e(route('studiomanager.batch-reports-shiftwise')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Batch Reports shift wise</span></a>
        </li>
       
		<li class="<?php echo e(Request::is('studiomanager/faculty-reports') ? 'active' : ''); ?>"><a href="<?php echo e(route('studiomanager.faculty-reports')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Faculty Reports</span></a>
		</li>
		
		<li class="<?php echo e(Request::is('studiomanager/faculty-early-delay-reports') ? 'active' : ''); ?>"><a href="<?php echo e(route('studiomanager.faculty-early-delay-reports')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Faculty Early/Delay Reports</span></a></li>

		<li class="<?php echo e(Request::is('admin/timetable-change-counts') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.timetable-change-counts')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Timetablle Change Counts</span></a></li>

		<?php 
		$reportNotArr =  array(901,1859); 
		if(in_array(Auth::user()->id, $reportNotArr)){ ?> 
			<li class="<?php echo e(Request::is('studiomanager/faculty-hours-reports') ? 'active' : ''); ?>"><a href="<?php echo e(route('studiomanager.faculty-hours-reports')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Faculty Hours Reports</span></a></li>
		<?php } ?>

		<li class="<?php echo e(Request::is('studiomanager/subject-reports') ? 'active' : ''); ?>"><a href="<?php echo e(route('studiomanager.subject-reports')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Subject Reports</span></a>
		</li>
		<li class="<?php echo e(Request::is('studiomanager/free-faculty-reports') ? 'active' : ''); ?>"><a href="<?php echo e(route('studiomanager.free-faculty-reports')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Free Faculty</span></a>
		</li>  
		<li class="<?php echo e(Request::is('studiomanager/free-assistant-reports') ? 'active' : ''); ?>"><a href="<?php echo e(route('studiomanager.free-assistant-reports')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Free Assistant</span></a>
		</li>  
		<li class="<?php echo e(Request::is('studiomanager/reports') ? 'active' : ''); ?>"><a href="<?php echo e(route('studiomanager.reports.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
		</li>
		<li class="<?php echo e(Request::is('typist-work-report') ? 'active' : ''); ?>"><a href="<?php echo e(route('studiomanager.typist-work-report')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Typist Work</span></a>
		</li>
		<li class="<?php echo e(Request::is('faculty-reports/subjects') ? 'active' : ''); ?>"><a href="<?php echo e(route('studiomanager.faculty-reports.subjects')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Faculty Subjects</span></a>
		</li> 
		<li class="<?php echo e(Request::is('studio-availability') ? 'active' : ''); ?>"><a href="<?php echo e(route('studiomanager.studio-availability')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Studio Class Report</span></a>
		</li> 
		<li class="<?php echo e(Request::is('faculty-agreement-hours') ? 'active' : ''); ?>"><a href="<?php echo e(route('studiomanager.faculty-agreement-hours')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Agreement Hours Report</span></a>
		</li> 
		<li class="<?php echo e(Request::is('batch-hours-reports') ? 'active' : ''); ?>"><a href="<?php echo e(route('studiomanager.batch-hours-reports')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Batch Hours Report</span></a>
		</li>
		<li class="<?php echo e(Request::is('faculty-batch-reports') ? 'active' : ''); ?>"><a href="<?php echo e(route('studiomanager.faculty-batch-reports')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Faculty Batch Report</span></a>
		</li> 
		<li class="<?php echo e(Request::is('admin/faculty-topic') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.faculty-topic')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Faculty Today</span></a>
					</li>
	</ul>
</li>
    <li class="nav-item"><a href="#"><i class="feather icon-file"></i><span class="menu-title" data-i18n="User">Class Managemet</span></a>
        <ul class="menu-content">
            <li class="<?php echo e(Request::is('studiomanager/subjects') ? 'active' : ''); ?>"><a href="<?php echo e(route('studiomanager.subjects.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Subjects</span></a>
            </li>
            <li class="<?php echo e(Request::is('studiomanager/chapters') ? 'active' : ''); ?>"><a href="<?php echo e(route('studiomanager.chapters.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Chapters</span></a>
            </li>
            <li class="<?php echo e(Request::is('studiomanager/topics') ? 'active' : ''); ?>"><a href="<?php echo e(route('studiomanager.topics.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Topics</span></a>
            </li>                       
        </ul>
    </li>
    <li class="nav-item <?php echo e(Request::is('studiomanager/batch/*') ? 'active' : ''); ?>">
    	<a href="#"><i class="fa fa-book"></i><span class="menu-title" data-i18n="User">Batch</span></a>
        <ul class="menu-content">
            <li class="<?php echo e(Request::is('studiomanager/batch/create') ? 'active' : ''); ?>"><a href="<?php echo e(route('studiomanager.batch.create')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Batch</span></a>
            </li>
            <li class="<?php echo e(Request::is('studiomanager/batch') ? 'active' : ''); ?>"><a href="<?php echo e(route('studiomanager.batch.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
            </li>                       
        </ul>
    </li>
    <li class="nav-item <?php echo e(Request::is('studiomanager/course/*') ? 'active' : ''); ?>">
    	<a href="#"><i class="fa fa-bookmark"></i><span class="menu-title" data-i18n="User">Course</span></a>
        <ul class="menu-content">
            <li class="<?php echo e(Request::is('studiomanager/course/create') ? 'active' : ''); ?>"><a href="<?php echo e(route('studiomanager.course.create')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Course</span></a>
            </li>
            <li class="<?php echo e(Request::is('studiomanager/course') ? 'active' : ''); ?>"><a href="<?php echo e(route('studiomanager.course.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
            </li>                       
        </ul>
    </li>
	<li class="nav-item"><a href="#"><i class="feather icon-user"></i><span class="menu-title" data-i18n="User">Send Links</span></a>
		<ul class="menu-content">
			<li class="<?php echo e(Request::is('studiomanager/links') ? 'active' : ''); ?>"><a href="<?php echo e(route('studiomanager.links.faculty')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Faculty</span></a>
			</li>                       
	  </ul>
	</li>

	<?php 
		$courseArr=array(5453,5441,1089,1207,1237,1069,1556,1868,5785,1246,1096,1215,1926,5525,6245,1926,1078,1027,7074); 
		if(in_array(Auth::user()->id, $courseArr)){
	?>
	<li class="nav-item"><a href="<?php echo e(route('admin.onlinecourses.index')); ?>"><i class="feather icon-help-circle"></i><span class="menu-title" data-i18n="User">Online Courses</span></a></li>
	<?php } ?>
	 
	<li class="nav-item"><a href="<?php echo e(route('studiomanager.batch-test-report')); ?>"><i class="feather icon-help-circle"></i><span class="menu-title" data-i18n="User">Batch Test Report</span></a></li> 
	
	<li class="nav-item"><a href="<?php echo e(route('studiomanager.faculty-leave')); ?>"><i class="feather icon-help-circle"></i><span class="menu-title" data-i18n="User">Faculty Leave</span></a></li>
	
	<li class="nav-item">
		<a href="#">
			<i class="feather icon-database"></i><span class="menu-title" data-i18n="User">Stu. Attendance/Inventory</span>  
		</a>			
		<ul class="menu-content">			
			<li><a href="<?php echo e(route('admin.batchinventory.add')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add</span></a></li>
			<li><a href="<?php echo e(route('admin.batchinventory.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">View</span></a></li>
			<li><a href="<?php echo e(route('admin.attendance-dashboard')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Attendance Dashboard</span></a></li>
			<li><a href="<?php echo e(route('admin.inventory-dashboard')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Inventory Dashboard</span></a></li> 
			<li><a href="<?php echo e(route('admin.student-attendence-record')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Stu. Attendance Record</span></a></li> 
			
			<li><a href="<?php echo e(route('admin.student-get-inventory')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Stu. Get Inventory</span></a></li> 
			<li><a href="<?php echo e(route('admin.anuprati-dashboard')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Anuprati-dashboard</span></a></li> 
			<li><a href="<?php echo e(route('admin.student-invalid-punch')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Stu. Invalid Punch</span></a></li> 
			<li><a href="<?php echo e(route('admin.student-inventory-track')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Stu. Inventory Track</span></a></li>
		</ul>
	</li>
	<li class="nav-item"><a href="<?php echo e(route('admin.support-dashboard')); ?>"><i class="feather icon-info"></i><span class="menu-title" data-i18n="User">Message to Chairman Dashboard</span></a></li>
	<li class="nav-item"><a href="<?php echo e(route('admin.support-enquiry')); ?>"><i class="feather icon-activity"></i><span class="menu-title" data-i18n="User">Message to Chairman Enquiry</span></a></li>

	<li class="nav-item"><a href="<?php echo e(route('admin.support-discussion')); ?>"><i class="feather icon-help-circle"></i><span class="menu-title" data-i18n="User">Chopal Discussion <sup class="text-warning">New</sup></span></a></li>
	
	<li class="nav-item"><a href="#"><i class="feather icon-wind"></i><span class="menu-title" data-i18n="User">Batch Holiday</span></a>
		<ul class="menu-content">
		  <li class=""><a href="<?php echo e(route('admin.batch_holiday.create')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add</span></a>
		  </li>
		  <li class=""><a href="<?php echo e(route('admin.batch_holiday.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
		  </li>                       
	  </ul>
	</li>

	<li class="nav-item"><a href="#"><i class="feather icon-wind"></i><span class="menu-title" data-i18n="User">Course Planner</span></a>
		<ul class="menu-content">
		  <li class=""><a href="<?php echo e(route('admin.course-planner.batchReports')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Batch Reports</span></a>
		  </li>
		  <li class=""><a href="<?php echo e(route('admin.course-planner.issue-raise-reports')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Faculty Issue Reports</span></a>
		  </li>                       
	  </ul>
	</li>
	<?php } ?>
	
	<?php
		$user = Auth::user();
		if (
			in_array($user->role_id, [27, 29]) ||
			in_array($user->department_type, [13, 50, 4])
		) {
	?>
	<li class="nav-item">
		<a href="#"><i class="fa fa-check"></i>
		 <span class="menu-title" data-i18n="User">Multi Course Planner</span>
		</a>
		<ul class="menu-content">
			<li class="nav-item">
				<a href="<?php echo e(route('admin.multi-course-planner.multi-planner-request')); ?>">
					<i class="fa fa-book"></i>
					<span class="menu-title" data-i18n="User"> Planner Request</span>
				</a>
			</li>
			<li class="nav-item">
				<a href="<?php echo e(route('admin.multi-course-planner.planner-request-view')); ?>">
					<i class="fa fa-book"></i>
					<span class="menu-title" data-i18n="User"> Planner Request View</span>
				</a>
			</li>
		</ul>
	</li>
	<?php } ?>

	<li class="nav-item">
		<a href="#"><i class="fa fa-check"></i>
		 <span class="menu-title" data-i18n="User">Discount Approval</span>
		</a>
		<ul class="menu-content">
			<li class="nav-item">
				<a href="<?php echo e(route('admin.discountApprovel.index')); ?>"><i class="fa fa-check"></i><span class="menu-title" data-i18n="User"> Approval Request</span></a>
			</li>

			<?php if(Auth::user()->id==901){ ?>
				<li class="nav-item">
					<a href="<?php echo e(route('admin.discount-role-wise.index')); ?>"><i class="fa fa-list"></i><span class="menu-title" data-i18n="User"> Discount Approver</span></a>
				</li>
			<?php } ?>
		</ul>
	</li>
</ul>
</div>
</div><?php /**PATH /var/www/html/laravel/resources/views/layouts/studiomanager/sidebar.blade.php ENDPATH**/ ?>