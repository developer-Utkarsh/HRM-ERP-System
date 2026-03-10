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
						<h2 class="content-header-title float-left mb-0">Feedback Form</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
						<a href="{{ route('admin.feedback-form-add') }}" class="btn btn-primary float-right">Add Form</a>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">			
			<section id="data-list-view" class="data-list-view-header">	
				<div class="table-responsive">
					<table class="table data-list-view">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Form Name</th>
								<th>Department</th>
								<th>Description</th>
								<th>Start Time</th>
								<th>End Time</th>		
								<th>Action</th>
							</tr>
						</thead>
						<tbody>		
							<?php 
								$i=1; 
								foreach($form as $f){ 
							?>
							<tr>
								<td><?=$pageNumber++;?></td>
								<td><?=$f->form_name;?></td>
								<td><?=$f->department;?></td>
								<td><?=$f->form_description;?></td>
								<td><?=$f->start_time;?></td>
								<td><?=$f->end_time;?></td>
								<td>
									<a href="{{ route('admin.feedback-form-edit', $f->form_id) }}">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
									</a>
									<a href="{{ route('admin.feedback-form-delete', $f->form_id) }}" onclick="return confirm('Are You Sure To Delete Question')">
										<span class="action-delete"><i class="feather icon-trash"></i></span>
									</a>
								</td>
							</tr>
							<?php $i++; } ?>
						</tbody>
					</table>
					<div class="d-flex justify-content-center">					
					{!! $form->appends($params)->links() !!}
					</div>
				</div>
				                  
			</section>
		</div>
	</div>
</div>
@endsection
