@extends('layouts.admin')
@section('content')

<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-7">
						<h2 class="content-header-title float-left mb-0">Edit Requisition Request</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Edit Request</a>
								</li>
							</ol>
						</div>
					</div>
					<div class="col-5 text-right">
						<a href="{{ route('admin.request.requisition-request') }}" class="btn btn-primary">Back</a>
						<?php if( Auth::user()->role_id ==25){ ?>
						<a href="#" class="btn btn-primary p-1 d-none" data-toggle="modal" data-target="#exampleModal">Check Stock</a>
						<button type="button" class="btn btn-success importProduct" data-id="<?=$id?>">Import</button>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
			</div>
		</div>
		<div class="content-body">
			<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="{{ route('admin.request.update-requisition', [$id, $type]) }}" method="post" enctype="multipart/form-data">
										@csrf
										
										<?php 
											$j = 0; 
											foreach($notification as $record){ 
												$j++;
										?>
										<div class="form-body ">
											<div class="row">
												<input type="hidden" name="request_id[]" value="<?=$record->arid;?>"/>
												<?php 
													if($record->request_type=='1'){
														$powoText = 'WO';
														$powoClass = "d-none";
														$required = "";
													}else{
														$powoText = 'PO';
														$powoClass = "";
														$required  = 'required';
													}
													if( Auth::user()->role_id ==31 && $record->company == 'na'){ 
														
												?>
													<div class="text-right col-md-12">
														<a href="javascript:void(0)"  data-id="{{ $record->arid }}" class="get_edit_data text-primary">Request <?=$powoText;?></a></br>
													</div>
												<?php } ?>
												
												<div class="col-md-3 col-3">
													<div class="form-group">
														<label for="first-name-column">Product Name</label>
														<input name="title[]" class="form-control" value="{{ $record->title }}"/>
													</div>
												</div>
												<div class="col-md-3 col-3">
													<div class="form-group">
														<label>Select Branch</label>
														<select class="form-control select-multiple1 branch_id<?=$j;?>" name="branch_id[]" data-id="<?=$j;?>">
															@php
																//$branch = DB::table('branches')->where('status', 1)->get();
															@endphp
															@foreach ($branch as $key => $bData)
																<option value="{{ $bData->id }}" <?php if($bData->id==$record->branch_id){ echo 'selected'; }?>>{{ $bData->name }}</option>
															@endforeach															
														</select>	
													</div>
												</div>
												<div class="col-md-6 col-6">
													<div class="form-group">
														<label for="first-name-column">Product Description</label>
														<textarea name="requirement[]" class="form-control" placeholder="Please enter your product description">{{ $record->requirement	 }}</textarea>
													</div>
												</div>	
												
												<div class="col-md-3 col-3">
													<div class="form-group">
														<label>Asset is requested</label>
														<select class="form-control select-multiple1" name="emp_id[]" required>
															<option value="">Select</option>
															<option value="0" <?php if($record->emp_id==0){ echo 'selected'; } ?>>New Employee</option>
															@foreach ($employee as $key => $value)															
																<option value="{{ $value->id }}" <?php if($value->id==$record->emp_id){ echo 'selected'; } ?>>{{ $value->name }} - {{ $value->register_id }}</option>
															@endforeach
														</select>													
													</div>
												</div>
												
												<div class="col-md-3 col-3">
													<div class="form-group">
														<label>Approved this requisition.</label>
														<?php if(is_numeric($record->remark)){ ?> 
														<select name="remark[]" class="form-control select-multiple1">
															<option value="">-- Select --</option>
															<?php foreach($dhemployee as $key => $dvalue){ ?>
															<option value="{{ $dvalue->id }}" <?php if($dvalue->id==$record->remark){ echo 'selected'; } ?>>{{ $dvalue->name }} - {{ $dvalue->register_id }}</option>
															<?php } ?>
														</select>
														<?php }else{ ?>
														<textarea name="remark[]" class="form-control">{{ $record->remark }}</textarea>
														<?php } ?>												
														@if($errors->has('remark'))
														<span class="text-danger">{{ $errors->first('remark') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-3 col-3">
													<div class="form-group">
														<label for="first-name-column">Requested Category</label>
														<?php 
															$material_category = app('request')->input('material_category');
															$mcategory = DB::table('material_category')->where('status',1); 
															if(!empty($material_category)){
																$mcategory->where('id', $material_category);
															}
															$mcategory = $mcategory->orderBy('id','asc')->get();											
														?>
														<select name="material_category[]" class="form-control select-multiple1" required>
															<option value="">-- Select --</option>																
															@if(count($mcategory) > 0)
															@foreach($mcategory as $key => $value)
															<option value="{{ $value->id }}" @if($value->id == $record->material_category) selected="selected" @endif>{{ $value->name }}</option>
															@endforeach
															@endif
														</select>
														@if($errors->has('material_category'))
														<span class="text-danger">{{ $errors->first('material_category') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-group">
														<div class="pb-1">Request Type :</div>
														<label>
														<input type="radio" name="request_type[<?=$j?>]" value="0" class="reqType" <?php if($record->request_type=='0'){ echo 'checked'; } ?> /> MRL 															
														</label>
														&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="request_type[<?=$j?>]" value="1" class="reqType" <?php if($record->request_type=='1'){ echo 'checked'; } ?>/> WRL 															
														</label> 
													</div>
												</div>						
												<?php if(Auth::user()->role_id == 25 || Auth::user()->role_id == 33 || Auth::user()->role_id == 31){ ?>
												<div class="w-50 row inventoryRole mx-0 wrlRole<?=$j?> <?=$powoClass;?>">
													<div class="col-lg-3 d-none">		
														<div class="form-group">
															<label for="first-name-column">Category</label>
															 <select class="form-control select-multiple1 cat_id" name="category[]" data-id="<?=$j;?>">
																<option value="">Select Category</option>
																@if(count($category) > 0) 
																@foreach($category as $categoryvalue)
																	<?php 
																		if($record->category==$categoryvalue->id){
																			$selected = 'selected';
																		}else{
																			$selected = '';
																		}
																	?>
																<option value="{{ $categoryvalue->id }}" <?=$selected;?>>{{ $categoryvalue->name }}</option>
																@endforeach
																@endif
															</select>
															@if($errors->has('cat_id'))
															<span class="text-danger">{{ $errors->first('cat_id') }} </span>
															@endif
														</div>	
													</div>	
													
													<div class="col-lg-3 d-none">
														<div class="form-group">
															<label for="first-name-column">Sub Category</label>
															<select class="form-control select-multiple1 sub_product sub_cat_id<?=$j;?>" name="scategory[]" data-id="<?=$j;?>">
																@if(!empty($record->scategory))
																	@php
																		$subCatData = DB::table('category')->where('id', $record->scategory)->get();
																	@endphp
																	@foreach ($subCatData as $key => $subCatDataValue)
																		<?php 
																			if($record->scategory==$subCatDataValue->id){
																				$selected = 'selected';
																			}else{
																				$selected = '';
																			}
																		?>
																		<option value="{{ $subCatDataValue->id }}" <?=$selected;?>>{{ $subCatDataValue->name }}</option>
																	@endforeach
																@else
																	<option value="">Select Category</option>
																@endif
															</select>
															@if($errors->has('sub_cat_id'))
															<span class="text-danger">{{ $errors->first('sub_cat_id') }} </span>
															@endif
														</div>	
													</div>
													<div class="col-lg-6"> 
														<div class="form-group">
															<label>Select Product</label>
															<select class="form-control select-multiple1 product_data<?=$j;?>" name="product[]" data-id="<?=$j;?>" <?=$required;?>>		
																<option value="">Select </option>
																@php
																	$proData = DB::table('products')
																		->select('products.*','A.name as cat_name','B.name as scat_name')
																		->leftJoin('category AS A','A.id', '=', 'products.cat_id')
																		->leftJoin('category AS B','B.id', '=', 'products.sub_cat_id')
																		->where('status','Active')
																		->get();
																@endphp
																@foreach ($proData as $key => $proDataValue)
																	<?php 
																		if($record->product_id==$proDataValue->id){
																			$selected = 'selected';
																		}else{
																			$selected = '';
																		}
																	?>
																	<option value="{{ $proDataValue->id }}" <?=$selected;?>>{{ $proDataValue->cat_name }}- {{ $proDataValue->scat_name }}- {{ $proDataValue->name }}</option>
																@endforeach
															
															</select>	
															@if($errors->has('product_data'))
															<span class="text-danger">{{ $errors->first('product_data') }} </span>
															@endif
														</div>
													</div>
													
													<div class="col-lg-6">
														<div class="form-group">
															<label>Type of Demand</label>
															<select class="form-control" name="type[]">
																<?php 
																	$aselected = '';
																	$nselected = '';
																	if($record->type=='Asset'){
																		$aselected = 'selected';
																	}else if($record->type=='Non Asset'){
																		$nselected = 'selected';
																	}
																?>
																<option value="">Select</option>
																<option value="Asset" <?=$aselected;?>>Asset</option>
																<option value="Non Asset" <?=$nselected;?>>Non Asset</option>
															</select>													
														</div>
													</div>
												</div>
												<?php } ?> 
												
												<div class="col-md-2 col-2">
													<div class="form-group">
														<label for="first-name-column">Quantity</label>
														<input type="number" name="qty[]" class="form-control" placeholder="Please enter your quantity" step="any" value="{{ $record->qty }}"/>	
													</div>
												</div>
												<div class="col-md-2 col-2">
													<div class="form-group">
														<label for="first-name-column">UOM</label>
														<select class="form-control" name="uom[]">
															<option value="">-- Select --</option>
															@php $uom = DB::table('uom')->get(); @endphp
															@foreach ($uom as $key => $val)
															<?php 
																
																if($record->uom==$val->code){ $selected = 'selected'; }else{ $selected = ''; }
															?>
															<option value="{{ $val->code }}" <?=$selected;?>>{{ $val->code }}</option>
															@endforeach															
														</select>	
													</div>
												</div>
												
												
												<?php //Purchase Team Status
												if($type=="1"){ ?>
												<div class="col-md-2 col-2">
													<div class="form-group">
														<label for="first-name-column">Purchase Status</label>
														<select class="form-control pur_status" name="purchase_status[]">
															<option value="0" @if('0' == old('type', $record->purchase_status )) selected="selected" @endif>In Progress</option>
															<option value="1" @if('1' == old('type', $record->purchase_status )) selected="selected" @endif>On Hold</option>
															<option value="2" @if('2' == old('type', $record->purchase_status )) selected="selected" @endif>Deliver</option>
															<option value="3" @if('3' == old('type', $record->purchase_status )) selected="selected" @endif>PO Generated</option>
															<option value="4" @if('4' == old('type', $record->purchase_status )) selected="selected" @endif>Below 5000 - Deliver</option>
															<option value="5" @if('5' == old('type', $record->purchase_status )) selected="selected" @endif>Cancel</option>
															<option value="6" @if('6' == old('type', $record->purchase_status )) selected="selected" @endif>Rejected</option>
															<option value="7" @if('7' == old('type', $record->purchase_status )) selected="selected" @endif>Proceed To Maintenance</option>
														</select>
													</div>
												</div>		
												<div class="col-md-12 col-12 preason" style="display:none">
													<div class="form-group">
														<label for="first-name-column">Reason</label>
														<textarea name="purchase_reason[]" class="form-control rfield2"></textarea>
													</div>
												</div>
												<?php //Decision Maker Status
												}else if($type=="2"){ ?>
												<div class="col-md-2 col-2">
													<div class="form-group">
														<label for="first-name-column">Status</label>
														<select class="form-control" name="dm_status[]" onChange="statusUpdate3(this.value)">
															<option value="0" @if('0' == old('type', $record->dm_status )) selected="selected" @endif>Pending</option>
															<option value="1" @if('1' == old('type', $record->dm_status )) selected="selected" @endif>Approved</option>
															<option value="2" @if('2' == old('type', $record->dm_status )) selected="selected" @endif>Reject</option>
														</select>
													</div>
												</div>		
												<div class="col-md-6 col-12 dmreason" style="display:none">
													<div class="form-group">
														<label for="first-name-column">Reason</label>
														<textarea name="reason[]" class="form-control rfield3"></textarea>
													</div>
												</div>	
												<?php //Inventory Team Status
												}else if($type=="3"){ ?>
												<div class="col-md-2 col-2">
													<div class="form-group">
														<label for="first-name-column">Status</label>
														<select class="form-control inv_status" name="it_status[]">
															<?php 
																$inSelect = ''; 
																$prselect = ''; 
																$inRselect = ''; 
																$inPselect = ''; 
																
																if($record->it_status==2){ 
																	$prselect = 'selected'; 
																}else if($record->it_status==1){
																	$inSelect = 'selected'; 
																}else if($record->it_status==3){
																	$inRselect = 'selected'; 
																}else if($record->it_status==4){
																	$inRselect = 'selected'; 
																}else if($record->it_status==5){
																	$inPselect = 'selected'; 
																}
															?>
															<option value="">-- Select Status --</option>
															<option value="0">Pending</option>
															<option value="1" <?=$inSelect;?>>In Stock</option>
															<option value="5" <?=$inPselect;?>>Proceed To Instock Approval</option>
															<option value="2" <?=$prselect;?>>Proceed To Purchase</option>		
															<option value="3" <?=$inRselect;?>>Rejected</option>		
															<option value="4" <?=$inRselect;?>>Transfer To Networking Team</option>		
														</select>
													</div>
												</div>	
												<div class="col-md-12 col-12 itreason" style="display:none" data-id="<?=$j;?>">
													<div class="form-group">
														<label for="first-name-column">Reason</label>
														<textarea name="reason[]" class="form-control rfield"></textarea>
													</div>
												</div>	
												<?php }else if($type=="4"){ ?>
												<div class="col-md-2 col-2">
													<div class="form-group">
														<label for="first-name-column">Status</label>
														<select class="form-control" name="pm_status[]" onChange="statusUpdate4(this.value)">
															<option value="0" @if('0' == old('type', $record->pm_status )) selected="selected" @endif>Pending</option>
															<option value="1" @if('1' == old('type', $record->pm_status )) selected="selected" @endif>Approved</option>
															<option value="2" @if('2' == old('type', $record->pm_status )) selected="selected" @endif>Reject</option>
														</select>
													</div>
												</div>
												
												<?php  //Department Head Status
												}else{ ?>
												<div class="col-md-2 col-2">
													<div class="form-group">
														<label for="first-name-column">Status</label>
														<select class="form-control" name="status[]" onChange="statusUpdate(this.value)">
															<option value="0" @if('0' == old('type', $record->status)) selected="selected" @endif>Pending</option>
															<option value="1" @if('1' == old('type', $record->status)) selected="selected" @endif>Approved</option>
															<option value="2" @if('2' == old('type', $record->status)) selected="selected" @endif>Reject</option>
														</select>
													</div>
												</div>	
												<div class="col-md-12 col-12 reason" style="display:none">
													<div class="form-group">
														<label for="first-name-column">Reason</label>
														<textarea name="reason[]" class="form-control rfield"></textarea>
													</div>
												</div>												
												<?php } ?>
												
												
											</div>
										</div>
										
										<hr style="background:#ff0303;height:2px;">
										
										<?php } ?>
										
									
										<?php if(Auth::user()->role_id == 25 || Auth::user()->role_id == 33){ ?>
										<div class="form-body">
											<div class="row">
												<span class="append_div w-100">
												
												</span>
												
												<div class="col-md-12 col-12 rAddmore">
													<div class="form-group text-right">
														<label for="">&nbsp;</label>
														<span id="rowno" style="display:none;"><?=$j;?></span>
														<button class="btn btn-primary add-more" type="button" style="margin-top:10px;">Add More</button>
													</div>
												</div>												
											</div>
										</div>
										
										<?php } ?>
										
										<div class="mt-2 text-left">
											<button type="submit" class="btn btn-primary mb-1">Submit</button>
										</div>
									</form>
									
									
									<!-- Hidden Coloum -->
									<div class="copy-fields w-100" style="display:none;">													
										<div class="remove_row">		
											<div class="row mx-0">
												<div class="col-md-3 col-3">
													<div class="form-group">
														<label for="first-name-column">Product Name</label>
														<input type="text" class="form-control" placeholder="Product Name" id="" name="title[]" value="">
														@if($errors->has('title'))
														<span class="text-danger">{{ $errors->first('title') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-3 col-3">
													<div class="form-group">
														<label>Select Branch</label>
														<select class="form-control branch_id select-multiple_2" name="branch_id[]" data-id>
															<option value="" >-- Select Branch --</option>
															@php
																//$branch = DB::table('branches')->where('status', 1)->get();
															@endphp
															@foreach ($branch as $key => $bData)
																<option value="{{ $bData->id }}" >{{ $bData->name }}</option>
															@endforeach															
														</select>	
													</div>
												</div>
												<div class="col-md-6 col-6">
													<div class="form-group">
														<label for="first-name-column">Product Description</label>
														<textarea name="requirement[]" class="form-control" placeholder="Please enter your product description" ></textarea>
														@if($errors->has('requirement'))
														<span class="text-danger">{{ $errors->first('requirement') }} </span>
														@endif
													</div>
												</div>	
												
												<div class="col-md-3 col-3">
													<div class="form-group">
														<label>Asset is requested</label>
														<select class="form-control select-multiple_2" name="emp_id[]" required>
															<option value="">Select</option>
															@foreach ($employee as $key => $value)															
																<option value="{{ $value->id }}" >{{ $value->name }} - {{ $value->register_id }}</option>
															@endforeach
														</select>												
													</div>
												</div>
												
												<div class="col-md-3 col-3">
													<div class="form-group">
														<label>Approved this requisition.</label>
														<select name="remark[]" class="form-control select-multiple_2">
															<option value="">-- Select --</option>
															<?php foreach($dhemployee as $key => $dvalue){ ?>
															<option value="{{ $dvalue->id }}">{{ $dvalue->name }} - {{ $dvalue->register_id }}</option>
															<?php } ?>
														</select>
														@if($errors->has('remark'))
														<span class="text-danger">{{ $errors->first('remark') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-3 col-3">
													<div class="form-group">
														<label for="first-name-column">Requested Category</label>
														<?php 
															$material_category = app('request')->input('material_category');
															$mcategory = DB::table('material_category')->where('status',1); 
															if(!empty($material_category)){
																$mcategory->where('id', $material_category);
															}
															$mcategory = $mcategory->orderBy('id','asc')->get();											
														?>
														<select name="material_category[]" class="form-control select-multiple_2" required>
															<option value="">-- Select --</option>																
															@if(count($mcategory) > 0)
															@foreach($mcategory as $key => $value)
															<option value="{{ $value->id }}" @if($value->id == app('request')->input('material_category')) selected="selected" @endif>{{ $value->name }}</option>
															@endforeach
															@endif
														</select>
														@if($errors->has('material_category'))
														<span class="text-danger">{{ $errors->first('material_category') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-group">
														<div class="pb-1">Request Type :</div>
														<label>
														<input type="radio" name="request_type[]" value="0" class="reqType"  checked/> MRL 															
														</label>
														&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="request_type[]" value="1" class="reqType"/> WRL 															
														</label> 
													</div>
												</div>
												<div class="mx-0 w-50 inventoryRole row">
													<input type="hidden" name="request_id[]" value="0"/>
													<div class="col-lg-3 d-none">
														<div class="form-group">
															<label for="first-name-column">Category</label>
															 <select class="form-control cat_id select-multiple_2" name="category[]" data-id>
																<option value="">Select Category</option>
																@if(count($category) > 0) 
																@foreach($category as $categoryvalue)
																<option value="{{ $categoryvalue->id }}" {{ old('cat_id') ? 'selected' : ''}}>{{ $categoryvalue->name }}</option>
																@endforeach
																@endif
															</select>
															@if($errors->has('cat_id'))
															<span class="text-danger">{{ $errors->first('cat_id') }} </span>
															@endif
														</div>	
													</div>	
													
													<div class="col-lg-3 d-none">
														<div class="form-group ">
															<label for="first-name-column">Sub Category</label>
															<select class="form-control sub_product sub_cat_id select-multiple_2" name="scategory[]"  data-id>
																@if(!empty(old('cat_id')))
																	@php
																		$proData = DB::table('category')->where('parent', old('sub_cat_id'))->where('is_deleted','0')->get();
																		
																	@endphp
																	@foreach ($subCatData as $key => $subCatDataValue)
																		<option value="{{ $subCatDataValue->id }}" {{ old('sub_cat_id', !empty(old('cat_id')) && $subCatDataValue->id == old('cat_id') ? 'selected' : '' ) }}>{{ $subCatDataValue->name }}</option>
																	@endforeach
																@else
																	<option value="">Select Category</option>
																@endif
															</select>
															@if($errors->has('sub_cat_id'))
															<span class="text-danger">{{ $errors->first('sub_cat_id') }} </span>
															@endif
														</div>	
													</div>	
													<div class="col-lg-6">
														<div class="form-group">
															<label>Select Product</label>
															<select class="form-control product_data select-multiple_2"  name="product[]" required data-id>												
																<option value="">Select</option>
																@php
																	$proData = DB::table('products')
																		->select('products.*','A.name as cat_name','B.name as scat_name')
																		->leftJoin('category AS A','A.id', '=', 'products.cat_id')
																		->leftJoin('category AS B','B.id', '=', 'products.sub_cat_id')
																		->where('status','Active')
																		->get();
																@endphp
																@foreach ($proData as $key => $proDataValue)
																	<option value="{{ $proDataValue->id }}" {{ old('sub_cat_id', !empty(old('sub_cat_id')) && $proDataValue->id == old('sub_cat_id') ? 'selected' : '' ) }}>{{ $proDataValue->cat_name }} - {{ $proDataValue->scat_name }} - {{ $proDataValue->name }}</option>
																@endforeach
															
															</select>	
															@if($errors->has('product_data'))
															<span class="text-danger">{{ $errors->first('product_data') }} </span>
															@endif
														</div>
													</div>
													<div class="col-lg-6">
														<div class="form-group">
															<label>Type of Demand</label>
															<select class="form-control" name="type[]" required>
																<option value="">Select</option>
																<option value="Asset">Asset</option>
																<option value="Non Asset">Non Asset</option>
															</select>													
														</div>
													</div>
												</div>
													
												
												<div class="col-md-2 col-3">
													<label>Quantity</label>
													<input type="number" name="qty[]" class="form-control" value="" />												
												</div>
												<div class="col-md-2 col-2">
													<div class="form-group">
														<label for="first-name-column">UOM</label>
														<select class="form-control" name="uom[]">
															<option value="">-- Select --</option>
															@php $uom = DB::table('uom')->get(); @endphp
															@foreach ($uom as $key => $val)
															<option value="{{ $val->code }}" {{ old('sub_cat_id', !empty(old('sub_cat_id')) && $proDataValue->id == old('sub_cat_id') ? 'selected' : '' ) }}>{{ $val->code }}</option>
															@endforeach															
														</select>	
													</div>
												</div>
												<div class="col-md-2 col-4">
													<div class="form-group">
														<label for="first-name-column">Status</label>
														<select class="form-control" name="it_status[]" onChange="poupdate(this.value)">
															<option value="">-- Select Status --</option>
															<option value="0">Pending</option>
															<option value="1">In Stock</option>
															<option value="2">Proceed To Purchase</option>
															<option value="3">Rejected</option>
															<option value="4">Transfer To Networking Team</option>		
														</select>
													</div>
												</div>	
												<div class="col-md-2  text-left">
													<div class="form-group mb-0">
														<label for="">&nbsp;</label>
														<button class="btn btn-danger remove" type="button" style="margin-top:10px;">Remove</button>
													</div>
												</div>
										
												
											</div>
											<hr style="background:#ff0303;height:2px;" class="mx-2">
										</div>
										
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
			
										
		</div>
	</div>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Check Stock</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div>
					<select class="form-control select-multiple2 product_id" name="product_id">
						<option value="">-- Select --</option>
						<?php foreach($product as $p){ ?>
						<option value="<?=$p->id;?>"><?=$p->name;?></option>
						<?php } ?>						
					</select>
				</div>
				<div class="pt-2">
					<input type="text" name="product_qty" value="" class="form-control product_qty" readonly />
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="importModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<form method="post" id="submit_import_file">
			@csrf
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Import Product</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div>
						<input type="file" name="product_file" value="" class="form-control"/>
						<input type="hidden" name="mrl_no" value="" class="mrl_no form-control"/>
						
						<a href="{{ asset('laravel/public/product-import.xlsx') }}" title="Click Here" download>Sample File</a>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success">Submit</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</div>
		</form>
	</div>
</div>

<div class="modal fade bd-example-modal-xl" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl text-dark" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<?php 
					if($notification[0]->request_type=='1'){ 
						$powoText = 'WO'; 
						$powoHead = 'Work';
					}else{	
						$powoText = 'PO'; 
						$powoHead = 'Purchase';
					} 
				?>
				<h5 class="modal-title" id="exampleModalLabel">Request <?=$powoText;?> </h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="{{ route('admin.request.po-request') }}" method="post" enctype="multipart/form-data" class="po_request_new">
				@csrf
				<div class="modal-body">
					<div class=" px-2"> 
						<div class="row match-height">
							<div class="col-12">
								<div class="card mb-0" style="border:solid 1px #000">
									<div class="card-content border">
										<div class="text-center py-1">
											<img src="{{ asset('laravel/public/logo.png')}}" width="80"/>
											<h2 class="text-primary pt-1">Utkarsh Classes & Edutech Pvt. Ltd.</h2>
											<p>
												<?=$poAddress;?>
												<textarea name="po_address" style="display:none;"><?=$poAddress;?></textarea>
											</p>
										</div>
										<div class="row text-center">													
											<div class="col-md-6 col-12">
												Phone: 7849906549
											</div>
											<div class="col-md-6 col-12">
												E-mail: accounts@utkarsh.com		
											</div>
										</div>
										<div class="border-top border-bottom p-1">
											<div class="text-center"><h3 class="text-primary"><?=$powoHead;?> Order</h3></div>
											<div class="text-center">GST No. - <?=$poGst;?>
												<input type="hidden" name="po_gst" value="<?=$poGst;?>"/></textarea>
											</div>
										</div>
										
										<div class="row py-2" style="font-size:14px;">													
											<div class="col-md-6 col-12 pl-4 comleft">
												<b>To,</b></br>
												<select name="company" class="select-multiple2 form-control blankvalue" onChange="getCompanydetails(this.value)">
													<option value="">-- Select --</option>
													<?php foreach($buyer as $b){ ?>
														<option value="{{ $b->id }}">{{ $b->name }} - (V{{ $b->id }})</option>
													<?php } ?>
												</select>
												<div class="fill-name">												
													<textarea name='address' placeholder='Address' readonly></textarea> </br>
													GSTIN - <input type="text" name="gstin" value="" readonly /> </br>
													PHONE - <input type="text" name="phone" value="" readonly /> </br>													
													Email - <input type="text" name="email" value=""/> 											
												</div>
											</div>
											<div class="col-md-6 col-12">
												</br>
												<?=$powoHead;?> Order Date -	<input type="date" name="pdate" value="<?=date('Y-m-d');?>"/> </br>
												<?php
													$mt = date('m');
													$yr = date('Y');
													$date = "2023-04-04"; //today()->format('Y-m-d');
																										
													$maxValue = DB::table('asset_request_notification')
													->leftjoin('asset_request','asset_request.id','asset_request_notification.request_id')
													->where('asset_request.request_type',$notification[0]->request_type)
													->whereYear('asset_request_notification.pdate', '=', $yr)
													->whereMonth('asset_request_notification.pdate', '=', $mt)
													->where('asset_request_notification.pdate','>=',$date)
													->max('asset_request_notification.po_no');
																										
													if($maxValue=="2038"){
														$maxValue = 1;
													}else{
														$maxValue = $maxValue + 1;
													}
												?>
												<?=$powoText;?> No.: <label class="poline text-center"><?='UTK'.$powoText;?>-<input type="text" name="po_location" value="<?=$polocation;?>" readonly style="width:34px;border:none;text-align:right;"/>-<input type="text" name="po_no" value="<?=$maxValue;?>" readonly style="width:34px;border:none;text-align:right;"/>
												
												/<input type="text" name="po_month" value="<?=date('m').'/'.date('Y');?>" readonly style="border:none;"/>
												</label></br>
												</br>
												Location - 
												<select name="location" class="select-multiple form-control blankvalue" required>
													<option value="">-- Select Location --</option>
													<?php foreach($branch as $b){	?>
														<option value="{{ $b->id }}">{{ $b->name }}</option>
													<?php } ?>
												</select>

											</div>
										</div>
										<div class="p-1 border-top border-bottom">
											Dear Sir,
											With reference to your quotation we have pleasure in confirming our <?=$powoHead;?> Order for the following as per the terms & conditions stated hereunder.	
										</div>
										<div class="">
											<table class="table table-bordered">
												<thead>
													<tr>
														<!--<th width="20">S. No.</th>-->
														<th>Particulars</th>
														<th width="90">UOM</th>
														<th width="100">Qty</th>
														<th width="80">Rate / Rs.</th>
														<th width="30">Amount / Rs.</th>
														<th width="70">GST %</th>
														<th width="30">GST Amount</th>
														<th width="100">Total Amount / Rs.</th>
													</tr>
												</thead>
												<tbody class="appenthtml">	
													<div class="cRecord" style="display:none;">
														<?php 
															echo $cRecord = '1';
														?>
													</div>
													<tr class="gethtml1">
														<!--<td><input type="text" name="" value="1" class="w-100"/></td>-->
														<td>
															<select class="w-25 mr-2 options requiId1 blankvalue" name="requiId[]" data-rowno="1">
																<option value="">-- Select --</option>
															</select>
															
															<input type="text" name="item[]" value="" class="item1 float-right" style="width:70%"/>
														</td>
														<td>
															<!--<input type="text" name="" value="" class="w-100"/>-->
															<select name="uom[]" class="w-100 uom1">
																<option value="">Select</option>
																@php $uom = DB::table('uom')->get(); @endphp
																@foreach ($uom as $key => $val)
																<option value="{{ $val->code }}">{{ $val->code }}</option>
																@endforeach
															</select>
														</td>
														<td><input type="text" name="qty[]" value="" placeholder="0" class="w-100 qty1" onblur="getRate(this.value)"/></td>
														<td><input type="text" name="rate[]" value="" placeholder="0" class="w-100 rate1 blankvalue" onblur="getRate(this.value)"/></td>
														<td><input type="text" name="amount[]" value="" placeholder="0" class="w-100 amount1 blankvalue" readonly /></td>
														<td>
															<select name="gstrate[]" class="w-100 gstrate1 blankvalue" onblur="getgstRate(this.value)">
																<option value="0">0</option>
																<option value="5">5</option>
																<option value="12">12</option>
																<option value="18">18</option>
																<option value="28">28</option>
															</select>
														</td>
														<td><input type="text" name="gstamt[]" value="" placeholder="0" class="gstamt1 w-100 blankvalue" readonly /></td>
														<td><input type="text" name="totalamt[]" value="" placeholder="0" class="totalamt1 w-100 blankvalue" readonly /></td>
													</tr>
													
												</tbody>
												<tfoot>
													<tr>
														<td colspan="7" align="right"><b>Total Amount</b></td>
														<td>
															<input type="text" name="finalAmt" value="" class="w-100 blankvalue finalAmt" style="font-size:12px;"/> 
														</td>
													</tr>
													<tr>
														<td colspan="10">
															<button type="button" onclick="rowAppend()">+</button>
															<button type="button" onclick="rowRemove()">-</button>
														</td>
													</tr>
												</tfoot>
											</table>
										</div>
										<div class="p-1">
											<div><b>Narration :</b></div>
											<textarea name="narration" class="form-control blankvalue" placeholder="Narration"></textarea>
										</div>
										<div class="p-1">
											<div><b>Advance :</b></div>
											<div class="row mx-0">
												<div class="float-left w-75">
													<input type="number"  value="" name="advance" class="form-control blankvalue" placeholder="Advance" onBlur="getAdvanceAmt(this.value)">
												</div>
												<div class="float-right w-25 pl-2">
													%   <input type="number"  value="" name="advanceAmt" class="blankvalue advanceAmt" placeholder="Advance Amount" readonly> 
												</div>
											</div>
										</div>
										
										<div class="p-1">
											<div><b>Terms & Conditions :</b></div>
											<div>
												<textarea name="terms" class="form-control terms" rows="8"> </textarea>
											</div>
										</div>
										<!--
										<div class="row pt-5 text-center pb-2">	
											<div class="col-md-4 col-12 pt-5">
												Store & Purchase Head
											</div>
											<div class="col-md-4 col-12 pt-5">
												HOD
											</div>
											<div class="col-md-4 col-12 pt-5">
												DIRECTOR
											</div>
										</div>
										-->
										
										<div class="p-1">
											<div class="form-group">
												<label>Quotation 1</label>
												<input type="file" name="quotation_one" value="" class="form-control blankvalue"/>
											</div>
											<div class="form-group">
												<label>Quotation 2</label>
												<input type="file" name="quotation_two" value="" class="form-control blankvalue"/>
											</div>
											<div class="form-group">
												<label>Quotation 2</label>
												<input type="file" name="quotation_three" value="" class="form-control blankvalue"/>
											</div>
										</div>
										<div class="col-md-12 col-12 pl-4 border-top py-1">
											Accepted By <input type="text" name="approved" value="" class="blankvalue"/>
											<input class="request_id" type="hidden" name="request_id" value=""/>
										</div>
										<div class="col-md-12 col-12 pl-4 border-top py-1">
											<label>Important <?=$powoText;?> ?</label>
											<select class="blankvalue" name="poImportant">
												<option value="">-- Select --</option>
												<option value="Yes">Yes</option>
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>	
					<div class="pt-2 px-2 text-right"><button type="submit" class="btn btn-primary">Submit</button></div>
				</div>
				
			</form>
		</div>
	</div>
</div>

@endsection


<style type="text/css">
	.select2  {
		width:100% !important;
	}
	
	.comleft input, select, textarea {
		border: 0;
		border-bottom: dotted 1px;
		width: 80%;
	}
	
	.poline {
		border: 0;
		border-bottom: dotted 1px;
	}
	
	input:focus-visible{
		outline: navajowhite;
	}
	
	select:focus-visible{
		outline: navajowhite;
	}
	
	textarea:focus-visible{
		outline: navajowhite;
	}
	
	input, select, textarea {
		border: 0;
		border-bottom: dotted 1px;
	}
	
	.po {
		display: none;
	}
	
	.po .border-top{
		border-top: solid 1px #000 !important;
	}
	
	.po .border-bottom{
		border-bottom: solid 1px #000 !important;
	}
	
	.po .select2  {
		width:80% !important;
		border: 0 !important;
		border-bottom: dotted 1px !important;
	}
	
	.po .select2-container--classic .select2-selection--single, .select2-container--default .select2-selection--single{
		min-height: 25px !important;
	}
	
	textarea.form-control {
		white-space: nowrap;
	}
	
	.select2-container .select2-selection--single {
		height: auto !important;
	}
</style>

@section('scripts')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<script src="{{ asset('laravel/public/admin/js/jquery.validate.min.js') }}"></script>
<script>
	$(document).ready(function() {
		$('.select-multiple1').select2({
			placeholder: "Select",
			allowClear: true
		});
		
		
		$('.select-multiple2').select2({
			placeholder: "Select",
			allowClear: true
		});
		
	});	

	
	function rowAppend(){	
		cRecord =	$('.cRecord').html();		
		options =	$('.options').html();		
		i 		=	parseInt(cRecord)+1;
		
		
		dsfd = '<td><input type="text" name="item[]" value="" class="blankvalue item'+i+'  float-right"  style="width:70%"/><select class="w-25 mr-2 blankvalue options requiId'+i+'" data-rowno="'+i+'" name="requiId[]">	'+options+'</select></td><td><select name="uom[]" class="w-100 uom'+i+'"><option value="">Select</option>@php $uom = DB::table("uom")->get(); @endphp	@foreach ($uom as $key => $val)	<option value="{{$val->code }}">{{ $val->code }}</option>	@endforeach</select></td><td><input type="text" name="qty[]" value="" placeholder="0" class="blankvalue w-100 qty'+i+'" onblur="getRate(this.value)"/></td><td><input type="text" name="rate[]" value="" placeholder="0" class="blankvalue w-100 rate'+i+'" onblur="getRate(this.value)"/></td><td><input type="text" name="amount[]" value="" placeholder="0" class="blankvalue w-100 amount'+i+'" readonly /></td><td><select name="gstrate[]" class="blankvalue w-100 gstrate'+i+'" onblur="getgstRate(this.value)"><option value="0" selected>0</option><option value="5">5</option><option value="12">12</option><option value="18">18</option><option value="28">28</option></select></td><td><input type="text" name="gstamt[]" value="" placeholder="0" class="blankvalue gstamt'+i+' w-100" readonly /></td><td><input type="text" name="totalamt[]" value="" placeholder="0" class="blankvalue totalamt'+i+' w-100" readonly /></td>';
		
		
		 $(".appenthtml").append('<tr class="gethtml'+i+'">'+dsfd+'</tr>');
		 
		 $('.cRecord').html(i);
	}
	
	function rowRemove(){
		cRecord =	$('.cRecord').html();		
		
		if(cRecord > 1){
			$('.gethtml'+cRecord).remove();
			
			i 	= cRecord - 1;
			$('.cRecord').html(i);
		}
	}
	
	function statusUpdate(value){
		if(value==2){
			$('.reason').show();
			// $(".rfield").prop('required',true);
		}else{
			$('.reason').hide();
			// $(".rfield").prop('required',false);
		}
	}
	
	// function poupdate(value){
	$("body").on('change', ".inv_status", function() {
		var value = $(this).val();
        var otherField = $(this).closest('.form-body').find('.itreason'); 
		
		
		if(value==2){
			$('.po').show();
			$("input").prop('required',true);
			
			otherField.hide();
			$(this).closest(".form-body").find('.rfield2').prop('required', false);
			$(this).closest(".form-body").find('.rfield').prop('required', false);
		}else if(value==3){
			otherField.show(); 
			$(this).closest(".form-body").find('.rfield').prop('required', true);
			$("input").prop('required',false);
		}else if(value==4){
			$("input").prop('required',false);
			$("select").prop('required',false);
			$(".rfield").prop('required',false);
		}else{
			$('.po').hide();
			$("input").prop('required',true);
			
			otherField.hide(); 
			$(this).closest(".form-body").find('.rfield2').prop('required', false);
			$(this).closest(".form-body").find('.rfield').prop('required', false);
		}
	});
	
	// function statusUpdate2(value){
		// if(value==1 || value==6){
			// $('.preason').show();
			// $(".rfield2").prop('required',true);
			
		// }else{
			// $('.preason').hide();
			// $(".rfield2").prop('required',false);
		// }
	// }
	
	$("body").on('change', ".pur_status", function() {
        var value = $(this).val();
        var otherField = $(this).closest('.form-body').find('.preason'); 

        if (value == 6 || value == 1) {
            otherField.show(); 
			$(this).closest(".form-body").find('.rfield2').prop('required', true);
        } else {
            otherField.hide(); 
			$(this).closest(".form-body").find('.rfield2').prop('required', false);

        }
    });
	
	function statusUpdate3(value){
		if(value==2){
			$('.dmreason').show();
			$(".rfield3").prop('required',true);
		}else{
			$('.dmreason').hide();
			$(".rfield3").prop('required',false);
		}
	}
	
	
	function getCompanydetails(id){
		$.ajax({
			type : 'POST',
			url : '{{ route('admin.get-company-details') }}',
			data : {'_token' : '{{ csrf_token() }}', 'id': id},
			dataType : 'html',
			success : function (data){
				$('.fill-name').empty();
				
				$('.fill-name').html(data);
			}
		});		
	}	
	
	function getRate(value){
		rowCount  = $(".appenthtml tr").length;
		
		for (i = 1; i <= rowCount; ++i) {					
			qty 	=	$('.qty'+i).val();
			rate 	=	$('.rate'+i).val();
			
			amount 	=	(qty*rate).toFixed(2);
			
			// alert(amount);
			
			$('.amount'+i).val(amount);
		}
	}
	
	
	function getgstRate(value){
		rowCount  = $(".appenthtml tr").length;
		newAmt = 0;
		for (i = 1; i <= rowCount; ++i) {		
			amount = $('.amount'+i).val();
			gstrate = $('.gstrate'+i).val();
			
			gstAmt = ((amount*gstrate)/100).toFixed(2);
			
			finalAmt = parseFloat(amount) + parseFloat(gstAmt);		
			$('.gstamt'+i).val(gstAmt);
			
			finalAmt = finalAmt.toFixed(2);
			$('.totalamt'+i).val(finalAmt);
			
			
			newAmt = (parseFloat(finalAmt) + parseFloat(newAmt)).toFixed(2);
		}
		
		$('.finalAmt').val(newAmt);
	}
	
	function getAdvanceAmt(value){
		finalAmt = $('.finalAmt').val();
		adAmt    = (finalAmt*value)/100;
		
		$('.advanceAmt').val(adAmt);
	}
</script>
<script type="text/javascript">
    $("body").on('change','.cat_id',function(){
		var cat_id = $(this).val(); 
		
		var id = $(this).attr("data-id"); 
		if (cat_id) {
			$.ajax({
				type : 'POST',
				url : '{{ route('admin.product.get-sub-cat') }}',
				data : {'_token' : '{{ csrf_token() }}', 'cat_id': cat_id},
				dataType : 'html',
				success : function (data){
					$('.sub_cat_id'+id).empty();
					$('.sub_cat_id'+id).append(data);
				}
			});
		}
	});
	
	$("body").on('change','.sub_product',function(){
		var sub_cat_id = $(this).val(); 
		
		// alert(sub_cat_id);
		
		var id = $(this).attr("data-id"); 
		console.log(id);
		if (sub_cat_id) {
			$.ajax({
				type : 'POST',
				url : '{{ route('admin.product.get-sub-product') }}',
				data : {'_token' : '{{ csrf_token() }}', 'sub_cat_id': sub_cat_id},
				dataType : 'html',
				success : function (data){
					$('.product_data'+id).empty();
					$('.product_data'+id).append(data);
				}
			});
		}
	});
		
	$(".get_edit_data").on("click", function() { 
		$('.fill-name input').val('');
		$('.blankvalue').val('');
		
		
		$(".options").each(function() {
			$(this).find('option').remove();
		});
		
		$('.fill-name textarea').val('');
		
		var request_id = $(this).attr("data-id"); 
				
		$('.request_id').val(request_id);
		$('#myModal').modal({
				backdrop: 'static',
				keyboard: true, 
				show: true
		});
		
		
		$.ajax({
			type : 'POST',
			url : '{{ route('admin.request.get-request-data') }}',
			data : {'_token' : '{{ csrf_token() }}', 'request_id': request_id},
			dataType : 'json',
			success : function (data){
				if(data.status == true){
					
					$('.item1').val(data.requirement);
					$('.qty1').val(data.qty);
					$('.terms').val(data.terms);
					$('.options').append(data.options);
					
				}
			}
		});
	}); 
	
	$(document).ready(function() {
		$(".add-more").click(function(){ 
		    var rowno=$("#rowno").html();
			rowno=parseInt(rowno)+1;
			$("#rowno").html(rowno);
			
			// alert(rowno);
			
			var html = $(".copy-fields").html();
			html = html.replace('inventoryRole', 'inventoryRole wrlRole'+rowno);
			html = html.replace('data-id=""', 'data-id='+rowno);
			html = html.replace('sub_cat_id', 'sub_cat_id'+rowno);
			html = html.replace('data-id=""', 'data-id='+rowno);
			html = html.replace('product_data', 'product_data'+rowno);			
			html = html.replaceAll('request_type[]', 'request_type['+rowno+']');
			  
			$(".append_div").append(html);    
			
			
			$('.append_div .select-multiple_2').select2({				
				width:'100%',
				placeholder: "Select",
				allowClear: true
			});
		});
		$("body").on("click",".remove",function(){ 
			$(this).parents(".remove_row").remove();
		});
	});
	
	$("body").on('change','.product_id',function(){
		var pro_id = $(this).val(); 
		
		if (pro_id) {
			$.ajax({
				type : 'POST',
				url : '{{ route('admin.check-product-quantity') }}',
				data : {'_token' : '{{ csrf_token() }}', 'pro_id': pro_id},
				dataType : 'html',
				success : function (data){
					console.log(data);
					$('.product_qty').empty();
					$('.product_qty').val(data);
				}
			});
		}
		
	});
	
	
	$("body").on("change",".reqType",function(){ 
	    var name=$(this).attr("name");
		var  name= name.replace('request_type[','');
		var rowno= name.replace(']','');
		rowno=parseInt(rowno);
		$(this).closest('.row').find(".wrlRole"+rowno).toggleClass("d-none").prop('required',false);
		$(this).closest('.row').find(".wrlRole"+rowno+" select").prop('required',false);
		
		// $('select').attr("required", !checkBoxes.attr("required"));
		
		// var checkBoxes = $(this).closest('.row').find(".wrlRole"+rowno+" select");
        // checkBoxes.prop("required", !checkBoxes.prop("required"));
	});
	
	
	$('.po_request_new').on('keyup keypress', function(e) {
	  var keyCode = e.keyCode || e.which;
	  if (keyCode === 13 && !$(document.activeElement).is('textarea')) { 
		e.preventDefault();
		return false;
	  }
	});
	
	$("body").on("change",".options",function(){ 
		var rawno = $(this).attr('data-rowno');
		var product_id = $(this).val();
		
		$.ajax({
			type : 'POST',
			url : '{{ route('admin.request.get-product-item') }}',
			data : {'_token' : '{{ csrf_token() }}', 'product_id': product_id},
			dataType : 'json',
			success : function (data){
				if(data.status == true){
					console.log(data.requirement);
					console.log(data.qty);
					console.log(rawno);

					$('.item'+rawno).val(data.requirement);
					$('.qty'+rawno).val(data.qty);
					//$('.terms').val(data.terms);
					// $('.options').append(data.options);
					
				}else{
					$('.item'+rawno).val('');	
					$('.qty'+rawno).val('');
				}
			}
		});
	});
	
	
	$(".importProduct").on("click", function() {  
		var mrl_no = $(this).attr("data-id"); 
		
		$('#importModel').modal({
				backdrop: 'static',
				keyboard: true, 
				show: true
		});
				
		$('.mrl_no').val(mrl_no);		
	}); 
</script>

<script>
	var $form = $('#submit_import_file');
	validatorprice = $form.validate({
		ignore: [],
		rules: {
			'import_file' : {
				required: true,                
			},       
		},

		/* errorElement : "span",*/
		errorClass : 'border-danger',
		errorPlacement: function(error, element) {
			if (element.is(':input') || element.is(':select')) {
				$(this).addClass('border-danger');
			}
			else {
				return true;
			}
		}
	});
	
	$("#submit_import_file").submit(function(e) {
		var form = document.getElementById('submit_import_file');
		var dataForm = new FormData(form); 
		e.preventDefault();
		if(validatorprice.valid()){
			$('#import_btn').attr('disabled', 'disabled');
			$.ajax({
				beforeSend: function(){
					$("#import_btn i").show();
				},
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},      
				type: "POST",
				url : '{{ route('admin.request.mrl-import') }}',
				data : dataForm,
				processData : false, 
				contentType : false,
				dataType : 'json',
				success : function(data){
					console.log(data);
					
					if(data.status == false){
						swal("Error!", data.message, "error");
					} else if(data.status == true){
						swal("Done!", data.message, "success").then(function(){ 
							location.reload();
						});						
					}
				}
			});
		}       
	});
</script>

@endsection
