<!DOCTYPE html>
<html lang="en">
  <head>
    <link rel="icon" type="image/x-icon" href="./Assets/logoPNG.png" />
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta title="Utkarsh offline_report" />
    <title>Faculty Mentor Report</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3"
      crossorigin="anonymous"
    /> 
    <link rel="stylesheet" href="./main.css" />
    <script src="https://kit.fontawesome.com/c92e53a223.js" crossorigin="anonymous"></script>
    <script
      src="https://code.jquery.com/jquery-3.6.3.min.js"
      integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU="
      crossorigin="anonymous"
    ></script>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
      crossorigin="anonymous"
    ></script>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  </head>

	<style>
		@import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap");

		body {
		  font-family: "Inter";
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
	</style>
  <body>
   

    <div class="body-container status-report-all">
      <div class="section gray-bg">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-6">
               
			<br/>
			<?php
			if(count($mentor_batch_list) > 0){
				foreach($mentor_batch_list as $val){
			?>
				<div class="bg-secondary-light batch-details"><a href="{{route('mentor-report-batch-detail')}}?batch_id=<?=$val['batch_id']?>&&mentor_id=<?=$val['mentor_id']?>">Batch Name -  <strong><?=$val['batch_name']?></strong></a></div>
			<?php }
			}
			else{				
				?>
				<div class="bg-secondary-light batch-details"><strong>No Batch Found</strong></div>
				<?php
			}
			?>
			
              
              
              
            </div>
          </div>
        </div>
      </div>


      
    </div>
  </body>
</html>
