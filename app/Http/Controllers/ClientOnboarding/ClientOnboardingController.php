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
use Illuminate\Support\Facades\Storage;

class ClientOnboardingController extends Controller
{
    
   public function client_onboarding_page($profile_link){
    //  dd(Storage::disk(config('filesystems.default'))->url('images/client\client_1749383831.png' ));
    // dd(asset('images/client/client_1749383831.png'));
    //  C:\xampp\htdocs\Namport\namport\public\images\client\client_1749383831.png
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
    
    try {

          $user_id = $request->user_id;
            $base64Audio = $request->input('voice_profile');

            if ($base64Audio && str_starts_with($base64Audio, 'data:audio')) {
                [$metadata, $base64Data] = explode(',', $base64Audio);
                preg_match('/^data:audio\/(\w+);base64$/', $metadata, $matches);
                $extension = $matches[1] ?? 'webm';

                $decoded = base64_decode($base64Data);
                $filename = 'voice_' . time() . '.' . $extension;
                $path = 'public/audio/' . $filename;

                Storage::disk('public')->put($path, $decoded);
                $publicPath = Storage::url($path); 
                // Storage::put($path, $decoded);
                // $publicPath = Storage::url('audio/' . $filename);

            }

          if ($request->hasFile('profile_pic')) {

                $old_data = ClientContacts::where('user_id', $user_id)->first();

                $image_path = ('images/' . $old_data->profile_pic); // prev image path
                $is_image_exist = Storage::disk(config('filesystems.default'))->exists($image_path);
                if($is_image_exist){
                    \Helper::deleteFile($image_path);
                }

                $file = $request->file('profile_pic');
                $image_name = 'client' . '_' . time() . "." . $file->getClientOriginalExtension();
                $destinationPath = ('images/client');
                
                Helper::upload_file($request->file('profile_pic'), $image_name, $destinationPath);

                $image_name = 'client'.'/'.$image_name;

                ClientContacts::where("user_id", $user_id)->update(array(
                    'profile_pic' => $image_name,
                ));

                User::where('user_id', $user_id)->update(array(
                    'profile_pic' => $image_name,
                ));

            }

            
            $user = User::where('user_id', $request->user_id)->first();

            if ($user) {
                $user->email = $request->email;
                $user->phone = $request->mobile_no;
                $user->gender_type_term = $request->gender_type_term;
                $user->updated_at = now();

                $user->save();
            }
            $clientContact = ClientContacts::where('user_id', $request->user_id)->first();

            if ($clientContact) {
                $clientContact->first_name = $request->first_name;
                $clientContact->last_name = $request->last_name;
                $clientContact->mobile_no = $request->mobile_no;
                $clientContact->email = $request->email;
                $clientContact->gender_term = $request->gender_type_term;
                $clientContact->department = $request->department;
                $clientContact->designation = $request->designation;
                $clientContact->reporting_to = $request->reporting_to;
                $clientContact->date_of_joining = $request->date_of_joining;
                $clientContact->status_term = $request->status_term ?? 'Active';
                $clientContact->description = $request->description ?? '';
                $clientContact->role = $request->role;
                $clientContact->updated_at = now();
                $clientContact->save();
            } 
            
            return redirect()->back()->with('success', 'Profile updated successfully.');


       

    } catch (\Throwable $e) {
        Log::error("Onboarding Save Failed: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return response()->json([
            'status' => false,
            'message' => 'Server Error',
            'error' => $e->getMessage()
        ], 500);
    }
   }
}
