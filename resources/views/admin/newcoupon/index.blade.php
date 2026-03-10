@extends('layouts.admin')
@section('content')
@php
	$user = Auth::user();
@endphp
<style type="text/css">
	.select2-container {
		width:100%!important;
	}
</style>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-6">
						<h2 class="content-header-title float-left mb-0">Coupon List</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-6 text-right">
                        <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target="#AddCoupon">Add New Coupon</button>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">

				<div class="table-responsive">
					<table class="table data-list-view" id="coupon_table">
						<thead>
							<tr>
								<th>#</th>
								<th>Title Name </th>
								<th>Coupon Type</th>
								<th>Start Date </th>
								<th>End Date </th>
								<th>Coupon Value</th>
								<th>Assign Type</th>
								<th>Transfer Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							@foreach($coupons as $c)
							<tr>
								<td>{{ $c['id'] }}</td>
								<td>{{ $c['coupon_tilte'] }}</td>
								<td>{{ ['0' => 'Course d/M/Y', '1' => 'User Dependent', '2' => 'User + Course Dependent'][$c['coupon_for']] ?? '-' }}</td>
								<td>{{ date('d/M/Y h:i A', $c['start']) }}</td>
								<td>{{ date('d/M/Y h:i A', $c['end']) }}</td>
								
								<td>{{ $c['coupon_value'] . ($c['coupon_type']==2? "%" : "/-") }}</td>
								<td><?=$c['target_id'] ? " Assign Later" : "Assign Now";?></td>
								<td><?=!$c['is_transferable'] ? "Non-Transferable" : "Transferable";?></td>
								<td>
									<?php
									if(Auth::user()->id==901 || Auth::user()->department_type==50){
									?>
										<button type="button" class="add_remark btn btn-primary btn-sm waves-effect waves-light"
											data-id="{{ $c['id'] }}"
											data-coupon_title="{{ $c['coupon_tilte'] }}"
											data-start_date="{{ $c['start'] }}"
											data-end_date="{{ $c['end'] }}"
											data-coupon_value="{{ $c['coupon_value'] }}"
											data-discount_type="{{ $c['coupon_type'] }}"
											data-max_discount="{{ $c['max_discount'] }}"
											data-max_usage="{{ $c['max_usage'] }}"
											data-coupon_type="{{ $c['coupon_for'] }}"
											data-coupon_mode="{{ $c['coupon_mode'] }}"
											data-course_type="{{ $c['is_pro'] }}"
											>
											Add Remark
										</button>
									<?php } ?>
									 
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
					
				</div>                   
			</section>
		</div>
	</div>
</div>
 <!--Add Remark---start-->
<div class="modal fade" id="AddCoupon" tabindex="-1" aria-labelledby="AddCouponLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header justify-content-between">
				<div>
					<h5 class="modal-title" id="AddCouponLabel">Add New Coupon</h5>
				</div>
				<div>
					
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
			</div>
			<div class="modal-body">
				<form id="add_new_coupon" method="post">
					 @csrf
					<div class="row g-2">
                        <div class="col-md-12 col-12 d-none">
                            <label for="first-name-column">Coupon</label>
                            <div class="form-group">
                                <select class="form-control" name="coupon" id="" required>
                                    <option value="New Coupon" selected{{ old('coupon') == 'New Coupon' ? 'selected' : '' }}>New Coupon</option>
                                </select>
                                <div class="error_msg text-danger fw-bold"></div>
                            </div>
                        </div>
						<div class="col-md-6 col-12">
                            <label for="first-name-column">Categaory Name</label>
                            <div class="form-group">
                                <select class="form-control select-multiple1" name="categaory_name" id="" required>
                                    <option value=""> - Select Any - </option>
                                    <option value="State One Day" {{ old('categaory_name') == 'State One Day' ? 'selected' : '' }}>State One Day</option>
									<option value="Civil Services" {{ old('categaory_name') == 'Civil Services' ? 'selected' : '' }}>Civil Services</option>
									<option value="K-13" {{ old('categaory_name') == 'K-13' ? 'selected' : '' }}>K-13</option>
									<option value="Center One Day(COD)" {{ old('categaory_name') == 'Center One Day(COD)' ? 'selected' : '' }}>Center One Day(COD)</option>
									<option value="Nursing/Pre Nursing" {{ old('categaory_name') == 'Nursing/Pre Nursing' ? 'selected' : '' }}>Nursing/Pre Nursing</option>
									<option value="Test Series" {{ old('categaory_name') == 'Test Series' ? 'selected' : '' }}>Test Series</option>
									<option value="Digital Influencers" {{ old('categaory_name') == 'Digital Influencers' ? 'selected' : '' }}>Digital Influencers</option>
									<option value="Digital Marketing" {{ old('categaory_name') == 'Digital Marketing' ? 'selected' : '' }}>Digital Marketing</option>
                                </select>
                                <div class="error_msg text-danger fw-bold"></div>
                            </div>
                        </div>
						<div class="col-lg-6 col-12 form-group">
							<label for="" class="fw-medium">Sub Category</label>
							<input type="text" placeholder="" name="sub_category" id="" class="form-control" required />
							<div class="error_msg text-danger fw-bold"></div>
						</div>
						<div class="col-lg-6 col-12 form-group">
							<label for="" class="fw-medium">Coupon Title</label>
							<input type="text" placeholder="" name="coupon_title" id="" class="form-control" required />
							<div class="error_msg text-danger fw-bold"></div>
						</div>
						<div class="col-lg-6 col-12 form-group">
							<label for="" class="fw-medium">Start Date</label>
							<input type="datetime-local" placeholder="" name="start_date" id="" class="form-control" required />
							<div class="error_msg text-danger fw-bold"></div>
						</div>
						<div class="col-lg-6 col-12 form-group">
							<label for="" class="fw-medium">End Date</label>
							<input type="datetime-local" placeholder="" name="end_date" id="" class="form-control" required />
							<div class="error_msg text-danger fw-bold"></div>
						</div>
						<div class="col-lg-6 col-12 form-group">
							<label for="" class="fw-medium">Coupon Discount Type</label>
							<select class="form-control select-multiple1" name="discount_type" id="" required>
								<option value=""> - Select Any - </option>
								<option value="2" {{ old('discount_type') == '2' ? 'selected' : '' }}>In Percentage(%)</option>
								<option value="1" {{ old('discount_type') == '1' ? 'selected' : '' }}>In Value</option>
							</select>
							<div class="error_msg text-danger fw-bold"></div>
						</div>
						<div class="col-lg-6 col-12 form-group">
							<label for="" class="fw-medium">Enter Coupon value -(Only Digit)</label>
							<input type="number" min="1" placeholder="" name="coupon_value" id="" class="form-control" required />
							<div class="error_msg text-danger fw-bold"></div>
						</div>
						<div class="col-lg-6 col-12 form-group">
							<label for="" class="fw-medium">Coupon Max Discount</label>
							<input type="number" min="1" placeholder="" name="max_discount" id="" class="form-control" required />
							<div class="error_msg text-danger fw-bold"></div>
						</div>
						<div class="col-lg-6 col-12 form-group">
							<label for="" class="fw-medium">Coupon Max Usage</label>
							<input type="number" min="1" placeholder="" name="max_usage" id="" class="form-control" required />
							<div class="error_msg text-danger fw-bold"></div>
						</div>
						<div class="col-lg-6 col-12 form-group">
							<label for="" class="fw-medium">Coupon Type</label>
							<select class="form-control select-multiple1" name="coupon_type" id="" required>
								<option value=""> - Select Any - </option>
								<option value="0" {{ old('coupon_type') == '0' ? 'selected' : '' }}>Course Dependent</option>
								<option value="1" {{ old('coupon_type') == '1' ? 'selected' : '' }}>User Dependent</option>
								<option value="2" {{ old('coupon_type') == '2' ? 'selected' : '' }}>User + Course Dependent</option>
							</select>
							<div class="error_msg text-danger fw-bold"></div>
						</div>
						<div class="col-lg-12 col-12 form-group">
							<label for="" class="fw-medium">Data Link Selection: <br/>
							Instructions for Data Link Selection:  <br/>
							<b>1.If the data is Course Dependent-</b><br/>
							   - Provide the Course ID in the data link. <br/>
							<b>2.If the data is User Dependent:  </b><br/>
							   - Provide the User Number in the data link.  <br/>
							<b>3.If the data is both User + Course Dependent</b><br/>
							   - Provideboth the User Number and Course ID in the data link.  <br/>
							</label>
							<input type="text" placeholder="" name="data_link" id="" class="form-control" required />
							<div class="error_msg text-danger fw-bold"></div>
						</div>
						<div class="col-lg-6 col-12 form-group">
							<label for="" class="fw-medium">Coupon Mode</label>
							<select class="form-control select-multiple1" name="coupon_mode" id="" required>
								<option value=""> - Select Any - </option>
								<option value="0" {{ old('coupon_mode') == '0' ? 'selected' : '' }}>Auto</option>
								<option value="1" {{ old('coupon_mode') == '1' ? 'selected' : '' }}>Manual</option>
							</select>
							<div class="error_msg text-danger fw-bold"></div>
						</div>
						<div class="col-lg-6 col-12 form-group">
							<label for="" class="fw-medium">Course Type</label>
							<select class="form-control select-multiple1" name="course_type" id="" required>
								<option value=""> - Select Any - </option>
								<option value="0" {{ old('discount_type') == '0' ? 'selected' : '' }}>Standard</option>
								<option value="1" {{ old('discount_type') == '1' ? 'selected' : '' }}>Prime</option>
								<option value="2" {{ old('discount_type') == '2' ? 'selected' : '' }}>Both</option>
							</select>
							<div class="error_msg text-danger fw-bold"></div>
						</div>
						<div class="col-lg-12 col-12 form-group">
							<label for="" class="fw-medium">Reason</label>
							<input type="text" placeholder="" name="reason" id="" class="form-control" required />
							<div class="error_msg text-danger fw-bold"></div>
						</div>
					</div>
					<div class="submit-btn text-end" style="display: flex; justify-content: center;">
						<button type="submit" id="submitCoupon" class="btn btn-dark px-2 submitCoupon">Submit</button>
						<div class="process text-center fw-bold text-warning"></div>
						<div class="responsemsgModal text-success text-center fw-bold"></div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="AddRemarkModal" tabindex="-1" aria-labelledby="AddRemarkLabel" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header justify-content-between">
				<div>
					<h5 class="modal-title" id="AddRemarkLabel">Add Remark</h5>
				</div>
				<div>
					
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
			</div>
			<div class="modal-body">
				<form id="remarkForm" method="post">
					@csrf
                    <input type="hidden" name="coupon_id" id="coupon_id">
                    <input type="hidden" name="coupon_title" id="coupon_title">
                    <input type="hidden" name="start_date" id="start_date">
                    <input type="hidden" name="end_date" id="end_date">
                    <input type="hidden" name="coupon_value" id="coupon_value">
                    <input type="hidden" name="discount_type" id="discount_type">
                    <input type="hidden" name="max_discount" id="max_discount">
                    <input type="hidden" name="max_usage" id="max_usage">
                    <input type="hidden" name="coupon_type" id="coupon_type">
                    <input type="hidden" name="coupon_mode" id="coupon_mode">
                    <input type="hidden" name="course_type" id="course_type">
					
					<div class="row g-2">
						<div class="col-md-12 col-12">
                            <label for="first-name-column">Categaory Name</label>
                            <div class="form-group">
                                <select class="form-control select-multiple1" name="categaory_name" id="" required>
                                    <option value=""> - Select Any - </option>
                                    <option value="State One Day" {{ old('categaory_name') == 'State One Day' ? 'selected' : '' }}>State One Day</option>
									<option value="Civil Services" {{ old('categaory_name') == 'Civil Services' ? 'selected' : '' }}>Civil Services</option>
									<option value="K-13" {{ old('categaory_name') == 'K-13' ? 'selected' : '' }}>K-13</option>
									<option value="Center One Day(COD)" {{ old('categaory_name') == 'Center One Day(COD)' ? 'selected' : '' }}>Center One Day(COD)</option>
									<option value="Nursing/Pre Nursing" {{ old('categaory_name') == 'Nursing/Pre Nursing' ? 'selected' : '' }}>Nursing/Pre Nursing</option>
									<option value="Test Series" {{ old('categaory_name') == 'Test Series' ? 'selected' : '' }}>Test Series</option>
									<option value="Digital Influencers" {{ old('categaory_name') == 'Digital Influencers' ? 'selected' : '' }}>Digital Influencers</option>
									<option value="Digital Marketing" {{ old('categaory_name') == 'Digital Marketing' ? 'selected' : '' }}>Digital Marketing</option>
                                </select>
                                <div class="error_msg text-danger fw-bold"></div>
                            </div>
                        </div>
						<div class="col-lg-12 col-12 form-group">
							<label for="" class="fw-medium">Sub Category</label>
							<input type="text" placeholder="" name="sub_category" id="" class="form-control" required />
							<div class="error_msg text-danger fw-bold"></div>
						</div>
						
						<div class="col-lg-12 col-12 form-group">
							<label for="remark" class="fw-medium">Remark</label>
							<textarea placeholder="Remark" name="remark" id="addremark" class="form-control" style="height:300px;" required></textarea>
							<div class="error_msg text-danger fw-bold"></div>
						</div>
						
					</div>
					<div class="submit-btn text-end" style="display: flex; justify-content: center;">
						<button type="submit" id="submitRemark" class="btn btn-dark px-2 submitRemark">Submit</button>
						<div class="process text-center fw-bold text-warning"></div>
						<div class="responsemsgModal text-success text-center fw-bold"></div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!--Add Remark---end-->
