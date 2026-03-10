
<?php $__env->startSection('content'); ?>
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Edit Profile</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?php echo e(route('studiomanager.dashboard')); ?>">Home</a>
                                </li>
                                <li class="breadcrumb-item active"><a href="#">Edit Profile</a>
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
                                <h4 class="card-title">Studio Manager Profile</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form class="form" action="<?php echo e(route('studiomanager.profile.update')); ?>" method="post" enctype="multipart/form-data">
                                        <?php echo csrf_field(); ?>
                                        <div class="form-body">
                                            <div class="row">
                                                <div class="col-md-6 col-12">
                                                    <div class="form-label-group">
                                                        <input type="text" class="form-control" placeholder="Full Name" name="name" value="<?php echo e(Auth::user()->name); ?>">
                                                        <label for="first-name-column">Full Name</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="form-label-group">
                                                        <input type="email" class="form-control" name="email" placeholder="Email" value="<?php echo e(Auth::user()->email); ?>" disabled="disabled">
                                                        <label for="email-id-column">Email</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="form-label-group">
                                                        <input type="text" class="form-control" name="mobile" placeholder="Mobile Number" value="<?php echo e(Auth::user()->mobile); ?>">
                                                        <label for="country-floating">Mobile Number</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="form-label-group">
                                                        <input type="file" class="form-control" name="image">
                                                        <label for="company-column">Profile Image</label>
                                                        <?php if(!empty(Auth::user()->image)): ?>
                                                        <img id="adminimg" src="<?php echo e(asset('laravel/public/profile/' . Auth::user()->image)); ?>" alt="<?php echo e(Auth::user()->name); ?>" height="80" width="80">
                                                        <?php endif; ?>
                                                    </div>
                                                </div>                                               
                                                <div class="col-12">
                                                    <button type="submit" class="btn btn-primary mr-1 mb-1">Update Profile</button>
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

<?php echo $__env->make('layouts.studiomanager', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/studiomanager/updateprofile.blade.php ENDPATH**/ ?>