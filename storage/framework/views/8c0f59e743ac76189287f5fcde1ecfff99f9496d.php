
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
                                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
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
                                    <form class="form" action="<?php echo e(route('admin.password.update')); ?>" method="post" enctype="multipart/form-data">
                                        <?php echo csrf_field(); ?>
                                        <div class="form-body">
                                            <div class="row">
                                                <div class="col-md-12 col-12">
                                                    <div class="form-label-group">
														<input type="password" class="form-control" name="cpass" placeholder="Current Password" required>
														<label for="first-name-column">Current Password*</label>
													</div>
												</div>
												<div class="col-md-12 col-12">
													<div class="form-label-group position-relative">
														<input type="password" class="form-control" id="newpass" name="newpass" placeholder="New Password" required>
														<label for="newpass">New Password</label>													
														<span onclick="togglePassword('newpass')" style="position:absolute; top:50%; right:10px; transform:translateY(-50%); cursor:pointer;">👁️</span>
													</div>
												</div>

												<div class="col-md-12 col-12">
													<div class="form-label-group position-relative">
														<input type="password" class="form-control" id="renewpass" name="renewpass" placeholder="Re-Type New Password" required>
														<label for="renewpass">Re-Type New Password</label>
														<span onclick="togglePassword('renewpass')" style="position:absolute; top:50%; right:10px; transform:translateY(-50%); cursor:pointer;">👁️</span>
													</div>
												</div>
												<div class="col-md-12 col-12 text-right">
													<div class="form-label-group"> 
														<button onclick="generatePassword()" type="button" class="btn-success">Generate Password</button>
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
<script>
  function togglePassword(id) {
    const field = document.getElementById(id);
    field.type = field.type === "password" ? "text" : "password";
  }

  function generatePassword() {
    const letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    const numbers = "0123456789";
    const specials = "!@#$%^&*()_+-=[]{}|;:,.<>?";
    const allChars = letters + numbers + specials;

    const passwordChars = [
      letters[Math.floor(Math.random() * letters.length)],
      numbers[Math.floor(Math.random() * numbers.length)],
      specials[Math.floor(Math.random() * specials.length)]
    ];

    for (let i = 3; i < 9; i++) {
      passwordChars.push(allChars[Math.floor(Math.random() * allChars.length)]);
    }

    const shuffledPassword = passwordChars.sort(() => Math.random() - 0.5).join('');
    $('#newpass').val(shuffledPassword);
    $('#renewpass').val(shuffledPassword);
    alertShown.newpass = false;
    alertShown.renewpass = false;
    validatePasswords();
  }

  function validatePasswordStructure(password) {
    const lengthOK = password.length >= 9;
    const hasLetter = /[A-Za-z]/.test(password);
    const hasNumber = /\d/.test(password);
    const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
    return lengthOK && hasLetter && hasNumber && hasSpecial;
  }

 
  let alertShown = {
    newpass: false,
    renewpass: false
  };

	function validatePasswords() {
	  const newpass = $('#newpass').val();
	  const renewpass = $('#renewpass').val();
	  let valid = true;

	  // Validate new password
	  if (!validatePasswordStructure(newpass)) {
		if (!alertShown.newpass) {
		  alert("New Password must be minimum 9 characters and include at least one letter, one number, and one special character.");
		  alertShown.newpass = true;
		}
		valid = false;
	  }

		// Validate re-typed password
		if(renewpass!=''){
		  if (!validatePasswordStructure(renewpass)) {
			if (!alertShown.renewpass) {
			  alert("Re-Type Password must be minimum 9 characters and include at least one letter, one number, and one special character.");
			  alertShown.renewpass = true;
			}
			valid = false;
		  }
		}

	  // Check if passwords match
		if(newpass!='' && renewpass!=''){
			if (newpass !== renewpass) {
				if (!alertShown.mismatch) {
					alert("Passwords do not match.");
					alertShown.mismatch = true;
				}
				valid = false;
			} else {
				alertShown.mismatch = false; // reset flag if they now match
			}
		}

	  $('button[type="submit"]').prop('disabled', !valid);
	}


  $(document).ready(function () {
    // Initially disable submit
    $('button[type="submit"]').prop('disabled', true);

    // On input change: reset alert flag and revalidate
    $('#newpass, #renewpass').on('change', function () {
      const id = $(this).attr('id');
      alertShown[id] = false; // allow showing alert again on next invalid attempt
      validatePasswords();
    });
  });
</script>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/updatepassword.blade.php ENDPATH**/ ?>