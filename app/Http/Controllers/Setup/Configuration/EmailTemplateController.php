<?php

namespace App\Http\Controllers\Setup\Configuration;

use App\Http\Controllers\Controller;
use App\Models\MstEmailSetting;
use App\Models\MstEmailTemplate;
use Illuminate\Http\Request;
use Exception;
use Log;
use DB;
use Validator;
use Helper;

class EmailTemplateController extends Controller
{
    public function mail_template_setting_list_view()
    {
        try {

            return view('pages.setup.configuration.email_temp.mail-template-list');

        } catch (Exception $exception) {

            Log::info("mail_template_setting_list_view_error ". print_r($exception->getMessage(), true));

            return redirect()->back()->with( "message" , trans('pages.something_wrong'));
        }
    }

    public function mail_template_setting_list_json(Request $request)
    {
        try{

            $page_index = (int)$request->input('start') > 0 ? ($request->input('start') / $request->input('length')) + 1 : 1;

            $limit = (int)$request->input('length') > 0 ? $request->input('length') : config('custom.default_records_limit');
            $columnIndex = $request->input('order')[0]['column']; // Column index
            $columnName = $request->input('columns')[$columnIndex]['data']; // Column name
            $columnSortOrder = $request->input('order')[0]['dir']; // asc or desc value

            $main_query = MstEmailTemplate::from('pb_mst_email_template as mail_temp')
                                        ->select('mail_temp.*' ,'mail.email_id')
                                        ->leftJoin('pb_mst_email_setting as mail' ,'mail.email_setting_id' ,'=','mail_temp.email_setting_id')
                                        ->orderBy('email_template_id', $columnSortOrder);

            $data_list_for_count = $main_query->get();  // group by and direct count not working

            $recordsTotal = count($data_list_for_count);
            $recordsFiltered = $recordsTotal;

            if(empty($request->input('search.value'))){

                $appointments = $main_query->paginate($limit, ['*'], 'page', $page_index);

            }else {

                $search = $request->input('search.value');

                $search_query = $main_query->where('mail_temp.mail_send_type', 'LIKE',"%{$search}%")
                                            ->orWhere('mail_temp.title', 'LIKE',"%{$search}%")
                                            ->orWhere('mail_temp.body', 'LIKE',"%{$search}%")
                                            ->orWhere('mail_temp.tag_name', 'LIKE',"%{$search}%")
                                            ->orWhere('mail.email_id', 'LIKE',"%{$search}%");

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

            Log::info("mail_template_setting_list_json_error ". print_r($e->getMessage(), true));

           return response()->json(['status' => 0, 'message' => trans('pages.something_wrong')]);

        }
    }

    public function mail_template_status_update(Request $request)
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

                $email_template_id = $request->id ;
                $mail_temp_setting = MstEmailTemplate::where('email_template_id' , $email_template_id)->first() ;

                if($mail_temp_setting){

                    $mail_temp_setting->is_active = $request->is_active;
                    $mail_temp_setting->save();

                    return response()->json(['status' => 1,  'message' => trans('pages.crud_messages.is_active_update' , ['attr' => 'The Email Template']), 'data' => $request->is_active], 200);

                }else{

                    return response()->json(['status' => 0, "message" =>  trans('pages.crud_messages.no_data' ,['attr' => 'The Email Template'])], 200);
                }

            }
        }
        catch(Exception $exception) {

            return redirect()->back()->with(['status' => 0, "message" => $exception->getMessage()]);


        }
    }

    public function mail_template_create()
    {
        try {

            $mails = MstEmailSetting::select('email_setting_id' ,'email_id' )
                                ->where('is_active' , 1)
                                ->get();

            return view('pages.setup.configuration.email_temp.mail-template-create' ,['mails' => $mails]);

        } catch (Exception $exception) {

            Log::info("mail_template_create_error ". print_r($exception->getMessage(), true));

            return redirect()->back()->with("message" , trans('pages.something_wrong'));
        }
    }

    public function mail_template_edit($id)
    {
        try {
            $mails = MstEmailSetting::where('is_active' , 1)->get();

            $mail_temp_details = MstEmailTemplate::where('email_template_id' , $id)->first();

            return view('pages.setup.configuration.email_temp.mail-template-edit' ,['mail_temp_details' => $mail_temp_details ,'mails' => $mails]);

        } catch (Exception $exception) {

            Log::info("mail_template_edit_error ". print_r($exception->getMessage(), true));

            return redirect()->back()->with("message" , trans('pages.something_wrong'));
        }
    }

    public function store_or_update(Request $request)
    {
        try{
                $validation_rules = [

                    'mail_type' => 'required',
                    'email_setting_id' => 'required',
                    'title' => 'required',
                    'body' => 'required',
                    'tag_name' => 'required',

                ];

                $validator = Validator::make($request->all(), $validation_rules);

                if($validator->fails()) {

                    return redirect()->back()->with('message', implode(',', $validator->messages()->all()) );

                }else{
                        if(isset($request->email_template_id)){

                            $mail_temp_setting = MstEmailTemplate::where('email_template_id' , $request->email_template_id)->first();

                        }else{

                            $mail_temp_setting = new MstEmailTemplate;
                        }

                        $mail_temp_setting->mail_type = $request->mail_type;
                        $mail_temp_setting->email_setting_id = $request->email_setting_id;
                        $mail_temp_setting->title = $request->title;
                        $mail_temp_setting->body = $request->body;
                        $mail_temp_setting->tag_name = $request->tag_name;
                        $mail_temp_setting->save();


                        if($mail_temp_setting){

                            $msg = isset($request->email_template_id) ? trans('pages.crud_messages.updated_success' , ['attr' => 'Email Template']) : trans('pages.crud_messages.added_success' , ['attr' => 'Email Template']);

                            return redirect()->route('setup.config.email_temp.index')->with( 'message', $msg );
                        }
                        else{
                            return redirect()->back()->with( 'message' , 'We are unable to process your request. Please try again later!' );
                        }
                }
        }
        catch(Exception $exception){

            Log::info("store_or_update_error ". print_r($exception->getMessage(), true));

            return redirect()->back()->with(  "message" , trans('pages.something_wrong') );
        }
    }
}
