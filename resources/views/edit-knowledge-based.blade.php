<!DOCTYPE html>
<html lang="en" >
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Knowledge Based</title>
		<script src="//code.jquery.com/jquery-1.9.1.min.js"></script> 
		<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.1/css/bootstrap.min.css" rel="stylesheet"> 
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.1/js/bootstrap.min.js"></script> 
		<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
		<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css'>
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
		<form action="{{ route('update-knowledge-based', [$emp_id, $id]) }}" method="post">		
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
				<input type="text" name="title" class="form-control title" value="{{ old('title', !empty($knowledge_based_result->title) ? $knowledge_based_result->title : '') }}" placeholder="Title"/>
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
				<input type="text" name="reference_link" class="form-control reference_link" value="{{ old('reference_link', !empty($knowledge_based_result->reference_link) ? $knowledge_based_result->reference_link : '') }}" placeholder="Reference Link"/>
				@if($errors->has('reference_link'))
				<span class="text-danger">{{ $errors->first('reference_link') }} </span>
				@endif
			</div>
			<div class="pt-3"><button type="submit" class="bg-dark text-white">Submit</button></div>
		</form>
		
		<script src='https://cdnjs.cloudflare.com/ajax/libs/summernote/0.6.16/summernote.min.js'></script>
		<script src='https://cdnjs.cloudflare.com/ajax/libs/snap.svg/0.3.0/snap.svg-min.js'></script>
		<script>
			$('#summernote').summernote({
			  toolbar: [
			  ["style", ["style"]],
			  ["font", ["bold", "italic", "underline", "clear"]],
			  ["fontsize", ["fontsize"]],
			  ["para", ["ul", "ol", "paragraph"]],
			  ["insert", ["link", "picture", "hr"]]],

			  lang: "fr-FR" });
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
