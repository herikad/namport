<?php
namespace App\Http\Controllers\ClientOnboarding;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ClientContacts;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Log;
use App\Repositories\MailRepository;
use DB;

class ClientContactController extends Controller
{
    public function index(Request $request)
    {
        try {
            return view('frontend.client_contact.index');
        } catch (\Exception $e) {
            Log::error("ClientContactController index Error : " . $e->getMessage());
        }
    }

    public function contact_list_json(Request $request)
    {

        try {
            $page_index = (int) $request->input('start') > 0 ? ($request->input('start') / $request->input('length')) + 1 : 1;

            $limit = (int) $request->input('length') > 0 ? $request->input('length') : DEFAULT_RECORDS_LIMIT;
            $columnIndex = $request->input('order')[0]['column']; // Column index
            $columnName = $request->input('columns')[$columnIndex]['data']; // Column name
            $columnSortOrder = $request->input('order')[0]['dir']; // asc or desc value

            $user = auth()->user();
            $main_query = ClientContacts::select('mcc.*')
                ->from('mst_client_contacts as mcc')
                ->orderBy($columnName, $columnSortOrder);

            if (empty($request->input('search.value'))) {
                $list = $main_query->paginate($limit, ['*'], 'page', $page_index);
            } else {

                $search = $request->input('search.value');

                $search_query = $main_query->where(function($query) use ($search, $user) {
                    $query->where(function($q) use ($search) {
                        $q->where('mcc.first_name', 'LIKE', "%{$search}%")
                          ->orWhere('mcc.last_name', 'LIKE', "%{$search}%")
                          ->orWhere('mcc.email', 'LIKE', "%{$search}%");
                    });
                });

                $list = $search_query->paginate($limit, ['*'], 'page', $page_index);

            }

            $response = array(
                "draw" => (int) $request->input('draw'),
                "recordsTotal" => $list->total(),
                "recordsFiltered" => $list->total(),
                "data" => $list->getCollection(),
            );

            return response()->json($response, 200);

        } catch (\Exception $e) {
            Log::info('ClientContactController contact_list_json Error : ' . print_r($e->getMessage(), true));
            return response()->json(['status' => 0, 'message' => trans('pages.something_wrong')]);

        }
    }

    public function create(Request $request)
    {
        try {
            return view('frontend.client_contact.create');
        } catch (\Exception $e) {
            Log::error("ClientContactController create Error : " . $e->getMessage());
        }
    }

    public function store(Request $request)
    {

        $validation_rules = [
            'first_name' => 'required|max:30',
            'last_name' => 'required|max:30',
            'email' => 'required',
            'mobile_no' => 'required',
            'id_no' => 'required',
        ];

        $validator = Validator::make($request->all(), $validation_rules);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', implode(',', $validator->messages()->all()));
        } else {

            $auth_user = auth()->user();

            // Start a database transaction for atomicity
            DB::beginTransaction();

            try {
            // 1. Create the User record
            $user = new User();
            $user->user_type_term      = config('custom.user_type_term.client_contact');
            $user->association_type_term = config('custom.user_type_term.client_contact');
            $user->display_name        = $request->first_name . ' ' . $request->last_name;
            $user->email               = $request->email;
            $user->user_name           = $request->email;
            $user->phone               = $request->mobile_no;
            $user->save(); // Save the User record

            Log::info("User created with ID: " . $user->user_id);

            // 2. Create the ClientContact record
            $contact = new ClientContacts();
            $contact->user_id       = $user->user_id;
            $contact->first_name    = $request->first_name;
            $contact->last_name     = $request->last_name;
            $contact->mobile_no     = $request->mobile_no;
            $contact->email         = $request->email;
            $contact->display_name  = $user->display_name;
            $contact->profile_link  = url('/client/onboarding/' . Helper::enc($user->user_id));
            // Generate the unique employee ID using your helper
            $contact->id_no         = $request->id_no;
            $contact->created_by    = $auth_user ? $auth_user->association_id : null;
            $contact->save(); // Save the ClientContact record

            Log::info("ClientContact created with ID: " . $contact->id . " and Employee ID: " . $contact->id_no);

            // 3. Handle Profile Picture Upload
            if ($request->hasFile('profile_pic')) {
                // Assuming \Helper::upload_file returns the full path relative to public_path() or similar
                $profile_pic_path = \Helper::upload_file(
                    $request->file('profile_pic'),
                    'profile_' . rand(10, 10000) . time() . '.' . $request->file('profile_pic')->getClientOriginalExtension(),
                    'images/client_contact/profile' // The directory path within public
                );

                if (!empty($profile_pic_path)) {
                    $contact->profile_pic = $profile_pic_path;
                    $contact->save(); // Save the profile pic path to contact

                    $user->profile_pic = $profile_pic_path;
                    $user->association_id = $contact->client_contacts_id;
                    $user->save();
                    Log::info("Profile picture uploaded and saved for user/contact.");
                } else {
                    Log::warning("Profile picture upload failed for user ID: " . $user->user_id);
                }
            }

            // Commit the transaction if all database operations were successful
            DB::commit();
            Log::info("Database transaction committed successfully for client contact creation.");

            // 4. Send Welcome Email with Setup Link
            if ($user && $contact && $user->user_id) {
                try {
                    $email_data = [
                        'MAIL_SUBJECT' => "Welcome to Namport!", // As discussed in previous response
                        'user_name'    => $user->display_name,
                        // Generate the unique setup URL using the token
                        'LINK_URL'     => url('/client/onboarding/' . Helper::enc($user->user_id)),
                    ];

                    MailRepository::replace_email_template(
                        config("custom.mail_send_types.WELCOME_CLIENT_CONTACT"),
                        $email_data,
                        [$contact->email] // Pass recipient emails as an array
                    );

                    Log::info("Welcome email sent successfully to " . $contact->email . " with setup link.");

                } catch (\Exception $e) {
                    Log::error("ClientContactController store MailRepository Error for user ID " . $user->user_id . ": " . $e->getMessage(), ['exception' => $e]);
                }
            } else {
                Log::warning("Skipping welcome email: User, Contact, or Setup Token is missing after creation.");
            }

            // 5. Redirect on success
            return redirect()->route('client_contacts.index')->with("success", "Client contact created successfully!");

            } catch (\Throwable $th) {
                // Rollback transaction on any error
                DB::rollBack();
                Log::error("ClientContactController store Error for client contact creation: " . $th->getMessage(), ['exception' => $th, 'request' => $request->all()]);
                return redirect()->route('client_contacts.index')->with("error", trans('pages.something_wrong'));
            }
        }
    }

    public function active_status_update(Request $request)
    {
        try{
            $contact = ClientContacts::where('client_contacts_id',$request->client_contacts_id)->first();

            if($contact){

                $contact->is_active = $request->is_active;
                $contact->save();

                return response()->json(['status' => 1,  'message' => "Status updated successfully!.", 'data' => $request->is_active], 200);

            }else{
                return response()->json(['status' => 0, "message" =>  "Data not found!."], 200);
            }
        } catch(\Exception $exception) {
            Log::info("ClientContactController active_status_update Error : ". print_r($exception->getMessage(), true));
            return redirect()->back()->with(['status' => 0,"message" => trans('pages.something_wrong')]);
        }
    }
}
