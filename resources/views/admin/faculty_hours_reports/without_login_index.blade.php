@extends('layouts.without_login_admin')
@section('content')

 <style>
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
						<h2 class="content-header-title float-left mb-0">Faculty Hours Report</h2>
						 
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ route('faculty-hours-reports-new') }}" method="get" name="filtersubmit">
									<div class="row">
										
										<input type="hidden" name="faculty_id" class="faculty_id_get" name="" value="{{ app('request')->input('faculty_id') }}">
										
										
										<div class="col-12 col-sm-6 col-lg-2">											
											<label for="users-list-status">From Date</label>								
											<fieldset class="form-group">																					
												<input type="date" name="fdate" placeholder="Date" value="{{ $selectFromDate }}" class="form-control StartDateClass fdate">	
											</fieldset>	
										</div>
										<div class="col-12 col-sm-6 col-lg-2">											
											<label for="users-list-status">To Date</label>									
											<fieldset class="form-group">																					
												<input type="date" name="tdate" placeholder="Date" value="{{ $selectToDate }}" class="form-control tdate">	
											</fieldset>									
										</div>	
										
										<div class="col-12 col-sm-6 col-lg-8"  >
												<label for="users-list-status">&nbsp;</label>		
										 		<fieldset class="form-group" style="">		
												<button type="submit" class="btn btn-primary">Search</button>
											</fieldset>					
										</div>										
											
									</div>
									
									
								</form>
							</div>
						</div>
					</div>
				</div>
				<!--div><a href="{{ asset('laravel/public/training_pdf/62d8f47ec6dfd.pdf') }}" download>Download</a></div-->
				<div class="table-responsive">
				
				<?php 
				if (count($get_faculty) > 0) {
					foreach ($get_faculty as $get_faculty_value) {
				?>
				<table class="table table-bordered " id="">
					<head>
							<tr style="">
								<td colspan="9"><b>Faculty Name : <?php echo isset($get_faculty_value->name)?$get_faculty_value->name:''; ?></b> 
								</td>
							</tr>
					</head>
					<tbody>
					<tr><td>
             <!--  table-responsive-stack -->
					<table class="table table-bordered table-striped table-condensed " id="tableOne">
					 
						 
						<thead>
							<tr style="">
								<th scope="col">Subject</th>
								<th scope="col">Schedule Time</th>
								<th scope="col">Spent Time</th>
							</tr>
						</thead>
						<tbody>
							@if(count($get_faculty) > 0)
							@php $s_no = 0; @endphp
								@foreach($get_faculty as $key=>$get_faculty_val)
								<?php
								    $f_date = date('Y-m-d'); $t_date = date('Y-m-d');
									if(!empty($selectFromDate)){
										$f_date = $selectFromDate;
									}
									if(!empty($selectToDate)){
										$t_date = $selectToDate;
									}
									$whereCond  = ' 1=1';

									$get_total_time = DB::table('timetables')
													->select('timetables.from_time as start_time','timetables.to_time as end_time','subject.name','start_classes.start_time as start_classes_start_time','start_classes.end_time as start_classes_end_time')
													->leftJoin('subject', 'subject.id', '=', 'timetables.subject_id')
													->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
													->leftJoin('branches', 'branches.id', '=', 'studios.branch_id')
													->leftJoin('start_classes', 'start_classes.timetable_id', '=', 'timetables.id')
													->where('timetables.faculty_id', $get_faculty_val->id)
													->where('timetables.time_table_parent_id', '0')
													->where('timetables.is_deleted', '0');
									 				
										$get_total_time = $get_total_time->whereRaw($whereCond)
													->whereRaw(' timetables.cdate >= "'.$f_date.'" AND timetables.cdate <= "'.$t_date.'"')
													->get();
													
												
							 
									$base_time2          = new DateTime('00:00');
									$base_time          = new DateTime('00:00');
									$total              = new DateTime('00:00');
									$total2              = new DateTime('00:00');
									$subject_arr        = array();
									$schedule_total_tt           = "00 : 00 Hours"; 
									$total_tt           = "00 : 00 Hours"; 
									if(count($get_total_time) > 0){
										foreach($get_total_time as $get_total_time_value){
											array_push($subject_arr, $get_total_time_value->name);
											$first_time = new DateTime($get_total_time_value->start_time);
											$second_time = new DateTime($get_total_time_value->end_time);
											$interval = $first_time->diff($second_time);
											$base_time->add($interval);


											$first_date = new DateTime($get_total_time_value->start_classes_start_time);
											$second_date = new DateTime($get_total_time_value->start_classes_end_time);
											$interval = $first_date->diff($second_date);
											$base_time2->add($interval); 											
										}
										
										$baseDays = $total->diff($base_time)->format("%a");
										$baseHours = $total->diff($base_time)->format("%H");
										$baseMinute = $total->diff($base_time)->format("%I");
										
										$schedule_total_tt = ($baseDays*24)+$baseHours. ":" . $baseMinute;
										
										$totalDays = $total2->diff($base_time2)->format("%a");
										$totalHours = $total2->diff($base_time2)->format("%H");
										$totalMinute = $total2->diff($base_time2)->format("%I");
										
										$total_tt = ($totalDays*24)+$totalHours. ":" . $totalMinute;
									}
									if(!empty($branch_location) ){
										if(count($get_total_time) > 0){
										?>
										<tr>
											<td class="product-category">{{ (count($subject_arr) > 0) ? implode(",", array_unique($subject_arr)) : '--' }}</td>
											<td class="product-category">{{ $schedule_total_tt }} Hours</td>
											<td class="product-category">{{ $total_tt }} Hours</td>
										</tr>
										<?php
										}

									}
									else{	
								?>
								<tr>
									<td class="product-category">{{ (count($subject_arr) > 0) ? implode(",", array_unique($subject_arr)) : '--' }}</td>
									<td class="product-category">{{ $schedule_total_tt }} Hours</td>
									<td class="product-category">{{ $total_tt }} Hours</td>
								</tr>
								<?php
									}
								?>
								@endforeach
							@else
								<tr>
									<td class="text-center" colspan="3">No Record Found</td>
								</tr>								
							@endif	
						</tbody>
					</table>
					</td>
					</tr>
					</tbody>
					</table>
					<hr/>
				<?php 
					}
				}else{
					echo "No found";
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
 
				
@endsection

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<link href="{{ asset('laravel/public/css/jquery.timepicker.css') }}" rel="stylesheet"/>
<script src="{{ asset('laravel/public/js/jquery.timepicker.js') }}"></script>
<script src="{{ asset('laravel/public/admin/js/jquery.validate.min.js') }}"></script>
 <script id="rendered-js">
$(document).ready(function () {


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
    
@endsection
