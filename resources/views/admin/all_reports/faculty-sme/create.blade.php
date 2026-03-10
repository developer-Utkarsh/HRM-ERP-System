@extends('layouts.without_login_admin')
@section('content')
<link href="{{ asset('laravel/public/css/sme_styles.css') }}" rel="stylesheet">
	<div class="question-main-file batch-info-page">
		<header class="header-section">
			<div class="back-arow"> 
				<span>
					<a href="{{ route('all-reports')}}?user_id={{$user_id}}" style="border:none;">
					  <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22.16 22.16">
						<path id="Icon_ionic-ios-arrow-dropleft-circle" data-name="Icon ionic-ios-arrow-dropleft-circle" d="M-56.77,9.19A11.08,11.08,0,0,0-67.85,20.26,11.08,11.08,0,0,0-56.77,31.34,11.08,11.08,0,0,0-45.69,20.26h0A11.08,11.08,0,0,0-56.77,9.19Zm2.31,15.4a1,1,0,0,1,0,1.45,1,1,0,0,1-.72.3,1,1,0,0,1-.73-.3l-5-5a1,1,0,0,1,0-1.42l5.08-5.1a1,1,0,0,1,1.45,0,1,1,0,0,1,0,1.46l-4.36,4.31Z" transform="translate(67.85 -9.19)"></path>
					  </svg>
					  <b>Request Form</b>
					</a>
				</span>
				<a href="{{ route('faculty-sme-request-index')}}?user_id={{$user_id}}" class="view-all-btn">View All Request</a>
			</div>
		</header>
		<div class="mid-content p-2">
			<form action="{{ route('faculty-sme-request-submit')}}" method="post">
				@csrf
				<input type="hidden" name="user_id" value="{{ $user_id }}"/>
				<div class="time-table-form">
					<div class="form-group">
						<label><b>Enter you requirement here.</b></label>
						<textarea name="req_msg" placeholder="Message here" required></textarea>
					</div>
					<div class="form-group">
						<label><b>By when do you need this?</b></label>
						<input type="datetime-local" name="req_date" class="form-control"  min="<?= date('Y-m-d\TH:i') ?>">
					</div>
					<button type="submit" class="primary-btn">Submit</button>
				</div>
				<div class="instruction-sec d-none">
					<b>Instruction</b>
					<ul>
						<li>Lorem Ipsum is simply dummy text of the printing .</li>
						<li>Lorem Ipsum is simply dummy text of the printing .</li>
						<li>Lorem Ipsum is simply dummy text of the printing .</li>
						<li>Lorem Ipsum is simply dummy text of the printing .</li>
					</ul>
				</div>
			</form>
		</div>
	   
	</div>
	
	<style>
		.time-table-form b{
			font-size: 16px
		}
		
		input[type="date"],
input[type="datetime-local"] {
    width: 100% !important;
    max-width: 100%;
    min-width: 100%;
    box-sizing: border-box;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    padding: 10px;
    font-size: 16px;
    background-color: #fff;
}
	</style>

@endsection

@section('scripts')

@endsection
