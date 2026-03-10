<!DOCTYPE html>
<html lang="en">
  <head>
    <link rel="icon" type="image/x-icon" href="./Assets/logoPNG.png" />
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta title="Utkarsh offline_report" />
    <title>Faculty Leave Report</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3"
      crossorigin="anonymous"
    /> 
	
	 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">

    <?php /* <link rel="stylesheet" href="./main.css" /> */ ?>
    <script src="https://kit.fontawesome.com/c92e53a223.js" crossorigin="anonymous"></script>
    <script
      src="https://code.jquery.com/jquery-3.6.3.min.js"
      integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU="
      crossorigin="anonymous"
    ></script>
   

  </head>

	<style>
		body {
		  font-family: "Arial";
		  font-size: 16px;
		}
		.h1,.h2,.h3,.h4,.h5,.h6,h1,h2,h3,h4,h5,h6 {
		  font-weight: 800;
		}

		::-webkit-scrollbar {
		  width: 8px;
		  height: 4px;
		}

		::-webkit-scrollbar-track {
		  background: #f1f1f1;
		}

		::-webkit-scrollbar-thumb {
		  background: #ccc;
		  border-radius: 10px;
		}

		::-webkit-scrollbar-thumb:hover {
		  background: #888;
		}

		@media (min-width: 768px) {
		}

		@media (max-width: 768px) {
		  .reply-input input {
			border-radius: 10px 0 0 10px !important;
		  }
		}
		@media (min-width: 1400px) {
		  .container,
		  .container-lg,
		  .container-md,
		  .container-sm,
		  .container-xl,
		  .container-xxl {
			max-width: 1200px;
		  }
		}

		.batch-details{
		  background-color: #fff7cf !important;
		  padding: 20px;
		  border-radius: 10px;
		  margin-bottom: 20px;
		}
		.gray-bg{ 
			background-image: linear-gradient(to top, #f38800, #f39300, #f29e00, #f1a900, #f0b400, #efbc02, #eec506, #edcd0e, #edd60e, #ecde11, #eae716, #e8f01c);
			background-size: 100% 100%;
			background-repeat: no-repeat;
			background-attachment: fixed;
		}
		.status-report{background: #fff;
			padding: 10px;}
		  .nav-pills .nav-link.active, .nav-pills .show>.nav-link {
			color: #070707;
			background-color: #FFE357;
		}
		.nav-link {color:#C9C9C9;}
		.nav-link:focus, .nav-link:hover {
			color: #070707;}
		.accordion-button:not(.collapsed) {
			color: #070707;
			background-color: #fffae2;
			box-shadow: 0 -1px 0 rgba(0,0,0,.125);
		}
		
		.accordion-button {background-color: #fffae2;}
		.tag{padding-top:10px; }
		.tag button{margin: 10px;width: -webkit-fill-available;}
		.bg-yellow {background-color: #ffe357 !important;}
			
		.table { border-color : #8dc6ff !important;}		
	</style>
	<body class=" gray-bg">
		<div class="body-container status-report-all">
			<div class="section">
				<div class="container">
						@if(!empty($firstquery))
						<div class="content-header row pt-3">
							<div class="content-header-left col-md-9 col-12 mb-2">
								<div class="row breadcrumbs-top"> 
									<div class="col-12">
										<h2 class="content-header-title float-left mb-0">Faculty Leave Report</h2>
									</div>
								</div>
							</div>
						</div>
						<div class="card">
							<div class="card-content collapse show">
								<div class="card-body">
									<div class="users-list-filter">
										<form action="{{ route('faculty-leave-report') }}" method="get" name="filtersubmit">
											<div class="row">
												<input type="hidden" name="faculty_id"  value="{{$user_id}}" >	
												
												<div class="col-12 col-sm-6 col-lg-3">											
													<label for="users-list-status">Month</label>								
													<fieldset class="form-group">																					
														<input type="month" name="fmonth"  value="{{ app('request')->input('fmonth') }}" class="form-control fmonth" >	
														
													</fieldset>	 
												</div>	
												
												<div class="col-12 col-sm-6 col-lg-3 pt-4" style="display:;" >
													<button type="submit" class="btn btn-primary">Search</button>
													<a href="{{ route('faculty-leave-report') }}?faculty_id={{$user_id}}" class="btn btn-warning">Reset</a>						
												</div>	
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
						<div class="row justify-content-center pt-3">
							<div class="col-lg-12 pb-3">
								<div class="all_report">									
									<table class="table table-bordered  bg-light text-center">
										<thead style="background: #ffe982;">
											<tr>
												<th scope="col">S. No.</th>
												<th scope="col">Date</th>												
												<th scope="col">Reason</th>												
											</tr>
										</thead>
										<tbody>		
											<?php 
												$i = 1;
												foreach($firstquery as $fi){
											?>
											<tr>												
												<td><?=$i;?></td>
												<td>{{ date('d-m-Y',strtotime($fi->date)) }}</td>
												<td>
													<?php 
													if(!empty($fi->reason)){	
														echo $fi->reason; 
														}else{	 
															echo '-';	
														}
														?>
												</td>
											</tr>	
											<?php $i++; } ?>
										</tbody>
									</table>
									
								</div>
							</div>	
						</div>
						@else 
							No Data Found
						@endif
				</div>
			</div>
		</div>
		
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>
		
		<script type="text/javascript">
			function showhide(fid, sid){
				$('.'+fid).show();
				$('.'+sid).hide();
				
				$('.buttonclass').addClass('btn-light'); //eventually removeClass of some previous class	
			}
			
			$('.buttonclass').on('click', function(){ 
				$(this).addClass('btn-success');
				$(this).removeClass('btn-light');			  
			});
		</script>
	</body>
</html>
