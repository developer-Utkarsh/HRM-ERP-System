<?php
$user_id = app('request')->input('user_id');
$selected_branch_id = null;

if (!empty($user_id)) {
    $selected_branch_id = DB::table('userbranches')
        ->where('user_id', $user_id)
        ->value('branch_id');
}

$branch_location = app('request')->input('branch_location');
$branches = \App\Branch::where('status', '1');
if (!empty($branch_location)) {
    $branches->where('branch_location', $branch_location);
}
$branches = $branches->orderBy('id', 'desc')->get();											
                ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cleanliness Report Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
		background-image: linear-gradient(to top, #f38800, #f39300, #f29e00, #f1a900, #f0b400, #efbc02, #eec506, #edcd0e, #edd60e, #ecde11,#eab516, #f0841c);
	}
    </style>
</head>

<body>
    <div class="container p-4 mt-3">
        <div class="text-center fw-bold">
            <h2 class="fs-5 mb-4">Daily Cleanliness Form</h2>
        </div>
        <div class=" text-end mb-3">
            <a href="<?php echo e(route('app-view-complaint-report',['user_id' => $user_id])); ?>" class="btn btn-dark" style="font-size:12px;">View Complaint</a>
            <a href="<?php echo e(route('app-view-cleanliness-report',['user_id' => $user_id])); ?>" class="btn btn-dark" style="font-size:12px;">View Report</a>
        </div>
        <form action="<?php echo e(route('cleanliness-report.submit')); ?>" method="POST" class="card p-2" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="mb-3">

                <label for="users-list-status">Branch</label>
             
                <input type="hidden" name="user_id" value="<?php echo e($user_id); ?>">
                <fieldset class="form-group">
                    <select class="form-control branch_id" name="branch_id">
                        <option value="">Select Any</option>
                        <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($value->id); ?>" <?php if(old('branch_id', $selected_branch_id) == $value->id): ?> selected
                            <?php endif; ?>>
                                <?php echo e($value->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </fieldset>
            </div>

            <div class="mb-3">
                <label class="form-label d-block">Shift</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="shift" id="shiftMorning" value="Morning" <?php echo e(old('shift') == 'Morning' ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="shiftMorning">Morning</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="shift" id="shiftAfternoon" value="Afternoon" <?php echo e(old('shift') == 'Afternoon' ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="shiftAfternoon">Afternoon</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="shift" id="shiftEvening" value="Evening" <?php echo e(old('shift') == 'Evening' ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="shiftEvening">Evening</label>
                </div>

                <?php if($errors->has('shift')): ?>
                    <div><span class="text-danger"><?php echo e($errors->first('shift')); ?></span></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="remark" class="form-label">Remark</label>
                <textarea class="form-control" id="remark" name="remark" rows="4"
                    placeholder="Enter remarks here..."></textarea>
                <?php if($errors->has('remark')): ?>
                    <span class="text-danger"><?php echo e($errors->first('remark')); ?> </span>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Upload Image</label>
                <input class="form-control" type="file" id="image" name="image">
                <?php if($errors->has('image')): ?>
                    <span class="text-danger"><?php echo e($errors->first('image')); ?> </span>
                <?php endif; ?>
            </div>
            <div class="text-center">
                <button type="submit" id="submitBtn" class="btn btn-success px-4">Submit Report</button>
            </div>
        </form>
    </div>
    <!-- <?php if(session('success')): ?>
    <script>
        if (performance.navigation.type !== 2) { 
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?php echo e(session('success')); ?>',
                confirmButtonColor: '#28a745'
            });
        }
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
<?php endif; ?> -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    $('form').on('submit', function (e) {
        e.preventDefault();

        let form = $(this);
        let submitBtn = $('#submitBtn');

        submitBtn.prop('disabled', true).text('Processing...');

        $.ajax({
            url: form.attr('action'),
            method: form.attr('method'),
            data: new FormData(this),
            contentType: false,
            processData: false,
            success: function (response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message,
                    confirmButtonColor: '#28a745'
                }).then(() => {
                    location.reload();
                });
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Something went wrong.',
                    confirmButtonColor: '#dc3545'
                });
                submitBtn.prop('disabled', false).text('Submit Report');
            }
        });
    });
});
</script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  

</body>

</html><?php /**PATH /var/www/html/laravel/resources/views/admin/all_reports/cleanliness-report.blade.php ENDPATH**/ ?>