<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
  <div class="navbar-header">
    <ul class="nav navbar-nav flex-row">
      <li class="nav-item mr-auto">
        <a class="navbar-brand" href="#">
                    {{-- <div class="brand-logo">
                        @if(!empty(Auth::user()->image))
                        <img src="{{ asset('laravel/public/adminprofile/' . Auth::user()->image)}}"/ height="65" width="65" style="margin-top: -22px;">
                        @else
                        <img src="{{ asset('laravel/public/admin/images/avatar.jpg')}}" height="65" width="65" style="margin-top: -22px;">
                        @endif
                    </div> --}}
                    <h2 class="brand-text mb-0">HRM Admin</h2>
                </a>
            </li>
            <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i><i class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block collapse-toggle-icon primary" data-ticon="icon-disc"></i></a></li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
		<li class="nav-item {{ Request::is('admin') || Request::is('admin/dashboard') ? 'active' : '' }}">
			<a href="{{ route('admin.dashboard') }}"><i class="feather icon-home"></i><span class="menu-title" data-i18n="Dashboard">Dashboard</span>
			</a>
		</li>
		
		<?php if(Auth::user()->role_id == 28){ ?>
		<li class="nav-item"><a href="{{ route('admin.inventory') }}"><i class="feather icon-codepen"></i><span class="menu-title" data-i18n="User">Inventory</span></a>
		<li class="nav-item"><a href="{{ route('admin.request-inventory') }}"><i class="feather icon-codepen"></i><span class="menu-title" data-i18n="User">Request Inventory</span></a>
		<?php } ?>
		
		<?php if(Auth::user()->role_id == 24 || Auth::user()->role_id == 29){ ?>
		<li class="nav-item"><a href="{{ route('admin.document.index') }}"><i class="feather icon-printer"></i><span class="menu-title" data-i18n="User">Employee Document</span></a>
		<?php } ?>
		
		
		<?php if(Auth::user()->role_id == 29){ ?>
		<li class="nav-item"><a href="{{ route('admin.category.index') }}"><i class="feather icon-git-branch"></i><span class="menu-title" data-i18n="User">Category</span></a>
		<!--li class="nav-item"><a href="{{ route('admin.buyer.index') }}"><i class="feather icon-truck"></i><span class="menu-title" data-i18n="User">Buyer</span></a>
		<li class="nav-item"><a href="{{ route('admin.product.index') }}"><i class="feather icon-codepen"></i><span class="menu-title" data-i18n="User">Product</span></a-->
		<li class="nav-item"><a href="#"><i class="feather icon-database"></i><span class="menu-title" data-i18n="User">Inventory Management</span></a>
            <ul class="menu-content">
              <li class="{{ Request::is('admin/buyer') ? 'active' : '' }}"><a href="{{ route('admin.buyer.index') }}"><i class="feather icon-list"></i><span class="menu-item" data-i18n="List">Buyer List</span></a>
              </li>
			  <li class="{{ Request::is('admin/buyer/create') ? 'active' : '' }}"><a href="{{ route('admin.buyer.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Buyer</span></a></li>
             
              <li class="{{ Request::is('admin/product') ? 'active' : '' }}"><a href="{{ route('admin.product.index') }}"><i class="feather icon-list"></i><span class="menu-item" data-i18n="View">Inventory List</span></a></li>
			  <li class="{{ Request::is('admin/product/create') ? 'active' : '' }}"><a href="{{ route('admin.product.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Inventory</span></a></li>
			 <li class="{{ Request::is('admin/branch-inventory') ? 'active' : '' }}"><a href="{{ route('admin.branch-inventory') }}"><i class="feather icon-list"></i><span class="menu-item" data-i18n="List">Branch Inventory Stock</span></a></li>
             </li>	
			 <li class="{{ Request::is('admin/request-branch-inventory') ? 'active' : '' }}"><a href="{{ route('admin.request-branch-inventory') }}"><i class="feather icon-list"></i><span class="menu-item" data-i18n="List">Request Inventory</span></a></li>			  
          </ul>
		</li>
		
		<li class="nav-item"><a href="#"><i class="feather icon-database"></i><span class="menu-title" data-i18n="User">Asset</span></a>
			<ul class="menu-content">
				<li class="{{ Request::is('admin/asset/create') ? 'active' : '' }}"><a href="{{ route('admin.asset.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Asset</span></a></li>
				<li class="{{ Request::is('admin/asset') ? 'active' : '' }}"><a href="{{ route('admin.asset.index') }}"><i class="feather icon-list"></i><span class="menu-item" data-i18n="List">Asset List</span></a>
				</li>	
				<li class="{{ Request::is('admin/asset/employee-asset') ? 'active' : '' }}"><a href="{{ route('admin.asset.employee-asset') }}"><i class="feather icon-list"></i><span class="menu-item" data-i18n="List">Employee Asset List</span></a>
				</li>				
			</ul>
		</li>
		<?php } ?>
		
		
		<?php 
		//,'anandkarwa2010@gmail.com','projectmanager@utkarsh.com','hr@utkarsh.com','jay+1@gmail.com'
		if(in_array(Auth::user()->email,array('admin@gmail.com'))){ ?>
			<!--li class="nav-item"><a href="{{ route('admin.salary.index') }}"><i class="feather icon-printer"></i><span class="menu-title" data-i18n="User">Salary</span></a></li-->
			<li class="nav-item"><a href="#"><i class="feather icon-printer"></i><span class="menu-title" data-i18n="User">Salary</span></a>
				<ul class="menu-content">
				  <li class="{{ Request::is('admin/salary') ? 'active' : '' }}"><a href="{{ route('admin.salary.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Salary</span></a>
				  </li>
				  <li class="{{ Request::is('admin/import-salary') ? 'active' : '' }}"><a href="{{ route('admin.import-salary') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Import Salary</span></a>
				  </li>                       
				</ul>
			
			</li>
		<?php } ?>
		
		
		<?php if(Auth::user()->role_id != 28 && Auth::user()->role_id !=24 && Auth::user()->role_id !=21 && Auth::user()->role_id != 20 && Auth::user()->user_details->degination != 'STUDIO ASSISTANT MANAGER' && Auth::user()->user_details->degination != 'TIME TABLE MANAGER' && Auth::user()->role_id != 16 && Auth::user()->role_id != 6 && Auth::user()->role_id != 22 && Auth::user()->role_id != 23 && Auth::user()->role_id != 25 && Auth::user()->role_id != 26 && Auth::user()->role_id != 30){ ?>
        <li class="nav-item"><a href="#"><i class="feather icon-list"></i><span class="menu-title" data-i18n="User">Roles</span></a>
            <ul class="menu-content">
              <li class="{{ Request::is('admin/roles/create') ? 'active' : '' }}"><a href="{{ route('admin.roles.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Role</span></a>
              </li>
              <li class="{{ Request::is('admin/roles') ? 'active' : '' }}"><a href="{{ route('admin.roles.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
              </li>                       
          </ul>
		</li>
		<?php } ?>
		
		
		<?php if(Auth::user()->role_id != 28 && Auth::user()->role_id !=24 && Auth::user()->role_id !=21 && Auth::user()->role_id != 20 && Auth::user()->role_id != 16 && Auth::user()->role_id != 6 && Auth::user()->role_id != 22 && Auth::user()->role_id != 23 && Auth::user()->role_id != 25 && Auth::user()->role_id != 26 && Auth::user()->role_id != 30){ ?>
		<li class="nav-item"><a href="#"><i class="fa fa-sitemap"></i><span class="menu-title" data-i18n="User">Branch</span></a>
        <ul class="menu-content">
          <li class="{{ Request::is('admin/branch/create') ? 'active' : '' }}"><a href="{{ route('admin.branch.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Branch</span></a>
          </li>
          <li class="{{ Request::is('admin/branch') ? 'active' : '' }}"><a href="{{ route('admin.branch.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
          </li>                       
		</ul>
		
		</li>
		<?php } ?>
		<?php if(Auth::user()->role_id != 28 && Auth::user()->role_id != 20 && Auth::user()->user_details->degination != 'STUDIO ASSISTANT MANAGER' && Auth::user()->user_details->degination != 'TIME TABLE MANAGER' && Auth::user()->role_id != 16 && Auth::user()->role_id != 6 && Auth::user()->role_id != 22 && Auth::user()->role_id != 23 && Auth::user()->role_id != 25 && Auth::user()->role_id != 26 && Auth::user()->role_id != 30){ ?>
		<li class="nav-item"><a href="#"><i class="feather icon-user"></i><span class="menu-title" data-i18n="User">Employees</span></a>
		<ul class="menu-content">
			<?php if(Auth::user()->role_id !=21){ ?>
				<li class="{{ Request::is('admin/employees/create') ? 'active' : '' }}"><a href="{{ route('admin.employees.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Employee</span></a>
				</li>
			<?php } ?>
			<li class="{{ Request::is('admin/employees') ? 'active' : '' }}"><a href="{{ route('admin.employees.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
			</li>                       
		</ul>
		</li>
        <?php } ?>
		<?php
		$modules = false;
		if(Auth::user()->role_id == 21){
			if(Auth::user()->role_id == 21 && Auth::user()->department_type == 2){
				$modules = true;
			}
		}
		else if(Auth::user()->role_id != 20 && Auth::user()->role_id != 28 && Auth::user()->role_id != 16 && Auth::user()->role_id != 6 && Auth::user()->role_id != 22 && Auth::user()->role_id != 23 && Auth::user()->role_id != 25 && Auth::user()->role_id != 26 && Auth::user()->role_id != 30){
			if(Auth::user()->role_id == 24){
				$modules = false;
			}
			else{
				$modules = true;
			}
		}
		?>
		<?php if($modules){ ?>
		<li class="nav-item"><a href="#"><i class="fa fa-video-camera"></i><span class="menu-title" data-i18n="User">Studios</span></a>
			<ul class="menu-content">
			<?php //if(Auth::user()->role_id !=21){ ?>
			  <li class="{{ Request::is('admin/studios/create') ? 'active' : '' }}"><a href="{{ route('admin.studios.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Studio</span></a>
			  </li>
			<?php //} ?>
			  <li class="{{ Request::is('admin/studios') ? 'active' : '' }}"><a href="{{ route('admin.studios.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
			  </li>                       
		  </ul>
		</li>
		<?php } ?>
		
		<?php if($modules){ ?>
		<li class="nav-item"><a href="#"><i class="fa fa-calendar"></i><span class="menu-title" data-i18n="User">Time Table Management</span></a>
			<ul class="menu-content">
			 <li class="{{ Request::is('admin/timetable') ? 'active' : '' }}"><a href="{{ route('admin.timetable.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
			 </li>                       
		 </ul>
		</li>
		<?php } ?>
		<?php if($modules){ ?>
		<li class="nav-item">
		<a href="{{ route('admin.classchangerequest.index') }}"><i class="fa fa-exchange"></i><span class="menu-title" data-i18n="User">Class Change Request</span>
		</a>
		</li>
		<?php } ?>

		<?php if(Auth::user()->role_id != 28 && Auth::user()->role_id !=21 && Auth::user()->role_id != 20 && Auth::user()->role_id != 24 && Auth::user()->role_id != 16 && Auth::user()->role_id != 6 && Auth::user()->role_id != 22 && Auth::user()->role_id != 23 && Auth::user()->role_id != 25 && Auth::user()->role_id != 26 && Auth::user()->role_id != 30){ ?>
		<li class="nav-item"><a href="#"><i class="fa fa-file-text"></i><span class="menu-title" data-i18n="User">Reports</span></a>
			<ul class="menu-content">
				<li class="{{ Request::is('admin/studio-reports') ? 'active' : '' }}"><a href="{{ route('admin.studio-reports') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Studio Reports</span></a>
				</li>
				<li class="{{ Request::is('admin/faculty-reports') ? 'active' : '' }}"><a href="{{ route('admin.faculty-reports') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Faculty Reports</span></a>
				</li>
				<li class="{{ Request::is('admin/subject-reports') ? 'active' : '' }}"><a href="{{ route('admin.subject-reports') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Subject Reports</span></a>
				</li>
				<li class="{{ Request::is('admin/free-faculty-reports') ? 'active' : '' }}"><a href="{{ route('admin.free-faculty-reports') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Free Faculty</span></a>
				</li>  
				<li class="{{ Request::is('admin/free-assistant-reports') ? 'active' : '' }}"><a href="{{ route('admin.free-assistant-reports') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Free Assistant</span></a>
				</li>  
				<li class="{{ Request::is('admin/reports') ? 'active' : '' }}"><a href="{{ route('admin.reports.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
				</li>
				<li class="{{ Request::is('typist-work-report') ? 'active' : '' }}"><a href="{{ route('admin.typist-work-report') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Typist Work</span></a>
				</li> 
				<li class="{{ Request::is('faculty-reports/subjects') ? 'active' : '' }}"><a href="{{ route('admin.faculty-reports.subjects') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Faculty Subjects</span></a>
				</li> 
			</ul>
		</li>
		<?php } ?>
		<?php if($modules){ ?>
		<li class="nav-item"><a href="#"><i class="fa fa-puzzle-piece"></i><span class="menu-title" data-i18n="User">Class Managemet</span></a>
			<ul class="menu-content">
			  <li class="{{ Request::is('admin/subjects') ? 'active' : '' }}"><a href="{{ route('admin.subjects.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Subjects</span></a>
			  </li>
			  <li class="{{ Request::is('admin/chapters') ? 'active' : '' }}"><a href="{{ route('admin.chapters.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Chapters</span></a>
			  </li>
			  <li class="{{ Request::is('admin/topics') ? 'active' : '' }}"><a href="{{ route('admin.topics.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Topics</span></a>
			  </li>                       
		  </ul>
		</li>
		<?php } ?>
		<?php if(Auth::user()->role_id != 28 && Auth::user()->role_id !=21 && Auth::user()->role_id !=20 && Auth::user()->role_id != 24 && Auth::user()->role_id != 16 && Auth::user()->role_id != 6 && Auth::user()->role_id != 22 && Auth::user()->role_id != 23 && Auth::user()->role_id != 25 && Auth::user()->role_id != 26 && Auth::user()->role_id != 30){ ?>
		<li class="nav-item"><a href="#"><i class="fa fa-columns"></i><span class="menu-title" data-i18n="User">Batch</span></a>
			<ul class="menu-content">
			  <li class="{{ Request::is('admin/batch/create') ? 'active' : '' }}"><a href="{{ route('admin.batch.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Batch</span></a>
			  </li>
			  <li class="{{ Request::is('admin/batch') ? 'active' : '' }}"><a href="{{ route('admin.batch.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
			  </li>                       
		  </ul>
		</li>
		<?php } ?>

		<?php if(Auth::user()->role_id != 28 && Auth::user()->role_id !=21 && Auth::user()->role_id !=20 && Auth::user()->role_id != 24 && Auth::user()->role_id != 16 && Auth::user()->role_id != 6 && Auth::user()->role_id != 22 && Auth::user()->role_id != 23 && Auth::user()->role_id != 25 && Auth::user()->role_id != 26 && Auth::user()->role_id != 30){ ?>
		<li class="nav-item"><a href="#"><i class="fa fa-book"></i><span class="menu-title" data-i18n="User">Course</span></a>
			<ul class="menu-content">
			  <li class="{{ Request::is('admin/course/create') ? 'active' : '' }}"><a href="{{ route('admin.course.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Course</span></a>
			  </li>
			  <li class="{{ Request::is('admin/course') ? 'active' : '' }}"><a href="{{ route('admin.course.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
			  </li>                       
		  </ul>
		</li>
		<?php } ?>


		<?php
		$modules2 = false;
		if(Auth::user()->user_details->degination != 'STUDIO ASSISTANT MANAGER' && Auth::user()->user_details->degination != 'TIME TABLE MANAGER' && Auth::user()->role_id != 28 && Auth::user()->role_id != 16 && Auth::user()->role_id != 6 && Auth::user()->role_id != 22 && Auth::user()->role_id != 23 && Auth::user()->role_id != 25 && Auth::user()->role_id != 26 && Auth::user()->role_id != 30){
			$modules2 = true;
		}
		else if(Auth::user()->role_id == 20 || Auth::user()->role_id ==29){
			$modules2 = true;
		}
		?>

		<?php if($modules2){ ?>
		<li class="nav-item"><a href="#"><i class="fa fa-bell"></i><span class="menu-title" data-i18n="User">Notifications</span></a>
			<ul class="menu-content">
			 <?php if(Auth::user()->role_id !=20){ ?>
			  <li class="{{ Request::is('admin/notification/create') ? 'active' : '' }}"><a href="{{ route('admin.notification.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add</span></a>
			  </li>
			  <?php } ?>
			  <li class="{{ Request::is('admin/notification') ? 'active' : '' }}"><a href="{{ route('admin.notification.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
			  </li>                       
		  </ul>
		</li>
		<?php } ?>
        <?php if(Auth::user()->role_id !=20){ ?>
		<!--<li class="nav-item"><a href="#"><i class="feather icon-file"></i><span class="menu-title" data-i18n="User">Task</span></a>
			<ul class="menu-content">
			  <li class="{{ Request::is('admin/task/create') ? 'active' : '' }}"><a href="{{ route('admin.task.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Task</span></a>
			  </li>
			  <li class="{{ Request::is('admin/task') ? 'active' : '' }}"><a href="{{ route('admin.task.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
			  </li>                       
		  </ul>
		</li>-->
		<?php } ?>
		<?php //if(Auth::user()->user_details->degination != 'STUDIO ASSISTANT MANAGER' && Auth::user()->user_details->degination != 'TIME TABLE MANAGER' && Auth::user()->role_id != 28){ ?>
		
		<li class="nav-item"><a href="#"><i class="fa fa-tasks"></i><span class="menu-title" data-i18n="User">Task</span></a>
			<ul class="menu-content">
			  <li class="{{ Request::is('admin/task/create') ? 'active' : '' }}"><a href="{{ route('admin.task.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Task</span></a>
			  </li>
			  <li class="{{ Request::is('admin/task') ? 'active' : '' }}"><a href="{{ route('admin.task.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
			  </li>                       
		  </ul>
		</li>
		
		<!--li class="nav-item"><a href="#"><i class="feather icon-file"></i><span class="menu-title" data-i18n="User">New Task</span></a>
			<ul class="menu-content">
				<li class="{{ Request::is('admin/newtask/create') ? 'active' : '' }}"><a href="{{ route('admin.newtask.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add New Task</span></a>
				</li>
				<li class="{{ Request::is('admin/newtask') ? 'active' : '' }}"><a href="{{ route('admin.newtask.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View New Task</span></a>
				</li>  
				<li class="{{ Request::is('admin/newtask/open-task') ? 'active' : '' }}"><a href="{{ route('admin.newtask.open-task') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Open New Task</span></a>
				  </li>	  
		  </ul>
		</li-->
		<?php //} ?>
		
		<?php
		$modules3 = false;
		if(Auth::user()->user_details->degination != 'STUDIO ASSISTANT MANAGER' && Auth::user()->user_details->degination != 'TIME TABLE MANAGER' && Auth::user()->role_id != 28 && Auth::user()->role_id != 16 && Auth::user()->role_id != 6 && Auth::user()->role_id != 22 && Auth::user()->role_id != 23 && Auth::user()->role_id != 25 && Auth::user()->role_id != 26 && Auth::user()->role_id != 30){
			$modules3 = true;
		}
		?>
		<?php //if($modules3){ ?>
		<li class="nav-item"><a href="#"><i class="feather icon-clock"></i><span class="menu-title" data-i18n="User">Attendance</span></a>
			<ul class="menu-content">
				<?php //if(Auth::user()->role_id !=20 && Auth::user()->role_id !=21){ ?>
				<li class="{{ Request::is('admin/attendance/create') ? 'active' : '' }}"><a href="{{ route('admin.attendance.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Attendance</span></a>
				</li>
				<?php //} ?>
				<?php if(Auth::user()->role_id !=21 && Auth::user()->role_id !=20 && Auth::user()->role_id !=28 && Auth::user()->role_id !=16 && Auth::user()->role_id != 6 && Auth::user()->role_id != 22 && Auth::user()->role_id != 23 && Auth::user()->role_id != 25 && Auth::user()->role_id != 26 && Auth::user()->role_id != 30){ ?>
				<li class="{{ Request::is('admin/attendance') ? 'active' : '' }}"><a href="{{ route('admin.attendance.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">App Attendance</span></a>
				</li>
				<li class="{{ Request::is('admin/rp-attendance') ? 'active' : '' }}"><a href="{{ route('admin.attendance.rpattendance') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">RFID Attendance</span></a>
				</li>
				<?php } ?>

                <?php if(Auth::user()->role_id !=20) {?>
					<li class="{{ Request::is('admin/attendence-record') ? 'active' : '' }}"><a href="{{ route('admin.attendance.attendencerecord') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Attendence Record</span></a>
					</li>
				<?php } ?>

				<li class="{{ Request::is('admin/attendance/full-attendence') ? 'active' : '' }}"><a href="{{ route('admin.attendance.fullattendence') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Full Attendance</span></a>
				</li>
				<li class="{{ Request::is('admin/attendance/absent-full-attendence') ? 'active' : '' }}"><a href="{{ route('admin.attendance.absentfullattendence') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Absent Full Attendance</span></a>
				</li>
				<?php if( Auth::user()->role_id !=21 && Auth::user()->role_id !=20 && Auth::user()->role_id !=28 &&  Auth::user()->role_id !=16 && Auth::user()->role_id != 6 && Auth::user()->role_id != 22 && Auth::user()->role_id != 23 && Auth::user()->role_id != 25 && Auth::user()->role_id != 26 && Auth::user()->role_id != 30){ ?>
				<li class="{{ Request::is('admin/attendance/users/absent') ? 'active' : '' }}"><a href="{{ route('admin.attendance.absentuser') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Absent Users</span></a>
				</li>
				
				<li class="{{ Request::is('admin/attendance/gallery') ? 'active' : '' }}"><a href="{{ route('admin.attendance.gallery') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Gallery</span></a>
				</li>
				<?php } ?>

                <?php /*if(Auth::user()->role_id==21 || Auth::user()->role_id==24 || Auth::user()->role_id==29){ ?>
                <li class="{{ Request::is('admin/attendence-record') ? 'active' : '' }}">
                	<a href="attendence-record"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Attendance Record</span></a>
				</li>
			   <?php }*/ ?>
                				
				


		  </ul>
		</li>
		<?php //} ?>
		<?php if($modules3){ ?>
		<li class="nav-item"><a href="#"><i class="feather icon-calendar"></i><span class="menu-title" data-i18n="User">Leave</span></a>
			<ul class="menu-content">
			<?php if( Auth::user()->role_id !=20){ ?>
				<li class="{{ Request::is('admin/create') ? 'active' : '' }}"><a href="{{ route('admin.leave.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Add Leave</span></a>
			</li><?php } ?>				
			  <li class="{{ Request::is('admin/leave') ? 'active' : '' }}"><a href="{{ route('admin.leave.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Leave List</span></a>
			  </li>                       
		  </ul>
		</li>
		<?php } ?>
		<?php if((Auth::user()->role_id != 28 && Auth::user()->role_id != 24 && Auth::user()->role_id !=20 && Auth::user()->user_details->degination != 'STUDIO ASSISTANT MANAGER' && Auth::user()->user_details->degination != 'TIME TABLE MANAGER' && Auth::user()->role_id != 16 && Auth::user()->role_id != 6 && Auth::user()->role_id != 22 && Auth::user()->role_id != 23 && Auth::user()->role_id != 25 && Auth::user()->role_id != 26 && Auth::user()->role_id != 30) || (Auth::user()->department_type == 10)){ ?>
		<li class="nav-item"><a href="#"><i class="feather icon-package"></i><span class="menu-title" data-i18n="User">Invoice</span></a>
			<ul class="menu-content">
			  <li class="{{ Request::is('admin.invoice.create') ? 'active' : '' }}"><a href="{{ route('admin.invoice.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Add Invoice</span></a>
			  </li>  
			  <li class="{{ Request::is('admin/invoice') ? 'active' : '' }}"><a href="{{ route('admin.invoice.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Tax Invoice</span></a>
			  </li>  
			<li class="{{ Request::is('admin.invoice.credit-note') ? 'active' : '' }}"><a href="{{ route('admin.invoice.credit-note') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Credit Note</span></a>
			</li>  	  
		  </ul>
		</li>
		<?php } ?>
		<?php
		$modules4 = false;
		if(Auth::user()->user_details->degination != 'STUDIO ASSISTANT MANAGER' && Auth::user()->user_details->degination != 'TIME TABLE MANAGER' && Auth::user()->role_id != 28 && Auth::user()->role_id != 16 && Auth::user()->role_id != 6 && Auth::user()->role_id != 22 && Auth::user()->role_id != 23 && Auth::user()->role_id != 25 && Auth::user()->role_id != 26 && Auth::user()->role_id != 30){
			if(Auth::user()->role_id == 24){
				$modules4 = false;
			}
			else{
				$modules4 = true;
			}	
		}
		?>
		<?php if($modules4){ ?>
		<li class="nav-item"><a href="#"><i class="fa fa-random"></i><span class="menu-title" data-i18n="User">Staff Movement System</span></a>
			<ul class="menu-content">
			  <li class="{{ Request::is('admin/staff') ? 'active' : '' }}"><a href="{{ route('admin.staff.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Staff Movement System</span></a>
			  </li>   	  
		  </ul>
		</li>
		<?php } ?>
		<?php if(Auth::user()->role_id != 28 && Auth::user()->role_id != 24 && Auth::user()->role_id !=21 && Auth::user()->role_id !=20 && Auth::user()->user_details->degination != 'STUDIO ASSISTANT MANAGER' && Auth::user()->user_details->degination != 'TIME TABLE MANAGER' && Auth::user()->role_id != 16 && Auth::user()->role_id != 6 && Auth::user()->role_id != 22 && Auth::user()->role_id != 23 && Auth::user()->role_id != 25 && Auth::user()->role_id != 26 && Auth::user()->role_id != 30){ ?>
		<li class="nav-item"><a href="#"><i class="fa fa-folder-open"></i><span class="menu-title" data-i18n="User">Department</span></a>
			<ul class="menu-content">
				<li class="{{ Request::is('admin/department/create') ? 'active' : '' }}"><a href="{{ route('admin.department.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Department</span></a>
				</li>
				<li class="{{ Request::is('admin/department') ? 'active' : '' }}"><a href="{{ route('admin.department.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View Department</span></a>
				</li>                       
			</ul>
		</li>
		
		<li class="nav-item"><a href="#"><i class="fa fa-address-book"></i><span class="menu-title" data-i18n="User">Designation</span></a>
			<ul class="menu-content">
				<li class="{{ Request::is('admin/designation/create') ? 'active' : '' }}"><a href="{{ route('admin.designation.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Designation</span></a>
				</li>
				<li class="{{ Request::is('admin/designation') ? 'active' : '' }}"><a href="{{ route('admin.designation.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View Designation</span></a>
				</li>                       
			</ul>
		</li>
		<?php } ?>
</ul>
</div>
</div>