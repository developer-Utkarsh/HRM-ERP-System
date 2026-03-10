@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Edit Branch</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">Edit Branch
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
									<form class="form" action="{{ route('admin.branch.update', $branch->id) }}" method="post" enctype="multipart/form-data">
										<!-- @method('PATCH') -->
										@csrf
										<div class="form-body">
											<div class="row">
												<div class="col-md-6 col-12">
													<div class="form-label-group">
														<input type="text" class="form-control" placeholder="Branch Name" name="name" value="{{ old('name', $branch->name) }}">
														<label for="first-name-column">Branch Name</label>
														@if($errors->has('name'))
														<span class="text-danger">{{ $errors->first('name') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-label-group">
														<input type="text" class="form-control" placeholder="Branch Address" name="address" value="{{ old('address', $branch->address) }}">
														<label for="first-name-column">Branch Address</label>
														@if($errors->has('address'))
														<span class="text-danger">{{ $errors->first('address') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Nickname</label>
														<input type="text" class="form-control" placeholder="Nickname" name="nickname" value="{{ old('nickname', $branch->nickname) }}">
														@if($errors->has('nickname'))
														<span class="text-danger">{{ $errors->first('nickname') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="">Related Branch</label>
														<select class="form-control related" id="related" name="related" required>	
															<option value=""> Select</option>
															<option value="1" @if('1' == old('status', $branch->related)) selected="selected" @endif> JODHPUR</option>
															<option value="2" @if('2' == old('status', $branch->related)) selected="selected" @endif> JAIPUR</option>
															<option value="3" @if('3' == old('status', $branch->related)) selected="selected" @endif> PRAYAGRAJ</option>
															<option value="4" @if('4' == old('status', $branch->related)) selected="selected" @endif> DELHI</option>
															<option value="5" @if('5' == old('status', $branch->related)) selected="selected" @endif> INDORE</option>
															<option value="6" @if('6' == old('status', $branch->related)) selected="selected" @endif> LUCKNOW</option>
															<option value="7" @if('7' == old('status', $branch->related)) selected="selected" @endif> PATNA</option>
														</select>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group d-flex align-items-center">
														<label class="mr-2">Status :</label>
														<label>
															<input type="radio" name="status" value="1" {{ ($branch->status == 1) ? "checked" : ""}}>
															Active
														</label>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="status" value="0" {{ ($branch->status == 0) ? "checked" : ""}}>
															Inactive
														</label>
													</div>
												</div>   
												<div class="col-md-6 col-12">
													<div class="form-group d-flex align-items-center">
														<label class="mr-2">Show In Web :</label>
														<label>
															<input type="radio" name="show_in_web" value="1" {{ ($branch->show_in_web == 1) ? "checked" : ""}}>
															Yes
														</label>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="show_in_web" value="0" {{ ($branch->show_in_web == 0) ? "checked" : ""}}>
															No
														</label>
													</div>
												</div>  

												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Cover Image</label>
														<input type="file" class="form-control" name="cover_image" accept="image/*">
														@if($branch->cover_image)
														   <a href="{{$branch->cover_image??''}}" target="_blank">View</a>
														@endif
													</div>
												</div>

												<div class="col-md-12 col-12">
													<div class="form-group d-flex align-items-center mt-2">
														<div id="image-upload-container">
															<div class="image-container">
																<div>
																	<input type="file" name="images[]" accept="image/*" onchange="uploadImage(this)">
																	<input type="hidden" name="imagespath[]" value="" class="imagespath" readonly>
																	<button type="button" onclick="addImageField()">Add More</button>
																</div>
															</div>
														</div>
													</div>		
													<?php 
														if(!empty($branch->gallery)){
														$gallery = json_decode($branch->gallery);
														foreach($gallery as $ga){ 
													?>
													<div>
														<input type="hidden" name="imagespath[]" class="imagespath" value="<?=$ga;?>" readonly>
														<img src="<?=$ga;?>" width="60"/>
														<button type="button" onclick="removeField(this)">Remove</button>
													</div>												
														<?php } } ?>
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
<script type="text/javascript">
	function addImageField() {
        let container = document.getElementById("image-upload-container");
        let newField = document.createElement("div");
        newField.classList.add("image-container");
        newField.innerHTML = `
            <div>
                <input type="file" name="images[]" accept="image/*" onchange="uploadImage(this)">
                <input type="hidden" name="imagespath[]" class="imagespath" readonly>
                <button type="button" onclick="removeField(this)">Remove</button>
            </div>`;
        container.appendChild(newField);
    }

	function removeField(button) {
		button.parentElement.remove();
	}
	
	
	function uploadImage(input) {
		let formData = new FormData();
		formData.append('image', input.files[0]);
		let imagePathField = input.nextElementSibling;
		
		
		$.ajax({
			url: "{{ route('admin.branch.upload-image') }}",
			type: "POST",
			data: formData,
			processData: false,
			contentType: false,
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			success: function(response) {
				if (response.success) {
					imagePathField.value = response.imagePath; 
				} else {
					alert(response.error);
				}
			},
			error: function(xhr) {
				alert('Upload failed. Please try again.');
			}
		});
	}
</script>
@endsection
