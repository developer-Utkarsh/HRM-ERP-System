<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CouponMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function build()
    {
        // Default subject and view

        // Customize based on type
        if (isset($this->details['type'])) {
			
            if ($this->details['type'] === 'new_coupon') {
                $subject = 'Coupon Approval Request - ' . $this->details['coupon_title'];
				
				return $this->subject($subject)->view("emails.new_coupon")->with(['details' => $this->details]);
            } 
			elseif ($this->details['type'] === 'remark') {
                $subject = 'Coupon Edit/User Assign Request – Approval Needed - ' . $this->details['coupon_title'];
				
				return $this->subject($subject)->view("emails.remark_coupon")->with(['details' => $this->details]);
            }
			elseif ($this->details['type'] === 'approved_new_coupon') {
                $subject = 'Approved – Proceed with Coupon Creation in ERP - ' . $this->details['coupon_title'];
				
				return $this->subject($subject)->view("emails.approved_coupon")->with(['details' => $this->details]);
            }
			elseif ($this->details['type'] === 'approved_update_coupon') {
                $subject = 'Approved – Coupon Edit/User Assign Request - ' . $this->details['coupon_title'];
				
				return $this->subject($subject)->view("emails.approved_update_coupon")->with(['details' => $this->details]);
            }
			
        }

        return true;
    }
}
