<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Complaint Cleanliness Reports</title>
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
    <h5 class="text-center mb-3">Complaint Cleanliness Report</h5>

    <?php if(count($details) > 0): ?>
        <div class="row">
            <?php $__currentLoopData = $details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="card-title">#<?php echo e($index + 1); ?> - <?php echo e($details->branch_name); ?></h6>
                            
                            <p><strong>Complaint:</strong> <?php echo e($details->complaint); ?></p>
                            <p><strong>Date:</strong> <?php echo e(\Carbon\Carbon::parse($details->created_at)->format('d M Y h:i A')); ?></p>
                            <?php
                            switch($details->status){
                                                case 'Pending': 
                                                    $statusText = 'Pending';
                                                    $statusClass = 'text-warning';
                                                    break;
                                                case 'In Progress': 
                                                    $statusText = 'In Progress';
                                                    $statusClass = 'text-info';
                                                    break;
                                                case 'Cleaned': 
                                                    $statusText = 'Cleaned';
                                                    $statusClass = 'text-success';
                                                    break;
                                                default:
                                                    $statusText = 'Unknown';
                                                    $statusClass = 'text-muted';
                                                    break;
                                            }
                            ?>
                            <p><strong>Status:</strong> <span class="btn <?php echo e($statusClass); ?>"><?php echo e($statusText); ?></span></p>
                            <p><strong>Comment:</strong> <?php echo e($details->comment); ?></p>

                            <?php if($details->media_path): ?>
                            <div class="text-center">
                                <button type="button" class="btn btn-sm btn-danger text-center" data-toggle="modal"
                                    data-target="#imageModal<?php echo e($details->id); ?>">
                                    View Image
                                </button>
                            </div>

                                <!-- Modal -->
                                <div class="modal fade" id="imageModal<?php echo e($details->id); ?>" tabindex="-1" role="dialog"
                                    aria-labelledby="imageModalLabel<?php echo e($details->id); ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="text-end">
                                                <button type="button" class="close m-1" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <img src="<?php echo e(asset('laravel/public/cleanliness/complaint/' . basename($details->media_path))); ?>"
                                                    class="img-fluid rounded" style="width:200px" alt="Report Image">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <p><strong>Image:</strong> N/A</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php else: ?>
        <p class="text-center">No reports found for this user.</p>
    <?php endif; ?>
</div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html><?php /**PATH /var/www/html/laravel/resources/views/admin/all_reports/app-view-complaint.blade.php ENDPATH**/ ?>