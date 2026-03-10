<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Utkarsh</title>
    <style>
        body {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }
        
        @media only screen and (max-width: 680px) {
            body,
            table,
            td,
            p,
            a {
                -webkit-text-size-adjust: none !important;
            }
            img {
                height: auto !important;
                
            }
            table {
                width: 100% !important;
            }
            td {
                font-size: 13px !important;
            }
            td.header {
                width: auto !important;
            }
            td.header-heading {
                padding: 49px 0 0 0 !important;
                font-size: 16px !important;
            }
            table.invoice-detail {
                padding: 0 0 !important;
            }
            td.user {
                font-size: 16px !important;
                font-weight: 600 !important;
            }
            td.detail45 {
                width: 30% !important;
            }
            td.detail18 {
                width: 23.33% !important;
            }
        }
		
		
		
		<!--@page {        
			size: a4 landscape;       
		}       
		body {        
			font-family: Times New Roman;        
			text-align: center;        
			border: thin solid black;		
			width:100%;      
		} -->
    </style>
</head>

<body>
<?php
if(count($get_invoice) > 0){
	
	$grand_total = 1;
	foreach($get_invoice as $key=>$invoiceDetail){
		$i = 0;
		$i++;
?>
    <table border="0" align="center" cellpadding="0" cellspacing="0" style="width: 100%; max-width: 100%; min-width: 100%; -webkit-text-size-adjust: 100%; background: #ffffff; color: #000; margin: 0px auto 0px;">
        <tr>
            <td>
                <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="padding:20px 20px 30px 20px;">
                    <tr>
                        <td valign="top">
                            <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="20%">
                                        <table width="100%" align="left" border="0" cellpadding="0" cellspacing="0">
										<tr>
                                            <td align="center" style="color:#000;padding:15px 0 0px 0px">
                                                <a href="javascript:void(0)" target="_blank" style="display:inline-block;"><img src="<?php echo url('/laravel/public/logo.png') ?>" alt="Logo" style="max-width:100px;height:auto;display:block;" /></a>
                                            </td>
										</tr>
                                        </table>
									</td>
									<td width="100%">
                                        <table width="100%" align="left" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="100%" style="color:#000;font-size:16px;font-weight:600;padding-top:20px;padding-bottom:5px;">
                                                   Utkarsh Classes & Edutech Private Limited
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="100%" style="color:#000;font-size:15px;line-height:1.67;padding-bottom:5px;">
                                                   832, Utkarsh Bhawan, Near Mandap Restaurant 9th Chopasni Road, 
                                                </td>
                                            </tr>
											<tr>
                                                <td width="100%" style="color:#000;font-size:15px;line-height:1.67;padding-bottom:5px;">
                                                   Jodhpur-342001, Rajasthan, India
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="100%" style="color:#000;font-size:14px;line-height:1.67;">
                                                 [CIN: U72900RJ2018PTC063026]
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="100%" style="color:#000;font-size:14px;line-height:1.67;">
                                                   GST No.: 08AAFCE2658C1ZT
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="100%" style="color:#000;font-size:14px;line-height:1.67;">
                                                  Customer Care: 0291-2708400
                                                </td>
                                            </tr>
                                             <tr>
                                                <td width="100%" style="color:#000;font-size:14px;line-height:1.67;">
                                                  E-mail: support@utkarsh.com
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="100%" style="color:#000;font-size:14px;line-height:1.67;padding-bottom:10px;">
                                                  Website:<a href=""> www.utkarsh.com</a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    
                                </tr>
                                
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
		
		<tr><td><p>&nbsp;</p></td></tr>
		<tr><td width="100%"><hr></td></tr>
		<tr>
			<td width="50%" style="">
				<table width="100%" align="left" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td style="font-weight: bold;color:#000;font-size:14px;line-height:1.67;padding-bottom:10px;">
							&nbsp;&nbsp;Invoice No: <?=$invoiceDetail->invoice_no?>
						</td>
					</tr>
					<tr>
						<td style="font-weight: bold;color:#000;font-size:14px;line-height:1.67;padding-bottom:10px;">
							&nbsp;&nbsp;Order No: <?=$invoiceDetail->order_number?>
						</td>
					</tr>
					<tr>
						<td style="font-weight: bold;color:#000;font-size:14px;line-height:1.67;padding-bottom:10px;">
							&nbsp;&nbsp;Ref. Id: <?=$invoiceDetail->payment_id?>
						</td>
					</tr>
					<tr>
						<td style="font-weight: bold;color:#000;font-size:14px;line-height:1.67;padding-bottom:10px;">
							&nbsp;&nbsp;Contact No: <?=$invoiceDetail->contact?>
						</td>
					</tr>
				</table>
			</td>
			<td>
				<table width="100%" align="left" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td style="font-weight: bold;color:#000;font-size:14px;line-height:1.67;padding-bottom:10px;">
							Date: <?=$invoiceDetail->c_date?>
						</td>
					</tr>
					<tr>
						<td style="font-weight: bold;color:#000;font-size:14px;line-height:1.67;padding-bottom:10px;">
							Order Date: <?=$invoiceDetail->date?>
						</td>
					</tr>
					<tr>
						<td style="font-weight: bold;color:#000;font-size:14px;line-height:1.67;padding-bottom:10px;">
							E-mail: <?=$invoiceDetail->email?>
						</td>
					</tr>
					<tr>
						<td style="font-weight: bold;color:#000;font-size:14px;line-height:1.67;padding-bottom:10px;">
							Address: <?=$invoiceDetail->state?>
						</td>
					</tr>
				</table>
			</td>  
		</tr>
		<tr><td><p>&nbsp;</p></td></tr>
		<tr><td><p>&nbsp;</p></td></tr>
        <tr>
            <td colspan="2" width="100%">
                <table width="100%" align="center" border="1" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="">
                        <tr>
                            <th style="font-size: 13px;color: #000;padding: 10px 5px;">S.No</th>
                            <th style="font-size: 13px;color: #000;padding: 10px 5px;">Description</th>
                            <th style="font-size: 13px;color: #000;padding: 10px 5px;">Taxable Amount</th>
                            <th style="font-size: 13px;color: #000;padding: 10px 5px;">CGST (9%)</th>
                            <th style="font-size: 13px;color: #000;padding: 10px 5px;">SGST (9%)</th>
                            <th style="font-size: 13px;color: #000;padding: 10px 5px;">IGST (18%)</th>
                            <th style="font-size: 13px;color: #000;padding: 10px 5px;">Total Amount</th>
                        </tr>
                        <tr>
                            <td style="font-size: 12px;color: #000;padding: 10px 5px;font-weight: 700;"><?=$i?></td>
                            <td style="font-size: 12px;color: #000;padding: 10px 5px;font-weight: 700;"><?=$invoiceDetail->description?></td>
                            <td style="font-size: 12px;color: #000;padding: 10px 5px;font-weight: 700;"><?=$invoiceDetail->taxable_amount?></td>
                            <td style="font-size: 12px;color: #000;padding: 10px 5px;font-weight: 700;"><?=$invoiceDetail->cgst?></td>
                            <td style="font-size: 12px;color: #000;padding: 10px 5px;font-weight: 700;"><?=$invoiceDetail->sgst?></td>
                            <td style="font-size: 12px;color: #000;padding: 10px 5px;font-weight: 700;"><?=$invoiceDetail->igst?></td>
                            <td style="font-size: 12px;color: #000;padding: 10px 5px;font-weight: 700;"><?=$invoiceDetail->amount?></td>
                        </tr>
                        <tr>
                            <td style="font-size: 12px;color: #000;padding: 10px 5px;text-align: center;font-weight: 700;" colspan="6">Grand Total</td>
                            <td style="font-size: 12px;color: #000;padding: 10px 5px;font-weight: 700;"><?=$invoiceDetail->amount?></td>
                        </tr>
                </table>
            </td>
        </tr>
		
		<tr><td><p>&nbsp;</p></td></tr>
        <tr>
            <td width="100%">
                <table width="100%" align="left" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="padding:20px 20px 20px 20px;">
                    <tr>
                        <td valign="middle" style="font-size:16px;line-height:1.67;color:#000;font-weight: bold;">
                            Terms & Conditions:
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 12px;color: #000;padding: 5px 0;">
						(a) &nbsp; &nbsp;The terms of this Agreement shall be binding for any further goods/services supplied by Company to Client.
						</td>
                    </tr>
                    <tr>
                        <td style="font-size: 12px;color: #000;padding: 5px 0;">(b) &nbsp; &nbsp; Upon execution of this Agreement, customer is agreeing to pay to Company, the full amount of the Fee.</td>
                    </tr>
                    <tr>
                        <td style="font-size: 12px;color: #000;padding: 5px 0;">(c) &nbsp; &nbsp; If customer does not attend any part of Course for any reason whatsoever, customer will not be entitled to receive a refund. </td>
                    </tr>
                    <tr>
                        <td style="font-size: 12px;color: #000;padding: 5px 0;">(d) &nbsp; &nbsp; Amount is inclusive of all Taxes.</td>
                    </tr>
                    <tr>
                        <td style="font-size: 12px;color: #000;padding: 5px 0;">(e) &nbsp; &nbsp; All disputes are subject to Jodhpur Jurisdiction.</td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
	<?php
	if( count($get_invoice) > 1 && count($get_invoice) > $key+1){
	?>
	<br pagebreak="true"/>
	<?php
	}
	?>
<?php
		}
	}
	?>
	
</body>

</html>