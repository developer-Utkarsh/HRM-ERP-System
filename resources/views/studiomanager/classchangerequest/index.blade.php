@extends('layouts.studiomanager')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Class Change Request</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('studiomanager.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">View</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
			{{-- <div class="content-header-left col-md-3 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<a href="{{ route('studiomanager.chapters.create') }}" class="btn btn-primary">
							Add Chapter
						</a>
					</div>
				</div>
			</div> --}}
		</div>
		<div class="content-body">			
			<section id="data-list-view" class="data-list-view-header">				
				<div class="card-content">
					<div class="card-body">						
						<ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" id="home-tab-fill" data-toggle="tab" href="#reschedule" role="tab" aria-controls="home-fill" aria-selected="true">Reschedule Request</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="profile-tab-fill" data-toggle="tab" href="#swap" role="tab" aria-controls="profile-fill" aria-selected="false">Swap Request</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="messages-tab-fill" data-toggle="tab" href="#delete" role="tab" aria-controls="messages-fill" aria-selected="false">Delete Request</a>
							</li>
						</ul>

						<!-- Tab panes -->
						<div class="tab-content pt-1">
							<div class="tab-pane active" id="reschedule" role="tabpanel" aria-labelledby="home-tab-fill">
								<div class="table-responsive">
									<table class="table data-list-view">
										<thead>
											<tr>
												{{-- <th>S. No.</th> --}}
												<th>To Time</th>
												<th>Faculty Reason</th>
												<th>Admin Reason</th>
												<th>Status</th>
												<th>Created</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											@if(count($studios) > 0)
											@foreach($studios as $key => $studio)
											@if(!empty($studio->timetable))
											@foreach($studio->timetable as $count => $time_table)
											@if(!empty($time_table->reschedule))
											@foreach($time_table->reschedule as $single_reschedule)
											@if(!empty($single_reschedule))
											<tr>
												{{-- <td>{{ $count + 1 }}</td> --}}
												<td>{{ isset($single_reschedule->to_time) ? $single_reschedule->to_time : '' }}</td>
												<td>{{ isset($single_reschedule->faculty_reason) ? $single_reschedule->faculty_reason : '' }}</td>
												<td>{{ isset($single_reschedule->admin_reason) ? $single_reschedule->admin_reason : '' }}</td>
												<td>{{ isset($single_reschedule->status) ? $single_reschedule->status : '' }}</td>
												<td>
													@if(!empty($single_reschedule->created_at))
													{{ $single_reschedule->created_at->format('d-m-Y') }}
													@endif
												</td>
												<td>
													<a href="{{ route('studiomanager.reschedule.edit', $single_reschedule->id) }}">
														<span class="action-edit"><i class="feather icon-edit"></i></span>
													</a>
												</td>
											</tr>
											@endif
											@endforeach
											@endif
											@endforeach
											@endif
											@endforeach
											@endif
										</tbody>
									</table>
								</div>
							</div>
							<div class="tab-pane" id="swap" role="tabpanel" aria-labelledby="profile-tab-fill">
								<div class="table-responsive">
									<table class="table data-list-view">
										<thead>
											<tr>
												{{-- <th>S. No.</th> --}}
												<th>Time Table</th>
												<th>Swap Faculty</th>
												<th>Swap Time Table</th>
												<th>Status</th>
												<th>Created</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											@if(count($studios) > 0)
											@foreach($studios as $key => $studio)
											@if(!empty($studio->timetable))
											@foreach($studio->timetable as $count => $time_table)
											@if(!empty($time_table->swap))
											@foreach($time_table->swap as $single_swap)
											@if(!empty($single_swap))
											<tr>
												{{-- <td>{{ $count + 1 }}</td> --}}
												<td>
													@if(!empty($single_swap->s_timetable))
													{{ $single_swap->s_timetable->from_time }} - {{ $single_swap->s_timetable->to_time }}
													{{-- {{ $time_table->swap->s_timetable->cdate }} --}}
													@endif
												</td>
												<td>
													@if(!empty($single_swap->faculty))
													{{ $single_swap->faculty->name }}
													@endif
												</td>
												<td>
													@if(!empty($single_swap->swap_timetable))
													{{ $single_swap->swap_timetable->from_time }} - {{ $single_swap->swap_timetable->to_time }}
													{{-- {{ $time_table->swap->swap_timetable->cdate }} --}}
													@endif
												</td>
												<td>{{ isset($single_swap->status) ? $single_swap->status : '' }}</td>
												<td>
													@if(!empty($single_swap->created_at))
													{{ $single_swap->created_at->format('d-m-Y') }}
													@endif
												</td>
												<td>
													<a href="{{ route('studiomanager.swap.edit', $single_swap->id) }}">
														<span class="action-edit"><i class="feather icon-edit"></i></span>
													</a>
												</td>
											</tr>
											@endif
											@endforeach
											@endif
											@endforeach
											@endif
											@endforeach
											@endif
										</tbody>
									</table>
								</div>
							</div>
							<div class="tab-pane" id="delete" role="tabpanel" aria-labelledby="messages-tab-fill">
								<div class="table-responsive">
									<table class="table data-list-view">
										<thead>
											<tr>
												{{-- <th>S. No.</th> --}}
												<th>Days</th>
												<th>Faculty Reason</th>
												<th>Admin Reason</th>
												<th>Status</th>
												<th>Created</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											@if(count($studios) > 0)
											@foreach($studios as $key => $studio)
											@if(!empty($studio->timetable))
											@foreach($studio->timetable as $count => $time_table)
											@if(!empty($time_table->cancelclass))
											@foreach($time_table->cancelclass as $single_cancelclass)
											@if(!empty($single_cancelclass))
											<tr>
												{{-- <td>{{ $count + 1 }}</td> --}}
												<td>{{ isset($single_cancelclass->days) ? $single_cancelclass->days : '' }}</td>
												<td>{{ isset($single_cancelclass->faculty_reason) ? $single_cancelclass->faculty_reason : '' }}</td>
												<td>{{ isset($single_cancelclass->admin_reason) ? $single_cancelclass->admin_reason : '' }}</td>
												<td>{{ isset($single_cancelclass->status) ? $single_cancelclass->status : '' }}</td>
												<td>@if(!empty($single_cancelclass->created_at))
													{{ $single_cancelclass->created_at->format('d-m-Y') }}
													@endif
												</td>
												<td>
													<a href="{{ route('studiomanager.cancelclass.edit', $single_cancelclass->id) }}">
														<span class="action-edit"><i class="feather icon-edit"></i></span>
													</a>
												</td>
											</tr>
											@endif
											@endforeach
											@endif
											@endforeach
											@endif
											@endforeach
											@endif
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>                   
			</section>
		</div>
	</div>
</div>
@endsection

@section('scripts')
@endsection
