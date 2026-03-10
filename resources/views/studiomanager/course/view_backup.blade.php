@extends('layouts.studiomanager')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Course</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('studiomanager.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">Details</li>
							</ol>
						</div>
					</div> 
				</div>
			</div>
		</div>
		<div class="content-body">
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				 
				<div class="table-responsive">
					<table class="table data-list-view">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Course Name</th>
								<th>Subject Name</th>
								<th>Duration</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$i =1;
								foreach($totalSubejctHour as  $key => $value2){ 
							?>
							<tr>
								<td><?=$i;?></td>
								<td><?=$value2->name?></td>
								<td><?=$value2->sname?></td>
								<td>
									<?php 
										$minutes=$value2->new_duration;
										$hours = floor($minutes / 60).':'.($minutes -   floor($minutes / 60) * 60);
										echo $hours.' Hrs';
									?>
								</td>
							</tr>
							<?php $i++; } ?>
						</tbody>
					</table>
				</div>                   
			</section>
			
			
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				 
				<div class="table-responsive">
					<table class="table data-list-view">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Course Name</th>
								<th>Subject Name</th>
								<th>Topic Name</th>
								<th>Sub Topic Name</th>
								<th>Duration</th>
								<?php if(Auth::user()->id==5126 || Auth::user()->id==5603){ ?> <th>Action</th><?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php 
								$i = 1; 
								foreach($new_array as  $key => $value){ 
							?>
							<tr>
								<td>{{ $i }}</td>
								<td class="product-category">{{ $value->course_name }}</td>
								<td class="product-category">{{ $value->subject_name }}</td>
								<td class="product-category">{{ $value->chapter_name }}</td>
								<td class="product-category">{{ $value->topic_name }}</td>
								<td class="product-category">{{ $value->duration }}</td>
								<?php if(Auth::user()->id==5126 || Auth::user()->id==5603){ ?>
								<td class="product-category">
									<?php if(!empty($value->chapter_name)){ ?>
									<a href="{{ route('studiomanager.course.chapter-topic-view',$value->topic_id) }}">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
									</a>
									<?php }else{ echo '-';} ?>
								</td>
								<?php } ?>
							</tr>
							<?php $i++; } ?>
							
							<?php /*
							@foreach($send_array as  $key => $value)
							<tr>
								<td>{{ $value['s_no'] }}</td>
								<td class="product-category">{{ $value['course_name'] }}</td>
								<td class="product-category">{{ $value['subject_name'] }}</td>
								<td class="product-category">{{ $value['chapter_name'] }}</td>
								<td class="product-category">{{ $value['topic_name'] }}</td>
								<td class="product-category">{{ $value['duration'] }}</td>
								<?php if(Auth::user()->id==5126 || Auth::user()->id==5603){ ?>
								<td class="product-category">
									<?php if(!empty($value['chapter_name'])){ ?>
									<a href="{{ route('studiomanager.course.chapter-topic-view',$value['topic_id']) }}">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
									</a>
									<?php }else{ echo '-';} ?>
								</td>
								<?php } ?>
							</tr>
							@endforeach	
							*/ ?>
						</tbody>
					</table>
				</div>                   
			</section>
		</div>
	</div>
</div>
@endsection

@section('scripts')
@endsection
