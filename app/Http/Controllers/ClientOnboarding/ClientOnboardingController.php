<?php

namespace App\Http\Controllers\ClientOnboarding;

use App\Models\ClientContacts;
use App\Models\Department;
use App\Models\Designation;
use App\Models\User;
use App\Models\Term;
use App\Models\UsrRole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Helper;
use Exception;
use Log;
use DB;

class ClientOnboardingController extends Controller
{
   public function client_onboarding_page($profile_link){
        try {
           
            $client_contact_details = ClientContacts::where('profile_link', $profile_link)->first();
            
            if (isset($client_contact_details)) {

                $client_id = $client_contact_details->client_id;

                $data['contact_details'] = $client_contact_details;
                $data['user_details'] = $client_contact_details?->user;
                
                $data['departments'] = Department::where('client_id', $client_id)->where('is_active', 1)->pluck('department_name', 'department_id');
                $data['designations'] = Designation::where('client_id', $client_id)->where('is_active', 1)->pluck('designation_name', 'designation_id');
                $data['gender_term']  = \Helper::get_all_terms_by_category(config('custom.term_category.gender_type'));
                $data['role_term']  = UsrRole::where('pb_usr_role.user_type_term',$data['user_details']->user_type_term)->where('is_active',1)->get(['display_name','role_id']);
                $data['reporting_to'] = ClientContacts::where('client_id', $client_id)->where('client_contacts_id', '!=', $client_contact_details->client_contacts_id)->get(['client_contacts_id','display_name']);
                // dd($data);
                
               return view('frontend.client_onboarding.client_onboarding', $data);
            }

        } catch (\Throwable $th) {
            //throw $th;
        }
   }

   public function save_client_onboarding_page(Request $request){
    dd($request->all());
    try {
            
       

    } catch (\Throwable $th) {
        //throw $th;
    }
   }
}
