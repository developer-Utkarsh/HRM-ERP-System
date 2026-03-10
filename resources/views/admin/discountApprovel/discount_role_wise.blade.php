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
						<h2 class="content-header-title float-left mb-0">Discount Role Wise</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">Discount Role Wise View</li>
							</ol>
						</div>
					</div>
					<div class="col-2">
						
						<a class="btn btn-primary" style ="float:right;" href="<?php echo url('admin/discount-category-role-add') ?>">Add Discount</a>
					</div>
					
				</div>
			</div>
		</div>
		<div class="content-body">
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
			
				<div class="table-responsive">
					<table class="table data-list-view" style="background:#fff;">
						<thead style="text-align: ;">
							<tr>
								<th>S. No.</th>
								<th>Role</th>	
								<th>Category</th>
								<th>Online</th>	
								<th>Offline</th>
												
							</tr>
						</thead>
						<tbody >
						<?php 	if(count($discount_list) > 0){
									$i = 1;
									?>
                                    @foreach($discount_list as $records)
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td>{{ $records->role_name}}</td> 
										<td>{{ $records->category}}</td>
										<td>{{ $records->online }}</td>
                                        <td>{{ $records->offline }}</td>
                                    </tr>
                                    <?php $i++; ?>
                                    @endforeach
									 <?php } else { ?>
										<tr class="raw_data">
											<td colspan="6" style="text-align: center;">No record found.</td>
										</tr>
									<?php } ?>
						</tbody>
					</table>
				</div>                   
			</section>
		</div>
	</div>
</div>
@endsection
