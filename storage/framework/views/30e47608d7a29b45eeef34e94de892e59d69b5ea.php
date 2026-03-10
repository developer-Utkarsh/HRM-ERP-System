<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Cleanliness Complaint Reports</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            /* font-size: 14px; */
            background-image: linear-gradient(to top, #f38800, #f39300, #f29e00, #f1a900, #f0b400, #efbc02, #eec506, #edcd0e, #edd60e, #ecde11, #eab516, #f0841c);

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
        <h5 class="text-center mb-3">Cleanliness Complaint Report List</h5>

        <?php if(count($details) > 0): ?>
            <div class="row">
                <?php $__currentLoopData = $details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="card-title">Emp.Name : <?php echo e($details->user_name); ?> <span class="text-info">(<?php echo e($details->register_id); ?>)</span></h6>
                                <p><strong>Complaint:</strong> <?php echo e($details->complaint); ?></p>
                                <p><strong>Date:</strong>
                                    <?php echo e(\Carbon\Carbon::parse($details->created_at)->format('d M Y h:i A')); ?></p>

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
                                <form method="POST" action="<?php echo e(route('complaint-report-update', $details->id)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="updated_by_id" value="<?php echo e(request()->segment(3)); ?>">

                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select name="status" class="form-control form-control-sm">
                                            <option value="">Select Status</option>
                                            <option value="In Progress" <?php echo e($details->status == 'In Progress' ? 'selected' : ''); ?>> In Progress</option>
                                            <option value="Cleaned" <?php echo e($details->status == 'Cleaned' ? 'selected' : ''); ?>>Cleaned</option>
                                        </select>
                                        <?php if($errors->has('status')): ?>
                                            <span class="text-danger"><?php echo e($errors->first('status')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group">
                                        <label for="admin_comment">Comment</label>
                                        <textarea name="admin_comment" class="form-control" placeholder="Write your comment here..." id="admin_comment"><?php echo e($details->comment); ?></textarea>
                                        <?php if($errors->has('admin_comment')): ?>
                                            <span class="text-danger"><?php echo e($errors->first('admin_comment')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <p class="text-center">No reports found for this user.</p>
        <?php endif; ?>
        <?php if(session('success')): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?php echo e(session('success')); ?>',
                confirmButtonColor: '#28a745'
            });
        </script>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '<?php echo e(session('error')); ?>',
                confirmButtonColor: '#dc3545'
            });
        </script>
    <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html><?php /**PATH /var/www/html/laravel/resources/views/admin/all_reports/app-complaint-view-report.blade.php ENDPATH**/ ?>