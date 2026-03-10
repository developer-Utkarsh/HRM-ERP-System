
<?php $__env->startSection('content'); ?>

 <style>
	body {
		font-size: 13px;
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
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12 text-center">
						<h3 class="content-header-title mb-0">Employees Message To CEO</h3>

                <span class="float-right"><a href="<?php echo e(route('acge')); ?>?export=yes" target="_blank">Export</a></span>

					</div>
				</div>
			</div>
		</div>
		<div class="accordion" id="accordionExample">
			<?php
				$i = 1; 
				foreach($query as $q){ 
			?>
			
			<div class="card" id="result<?=$i;?>">
				<?php if($q->cread==1){ $bgNew = '448e44'; }else{ $bgNew = '000'; } ?>
				<div class="card-header py-1" id="headingOne" style="background-color:#<?=$bgNew;?>;">
				  <h5 class="mb-0 w-100">
						<div class="px-0 text-left w-100 get_edit_data" type="button" style="color:#fff;" data-toggle="collapse" data-target="#collapse<?=$i;?>" data-id="<?php echo e($q->id); ?>" aria-expanded="true" aria-controls="collapseOne">
							<b>By :  <?=$q->uname;?> &nbsp;( Department: <?=$q->dname;?>,- Branch: <?=$q->branch;?> - Department Head:<?=$q->head_name;?> - <?php echo date('d-m-Y h:i:s', strtotime($q->created_at));?> )</b>

							<span class="copy_heading d-none"><b>By :  <?=$q->uname;?>-<?=$q->mobile;?> &nbsp;( Department: <?=$q->dname;?>,- Branch: <?=$q->branch;?> - Department Head:<?=$q->head_name;?> - <?php echo date('d-m-Y h:i:s', strtotime($q->created_at));?> )</b></span>
						</div>
				  </h5>
				</div>

				<?php if($q->cread==1){ $nClass = 'eff5ff'; }else{ $nClass = 'fff'; } ?>
				<div id="collapse<?=$i;?>" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample" style="background:#<?=$nClass;?>">
					<div class="card-body border-top pt-2">
						<div class="row d-none">
							<div class="float-left col-12 text-dark" style="font-size:16px;font-weight:bold;"><?=$q->message;?></div>
						</div>

						<div class="row childMsg<?=$q->id;?>">Child Massage will display here</div>

				      <p class="copy_content<?=$q->id;?> d-none"></p>
				      <span class="float-right btn btn-outline-dark text-dark px-1 copy_content" data-id="<?=$q->id;?>">Copy & Share</span>


						<form action="<?php echo e(route('employee-complaint-reply')); ?>" method="post">
							<?php echo csrf_field(); ?>
							<input type="hidden" name="ceo_id" class="inputCeoId<?=$q->id;?>" value="<?=$q->id;?>"/>
							<div class=" my-2">
								   <textarea class="form-control text-dark" name="ceo_reply" rows="4" placeholder="Reply.." style="font-size:16px;font-weight:500;"></textarea></br>
								<div class="text-right">

									<button type="submit" class="btn btn-outline-secondary text-dark px-1" type="button">Send</button>
									
									<a href="tel:+91<?=$q->mobile;?>"><button type="button" class="btn btn-primary px-1" style="background-color:#f5bb0c !important;color:#000;font-weight:bold;">Call</button>
									</a>
									<?php $msg=urlencode('नमस्कार, आपका मैसेज मुझे उत्कर्ष एम्पलोई एप पर प्राप्त हुआ है। यह मेरा व्यक्तिगत वाट्सअप नम्बर है। मुझे आप वाट्सअप पर व्यक्तिगत मैसेज भी कर देवें। जल्दी ही मैं आपसे सम्पर्क करूंगा।  - निर्मल गहलोत'); ?>
									<a href="https://api.whatsapp.com/send?sendoutappwebview=yes&phone=+91<?=$q->mobile;?>&text=<?=$msg?>"><button type="button" class="btn btn-primary px-1" style="background-color:#25D366 !important;color:#FFF;font-weight:bold;">Whatsapp</button>
									</a>
									
									<?php if($q->cread==1){ ?> 
									
										<button type="button" class="btn btn-danger px-1" style="color:#fff;font-weight:500;" onClick="verifyDelete('<?=$q->id;?>','<?=$i?>')">Delete</button>
									
									<?php } ?>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			
			<?php $i++; } ?>
		</div>
		
			
	</div>
</div>
 <div id="loading"></div>
	
<style>
#loading {
    background: url("<?php echo e(asset('/laravel/public/images/loading-gif.gif')); ?>") no-repeat center center;
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: 100%;
    z-index: 9999999;
}
</style>	
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
function verifyDelete(id, fid){
	var answer = window.confirm("Are you sure you want to delete ?");
	if (answer) {
		//window.location.href = "employee-complaint-delete/"+id;
		$('#result'+fid).hide();
		$.ajax({
			type : 'GET',
			url : "employee-complaint-delete/"+id,
			// data : {'_token' : '<?php echo e(csrf_token()); ?>', 'id': id},
			dataType : 'json',
			success : function (data){
				if(data.status == true){		
					$('#result'+fid).hide();
					
					qw = parseInt(fid) + 1;
					$('#result'+qw).fadeIn();
				}
			}
		});		
	}
}



$('#loading').hide();
$(document).on("click",".clickonloader",function(){
	$('#loading').show();
});


$(".get_edit_data").on("click", function() {
   var heading=$(this).children(".copy_heading").text();  

	var request_id = $(this).attr("data-id"); 
				
	$.ajax({
		type : 'POST',
		url : '<?php echo e(route('complaint-read')); ?>',
		data : {'_token' : '<?php echo e(csrf_token()); ?>', 'request_id': request_id},
		dataType : 'json',
		success : function (data){
			if(data.status == true){		
				$('#headingOne').css("background", "#448e44");
            
            var childLastId=request_id;
            var html="";
				$.each(data.data, function (key, val) {
					childLastId=val['id'];
		         html+='<div class="float-left col-12 text-dark" style="font-size:16px;font-weight:bold;">'+val['message']+'</div>';
		         if(val['reply']!=null){
                 html+='<div class="float-left col-12 text-pink" style="font-size:14px;">Reply :- '+val['reply']+'</div>';
               }
			   });

			   $(".childMsg"+request_id).html(html);

			   $(".copy_content"+request_id).html(heading+"<br>"+html);

		      $(".inputCeoId"+request_id).val(childLastId);			   
				
			}
		}
	});		
}); 

$(".copy_content").on("click", function() {
	var request_id = $(this).attr("data-id");
	 // var data=$(".copy_content"+request_id).text();
	var data=$(".copy_content"+request_id).text();
	
	data = data.replace('<br>','\n');
	  
	 /*data = data.replace(/\s+/g,'').trim();
	 data = data.replace('<b>','*');
	 data = data.replace('</b>','*');
	 data = data.replace('</div>','\n');*/

	 navigator.clipboard.writeText(data);
	 //alert("Copied the text: " +data);
	 $(this).text("Copied");
});

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.without_login_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/all_reports/ceo-complaint.blade.php ENDPATH**/ ?>