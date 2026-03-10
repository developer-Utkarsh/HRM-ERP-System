
<?php $__env->startSection('content'); ?>
<!--style>
#attendanceTable tbody tr td:nth-child(5){display:none !important;}
</style-->
<div class="app-content content" style="margin: 0px !important;">
	<div class="content-wrapper" style="margin: 0px !important;">
		
		<div class="content-body">
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="<?php echo e(url('admin/anupriti-attendence')); ?>" method="get" name="filtersubmit">
									<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>" />
									<div class="row">
										<div class="col-md-3 d-none">
											<label for="users-list-role">Application ID</label>
											<fieldset class="form-group">
												<input type="text" class="form-control name" id="se_name" name="application_id" placeholder="Application Id" value="<?php if(!empty(app('request')->input('application_id'))): ?><?php echo e(app('request')->input('application_id')); ?><?php endif; ?>">
											</fieldset>
										</div>
										<input type="hidden" name="course" value="<?php echo e($_GET['course_2']??''); ?>">
										<div class="col-md-3">
											<label for="users-list-role">Course</label>
											<fieldset class="form-group">
												<select class="form-control" name="course_2">
													<option value="">Select</option>
													<!--<option value="CLAT EXAM">CLAT EXAM</option>
													<option value="CONSTABLE">CONSTABLE</option>
													<option value="ENGINEERING ENTRANCE EXAM">ENGINEERING ENTRANCE EXAM</option>
													<option value="JEE">JEE</option>
													<option value="MAIN">MAIN</option>
													<option value="MEDICAL ENTRANCE EXAM">MEDICAL ENTRANCE EXAM</option>
													<option value="NEET">NEET</option>
													<option value="PATWARI">PATWARI</option>
													<option value="PRE">PRE</option>
													<option value="RAS">RAS</option>
													<option value="RAS-PRE">RAS-PRE</option>
													<option value="REET EXAM">REET EXAM</option>
													<option value="S.I">S.I</option>
													<option value="UPSC IAS">UPSC IAS</option>
													<option value="UPSC Mains">UPSC Mains</option>
													<option value="RAS Mains">RAS Mains</option>-->													
													<option value="3rd Grade level 2 HINDI (B-1) MAY 2025 (Anuprati batch) Jodhpur">3rd Grade level 2 HINDI (B-1) MAY 2025 (Anuprati batch) Jodhpur</option>
													<option value="2nd Grade GK (Target Batch) (B-01) 2025--Jaipur">2nd Grade GK (Target Batch) (B-01) 2025--Jaipur</option>
													<option value="3RD GRADE L-1(B-01)-2025 (ANUPRATI BATCH) - JAIPUR">3RD GRADE L-1(B-01)-2025 (ANUPRATI BATCH) - JAIPUR</option>
													<option value="3rd GRADE L-2 ENGLISH (B-01)-2025 (ANUPRATI BATCH) - JAIPUR">3rd GRADE L-2 ENGLISH (B-01)-2025 (ANUPRATI BATCH) - JAIPUR</option>
													<option value="3rd GRADE L-2 HINDI (B-01)-2025 (ANUPRATI BATCH) - JAIPUR">3rd GRADE L-2 HINDI (B-01)-2025 (ANUPRATI BATCH) - JAIPUR</option>
													<option value="3rd GRADE L-2 SCIENCE (B-01)-2025 (ANUPRATI BATCH) - JAIPUR">3rd GRADE L-2 SCIENCE (B-01)-2025 (ANUPRATI BATCH) - JAIPUR</option>
													<option value="3rd GRADE L-2 SST (B-01) -2025 (ANUPRATI BATCH) - JAIPUR">3rd GRADE L-2 SST (B-01) -2025 (ANUPRATI BATCH) - JAIPUR</option>
													<option value="3rd Grade Level 1st (B-1) May 2025 (Anuprati Batch) - Jodhpur">3rd Grade Level 1st (B-1) May 2025 (Anuprati Batch) - Jodhpur </option>
													<option value="3rd Grade Level 2nd Science (B-1) May 2025(Anuprati Batch) - Jodhpur">3rd Grade Level 2nd Science (B-1) May 2025(Anuprati Batch) - Jodhpur</option>
													<option value="3rd Grade Level 2nd SST (B-1) May 2025 (Anuprati Batch)- Jodhpur">3rd Grade Level 2nd SST (B-1) May 2025 (Anuprati Batch)- Jodhpur</option>
													<option value="Bank PO & Clerk (B-01)--2025 (ANUPRATI BATCH) - JAIPUR">Bank PO & Clerk (B-01)--2025 (ANUPRATI BATCH) - JAIPUR</option>
													<option value="CONSTABLE (B-1) May 2025 (Anuprati Batch) - Jodhpur">CONSTABLE (B-1) May 2025 (Anuprati Batch) - Jodhpur</option>
													<option value="LDC (B-01) 2025--Jaipur">LDC (B-01) 2025--Jaipur</option>
													<option value="Patwar Target (B-1) May 2025 (Anuprati Batch) - Jodhpur">Patwar Target (B-1) May 2025 (Anuprati Batch) - Jodhpur</option>
													<option value="PATWARI (B-01)-2025 (ANUPRATI BATCH) - JAIPUR">PATWARI (B-01)-2025 (ANUPRATI BATCH) - JAIPUR</option>
													<option value="RAILWAY NTPC & D-GROUP (B-1) May 2025(Anuprati Batch) - Jodhpur">RAILWAY NTPC & D-GROUP (B-1) May 2025(Anuprati Batch) - Jodhpur</option>
													<option value="RAJ.POLICE CONSTABLE (B-01)-2025 (ANUPRATI BATCH) - JAIPUR">RAJ.POLICE CONSTABLE (B-01)-2025 (ANUPRATI BATCH) - JAIPUR</option>
													<option value="RAS FOUNDATION (B-01)- 2025 (ANUPRATI BATCH) - JAIPUR">RAS FOUNDATION (B-01)- 2025 (ANUPRATI BATCH) - JAIPUR</option>
													<option value="RAS FOUNDATION (B-01) May 2025 (Anuprati Batch) - Jodhpur">RAS FOUNDATION (B-01) May 2025 (Anuprati Batch) - Jodhpur</option>
													<option value="RRB (B-01) - 2025 (ANUPRATI BATCH) - JAIPUR">RRB (B-01) - 2025 (ANUPRATI BATCH) - JAIPUR</option>
													<option value="RRB BANKING(PRE+MAINS) (B-1) May 2025(Anuprati Batch) - Jodhpur">RRB BANKING(PRE+MAINS) (B-1) May 2025(Anuprati Batch) - Jodhpur</option>
													<option value="SI (RAJ.POLICE) (B-01)-2025 (ANUPRATI BATCH) - JAIPUR">SI (RAJ.POLICE) (B-01)-2025 (ANUPRATI BATCH) - JAIPUR</option>
													<option value="SI Target Batch (B-1) MAY 2025 (ANUPRATI BATCH)-Jodhpur">SI Target Batch (B-1) MAY 2025 (ANUPRATI BATCH)-Jodhpur</option>
													<option value="SSC CGL/CHSL/MTS FOUNDATION (B-1) May 2025(Anuprati Batch) - Jodhpur">SSC CGL/CHSL/MTS FOUNDATION (B-1) May 2025(Anuprati Batch) - Jodhpur</option>
													<option value="SSC FOUNDATION (B-01)-2025 (ANUPRATI BATCH) - JAIPUR">SSC FOUNDATION (B-01)-2025 (ANUPRATI BATCH) - JAIPUR</option>
												</select>
											</fieldset>
										</div>
										<div class="col-md-3">
											<label for="users-list-role">Location</label>
											<fieldset class="form-group">
												<select class="form-control" name="location">
													<option value="">Select</option>
													<option value="Jodhpur" 
													<?php if(($_GET['location']??'')=='Jodhpur'): ?> selected <?php endif; ?>>Jodhpur</option>
													<option value="Jaipur" 
													<?php if(($_GET['location']??'')=='Jaipur'): ?> selected <?php endif; ?>>Jaipur</option>
												</select>
											</fieldset>
										</div>
										<div class="col-md-3">
											<label for="users-list-role">Month</label>
											<fieldset class="form-group">
												<input type="month" class="form-control" name="month" value="<?php if(!empty(app('request')->input('month'))): ?><?php echo e(app('request')->input('month')); ?><?php endif; ?>">
											</fieldset>
										</div>

										<div class="col-md-3">
											<input type="hidden" name="category" value="<?php echo e($_GET['category_2']??''); ?>">
											<label for="users-list-role">Category</label>
											<fieldset class="form-group">
												<select class="form-control" name="category_2">
													<option value="">Select</option>
													<option value="Minority" <?php echo e(request('category_2') == 'Minority' ? 'selected' : ''); ?>>Minority</option>
												</select>

											</fieldset>
										</div>
										
										<div class="col-12 text-right">
											<fieldset class="form-group">		
												<button type="submit" class="btn btn-primary search_click">Search</button>
												<button type="button" class="btn btn-primary d-none" id="exportPDF">Export PDF</button>
												
												<button id="downloadBtn" type="button" class="btn btn-primary">Export in Excel</button>
												<a href="<?php echo URL::to('/admin/anupriti-attendence'); ?>" class="btn btn-warning">Reset</a>
												
											</fieldset>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div id="content">
				
					
					<div id="printData"> 
						<table style="font-size:12px;line-height:24px;border:solid 2px;width:100%;border-collapse:collapse;" class="table">
							<tr style="text-align:center"> 
								<td style="text-align:center" colspan="38"><h3><b>CENTRE NAME : UTKARSH CLASSES AND EDUTECH PRIVATE LIMITED</b></h3></td>
							</tr>										
							<tr>
								<td style="text-align:center" colspan="38"><h4>CHIEF MINISTER ANUPRATI YOJANA : 2024 - 2025</h4></td>
							</tr>
							<tr>
								<td style="text-align:center" colspan="38"><h3>ATTENDANCE SHEET RECORD </br> (ACADEMIC SESSION -  2024 – 2025)</h3></td>
							</tr>
							<tr>
								<td colspan="33"></td>
							</tr>
							<tr>
								<td style="text-align:center" colspan="30"><b>Exam Name</b>: <?php echo e($_GET['course_2']??''); ?>, Month: <?php echo e($_GET['month']??''); ?></td>
							</tr>
							<tr>
								<td colspan="38"></td>
							</tr>
						</table>
					
						<div class="table-responsive">
							<table class="table data-list-view text-center" id="my-table-id" style="text-align:center">
								<thead>
									
									<tr>
										<th>SNO</th>
										<th>Name</th>
										<th>Father Name</th>
										<th>DOB</th>
										<th>Contact</th>
										<th>UC Reg. No.</th>
										<th>Application ID</th>								
										<th>District</th>								
										<th>Admission Date</th>								
										<th>Month</th>								
										<th>Category</th>								

										
										<?php
										$i = 1;
										$getWorkSunday = 31;
										$setDataFOrJs  =31;
										while($getWorkSunday > 0)
										{
											$ii = $i++;
											?>
											<th><?=$ii;?></th>
											<?php
											$getWorkSunday--;
										}
										?>
										<!--
										<th>Total Present</th>
										<th>Total Absent</th>
										-->
										<th>Percentage</th>
									</tr>
								</thead>
								<tbody>
								
								</tbody>
								
								<tfoot>
									<tr>
										<td colspan="33"></td>
									</tr>
									<tr>
										<td style="font-size:7px;" colspan="33">NOTE: L.F.C / L.F.S - ATTENDANCE ALSO INCLUDED IN THIS SHEET</td>
									</tr>
									<tr>
										<td style="font-size:7px;" colspan="33"><b>P: PRESENT, A: ABSENT, WO: WEEK OFF, GH: GOVT. HOLIDAY</b></td>
									</tr>
								</tfoot>
							</table>							
						</div> 
					</div>
				</div>                   
			</section>
		</div>
	</div>
