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
						<h2 class="content-header-title float-left mb-0">{{ $heading }} Form</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">{{ $heading }} Form
								</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
						<a href="{{ route('admin.feedback-form') }}" class="btn btn-primary mr-1">Back</a>
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
									<form class="form" action="{{$url}}" method="post" enctype="multipart/form-data">
										@csrf 
										<div>	
											<div class="col-md-12 col-12">
												<div class="form-group">
													<label for="first-name-column">FeedBack Form Name</label> 
													<input type="text" name="form_name" value="<?php if(!empty($form->form_name)){ echo $form->form_name; }?>" class="form-control"/>
												</div>
											</div>
											<div class="col-md-12 col-12">
												<div class="form-group">
													<label for="first-name-column">Form Description</label> 
													<textarea name="form_description" class="form-control remark" required><?php if(!empty($form->form_description)){ echo $form->form_description; }?></textarea>
												</div>
											</div>
											<div class="col-md-12 col-12">
												<div class="form-group">
													<label for="first-name-column">Department</label> 
													<select class="form-control" name="department">
														<option value="">-- Select --</option>
														<?php 
															$department  = DB::table('departments')->where('is_deleted','0')->get();
															foreach($department as $d){
																if(!empty($form->department)){
																	if($d->id == $form->department){
																		$selected = 'selected';
																	}else{
																		$selected = '';
																	}
																}else{
																	$selected = '';
																}
														?>
															<option value="<?=$d->id;?>" <?=$selected;?>><?=$d->name;?></option>
														<?php }?>
													</select>
												</div>
											</div>
											<div class="row mx-0">
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Start Date</label> 
														<input type="date" name="start_time" value="<?php if(!empty($form->start_time)){ echo $form->start_time; }?>" class="form-control"/>
													</div>
												</div>	
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">End Date</label> 
														<input type="date" name="end_time" value="<?php if(!empty($form->end_time)){ echo $form->end_time; }?>" class="form-control"/>
													</div>
												</div>	
											</div>	
											
											<div class="px-1">
												<table border="1">
													<tr>
														<h4>Questions</h4>
													</tr>
													<?php 
														$question  = DB::table('feedbackquestion')->where('is_deleted','0')->get();
														foreach($question as $q){
															if(!empty($form->question_ids)){
																$hiddenProducts = explode(',', $form->question_ids);
																if(in_array($q->qid, $hiddenProducts)){
																	$checked = 'checked';
																}else{
																	$checked = '';
																}
															}else{
																$checked = '';
															}
													?>
													<tr>
														<td class="p-1" style="font-size:13px;"><input type="checkbox" name="question_ids[]" value="<?=$q->qid;?>"<?=$checked;?>/> <?=$q->question?></td>
													</tr>
													<?php } ?>
												</table>
											</div>
											
											<div class="col-12 pt-2 text-right">
												<button type="submit" class="btn btn-primary mr-1 mb-1 btn_submit">Submit</button>
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
<script type="text/javascript">

  function qu_typechange(ntType){
    if(ntType=='Radio'){
      $('#q_options').show();
    }else{
       $('#q_options').hide();
    }
  } 
</script> 
@endsection