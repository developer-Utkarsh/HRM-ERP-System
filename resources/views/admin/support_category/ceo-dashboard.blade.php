@extends('layouts.without_login_admin')
@section('content')
<div class="app-content content"  style="margin: 0px !important;">
	<div class="content-wrapper"  style="margin-top: 0px !important;">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-6 text-center">
						<h5 class="">Message to Chairman</h5>
					</div>
					
					<div class="col-6">
						<div class="float-right">
						   <i class="filterIcon fa fa-filter text-primary" style="font-size:30px;"></i>
						</div>
					</div>
				</div>
			</div>
		</div>

		@include('admin.support_category.filter')
        
        <div class="content-body">
			<section id="data-list-view" class="data-list-view-header">
				<div class="row">
					@foreach($data as $val)
					    @if(count($val->ticket_count))
						<div class="col-xl-4 col-md-6 mb-1">
							<div class="card" style="margin-bottom:0px">
								<div class="card-body">
									<div class="row">
										<div class="col mr-2">
											<div class="text-xs font-weight-bold text-black text-uppercase">{{$val->name}}</div>
										</div>
									</div>
									@php $total=0;@endphp
									<div class="row mt-2">
										@foreach($val->ticket_count as $tval)
										    <div class="col mr-2">
										      <?php 
										      $className="";
										      if($tval->status=='pending')
										       $className='text-danger';
										      else if($tval->status=='replied')
										       $className='text-primary';
										      else if($tval->status=='resolved')
										       $className='text-success';
										      else if($tval->status=='reopen')
										       $className='text-warning';?>
											  <a href="{{ \Request::Url()}}?search=search&category_id={{$val->id}}&status={{$tval->status}}">
											  	<div class="{{$className}}">
											  	 {{ucwords($tval->status)}}
											  </div></a>
											  <div>
											  	{{$tval->total}}
											  </div><hr>
									          @php $total+=$tval->total;@endphp
			                               </div>
										@endforeach
									</div>
								</div>
							</div>
						</div>
						@endif
					@endforeach
				</div>

				<div class="row">
					@if(count($enquiry) > 0)
						@foreach($enquiry as  $key => $val)
							<div class="col-md-6">
								<div class="card">
									<div class="card-body">
										<div class="row">
											<div class="col">
												
												<div class="row">
													<p class="col-4">Enquiry :</p>
												    <p class="col-8">{{$val->description}}</p>
												</div>

												<div class="row">
													<p class="col-4">Student Name :</p>
												    <p class="col-8">{{$val->student_name}}</p>
												</div>

												<div class="row">
													<p class="col-4">Reg No :</p>
												    <p class="col-8">{{$val->reg_no}}</p>
												</div>

												<div class="row">
													<p class="col-4">Batch:</p>
												    <p class="col-8">{{$val->batch_name}}</p>
												</div>

												<div class="row">
													<p class="col-4">Branch :</p>
												    <p class="col-8">{{$val->branch_name}} - {{ $val->location}}</p>
												</div>

												<div class="row">
													<p class="col-4">Category :</p>
												    <p class="col-8">{{$val->cat}}</p>
												</div>
												<div class="row">
													<p class="col-4">Status :</p>
												    <p class="col-8">{{ucwords($val->status) }}</p>
												</div>

												<div class="row">
													<p class="col-4">Created At :</p>
												    <p class="col-8">{{$val->created_at}}</p>
												</div>

												<div class="row">
													<p class="col-4">Updated At :</p>
												    <p class="col-8">{{$val->updated_at}}</p>
												</div>
											</div>
											
											<div class="col-auto d-none">
												<a href="javascript:void(0)" class="btn btn-outline-info btn-sm mt-1 old_query_data" data-id="{{$val->id}}" title="Old Query" style="padding: 0.5rem 0.5rem;"> <i class="fa fa-reply"></i></a>
											</div>
										</div>
									</div>
								</div>
							</div>
						@endforeach
					   <div class="col-12 mt-3 text-center">
						  {{$enquiry->links()}}
					    </div>
					@else
					  No record found
					@endif
				</div>
			</section>
		</div>
	</div>
</div>

@endsection
