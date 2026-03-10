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
                            <h2 class="content-header-title float-left mb-0">Cleanliness Complaint Report View</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                    <li class="breadcrumb-item active">List View</li>
                                </ol>
                            </div>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('view-cleanliness-report') }}" class="btn btn-outline-primary">Back</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-body">
               
                <section id="data-list-view" class="data-list-view-header">
                    <div class="table-responsive">
                        <table class="table data-list-view" style="background:#fff;width:100%" id="assetTable">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Name</th>
                                    <th>Branch Name</th>
                                    <th>Complaint</th>
                                    <th class="text-center">Image</th>
                                    <?php if (Auth::user()->user_details->degination == "CENTER HEAD") {?>
                                    <th>Status</th>
                                    <?php } ?>
                                    <th>Comment</th>
                                    <th>Created Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $index => $report)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $report->user_name }} <span class="text-primary font-weight-bold">
                                                ({{ $report->register_id }}) </span> </td>
                                        <td>{{ $report->branch_name }}</td>
                                  
                                        <td>{{ $report->complaint }}</td>
                                        <td class="text-center">
                                            @if($report->media_path)
                                                <a href="{{ asset('laravel/public/cleanliness/complaint/' . basename($report->media_path)) }}"
                                                    target="_blank">
                                                    View
                                                </a>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        @php
                                            switch($report->status){
                                                case 'Pending': 
                                                    $statusText = 'Pending';
                                                    $statusClass = 'text-warning';
                                                    break;
                                                case 'Cleaned': 
                                                    $statusText = 'Cleaned';
                                                    $statusClass = 'text-success';
                                                    break;
                                                case 'In Progress': 
                                                    $statusText = 'In Progress';
                                                    $statusClass = 'text-info';
                                                    break;
                                                default:
                                                    $statusText = 'Unknown';
                                                    $statusClass = 'text-muted';
                                                    break;
                                            }
                                         
                                        @endphp
                                        <?php if (Auth::user()->user_details->degination == "CENTER HEAD") {?>
                                        <td> <span class="font-weight-bold {{ $statusClass }}">{{ $statusText }} </span></td>
                                        <?php } ?>
                                        <td>{{ $report->comment }}</td>
                                        <td>{{ \Carbon\Carbon::parse($report->created_at)->format('d M Y h:i A') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No Data Found</td>
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