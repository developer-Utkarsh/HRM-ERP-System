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
						<h2 class="content-header-title float-left mb-0">Feedback Question</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
						<a href="{{ route('admin.feedback-question-add') }}" class="btn btn-primary float-right">Add Question</a>
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
								<th>Question</th>
								<th>Question Type</th>
								<th>Option</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>		
							<?php 
								$i = 1;
								foreach($question as $q){
							?>
							<tr>
								<td><?=$i;?></td>
								<td><?=$q->question;?></td>
								<td><?=$q->qtype;?></td>
								<td><?=$q->options;?></td>
								<td><?=$q->status;?></td>
								<td>
									<a href="{{ route('admin.feedback-question-edit', $q->qid) }}">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
									</a>
									<a href="{{ route('admin.feedback-question-delete', $q->qid) }}" onclick="return confirm('Are You Sure To Delete Question')">
										<span class="action-delete"><i class="feather icon-trash"></i></span>
									</a>
								</td>
							</tr>
							<?php $i++; } ?>
						</tbody>
					</table>
				</div>
				                  
			</section>
		</div>
	</div>
</div>
@endsection
