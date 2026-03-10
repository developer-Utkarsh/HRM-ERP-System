@extends('layouts.faculty')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
		</div>
		<div class="content-body">
			<!-- Dashboard Analytics Start -->
			<section id="dashboard-analytics">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12">
						<div class="card bg-analytics text-white">
							<div class="card-content">
								<div class="card-body text-center">                                        
									<div class="avatar avatar-xl bg-primary shadow mt-0">
										<div class="avatar-content">
											<i class="feather icon-award white font-large-1"></i>
										</div>
									</div>
									<div class="text-center">
										<h1 class="mb-2 text-white">Welcome {{ Auth::user()->name }},</h1>
										<p class="m-auto w-75">You have done <strong>57.6%</strong> more sales today. Check your new badge in your profile.</p>
									</div>
								</div>
							</div>
						</div>
					</div>					
				</div>				
			</section>
			<!-- Dashboard Analytics end -->
		</div>
	</div>
</div>
@endsection

@section('scripts')
@endsection
