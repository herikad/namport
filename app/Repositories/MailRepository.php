<?php

namespace App\Repositories;

use App\Models\MstEmailTemplate;
use Illuminate\Http\Request;
use Log;
use DB;
use Helper;

class MailRepository {

    public static function replace_email_template($mail_type,$email_data, $receiver_users )
    {
        try{

            \Log::info("email data ". print_r($email_data, true));

            $mail_template_dtls = MstEmailTemplate::where('mail_type', $mail_type)->first();

            if($mail_template_dtls){

                $mail_body_view = $mail_template_dtls->body;

                $mail_body_view = str_replace('[USERNAME]', isset($email_data['user_name']) ? $email_data['user_name'] : '-', $mail_body_view);

                $mail_body_view = str_replace('[BENEFITS]', isset($email_data['benefits']) ? $email_data['benefits'] : '-', $mail_body_view);
                
                $mail_body_view = str_replace('[RULES]', isset($email_data['rules']) ? $email_data['rules'] : '-', $mail_body_view);

                if($mail_template_dtls->mail_type == config('custom.mail_send_types.RESET_PASSWORD_OTP')){

                    $mail_body_view = str_replace('[OTP_CODE]', isset($email_data['otp']) ? $email_data['otp'] : '', $mail_body_view);

                }else if($mail_template_dtls->mail_type == config('custom.mail_send_types.CLUB_OWNER_APPROVAL_UPDATE')){

                    $mail_body_view = str_replace('[MESSAGE]', isset($email_data['message']) ? $email_data['message'] : '-', $mail_body_view);

                }else if($mail_template_dtls->mail_type == config('custom.mail_send_types.SAVE_ORDER')){

                    //order placed email data
                    $mail_body_view = str_replace('[SLOT_DETAIL]', isset($email_data['slot_detail']) ? $email_data['slot_detail'] : '-', $mail_body_view);

                    $mail_body_view = str_replace('[TOTAL]', isset($email_data['total']) ? $email_data['total'] : '-', $mail_body_view);

                    $mail_body_view = str_replace('[DISCOUNT]', isset($email_data['discount_amount']) ? $email_data['discount_amount'] : '-', $mail_body_view);

                    $mail_body_view = str_replace('[SUB_TOTAL]', isset($email_data['subtotal_amount']) ? $email_data['subtotal_amount'] : '-', $mail_body_view);

                }else if($mail_template_dtls->mail_type == config('custom.mail_send_types.ORDER_CANCEL')){

                    $mail_body_view = str_replace('[OrderID]', isset($email_data['OrderID']) ? $email_data['OrderID'] : '-', $mail_body_view);
                    $mail_body_view = str_replace('[CancellationDate]', isset($email_data['CancellationDate']) ? $email_data['CancellationDate'] : '-', $mail_body_view);
                    $mail_body_view = str_replace('[TotalAmount]', isset($email_data['TotalAmount']) ? $email_data['TotalAmount'] : '-', $mail_body_view);

                }else if($mail_template_dtls->mail_type == config('custom.mail_send_types.SUBSCRIPTION_WELCOME_MAIL')){

                    $mail_body_view = str_replace('[subscription_name]', isset($email_data['subscription_name']) ? $email_data['subscription_name'] : '-', $mail_body_view);

                }else if($mail_template_dtls->mail_type == config('custom.mail_send_types.CLUB_MANAGER_WELCOME_MAIL')){

                    $mail_body_view = str_replace('[ConfirmationLink]', isset($email_data['confirmation_link']) ? $email_data['confirmation_link'] : '-', $mail_body_view);

                }else if($mail_template_dtls->mail_type == config('custom.mail_send_types.MEMBER_WELCOME_MAIL')){

                    $mail_body_view = str_replace('[ConfirmationLink]', isset($email_data['confirmation_link']) ? $email_data['confirmation_link'] : '-', $mail_body_view);

                }else if($mail_template_dtls->mail_type == config('custom.mail_send_types.CLUB_PAYOUT_UPDATE')){

                    $mail_body_view = str_replace('[Month]', isset($email_data['month']) ? $email_data['month'] : '-', $mail_body_view);
                    $mail_body_view = str_replace('[ClubName]', isset($email_data['club_name']) ? $email_data['club_name'] : '-', $mail_body_view);
                    $mail_body_view = str_replace('[Currency]', isset($email_data['currency']) ? $email_data['currency'] : '-', $mail_body_view);
                    $mail_body_view = str_replace('[Amount]', isset($email_data['amount']) ? $email_data['amount'] : '-', $mail_body_view);
                    $mail_body_view = str_replace('[PayoutStatus]', isset($email_data['payout_status']) ? $email_data['payout_status'] : '-', $mail_body_view);

                }else if($mail_template_dtls->mail_type == config('custom.mail_send_types.TRAINER_PAYOUT_UPDATE')){

                    $mail_body_view = str_replace('[Month]', isset($email_data['month']) ? $email_data['month'] : '-', $mail_body_view);
                    $mail_body_view = str_replace('[TrainerName]', isset($email_data['trainer_name']) ? $email_data['trainer_name'] : '-', $mail_body_view);
                    $mail_body_view = str_replace('[Currency]', isset($email_data['currency']) ? $email_data['currency'] : '-', $mail_body_view);
                    $mail_body_view = str_replace('[Amount]', isset($email_data['amount']) ? $email_data['amount'] : '-', $mail_body_view);
                    $mail_body_view = str_replace('[PayoutStatus]', isset($email_data['payout_status']) ? $email_data['payout_status'] : '-', $mail_body_view);

                }else if($mail_template_dtls->mail_type == config('custom.mail_send_types.RESEND_VERIFICATION_MAIL')){

                    $mail_body_view = str_replace('[ConfirmationLink]', isset($email_data['confirmation_link']) ? $email_data['confirmation_link'] : '-', $mail_body_view);

                    \Log::info("RESEND_VERIFICATION_MAIL MailRepo ==> ". print_r($email_data, true));

                }

                $mail_body_view = str_replace('[RESET_PASS_LINK]',  isset($email_data['email_token']) ?  url('/password_reset/' .$email_data['email_token']) : '-', $mail_body_view);

                if( isset($email_data['email_token'])){

                    $mail_body_view = str_replace('[RESET_PASS_LINK]', url('/password_reset/' .$email_data['email_token'] ), $mail_body_view );
                }


                // return $mail_body_view;

                // Log::info("mail_body_view  ". print_r($mail_body_view, true));
                \Helper::send_dynamic_mail($mail_template_dtls->email_setting_id , $mail_body_view, $receiver_users, $mail_template_dtls->title ,[] );

            }else{
                return ['status' => 0, 'message' => 'Email template data not found'];
            }

        }catch(\Exception $e){

            Log::info("replace_email_template error  ". print_r($e->getMessage(), true));

            return ['status' => 0, 'message' => 'Something went wrong'];
        }
    }

}
