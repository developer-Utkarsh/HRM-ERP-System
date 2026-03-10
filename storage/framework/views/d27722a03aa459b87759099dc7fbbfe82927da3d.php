<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">    
    <title>Login Page - Admin</title>

   <link href="<?php echo e(url('../laravel/public/logo.png')); ?>" rel="icon" type="image/ico" />
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">
    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('laravel/public/admin/css/vendors.min.css')); ?>">
    <!-- END: Vendor CSS-->
    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('laravel/public/admin/css/bootstrap.css')); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('laravel/public/admin/css/bootstrap-extended.css')); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('laravel/public/admin/css/colors.css')); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('laravel/public/admin/css/components.css')); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('laravel/public/admin/css/authentication.css')); ?>">
	
	<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


</head>
<!-- END: Head-->
<body class="vertical-layout vertical-menu-modern 1-column  navbar-floating footer-static bg-full-screen-image  blank-page blank-page" data-open="click" data-menu="vertical-menu-modern" data-col="1-column">
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <section class="row flexbox-container">
                    <div class="col-xl-8 col-11 d-flex justify-content-center">
                        <div class="card bg-authentication rounded-0 mb-0">
                            <div class="row m-0">
                                <div class="col-lg-6 d-lg-block d-none text-center align-self-center px-1 py-0">
                                    <img src="<?php echo e(asset('laravel/public/admin/images/login.png')); ?>" alt="branding logo">
                                </div>
                                <div class="col-lg-6 col-12 p-0">
                                    <div class="card rounded-0 mb-0 px-2">
                                        <div class="card-header pb-1">
                                            <div class="card-title">
                                                <h4 class="mb-0">Login</h4>
                                            </div>
                                        </div>
										<?php
										$remember_me = "";
										$email = "";
										$password = "";
										if(!empty($_COOKIE['remember_me'])) {
											if($_COOKIE['remember_me']=="yes"){
												$email = $_COOKIE['email'];
												$password = $_COOKIE['password'];
												$remember_me = "checked";
											}
										}
										?>
                                        <p class="px-2">Welcome back, please login to your account.</p>
                                        <div class="card-content">
                                            <div class="card-body pt-1">
                                                <form action="<?php echo e(route('login')); ?>" method="post">
                                                    <?php echo csrf_field(); ?>
                                                    <fieldset class="form-label-group form-group position-relative has-icon-left">
                                                        <input type="text" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="email" placeholder="Email or Mobile" name="email" value="<?php echo e($email); ?>">
                                                        <div class="form-control-position">
                                                            <i class="feather icon-user"></i>
                                                        </div>
                                                        <label for="user-name">Email</label>
                                                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong><?php echo e($message); ?></strong>
                                                        </span>
                                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                                    </fieldset>

                                                    <fieldset class="form-label-group position-relative has-icon-left">
                                                        <input type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="password" placeholder="Password" value="<?php echo e($password); ?>">
                                                        <div class="form-control-position">
                                                            <i class="feather icon-lock"></i>
                                                        </div>
                                                        <label for="user-password">Password</label>
                                                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong><?php echo e($message); ?></strong>
                                                        </span>
                                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                                    </fieldset>
													
                                                    <div class="form-group d-flex justify-content-between align-items-center">
                                                        <div class="text-left">
                                                            <fieldset class="checkbox">
                                                                <div class="vs-checkbox-con vs-checkbox-primary">
                                                                    <input type="checkbox" name="remember" <?php echo e(old('remember') ? 'checked' : $remember_me); ?>>
                                                                    <span class="vs-checkbox">
                                                                        <span class="vs-checkbox--check">
                                                                            <i class="vs-icon feather icon-check"></i>
                                                                        </span>
                                                                    </span>
                                                                    <span class="">Remember me</span>
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        
                                                        <button type="button" class="text-right sts-data" data-toggle="modal" data-target="#forgetPasswordModal">Forgot Password ?</button>
                                                        

                                                    </div>                                                    
                                                    <button type="submit" class="btn btn-primary float-right btn-inline">Login</button>
                                                </form>
                                            </div>
                                        </div>


                                        <div class="login-footer">
                                            <div class="divider">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <?php echo $__env->make('layouts.notification', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>    
</body>
</html>


<!-- Modal -->
<div class="modal fade" id="forgetPasswordModal" tabindex="-1" role="dialog" aria-labelledby="forgetPasswordModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="forgetPasswordModalLabel">Forgot Password</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin:0px;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
       
        <div class="modal-body">
            <div class="frg_detail" style="display:block;">
                <div class="form-group">
                 <label>Mobile Number</label>
                 <input type="number" class="form-control" name="mobile" placeholder="Enter Mobile Number">
                </div>

                <div class="form-group">
                 <label>Employee Code</label>
                 <input type="number" class="form-control" name="emp_code" placeholder="Enter Employee Code">
                </div>
                <button type="button" class="btn btn-primary float-right check_user" >Next</button>
            </div>

            <div class="frg_otp" style="display:none;">
                <div class="form-group">
                 <label>OTP</label>
                 <input type="number" class="form-control" name="frg_otp" placeholder="Enter Otp">
                </div>
                <button type="button" class="btn btn-primary float-right check_otp" >Next</button>
            </div>

            <div class="frg_rpassword" style="display:none;">				
				<div class="col-md-12 col-12">
					<div class="form-label-group position-relative">
						<input type="text" class="form-control" value="" id="frg_password" name="frg_password" placeholder="New Password" required autocomplete="off">
						<label for="frg_password">New Password</label>													
						<span onclick="togglePassword('frg_password')" style="position:absolute; top:50%; right:10px; transform:translateY(-50%); cursor:pointer;">👁️</span>
					</div>
				</div>

				<div class="col-md-12 col-12">
					<div class="form-label-group position-relative">
						<input type="text" class="form-control" value="" id="frg_cpassword" name="frg_cpassword" placeholder="Confirm Password" required autocomplete="off">
						<label for="frg_cpassword">Confirm Password</label>
						<span onclick="togglePassword('frg_cpassword')" style="position:absolute; top:50%; right:10px; transform:translateY(-50%); cursor:pointer;">👁️</span>
					</div>
				</div>
				<div class="col-md-12 col-12 text-right">
					<div class="form-label-group"> 
						<button onclick="generatePassword()" type="button" class="btn-success">Generate Password</button>
					</div>
				</div>	
                <button type="button" class="btn btn-primary float-right check_restpassword" >Next</button>
            </div>


        </div>

        <div class="modal-footer"></div>
    </div>
  </div>
</div>

 <!-- BEGIN: Vendor JS-->
<!--script src="http://15.207.232.85/laravel/public/admin/js/vendors.min.js"></script-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/alchemyjs/0.4.0/scripts/vendor.js"></script>
<!-- BEGIN Vendor JS-->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>


<script type="text/javascript">

    $(document).ready(function() {
        $(".check_user").on("click", function() { 
            var mobile=$("input[name='mobile']").val();
            var emp_code=$("input[name='emp_code']").val();
            if(mobile!="" && emp_code!=""){
                $.ajax({
                    type : 'POST',
                    url : '<?php echo e(route('checkUser')); ?>',
                    data : {'_token' : '<?php echo e(csrf_token()); ?>','query_check':'checkuser','mobile': mobile,'emp_code':emp_code},
                    dataType : 'html',
                    success : function (response){
                      var obj=$.parseJSON(response);
                      if(obj.status){
                        $(".frg_detail").hide();
                        $(".frg_otp").show();
                      }else{
                        alert(obj.message);
                      }   
                    }
                });
            }else{
                alert('Enter Mobile or Emp Code Both');
            } 
        });

        $(".check_otp").on("click", function() { 
            var mobile=$("input[name='mobile']").val();
            var emp_code=$("input[name='emp_code']").val();
            var frg_otp=$("input[name='frg_otp']").val();
            if(mobile!="" && emp_code!="" && frg_otp!=""){
                $.ajax({
                    type : 'POST',
                    url : '<?php echo e(route('checkUser')); ?>',
                    data : {'_token' : '<?php echo e(csrf_token()); ?>','query_check':'checkotp','mobile': mobile,'emp_code':emp_code,'frg_otp':frg_otp},
                    dataType : 'html',
                    success : function (response){
                      var obj=$.parseJSON(response);
                      if(obj.status){
                        $(".frg_detail").hide();
                        $(".frg_otp").hide();
                        $(".frg_rpassword").show();
                      }else{
                        alert(obj.message);
                      }  
                    }
                });
            }else{
                alert('Enter Mobile or Emp Code Both');
            } 
        });

        $(".check_restpassword").on("click", function() { 
            var mobile=$("input[name='mobile']").val();
            var emp_code=$("input[name='emp_code']").val();
            var frg_otp=$("input[name='frg_otp']").val();
            var frg_password=$("input[name='frg_password']").val();
            var frg_cpassword=$("input[name='frg_cpassword']").val();
            if(mobile!="" && emp_code!="" && frg_otp!=""){
                if(frg_cpassword!="" && frg_password==frg_cpassword){
                    $.ajax({
                        type : 'POST',
                        url : '<?php echo e(route('checkUser')); ?>',
                        data : {'_token' : '<?php echo e(csrf_token()); ?>','query_check':'resetpassword','mobile': mobile,'emp_code':emp_code,'frg_otp':frg_otp,'password':frg_password},
                        dataType : 'html',
                        success : function (response){
                          var obj=$.parseJSON(response);
                          if(obj.status){
                            $(".frg_detail").show();
                            $(".frg_otp").hide();
                            $(".frg_rpassword").hide();
                            
                            $("input[name='mobile']").val('');
                            $("input[name='emp_code']").val('');
                            $("input[name='frg_otp']").val('');
                            $("input[name='frg_password']").val('');
                            $("input[name='frg_cpassword']").val('');

                            $("#forgetPasswordModal").modal('toggle');
                            //$('#forgetPasswordModal').modal({show: false});
                            alert(obj.message);
                          }else{
                            alert(obj.message);
                          }   
                        }
                    });
                }else{
                 alert('Password Not Match');
                }
            }else{
                alert('Enter Mobile or Emp Code Both');
            } 
        });

    });
</script>

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
		$('#frg_password').val(shuffledPassword);
		$('#frg_cpassword').val(shuffledPassword);
		alertShown.frg_password = false;
		alertShown.frg_cpassword = false;
		validatePasswords();
	}
  
	function validatePasswordStructure(password) {
		const lengthOK = password.length >= 9;
		const hasLetter = /[A-Za-z]/.test(password);
		const hasNumber = /\d/.test(password);
		const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
		return lengthOK && hasLetter && hasNumber && hasSpecial;
	}

	// Track if alert has already been shown per field
	let alertShown = {
		frg_password: false,
		frg_cpassword: false,
		mismatch:false
	};

	function validatePasswords() {
		const frg_password = $('#frg_password').val();
		const frg_cpassword = $('#frg_cpassword').val();
		let valid = true;
		
		//alert(frg_password);
		// Validate new password
		if(frg_password!=''){
			if (!validatePasswordStructure(frg_password)) {
			  if (!alertShown.frg_password) {
				alert("New Password must be minimum 9 characters and include at least one letter, one number, and one special character.");
				alertShown.frg_password = true;
			  }
			  valid = false;
			}
		}

		// Validate re-typed password
		if(frg_cpassword!=''){
			if (!validatePasswordStructure(frg_cpassword)) {
			  if (!alertShown.frg_cpassword) {
				alert("Confirm Password must be minimum 9 characters and include at least one letter, one number, and one special character.");
				alertShown.frg_cpassword = true;
			  }
			  valid = false;
			}
		}

    
		// Check if passwords match
		if(frg_password!='' && frg_cpassword!=''){
			if (frg_password !== frg_cpassword) {
				if (!alertShown.mismatch) {
				  alert("Passwords do not match.");
				  alertShown.mismatch = true;
				}
				valid = false;
			} else {
				alertShown.mismatch = false; // reset flag if they now match
			}
		}

		$('.check_restpassword').prop('disabled', !valid);
	}

	$(document).ready(function () {			
		$('.check_restpassword').prop('disabled', true);
		
		$('#frg_password, #frg_cpassword').on('change', function () {
		  const id = $(this).attr('id');
		  alertShown[id] = false; 
		  validatePasswords();
		});
	});
</script>
<?php /**PATH /var/www/html/laravel/resources/views/auth/login.blade.php ENDPATH**/ ?>