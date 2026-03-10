@extends('layouts.front')
@section('content')
<!-- Breadcrumbs -->
@include('include/page-header')
<div class="main-container container" id="main-container">
	<div class="blog__content mb-72">
		<h1 class="page-title text-center">Contact Us</h1>
		<div class="row justify-content-center">
			<div class="col-lg-6">
				<h4>Company Information</h4>
				<p>Sangri Times</p>
				<ul class="contact-items">
					<li class="contact-item"><address>एस 5, द्वितीय मंजिल, चित्रकूट मार्ग, भूरा पटेल नगर,टैगोर नगर, जयपुर, राजस्थान 302021</address></li>
					<li class="contact-item"><a href="tel:9982210777">Phone : +91-9982210777</a></li>
				</ul>
				<p>Sangri Internet</p>
				<ul class="contact-items">
					<li class="contact-item"><a href="tel:9982210777">Phone : +91-9982210777</a></li>
					<li class="contact-item">Whatsapp : 9828886889</li>
				</ul> 
				<p>Sangri Network</p>
				<ul class="contact-items">
					<li class="contact-item"><a href="mailto:sangritimes@gmail.com">sangritimes@gmail.com</a></li>
				</ul>
			</div>
			<div class="col-lg-6">
				<!-- Contact Form -->
				<form id="contact-form" class="contact-form mt-30 mb-30" method="post" action="#">
					<div class="contact-name">
						<label for="name">Name <abbr title="required" class="required">*</abbr></label>
						<input name="name" id="name" type="text">
					</div>
					<div class="contact-email">
						<label for="email">Email <abbr title="required" class="required">*</abbr></label>
						<input name="email" id="email" type="email">
					</div>
					<div class="contact-subject">
						<label for="email">Subject</label>
						<input name="subject" id="subject" type="text">
					</div>
					<div class="contact-message">
						<label for="message">Message <abbr title="required" class="required">*</abbr></label>
						<textarea id="message" name="message" rows="7" required="required"></textarea>
					</div>
					<input type="submit" class="btn btn-lg btn-color btn-button" value="Send Message" id="submit-message">
					<div id="msg" class="message"></div>
				</form>
			</div>
		</div>
	</div> 
</div>
@endsection