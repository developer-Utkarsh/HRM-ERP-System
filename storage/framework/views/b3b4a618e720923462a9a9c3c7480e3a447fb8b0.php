<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
  <div class="navbar-header">
    <ul class="nav navbar-nav flex-row">
      <li class="nav-item mr-auto">
        <a class="navbar-brand" href="#">
                    
                    <h2 class="brand-text mb-0">HRM Admin</h2>
                </a>
            </li>
            <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i><i class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block collapse-toggle-icon primary" data-ticon="icon-disc"></i></a></li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">			
			
			<?php if(Auth::user()->id == 9428){ ?> 
			<li class="nav-item"><a href="<?php echo e(route('admin.support-dashboard')); ?>"><i class="feather icon-info"></i><span class="menu-title" data-i18n="User">Message to Chairman Dashboard <sup class="text-warning">New</sup></span></a></li>
		  <li class="nav-item"><a href="<?php echo e(route('admin.support-enquiry')); ?>"><i class="feather icon-activity"></i><span class="menu-title" data-i18n="User">Message to Chairman Enquiry <sup class="text-warning">New</sup></span></a></li>

		
			<?php }else{ ?>
			
			
			<?php if(Auth::user()->id == 8473){ ?>
			<li class="nav-item <?php echo e(Request::is('admin') || Request::is('admin/dashboard') ? 'active' : ''); ?>">
				<a href="<?php echo e(route('admin.dashboard')); ?>"><i class="feather icon-home"></i><span class="menu-title" data-i18n="Dashboard">Dashboard</span>
				</a>
			</li>
			<li class="<?php echo e(Request::is('admin/faculty-early-delay-reports') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.faculty-early-delay-reports')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Faculty Early/Delay Reports</span></a>
			<li><a href="<?php echo e(route('admin.academic.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Academic Dashboard</span></a>
						  </li>
					</li>
					
			<?php }else{ ?>
			<?php if(Auth::user()->role_id == 32){ ?>
			<li><a href="<?php echo e(route('admin.attendance-dashboard')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Attendance Dashboard</span></a></li>
			<li><a href="<?php echo e(route('admin.student-attendence-record')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Stu. Attendance Record</span></a></li> 
			<?php }else{ ?>
			
			<li class="nav-item <?php echo e(Request::is('admin') || Request::is('admin/dashboard') ? 'active' : ''); ?>">
				<a href="<?php echo e(route('admin.dashboard')); ?>"><i class="feather icon-home"></i><span class="menu-title" data-i18n="Dashboard">Dashboard</span>
				</a>
			</li>
			
			<?php if(Auth::user()->id==901 || Auth::user()->id==5409){ ?>
			<li class="nav-item">
				<a href="<?php echo e(route('admin.freelancer.faculty-invoice-history')); ?>"><i class="feather icon-home"></i><span class="menu-title" data-i18n="Dashboard">Faculty Invoice</span>
				</a>
			</li>
			<?php } ?>
		
			<?php if(Auth::user()->role_id == 28 || Auth::user()->role_id == 25 || Auth::user()->user_details->degination == "CENTER HEAD"){ ?>
			<li class="nav-item"><a href="<?php echo e(route('admin.branch-product-inventory')); ?>"><i class="feather icon-codepen"></i><span class="menu-title" data-i18n="User">Inventory <sup>(For Center Head)</sup></span></a>
			<!-- 
			<li class="nav-item"><a href="<?php echo e(route('admin.request-inventory')); ?>"><i class="feather icon-codepen"></i><span class="menu-title" data-i18n="User">Request Inventory</span></a>
			-->
			<?php } ?>
		
			<?php if(Auth::user()->role_id == 29 || Auth::user()->is_cxo==1 || Auth::user()->user_details->degination == "CATEGORY HEAD" || Auth::user()->user_details->degination == "CITY HEAD" || Auth::user()->user_details->degination == "CENTER HEAD"){ ?>		
				<li class="nav-item"><a href="#"><i class="feather icon-wind"></i><span class="menu-title" data-i18n="User">Academic</span></a>
					<ul class="menu-content">
					  <?php if(Auth::user()->role_id == 29 || Auth::user()->is_cxo==1 || Auth::user()->user_details->degination == "CATEGORY HEAD" || Auth::user()->user_details->degination == "CITY HEAD"){ ?>	
						  <li><a href="<?php echo e(route('admin.academic.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Academic Dashboard</span></a>
						  </li>
						<?php } ?>

						<?php if(Auth::user()->role_id == 29 || Auth::user()->user_details->degination == "CITY HEAD" || Auth::user()->user_details->degination == "CENTER HEAD"){
						?>
						  <li class=""><a href="<?php echo e(route('admin.academic.attendance')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Academic Attendance</span></a>
						  </li>  
						<?php } ?>                    
				  </ul>
				</li>
		<?php } ?>

		<?php if(Auth::user()->role_id == 29 || Auth::user()->role_id == 24){ ?>
			<li class="nav-item"><a href="#"><i class="feather icon-wind"></i><span class="menu-title" data-i18n="User">Holiday</span></a>
				<ul class="menu-content">
				  <li class="<?php echo e(Request::is('admin/holiday/create') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.holiday.create')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Holiday</span></a>
				  </li>
				  <li class="<?php echo e(Request::is('admin/holiday') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.holiday.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
				  </li>                       
			  </ul>
			</li>
			<li class="nav-item"><a href="<?php echo e(route('admin.document.index')); ?>"><i class="feather icon-printer"></i><span class="menu-title" data-i18n="User">Employee Document</span></a>
		<?php } ?>
		
		
			<?php if(Auth::user()->role_id == 29 || Auth::user()->role_id == 30 || Auth::user()->role_id == 25 || Auth::user()->role_id == 31 || Auth::user()->id==8799 || Auth::user()->id==6859 || Auth::user()->department_type==10 || Auth::user()->role_id==33){ ?>
			<li class="nav-item"><a href="#"><i class="feather icon-database"></i><span class="menu-title" data-i18n="User">Inventory Management</span></a>
				<ul class="menu-content">	
				
					<?php /*if(Auth::user()->id==8799 || Auth::user()->id==5362 || Auth::user()->id==5409){ ?>
					<li class=""><a href="{{ route('admin.buyer.vendor-new-list') }}"><i class="feather icon-list"></i><span class="menu-item" data-i18n="List">New Buyer List</span></a></li>
					<?php } */ ?>
					<li class="<?php echo e(Request::is('admin/buyer') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.buyer.index')); ?>"><i class="feather icon-list"></i><span class="menu-item" data-i18n="List">Buyer List</span></a></li>
					<li class="<?php echo e(Request::is('admin/buyer/create') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.buyer.create')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Buyer</span></a></li>

					<?php if(Auth::user()->department_type != 10 || Auth::user()->id == 8799){ ?>
						<li class="nav-item"><a href="<?php echo e(route('admin.category.index')); ?>"><i class="feather icon-git-branch"></i><span class="menu-title" data-i18n="User">Category</span></a></li>
						<li class="<?php echo e((Request::is('admin/product') || Request::is('admin/product/create')) ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.product.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Products</span></a></li>
						<li class="<?php echo e((Request::is('admin/inventory') || Request::is('admin/inventory/create')) ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.inventory.index')); ?>"><i class="feather icon-list"></i><span class="menu-item" data-i18n="View">Inventory List</span></a></li>
						<li class="<?php echo e(Request::is('admin/transfer-branch-inventory') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.transfer-branch-inventory')); ?>"><i class="feather icon-list"></i><span class="menu-item" data-i18n="List">Branch Transfer Inventory</span></a></li>
					<?php } ?>

				</ul>
			</li>
			<?php } ?>
			<?php if(Auth::user()->role_id == 29 || Auth::user()->role_id == 30 || Auth::user()->role_id == 25 || Auth::user()->role_id == 24){ ?>
			<li class="nav-item"><a href="#"><i class="feather icon-database"></i><span class="menu-title" data-i18n="User">Asset</span></a>
				<ul class="menu-content">
					
					<li class="<?php echo e(Request::is('admin/asset/create') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.asset.create')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Asset</span></a></li>
					<li class="<?php echo e(Request::is('admin/asset') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.asset.index')); ?>"><i class="feather icon-list"></i><span class="menu-item" data-i18n="List">Asset List</span></a>
					</li>
					
					<li class="<?php echo e(Request::is('admin/asset/employee-asset') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.asset.employee-asset')); ?>"><i class="feather icon-list"></i><span class="menu-item" data-i18n="List">Employee Asset List</span></a>
					</li>				
				</ul>
			</li>
			<?php } ?>
		
			<?php if(Auth::user()->department_type == 2){ ?>
			<li class="nav-item"><a href="#"><i class="feather icon-database"></i><span class="menu-title" data-i18n="User">My Asset</span></a>
				<ul class="menu-content">
					<li class="<?php echo e(Request::is('admin/asset_pro') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.asset_pro.index')); ?>"><i class="feather icon-list"></i><span class="menu-item" data-i18n="List">Asset List</span></a>
					</li>
					<!--
					<li class="<?php echo e(Request::is('admin/asset_pro/employee-asset-pro') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.asset_pro.employee-asset-pro')); ?>"><i class="feather icon-list"></i><span class="menu-item" data-i18n="List">Employee Asset List</span></a>
					</li>
					-->			
				</ul>
			</li>
			<?php } ?>
		
			<li class="nav-item">
				<a href="#">
					<i class="feather icon-database"></i><span class="menu-title" data-i18n="User">Asset Request</span>  
					
					<?php 
						$nID 	= Auth::user()->id; 					
						$nRole  = Auth::user()->role_id; 	
						
						$approval = DB::table('users')->where('approval_id',$nID)->get();
						
						if($nRole==21 || $nRole==25 || $nRole==31 || $nID==8799 || $nID==6859){
							
							if($nRole==21){
								//Department Head
								$nCount = DB::table('asset_request_notification')->where('status', '=', '0')->where('receiver_id', 'LIKE', '%' . $nID . '%')->count();
							}else if($nRole==25){
								//Inventory
								$nCount = DB::table('asset_request_notification')->where('status', '=', '1')->where('it_status', '=', '0')->count();
							}else if($nRole==31){
								//Purchase Team
								$nCount = DB::table('asset_request_notification')->where('dm_status', '=', '1')->where('purchase_status', '=', '0')->count();
							}else if($nID==8799 || $nID==6859){
								//PO
								$nCount = DB::table('asset_request_notification')->where('it_status', '=', '2')->where('dm_status', '=', '0')->count();
							}else{
								$nCount = 0;
							}
							
							if($nCount > 0){
					?>
					<span style="color:#ff0000;">&#8727;</span>
					<?php } } ?>
				</a>			
				<ul class="menu-content">			
					<?php if(Auth::user()->role_id == 25 || Auth::user()->role_id == 29 || Auth::user()->role_id == 31 || Auth::user()->id == 8799 || Auth::user()->id == 6859){ ?>
					
					<li class="nav-item">
						<a href="#"><i class="feather icon-database"></i><span class="menu-title" data-i18n="User">Report</span></a>
						<ul class="menu-content">
							<!--
							
							<li><a href="<?php echo e(route('admin.request.reports.location-request-list')); ?>"><i class="feather icon-list"></i><span class="menu-item" data-i18n="List">Location Request List</span></a></li>
							-->
							<li><a href="<?php echo e(route('admin.request.reports.inventory-valuation')); ?>"><i class="feather icon-list"></i><span class="menu-item" data-i18n="List">Inventory Valuation</span></a></li>
							<li><a href="<?php echo e(route('admin.request.reports.monthly-master-data')); ?>"><i class="feather icon-list"></i><span class="menu-item" data-i18n="List">Monthly Master Data</span></a></li>
							<li><a href="<?php echo e(route('admin.store-purchase-dashboard')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Store/Pur. Dashboard</span></a></li>
						</ul>
					</li>
					<?php } ?>
					
					<li><a href="<?php echo e(route('admin.request.add-request')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add</span></a></li>
					<li><a href="<?php echo e(route('admin.request.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">View</span></a></li>
					
					<?php if(Auth::user()->role_id == 29 || Auth::user()->role_id == 31 || Auth::user()->role_id == 30 || Auth::user()->role_id == 21 || Auth::user()->role_id == 25 || Auth::user()->id == 8799 || Auth::user()->id == 6859 || Auth::user()->user_details->degination == "BRANCH HEAD" || Auth::user()->user_details->degination == "CENTER HEAD" || Auth::user()->role_id == 33 || !empty($approval)){ ?>
					
					<li><a href="<?php echo e(route('admin.request.requisition-request')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Requisition Request </span></a></li>
					<?php } ?>
					
					<?php if(Auth::user()->role_id == 25){ ?>
					<li><a href="<?php echo e(route('admin.request.request-approval')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">InStock Request Approval</span></a></li>
					<?php } ?>
					
					<?php if(Auth::user()->role_id == 29 || Auth::user()->role_id == 31 || Auth::user()->id == 8799 || Auth::user()->id == 6859 || Auth::user()->department_type == 10 || Auth::user()->user_details->degination == "MANAGER-PURCHASE & STORE"){ ?>
					<li><a href="<?php echo e(route('admin.request.manual-invoice')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Manual Invoice</span></a></li>
					<?php } ?>
					
					<?php if(Auth::user()->role_id == 31 || Auth::user()->role_id == 29 || Auth::user()->id == 8799 || Auth::user()->id == 6859 || Auth::user()->department_type == 10 || Auth::user()->user_details->degination == "MANAGER-PURCHASE & STORE"){ ?>
					<li><a href="<?php echo e(route('admin.request.po-list')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">PO / WO</span></a></li>
					<li><a href="<?php echo e(route('admin.request.vendor-invoice')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Vendor Invoice</span></a></li>
					<?php }  ?>
					
					<?php if(Auth::user()->role_id == 31 || Auth::user()->role_id == 29 || Auth::user()->id == 8799 || Auth::user()->id == 901 || Auth::user()->user_details->degination == "MANAGER-PURCHASE & STORE"){ ?>
					<li><a href="<?php echo e(route('admin.request-pending-accept')); ?>"><i class="feather icon-list"></i><span class="menu-item" data-i18n="List">Request Pending Accept</span></a></li>
					<?php } ?>
					
					
					<?php if(Auth::user()->role_id == 31 || Auth::user()->role_id == 29 || Auth::user()->role_id == 34 || Auth::user()->user_details->degination == "MANAGER-PURCHASE & STORE"){ ?>
					<li><a href="<?php echo e(route('admin.request.maintenance-list')); ?>"><i class="feather icon-list"></i><span class="menu-item" data-i18n="List">Maintenance Report</span></a></li>
					<?php } ?>
					
					<?php if(Auth::user()->role_id == 29 || Auth::user()->role_id == 31 || Auth::user()->role_id == 25 || Auth::user()->id == 8799 || Auth::user()->id == 6859 || Auth::user()->role_id == 33){ ?>
					<li><a href="<?php echo e(route('admin.request.requested-asset-by-hr')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Requested Asset By HR</span></a></li>
					<li><a href="<?php echo e(route('admin.request.return-product-list')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Return Products List</span></a></li>
					<?php } ?>
				</ul>
			</li>
		
			<?php 
			//,'anandkarwa2010@gmail.com','projectmanager@utkarsh.com','hr@utkarsh.com','jay+1@gmail.com'
			if(in_array(Auth::user()->email,array('admin@gmail.com'))){ ?>
				<!--li class="nav-item"><a href="<?php echo e(route('admin.salary.index')); ?>"><i class="feather icon-printer"></i><span class="menu-title" data-i18n="User">Salary</span></a></li-->
				<li class="nav-item"><a href="#"><i class="feather icon-printer"></i><span class="menu-title" data-i18n="User">Salary</span></a>
					<ul class="menu-content">
					  <li class="<?php echo e(Request::is('admin/salary') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.salary.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Salary</span></a>
					  </li>
					  <!--li class="<?php echo e(Request::is('admin/import-salary') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.import-salary')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Import Salary</span></a>
					  </li>
						<li class="<?php echo e(Request::is('admin/add-increment') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.add-increment')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Add Increment</span></a>
					  </li--> 
					</ul>
				
				</li>
			<?php } ?>
		
		
			<?php if(Auth::user()->role_id != 31 && Auth::user()->role_id != 28 && Auth::user()->role_id !=24 && Auth::user()->role_id !=21 && Auth::user()->role_id != 20 && Auth::user()->role_id != 35 && Auth::user()->user_details->degination != 'STUDIO ASSISTANT MANAGER' && Auth::user()->user_details->degination != 'TIME TABLE MANAGER' && Auth::user()->role_id != 16 && Auth::user()->role_id != 6 && Auth::user()->role_id != 22 && Auth::user()->role_id != 23 && Auth::user()->role_id != 25 && Auth::user()->role_id != 26 && Auth::user()->role_id != 30 && Auth::user()->role_id != 3 && Auth::user()->role_id != 2 && Auth::user()->role_id != 33 && Auth::user()->role_id != 34){ ?>
			<li class="nav-item"><a href="#"><i class="feather icon-list"></i><span class="menu-title" data-i18n="User">Roles</span></a>
				<ul class="menu-content">
				  <li class="<?php echo e(Request::is('admin/roles/create') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.roles.create')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Role</span></a>
				  </li>
				  <li class="<?php echo e(Request::is('admin/roles') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.roles.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
				  </li>                       
			  </ul>
			</li>
			<?php } ?>
		
		
			<?php if(Auth::user()->role_id != 31 && Auth::user()->role_id != 28 && Auth::user()->role_id !=24 && Auth::user()->role_id !=21 && Auth::user()->role_id != 20 && Auth::user()->role_id != 35 && Auth::user()->role_id != 16 && Auth::user()->role_id != 6 && Auth::user()->role_id != 22 && Auth::user()->role_id != 23 && Auth::user()->role_id != 25 && Auth::user()->role_id != 26 && Auth::user()->role_id != 30 && Auth::user()->role_id != 3 && Auth::user()->role_id != 2 && Auth::user()->role_id != 33 && Auth::user()->role_id != 34){ ?>
			<li class="nav-item"><a href="#"><i class="fa fa-sitemap"></i><span class="menu-title" data-i18n="User">Branch</span></a>
				<ul class="menu-content">
				  <li class="<?php echo e(Request::is('admin/branch/create') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.branch.create')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Branch</span></a>
				  </li>
				  <li class="<?php echo e(Request::is('admin/branch') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.branch.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
				  </li>                       
				</ul>		
			</li>
			<?php } ?>
			<?php if(Auth::user()->role_id != 31 && Auth::user()->role_id != 28 && Auth::user()->role_id != 20 && Auth::user()->role_id != 35 && Auth::user()->user_details->degination != 'STUDIO ASSISTANT MANAGER' && Auth::user()->user_details->degination != 'TIME TABLE MANAGER' && Auth::user()->role_id != 16 && Auth::user()->role_id != 6 && Auth::user()->role_id != 22 && Auth::user()->role_id != 23 && Auth::user()->role_id != 25 && Auth::user()->role_id != 26 && Auth::user()->role_id != 30 && Auth::user()->role_id != 3 && Auth::user()->role_id != 2 && Auth::user()->role_id != 33 && Auth::user()->role_id != 34){ ?>
			<li class="nav-item"><a href="#"><i class="feather icon-user"></i><span class="menu-title" data-i18n="User">Employees</span></a>
			<ul class="menu-content">
				<?php if(Auth::user()->role_id !=21){ ?>
					<li class="<?php echo e(Request::is('admin/employees/create') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.employees.create')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Employee</span></a>
					</li>
				<?php } ?>
				<li class="<?php echo e(Request::is('admin/employees') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.employees.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
				</li> 

				<?php if(Auth::user()->role_id == 29){ ?>
					<li class="<?php echo e(Request::is('admin/employee/esic-no-detail') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.employees.esic-no-detail')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">ESI NO.</span></a>
					</li> 

					<li class="<?php echo e(Request::is('admin/employee/uan-no-detail') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.employees.uan-no-detail')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">UAN NO.</span></a>
					</li> 
					
					<li class="<?php echo e(Request::is('admin/employee/add-supervisor') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.employees.add-supervisor')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Add Supervisor</span></a>
					</li> 
					
					<li class="<?php echo e(Request::is('admin/employee/remove-supervisor') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.employees.remove-supervisor')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Remove Supervisor</span></a>
					</li> 
					
				<?php } ?> 	
				<?php if(Auth::user()->role_id == 21 || Auth::user()->role_id == 24 || Auth::user()->role_id == 29){ ?>
					<li class="<?php echo e(Request::is('admin/employee/job-role') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.employees.job-role')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Job Role</span></a>
					</li> 
				<?php } ?> 	
				
				<?php if(Auth::user()->role_id == 24 || Auth::user()->role_id == 29){ ?>
					<li class="<?php echo e(Request::is('admin/employee/probation-month') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.employees.probation-month')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Probation Month</span></a>
					</li> 
				<?php } ?>

					<li class="<?php echo e(Request::is('admin/employee/birthday') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.employees.birthday')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Birthday</span></a>
					</li> 
					
					<li class="<?php echo e(Request::is('admin/employee/work-anniversary') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.employees.work-anniversary')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Work Anniversary</span></a>
					</li> 			
			</ul>
			</li>
			<?php } ?>
		
			<?php if(Auth::user()->role_id != 24 || Auth::user()->role_id != 29){ ?>
			  <li class="<?php echo e(Request::is('admin/employee/view-job-role') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.employees.view-job-role')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View Job Role</span></a>
			</li> 
			<?php } ?>
		
			<?php
			$studio_incharge = false;
			$modules = false;
			if(Auth::user()->role_id == 21){
				if(Auth::user()->role_id == 21 && Auth::user()->department_type == 2){
					$modules = true;
				}
			}
			else if(Auth::user()->role_id != 31 && Auth::user()->role_id != 20  && Auth::user()->role_id != 35 && Auth::user()->role_id != 28 && Auth::user()->role_id != 16 && Auth::user()->role_id != 6 && Auth::user()->role_id != 22 && Auth::user()->role_id != 23 && Auth::user()->role_id != 25 && Auth::user()->role_id != 26 && Auth::user()->role_id != 30 && Auth::user()->role_id != 3  && Auth::user()->role_id != 2 && Auth::user()->role_id != 33 && Auth::user()->role_id != 34){
				if(Auth::user()->role_id == 24){
					$modules = false;
				}
				else{
					$modules = true;
				}
			}
			else if(Auth::user()->user_details->degination=='STUDIO INCHARGE'){
				$modules = true;
				$studio_incharge = true;
			}
			?>
			<?php if($modules){
				if($studio_incharge==false){
			?>
			
			<li class="nav-item"><a href="#"><i class="feather icon-user"></i><span class="menu-title" data-i18n="User">Send Links</span></a>
				<ul class="menu-content">
					<li class="<?php echo e(Request::is('admin/links') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.links.faculty')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Faculty</span></a>
					</li>                       
			  </ul>
			</li>
			<?php
				}
				?>
			
			<!--li class="nav-item"><a href="#"><i class="feather icon-user"></i><span class="menu-title" data-i18n="User">Drivers</span></a>
				<ul class="menu-content">
					<li class="<?php echo e(Request::is('admin/drivers') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.drivers.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
					</li>                       
			  </ul>
			</li-->
		
		
			<li class="nav-item"><a href="#"><i class="fa fa-video-camera"></i><span class="menu-title" data-i18n="User">Studios</span></a>
				<ul class="menu-content">
				<?php if($studio_incharge==false){ ?>
				  <li class="<?php echo e(Request::is('admin/studios/create') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.studios.create')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Studio</span></a>
				  </li>
				
				  <li class="<?php echo e(Request::is('admin/studios') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.studios.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a> 
				  </li>  
				  
				  <li class="<?php echo e(Request::is('admin/assigned-incharge') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.assigned-incharge')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Assigned Incharge</span></a>
					</li>
				<?php } ?>

					<li class="<?php echo e(Request::is('admin/assigned-assistants') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.assigned-assistants')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Assigned Assistants</span></a>
					</li> 
			  </ul>
			</li>
			<?php } ?>
		
			<?php if($modules || Auth::user()->id == 1172 || Auth::user()->id == 1537){
			?> 
			<li class="nav-item"><a href="#"><i class="fa fa-calendar"></i><span class="menu-title" data-i18n="User">Time Table Management</span></a>
				<ul class="menu-content">
				 <li class="<?php echo e(Request::is('admin/timetable') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.timetable.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
				 </li> 
				 <li class="<?php echo e(Request::is('admin/timetable-history-reports') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.timetable-history-reports')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Changes History</span></a>
				 </li> 
				 
				<?php /*
					if(Auth::user()->role_id ==3){ ?>
				<li class="{{ Request::is('admin/timetables') ? 'active' : '' }}">
					<a href="{{ route('admin.timetables.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Start/End Class</span></a>
				</li>	
				<?php } */ ?>	
				
			 </ul>
			</li>
			<?php 
			} ?>
			<?php if($modules){ ?>
			<li class="nav-item">
			<a href="<?php echo e(route('admin.classchangerequest.index')); ?>"><i class="fa fa-exchange"></i><span class="menu-title" data-i18n="User">Class Change Request</span>
			</a>
			</li>
			<?php } ?>

			<?php 
				$reportArr = array(5760,1172,1537,4383,6564,7420,7241,7820,7046,8619,6141,8128,1729,7783,8462,1926,8702,8167,8509,8206,8776,7268,8861,8856,1546,5097,8922,1043,9039,5603,9578); 
				// if(in_array(Auth::user()->id, $courseArr)){
			if((Auth::user()->role_id != 31 && Auth::user()->role_id != 28 && Auth::user()->role_id != 20 && Auth::user()->role_id != 35 && Auth::user()->role_id != 24 && Auth::user()->role_id != 16 && Auth::user()->role_id != 6 && Auth::user()->role_id != 22 && Auth::user()->role_id != 23 && Auth::user()->role_id != 25 && Auth::user()->role_id != 33 && Auth::user()->role_id != 34 && Auth::user()->role_id != 26 && Auth::user()->role_id != 30  && Auth::user()->role_id != 2) || (Auth::user()->role_id ==21 &&  Auth::user()->department_type==2) || in_array(Auth::user()->id, $reportArr)){ ?>
			<li class="nav-item"><a href="#"><i class="fa fa-file-text"></i><span class="menu-title" data-i18n="User">Reports</span></a>
				<ul class="menu-content">
				
					<li class="<?php echo e(Request::is('admin/studio-reports') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.studio-reports')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Studio Reports</span></a>
					</li>
					<li class="<?php echo e(Request::is('admin/faculty-reports') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.faculty-reports')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Faculty Reports</span></a>
					</li>
					<li class="<?php echo e(Request::is('admin/batch-reports') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.batch-reports')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Batch Reports</span></a>
					</li>
					<?php 
						$reportNotArr =  array(5760,1172,1537,6564,7820,7046,8619,8128,1729,7783,1926,8702,8509,8206,8776,7268,1043,9578); 
						if( (Auth::user()->role_id != 3 ||  Auth::user()->id == 6141) && !in_array(Auth::user()->id, $reportNotArr)){ ?> 
					
					
					<li class="<?php echo e(Request::is('admin/batch-reports-shiftwise') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.batch-reports-shiftwise')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Batch Reports shift wise</span></a>
					</li>
					<!--li class="<?php echo e(Request::is('admin/dppsystem') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.dppsystem.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">DPP System</span></a>
					</li-->
					
					<li class="<?php echo e(Request::is('admin/faculty-early-delay-reports') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.faculty-early-delay-reports')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Faculty Early/Delay Reports</span></a>
					</li>

					<li class="<?php echo e(Request::is('admin/timetable-change-counts') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.timetable-change-counts')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Timetablle Change Counts</span></a>
					</li>
					
				  <?php 
					$reportNotArr =  array(901,1056,5409,1859); 
					if(in_array(Auth::user()->id, $reportNotArr)){ ?> 	
						<li class="<?php echo e(Request::is('admin/faculty-hours-reports') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.faculty-hours-reports')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Faculty Hours Reports</span></a>
						</li>
					<?php } ?>


					<li class="<?php echo e(Request::is('admin/subject-reports') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.subject-reports')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Subject Reports</span></a>
					</li>
					<li class="<?php echo e(Request::is('admin/free-faculty-reports') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.free-faculty-reports')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Free Faculty</span></a>
					</li>  
					<li class="<?php echo e(Request::is('admin/free-assistant-reports') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.free-assistant-reports')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Free Assistant</span></a>
					</li>  
					<li class="<?php echo e(Request::is('admin/reports') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.reports.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
					</li>
					<li class="<?php echo e(Request::is('typist-work-report') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.typist-work-report')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Typist Work</span></a>
					</li> 
					<li class="<?php echo e(Request::is('faculty-reports/subjects') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.faculty-reports.subjects')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Faculty Subjects</span></a>
					</li> 
					
					<li class="<?php echo e(Request::is('studio-availability') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.studio-availability')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Studio Class Report</span></a>
					</li>
					<li class="<?php echo e(Request::is('faculty-agreement-hours') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.faculty-agreement-hours')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Agreement Hours Report</span></a>
					</li> 
					<li class="<?php echo e(Request::is('batch-hours-reports') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.batch-hours-reports')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Batch Hours Report</span></a>
					</li> 
					
					<li class="<?php echo e(Request::is('admin/faculty-monthly-hours-reports') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.faculty-monthly-hours-reports')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Faculty Monthly Hours</span></a>
					</li>
					
					<li class="<?php echo e(Request::is('admin/class-type-wise') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.class-type-wise')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Class Type Wise </span></a>
					</li>
					<li class="<?php echo e(Request::is('admin/faculty-topic') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.faculty-topic')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Faculty Today</span></a>
					</li>
					<?php } ?>
				</ul>
			</li>
			<?php } ?>
			
			<?php 
				$reportFArr = array(1110,5498,7431); 
				if(in_array(Auth::user()->id, $reportFArr)){ 
			?>
			<li class="<?php echo e(Request::is('admin/faculty-topic') ? 'active' : ''); ?>">
				<a href="<?php echo e(route('admin.faculty-topic')); ?>"><i class="feather icon-circle"></i>
					<span class="menu-item" data-i18n="View">Faculty Today</span>
				</a>
			</li>
			<?php } ?>		
			
			<?php if(Auth::user()->user_details->degination=='CENTER HEAD' || Auth::user()->user_details->degination=='SR EXECUTIVE'){ ?>
				<li class="<?php echo e(Request::is('batch-hours-reports') ? 'active' : ''); ?>">
					<a href="<?php echo e(route('admin.batch-hours-reports')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Batch Hours Report</span></a>
				</li> 
			<?php } ?>
			
			<?php if($modules){ ?>
			<li class="nav-item"><a href="#"><i class="fa fa-puzzle-piece"></i><span class="menu-title" data-i18n="User">Class Managemet</span></a>
				<ul class="menu-content">
				  <li class="<?php echo e(Request::is('admin/subjects') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.subjects.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Subjects</span></a>
				  </li>
				  <li class="<?php echo e(Request::is('admin/chapters') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.chapters.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Chapters</span></a>
				  </li>
				  <!--li class="<?php echo e(Request::is('admin/topics') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.topics.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Topics</span></a>
				  </li-->                       
			  </ul>
			</li>
			<?php } ?>
			<?php if((Auth::user()->role_id != 31 && Auth::user()->role_id != 28 && Auth::user()->role_id !=21 && Auth::user()->role_id !=20 && Auth::user()->role_id != 35 && Auth::user()->role_id != 24 && Auth::user()->role_id != 16 && Auth::user()->role_id != 6 && Auth::user()->role_id != 22 && Auth::user()->role_id != 23 && Auth::user()->role_id != 25 && Auth::user()->role_id != 33 && Auth::user()->role_id != 34 && Auth::user()->role_id != 26 && Auth::user()->role_id != 30 && Auth::user()->role_id != 3  && Auth::user()->role_id != 2) || (Auth::user()->user_details->degination == "MANAGER- SALES & MARKETING" || Auth::user()->id==5970 || Auth::user()->id==5097)){ ?>
			<li class="nav-item"><a href="#"><i class="fa fa-columns"></i><span class="menu-title" data-i18n="User">Batch</span></a>
				<ul class="menu-content">
					<?php
					if(Auth::user()->user_details->degination != "MANAGER- SALES & MARKETING"){
					?>
					<li class="<?php echo e(Request::is('admin/batch/create') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.batch.create')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Batch</span></a>
					</li>
					<?php } ?>
				  <li class="<?php echo e(Request::is('admin/batch') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.batch.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
				  </li>                       
			  </ul>
			</li>
			<?php } ?>

			<?php if(Auth::user()->role_id != 31 && Auth::user()->role_id != 28 && Auth::user()->role_id !=21 && Auth::user()->role_id !=20 && Auth::user()->role_id != 35 && Auth::user()->role_id != 24 && Auth::user()->role_id != 16 && Auth::user()->role_id != 6 && Auth::user()->role_id != 22 && Auth::user()->role_id != 23 && Auth::user()->role_id != 25 && Auth::user()->role_id != 26 && Auth::user()->role_id != 30 && Auth::user()->role_id != 3  && Auth::user()->role_id != 2 && Auth::user()->role_id != 33 && Auth::user()->role_id != 34){ ?>
			<li class="nav-item"><a href="#"><i class="fa fa-book"></i><span class="menu-title" data-i18n="User">Course</span></a>
				<ul class="menu-content">
				  <li class="<?php echo e(Request::is('admin/course/create') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.course.create')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Course</span></a>
				  </li>
				  <li class="<?php echo e(Request::is('admin/course') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.course.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
				  </li>                       
			  </ul>
			</li>
			<?php } ?>


			<?php
			$modules2 = false;
			if(Auth::user()->user_details->degination != 'STUDIO ASSISTANT MANAGER' && Auth::user()->user_details->degination != 'TIME TABLE MANAGER' && Auth::user()->role_id != 28 && Auth::user()->role_id != 16 && Auth::user()->role_id != 6 && Auth::user()->role_id != 22 && Auth::user()->role_id != 23 && Auth::user()->role_id != 25 && Auth::user()->role_id != 26 && Auth::user()->role_id != 30  && Auth::user()->role_id != 2 && Auth::user()->role_id != 33 && Auth::user()->role_id != 34){
				$modules2 = true;
			}
			else if(Auth::user()->role_id == 20 || Auth::user()->role_id != 35 || Auth::user()->role_id ==29){
				$modules2 = true;
			}
			?>

			<?php if($modules2){ ?>
			<li class="nav-item"><a href="#"><i class="fa fa-bell"></i><span class="menu-title" data-i18n="User">Notifications</span></a>
				<ul class="menu-content">
				 <?php if(Auth::user()->role_id !=20 && Auth::user()->role_id != 35 && Auth::user()->role_id !=3 && Auth::user()->role_id != 31  && Auth::user()->role_id != 2){ ?>
				  <li class="<?php echo e(Request::is('admin/notification/create') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.notification.create')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add</span></a>
				  </li>
				  <?php } ?>
				  <li class="<?php echo e(Request::is('admin/notification') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.notification.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
				  </li>                       
			  </ul>
			</li>
			<?php } ?>
			<?php if(Auth::user()->role_id !=20 ){ ?>
			<!--<li class="nav-item"><a href="#"><i class="feather icon-file"></i><span class="menu-title" data-i18n="User">Task</span></a>
				<ul class="menu-content">
				  <li class="<?php echo e(Request::is('admin/task/create') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.task.create')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Task</span></a>
				  </li>
				  <li class="<?php echo e(Request::is('admin/task') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.task.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
				  </li>                       
			  </ul>
			</li>-->
			<?php } ?>
			<?php //if(Auth::user()->user_details->degination != 'STUDIO ASSISTANT MANAGER' && Auth::user()->user_details->degination != 'TIME TABLE MANAGER' && Auth::user()->role_id != 28){ ?>
			
			<!--
			<li class="nav-item"><a href="#"><i class="fa fa-tasks"></i><span class="menu-title" data-i18n="User">Task</span></a>
				<ul class="menu-content">
				  <li class="<?php echo e(Request::is('admin/task/create') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.task.create')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Task</span></a>
				  </li>
				  <li class="<?php echo e(Request::is('admin/task') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.task.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
				  </li>                       
			  </ul>
			</li>
			-->
			
			<li class="nav-item">
				<a href="#"><i class="feather icon-user-plus"></i><span class="menu-title" data-i18n="User">Task</span></a>
				<ul class="menu-content">
					<li class="">
					<a href="<?php echo e(route('admin.task-add')); ?>"><i class="feather icon-database"></i><span class="menu-title" data-i18n="User">Add Task</span></a>
					</li>    
					<li class="">
						<a href="<?php echo e(route('admin.view-task')); ?>"><i class="feather icon-slack"></i><span class="menu-title" data-i18n="User">View Task</span></a>
					</li>
				</ul>
			</li>
			
			<!--li class="nav-item"><a href="#"><i class="feather icon-file"></i><span class="menu-title" data-i18n="User">New Task</span></a>
				<ul class="menu-content">
					<li class="<?php echo e(Request::is('admin/newtask/create') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.newtask.create')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add New Task</span></a>
					</li>
					<li class="<?php echo e(Request::is('admin/newtask') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.newtask.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View New Task</span></a>
					</li>  
					<li class="<?php echo e(Request::is('admin/newtask/open-task') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.newtask.open-task')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Open New Task</span></a>
					  </li>	  
			  </ul>
			</li-->
			<?php //} ?>
			
			<?php
			$modules3 = false;
			if(Auth::user()->user_details->degination != 'STUDIO ASSISTANT MANAGER' && Auth::user()->role_id != 16 && Auth::user()->role_id != 6 && Auth::user()->role_id != 22 && Auth::user()->role_id != 23 && Auth::user()->role_id != 25 && Auth::user()->role_id != 26 && Auth::user()->role_id != 30  && Auth::user()->role_id != 2){
				$modules3 = true;
			}
			?>
			<?php if(Auth::user()->role_id != 25  && Auth::user()->role_id != 2){ ?> 
			<li class="nav-item"><a href="#"><i class="feather icon-clock"></i><span class="menu-title" data-i18n="User">Attendance</span></a>
				<ul class="menu-content">
					<?php //if(Auth::user()->role_id !=20 && Auth::user()->role_id !=21){ ?>
					
					<?php //} ?>
					<?php if(Auth::user()->role_id != 31 && Auth::user()->role_id !=20 && Auth::user()->role_id != 35 && Auth::user()->role_id !=16 && Auth::user()->role_id != 6 && Auth::user()->role_id != 22 && Auth::user()->role_id != 23 && Auth::user()->role_id != 25 && Auth::user()->role_id != 26 && Auth::user()->role_id != 30 && Auth::user()->role_id != 3 && Auth::user()->role_id != 33 && Auth::user()->role_id != 34){ ?>
					
					<?php if(Auth::user()->role_id == 29 || Auth::user()->role_id == 30 || Auth::user()->role_id == 24 ){ ?>
					<li class="<?php echo e(Request::is('admin/attendance/create') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.attendance.create')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Attendance</span></a>
					</li>
					<?php } ?>
					<!--li class="<?php echo e(Request::is('admin/attendance') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.attendance.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">App Attendance</span></a>
					</li-->
					<!--li class="<?php echo e(Request::is('admin/rp-attendance') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.attendance.rpattendance')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">RFID Attendance</span></a>
					</li-->
					<?php } ?>

					<?php if(Auth::user()->role_id != 31 && Auth::user()->role_id !=20 && Auth::user()->role_id != 35 && Auth::user()->role_id != 3) {?>
						<li class="<?php echo e(Request::is('admin/attendence-record') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.attendance.attendencerecord')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Attendence Record</span></a>
						</li>
					<?php } ?>


					<li class="<?php echo e(Request::is('admin/attendance/full-attendence') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.attendance.fullattendence')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Full Attendance</span></a>
					</li>
					<li class="<?php echo e(Request::is('admin/attendance/absent-full-attendence') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.attendance.absentfullattendence')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Absent Full Attendance</span></a>
					</li>
					<?php if(Auth::user()->role_id != 31 && Auth::user()->role_id !=20 && Auth::user()->role_id !=28 &&  Auth::user()->role_id !=16 && Auth::user()->role_id != 6 && Auth::user()->role_id != 22 && Auth::user()->role_id != 23 && Auth::user()->role_id != 25 && Auth::user()->role_id != 26 && Auth::user()->role_id != 30 && Auth::user()->role_id != 3 && Auth::user()->role_id != 33 && Auth::user()->role_id != 34){ ?>
					<li class="<?php echo e(Request::is('admin/attendance/users/absent') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.attendance.absentuser')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Absent Users</span></a>
					</li>
					
					<li class="<?php echo e(Request::is('admin/attendance/gallery') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.attendance.gallery')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Gallery</span></a>
					</li>
					<?php }
					else if(Auth::user()->user_details->degination == "CENTER HEAD"){
						?>
						<li class="<?php echo e(Request::is('admin/attendance/gallery') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.attendance.gallery')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Gallery</span></a>
						</li>
						<?php
					}
					?>

					<?php /*if(Auth::user()->role_id==21 || Auth::user()->role_id==24 || Auth::user()->role_id==29){ ?>
					<li class="{{ Request::is('admin/attendence-record') ? 'active' : '' }}">
						<a href="attendence-record"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Attendance Record</span></a>
					</li>
				   <?php }*/ ?>
									
					<?php if(Auth::user()->role_id ==21 || Auth::user()->role_id ==24 || Auth::user()->role_id ==29 || Auth::user()->role_id ==30){ ?>
					<li class="<?php echo e(Request::is('admin/attendance/incomplete-attendence') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.attendance.incompleteattendence')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Incomplete Attendance</span></a>
					</li>
					<?php } ?>
					
					
					<?php if(Auth::user()->role_id == 29){ ?>
					<li class="<?php echo e(Request::is('admin/attendance/final-attendence') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.attendance.final-attendence')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Final Attendence</span></a>
					</li>
					
					<li class="<?php echo e(Request::is('admin/leave-wages') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.leave.leave-wages')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Leave Wages</span></a>
					</li>
					
					<li class="<?php echo e(Request::is('admin/attendance-lock/index') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.attendance-lock.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Lock Attendance/Leave</span></a>
					</li>
					
					<?php } ?>
	 
			  </ul>
			</li>
			<?php } ?>
			<?php if($modules3){ ?>
			<li class="nav-item"><a href="#"><i class="feather icon-calendar"></i><span class="menu-title" data-i18n="User">Leave</span></a>
				<ul class="menu-content">
				
					<li class="<?php echo e(Request::is('admin/create') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.leave.create')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Add Leave</span></a>
					</li>		
					<li class="<?php echo e(Request::is('admin/leave') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.leave.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Leave List</span></a>
					</li> 
					<?php if(Auth::user()->role_id == 29 || Auth::user()->role_id == 24){ ?>
						<li class="<?php echo e(Request::is('admin/paternity-leave-list') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.leave.paternity_leave_list')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Paternity Leave</span></a>
				</li> 
					<?php } ?>
					<li class="<?php echo e(Request::is('admin/leave-count-view') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.leave.leavecount')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Leave Count View</span></a>
					</li>
					<?php if(Auth::user()->role_id == 29 || Auth::user()->role_id == 21 || Auth::user()->role_id == 24){ ?>
					<li class="<?php echo e(Request::is('admin/leave-full-detail') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.leave.leave-full-detail')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Leave Full Details</span></a>
					</li>
					<?php } ?>
					<?php if(Auth::user()->role_id == 29){ ?>
					<!--li class="<?php echo e(Request::is('admin/approved-leave') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.leave.approved-leave')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Approved Leave</span></a>
					</li--> 
					<?php } ?>	
				</ul>
			</li>
			<?php } ?>
			<?php if((Auth::user()->role_id != 31 && Auth::user()->role_id != 28 && Auth::user()->role_id != 24 && Auth::user()->role_id !=20 && Auth::user()->role_id != 35 && Auth::user()->user_details->degination != 'STUDIO ASSISTANT MANAGER' && Auth::user()->user_details->degination != 'TIME TABLE MANAGER' && Auth::user()->role_id != 16 && Auth::user()->role_id != 6 && Auth::user()->role_id != 22 && Auth::user()->role_id != 23 && Auth::user()->role_id != 25 && Auth::user()->role_id != 33 && Auth::user()->role_id != 34 && Auth::user()->role_id != 26 && Auth::user()->role_id != 30  && Auth::user()->role_id != 2) && Auth::user()->role_id != 3 || (Auth::user()->department_type == 10)){ ?>
			<li class="nav-item"><a href="#"><i class="feather icon-package"></i><span class="menu-title" data-i18n="User">Invoice</span></a>
				<ul class="menu-content">
				  <li class="<?php echo e(Request::is('admin.invoice.create') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.invoice.create')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Add Invoice</span></a>
				  </li>  
				  <li class="<?php echo e(Request::is('admin/invoice') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.invoice.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Tax Invoice</span></a>
				  </li>  
				<li class="<?php echo e(Request::is('admin.invoice.credit-note') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.invoice.credit-note')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Credit Note</span></a>
				</li>  	  
			  </ul>
			</li>
			<?php } ?>
			<?php
			$modules4 = false;
			if(Auth::user()->user_details->degination != 'STUDIO ASSISTANT MANAGER' && Auth::user()->user_details->degination != 'TIME TABLE MANAGER' && Auth::user()->role_id != 28 && Auth::user()->role_id != 16 && Auth::user()->role_id != 6 && Auth::user()->role_id != 22 && Auth::user()->role_id != 23 && Auth::user()->role_id != 25 && Auth::user()->role_id != 33 && Auth::user()->role_id != 34 && Auth::user()->role_id != 26 && Auth::user()->role_id != 30  && Auth::user()->role_id != 2){
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
				  <li class="<?php echo e(Request::is('admin/staff') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.staff.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Staff Movement System</span></a>
				  </li>   	  
			  </ul>
			</li>
			<?php } ?>
			<?php if(Auth::user()->role_id != 31 && Auth::user()->role_id != 28 && Auth::user()->role_id != 24 && Auth::user()->role_id !=21 && Auth::user()->role_id !=20 && Auth::user()->role_id != 35 && Auth::user()->user_details->degination != 'STUDIO ASSISTANT MANAGER' && Auth::user()->user_details->degination != 'TIME TABLE MANAGER' && Auth::user()->role_id != 16 && Auth::user()->role_id != 6 && Auth::user()->role_id != 22 && Auth::user()->role_id != 23 && Auth::user()->role_id != 25 && Auth::user()->role_id != 33 && Auth::user()->role_id != 34 && Auth::user()->role_id != 26 && Auth::user()->role_id != 30 && Auth::user()->role_id != 3  && Auth::user()->role_id != 2){ ?>
			<li class="nav-item"><a href="#"><i class="fa fa-folder-open"></i><span class="menu-title" data-i18n="User">Department</span></a>
				<ul class="menu-content">
					<li class="<?php echo e(Request::is('admin/department/create') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.department.create')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Department</span></a>
					</li>				
					<li class="<?php echo e(Request::is('admin/sub_department') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.sub_department.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Add Sub Department</span></a>
					</li>
					<li class="<?php echo e(Request::is('admin/department') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.department.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View Department</span></a>
					</li>
				</ul>
			</li>
			
			<li class="nav-item"><a href="#"><i class="fa fa-address-book"></i><span class="menu-title" data-i18n="User">Designation</span></a>
				<ul class="menu-content">
					<li class="<?php echo e(Request::is('admin/designation/create') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.designation.create')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add Designation</span></a>
					</li>
					<li class="<?php echo e(Request::is('admin/designation') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.designation.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View Designation</span></a>
					</li>                       
				</ul>
			</li>
			<?php } ?>
			
			<?php
			if(Auth::user()->department_type == 37){
				?>
				<li class="nav-item"><a href="#"><i class="fa fa-file-video-o"></i><span class="menu-title" data-i18n="User">Training</span></a>
					<ul class="menu-content">
						<li class="<?php echo e(Request::is('admin/training_video_category') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.training_video_category.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Category</span></a>
						</li>    
						<li class="<?php echo e(Request::is('admin/training_video') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.training_video.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Video</span></a>
						</li>  
						<li class="<?php echo e(Request::is('admin/training_pdf') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.training_pdf.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">PDF</span></a>  
					</li> 					
					</ul>
				</li>
				<?php
			}
			?>
			<?php if(Auth::user()->role_id == 29){ ?>
			<li class="nav-item"><a href="#"><i class="fa fa-link"></i><span class="menu-title" data-i18n="User">Knowledge Based</span></a>
				<ul class="menu-content">
					<li class="<?php echo e(Request::is('admin/knowledge_based_category') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.knowledge_based_category.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Category</span></a>
					</li>    
					<li class="<?php echo e(Request::is('admin/knowledge_based') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.knowledge_based.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
					</li>                       
				</ul>
			</li>

			<li class="nav-item"><a href="#"><i class="fa fa-file-video-o"></i><span class="menu-title" data-i18n="User">Training</span></a>
				<ul class="menu-content">
					<li class="<?php echo e(Request::is('admin/training_video_category') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.training_video_category.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Category</span></a>
					</li>    
					<li class="<?php echo e(Request::is('admin/training_video') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.training_video.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Video</span></a>
					<li class="<?php echo e(Request::is('admin/training_pdf') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.training_pdf.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">PDF</span></a>
					</li> 
					</li>                       
				</ul>
			</li>
			<?php } ?>
			
			<li class="nav-item"><a href="#"><i class="fa fa-laptop"></i><span class="menu-title" data-i18n="User">Meeting</span></a>
				<ul class="menu-content">  
					<?php if(Auth::user()->role_id == 29){ ?>
					<li class="<?php echo e(Request::is('admin/meeting-places') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.meeting-places')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Meeting Places</span></a>
					</li>
					<?php } ?>
					<li class="<?php echo e(Request::is('admin/appointment') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.appointment')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Meeting</span></a>
					</li>  
				</ul>
			</li> 
			
			<?php if(Auth::user()->role_id == 29){ ?>
			<li class="nav-item"><a href="#"><i class="fa fa-money"></i><span class="menu-title" data-i18n="User">Expense</span></a>
				<ul class="menu-content">
					<li class="<?php echo e(Request::is('admin/expense_category') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.expense_category.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Category</span></a>
					</li>    
					<li class="<?php echo e(Request::is('admin/expense') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.expense.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
					</li>                       
				</ul>
			</li>
			
			<li class="nav-item"><a href="#"><i class="fa fa-file-text-o"></i><span class="menu-title" data-i18n="User">Material Requisition</span></a>
				<ul class="menu-content">    
					<li class="<?php echo e(Request::is('admin/material-requisition') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.material-requisition.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
					</li>                       
				</ul>
			</li>
			
			<?php } ?>
			
			<?php if(Auth::user()->role_id == 29 || Auth::user()->role_id == 21){ ?> 
			<li class="nav-item"><a href="#"><i class="feather icon-help-circle"></i><span class="menu-title" data-i18n="User">Appraisal</span></a>
				<ul class="menu-content">   
					<?php if(Auth::user()->role_id == 29){ ?> 
					<li class="<?php echo e(Request::is('admin/appraisal') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.appraisal.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Appraisal Questions</span></a>
					</li> 
					<?php } ?>
					 
					<li class="<?php echo e(Request::is('admin/appraisal-user-list') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.appraisal-user-list')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Appraisal User List</span></a>
					</li> 
					
				</ul>
			</li>
			<?php } ?>
			<?php $check_support_admin = DB::table("support_user")->where("user_id", Auth::user()->id)->where('role', 'admin')->first(); ?>
			<?php if(Auth::user()->role_id == 29 || !empty($check_support_admin)){ ?>
				<li class="nav-item"><a href="#"><i class="feather icon-user-plus"></i><span class="menu-title" data-i18n="User">Support</span></a>
					<ul class="menu-content">
						<li class="<?php echo e(Request::is('admin/support-dashboard') ? 'active' : ''); ?>">
						<a href="<?php echo e(route('admin.support-dashboard')); ?>"><i class="feather icon-database"></i><span class="menu-title" data-i18n="User">Message to Chairman Dashboard</span></a>
						</li>    
						<li class="<?php echo e(Request::is('admin/support_category') ? 'active' : ''); ?>">
							<a href="<?php echo e(route('admin.support_category.index')); ?>"><i class="feather icon-slack"></i><span class="menu-title" data-i18n="User">Message to Chairman Category</span></a>
						</li>
						<li class="<?php echo e(Request::is('admin/support_user') ? 'active' : ''); ?>">
							<a href="<?php echo e(route('admin.support_user.index')); ?>"><i class="feather icon-headphones"></i><span class="menu-title" data-i18n="User">Message to Chairman User</span></a>
						</li>  					
					</ul>
				</li>
			<?php } ?> 
			<?php $check_enquiry_modules = DB::table("support_user")->where("user_id", Auth::user()->id)->first();
			  $support_role=array(28,29);
			?>
			<?php if(!empty($check_enquiry_modules) || !empty($check_support_admin) || in_array(Auth::user()->role_id,$support_role)){ ?>
			  <li class="nav-item"><a href="<?php echo e(route('admin.support-dashboard')); ?>"><i class="feather icon-info"></i><span class="menu-title" data-i18n="User">Message to Chairman Dashboard <sup class="text-warning">New</sup></span></a></li>
			  <li class="nav-item"><a href="<?php echo e(route('admin.support-enquiry')); ?>"><i class="feather icon-activity"></i><span class="menu-title" data-i18n="User">Message to Chairman Enquiry <sup class="text-warning">New</sup></span></a></li>

			  <li class="nav-item d-none"><a href="<?php echo e(route('admin.enquiry.index')); ?>"><i class="feather icon-help-circle"></i><span class="menu-title" data-i18n="User">Enquiry</span></a></li>
			   
			<?php } ?>

			<?php $chopal_agent=DB::table("batch")->where("chopal_agent_id", Auth::user()->id)->first();
			 if(!empty($chopal_agent)  || Auth::user()->id==6123 || Auth::user()->id==1172 || Auth::user()->role_id==29){
			?>
			 <li class="nav-item"><a href="<?php echo e(route('admin.support-discussion')); ?>"><i class="feather icon-help-circle"></i><span class="menu-title" data-i18n="User">Chopal Discussion</span></a></li>
			<?php } ?>
			
			<?php 
				$courseArr = array(5453,5441,1089,1207,1237,1069,1556,1868,5785,1246,1096,1215,1926,5525,1926,1078,5006,1840,1025,1546,7302,1292,5140,6707,7860,1027,1522,1540,5753,8462,8708,1661,1665); 
				if(in_array(Auth::user()->id, $courseArr)){
			?>
			<li class="nav-item"><a href="<?php echo e(route('admin.onlinecourses.index')); ?>"><i class="feather icon-help-circle"></i><span class="menu-title" data-i18n="User">Online Courses</span></a></li>
			<?php } ?>
			
			<?php if(in_array(Auth::user()->email,array('admin@gmail.com'))){ ?>
			<li class="nav-item">
				<a href="#"><i class="feather icon-user-plus"></i><span class="menu-title" data-i18n="User">Role & Permission</span></a>
				<ul class="menu-content">
					<li class="">
					<a href="<?php echo e(route('admin.permission-add')); ?>"><i class="feather icon-database"></i><span class="menu-title" data-i18n="User">Add Role</span></a>
					</li>    
					<li class="">
						<a href="<?php echo e(route('admin.permission-list')); ?>"><i class="feather icon-slack"></i><span class="menu-title" data-i18n="User">View Role</span></a>
					</li>
				</ul>
			</li>
			<?php } ?>
			
			<?php if(Auth::user()->role_id == 24 || Auth::user()->role_id == 29){ ?>
			<li class="nav-item"><a href="<?php echo e(route('admin.course_category.index')); ?>"><i class="feather icon-help-circle"></i><span class="menu-title" data-i18n="User">Course Category</span></a></li>
			<?php } ?>
			
			<?php if(Auth::user()->role_id == 24 || Auth::user()->role_id == 29 || Auth::user()->user_details->degination == 'DPP & TEST PAPER INCHARGE'){ ?>
			<li class="nav-item"><a href="<?php echo e(route('admin.batch-test-report')); ?>"><i class="feather icon-help-circle"></i><span class="menu-title" data-i18n="User">Batch Test Report</span></a></li>
			<?php } ?>
			
			<?php if(Auth::user()->role_id == 29){ ?>
			<li class="nav-item"><a href="<?php echo e(route('admin.faculty-leave')); ?>"><i class="feather icon-help-circle"></i><span class="menu-title" data-i18n="User">Faculty Leave</span></a></li>
					</li>
			<?php } ?>
			
			
			<?php if(in_array(Auth::user()->email,array('admin@gmail.com'))){ ?>
			
			<li class="nav-item">
				<a href="#"><i class="feather icon-user-plus"></i><span class="menu-title" data-i18n="User">Feedback</span></a>
				<ul class="menu-content">
					<li class="">
					<a href="<?php echo e(route('admin.feedback-form')); ?>"><i class="feather icon-database"></i><span class="menu-title" data-i18n="User">Feedback Form</span></a>
					</li>    
					<li class="">
						<a href="<?php echo e(route('admin.feedback-question')); ?>"><i class="feather icon-slack"></i><span class="menu-title" data-i18n="User">Feedback Question</span></a>
					</li>
				</ul>
			</li>
			
			
			<li class="nav-item"><a href="<?php echo e(route('admin.employee-complaint-view')); ?>"><i class="feather icon-help-circle"></i><span class="menu-title" data-i18n="User">Employee Complaint</span></a></li>
			<?php } ?>
			
			<?php if(Auth::user()->id==901 || Auth::user()->id==6059 || Auth::user()->id==1540 || Auth::user()->id==1761){ ?>
			<li class="nav-item">
				<a href="#"><i class="feather icon-user-plus"></i><span class="menu-title" data-i18n="User">SMS Send</span></a>
				<ul class="menu-content">
					<li class="">
					<a href="<?php echo e(route('admin.sendsms_textlocal')); ?>"><i class="feather icon-database"></i><span class="menu-title" data-i18n="User">Send Template SMS</span></a>
					</li>    
					<li class="">
						<a href="<?php echo e(route('admin.sendsms_templates')); ?>"><i class="feather icon-slack"></i><span class="menu-title" data-i18n="User">All Templates</span></a>
					</li>
				</ul>
			</li>
			<?php } ?>
			<?php 
			if(Auth::user()->role_id != 2){ ?>
			<li class="nav-item"><a href="<?php echo e(route('admin.crm-desk.search')); ?>"><i class="feather icon-help-circle"></i><span class="menu-title" data-i18n="User">CRM-DESK</span></a></li>
			<!--
			<li class="nav-item"><a href="<?php echo e(route('admin.student-attendance')); ?>"><i class="feather icon-help-circle"></i><span class="menu-title" data-i18n="User">Student Attendance</span></a></li>-->
			<?php } ?>
			<?php
				if(Auth::user()->role_id == 28 || Auth::user()->role_id == 27 || Auth::user()->role_id == 29 || Auth::user()->user_details->degination == "BRANCH HEAD" || Auth::user()->user_details->degination == "CENTER HEAD" || Auth::user()->user_details->degination == "HEAD- OPERATIONS" || Auth::user()->user_details->degination == "MANAGER- SALES & MARKETING" || Auth::user()->register_id == 4007 || Auth::user()->register_id == 2608 || Auth::user()->register_id == 2991 || Auth::user()->register_id == 2013 || Auth::user()->register_id == 4510 || Auth::user()->register_id == 1215 || Auth::user()->register_id == 3661 || Auth::user()->register_id == 1648 || Auth::user()->register_id == 1522  || Auth::user()->user_details->degination == "SR. EXECUTIVE-NOTES DISTRIBUTION" || Auth::user()->id == 6165 || Auth::user()->id == 8006 || Auth::user()->id == 7087 || Auth::user()->id == 6747 || Auth::user()->id == 5753 || Auth::user()->id == 7860 || Auth::user()->id == 8128 || Auth::user()->id == 8462){ ?>
			<li class="nav-item">
				<a href="#">
					<i class="feather icon-database"></i><span class="menu-title" data-i18n="User">Stu. Attendance/Inventory</span>  
				</a>			
				<ul class="menu-content">			
					<?php
					if(Auth::user()->user_details->degination != "MANAGER- SALES & MARKETING"){
					?>
					<li><a href="<?php echo e(route('admin.batchinventory.add')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add</span></a></li>
					<?php } ?>
					<li><a href="<?php echo e(route('admin.batchinventory.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">View</span></a></li>
					<li><a href="<?php echo e(route('admin.attendance-dashboard')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Attendance Dashboard</span></a></li>
					<li><a href="<?php echo e(route('admin.inventory-dashboard')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Inventory Dashboard</span></a></li> 
					<li><a href="<?php echo e(route('admin.student-attendence-record')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Stu. Attendance Record</span></a></li> 
					
					<li><a href="<?php echo e(route('admin.student-get-inventory')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Stu. Get Inventory</span></a></li> 
					<li><a href="<?php echo e(route('admin.anuprati-dashboard')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Anuprati-dashboard</span></a></li> 
					<li><a href="<?php echo e(route('admin.student-invalid-punch')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Stu. Invalid Punch</span></a></li> 
					<li><a href="<?php echo e(route('admin.student-inventory-track')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Stu. Inventory Track</span></a></li>  
					<?php if(in_array(Auth::user()->id,[901,7087])) {?>
						<li class="<?php echo e(Request::is('admin/std-attendence-anupriti') ? 'active' : ''); ?>">
							<a href="<?php echo e(route('admin.std-attendence-anupriti')); ?>">
							 <i class="feather icon-circle"></i>
							 <span class="menu-item" data-i18n="View">Anupriti Attendence Record</span>
							</a>
						</li>
					<?php } ?>
					
				</ul>
			</li>
			<?php } ?>
			<?php 
				$authID = Auth::user()->id;
				if($authID=='7281' || $authID=='7666' || $authID=='6840' || $authID=='7302' || $authID=='1215' || $authID=='5006' || $authID=='6806' || Auth::user()->role_id == 29){
			?>
			<li><a href="<?php echo e(route('admin.studnet-attendance-notification')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Student Notification</span></a></li> 
			<?php } ?>
			
			<?php 
				$trUser = array(1292,1557,7087); 
				if(Auth::user()->role_id == 29 || in_array(Auth::user()->id, $trUser)){ ?>
			<li><a href="<?php echo e(route('admin.batch-test-report-new')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Test Report</span></a></li> 	
			<?php } ?>
			
		
			
			<?php 
				$pdrarr = array(901,1172,1813,5997,6437,7015,7030,7256,7910,7914,8382); 
				if(in_array(Auth::user()->id, $pdrarr)){ ?>
			<li><a href="<?php echo e(route('admin.send-faculty-pdf-view')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Send Faculty PDF</span></a></li> 
			<?php } ?>
		<?php } ?>
		
		<?php if(Auth::user()->id==901 || Auth::user()->id==927){ ?>
		<li class="nav-item"><a href="<?php echo e(route('admin.faculty-leave')); ?>"><i class="feather icon-help-circle"></i><span class="menu-title" data-i18n="User">Faculty Leave</span></a></li>
		<?php } ?>
		
		<li class="nav-item"><a href="<?php echo e(route('admin.it-deo-work-report')); ?>"><i class="feather icon-help-circle"></i><span class="menu-title" data-i18n="User">IT DEO Report</span></a></li>
		<?php } ?>
		
		<?php 
			$itdeouser = array(1123,1683,1685,1703,5097,5030,5358,5390,6933); 
			if(in_array(Auth::user()->id, $itdeouser)){ 
		?>
		<li class="<?php echo e(Request::is('admin/studio-reports') ? 'active' : ''); ?>"><a href="<?php echo e(route('admin.studio-reports')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Studio Reports</span></a>
					</li>
		<?php } ?>

		<?php if(Auth::user()->id==901 || Auth::user()->id==8473 || in_array(Auth::user()->register_id,[1537,1069,5039,4800,3935,904,5013,5557,2012,1027,4842,2745,4450,3213,3084,1698,1134,2777,5130]) || Auth::user()->user_details->degination=='CATEGORY HEAD' || Auth::user()->department_type==50){ ?>
			
			<?php if(in_array(Auth::user()->register_id,[4842,2745,4450,3213,3084,1698,1134,2777,5130])){ ?>
			<li class="nav-item"><a href="#"><i class="feather icon-wind"></i><span class="menu-title" data-i18n="User">Batch Holiday</span></a>
				<ul class="menu-content">
				  <li class=""><a href="<?php echo e(route('admin.batch_holiday.create')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add</span></a>
				  </li>
				  <li class=""><a href="<?php echo e(route('admin.batch_holiday.index')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
				  </li>                       
			  </ul>
			</li>
			<?php } ?>

			<li class="nav-item"><a href="#"><i class="feather icon-wind"></i><span class="menu-title" data-i18n="User">Course Planner</span></a>
				<ul class="menu-content">
				  <li class=""><a href="<?php echo e(route('admin.course-planner.batchReports')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Batch Reports</span></a>
				  </li>
				  <li class=""><a href="<?php echo e(route('admin.course-planner.issue-raise-reports')); ?>"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Faculty Issue Reports</span></a>
				  </li>                       
			  </ul>
			</li>
		<?php } ?>
		
		<?php if(Auth::user()->id==901 || Auth::user()->id==7509  || Auth::user()->department_type==45){ ?>
		<li class="nav-item">
			<a href="<?php echo e(route('admin.payment.index')); ?>"><i class="fa fa-inr"></i><span class="menu-title" data-i18n="User">Payment Link</span></a> 
		</li>
		<?php } ?>
		
		<?php if(Auth::user()->id==901 || Auth::user()->id==7651){ ?>
		<li class="nav-item">
			<a href="<?php echo e(route('admin.pincode.index')); ?>"><i class="feather icon-wind"></i><span class="menu-title" data-i18n="User">Pincode</span></a> 
		</li>
		<?php } ?>
		
		<?php if(Auth::user()->id==901){ ?>
		<li class="nav-item">
			<a href="<?php echo e(route('admin.freelancer.index')); ?>"><i class="feather icon-wind"></i><span class="menu-title" data-i18n="User">Freelancer</span></a>
		</li>	
		<?php 
		} ?>
		<?php if(Auth::user()->id==901 || Auth::user()->department_type==50 || Auth::user()->id == 7087 || Auth::user()->id == 8088 || Auth::user()->id == 1665 || Auth::user()->id == 8802 || Auth::user()->id == 1246 || Auth::user()->id == 7473 || Auth::user()->id == 5373 || Auth::user()->id == 5391 || Auth::user()->id == 7509 || Auth::user()->id == 7313){ ?>
		<li class="nav-item">
			<a href="#"><i class="fa fa-check"></i>
			 <span class="menu-title" data-i18n="User">ERP Coupons</span>
			</a>
			<ul class="menu-content">
			<?php
			if(Auth::user()->id==901 || Auth::user()->department_type==50 || Auth::user()->id == 8802 || Auth::user()->id == 1246 || Auth::user()->id == 7473 || Auth::user()->id == 5373 || Auth::user()->id == 5391 || Auth::user()->id == 7509 || Auth::user()->id == 7313){
			?>
				<li class="nav-item">
					<a href="<?php echo e(route('admin.newcoupon.index')); ?>"><i class="fa fa-check"></i><span class="menu-title" data-i18n="User">Request Coupon</span></a>
				</li>
				<li class="nav-item">
					<a href="<?php echo e(route('admin.newcoupon.historyList')); ?>"><i class="fa fa-list"></i><span class="menu-title" data-i18n="User">All Requests List</span></a>
				</li>
					<?php
			}
			
			if(Auth::user()->id==901 || Auth::user()->id == 7087 || Auth::user()->id == 8088 || Auth::user()->id == 1665){
				?>
				<li class="nav-item">
					<a href="<?php echo e(route('admin.newcoupon.historyList')); ?>"><i class="fa fa-list"></i><span class="menu-title" data-i18n="User">Approval Request</span></a>
				</li>
			<?php } ?>
			</ul>
		</li>
		<?php 
		} ?>
		
		
		<?php if(Auth::user()->department_type==4 || Auth::user()->department_type==5 || in_array(Auth::user()->id,[901,6321,1078])){ ?>
		<li class="nav-item">
			<a href="<?php echo e(route('admin.faculty-sme.index')); ?>"><i class="fa fa-university"></i><span class="menu-title" data-i18n="User">Faculty Requests</span></a>
		</li>
		<?php  } ?>
		
		<?php if(Auth::user()->role_id==3 || Auth::user()->department_type==2 || in_array(Auth::user()->id,[901,6321,1078])){ ?>
		<li class="nav-item">
			<a href="<?php echo e(route('admin.faculty-sme.faculty-sme-assistant')); ?>"><i class="fa fa-university"></i><span class="menu-title" data-i18n="User">Panel PDF</span></a>
		</li>
		<?php  } ?>
		
		
		<?php if(in_array(Auth::user()->id,[901,6321,8708,1078])){ ?>
		<li class="nav-item">
			<a href="<?php echo e(route('admin.faculty-sme.sme-visibility')); ?>"><i class="fa fa-university"></i><span class="menu-title" data-i18n="User">SME Visibility</span></a>
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
		
		<?php if(Auth::user()->id==8377 || Auth::user()->id == 901 ||  Auth::user()->department_type==45){ ?>
		<li class="nav-item">
			<a href="<?php echo e(route('coupon.index')); ?>"><i class="fa fa-university"></i><span class="menu-title" data-i18n="User">Assign Coupon <sup class="text-danger">New</sup></span></a>
		</li>
		<?php } ?>

		<?php if(Auth::user()->id==8377 || Auth::user()->id==901 || Auth::user()->user_details->degination == "CENTER HEAD"){ ?>
		<li class="nav-item">
			<a href="<?php echo e(route('view-cleanliness-report')); ?>"><i class="fa fa-bath"></i><span class="menu-title" data-i18n="User">Cleanliness Report</span></a>
		</li>
		<?php } ?>

		<?php if(Auth::user()->id == 8377 || Auth::user()->id==901){ ?>
		<li class="nav-item">
			<a href="<?php echo e(route('software-management')); ?>" title="Software Management"><i class="fa fa-desktop"></i><span class="menu-title" data-i18n="User">Software Management</span></a>
		</li>
		<?php } ?>
		
		
		<li class="nav-item">
			<a href="<?php echo e(route('request-access')); ?>" title="Request Access">
				<i class="fa fa-desktop"></i>
				<span class="menu-title" data-i18n="User"> Request Access
					<?php if(!empty($hasRequestAccessUpdates)): ?>
						<sup class="text-danger">●</sup>
					<?php endif; ?>
				</span>
			</a>
		</li>
		
		<?php if(Auth::user()->register_id==1895 || Auth::user()->register_id==5439){ ?>
		<li class="nav-item">
			<a href="#"><i class="fa fa-check"></i>
			 <span class="menu-title" data-i18n="User">Academic Report <sup class="text-danger">●</sup></span>
			</a>
			<ul class="menu-content">
				<li>
					<a href="<?php echo e(route('admin.faculty-utilization-dashboard')); ?>" title="Academic Access">
						<i class="fa fa-desktop"></i>
						<span class="menu-title" data-i18n="User"> 
							Dashboard
						</span>
					</a>
				</li>
				<li>
					<a href="<?php echo e(route('admin.subject-utilization-dashboard')); ?>" title="Academic Access">
						<i class="fa fa-desktop"></i>
						<span class="menu-title" data-i18n="User"> 
							Subject Dashboard
						</span>
					</a>
				</li>
				<li>
					<a href="<?php echo e(route('admin.academic-faculty-report')); ?>" title="Academic Access">
						<i class="fa fa-desktop"></i>
						<span class="menu-title" data-i18n="User"> 
							Faculty Overview
						</span>
					</a>
				</li>
				<li class="nav-item">
					<a href="<?php echo e(route('admin.prediction-report')); ?>" title="Prediction Report">
						<i class="fa fa-desktop"></i>
						<span class="menu-title" data-i18n="User"> 							
							Prediction Report			
						</span>
					</a>
				</li>
				<li class="nav-item">
					<a href="<?php echo e(route('admin.faculty-plan')); ?>" title="Faculty Plan">
						<i class="fa fa-desktop"></i>
						<span class="menu-title" data-i18n="User"> 							
							Faculty Plan		
						</span>
					</a>
				</li>
				<li class="nav-item">
					<a href="<?php echo e(route('admin.proposed-plan')); ?>" title="Faculty Plan">
						<i class="fa fa-desktop"></i>
						<span class="menu-title" data-i18n="User"> 							
							Proposed plan	
						</span>
					</a>
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
				<?php if($user->department_type==50){?>
				<li class="nav-item">
					<a href="<?php echo e(route('admin.multi-course-planner.multi-planner-request')); ?>">
						<i class="fa fa-book"></i>
						<span class="menu-title" data-i18n="User"> Planner Request</span>
					</a>
				</li>
				<?php } ?>
				<li class="nav-item">
					<a href="<?php echo e(route('admin.multi-course-planner.planner-request-view')); ?>">
						<i class="fa fa-book"></i>
						<span class="menu-title" data-i18n="User"> Planner Request View</span>
					</a>
				</li>
			</ul>
		</li>
			<?php } 
			}?>
	</ul>
</div>
</div><?php /**PATH /var/www/html/laravel/resources/views/layouts/admin/sidebar.blade.php ENDPATH**/ ?>