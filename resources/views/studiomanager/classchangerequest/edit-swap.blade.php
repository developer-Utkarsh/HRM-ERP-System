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
						<h2 class="content-header-title float-left mb-0">Edit Swap</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('studiomanager.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Edit Swap</a>
								</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
			<div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
			</div>
		</div>
		<div class="content-body">
			<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="{{ route('studiomanager.swap.update', $swap->id) }}" method="post" enctype="multipart/form-data">
										@method('PATCH')
										@csrf
										<div class="form-body">
											<div class="row">
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Status</label>
														<select class="form-control" name="status">
															<option value=""> - Select Any - </option>
															<option value="Approved" @if($swap->status == 'Approved') selected="selected" @endif>Approved</option>
															<option value="Reject" @if($swap->status == 'Reject') selected="selected" @endif>Reject</option>
															<option value="pending" @if($swap->status == 'pending') selected="selected" @endif>Pending</option>
														</select>
													</div>
												</div>
												<?php
												$get_timetable = \App\Timetable::where('id',$swap->timetable_id)->first();
												$get_swap_timetable = \App\Timetable::where('id',$swap->swap_timetable_id)->first();
												?>

												<input type="hidden" name="c_from_time" value="{{ $get_timetable->from_time }}">
												<input type="hidden" name="c_to_time" value="{{ $get_timetable->to_time }}">
												<input type="hidden" name="s_from_time" value="{{ $get_swap_timetable->from_time }}">
												<input type="hidden" name="s_to_time" value="{{ $get_swap_timetable->to_time }}">

												<div class="col-12">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Update</button>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
			<!-- // Basic Floating Label Form section end -->
		</div>
	</div>
</div>
@endsection

@section('scripts')
@endsection
