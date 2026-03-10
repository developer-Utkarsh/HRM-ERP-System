@extends('layouts.front')
@section('content')
<!-- Breadcrumbs -->
@include('include/page-header')
<div class="main-container container" id="main-container">
	<div class="blog__content mb-72">
		<h1 class="page-title text-center">Our Team</h1>
		<div class="row justify-content-center">
			<div class="col-lg-12">
				<div class="entry__article">
				    
				<div class="editor">
							<div class="row">
									<div class="col-sm-3 editor-info text-center">
										<img src="https://en.sangritimes.com/laravel/public/images/editorial/junjaram-thory.jpg" alt="Junjaram Thory">
										<div class="meta-data">
											<h4 class="member-name">Junjaram Thory </h4>
											<span class="member-role">Founder/Chief Editor </span>
										</div>
									</div>									
									<div class="col-sm-3 editor-info text-center">
										<img src="https://en.sangritimes.com/laravel/public/images/editorial/deepa-choudhary.jpg" alt="editor">
										<div class="meta-data">
											<h4 class="member-name">Deepa Choudhary</h4>
											<span class="member-role">Assistant Editor 
											</span>
										</div>
									</div>
									<div class="col-sm-3 editor-info text-center">
										<img src="https://en.sangritimes.com/laravel/public/images/editorial/kapilraj.jpg" alt="editor">
										<div class="meta-data">
											<h4 class="member-name">Kapil Raj </h4>
											<span class="member-role">News Editor/Content Editor </span>
										</div>
									</div>
									<div class="col-sm-3 editor-info text-center">
										<img src="https://en.sangritimes.com/laravel/public/images/editorial/sunil-kumawat.jpg" alt="editor">
										<div class="meta-data">
											<h4 class="member-name">Sunil Kumawat </h4>
											<span class="member-role">Graphic Designer </span>
										</div>
									</div>									
								</div>
							</div>	
						
							
				</div>
			</div>
		</div>
	</div>
</div>
@endsection