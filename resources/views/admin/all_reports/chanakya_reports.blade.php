@extends('layouts.without_login_admin')
@section('content')

 <style>
	body {
		background-image: linear-gradient(to top, #f38800, #f39300, #f29e00, #f1a900, #f0b400, #efbc02, #eec506, #edcd0e, #edd60e, #ecde11, #eae716, #e8f01c);
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

@media screen and (max-width: 770px) {
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
						<h2 class="content-header-title float-left mb-0">All Reports</h2>
						 
					</div>
				</div>
			</div>
		</div>
		
		<div class="content-body">
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				<div class="row">
				<div class="col-md-12 btn-dark text-center text-light" style="margin-top: 15px;border-radius: 10px;" >		
					<a href="{{ asset('laravel/public/chanakya-employee-management.pdf')}}" class="btn clickonloader" style="color:#fff !important;width:100%;"><strong>चाणक्य की भूमिका </strong></a> 
				</div>
				
				<?php 
				
				if(count($mentor_batch_list) > 0){
					foreach($mentor_batch_list as $val){
					?>
					
					<div class="col-md-12 btn-dark text-center text-light" style="margin-top: 15px;border-radius: 10px;" >		
						<a href="{{route('mentor-report-batch-detail')}}?batch_id=<?=$val['batch_id']?>&&mentor_id=<?=$val['mentor_id']?>" class="btn clickonloader" style="color:#fff !important;width:100%;">Batch Name -  <strong><?=$val['batch_name']?></strong></a> 
					</div>
				
				<?php }
				}
				
				?>
				</div>	
				    
				
			</section>
		</div>
	</div>
</div>
 <div id="loading"></div>
	
<style>
#loading {
    background: url("{{asset('/laravel/public/images/loading-gif.gif')}}") no-repeat center center;
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: 100%;
    z-index: 9999999;
}
</style>	
@endsection

@section('scripts')
<script>
$('#loading').hide();
$(document).on("click",".clickonloader",function(){
	$('#loading').show();
});

</script>
@endsection
