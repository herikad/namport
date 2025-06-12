<?php

namespace App\Http\Controllers\Setup\Configuration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Term;
use App\Models\TermCategory;

use App\Models\User;
use Helper;
use Exception;
use Log;
use DB;

class GeneralSettingController extends Controller
{

  public static function get_all_term_categories_and_terms(){

    $term_categories = TermCategory::where('is_active', 1)->get();

    if(count($term_categories) > 0){
        foreach ($term_categories as $key => $value) {
            $term_categories[$key]['data'] = Term::where('term_category_id', $value->term_category_id)->where('is_active', 1)->get();
        }
    }
    return $term_categories;

  }
  public function index(){

    // dd(isset(auth()->user()->client_id) ? auth()->user()->client_id : null);
    $term_categories = Self::get_all_term_categories_and_terms();

    return view('pages.setup.configuration.general_setting.index',compact('term_categories'));

  }

  public function store(Request $request)
  {
      try{

          if(!empty($request->term_id)){

              $term = Term::find($request->term_id);

              if (is_null($term)) {

                  return response()->json(['status' => 0, 'message' => trans('pages.crud_messages.no_data', [ 'attr' => 'Term Type'])]);

              }

              $term->term_code = $request->term_code;
              $term->term_name = $request->term_name;
              $term->term_details = $request->term_details;

              if ($request->hasFile('term_icon')){

                \Helper::deleteFile($term->term_icon);
                $file = $request->file('term_icon');
                $file_name_to_store = time();
                $file_uploaded_path = 'assets/img/term';

                $file_path = \Helper::upload_file($file, "", $file_name_to_store, $file_uploaded_path);
                if($file_path){
                  $term->term_icon = $file_path;
                }
            }
              $term->save();

              $term_categories = self::get_all_term_categories_and_terms();

              return response()->json(['status' => 1, 'message' => trans('pages.crud_messages.updated_success', [ 'attr' => 'Term Type']), 'data' => $term_categories]);

          }else{

              $term = new Term;
              $term->term_code = $request->term_code;
              $term->term_name = $request->term_name;
              $term->term_details = $request->term_details;
              $term->is_default = 0;
              $term->term_category_id = $request->term_category_id;
              $term->is_active = 1;
              if ($request->hasFile('term_icon')){
                $file = $request->file('term_icon');
                $file_name_to_store = time();
                $file_uploaded_path = 'assets/img/term';

                $file_path = \Helper::upload_file($file, "", $file_name_to_store, $file_uploaded_path);

                if($file_path){
                  $term->term_icon = $file_path;
                }
                else{

                }
            }
              $term->save();

              $term_categories = self::get_all_term_categories_and_terms();

              return response()->json(['status' => 1, 'message' => trans('pages.crud_messages.added_success', [ 'attr' => 'Term Type']), 'data' => $term_categories]);

          }

      }catch(Exception $exception) {

          return response()->json(['status' => 0, 'message' => trans('pages.something_wrong')]);

      }
  }

  public function delete($id){

    try{

        $term = Term::where('term_id', $id)->first();
        if($term)
        {
          \Helper::deleteFile($term->term_icon);
        }

        if (is_null($term)) {

            return response()->json(['status' => 0, 'message' => trans('pages.crud_messages.no_data', [ 'attr' => 'Term Type'])]);

        }

        $term->is_active = 0;
        $term->save();

        $term_categories = self::get_all_term_categories_and_terms();

        return response()->json(['status' => 1, 'message' => trans('pages.crud_messages.deleted_success', [ 'attr' => 'Term Type']), 'data' => $term_categories]);

    }catch(Exception $exception) {
        return response()->json(['status' => 0, 'message' => trans('pages.something_wrong')]);
    }
}

}
