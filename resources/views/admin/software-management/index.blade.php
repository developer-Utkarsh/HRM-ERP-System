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
                            <h2 class="content-header-title float-left mb-0">Software Management System</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active">List View</li>
                                </ol>
                            </div>
                        </div>
                        @if(Auth::user()->id == 901)
                        <div class="col-4 text-right">
                            <a href="{{ route('software-management.create') }}" class="btn btn-outline-primary mr-1">Add Software</a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="content-body">
            <section id="multiple-column-form" style="display: none;">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="{{ route('coupon.index') }}" method="get" name="filtersubmit">
										@csrf
										<div class="form-body">
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<input type="number" class="form-control" placeholder="Enter Mobile number to search..." value="{{ request('mobile') }}" id="mobile" name="mobile">
														@if($errors->has('mobile'))
														<span class="text-danger">{{ $errors->first('mobile') }} </span>
														@endif
													</div>
												</div>		                                
												<div class="col-md-8">
													<fieldset class="form-group">		
														<a href="{{ route('coupon.index') }}" class="btn btn-warning">Reset</a>
													</fieldset>
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
                <section id="data-list-view" class="data-list-view-header">

                    <div class="table-responsive">
                        <table class="table data-list-view" style="background:#fff;width:100%" id="assetTable">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th >Software Name</th>
                                    <th >Type</th>
                                    <th >Description</th>
                                    <th >Software Owner</th>
                                    <th >Status</th>
                                    <th >Date</th>
                                    <th >Action</th>
                                </tr>
                            </thead>
                           <tbody>
                            @forelse($details as $index=> $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->soft_type ?? '-'}}</td>
                                    <td>{{ $item->description ?? '-' }}</td>
                                    <td>{{ $item->owner_name }} <span class="text-primary font-weight-bold">({{ $item->owner_register_id }})</span></td>
                                    <td>{{ $item->status }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->created)->format('d-m-Y') }}</td>
                                    <td><a href="{{ route('software-management.edit', $item->id) }}" class="btn btn-sm btn-primary">Edit</a></td>

                                </tr>
                                @empty
                                <tr>
                                    <td class="text-center" colspan="8">No Software Found</td>
                                </tr>
                            @endforelse
                           </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection