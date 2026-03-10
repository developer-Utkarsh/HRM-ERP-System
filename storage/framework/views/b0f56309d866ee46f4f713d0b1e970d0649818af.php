
<?php $__env->startSection('content'); ?>
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
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a></li>
                                    <li class="breadcrumb-item active">List View</li>
                                </ol>
                            </div>
                        </div>
                        <div class="col-4 text-right">
                            <a href="<?php echo e(route('view-cleanliness-report')); ?>" class="btn btn-outline-primary">Back</a>
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
                                <?php $__empty_1 = true; $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($index + 1); ?></td>
                                        <td><?php echo e($report->user_name); ?> <span class="text-primary font-weight-bold">
                                                (<?php echo e($report->register_id); ?>) </span> </td>
                                        <td><?php echo e($report->branch_name); ?></td>
                                  
                                        <td><?php echo e($report->complaint); ?></td>
                                        <td class="text-center">
                                            <?php if($report->media_path): ?>
                                                <a href="<?php echo e(asset('laravel/public/cleanliness/complaint/' . basename($report->media_path))); ?>"
                                                    target="_blank">
                                                    View
                                                </a>
                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>
                                        <?php
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
                                         
                                        ?>
                                        <?php if (Auth::user()->user_details->degination == "CENTER HEAD") {?>
                                        <td> <span class="font-weight-bold <?php echo e($statusClass); ?>"><?php echo e($statusText); ?> </span></td>
                                        <?php } ?>
                                        <td><?php echo e($report->comment); ?></td>
                                        <td><?php echo e(\Carbon\Carbon::parse($report->created_at)->format('d M Y h:i A')); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No Data Found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/all_reports/view-cleanliness-complaint.blade.php ENDPATH**/ ?>