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
						<h2 class="content-header-title float-left mb-0">Edit Studio</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('studiomanager.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Edit Studio</a>
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
									<form class="form" action="{{ route('studiomanager.studios.update', $studio->id) }}" method="post" enctype="multipart/form-data">
										@method('PATCH')
										@csrf
										<div class="form-body">
											<div class="row">
												<div class="col-md-6 col-12">
													<div class="form-label-group">
														<input type="text" class="form-control" placeholder="Studio Name" name="name" value="{{ old('name', $studio->name) }}">
														<label for="first-name-column">Studio Name</label>
														@if($errors->has('name'))
														<span class="text-danger">{{ $errors->first('name') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-label-group">
														<?php $studio_assistants = \App\User::where('role_id', '3')->get(); ?>
														<select class="form-control" name="assistant_id">
															<option value=""> - Select Studio Assistant - </option>
															@if(count($studio_assistants) > 0)
															@foreach($studio_assistants as $value)
															<option value="{{ $value->id }}" @if($studio->assistant_id == $value->id) selected="selected" @endif>{{ $value->name }}</option>
															@endforeach
															@endif
														</select>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-label-group">
														<select class="form-control" name="studio_slot">
															<option value=""> - Select Any - </option>
															<option value="Studio Slot" @if($studio->studio_slot == 'Studio Slot') selected="selected" @endif>Studio Slot</option>
														</select>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-label-group">
														<select class="form-control" name="branch">
															<option value=""> - Select Branch - </option>
															<option value="Jaipur" @if($studio->branch == 'Jaipur') selected="selected" @endif>Jaipur</option>
														</select>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group d-flex align-items-center">
														<label class="mr-2">Status :</label>
														<label>
															<input type="radio" name="status" value="1" {{ ($studio->status == 1) ? "checked" : ""}}>
															Active
														</label>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="status" value="0" {{ ($studio->status == 0) ? "checked" : ""}}>
															Inactive
														</label>
													</div>
												</div>                                       
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
