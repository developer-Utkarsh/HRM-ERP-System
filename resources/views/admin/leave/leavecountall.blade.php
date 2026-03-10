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
						<h2 class="content-header-title float-left mb-0">Leave Count View</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">Leave View</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="content-body">
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				<?php if(Auth::user()->role_id == 29 || Auth::user()->role_id == 30 || Auth::user()->role_id == 24){ ?>
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								 
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
				<div class="table-responsive">
					<table class="table data-list-view" style="background:#fff;">
						<thead style="text-align: ;">
							<tr>
								<th>S.No.</th>
								<th>Emp Code</th>
								<th>Total Remaining PL</th>
								<th>Total Remaining SL</th>
							</tr>
						</thead>
						<tbody >
						<?php
						$i = 0;
						if(count($allUsers) > 0){
							foreach($allUsers as $val){
								$i++;
							?>
								<tr >
									<td><?=$i?></td>
									<td><?=$val['register_id']?></td>
									<td><?=!empty($val['leaves']->pending_pl)?$val['leaves']->pending_pl:0;?></td>
									<td><?=!empty($val['leaves']->pending_sl)?$val['leaves']->pending_sl:0;?></td>
								</tr>
							<?php
							}
						}
						?>
						
						</tbody>
					</table>
				</div>                   
			</section>
		</div>
	</div>
</div>
@endsection

@section('scripts')

@endsection
