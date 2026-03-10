<div class="card filterCard" @if(empty(app('request')->input('search'))) style="display:none;" @endif>
	<div class="card-content collapse show">
		<div class="card-body">
			<div class="users-list-filter">
				<form action="{{ \Request::Url()}}" method="get" name="filtersubmit">
					<div class="row">
						<div class="col-md-3">
							<label for="users-list-role">Search</label>
							<fieldset class="form-group">
								<input type="text" class="form-control" name="reg_no" 
								value="@if(!empty(app('request')->input('reg_no'))) {{ app('request')->input('reg_no') }} @endif" placeholder="Ex:Mobile, Reg Number">
							</fieldset>
						</div>
						
						<div class="col-12 col-sm-6 col-lg-3 branch_loader">
							<label for="users-list-status">Location</label>
							<fieldset class="form-group">
								<select class="form-control branch_location" name="branch_location" id="">
									<option value="">Select Any</option>
									<option value="jaipur" @if('jaipur' == app('request')->input('branch_location')) selected="selected" @endif>Jaipur</option>
									<option value="jodhpur" @if('jodhpur' == app('request')->input('branch_location')) selected="selected" @endif>Jodhpur</option>
									<option value="delhi" @if('delhi' == app('request')->input('branch_location')) selected="selected" @endif>Delhi</option>
									<option value="prayagraj" @if('prayagraj' == app('request')->input('branch_location')) selected="selected" @endif>Prayagraj</option>
									<option value="indore" @if('indore' == app('request')->input('branch_location')) selected="selected" @endif>Indore</option>
								</select>
							</fieldset>
						</div>
					
						<div class="col-12 col-md-3 branch_loader">
							<label for="users-list-status">Branch</label>
							<?php 
							$branches = \App\Branch::where('status', 1)
									->where('is_deleted','0');
							if(!empty($login_brances)){
								$branches->whereIn('id', $login_brances);
							}
							$branches = $branches->orderByRaw('Field(id,37,42,40,41,38,48,49,53,52,54,55,56,36,39,43,44,45,46,47,50,51)')->get();
							?>
							<fieldset class="form-group">												
								<select class="form-control select-multiple1 branch_id" name="branch_id[]" multiple style="width:100%;">
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

						<div class="clearfix"></div>
						
						<div class="col-12 col-md-3">
							<label for="users-list-status">Class Room / Studio</label>
							<fieldset class="form-group">
								<select class="form-control select-multiple3 studio_id" name="studio_id" style="width:100%;">
									<option value="">Select Any</option>
								</select>												
							</fieldset>
						</div>
						
						
						
						<div class="col-12 col-sm-6 col-lg-2">
							<label for="users-list-status">Status</label>
								<select class="form-control type" name="status">
									<option value="">Select Status</option>
									<option value="1" @if('1' == app('request')->input('status')) selected="selected" @endif>Active</option>
									<option value="2" @if('2' == app('request')->input('status')) selected="selected" @endif>Deleted</option>
								</select>												
							</fieldset>
						</div>

						<div class="col-12 col-md-3">
							<label for="users-list-status">Course</label>
							<?php
							$tt=DB::table('timetables')->select('batch_id')->where('is_deleted', '0')
							->where('cdate','>=', date('Y-m-d',strtotime(now().' -10 days')))
							->groupby('batch_id')->get();
							$batch_ids=[];
							foreach ($tt as $val) {
							  $batch_ids[]=$val->batch_id;
							}

							$batchs = \App\Batch::where('status', '1')->where('is_deleted', '0')
							->whereIN('id',$batch_ids)->orderBy('id','DESC')->get(); ?>
							<fieldset class="form-group">												
								<select class="form-control select-multiple4 batch_id" name="batch_id[]" multiple style="width:100%">
									<option value="">Select Any</option>
									@if(count($batchs) > 0)
										@foreach($batchs as $key => $value)
										<option value="{{ $value->id }}" @if($value->id == app('request')->input('batch_id')) selected="selected" @endif>{{ $value->name }}</option>
										@endforeach
									@endif
								</select>												
							</fieldset>
						</div>
						
						<div class="col-12 col-md-2">
							<label for="users-list-status">From Date</label>
							<fieldset class="form-group">									
								<input type="date" name="fdate" placeholder="Date" value="{{ app('request')->input('fdate') }}" class="form-control StartDateClass fdate">
							</fieldset>
						</div>
						
						<div class="col-12 col-md-2">
							<label for="users-list-status">To Date</label>
							<fieldset class="form-group">												
								<input type="date" name="tdate" placeholder="Date" value="{{ app('request')->input('tdate') }}" class="form-control EndDateClass tdate">
							</fieldset>
						</div>

						<div class="col-12 col-md-2">
							<label for="users-list-status">Block User List</label>
							<fieldset class="form-group">												
								<input type="checkbox" name="blockUsers" placeholder="Date" value="1" class="form-control" @if(!empty(app('request')->input('blockUsers'))) checked @endif>
							</fieldset>
						</div>

						<div class="col-12 col-md-4 mt-2">
							<fieldset class="form-group">		
								<button type="submit" class="btn btn-primary" name="search" value="search">Search</button>
								<a href="{{ \Request::Url() }}" class="btn btn-warning">Reset</a>
							</fieldset>
					    </div>
					</div>
					
				</form>
				
				<a href="?search=search&totals=1" class="btn btn-success">Total Unique Students </a>
				<?php if(app('request')->input('totals')==1){ ?>
				= <span class="btn btn-warning"><b><?=$total_students?></b></span>
				<?php } ?>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="?search=search&totals=2" class="btn btn-success">Total Batch </a>
				<?php if(app('request')->input('totals')==2){ ?>
				= <a href="{{route('admin.support-discussion-batch')}}" target="_blank" class="btn btn-info"><b><?=$total_batch?></b></a>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

@push('js')
	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
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

			$('.select-multiple4').select2({
				placeholder: "Select Any",
				allowClear: true
			});

			$(".filterIcon").on("click",function(){
                 $(".filterCard").toggle();
			})
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
		
		$(".branch_location").on("change", function () {
			var b_location = $(this).val();
			if (b_location) {
				$.ajax({
					beforeSend: function(){
						// $(".branch_loader i").show();
					},
					type : 'POST',
					url : '{{ route('get-location-wise-branch') }}',
					data : {'_token' : '{{ csrf_token() }}', 'b_location': b_location},
					dataType : 'html',
					success : function (data){
						// $(".branch_loader i").hide();
						$('.branch_id').empty();
						$('.branch_id').append(data);
					}
				});
				
			}
		});
		
	</script>
@endpush