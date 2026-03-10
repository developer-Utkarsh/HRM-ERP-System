@extends('layouts.without_login_admin')
@section('content')

<link href="http://15.207.232.85/laravel/public/css/bootstrap.min.css" rel="stylesheet" type="text/css">
<link href="http://15.207.232.85/laravel/public/css/bootstrap.min(1).css" rel="stylesheet" type="text/css">
<link href="http://15.207.232.85/laravel/public/css/font-awesome.min.css" rel="stylesheet" type="text/css">

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
<style>
    .panel-title > a:before {
    float: left !important;
    font-family: FontAwesome;
    content: "\f068";
    padding-right: 5px;
}

.panel-title > a.collapsed:before {
    float: left !important;
    content: "\f067";
}

.panel-title > a:hover,
.panel-title > a:active,
.panel-title > a:focus {
    text-decoration: none !important;
}
.panel-title {
    color: white !important;
}
@-webkit-keyframes glowing {
    0% {
        background-color: #fcb401;
        -webkit-box-shadow: 0 0 3px #fcb401
    }

    50% {
        background-color: #fcb401;
        -webkit-box-shadow: 0 0 40px #fcb401
    }

    100% {
        background-color: #fcb401;
        -webkit-box-shadow: 0 0 3px #fcb401
    }
}

@-moz-keyframes glowing {
    0% {
        background-color: #fcb401;
        -moz-box-shadow: 0 0 3px #fcb401
    }

    50% {
        background-color: #fcb401;
        -moz-box-shadow: 0 0 40px #fcb401
    }

    100% {
        background-color: #fcb401;
        -moz-box-shadow: 0 0 3px #fcb401
    }
}

@-o-keyframes glowing {
    0% {
        background-color: #fcb401;
        box-shadow: 0 0 3px #fcb401
    }

    50% {
        background-color: #fcb401;
        box-shadow: 0 0 40px #fcb401
    }

    100% {
        background-color: #fcb401;
        box-shadow: 0 0 3px #fcb401
    }
}

@keyframes glowing {
    0% {
        background-color: #fcb401;
        box-shadow: 0 0 3px #fcb401
    }

    50% {
        background-color: #fcb401;
        box-shadow: 0 0 40px #fcb401
    }

    100% {
        background-color: #fcb401;
        box-shadow: 0 0 3px #fcb401
    }
}

    </style>

<!-- Accordion CSS & JS Strat-->
    
<!--
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
-->
    <!-- Accordion CSS & JS End-->
    
    <style>
	/* Accordion CSS style */
/*
    .accordion-button::after {
      background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='%23333' xmlns='http://www.w3.org/2000/svg'%3e%3cpath fill-rule='evenodd' d='M8 0a1 1 0 0 1 1 1v6h6a1 1 0 1 1 0 2H9v6a1 1 0 1 1-2 0V9H1a1 1 0 0 1 0-2h6V1a1 1 0 0 1 1-1z' clip-rule='evenodd'/%3e%3c/svg%3e");
      transform: scale(.7) !important;
    }
    .accordion-button:not(.collapsed)::after {
      background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='%23333' xmlns='http://www.w3.org/2000/svg'%3e%3cpath fill-rule='evenodd' d='M0 8a1 1 0 0 1 1-1h14a1 1 0 1 1 0 2H1a1 1 0 0 1-1-1z' clip-rule='evenodd'/%3e%3c/svg%3e");
    }
        .accordion-button{ font-size: 1.75rem;}
*/
        /* Accordion CSS style */
</style>

