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

                $mail_body_view = str_replace('[URL]', isset($email_data['LINK_URL']) ? $email_data['LINK_URL'] : '-', $mail_body_view);

                if($mail_template_dtls->mail_type == config('custom.mail_send_types.RESET_PASSWORD_OTP')){

                    $mail_body_view = str_replace('[OTP_CODE]', isset($email_data['otp']) ? $email_data['otp'] : '', $mail_body_view);

                } else if($mail_template_dtls->mail_type == config('custom.mail_send_types.RESEND_VERIFICATION_MAIL')){

                    $mail_body_view = str_replace('[ConfirmationLink]', isset($email_data['confirmation_link']) ? $email_data['confirmation_link'] : '-', $mail_body_view);

                    \Log::info("RESEND_VERIFICATION_MAIL MailRepo ==> ". print_r($email_data, true));

                }

                $mail_body_view = str_replace('[RESET_PASS_LINK]',  isset($email_data['email_token']) ?  url('/password_reset/' .$email_data['email_token']) : '-', $mail_body_view);

                if( isset($email_data['email_token'])){

                    $mail_body_view = str_replace('[RESET_PASS_LINK]', url('/password_reset/' .$email_data['email_token'] ), $mail_body_view );
                }

                if(isset($email_data['MAIL_SUBJECT'])){
                  $subject = $email_data['MAIL_SUBJECT'];
                }else{
                  $subject = $mail_template_dtls->title;
                }

                $media_files = [];

                if (isset($email_data['MEDIA_FILES']) && is_array($email_data['MEDIA_FILES'])) {
                    foreach ($email_data['MEDIA_FILES'] as $file) {
                        $media_files[] = $file;
                    }
                }

                Log::info("Before helper send_dynamic_mail() media_files ". print_r($media_files, true));

                \Helper::send_dynamic_mail($mail_template_dtls->email_setting_id , $mail_body_view, $receiver_users, $subject,$media_files);

            }else{
                return ['status' => 0, 'message' => 'Email template data not found'];
            }

        }catch(\Exception $e){

            Log::info("replace_email_template error  ". print_r($e->getMessage(), true));

            return ['status' => 0, 'message' => 'Something went wrong'];
        }
    }

}