</div>
<style type="text/css">
	th,td{
		padding:4px 0px 4px 0px !important;
		font-size:10px !important;
	}
	
	table {
		width: 100%;
		border-collapse: collapse;
	}
	table, th, td {
		border: 1px solid black;
	}
	
	.table thead th {
		color: #000 !important;
	}
	
	.table tbody td {
		color: #000 !important;
	}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script type="text/javascript">		
	$(document).ready(function() {
		$('.select-multiple').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
	
	$.fn.serializeObject = function() {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };
	
	$(document).ready(function () {
        var attendanceTable = $('#my-table-id').DataTable({
			"searching": false, 
			"info": false,
			"ordering": false,
			"bPaginate": false,
			"lengthChange": false,
			"pageLength": 10000,
            "processing": true,
            "serverSide": true,
            "ajax":{
				"url": "<?php echo e(route('admin.std-anupriti-detail')); ?>",
				"dataType": "json",
				"type": "post",
				"data": function(data){
					console.log(data);
					Object.assign(data, $('[name="filtersubmit"]').serializeObject());
					return data;
				},
		    },
			preDrawCallback: function(settings) {
				if ($.fn.DataTable.isDataTable('#attendanceTable')) {
					var dt = $('#attendanceTable').DataTable();

					//Abort previous ajax request if it is still in process.
					var settings = dt.settings();
					if (settings[0].jqXHR) {
						settings[0].jqXHR.abort();
					}
				}
			},
			"createdRow": function(row, data, dataIndex){
				console.log(data.total_month_days);
				$('td:eq(3)', row).attr('colspan', 1);
				// $('td:eq(4)', row).remove();
			},
	    	"columns": [
		          { "data": "sno" },
		          { "data": "s_name" },
		          { "data": "f_name" },
		          { "data": "dob" },
		          { "data": "contact" },
		          { "data": "registration_no" },
		          { "data": "application_id" },
		          { "data": "district" },
		          { "data": "admission_date" },
		          { "data": "month" },
		          { "data": "category" },
					<?php
					$i = 1;
					while($setDataFOrJs > 0)
					{
						$ii = $i++;
						?>
						{ "data": <?=$ii?> },
						<?php
						$setDataFOrJs--;
					}
					?>
		          //{ "data": "total_present" },
				  //{ "data": "total_absent" },
				  { "data": "percentage" }
		       ]	 

	    });
		// attendanceTable.column(33).visible(false);
		$("body").on("change","#se_name",function(e){
			e.preventDefault();
			//attendanceTable.ajax.reload();
		});
    });

    function ExportToExcel(){
	   var htmltable= document.getElementById('printData');
	   var html = htmltable.outerHTML;
	   window.open('data:application/vnd.ms-excel,' + encodeURIComponent(html));
	}
	
			
	$(document).ready(function(){
		$('#exportPDF').click(function(){
			const { jsPDF } = window.jspdf;
			const a4Width = 210; // width of A4 in mm
			const a4Height = 297; // height of A4 in mm
			const padding = 10; // padding in mm
			
			html2canvas(document.querySelector("#printData")).then(canvas => {
				const imgData = canvas.toDataURL('image/png');
				const pdf = new jsPDF('portrait', 'mm', 'a4');
				
				const imgProps = pdf.getImageProperties(imgData);
				const pdfWidth = pdf.internal.pageSize.getWidth() - 2 * padding;
				const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

				pdf.addImage(imgData, 'PNG', padding, padding, pdfWidth, pdfHeight);
				pdf.save("attendance.pdf");
			});
		});
	});
			
	
	$(document).ready(function() {
		$('#downloadBtn').click(function() {
			var table = document.getElementById('printData');
			var wb = XLSX.utils.table_to_book(table, { sheet: "Sheet1" });

			// Access the worksheet
			var ws = wb.Sheets["Sheet1"];

			// Apply center alignment to all cells
			for (var cell in ws) {
				if (ws.hasOwnProperty(cell) && cell[0] !== '!') {
					ws[cell].s = { alignment: { horizontal: "center", vertical: "center" } };
				}
			}

			var wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'binary' });

			function s2ab(s) {
				var buf = new ArrayBuffer(s.length);
				var view = new Uint8Array(buf);
				for (var i = 0; i < s.length; i++) {
					view[i] = s.charCodeAt(i) & 0xFF;
				}
				return buf;
			}

			saveAs(new Blob([s2ab(wbout)], { type: "application/octet-stream" }), 'table_data.xlsx');
		});
	});

	// Include FileSaver.js (https://github.com/eligrey/FileSaver.js)
	function saveAs(blob, fileName) {
		var a = document.createElement('a');
		a.href = URL.createObjectURL(blob);
		a.download = fileName;
		a.click();
		URL.revokeObjectURL(a.href);
	}

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.without_login_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/batchinventory/anupriti/attendence.blade.php ENDPATH**/ ?>