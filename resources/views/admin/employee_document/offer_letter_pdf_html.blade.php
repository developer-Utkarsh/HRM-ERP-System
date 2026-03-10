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
	* { font-family: Hind; }
	</style>
</head>
 

<body>         

    <table border="0" align="center" cellpadding="0" cellspacing="0" style="width: 100%; -webkit-text-size-adjust: 100%; background: #ffffff; color: #000; margin: 0px auto 0px; padding: 15px; text-align: left">
        <tr>
            <td valign="top" colspan="2" style="color:#000; font-size:18px; line-height: 24px; font-weight:700; text-align: center;">
                @if($type == 'offer'){{ 'Offer Letter' }}@elseif($type == 'relieving'){{ 'Relieving Letter' }}@else{{ '' }}@endif
            </td>
        </tr>
        <tr><td height="40"></td></tr>
        <tr>
            <td width="70%" valign="top" style="color:#000; font-size:14px; line-height: 20px; font-weight:300;">
                <span style="font-weight: 600;">Utkarsh Classes & Edutech Private Limited</span><br/>
                832, Utkarsh Bhawan, Near Mandap Restaurant 9th Chopasni Road, <br/>
                Jodhpur-342001, Rajasthan, India<br/>
                [CIN: U72900RJ2018PTC063026]<br/>
                GST No.: 08AAFCE2658C1ZT<br/>
                Customer Care: 0291-2708400<br/>
                E-mail: support@utkarsh.com<br/>
                Website:<a href=""> www.utkarsh.com</a>
            </td>
            <td width="30%" valign="top" style="text-align: right;">
                <a href="javascript:void(0)" target="_blank" style="display:inline-block;"><img src="{{ asset('laravel/public/logo.png') }}" alt="Logo" style="max-width:200px;height:auto;display:block;" /></a>
            </td>
        </tr>
        <tr><td height="30"></td></tr>
        <tr>
            <td valign="top" colspan="2">
                <table cellpadding="0" cellspacing="0" width="100%;">
                    <tr>
                        <td valign="top" style="width: 25%; font-size: 12px; line-height: 15px;  font-weight: 600;">Name. : <?=$get_emp_details->name?></td>
                        <td valign="top" style="width: 15%; font-size: 12px; line-height: 15px; font-weight: 300;">&nbsp;</td>
                        <td valign="top" style="width: 30%; font-size: 12px; line-height: 15px;  font-weight: 600;">Father Name. : <?=$get_emp_details->user_details->fname?></td>
                        <td valign="top" style="width: 30%; font-size: 12px; line-height: 15px; font-weight: 300;">&nbsp;</td>
                    </tr>
                    <tr><td height="20"></td></tr>
                    <tr>
                        <td valign="top" style="font-size: 12px; line-height: 15px;  font-weight: 600;">Salary. : <?=$get_emp_details->user_details->net_salary?></td>
                        <td valign="top" style="font-size: 12px; line-height: 15px; font-weight: 300;">&nbsp;</td>
                        <td valign="top" style="font-size: 12px; line-height: 15px;  font-weight: 600;">Degination : <?=$get_emp_details->user_details->degination?></td>
                        <td valign="top" style="font-size: 12px; line-height: 15px; font-weight: 300;">&nbsp;</td>
                    </tr>
                    <tr><td height="20"></td></tr>
                    <tr>
					    @if($type == 'offer')
                        <td valign="top" style="font-size: 12px; line-height: 15px;  font-weight: 600;">Joining Date : <?=date('d-m-Y',strtotime($get_emp_details->user_details->joining_date))?></td>
						@elseif($type == 'relieving')
						<td valign="top" style="font-size: 12px; line-height: 15px;  font-weight: 600;">Relieving Date : <?=date('d-m-Y')?></td>
						@else
						@endif
                        <td valign="top" style="font-size: 12px; line-height: 15px; font-weight: 300;">&nbsp;</td>
                    </tr>
                </table>

            </td>
        </tr>
        <tr><td height="20"></td></tr>
        <tr>
            <td valign="top" colspan="2">
                <table width="100%" align="left" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td valign="middle" style="font-size:16px;line-height:24px; padding-bottom: 10px; color:#000;font-weight: bold;">
                            Terms & Conditions:
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 12px;color: #000;padding: 2px 0;">
                        (a) &nbsp;The terms of this Agreement shall be binding for any further goods/services supplied by Company to Client.
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 12px;color: #000;padding: 2px 0;">(b) &nbsp;Upon execution of this Agreement, customer is agreeing to pay to Company, the full amount of the Fee.</td>
                    </tr>
                    <tr>
                        <td style="font-size: 12px;color: #000;padding: 2px 0;">(c) &nbsp;If customer does not attend any part of Course for any reason whatsoever, customer will not be entitled to receive a refund. </td>
                    </tr>
                    <tr>
                        <td style="font-size: 12px;color: #000;padding: 2px 0;">(d) &nbsp;Amount is inclusive of all Taxes.</td>
                    </tr>
					<tr>
                        <td style="font-size: 12px;color: #000;padding: 2px 0;">(e) &nbsp;All amounts in INR.</td>
                    </tr>
                    <tr>
                        <td style="font-size: 12px;color: #000;padding: 2px 0;">(f) &nbsp;All disputes are subject to Jodhpur Jurisdiction.</td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>