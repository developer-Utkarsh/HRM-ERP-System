@extends('layouts.admin')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
    a i:hover {
        cursor: pointer;
        transform: scale(1.2);
        transition: 0.2s;
    }

    a i {
        font-size: 18px !important;
        margin-left: 10px !important;
    }
</style>
@section('content')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-8">
                            <h2 class="content-header-title float-left mb-0"> Access Request</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-request"><a href="{{ route('admin.dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-request active"> >> List View</li>
                                </ol>
                            </div>
                        </div>
                        <div class="col-4 text-right">
                            @if (Auth::user()->role_id == 21)
                                <a href="{{ route('request-access.create') }}" class="btn btn-outline-primary mr-1">Access
                                    Request</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <section id="multiple-column-form">
                    <div class="row match-height">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body">
                                        <form class="form" action="{{ route('request-access') }}" method="get"
                                            name="filtersubmit">
                                            @csrf
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="first-name-column">Employee Name</label>
                                                            <input type="text" class="form-control"
                                                                placeholder="Employee name..." name="emp_name"
                                                                value="{{ request('emp_name') }}">
                                                            @if($errors->has('emp_name'))
                                                                <span
                                                                    class="text-danger">{{ $errors->first('emp_name') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="first-name-column">Status</label>
                                                            <select name="status" class="form-control">
                                                                <option value="">-- Select Status --</option>
                                                                <option value="InProcess" {{ request('status') == 'InProcess' ? 'selected' : '' }}>InProcess</option>
                                                                <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                                                                <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                                                <option value="Access Assigned" {{ request('status') == 'Access Assigned' ? 'selected' : '' }}>Access Assigned</option>
                                                            </select>
                                                            @if($errors->has('status'))
                                                                <span class="text-danger">{{ $errors->first('status') }} </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="first-name-column">Requester Name</label>
                                                            <input type="text" class="form-control" name="requester_name"
                                                                placeholder="Requester Name..."
                                                                value="{{ request('requester_name') }}">
                                                            @if($errors->has('status'))
                                                                <span class="text-danger">{{ $errors->first('status') }} </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="first-name-column">Software Name</label>
                                                            <input type="text" name="soft_name" class="form-control"
                                                                placeholder="Software Name..."
                                                                value="{{ request('soft_name') }}">
                                                            @if($errors->has('soft_name'))
                                                                <span class="text-danger">{{ $errors->first('soft_name') }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="d-flex" style="float:right">
                                                            <fieldset class="form-group mr-2">
                                                                <button type="submit"
                                                                    class="btn btn-primary">Search</button>
                                                            </fieldset>
                                                            <fieldset class="form-group">
                                                                <a href="{{ route('request-access') }}"
                                                                    class="btn btn-warning">Reset</a>
                                                            </fieldset>
                                                        </div>
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
                                    <th>Software Name</th>
                                    <th>Requested For</th>
                                    <th>Access Level</th>
                                    <th>Request Type</th>
                                    <th>Purpose</th>
                                    <th>Remark</th>
                                    <th>Requested By</th>
                                    <th>Status</th>
                                    <th>History</th>
                                    @php
                                        $isOwner = $requests->first() && Auth::id() == $requests->first()->owner_id;
                                    @endphp
                                    @if ($isOwner)
                                        <th>Action</th>
                                    @endif
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($requests as $index => $request)
                                    <tr>
                                        <td>{{ $requests->firstItem() + $index }}</td>
                                        <td>{{ $request->name }}</td>
                                        <td>
                                            @if($request->request_for == 'self')
                                                Self
                                            @else
                                                {{ $request->employee_name ?? '-' }} ({{ $request->employee_register_id ?? '-' }})
                                            @endif
                                        </td>
                                        <td>{{ $request->access_level ?? '-' }}</td>
                                        <td>{{ $request->request_type ?? '-' }}</td>
                                        <td>{{ $request->purpose ?? '-' }}</td>
                                        <td>{{ $request->remark ?? '-' }}</td>
                                        <td>{{ $request->requester_name ?? '-' }} ({{ $request->requester_register_id ?? '-' }})</td>


                                        <td>
                                            @if($request->status == 'InProcess')
                                                <span class="text-warning">InProcess</span>
                                            @elseif($request->status == 'Approved')
                                                <span class="text-success">Approved</span>
                                                <br>
                                                <small><strong>Remark: </strong> {{ $request->assign_remark ?? '-' }}</small>
                                            @elseif($request->status == 'Access Assigned')
                                                <span class="text-success">Access Assigned</span>
                                            @elseif($request->status == 'Rejected')
                                                <span class="text-danger">Rejected</span>
                                                <br>
                                                <small><strong>Reason:</strong> {{ $request->rej_reason ?? '-' }}</small>
                                            @elseif($request->status == 'Revoked')
                                                <span class="text-secondary">Revoked</span>
                                                <br>
                                                <small><strong>Reason: </strong> {{ $request->revoke_reason ?? '-' }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <button data-toggle="modal" class="btn btn-info btn-sm" data-target="#historyModal{{ $request->id }}">History</button>
                                                <!-- History Modal -->
                                            <div class="modal fade" id="historyModal{{ $request->id }}" tabindex="-1"
                                                role="dialog">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Access Request History</h5>
                                                            <button type="button" class="close"
                                                                data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            @if($request->history->count())
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Date</th>
                                                                            <th>Requested By</th>
                                                                            <th>Access Level</th>
                                                                            <th>Request Type</th>
                                                                            <th>Status</th>
                                                                            <th>Purpose</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($request->history as $entry)
                                                                            <tr>
                                                                                <td>{{ \Carbon\Carbon::parse($entry->created_at)->format('d M Y') }}
                                                                                </td>
                                                                                <td>{{ $entry->requester_name }}
                                                                                    ({{ $entry->requester_register_id }})</td>
                                                                                <td>{{ $entry->access_level ?? '-' }}</td>
                                                                                <td>{{ $entry->request_type ?? '-' }}</td>
                                                                                <td>
                                                                                    @if($entry->status == 'InProcess')
                                                                                        <span class="text-warning">InProcess</span>
                                                                                    @elseif($entry->status == 'Approved')
                                                                                        <span class="text-success">Approved</span>
                                                                                        <br>
                                                                                        <small><strong>Remark: </strong>
                                                                                            {{ $entry->assign_remark ?? '-' }}</small>
                                                                                    @elseif($entry->status == 'Access Assigned')
                                                                                        <span class="text-success">Access Assigned</span>
                                                                                    @elseif($entry->status == 'Rejected')
                                                                                        <span class="text-danger">Rejected</span>
                                                                                        <br>
                                                                                        <small><strong>Reason:</strong>
                                                                                            {{ $entry->rej_reason ?? '-' }}</small>
                                                                                    @elseif($entry->status == 'Revoked')
                                                                                        <span class="text-secondary">Revoked</span>
                                                                                        <br>
                                                                                        <small><strong>Reason: </strong>
                                                                                            {{ $entry->revoke_reason ?? '-' }}</small>
                                                                                    @endif
                                                                                </td>
                                                                                <td>{{ $entry->purpose ?? '-' }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            @else
                                                                <p>No previous requests found.</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </td>

                                        @if(Auth::id() == $request->owner_id)
                                            <td class="text-center">
                                                @if($request->status == 'Approved' || $request->status == 'Access Assigned')
                                                    <button data-toggle="modal" class="btn btn-secondary btn-sm"
                                                        data-target="#revokeModal{{ $request->id }}">Revoke</button>
                                                    <div class="modal fade" id="revokeModal{{ $request->id }}" tabindex="-1"
                                                        role="dialog" aria-labelledby="revokeModalLabel{{ $request->id }}"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <form action="{{ route('request-access.update') }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="id" value="{{ $request->id }}">
                                                                <input type="hidden" name="status" value="Revoked">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="revokeModalLabel{{ $request->id }}">
                                                                            Access</h5>
                                                                        <button type="button" class="close" data-dismiss="modal"
                                                                            aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>

                                                                    <div class="modal-body">
                                                                        <div class="form-group">
                                                                            <label for="revoke_reason">Reason for Revoking
                                                                                Access</label>
                                                                            <textarea name="revoke_reason" class="form-control"
                                                                                placeholder="Enter revoke reason..."
                                                                                required></textarea>
                                                                        </div>
                                                                    </div>

                                                                    <div class="modal-footer">
                                                                        <button type="submit" class="btn btn-danger">Revoke</button>
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-dismiss="modal">Cancel</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>

                                                @elseif($request->status == 'Rejected')
                                                    <span class="font-weight-bold">-</span>
                                                @elseif($request->status == 'Revoked')
                                                    <span class="">-</span>
                                                @elseif(Auth::id() == $request->owner_id && $request->status == 'InProcess')

                                                    <a href="#" data-toggle="modal" data-target="#approveModal{{ $request->id }}"
                                                        title="Approve">
                                                        <i class="fa fa-check text-success"></i>
                                                    </a>


                                                    <div class="modal fade" id="approveModal{{ $request->id }}" tabindex="-1"
                                                        role="dialog">
                                                        <div class="modal-dialog" role="document">
                                                            <form action="{{ route('request-access.update') }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="id" value="{{ $request->id }}">
                                                                <input type="hidden" name="status" value="Approved">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Assign Access Remark</h5>
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal">&times;</button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="form-group">
                                                                            <label for="assign_remark">Remark</label>
                                                                            <textarea name="assign_remark" class="form-control"
                                                                                placeholder="Enter login credentials, access link, etc."
                                                                                required></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="submit" class="btn btn-success">Approve</button>
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-dismiss="modal">Cancel</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>

                                                    <!-- <button type="button" class="btn btn-danger btn-sm mt-1" data-toggle="modal"
                                                                                                        data-target="#rejectModal{{ $request->id }}">
                                                                                                        Reject
                                                                                                    </button> -->
                                                    <a href="#" data-toggle="modal" data-target="#rejectModal{{ $request->id }}"
                                                        title="Reject">
                                                        <i class="fa fa-times-circle text-danger"></i>
                                                    </a>

                                                    <!-- Reject Modal -->
                                                    <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1"
                                                        role="dialog">
                                                        <div class="modal-dialog" role="document">
                                                            <form action="{{ route('request-access.update') }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="id" value="{{ $request->id }}">
                                                                <input type="hidden" name="status" value="Rejected">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Rejection Reason</h5>
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal">&times;</button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="form-group">
                                                                            <label for="rej_reason">Reason</label>
                                                                            <textarea name="rej_reason"
                                                                                placeholder="Enter Reason Reject Request..."
                                                                                class="form-control" required></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="submit" class="btn btn-danger">Reject</button>
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-dismiss="modal">Cancel</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center" colspan="9">No Requests Found</td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                        <div class="d-flex justify-content-center">
                            {!! $requests->appends($params)->links() !!}
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection