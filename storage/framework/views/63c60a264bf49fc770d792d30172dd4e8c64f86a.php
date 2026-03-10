
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
<?php $__env->startSection('content'); ?>
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
                                    <li class="breadcrumb-request"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
                                    </li>
                                    <li class="breadcrumb-request active"> >> List View</li>
                                </ol>
                            </div>
                        </div>
                        <div class="col-4 text-right">
                            <?php if(Auth::user()->role_id == 21): ?>
                                <a href="<?php echo e(route('request-access.create')); ?>" class="btn btn-outline-primary mr-1">Access
                                    Request</a>
                            <?php endif; ?>
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
                                        <form class="form" action="<?php echo e(route('request-access')); ?>" method="get"
                                            name="filtersubmit">
                                            <?php echo csrf_field(); ?>
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="first-name-column">Employee Name</label>
                                                            <input type="text" class="form-control"
                                                                placeholder="Employee name..." name="emp_name"
                                                                value="<?php echo e(request('emp_name')); ?>">
                                                            <?php if($errors->has('emp_name')): ?>
                                                                <span
                                                                    class="text-danger"><?php echo e($errors->first('emp_name')); ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="first-name-column">Status</label>
                                                            <select name="status" class="form-control">
                                                                <option value="">-- Select Status --</option>
                                                                <option value="InProcess" <?php echo e(request('status') == 'InProcess' ? 'selected' : ''); ?>>InProcess</option>
                                                                <option value="Approved" <?php echo e(request('status') == 'Approved' ? 'selected' : ''); ?>>Approved</option>
                                                                <option value="Rejected" <?php echo e(request('status') == 'Rejected' ? 'selected' : ''); ?>>Rejected</option>
                                                                <option value="Access Assigned" <?php echo e(request('status') == 'Access Assigned' ? 'selected' : ''); ?>>Access Assigned</option>
                                                            </select>
                                                            <?php if($errors->has('status')): ?>
                                                                <span class="text-danger"><?php echo e($errors->first('status')); ?> </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="first-name-column">Requester Name</label>
                                                            <input type="text" class="form-control" name="requester_name"
                                                                placeholder="Requester Name..."
                                                                value="<?php echo e(request('requester_name')); ?>">
                                                            <?php if($errors->has('status')): ?>
                                                                <span class="text-danger"><?php echo e($errors->first('status')); ?> </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="first-name-column">Software Name</label>
                                                            <input type="text" name="soft_name" class="form-control"
                                                                placeholder="Software Name..."
                                                                value="<?php echo e(request('soft_name')); ?>">
                                                            <?php if($errors->has('soft_name')): ?>
                                                                <span class="text-danger"><?php echo e($errors->first('soft_name')); ?>

                                                                </span>
                                                            <?php endif; ?>
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
                                                                <a href="<?php echo e(route('request-access')); ?>"
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
                                    <?php
                                        $isOwner = $requests->first() && Auth::id() == $requests->first()->owner_id;
                                    ?>
                                    <?php if($isOwner): ?>
                                        <th>Action</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>

                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($requests->firstItem() + $index); ?></td>
                                        <td><?php echo e($request->name); ?></td>
                                        <td>
                                            <?php if($request->request_for == 'self'): ?>
                                                Self
                                            <?php else: ?>
                                                <?php echo e($request->employee_name ?? '-'); ?> (<?php echo e($request->employee_register_id ?? '-'); ?>)
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($request->access_level ?? '-'); ?></td>
                                        <td><?php echo e($request->request_type ?? '-'); ?></td>
                                        <td><?php echo e($request->purpose ?? '-'); ?></td>
                                        <td><?php echo e($request->remark ?? '-'); ?></td>
                                        <td><?php echo e($request->requester_name ?? '-'); ?> (<?php echo e($request->requester_register_id ?? '-'); ?>)</td>


                                        <td>
                                            <?php if($request->status == 'InProcess'): ?>
                                                <span class="text-warning">InProcess</span>
                                            <?php elseif($request->status == 'Approved'): ?>
                                                <span class="text-success">Approved</span>
                                                <br>
                                                <small><strong>Remark: </strong> <?php echo e($request->assign_remark ?? '-'); ?></small>
                                            <?php elseif($request->status == 'Access Assigned'): ?>
                                                <span class="text-success">Access Assigned</span>
                                            <?php elseif($request->status == 'Rejected'): ?>
                                                <span class="text-danger">Rejected</span>
                                                <br>
                                                <small><strong>Reason:</strong> <?php echo e($request->rej_reason ?? '-'); ?></small>
                                            <?php elseif($request->status == 'Revoked'): ?>
                                                <span class="text-secondary">Revoked</span>
                                                <br>
                                                <small><strong>Reason: </strong> <?php echo e($request->revoke_reason ?? '-'); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button data-toggle="modal" class="btn btn-info btn-sm" data-target="#historyModal<?php echo e($request->id); ?>">History</button>
                                                <!-- History Modal -->
                                            <div class="modal fade" id="historyModal<?php echo e($request->id); ?>" tabindex="-1"
                                                role="dialog">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Access Request History</h5>
                                                            <button type="button" class="close"
                                                                data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <?php if($request->history->count()): ?>
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
                                                                        <?php $__currentLoopData = $request->history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                            <tr>
                                                                                <td><?php echo e(\Carbon\Carbon::parse($entry->created_at)->format('d M Y')); ?>

                                                                                </td>
                                                                                <td><?php echo e($entry->requester_name); ?>

                                                                                    (<?php echo e($entry->requester_register_id); ?>)</td>
                                                                                <td><?php echo e($entry->access_level ?? '-'); ?></td>
                                                                                <td><?php echo e($entry->request_type ?? '-'); ?></td>
                                                                                <td>
                                                                                    <?php if($entry->status == 'InProcess'): ?>
                                                                                        <span class="text-warning">InProcess</span>
                                                                                    <?php elseif($entry->status == 'Approved'): ?>
                                                                                        <span class="text-success">Approved</span>
                                                                                        <br>
                                                                                        <small><strong>Remark: </strong>
                                                                                            <?php echo e($entry->assign_remark ?? '-'); ?></small>
                                                                                    <?php elseif($entry->status == 'Access Assigned'): ?>
                                                                                        <span class="text-success">Access Assigned</span>
                                                                                    <?php elseif($entry->status == 'Rejected'): ?>
                                                                                        <span class="text-danger">Rejected</span>
                                                                                        <br>
                                                                                        <small><strong>Reason:</strong>
                                                                                            <?php echo e($entry->rej_reason ?? '-'); ?></small>
                                                                                    <?php elseif($entry->status == 'Revoked'): ?>
                                                                                        <span class="text-secondary">Revoked</span>
                                                                                        <br>
                                                                                        <small><strong>Reason: </strong>
                                                                                            <?php echo e($entry->revoke_reason ?? '-'); ?></small>
                                                                                    <?php endif; ?>
                                                                                </td>
                                                                                <td><?php echo e($entry->purpose ?? '-'); ?></td>
                                                                            </tr>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                    </tbody>
                                                                </table>
                                                            <?php else: ?>
                                                                <p>No previous requests found.</p>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </td>

                                        <?php if(Auth::id() == $request->owner_id): ?>
                                            <td class="text-center">
                                                <?php if($request->status == 'Approved' || $request->status == 'Access Assigned'): ?>
                                                    <button data-toggle="modal" class="btn btn-secondary btn-sm"
                                                        data-target="#revokeModal<?php echo e($request->id); ?>">Revoke</button>
                                                    <div class="modal fade" id="revokeModal<?php echo e($request->id); ?>" tabindex="-1"
                                                        role="dialog" aria-labelledby="revokeModalLabel<?php echo e($request->id); ?>"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <form action="<?php echo e(route('request-access.update')); ?>" method="POST">
                                                                <?php echo csrf_field(); ?>
                                                                <input type="hidden" name="id" value="<?php echo e($request->id); ?>">
                                                                <input type="hidden" name="status" value="Revoked">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="revokeModalLabel<?php echo e($request->id); ?>">
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

                                                <?php elseif($request->status == 'Rejected'): ?>
                                                    <span class="font-weight-bold">-</span>
                                                <?php elseif($request->status == 'Revoked'): ?>
                                                    <span class="">-</span>
                                                <?php elseif(Auth::id() == $request->owner_id && $request->status == 'InProcess'): ?>

                                                    <a href="#" data-toggle="modal" data-target="#approveModal<?php echo e($request->id); ?>"
                                                        title="Approve">
                                                        <i class="fa fa-check text-success"></i>
                                                    </a>


                                                    <div class="modal fade" id="approveModal<?php echo e($request->id); ?>" tabindex="-1"
                                                        role="dialog">
                                                        <div class="modal-dialog" role="document">
                                                            <form action="<?php echo e(route('request-access.update')); ?>" method="POST">
                                                                <?php echo csrf_field(); ?>
                                                                <input type="hidden" name="id" value="<?php echo e($request->id); ?>">
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
                                                                                                        data-target="#rejectModal<?php echo e($request->id); ?>">
                                                                                                        Reject
                                                                                                    </button> -->
                                                    <a href="#" data-toggle="modal" data-target="#rejectModal<?php echo e($request->id); ?>"
                                                        title="Reject">
                                                        <i class="fa fa-times-circle text-danger"></i>
                                                    </a>

                                                    <!-- Reject Modal -->
                                                    <div class="modal fade" id="rejectModal<?php echo e($request->id); ?>" tabindex="-1"
                                                        role="dialog">
                                                        <div class="modal-dialog" role="document">
                                                            <form action="<?php echo e(route('request-access.update')); ?>" method="POST">
                                                                <?php echo csrf_field(); ?>
                                                                <input type="hidden" name="id" value="<?php echo e($request->id); ?>">
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
                                                <?php endif; ?>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td class="text-center" colspan="9">No Requests Found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>

                        </table>
                        <div class="d-flex justify-content-center">
                            <?php echo $requests->appends($params)->links(); ?>

                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/request-access/index.blade.php ENDPATH**/ ?>