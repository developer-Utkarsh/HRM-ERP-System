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
						<h2 class="content-header-title float-left mb-0">View Batch List</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			
			<div class="table-responsive">
				<table class="table data-list-view" id="">
					<thead>
						<tr>
							<th>S. No.</th>
							<th>Batch Name</th>
							<th>Batch Code</th>
						</tr>
					</thead>
					<tbody>
						<?php 
							$i = 1;
							foreach($forum_question as $r){
						?>
						<tr>
							<td><?=$i;?></td>
							<td>{{ $r->name }}</td>
							<td>{{ $r->batch_code }}</td>
						</tr>
						<?php $i++; } ?>
					</tbody>
				</table>
			</div> 
		</div>
	</div>
</div>


@endsection
@section('scripts')
<script type="text/javascript">

</script>
@endsection
