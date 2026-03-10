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
		<?php if(Auth::user()->role_id !=21){ ?>
        <li class="nav-item"><a href="#"><i class="feather icon-list"></i><span class="menu-title" data-i18n="User">Roles</span></a>
            <ul class="menu-content">
              <li class="{{ Request::is('admin/roles/create') ? 'active' : '' }}"><a href="{{ route('admin.roles.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Role</span></a>
              </li>
              <li class="{{ Request::is('admin/roles') ? 'active' : '' }}"><a href="{{ route('admin.roles.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
              </li>                       
          </ul>
		</li>
		<?php } ?>
		<?php if(Auth::user()->role_id !=21){ ?>
		<li class="nav-item"><a href="#"><i class="feather icon-list"></i><span class="menu-title" data-i18n="User">Branch</span></a>
        <ul class="menu-content">
          <li class="{{ Request::is('admin/branch/create') ? 'active' : '' }}"><a href="{{ route('admin.branch.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Branch</span></a>
          </li>
          <li class="{{ Request::is('admin/branch') ? 'active' : '' }}"><a href="{{ route('admin.branch.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
          </li>                       
		</ul>
		
		</li>
		<?php } ?>
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


<li class="nav-item"><a href="#"><i class="feather icon-file"></i><span class="menu-title" data-i18n="User">Studios</span></a>
    <ul class="menu-content">
	<?php if(Auth::user()->role_id !=21){ ?>
      <li class="{{ Request::is('admin/studios/create') ? 'active' : '' }}"><a href="{{ route('admin.studios.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Studio</span></a>
      </li>
	<?php } ?>
      <li class="{{ Request::is('admin/studios') ? 'active' : '' }}"><a href="{{ route('admin.studios.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
      </li>                       
  </ul>
</li>

<li class="nav-item"><a href="#"><i class="feather icon-file"></i><span class="menu-title" data-i18n="User">Time Table Management</span></a>
    <ul class="menu-content">
     <li class="{{ Request::is('admin/timetable') ? 'active' : '' }}"><a href="{{ route('admin.timetable.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
     </li>                       
 </ul>
</li>

<li class="nav-item">
    <a href="{{ route('admin.classchangerequest.index') }}"><i class="feather icon-file"></i><span class="menu-title" data-i18n="User">Class Change Request</span>
    </a>
</li>

<?php if(Auth::user()->role_id !=21){ ?>
<li class="nav-item"><a href="#"><i class="feather icon-file"></i><span class="menu-title" data-i18n="User">Reports</span></a>
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

<li class="nav-item"><a href="#"><i class="feather icon-file"></i><span class="menu-title" data-i18n="User">Class Managemet</span></a>
    <ul class="menu-content">
      <li class="{{ Request::is('admin/subjects') ? 'active' : '' }}"><a href="{{ route('admin.subjects.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Subjects</span></a>
      </li>
      <li class="{{ Request::is('admin/chapters') ? 'active' : '' }}"><a href="{{ route('admin.chapters.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Chapters</span></a>
      </li>
      <li class="{{ Request::is('admin/topics') ? 'active' : '' }}"><a href="{{ route('admin.topics.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Topics</span></a>
      </li>                       
  </ul>
</li>

<?php if(Auth::user()->role_id !=21){ ?>
<li class="nav-item"><a href="#"><i class="feather icon-file"></i><span class="menu-title" data-i18n="User">Batch</span></a>
    <ul class="menu-content">
      <li class="{{ Request::is('admin/batch/create') ? 'active' : '' }}"><a href="{{ route('admin.batch.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Batch</span></a>
      </li>
      <li class="{{ Request::is('admin/batch') ? 'active' : '' }}"><a href="{{ route('admin.batch.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
      </li>                       
  </ul>
</li>
<?php } ?>

<?php if(Auth::user()->role_id !=21){ ?>
<li class="nav-item"><a href="#"><i class="feather icon-file"></i><span class="menu-title" data-i18n="User">Course</span></a>
    <ul class="menu-content">
      <li class="{{ Request::is('admin/course/create') ? 'active' : '' }}"><a href="{{ route('admin.course.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Course</span></a>
      </li>
      <li class="{{ Request::is('admin/course') ? 'active' : '' }}"><a href="{{ route('admin.course.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
      </li>                       
  </ul>
</li>
<?php } ?>




<?php if(Auth::user()->role_id !=21){ ?>
<li class="nav-item"><a href="#"><i class="feather icon-file"></i><span class="menu-title" data-i18n="User">Notifications</span></a>
    <ul class="menu-content">
      <li class="{{ Request::is('admin/notification/create') ? 'active' : '' }}"><a href="{{ route('admin.notification.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add</span></a>
      </li>
      <li class="{{ Request::is('admin/notification') ? 'active' : '' }}"><a href="{{ route('admin.notification.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
      </li>                       
  </ul>
</li>
<?php } ?>

<li class="nav-item"><a href="#"><i class="feather icon-file"></i><span class="menu-title" data-i18n="User">Task</span></a>
    <ul class="menu-content">
      <li class="{{ Request::is('admin/task/create') ? 'active' : '' }}"><a href="{{ route('admin.task.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Task</span></a>
      </li>
      <li class="{{ Request::is('admin/task') ? 'active' : '' }}"><a href="{{ route('admin.task.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
      </li>                       
  </ul>
</li>

<li class="nav-item"><a href="#"><i class="feather icon-file"></i><span class="menu-title" data-i18n="User">Attendance</span></a>
    <ul class="menu-content">
		<li class="{{ Request::is('admin/attendance/create') ? 'active' : '' }}"><a href="{{ route('admin.attendance.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Attendance</span></a>
		</li>
		<li class="{{ Request::is('admin/attendance') ? 'active' : '' }}"><a href="{{ route('admin.attendance.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
		</li>
		<li class="{{ Request::is('admin/attendance/users/absent') ? 'active' : '' }}"><a href="{{ route('admin.attendance.absentuser') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Absent Users</span></a>
		</li>
		<li class="{{ Request::is('admin/attendance/gallery') ? 'active' : '' }}"><a href="{{ route('admin.attendance.gallery') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Gallery</span></a>
		</li>
  </ul>
</li>

<li class="nav-item"><a href="#"><i class="feather icon-file"></i><span class="menu-title" data-i18n="User">Leave</span></a>
    <ul class="menu-content">
      <li class="{{ Request::is('admin/leave') ? 'active' : '' }}"><a href="{{ route('admin.leave.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Leave List</span></a>
      </li>                       
  </ul>
</li>

<?php if(Auth::user()->role_id !=21){ ?>
<li class="nav-item"><a href="#"><i class="feather icon-file"></i><span class="menu-title" data-i18n="User">Invoice</span></a>
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

</ul>
</div>
</div>