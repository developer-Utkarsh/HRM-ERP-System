@if(isset($breadcums) && count($breadcums) > 0)
<div class="container">
	<ul class="breadcrumbs">
		@foreach($breadcums as $key => $value)
		@if(isset($value) && !empty($value))
		<li class="breadcrumbs__item">
			<a href="{{ $value }}" class="breadcrumbs__url">{{ $key }}</a>
		</li>
		@else
		<li class="breadcrumbs__item">{{ $key }}</li>
		@endif
		@endforeach
	</ul>
</div>
@endif