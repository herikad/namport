<?php
namespace App\Http\Controllers\ClientOnboarding;

use App\Http\Controllers\Controller;
use App\Models\ClientContacts;
use App\Models\Department;
use App\Models\Designation;
use App\Models\User;
use App\Models\UsrRole;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Log;

class ClientOnboardingController extends Controller
{

    public function client_onboarding_page($profile_link)
    {
        try {
            $profile_link = Helper::dnc($profile_link);
            $client_contact_details = ClientContacts::where('user_id', $profile_link)->first();
            if (isset($client_contact_details)) {

                $client_id = $client_contact_details->client_id;

                $data['contact_details'] = $client_contact_details;
                $data['user_details']    = $client_contact_details?->user;

                $data['departments']  = Department::where('client_id', $client_id)->where('is_active', 1)->pluck('department_name', 'department_id');
                $data['designations'] = Designation::where('client_id', $client_id)->where('is_active', 1)->pluck('designation_name', 'designation_id');
                $data['gender_term']  = \Helper::get_all_terms_by_category(config('custom.term_category.gender_type'));
                // $data['role_term']    = UsrRole::where('pb_usr_role.user_type_term', $data['user_details']->user_type_term)->where('is_active', 1)->get(['display_name', 'role_id']);
                $data['reporting_to'] = ClientContacts::where('client_id', $client_id)->where('is_active', 1)->where('client_contacts_id', '!=', $client_contact_details->client_contacts_id)->get(['client_contacts_id', 'display_name']);
                $data['profile_link_term'] = config('custom.profile_status_term');

                return view('frontend.client_onboarding.client_onboarding', $data);
            }
        } catch (\Throwable $th) {
            return redirect()->route('login')->with("error", "Sorry, Please contact to admin!");
        }
    }

    public function save_client_onboarding_page(Request $request)
    {
        Log::info("Client Onboarding Save Request: ", $request->all());

        try {

            $user_id     = $request->user_id;
            $base64Audio = $request->input('voice_profile');

            if ($base64Audio && str_starts_with($base64Audio, 'data:audio')) {
                [$metadata, $base64Data] = explode(',', $base64Audio);
                preg_match('/^data:audio\/(\w+);base64$/', $metadata, $matches);
                $extension = $matches[1] ?? 'webm';

                $decoded  = base64_decode($base64Data);
                $filename = 'voice_' . time() . '.' . $extension;
                $path     = 'images/client_contact/voice_profile/' . $filename;

                Storage::disk(config('filesystems.default'))->put($path, $decoded);
                $publicPath = Storage::url($path);
                // Storage::put($path, $decoded);
                // $publicPath = Storage::url('audio/' . $filename);

            }

            if ($request->hasFile('profile_pic')) {

                $old_data = ClientContacts::where('user_id', $user_id)->first();

                $image_path     = ('images/client_contact/profile' . $old_data->profile_pic); // prev image path
                $is_image_exist = Storage::disk(config('filesystems.default'))->exists($image_path);
                if ($is_image_exist) {
                    \Helper::deleteFile($image_path);
                }

                $file            = $request->file('profile_pic');
                $image_name      = 'profile_'. time() . "." . $file->getClientOriginalExtension();
                $destinationPath = ('images/client_contact/profile');

                \Helper::upload_file($request->file('profile_pic'), $image_name, $destinationPath);

                ClientContacts::where("user_id", $user_id)->update([
                    'profile_pic' => $image_name,
                ]);

                User::where('user_id', $user_id)->update([
                    'profile_pic' => $image_name,
                ]);

            }

            $user = User::where('user_id', $request->user_id)->first();

            if ($user) {
                $user->email            = $request->email;
                $user->phone            = $request->mobile_no;
                $user->gender_type_term = $request->gender_type_term;
                $user->updated_at       = now();

                $user->save();
            }
            $clientContact = ClientContacts::where('user_id', $request->user_id)->first();

            if ($clientContact) {
                $clientContact->first_name      = $request->first_name;
                $clientContact->last_name       = $request->last_name;
                $clientContact->display_name    = $request->first_name . ' ' . $request->last_name;
                $clientContact->mobile_no       = $request->mobile_no;
                $clientContact->email           = $request->email;
                $clientContact->gender_term     = $request->gender_type_term;
                $clientContact->department      = $request->department;
                $clientContact->designation     = $request->designation;
                $clientContact->reporting_to    = $request->reporting_to;
                $clientContact->date_of_joining = $request->date_of_joining;
                $clientContact->status_term     = $request->status_term ?? 'Active';
                $clientContact->responsibilities = $request->responsibilities ?? '';
                $clientContact->role            = $request->role;
                $clientContact->updated_at      = now();
                $clientContact->profile_status_term  = config('custom.profile_progress_term.done'); // 'done' if the voice_profile recorded
                $clientContact->save();
            }

            return redirect()->back()->with('success', 'Profile updated successfully.');

        } catch (\Throwable $e) {
            Log::error("Onboarding Save Failed: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'status'  => false,
                'message' => 'Server Error',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
