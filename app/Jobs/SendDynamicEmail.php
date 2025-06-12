<?php

namespace App\Jobs;

use App\Models\MstEmailSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;
use Log;

class SendDynamicEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $view;
    protected $email_users;
    protected $subject;
    protected $files;
    protected $images_tag_url;
    protected $mail_id ;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mail_id , $view, $email, $subject,  $files = [] ,$images_tag_url =[] )
    {
        $this->view = $view;
        $this->email_users = $email ? $email : [];
        $this->subject = $subject;
        $this->files = $files;
        $this->images_tag_url = $images_tag_url ? $images_tag_url : [] ;
        $this->mail_id = $mail_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{

            Log::info("email yes in Job ");

            $email_users = $this->email_users;
            $subject = $this->subject;
            $files = $this->files;
            $view = $this->view ;
            $images_tag_url = $this->images_tag_url ? $this->images_tag_url :[] ;
            $mail_id = $this->mail_id ;

            if(is_array($email_users) && count($email_users)){

                foreach ($email_users as $key => $value) {

                    try {

                        if(!empty($value)){

                            $mail_setting_dtls = MstEmailSetting::where([
                                'email_setting_id' => $mail_id ,
                                'is_active' => 1
                            ])->first();

                            if(!$mail_setting_dtls){

                                $mail_setting_dtls = MstEmailSetting::where([
                                    'is_default' => 1,
                                ])->first();
                            }

                            if($mail_setting_dtls){

                                    config([
                                        'mail.mailers.smtp.host' => $mail_setting_dtls->smtp_host,
                                        'mail.mailers.smtp.port' => $mail_setting_dtls->port,
                                        'mail.mailers.smtp.encryption' => $mail_setting_dtls->smtp_crypto,
                                        'mail.mailers.smtp.username' => $mail_setting_dtls->email_id,
                                        'mail.mailers.smtp.password' => $mail_setting_dtls->password,
                                        'mail.from.address' => $mail_setting_dtls->email_id,
                                    ]);
                            }


                            Mail::send([], [], function($message) use($value, $subject, $files , $view ,$images_tag_url) {


                                foreach ($images_tag_url as $key => $url) {

                                    $view = str_replace($key,  $message->embed($url), $view) ;
                                }

                                // Log::info("view ===> ".print_r( $view,true));
                                $message->to($value)
                                        ->subject($subject)
                                        ->html($view);

                                if(count($files) > 0){
                                    foreach ($files as $file){
                                        $message->attach($file);
                                    }
                                }

                            });
                        }
                        Log::info("email done ");
                    } catch (\Throwable $th) {
                        Log::info("email error ". $th->getMessage());
                    }

                }
            }



        }catch(Exception $exception){
            Log::info("error send_mail ". $exception->getMessage());
        }
    }
}
