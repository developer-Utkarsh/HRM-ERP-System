@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-6">
						<h2 class="content-header-title float-left mb-0">Message to Chairman</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
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
					@php 
					 $totalStatus=[];
					 $totalStatus[]=["id"=>0,"name"=>'pending','total'=>0,"className"=>'text-danger'];
					 $totalStatus[]=["id"=>0,"name"=>'replied','total'=>0,"className"=>'text-primary'];
					 $totalStatus[]=["id"=>0,"name"=>'resolved','total'=>0,"className"=>'text-success'];
					 $totalStatus[]=["id"=>0,"name"=>'reopen','total'=>0,"className"=>'text-warning'];
					@endphp

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
										@php
										 $tkStatus=[];
                                         $tkStatus[]=["id"=>$val->id,"name"=>'pending','total'=>0,"className"=>'text-danger'];
                                         $tkStatus[]=["id"=>$val->id,"name"=>'replied','total'=>0,"className"=>'text-primary'];
                                         $tkStatus[]=["id"=>$val->id,"name"=>'resolved','total'=>0,"className"=>'text-success'];
                                         $tkStatus[]=["id"=>$val->id,"name"=>'reopen','total'=>0,"className"=>'text-warning'];
									    @endphp
										
										@foreach($val->ticket_count as $tval)
										    @php 
										     $key = array_search($tval->status, array_column($tkStatus, 'name'));
                                             $tkStatus[$key]['total']=$tval->total;

                                             $index= array_search($tval->status, array_column($totalStatus, 'name'));
                                             $totalStatus[$index]['total']=$totalStatus[$index]['total']+$tval->total;
										    @endphp
										    
									        @php $total+=$tval->total;@endphp
										@endforeach

										@foreach($tkStatus as $tval)
										    <div class="col mr-1">
											  <a href="{{ route('admin.support-enquiry')}}?search=search&category_id={{$tval['id']}}&status={{$tval['name']}}">
											  	<div class="{{$tval['className']}}">
											  	 {{ucwords($tval['name'])}}
											  </div></a>
											  <div>
											  	{{$tval['total']}}
											  </div><hr>
			                               </div>
										@endforeach
									   <div class="col mr-1">
										  <a href="{{ route('admin.support-enquiry')}}?search=search&category_id={{$val->id}}">
										  	<div class="text-danger">
										  	 Total
										  </div></a>
										  <div>
										  	{{$total}}
										  </div><hr>
		                               </div>
									</div>
								</div>
							</div>
						</div>
						@endif
					@endforeach
                    
                    <div class="col-xl-4 col-md-6 mb-1">
						<div class="card" style="margin-bottom:0px">
							<div class="card-body" style="background:#b8c2cc;">
								<div class="row">
									<div class="col mr-2">
										<div class="text-xs font-weight-bold text-black text-uppercase">Total </div>
									</div>
								</div>

								<div class="row mt-2">
									@foreach($totalStatus as $tval)
									    <div class="col mr-1">
										  <a href="{{ route('admin.support-enquiry')}}?search=search&category_id={{$tval['id']}}&status={{$tval['name']}}">
										  	<div class="{{$tval['className']}}">
										  	 {{ucwords($tval['name'])}}
										  </div></a>
										  <div>
										  	{{$tval['total']}}
										  </div><hr>
				                       </div>
									@endforeach
				                </div>
				            </div>
				        </div>
				    </div>

				</div>
			</section>
		</div>
	</div>
</div>

@endsection
