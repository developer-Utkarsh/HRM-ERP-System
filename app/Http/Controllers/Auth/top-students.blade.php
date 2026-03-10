@include('partials.admin.header')
<style>
#exam-list td {
	text-align: center;
}
</style>
<div >
<div class="page-body">
	<div class="container-xl">
		<div class="row row-cards">
			<div class="col-10">
				<div class="card-header">
					<h4 class="card-title">Top Students</h4>
				</div>
				 
			</div>
			<div class="col-md-2 mt-4"> 
				<a href="{{ route('admin.exam') }}" class="btn btn-xs btn-warning">Back</a>
			</div>
		</div>
	</div>
</div>
<div class="page-body">
	<div class="container-xl">
		<div class="row row-cards">
			
			
			<div class="col-md-12">
				<div class="card">
					<div class="table-responsive">
						<table class="table table-vcenter card-table" id="TableSearch">
							<thead>
								<tr>
									<th class="text-center">#</th>
									<th class="text-center">Mobile No</th> 	
									<th class="text-center">Name</th> 	
									<th class="text-center">District / State</th> 	
									<th class="text-center">Marks</th>
									<th class="text-center">Student PDF</th>
									<th class="text-center">Result</th>
								</tr>
							</thead>
							<tbody id="exam-list">
								<?php
								if(count($top_student) > 0){
									$i = 0;
									foreach($top_student as $val){
										$i++;
										?>
										<tr>
											<td><?=$i;?></td>
											<td>******<?=substr($val->mobile, -4);?></td>
											<td><?=$val->name;?></td>
											<td><?=$val->district;?> / <?=$val->state;?></td>
											<td><?=$val->total_marks;?></td>
											<td>
											<a target="_blank" href="{{asset('student_answer/'.$val->student_answer_pdf)}}" class="btn btn-sm btn-success" title="View Student PDF">View</a>
											</td>
											<td>
											<a target="_blank" href="{{url('admin/student-result/'.$val->student_exam_id)}}" class="btn btn-sm btn-success" title="View Result">View</a>
											</td>
										</tr>
										<?php
									}
								}
								?>
								
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
 
</div>

<div class="modal fade" id="fetch_student_marks_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Student Marks</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
	  <form action="" method="post" >
		  <div class="modal-body student_marks_detail_set">
			
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" >Close</button>
		  </div>
	  </form>
    </div>
  </div>
</div>


		
		
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>
<script>

</script>

@include('partials.admin.footer')