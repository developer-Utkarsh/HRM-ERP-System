
<?php $__env->startSection('content'); ?>

 <style>
	body {
		background-image: url(/laravel/public/Gradient-BG.png);
		background-size: 100% 100%;
		background-repeat: no-repeat;
		background-attachment: fixed;
	}
	
    .table-responsive-stack tr {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-orient: horizontal;
  -webkit-box-direction: normal;
      -ms-flex-direction: row;
          flex-direction: row;
}


.table-responsive-stack td,
.table-responsive-stack th {
   display:block;
/*      
   flex-grow | flex-shrink | flex-basis   */
   -ms-flex: 1 1 auto;
    flex: 1 1 auto;
}

.table-responsive-stack .table-responsive-stack-thead {
   font-weight: bold;
}

@media  screen and (max-width: 770px) {
   .table-responsive-stack tr {
      -webkit-box-orient: vertical;
      -webkit-box-direction: normal;
          -ms-flex-direction: column;
              flex-direction: column;
      border-bottom: 3px solid #ccc;
      display:block;
      
   }
   /*  IE9 FIX   */
   .table-responsive-stack td {
      float: left\9;
      width:100%;
   }
}
.table tbody + tbody {
    border-top: 2px solid #ccc;
}

</style>
	
<div class="app-content content" style="margin: 0px !important;">
	<div class="content-wrapper" style="margin-top: 0px !important;">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Leave Details</h2>
						 
					</div>
				</div>
			</div>
		</div>
		
		<div class="content-body">
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				 
				<div class="table-responsive">
				
				<?php 
				if (!empty($user_id)) {
				?>
             <!--  table-responsive-stack -->
					<table class="table table-bordered table-striped table-condensed " id="tableOne">
					 
						 
						<thead>
							<tr style="">
								<th scope="col"></th>
								<th scope="col"></th>
								<th scope="col"></th>
								<th scope="col" colspan="3">Earned</th>
								<th scope="col" colspan="3">Taken</th>
								<th scope="col" colspan="3">Balance</th>
							</tr>
							<tr style="">
								<th scope="col">Code</th>
								<th scope="col">Emp Name</th>
								<th scope="col">Month</th>
								<th scope="col">CO</th>
								<th scope="col">PL</th>
								<th scope="col">CL</th>
								<th scope="col">CO</th>
								<th scope="col">PL</th>
								<th scope="col">CL</th>
								<th scope="col">CO</th>
								<th scope="col">PL</th>
								<th scope="col">CL</th>
							</tr>
						</thead>
						<tbody>
							<?php
							if(!empty($user_leave_manual)){
								$toal_co = 0;
								$toal_pl = 0;
								$toal_cl = 0;

								$toal_balance_co = 0;
								$toal_balance_pl = 0;
								$toal_balance_cl = 0;

								foreach($user_leave_manual as $val){
									$toal_co += $val->earn_co;
								  $toal_pl += $val->earn_pl;
								  $toal_cl += $val->earn_cl;

									$toal_balance_co += $val->taken_co;
									$toal_balance_pl += $val->taken_pl;
									$toal_balance_cl += $val->taken_cl;
									?>
									<tr>
										<td><?=$val->emp_code?></td>
										<td><?=$val->name?></td>
										<td><?=date('M-Y',strtotime($val->date))?></td>
										<td style="background-color: #99a592;"><?=$val->earn_co?></td>
										<td style="background-color: #99a592;"><?=$val->earn_pl?></td>
										<td style="background-color: #99a592;"><?=$val->earn_cl?></td>
										<td style="background-color: #d7e8cd;"><?=$val->taken_co?></td>
										<td style="background-color: #d7e8cd;"><?=$val->taken_pl?></td>
										<td style="background-color: #d7e8cd;"><?=$val->taken_cl?></td>
										<td style="background-color: #99dd71;"><?=$val->balance_co?></td>
										<td style="background-color: #99dd71;"><?=$val->balance_pl?></td>
										<td style="background-color: #99dd71;"><?=$val->balance_cl?></td>
									</tr>
									<?php
								}
								
							}
							?>
							
							 	
						</tbody>
						</tfoot>
							<tr style="">
								<th scope="col" colspan="3" style="background-color: #e15656;"><strong>Total</strong></th>
								<th scope="col" colspan="" style="background-color: #e15656;"><strong><?=$toal_co?></strong></th>
								<th scope="col" colspan="" style="background-color: #e15656;"><strong><?=$toal_pl?></strong></th>
								<th scope="col" colspan="" style="background-color: #e15656;"><strong><?=$toal_cl?></strong></th>
								
								<th scope="col" colspan="" style="background-color: #e15656;"><strong><?=$toal_balance_co?></strong></th>
								<th scope="col" colspan="" style="background-color: #e15656;"><strong><?=$toal_balance_pl?></strong></th>
								<th scope="col" colspan="" style="background-color: #e15656;"><strong><?=$toal_balance_cl?></strong></th>

								<th scope="col" colspan="" style="background-color: #e15656;"><strong><?=$toal_co-$toal_balance_co;?></strong></th>
								<th scope="col" colspan="" style="background-color: #e15656;"><strong><?=$toal_pl-$toal_balance_pl;?></strong></th>
								<th scope="col" colspan="" style="background-color: #e15656;"><strong><?=$toal_cl-$toal_balance_cl;?></strong></th>
								
								<?php /* ?><th scope="col" colspan="2" style="background-color: #e15656;"><strong>Balance</strong></th>
								<th scope="col" colspan="" style="background-color: #e15656;"><strong><?=$toal_pl+$toal_co+$toal_cl-$toal_balance_co-$toal_balance_pl-$toal_balance_cl;?></strong></th>
								<?php */ ?>
							</tr>
						</tfoot>
					</table>
					  
				<?php 
				}else{
					echo "No timetable found";
				}
				?>
		<style>
		 hr{background:#000;}
		</style>
					 
				</div>       
				
			</section>
		</div>
	</div>
</div>
 
				
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<link href="<?php echo e(asset('laravel/public/css/jquery.timepicker.css')); ?>" rel="stylesheet"/>
<script src="<?php echo e(asset('laravel/public/js/jquery.timepicker.js')); ?>"></script>
<script src="<?php echo e(asset('laravel/public/admin/js/jquery.validate.min.js')); ?>"></script>
 <script id="rendered-js">
$(document).ready(function () {

	$('.select-multiple2').select2({
		placeholder: "Select Any",
		allowClear: true
	});
  // inspired by http://jsfiddle.net/arunpjohny/564Lxosz/1/
  $('.table-responsive-stack').each(function (i) {
    var id = $(this).attr('id');
    //alert(id);
    $(this).find("th").each(function (i) {
      $('#' + id + ' td:nth-child(' + (i + 1) + ')').prepend('<span class="table-responsive-stack-thead">' + $(this).text() + ':</span> ');
      $('.table-responsive-stack-thead').hide();

    });



  });





  $('.table-responsive-stack').each(function () {
    var thCount = $(this).find("th").length;
    var rowGrow = 100 / thCount + '%';
    //console.log(rowGrow);
    $(this).find("th, td").css('flex-basis', rowGrow);
  });




  function flexTable() {
    if ($(window).width() < 770) {

      $(".table-responsive-stack").each(function (i) {
        $(this).find(".table-responsive-stack-thead").show();
        $(this).find('thead').hide();
      });


      // window is less than 768px   
    } else {


      $(".table-responsive-stack").each(function (i) {
        $(this).find(".table-responsive-stack-thead").hide();
        $(this).find('thead').show();
      });



    }
    // flextable   
  }

  flexTable();

  window.onresize = function (event) {
    flexTable();
  };






  // document ready  
});
//# sourceURL=pen.js
    </script>	
    
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.without_login_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/webview_reports/employee_leave_detail.blade.php ENDPATH**/ ?>