<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Cleanliness Reports</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        body {
            /* font-size: 14px; */
            background-image: linear-gradient(to top, #f38800, #f39300, #f29e00, #f1a900, #f0b400, #efbc02, #eec506, #edcd0e, #edd60e, #ecde11,#eab516, #f0841c);

        }

        .report-image {
            width: 100%;
            max-width: 120px;
            height: auto;
            border-radius: 5px;
        }

        .table-responsive {
            font-size: 12px;
        }
    </style>
</head>

<body>

<div class="container mt-3">
    <h5 class="text-center mb-3">Cleanliness Report View</h5>

    @if(count($details) > 0)
        <div class="row">
            @foreach($details as $index => $details)
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="card-title">#{{ $index + 1 }} - {{ $details->branch_name }}</h6>
                            <p><strong>Shift:</strong> {{ $details->shift }}</p>
                            <p><strong>Remark:</strong> {{ $details->remark }}</p>
                            @php
                            switch($details->status){
                                                case 1: 
                                                    $statusText = 'Pending';
                                                    $statusClass = 'text-warning';
                                                    $rej_reason = '';
                                                    break;
                                                case 2: 
                                                    $statusText = 'Approved';
                                                    $statusClass = 'text-success';
                                                    $rej_reason = '';
                                                    break;
                                                case 3: 
                                                    $statusText = 'Reject';
                                                    $statusClass = 'text-danger';
                                                    $rej_reason = '(' . $details->rej_reason . ')';
                                                    break;
                                                default:
                                                    $statusText = 'Unknown';
                                                    $statusClass = 'text-muted';
                                                    $rej_reason = '';
                                                    break;
                                            }
                            @endphp
                            <p><strong>Status:</strong> <span class="btn {{ $statusClass }}">{{ $statusText }} {{ $rej_reason }}</span></p>
                            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($details->created_at)->format('d M Y h:i A') }}</p>

                            @if($details->image_path)
                            <div class="text-center">
                                <button type="button" class="btn btn-sm btn-danger text-center" data-toggle="modal"
                                    data-target="#imageModal{{ $details->id }}">
                                    View Image
                                </button>
                            </div>

                                <!-- Modal -->
                                <div class="modal fade" id="imageModal{{ $details->id }}" tabindex="-1" role="dialog"
                                    aria-labelledby="imageModalLabel{{ $details->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="imageModalLabel{{ $details->id }}">Report Image</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <img src="{{ asset('laravel/public/cleanliness/' . basename($details->image_path)) }}"
                                                    class="img-fluid rounded" style="width:200px" alt="Report Image">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <p><strong>Image:</strong> N/A</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-center">No reports found for this user.</p>
    @endif
</div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>