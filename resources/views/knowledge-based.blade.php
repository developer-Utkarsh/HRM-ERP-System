<!DOCTYPE html>
<html lang="en" >
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Knowledge Based</title>
		
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css">
		<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.css" rel="stylesheet">
		<style>
			textarea.note-codable {
			  display: none;
			}

			.pt-3{
				padding-top : 3rem;
			}

			.panel {
				margin:0;
				height: 200px;
			}

			.bg-dark {
				background-color: #333;
				color: #fff;
				border: 0;
				padding: 5px 35px;
				font-size: 16px;
			}
		</style>
	</head>
	<body style="padding:15px">
		<form action="{{ route('store-knowledge-based', [$emp_id, $id]) }}" method="post">		
			@csrf	
			<div class="pt-3">
				<select class="form-control category_name" name="category_name">
					<option value="">-- Select Category --</option>
					@if(count($allCategory) > 0)
						@foreach($allCategory as $allCategoryValue)
							<option value="{{$allCategoryValue->id}}"  @if($allCategoryValue->id == old('category_name') || (!empty($knowledge_based_result->cat_id) && $knowledge_based_result->cat_id == $allCategoryValue->id)) selected="selected" @endif>{{$allCategoryValue->name}}</option>
						@endforeach
					@endif
				</select>
				@if($errors->has('category_name'))
				<span class="text-danger">{{ $errors->first('category_name') }} </span>
				@endif
			</div>
			<div class="pt-3">
				<textarea name="title" class="form-control title" placeholder="Title">{{ old('title', !empty($knowledge_based_result->title) ? $knowledge_based_result->title : '') }}</textarea>
				@if($errors->has('title'))
				<span class="text-danger">{{ $errors->first('title') }} </span>
				@endif
			</div>
			<div class="pt-3">
				<textarea name="description" id="summernote">{{ old('description', !empty($knowledge_based_result->description) ? $knowledge_based_result->description : '') }}</textarea>
				@if($errors->has('description'))
				<span class="text-danger">{{ $errors->first('description') }} </span>
				@endif
			</div>
			
			<div class="pt-3">
				<textarea name="reference_link" class="form-control reference_link" placeholder="Reference Link">{{ old('reference_link', !empty($knowledge_based_result->reference_link) ? $knowledge_based_result->reference_link : '') }}</textarea>
				@if($errors->has('reference_link'))
				<span class="text-danger">{{ $errors->first('reference_link') }} </span>
				@endif
			</div>
			<div class="pt-3"><button data-spinning-button type="submit" class="bg-dark text-white" style="border-radius: 10px;background-color: #000!important;">Submit</button></div>
		</form>
		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.js"></script>
		<script>
			$(document).ready(function(){
				<?php if(empty($knowledge_based_result->description)){ ?>
				$(".card-block").empty();
				<?php } ?>
			});
			$('#summernote').summernote({
				height: '300px',
				toolbar: [
					['style', ['bold', 'italic', 'underline', 'clear','fontname']],
					['color', ['color']],
					['fontsize', ['fontsize']],
					['height', ['height']],
					['style', ['style']],
					['para', ['ul', 'ol', 'paragraph']],  
					['misc', ['fullscreen','undo','redo']],
					['table', ['table']],
					["insert", ["link", "picture"]]
				]
			});

			$("button[data-spinning-button]").click(function(e) {
				$(this).append(" <i class='fa fa-spinner fa-spin'></i>");
			});
		</script>

		<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
		@if (session('success'))
		<script>
			swal({
				title: "Done!",
				text: "{{ session('success') }}",
				icon: "success",
				button: "ok",
			});
		</script>
		@endif
		@if (session('error'))
		<script>
			swal({
				title: "Error!",
				text: "{{ session('error') }}",
				icon: "error",
				button: "ok",
			});
		</script>
		@endif

	</body>
</html>