@endsection
@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
$('#coupon_table').DataTable({
    paging: false,
    searching: true,
    ordering: false,
    info: true,
    lengthChange: true, // show entries dropdown
    pageLength: 10,     // default rows per page
    language: {
        search: "_INPUT_",
        searchPlaceholder: "Search coupons..."
    }
});
$(document).ready(function() {
    $('.select-multiple1').select2({
        placeholder: " -- Select -- ",
        allowClear: true
    });
});

$(document).ready(function () {
	
	$('#add_new_coupon').on('submit', function (e) {
		e.preventDefault();
		$(".submitCoupon").css('display','none');
		$('.responsemsgModal').html("Please Wait...");
		let formData = new FormData(this);
		$.ajax({
			url: '{{ route('admin.coupon.addcoupon') }}',
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function (response) {
				let data = JSON.parse(response);
				if (data.status === 'success') {
					$('#AddCoupon').modal('hide');
					$('#add_new_coupon')[0].reset();
					window.location.href = "{{ route('admin.newcoupon.historyList') }}";
				} else {
					$(".submitCoupon").css('display','block');
					$('.responsemsgModal').html(data.message);
					setTimeout(function() {
						$('.responsemsgModal').html("");
					}, 3000);
				}
			},
			error: function () {
				$(".submitCoupon").css('display','block');
				$('.responsemsgModal').html("An error occurred. Please try again.");
				setTimeout(function() {
					$('.responsemsgModal').html("");
				}, 3000);
			}
		}); 
	});
	
	$(".add_remark").click(function(){
		$("#AddRemarkModal").modal('show');
		$("#coupon_id").val($(this).data('id'));
		$("#coupon_title").val($(this).data('coupon_title'));
		$("#start_date").val($(this).data('start_date'));
		$("#end_date").val($(this).data('end_date'));
		$("#coupon_value").val($(this).data('coupon_value'));
		$("#discount_type").val($(this).data('discount_type'));
		$("#max_discount").val($(this).data('max_discount'));
		$("#max_usage").val($(this).data('max_usage'));
		$("#coupon_type").val($(this).data('coupon_type'));
		$("#coupon_mode").val($(this).data('coupon_mode'));
		$("#course_type").val($(this).data('course_type'));
	});

    
	$('#remarkForm').on('submit', function (e) {
		e.preventDefault();
		$(".submitRemark").css('display','none');
		$('.responsemsgModal').html("Please Wait...");
		let formData = new FormData(this);
		$.ajax({
			url: '{{ route('admin.coupon.addRemark') }}',
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function (response) {
				let data = JSON.parse(response);
				if (data.status === 'success') {
					$('#AddRemarkModal').modal('hide');
					$('#remarkForm')[0].reset();
					window.location.href = "{{ route('admin.newcoupon.historyList') }}";
				} else {
					$(".submitRemark").css('display','block');
					$('.responsemsgModal').html(data.message);
					setTimeout(function() {
						$('.responsemsgModal').html("");
					}, 3000);
				}
			},
			error: function () {
				$(".submitRemark").css('display','block');
				$('.responsemsgModal').html("An error occurred. Please try again.");
				setTimeout(function() {
					$('.responsemsgModal').html("");
				}, 3000);
			}
		}); 
	});

    
	
	
	
});


</script>
@endsection
