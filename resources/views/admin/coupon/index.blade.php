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
                            <h2 class="content-header-title float-left mb-0">Assign Coupon</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active">List View</li>
                                </ol>
                            </div>
                        </div>
                        <div class="col-4 text-right">
                            <?php 
                                 $user_access = (Auth::user()->id == 901 || Auth::user()->department_type == 45)
                             ?>
                            <?php if ($user_access) { ?>
                            <a href="{{ route('coupon.assign') }}" class="btn btn-outline-primary mr-1">Assign Coupon</a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <section id="multiple-column-form">
                    <div class="card">
                        <div class="card-content collapse show">
                            <div class="card-body">
                                <div class="users-list-filter">
                                    <form action="{{ route('coupon.index') }}" method="get" name="filtersubmit">
                                        <div class="row">
                                            @if(Auth::user()->id == 901 || Auth::user()->id == 7322 || Auth::user()->id == 7509)
                                            <div class="col-12 col-sm-6 col-lg-3">
                                                <label for="users-list-status">Assign By</label>
                                                <?php $emp = \App\User::where('department_type', '45')->where('status', '1')->orderBy('id', 'desc')->get(); ?>
                                                <fieldset class="form-group">
                                                   <select class="form-control select-multiple1 emp_id" name="emp_id">
                                                        <option value="">Select Any</option>
                                                        @foreach($emp as $value)
                                                            <option value="{{ $value->id }}"
                                                                @if(app('request')->input('emp_id') == $value->id)
                                                                    selected
                                                                @endif>{{ $value->name }} ({{ $value->register_id }})</option>
                                                        @endforeach
                                                    </select>
                                                </fieldset>
                                            </div>
                                            @endif
                                            <div class="col-12 col-sm-6 col-lg-3">
                                                <label for="mobile">Mobile</label>
                                                <fieldset class="form-group">
                                                    <input type="text" class="form-control" name="mobile"
                                                        placeholder="Enter mobile number to search..."
                                                        value="{{ app('request')->input('mobile') }}">
                                                </fieldset>
                                            </div>
                                            <div class="col-12 col-sm-6 col-lg-3">
                                                <label for="users-list-status">From Date</label>
                                                <fieldset class="form-group">
                                                    <input type="date" name="fdate" placeholder="DD-MM-YYYY"
                                                        value="{{ app('request')->input('fdate') }}"
                                                        class="form-control StartDateClass fdate">
                                                </fieldset>
                                            </div>

                                            <div class="col-12 col-sm-6 col-lg-3">
                                                <label for="users-list-status">To Date</label>
                                                <fieldset class="form-group">
                                                    <input type="date" name="tdate" placeholder="DD-MM-YYYY"
                                                        value="{{ app('request')->input('tdate') }}"
                                                        class="form-control EndDateClass tdate">
                                                </fieldset>
                                            </div>
                                            <div class="col-12 col-sm-12 col-lg-12">
                                                <label for="" style="">&nbsp;</label>
                                                <fieldset class="form-group">
                                                    <button type="submit" class="btn btn-primary">Search</button>
                                                    <a href="{{ route('coupon.index') }}" class="btn btn-warning">Reset</a>
                                                    <a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a>
                                                </fieldset>
                                            </div>
                                        </div>
                                    </form>
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
                                    <th width="10%">S.No.</th>
                                    @php
                                        $adminIds = [901, 7509, 7322];
                                    @endphp
                                    @if(in_array(Auth::user()->id, $adminIds))
                                        <th width="20%">Assigner Name</th>
                                    @endif
                                    <th width="20%">Mobile</th>
                                    <th width="20%">Coupon Code</th>
                                    <th width="20%">Created Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($userDetails as $index => $coupon)
                                    <tr>
                                        <td>{{ ($userDetails->currentPage() - 1) * $userDetails->perPage() + $index + 1 }}</td>
                                        @if(in_array(Auth::user()->id, $adminIds))
                                            <td>{{ $coupon->assigner_name ?? '-' }}</td>
                                        @endif
                                        <td>{{ $coupon->mobile }}</td>
                                        <td><a href="" style="font-size:14px;"
                                                class="btn btn-sm btn-success font-weight-bold waves-effect waves-light">{{ $coupon->coupon_code }}</a>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($coupon->created_at)->format('d M Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No coupons assigned yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center">
                            {!! $userDetails->links() !!}
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
 <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.select-multiple1').select2({
                placeholder: "Select Any",
                allowClear: true
            });
             $("body").on("click", "#download_excel", function (e) {
                var data = {};
                data.emp_id = $('.emp_id').val();
                data.mobile = $('input[name="mobile"]').val();
                data.fdate = $('input[name="fdate"]').val();
                data.tdate = $('input[name="tdate"]').val();
                window.location.href = "{{ route('coupon.export') }}?" + Object.keys(data).map(function (k) {
                    return encodeURIComponent(k) + '=' + encodeURIComponent(data[k] || '');
                }).join('&');
            });
        });
    </script>
@endsection