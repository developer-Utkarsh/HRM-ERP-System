<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
    </style>
</head>

<body>
    
    <?php if(!empty($invoice_report)){ ?>	
    <table border="0" align="center" cellpadding="0" cellspacing="0" style="width: 100%; max-width: 100%; min-width: 100%; -webkit-text-size-adjust: 100%; background: #ffffff; color: #000; margin: 0px auto 0px;">
        <tr>
		
            <td>
                <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0" bgcolor="#fbfcff" style="padding:20px 20px 30px 20px;">
                    <tr>
                        <td valign="top">
                            <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
                                
                                <tr> 
                                    <td width="30%" style="margin:20px">
                                        <table align="left" border="0" cellpadding="0" cellspacing="0">
											<tr>
												<td colspan="2" width="100%" align="center" style="color:#000;padding:15px 0px 0px 0px">
													<a href="javascript:void(0)" target="_blank" style="display:inline-block;"><img src="<?php echo asset('laravel/public/logo.png'); ?>" alt="Logo" style="max-width:100px;height:auto;display:block;"></a>
												</td>
											</tr>
                                        </table>
										
									</td>
									<td width="68%">
									
                                        <table width="80%" align="left" border="0" cellpadding="0" cellspacing="0">
                                             <tr>
                                                <td colspan="2" style="color:#000;font-size:16px;font-weight:600;padding-top:20px;padding-bottom:5px;">
                                                   Utkarsh Classes & Edutech Private Limited
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="100%" style="color:#000;font-size:15px;line-height:1.67;padding-bottom:5px;">
                                                   832, Utkarsh Bhawan, Near Mandap Restaurant 9th Chopasni Road, Jodhpur-342001, Rajasthan, India
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
									<td width="2%">
									<form method="POST" action="">
										
										
										<a href="javascript:void(0)" id="download_pdf"><span class="action-edit"><i class="fa fa-cloud-download" style="font-size:36px"></i></span></a>
										<input type="hidden" name="invoice_id" class="invoice_id" value="<?= !empty($invoice_report->id) ? $invoice_report->id : '';?>">
									</form>
									</td>
                                    
                                </tr>
                                <?php //echo '<pre>'; print_r($invoice_report);die; ?>
                                <tr>
                                    <td colspan="2" style="font-weight: bold;color:#000;font-size:14px;line-height:1.67;padding-bottom:10px;">
                                        <table width="50%" align="left" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="font-weight: bold;color:#000;font-size:14px;line-height:1.67;padding-bottom:10px;">
                                                     Credit Note No.: <?= !empty($invoice_report->invoice_no) ? $invoice_report->invoice_no : '';?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: bold;color:#000;font-size:14px;line-height:1.67;padding-bottom:10px;">
                                                     Original Invoice No.: <?= !empty($invoice_report->order_number) ? $invoice_report->order_number : '';?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: bold;color:#000;font-size:14px;line-height:1.67;padding-bottom:10px;">
                                                     Ref. Id: <?= !empty($invoice_report->payment_id) ? $invoice_report->payment_id : '';?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: bold;color:#000;font-size:14px;line-height:1.67;padding-bottom:10px;">
                                                     Contact No: <?= !empty($invoice_report->contact) ? $invoice_report->contact : '';?>
                                                </td>
                                            </tr>
                                        </table>
                                        <table width="50%" align="left" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="font-weight: bold;color:#000;font-size:14px;line-height:1.67;padding-bottom:10px;">
                                                     Credit Note Date: <?= !empty($invoice_report->c_date) ? $invoice_report->c_date : '';?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: bold;color:#000;font-size:14px;line-height:1.67;padding-bottom:10px;">
                                                     Original Invoice Date: <?= !empty($invoice_report->date) ? $invoice_report->date : '';?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: bold;color:#000;font-size:14px;line-height:1.67;padding-bottom:10px;">
                                                       E-mail: <?= !empty($invoice_report->email) ? $invoice_report->email : '';?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: bold;color:#000;font-size:14px;line-height:1.67;padding-bottom:10px;">
                                                     Address: <?= !empty($invoice_report->state) ? $invoice_report->state : '';?>
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
        <tr>
            <td>
                <table width="100%" align="center" border="1" cellpadding="0" cellspacing="0" bgcolor="#fbfcff" style="border-right: 0px;border-left: 0px">
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
                            <td style="font-size: 12px;color: #000;padding: 10px 5px;font-weight: 700;">1</td>
                            <td style="font-size: 12px;color: #000;padding: 10px 5px;font-weight: 700;"><?= !empty($invoice_report->description) ? $invoice_report->description : '';?></td>
                            <td style="font-size: 12px;color: #000;padding: 10px 5px;font-weight: 700;"><?= !empty($invoice_report->taxable_amount) ? $invoice_report->taxable_amount : '';?></td>
                            <td style="font-size: 12px;color: #000;padding: 10px 5px;font-weight: 700;"><?= !empty($invoice_report->cgst) ? $invoice_report->cgst : '';?></td>
                            <td style="font-size: 12px;color: #000;padding: 10px 5px;font-weight: 700;"><?= !empty($invoice_report->sgst) ? $invoice_report->sgst : '';?></td>
                            <td style="font-size: 12px;color: #000;padding: 10px 5px;font-weight: 700;"><?= !empty($invoice_report->igst) ? $invoice_report->igst : '';?></td>
                            <td style="font-size: 12px;color: #000;padding: 10px 5px;font-weight: 700;"><?= !empty($invoice_report->amount) ? $invoice_report->amount : '';?></td>
                        </tr>
                        <tr>
                            <td style="font-size: 12px;color: #000;padding: 10px 5px;text-align: center;font-weight: 700;" colspan="6">Grand Total</td>
                            <td style="font-size: 12px;color: #000;padding: 10px 5px;font-weight: 700;"><?= !empty($invoice_report->amount) ? $invoice_report->amount : '';?></td>
                        </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0" bgcolor="#f9f9ff" style="padding:20px 20px 20px 20px;">
                    <tr>
                        <td valign="middle" style="font-size:16px;line-height:1.67;color:#000;font-weight: bold;">
                            Terms & Conditions:
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 12px;color: #000;padding: 5px 0;">(a) &nbsp; &nbsp;The terms of this Agreement shall be binding for any further goods/services supplied by Company to Client.</td>
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
	<?php }else{ ?>
	<p>oops, something is wrong!</p>
	<?php } ?>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$("body").on("click", "#download_pdf", function (e) { 

	var data = {};

		// data.invoice_id = $('.invoice_id').val(),
		var invoice_id = $('.invoice_id').val();
		window.location.href = "<?php echo url('/') ?>/invoice-report-pdf-credit/"+invoice_id;

	/* window.location.href = "download/" + Object.keys(data).map(function (k) {

		return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])

	}).join('&'); */

});
</script>
</html>