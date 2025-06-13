<?php

namespace App\Http\Controllers\Setup\Configuration;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\MstEmailSetting;
use Illuminate\Http\Request;
use Exception;
use Log;
use DB;
use Validator;
use Helper;

class EmailConfigurationController extends Controller
{
    public function mail_setting_list_view()
    {
        try {

            return view('pages.setup.configuration.email_config.mail-list');

        } catch (Exception $exception) {

            Log::info("mail_setting_list_view_error ". print_r($exception->getMessage(), true));

            return redirect()->back()->with( "message" , trans('pages.something_wrong'));
        }
    }

    public function mail_setting_list_json(Request $request)
    {
        try{

            $page_index = (int)$request->input('start') > 0 ? ($request->input('start') / $request->input('length')) + 1 : 1;

            $limit = (int)$request->input('length') > 0 ? $request->input('length') : config('custom.default_records_limit');
            $columnIndex = $request->input('order')[0]['column']; // Column index
            $columnName = $request->input('columns')[$columnIndex]['data']; // Column name
            $columnSortOrder = $request->input('order')[0]['dir']; // asc or desc value

            $main_query = MstEmailSetting::from('pb_mst_email_setting as mail')->orderBy('email_setting_id', $columnSortOrder);

            $data_list_for_count = $main_query->get();  // group by and direct count not working

            $recordsTotal = count($data_list_for_count);
            $recordsFiltered = $recordsTotal;

            if(empty($request->input('search.value'))){

                $appointments = $main_query->paginate($limit, ['*'], 'page', $page_index);

            }else {

                $search = $request->input('search.value');

                $search_query = $main_query->where('mail.email_id', 'LIKE',"%{$search}%")
                                            ->orWhere('mail.email_protocol', 'LIKE',"%{$search}%")
                                            ->orWhere('mail.smtp_crypto', 'LIKE',"%{$search}%")
                                            ->orWhere('mail.port', 'LIKE',"%{$search}%")
                                            ->orWhere('mail.smtp_host', 'LIKE',"%{$search}%")
                                            ->orWhere('mail.from_name', 'LIKE',"%{$search}%");

                $appointments = $search_query->paginate($limit, ['*'], 'page', $page_index);

                $search_list_for_count = $search_query->get();  // group by and direct count not working

                $recordsFiltered = count($search_list_for_count);

            }

            $response = array(
                "draw" => (int)$request->input('draw'),
                "recordsTotal" => (int)$recordsTotal,
                "recordsFiltered" => (int)$recordsFiltered,
                "data" => $appointments->getCollection()
            );

            return response()->json($response, 200);

        }catch(Exception $e) {

            Log::info("mail_setting_list_json_error ". print_r($e->getMessage(), true));

           return response()->json(['status' => 0, 'message' => trans('pages.something_wrong')]);

        }
    }

    public function mail_status_update(Request $request)
    {
        try{

            $validation_rules = [

                'is_active' => 'required',
                'id' => 'required',

            ];

            $validator = Validator::make($request->all(), $validation_rules);

            if($validator->fails()) {

                return response()->json(['status' => 0, 'message' => implode(',', $validator->messages()->all()) ]);

            }else{

                $email_setting_id = $request->id ;
                $mail_setting = MstEmailSetting::where('email_setting_id' , $email_setting_id)->first() ;

                if($mail_setting){

                    $mail_setting->is_active = $request->is_active;
                    $mail_setting->save();

                    return response()->json(['status' => 1,  'message' => trans('pages.crud_messages.is_active_update' , ['attr' => 'Mail']), 'data' => $request->is_active], 200);

                }else{

                    return response()->json(['status' => 0, "message" =>  trans('pages.crud_messages.no_data' ,['attr' => 'Mail'])], 200);
                }

            }
        }
        catch(Exception $exception) {

            Log::info("mail_status_update_error ". print_r($exception->getMessage(), true));

            return redirect()->back()->with(['status' => 0,"message" => trans('pages.something_wrong')]);


        }
    }

    public function mail_create()
    {
        try {

            return view('pages.setup.configuration.email_config.mail-create');

        } catch (Exception $exception) {

            Log::info("mail_create_error ". print_r($exception->getMessage(), true));

            return redirect()->back()->with( "message" , trans('pages.something_wrong'));
        }
    }

    public function mail_edit($id)
    {
        try {

            $mail_details = MstEmailSetting::where('email_setting_id' , $id)->first();

            return view('pages.setup.configuration.email_config.mail-edit' ,['mail_details' => $mail_details]);

        } catch (Exception $exception) {

            Log::info("mail_edit_error ". print_r($exception->getMessage(), true));

            return redirect()->back()->with("message" , trans('pages.something_wrong'));
        }
    }

    public function store_or_update(Request $request)
    {
        try{

            $validation_rules = [

                'email_id' => 'required',
                'from_name' => 'required',
                'email_protocol' => 'required',
                'smtp_host' => 'required',
                'port' => 'required',
                'smtp_crypto' => 'required',
                'password' => 'required',

            ];

            $validator = Validator::make($request->all(), $validation_rules);

            if($validator->fails()) {

                return redirect()->back()->with( 'message' , implode(',', $validator->messages()->all()) );

            }else{

                if(isset($request->email_setting_id)){

                    $mail_setting = MstEmailSetting::where('email_setting_id' , $request->email_setting_id)->first();

                }else{

                    $mail_setting = new MstEmailSetting;
                }

                $mail_setting->email_id = $request->email_id;
                $mail_setting->from_name = $request->from_name;
                $mail_setting->email_protocol = $request->email_protocol;
                $mail_setting->smtp_host = $request->smtp_host;
                $mail_setting->port = $request->port;
                $mail_setting->smtp_crypto = $request->smtp_crypto;
                $mail_setting->password = $request->password;


                $mail_setting->save();

                if($mail_setting){

                        return redirect()->route('setup.config.email_config.index')->with('message',isset($request->email_setting_id) ?  trans('pages.crud_messages.updated_success' , ['attr' => 'Email Configuration']) : trans('pages.crud_messages.added_success' , ['attr' => 'Email Configuration']));

                }
                else{
                    return redirect()->back()->with('message','We are unable to process your request. Please try again later!');
                }
            }
        }
        catch(Exception $exception){

            Log::info("store_or_update_error ". print_r($exception->getMessage(), true));

            return redirect()->back()->with('message', 'Something went wrong');

        }
    }

}