<div class="app-content content" style="margin: 0px !important;">
	<div class="content-wrapper" style="margin-top: 0px !important;">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Studio Report</h2>
						
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
								<form action="{{ route('studio-reports') }}" method="get" name="filtersubmit">
									<div class="row">
									
										<div class="col-12 col-md-3 branch_loader">
											<label for="users-list-status">Branch</label>
											<?php $branches = \App\Branch::where('status', '1')->orderByRaw('Field(id,37,42,40,41,38,48,49,53,52,54,55,56,36,39,43,44,45,46,47,50,51)')->get(); 
											//echo $branches;
											?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 branch_id" name="branch_id[]" id="" multiple>
													<option value="">Select Any</option>
													@if(count($branches) > 0)
													@foreach($branches as $key => $value)
													<option value="{{ $value->id }}"  @if(!empty(app('request')->input('branch_id')) && in_array($value->id, app('request')->input('branch_id'))) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>
												<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
											</fieldset>
										</div>
										
										<div class="col-12 col-md-3">
											<label for="users-list-status">Studio</label>
											<?php $studios = \App\Studio::where('status', '1');
											if(app('request')->input('branch_id')){
												$studios->where('branch_id',app('request')->input('branch_id'));
											}
											$studios = $studios->orderBy('id','desc')->get();
											?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple3 studio_id" name="studio_id" id="">
													<option value="">Select Any</option>
													@if(count($studios) > 0)
													@foreach($studios as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('studio_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										
										<input type="hidden" class="assistant_id_get" value="{{ app('request')->input('assistant_id') }}">
										
										<div class="col-12 col-md-3">
											<label for="users-list-status">Assistants</label>
											<?php $assistants = \App\User::where('role_id', '3')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2 assistant_id" name="assistant_id">
													<option value="">Select Any</option>
													@if(count($assistants) > 0)
													@foreach($assistants as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('assistant_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Type</label>
											<fieldset class="form-group">												
												<select class="form-control type" name="type">
													<option value="">Select Type</option>
													<option value="Online" @if('Online' == app('request')->input('type')) selected="selected" @endif>Online</option>
													<option value="Offline" @if('Offline' == app('request')->input('type')) selected="selected" @endif>Offline</option>
												</select>												
											</fieldset>
										</div>
										
										<div class="col-12 col-md-3">
											<label for="users-list-status">Date</label>
											<fieldset class="form-group">												
												<input type="date" name="fdate" placeholder="Date" value="{{ $fdate }}" class="form-control StartDateClass fdate">
											</fieldset>
										</div>
										
										<!--div class="col-12 col-md-3">
											<label for="users-list-status">To Date</label>
											<fieldset class="form-group">												
												<input type="date" name="tdate" placeholder="Date" value="{{ app('request')->input('tdate') }}" class="form-control EndDateClass tdate">
											</fieldset>
										</div-->
										
										
										<div class="col-12 col-md-6 mt-2">
										<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
										</fieldset>
									</div>
									</div>
									
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
				    <div class="m-0 panel-title">
                        <div class="accordion" id="myAccordion">
				<?php 
                    $iii = 0;
				$dataFound = 0;
				if (count($get_studios) > 0) {
					
					foreach ($get_studios as $branchArray) {
						if(count($branchArray->studio) > 0){ //echo '<pre>'; print_r($branchArray->studio);
							$dataFound++;
						?>
						<table  class="table table-bordered table-striped" id="">
							<head>
								<tr style="">
									<td colspan="3"><a class="btn btn-primary text-left lbldeptname w-100 collapsed" data-toggle="collapse" href="#multiCollapseExample0" role="button" aria-expanded="false" aria-controls="multiCollapseExample0" id="heading-<?=$dataFound?>">Branch Name : <?php echo $branchArray->name; ?></a>
                                        
<!--                                        <h3 class="accordion-header" id="heading-<?=$dataFound?>"><button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapse-<?=$dataFound?>">Branch Name : <?php echo $branchArray->name; ?></button> </h3>--></td>
								</tr>
							</head>
							<tbody>
							<tr style="">
							<td style="border: 1px solid;">
                                <div id="collapse-<?=$dataFound?>" class="multi-collapse depatcollapse px-3 pt-3 collapse" id="multiCollapseExample0" style="">
						<?php
                            
						foreach ($branchArray->studio as $value) { 
						if(count($value->timetable) > 0){
                            $iii++;
						?>
                        
                           <table  class="table table-bordered table-striped" id="">
							<head>
								<tr style="">
									<td colspan="12"><a class="btn btn-primary text-left lblsearchable w-100 collapsed" data-toggle="collapse" href="#SchemeCollapse1" role="button" aria-expanded="false" aria-controls="SchemeCollapse1"><b>Studio Name : <?php echo $value->name; ?></b></a></td>
									
								</tr>
							</head>
							<tbody>
							<tr style="">
							<td style="border: 1px solid;">     
                             <div class="multi-collapse schemscollapse px-3 pt-3 collapse" id="SchemeCollapse1" style="">   
						<table class="table table-bordered table-striped table-responsive-stack" id="table-<?=$iii?>">
						 
							
							<thead>
								<tr style="">
									<th scope="col">Assistant Name</th>
									<th scope="col">From Time</th>
									<th scope="col">To Time</th>
									<th scope="col">Date</th>
									<th scope="col">Faculty Name</th>
									<th scope="col">Batch Name</th>
									<th scope="col">Course Name</th>
									<th scope="col">Subject Name</th>
									<th scope="col">Type</th>
									
								</tr>
							</thead>
							<tbody>
								<?php
								foreach($value->timetable as $key => $timetable){
								
								$schedule_duration  = "00 : 00 Hours"; 	
								$from_time         = new DateTime($timetable->from_time);
								$to_time           = new DateTime($timetable->to_time);
								$schedule_interval = $from_time->diff($to_time);
								$schedule_duration = $schedule_interval->format('%H : %I Hours');
								
								?>
									<tr>
										<td><?php echo isset($timetable->assistant->name) ?  $timetable->assistant->name : '' ?></td>
										<td><?php echo isset($timetable->from_time) ?  date('h:i A', strtotime($timetable->from_time)) : '' ?></td>
										<td><?php echo isset($timetable->to_time) ?  date('h:i A', strtotime($timetable->to_time)) : '' ?></td>
										<td><?php echo isset($timetable->cdate) ?  date('d-m-Y',strtotime($timetable->cdate)) : '' ?></td>
										<td><?php echo isset($timetable->faculty->name) ?  $timetable->faculty->name : '' ?></td>
										<td><?php echo isset($timetable->batch->name) ?  $timetable->batch->name : '' ?></td>
										<td><?php echo isset($timetable->course->name) ?  $timetable->course->name : '' ?></td>
										<td><?php echo isset($timetable->subject->name) ?  $timetable->subject->name : '' ?></td>
										<td><?php echo isset($timetable->online_class_type) ?  $timetable->online_class_type : '' ?></td>
									</tr>
								<?php
								}
								//echo $plain_hr;die;
								?>

							</tbody>
						
						</table>
                                 </div>
                                </td>
				</tr>
				</tbody>
				</table>
                        
				
						<?php } } ?>
                                    </div>
				</td>
				</tr>
				</tbody>
				</table>
				<?php
					}
					}
				}
				?>
		<style>
		hr{background:#000;}
		</style>
                        </div> 
				    </div> 	 
				</div>       
<?php
if($dataFound==0){
	?>
	<p style="text-align:center;"><h3>Data not found.</h3></p>
	<?php
}?>
			</section>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<!--
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
-->
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple1').select2({
			placeholder: "Select Any",
			allowClear: true
		});
		$('.select-multiple2').select2({
			placeholder: "Select Any",
			allowClear: true
		});
		$('.select-multiple3').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
	
	$("body").on("click", "#download_pdf", function (e) {
		/* if ($userTable.data().count() == 0) {
			swal("Warning!", "Not have any data!", "warning");
			return;
		} */
		var data = {};
			data.studio_id = $('.studio_id').val(),
			data.branch_id = $('.branch_id').val(),
			data.assistant_id = $('.assistant_id').val(),
			data.fdate = $('.fdate').val(),
			data.type = $('.type').val(),
			// data.tdate = $('.tdate').val(),
			window.open("<?php echo URL::to('/admin/'); ?>/studio-report-pdf?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&'));
		/* window.location.href = "<?php echo URL::to('/admin/'); ?>/studio-report-pdf?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&'); */
	});
</script>
<script type="text/javascript">
	$(".branch_id").on("change", function () {
		var branch_id = $(".branch_id option:selected").attr('value');
		var assistant_id = $("input[name=assistant_id]").val();
		if (branch_id) {
			$.ajax({
				beforeSend: function(){
					// $(".branch_loader i").show();
				},
				type : 'POST',
				url : '{{ route('get-branchwise-studio') }}',
				data : {'_token' : '{{ csrf_token() }}', 'branch_id': branch_id},
				dataType : 'html',
				success : function (data){
					// $(".branch_loader i").hide();
					$('.studio_id').empty();
					$('.studio_id').append(data);
				}
			});
			
			$.ajax({
				beforeSend: function(){
					// $(".branch_loader i").show();
				},
				type : 'POST',
				url : '{{ route('get-branchwise-assistant') }}',
				data : {'_token' : '{{ csrf_token() }}', 'branch_id': branch_id, 'assistant_id': assistant_id},
				dataType : 'html',
				success : function (data){
					// $(".branch_loader i").hide();
					$('.assistant_id').empty();
					$('.assistant_id').append(data);
				}
			});
			
			
		}
	});
	
	$(document).ready(function() {
		var branch_id = $(".branch_id option:selected").attr('value');
		var assistant_id = $(".assistant_id_get").val();
		if (branch_id) {
			$.ajax({
				beforeSend: function(){
					// $(".branch_loader i").show();
				},
				type : 'POST',
				url : '{{ route('get-branchwise-assistant') }}',
				data : {'_token' : '{{ csrf_token() }}', 'branch_id': branch_id, 'assistant_id': assistant_id},
				dataType : 'html',
				success : function (data){
					// $(".branch_loader i").hide();
					$('.assistant_id').empty();
					$('.assistant_id').append(data);
				}
			});
		}
	});
</script>



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


<script src="http://15.207.232.85/laravel/public/js/bootstrap.min.js(1).download"></script>
<script src="http://15.207.232.85/laravel/public/js/jquery.min.js.download"></script>



@endsection
