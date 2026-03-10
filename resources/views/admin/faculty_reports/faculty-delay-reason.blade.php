@extends('layouts.without_login_admin')
@section('content')

	
<div class="app-content content" style="margin: 0px !important;">
	<div class="content-wrapper" style="margin-top: 0px !important;">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Faculty Delay Reason</h2>
						 
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ route('faculty-reports.delay-reason',$timetable->id) }}" method="get" name="filtersubmit">
									<div class="row">
										
										<input type="hidden" name="faculty_id" class="faculty_id_get" name="" value="{{ $timetable->faculty_id }}">
										
										
										<div class="col-12 col-sm-6 col-lg-4">											
											<label for="users-list-status">Reason</label>								
											<fieldset class="form-group">																					
												<textarea  name="delay_faculty_reason" placeholder="Reason" class="form-control">{{ $timetable->delay_faculty_reason }}</textarea>
											</fieldset>	
										</div>	
										
										<div class="col-12 col-sm-6 col-lg-8"  >
												<label for="users-list-status">&nbsp;</label>		
										 		<fieldset class="form-group" style="">		
												<button type="submit" class="btn btn-primary">Submit</button>
											</fieldset>					
										</div>										
											
									</div>
									
									
								</form>
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
