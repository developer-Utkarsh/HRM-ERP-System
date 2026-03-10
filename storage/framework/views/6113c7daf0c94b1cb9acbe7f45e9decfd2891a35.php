
<?php $__env->startSection('content'); ?>
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Change Password</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?php echo e(route('studiomanager.dashboard')); ?>">Home</a>
                                </li>
                                <li class="breadcrumb-item active"><a href="#">Change Password</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
            </div>
        </div>
        <div class="content-body">
            <section id="multiple-column-form">
                <div class="row match-height">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Change Password</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form class="form" action="<?php echo e(route('studiomanager.password.update')); ?>" method="post" enctype="multipart/form-data">
                                        <?php echo csrf_field(); ?>
                                        <div class="form-body">
                                            <div class="row">
                                                <div class="col-md-6 col-12">
                                                    <div class="form-label-group">
                                                     <input type="password" class="form-control" name="cpass" placeholder="Current Password" required>
                                                     <label for="first-name-column">Current Password*</label>
                                                 </div>
                                             </div>
                                             <div class="col-md-6 col-12">
                                                <div class="form-label-group">                     
                                                    <input type="password" class="form-control" name="newpass" placeholder="New Password" required>
                                                    <label for="email-id-column">New Password</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-label-group">                      
                                                    <input type="password" class="form-control" name="renewpass" placeholder="Re-Type New Password" required>
                                                    <label for="country-floating">Re-Type New Password</label>
                                                </div>
                                            </div>          
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary mr-1 mb-1">Change Password</button>
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
        <!-- // Basic Floating Label Form section end -->
    </div>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.studiomanager', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/studiomanager/updatepassword.blade.php ENDPATH**/ ?>