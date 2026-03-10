<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0,minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<title>Utkarsh</title>
	<style>
	@font-face {
		font-family: Hind;
		font-style: normal;
		font-weight: normal;
		src: url(<?php echo url('/laravel/public/Hind/Hind-Regular.ttf') ?>) format('truetype');
	}
	
	* {
		font-family: Hind;
	}
	</style>
</head>

<body>
	<table cellpadding="0" cellspacing="0" style="border:solid 1px;width: 100%; -webkit-text-size-adjust: 100%; background: #ffffff; color: #000; margin: 0px auto 0px;">
		<!--tr>
			<td valign="top" colspan="2" style="color:#000; font-size:18px; line-height: 24px; font-weight:700; text-align: center;"> Salary Slip </td>
		</tr>
		<tr>
			<td height="40"></td>
		</tr-->
		<tr>
			<td width="20%" valign="top" style="text-align: left;padding: 10px 0px 0px 10px;">
				<a href="javascript:void(0)" target="_blank" style="display:inline-block;"><img src="{{ asset('/laravel/public/logo.png') }}" alt="Logo" style="max-width:100px;height:auto;display:block;" /></a>
			</td>
			<td width="80%" valign="top" style="color:#000; font-size:14px; line-height: 20px; font-weight:300;text-align: right;padding: 10px 10px 0px 0px;"> <span style="font-weight: 600;">Utkarsh Classes & Edutech Private Limited</span>
				<br/> Address : Nehal Tower,City Shopping Centre, <br> Krishi Mandi Road, Saraswati Nagar, <br>Jodhpur, Rajasthan 342005                                                                    
				<br/> Website:<a href=""> www.utkarsh.com</a>
			</td>
			
		</tr>
		<tr>
			<td valign="middle" colspan="2"><hr style="background:#000; height:1px;"></td>
		</tr>
		<tr>
			<td valign="top" colspan="2">
				<table cellpadding="0" cellspacing="0" width="100%;" style="padding:10px 10px 0px 10px;">
					<tr>
						<td valign="top" style="width: 22%; font-size: 12px; line-height: 15px;font-weight: 600;">Employee Code :</td>
						<td valign="top" style="width: 2%; font-size: 12px; line-height: 15px; font-weight: 300;">&nbsp;</td>
						<td valign="top" style="width: 22%; font-size: 12px; line-height: 15px;"> <?= !empty($responseImportArray) ? $responseImportArray[0]['register_id'] : '--'; ?></td>

						<td valign="top" style="width: 8%; font-size: 12px; line-height: 15px; font-weight: 300;">&nbsp;</td>
						
						<td valign="top" style="width: 26%; font-size: 12px; line-height: 15px;font-weight: 600;">Salary Slip for the Month of :</td>
						<td valign="top" style="width: 2%; font-size: 12px; line-height: 15px; font-weight: 300; text-align:right;">&nbsp;</td>
						<td valign="top" style="width: 18%; font-size: 12px; line-height: 15px; text-align:right;"><?= !empty($date_salary) ? date('M-Y', strtotime($date_salary.'-01')) : '--'; ?></td>
					</tr>
					<tr>
						<td height="10"></td>
					</tr>


					<tr>
						<td valign="top" style="width: 22%; font-size: 12px; line-height: 15px;font-weight: 600;">Employee Name :</td>
						<td valign="top" style="width: 2%; font-size: 12px; line-height: 15px; font-weight: 300;">&nbsp;</td>
						<td valign="top" style="width: 22%; font-size: 12px; line-height: 15px;  "> <?= !empty($responseImportArray) ? $responseImportArray[0]['name'] : '--'; ?></td>

						<td valign="top" style="width: 8%; font-size: 12px; line-height: 15px; font-weight: 300;">&nbsp;</td>
						
						<td valign="top" style="width: 22%; font-size: 12px; line-height: 15px;font-weight: 600;">Paid Days :</td>
						<td valign="top" style="width: 2%; font-size: 12px; line-height: 15px; font-weight: 300; text-align:right;">&nbsp;</td>
						<td valign="top" style="width: 22%; font-size: 12px; line-height: 15px;   text-align:right;"><?= !empty($responseImportArray) ? $responseImportArray[0]['paid_day'] : '--'; ?></td>
					</tr>
					<tr>
						<td height="10"></td>
					</tr>

					<tr>
						<td valign="top" style="width: 22%; font-size: 12px; line-height: 15px;  font-weight: 600;">Designation :</td>
						<td valign="top" style="width: 2%; font-size: 12px; line-height: 15px; font-weight: 300;">&nbsp;</td>
						<td valign="top" style="width: 22%; font-size: 12px; line-height: 15px;"> <?= !empty($responseImportArray) ? $responseImportArray[0]['designation_name'] : '--'; ?></td>

						<td valign="top" style="width: 8%; font-size: 12px; line-height: 15px; font-weight: 300;">&nbsp;</td>
						
						<td valign="top" style="width: 22%; font-size: 12px; line-height: 15px; font-weight: 600;">Arrear Days :</td>
						<td valign="top" style="width: 2%; font-size: 12px; line-height: 15px; font-weight: 300; text-align:right;">&nbsp;</td>
						<td valign="top" style="width: 22%; font-size: 12px; line-height: 15px; text-align:right;"><?= !empty($responseImportArray) ? $responseImportArray[0]['last_month_pending_sunday'] : '--'; ?></td>
					</tr>
					<tr>
						<td height="10"></td>
					</tr>

					<tr>
						<td valign="top" style="width: 22%; font-size: 12px; line-height: 15px;font-weight: 600;">Department :</td>
						<td valign="top" style="width: 2%; font-size: 12px; line-height: 15px; font-weight: 300;">&nbsp;</td>
						<td valign="top" style="width: 22%; font-size: 12px; line-height: 15px;  "> <?= !empty($responseImportArray) ? $responseImportArray[0]['departments_name'] : '--'; ?></td>

						<td valign="top" style="width: 8%; font-size: 12px; line-height: 15px; font-weight: 300;">&nbsp;</td>
						
						<td valign="top" style="width: 22%; font-size: 12px; line-height: 15px;font-weight: 600;">UAN Number :</td>
						<td valign="top" style="width: 2%; font-size: 12px; line-height: 15px; font-weight: 300; text-align:right;">&nbsp;</td>
						<td valign="top" style="width: 22%; font-size: 12px; line-height: 15px;   text-align:right;"><?= !empty($responseImportArray) ? $responseImportArray[0]['uan_no'] : '--'; ?></td>
					</tr>
					<tr>
						<td height="10"></td>
					</tr>

					<tr>
						<td valign="top" style="width: 22%; font-size: 12px; line-height: 15px;font-weight: 600;">DOB :</td>
						<td valign="top" style="width: 2%; font-size: 12px; line-height: 15px; font-weight: 300;">&nbsp;</td>
						<td valign="top" style="width: 22%; font-size: 12px; line-height: 15px;  "> <?= !empty($responseImportArray) ? date('d-M-Y', strtotime($responseImportArray[0]['dob'])) : '--'; ?></td>

						<td valign="top" style="width: 8%; font-size: 12px; line-height: 15px; font-weight: 300;">&nbsp;</td>
						
						<td valign="top" style="width: 22%; font-size: 12px; line-height: 15px;font-weight: 600;">ESIC Number :</td>
						<td valign="top" style="width: 2%; font-size: 12px; line-height: 15px; font-weight: 300; text-align:right;">&nbsp;</td>
						<td valign="top" style="width: 22%; font-size: 12px; line-height: 15px;text-align:right;"><?= !empty($responseImportArray) ? $responseImportArray[0]['esic_no'] : '--'; ?></td>
					</tr>
					<tr>
						<td height="10"></td>
					</tr>

					<tr>
						<td valign="top" style="width: 22%; font-size: 12px; line-height: 15px;font-weight: 600;">DOJ :</td>
						<td valign="top" style="width: 2%; font-size: 12px; line-height: 15px; font-weight: 300;">&nbsp;</td>
						<td valign="top" style="width: 22%; font-size: 12px; line-height: 15px;  "> <?= !empty($responseImportArray) ? date('d-M-Y', strtotime($responseImportArray[0]['joining_date'])) : '--'; ?></td>

						<td valign="top" style="width: 8%; font-size: 12px; line-height: 15px; font-weight: 300;">&nbsp;</td>
						
						<td valign="top" style="width: 22%; font-size: 12px; line-height: 15px;font-weight: 600;">Account Number :</td>
						<td valign="top" style="width: 2%; font-size: 12px; line-height: 15px; font-weight: 300; text-align:right;">&nbsp;</td>
						<td valign="top" style="width: 22%; font-size: 12px; line-height: 15px;text-align:right;"><?= !empty($responseImportArray) ? $responseImportArray[0]['account_number'] : '--'; ?></td>
					</tr>
					<tr>
						<td height="10"></td>
					</tr>

					<tr>
						<td valign="top" style="width: 22%; font-size: 12px; line-height: 15px;font-weight: 600;">Job Location :</td>
						<td valign="top" style="width: 2%; font-size: 12px; line-height: 15px; font-weight: 300;">&nbsp;</td>
						<td valign="top" style="width: 22%; font-size: 12px; line-height: 15px;"> <?= !empty($responseImportArray) ? $responseImportArray[0]['branch_location'] : '--'; ?></td>

						<td valign="top" style="width: 8%; font-size: 12px; line-height: 15px; font-weight: 300;">&nbsp;</td>
						
						<td valign="top" style="width: 22%; font-size: 12px; line-height: 15px;font-weight: 600;">PAN Number :</td>
						<td valign="top" style="width: 2%; font-size: 12px; line-height: 15px; font-weight: 300; text-align:right;">&nbsp;</td>
						<td valign="top" style="width: 22%; font-size: 12px; line-height: 15px;text-align:right;"> <?= !empty($responseImportArray) ? $responseImportArray[0]['pan_no'] : '--'; ?></td>
					</tr>
					<tr>
						<td height="10"></td>
					</tr>

				
				</table>
			</td>
		</tr>
		<tr>
			<td height="20"></td>
		</tr>
		<tr>
			<td valign="top" colspan="2">
				<!--table width="100%" border="1" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="padding: 15px"-->
				<table border="1" cellpadding="2" cellspacing="0" style="width: 98%; -webkit-text-size-adjust: 100%; background: #ffffff; color: #000; margin: 1%;">
					<tr>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;text-align:left;" colspan="5">Earning Head</th>						
						<th style="font-size: 13px;color: #000;padding: 10px 5px;text-align:left;" colspan="4">Deduction Head</th>
					</tr>

					<tr>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;text-align:left;" colspan="3">Earning</th>						
						<th style="font-size: 13px;color: #000;padding: 10px 5px;text-align:right;">Actual</th>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;text-align:right;">Paid</th>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;text-align:left;" colspan="3">Deduction</th>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;text-align:right;">Rs</th>
					</tr>

					<tr>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;text-align:left;" colspan="3">Basic Salary</th>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;text-align:right;"><?= !empty($responseImportArray) ? $responseImportArray[0]['new_basic'] : '0'; ?></th>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;text-align:right;"><?= !empty($responseImportArray) ? $responseImportArray[0]['new_basic'] : '0'; ?></th>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;text-align:left;" colspan="3">PF</th>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;text-align:right;"><?= !empty($responseImportArray) ? $responseImportArray[0]['pf_amount'] : '0'; ?></th>
					</tr>

					<tr>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;text-align:left;" colspan="3">HRA</th>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;text-align:right;"><?= !empty($responseImportArray) ? '--' : '--'; ?></th>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;text-align:right;">--</th>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;text-align:left;" colspan="3">E.S.I.C.</th>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;text-align:right;"><?= !empty($responseImportArray) ? $responseImportArray[0]['esi_amount'] : '0'; ?></th>
					</tr>

					<tr>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;text-align:left;" colspan="3">Conveyance Allowance</th>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;text-align:right;">--</th>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;text-align:right;">--</th>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;text-align:left;" colspan="3">Loan/Advance</th>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;text-align:right;"><?= !empty($responseImportArray) ? $responseImportArray[0]['loan_amount'] : '0'; ?></th>
					</tr>

					<tr>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;text-align:left;" colspan="3">Arrear/Incentives</th>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;;text-align:right;"><?= !empty($responseImportArray) ? $responseImportArray[0]['incentive'] + $responseImportArray[0]['arrear'] : '0'; ?></th>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;text-align:right;"><?= !empty($responseImportArray) ?  $responseImportArray[0]['incentive'] + $responseImportArray[0]['arrear'] : '0'; ?></th>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;text-align:left;" colspan="3">TDS</th>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;text-align:right;"><?= !empty($responseImportArray) ? $responseImportArray[0]['tds_amount'] : '0'; ?></th>
					</tr>

					<tr>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;font-weight: 600;text-align:left;" colspan="3">Gross Salary</th>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;font-weight: 600;"></th>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;font-weight: 600;text-align:right;"><?= !empty($responseImportArray) ? $responseImportArray[0]['gross_salary'] : '0'; ?></th>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;font-weight: 600;text-align:left;" colspan="3">Total Dedutions</th>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;font-weight: 600;text-align:right;"><?= !empty($responseImportArray) ? ($responseImportArray[0]['pf_amount'] + $responseImportArray[0]['esi_amount'] + $responseImportArray[0]['loan_amount'] + $responseImportArray[0]['tds_amount']) : '0'; ?></th>
					</tr>

					<tr>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;font-weight: 600;text-align:left;" colspan="3">Net Salary</th>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;font-weight: 600;"></th>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;font-weight: 600;text-align:right;"><?= !empty($responseImportArray) ? $responseImportArray[0]['final_amount'] : ''; ?></th>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;font-weight: 600;" colspan="3"></th>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;font-weight: 600;"></th>
					</tr>

					<tr>
						<th style="font-size: 13px;color: #000;padding: 10px 5px;font-weight: 600; text-align:left" colspan="9">Salary In Word - <?= !empty($responseImportArray) ? $responseImportArray[0]['word_net_salary'] : ''; ?></th>
					</tr>
				
				</table>
			</td>
		</tr>
		<tr>
			<td height="10"></td>
		</tr>
		<tr>
			<td valign="top" colspan="2" style="padding:0px 10px;">
				<table width="100%" align="left" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td style="font-size: 12px;color: #000;padding: 2px 0;"> * This is Computer generated Slip does not require signature. </td>
					</tr>
					<tr>
						<td style="font-size: 12px;color: #000;padding: 2px 0;"> * Contact us for any queries- hr@utkarsh.com </td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
<!--style>
	body {
			font-family : 'Arial';
		}

	.table1 {
	  border-collapse: collapse;
	}

	.table1 td, .table1 th, .table2 th {
	  border: 1px solid #dee2e6;
	}

	.table1 tr:nth-child(even) {
	  //background-color: #ff9f43;
	}
	@media print {
		
	  @page {size: auto; margin: 0; }
	  body { margin: 1.6cm; }
	  
	}
    </style>
</html>

<script src="{{ asset('laravel/public/admin/js/vendors.min.js') }}"></script>
<script>

$(document).ready(function () { 
	window.print(); 
});
</script-->
</html>