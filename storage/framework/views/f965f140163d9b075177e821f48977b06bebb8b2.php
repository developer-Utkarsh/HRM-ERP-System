
<?php $__env->startSection('content'); ?>
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-8">
                            <h2 class="content-header-title float-left mb-0">Cleanliness Report View</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a></li>
                                    <li class="breadcrumb-item active">List View</li>
                                </ol>
                            </div>
                        </div>
                        <div class="col-4 text-right">
                            <a href="<?php echo e(route('complaint-view')); ?>" class="btn btn-outline-primary">View Complaint</a>
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
                                        <form class="form" action="<?php echo e(route('view-cleanliness-report')); ?>" method="get"
                                            name="filtersubmit">
                                            <?php echo csrf_field(); ?>
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="emp_code" class="form-label">Search by Employee
                                                                Code</label>
                                                            <input type="number" class="form-control"
                                                                placeholder="Enter Employee Code..." id="emp_code"
                                                                name="emp_code"
                                                                value="<?php echo e(app('request')->input('emp_code')); ?>">
                                                            <?php if($errors->has('emp_code')): ?>
                                                                <span class="text-danger"><?php echo e($errors->first('emp_code')); ?>

                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <fieldset class="form-group">
                                                            <button type="submit"
                                                                class="btn btn-primary mt-1">Search</button>
                                                            <a href="<?php echo e(route('view-cleanliness-report')); ?>"
                                                                class="btn btn-warning mt-1">Reset</a>
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
                                    <th>Name</th>
                                    <th>Branch Name</th>
                                    <th>Shift</th>
                                    <th>Remark</th>
                                    <th class="text-center">Image</th>
                                    <?php if (Auth::user()->user_details->degination != "CENTER HEAD") {?>
                                    <th>Status</th>
                                    <?php } ?>
                                    <th>Created Date</th>

                                    <?php if (Auth::user()->user_details->degination == "CENTER HEAD") {?>
                                    <th>Action</th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($index + 1); ?></td>
                                        <td><?php echo e($report->user_name); ?> <span class="text-primary font-weight-bold">
                                                (<?php echo e($report->register_id); ?>) </span> </td>
                                        <td><?php echo e($report->branch_name); ?></td>
                                        <td><?php echo e($report->shift); ?></td>
                                        <td><?php echo e($report->remark); ?></td>
                                        <td class="text-center">
                                            <?php if($report->image_path): ?>
                                                <a href="<?php echo e(asset('laravel/public/cleanliness/' . basename($report->image_path))); ?>"
                                                    target="_blank">
                                                    View
                                                </a>
                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>
                                        <?php
                                            switch($report->status){
                                                case 1: 
                                                    $statusText = 'Pending';
                                                    $statusClass = 'text-warning';
                                                    break;
                                                case 2: 
                                                    $statusText = 'Approved';
                                                    $statusClass = 'text-success';
                                                    break;
                                                case 3: 
                                                    $statusText = 'Reject';
                                                    $statusClass = 'text-danger';
                                                    break;
                                                default:
                                                    $statusText = 'Unknown';
                                                    $statusClass = 'text-muted';
                                                    break;
                                            }
                                         
                                        ?>
                                        <?php if (Auth::user()->user_details->degination != "CENTER HEAD") {?>
                                        <td> <span class="font-weight-bold <?php echo e($statusClass); ?>"><?php echo e($statusText); ?> </span></td>
                                        <?php } ?>
                                        
                                        <td><?php echo e(\Carbon\Carbon::parse($report->created_at)->format('d M Y h:i A')); ?></td>
                                        <?php    if (Auth::user()->user_details->degination == "CENTER HEAD") { ?>
                                        <td>
                                            <?php if($report->status == 1): ?>
                                            <form action="<?php echo e(route('update-cleanliness-status', $report->id)); ?>" method="post" style="display:inline;">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="status" value="2"> 
                                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                            </form>
                                            <div class="d-inline">
                                                
                                                <button type="submit" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectModal">Reject</button>

                                                    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                      <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                          <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel">Reason for Reject</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                              <span aria-hidden="true">&times;</span>
                                                            </button>
                                                          </div>
                                                          <form action="<?php echo e(route('update-cleanliness-status', $report->id)); ?>" method="post">
                                                             <?php echo csrf_field(); ?>
                                                          <div class="modal-body">
                                                            <div>
                                                                <input type="hidden" name="status" value="3">
                                                                <textarea name="rejectReason" rows="10" class="form-control" placeholder="Enter Reject Reason here..." id=""></textarea>
                                                                <?php if($errors->has('rejectReason')): ?>
                                                                    <span class="text-danger"><?php echo e($errors->first('rejectReason')); ?></span>
                                                                <?php endif; ?>
                                                            </div>
                                                          </div>
                                                          <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary">Submit</button>
                                                          </div>
                                                          </form>
                                                        </div>
                                                      </div>
                                                    </div>
                                            </div>
                                            <!--<form action="<?php echo e(route('update-cleanliness-status', $report->id)); ?>" method="POST" style="display:inline;">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PUT'); ?>
                                                <input type="hidden" name="status" value="3">
                                                <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                            </form> -->
                                            <?php elseif($report->status == 2): ?>
                                             <span class="font-weight-bold <?php echo e($statusClass); ?>"><?php echo e($statusText); ?></span>
                                            <?php elseif($report->status == 3): ?>
                                             <p class="font-weight-bold <?php echo e($statusClass); ?>"><?php echo e($statusText); ?><span class="text-info font-weight-normal"> (<?php echo e($report->rej_reason); ?>)</span></p>
                                            <?php endif; ?>
                                        </td>
                                        <?php    } ?>
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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/all_reports/view-cleanliness-report.blade.php ENDPATH**/ ?>