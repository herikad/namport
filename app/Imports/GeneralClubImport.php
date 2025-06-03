<?php

namespace App\Imports;

use App\Models\Club;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Log;

class GeneralClubImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        try {

            Log::info("GeneralClubImport row ". print_r( $row, true));

            $location = isset($row['location']) ? json_decode($row['location'], true) : null;

            $record = new Club();
            $record->club_type_term = config('custom.club_type_term.general');
            $record->club_name = isset($row['name']) ? $row['name'] : null;
            $record->location = isset($row['address']) ? $row['address'] : null;
            // $record->latitude = isset($row['location'][0]) ? $row['location'][0] : null;
            // $record->longitude = isset($row['location'][1]) ? $row['location'][1] : null;

            $record->latitude = isset($location[1]) ? $location[1] : null;
            $record->longitude = isset($location[0]) ? $location[0] : null;
            $record->sport_type_term = isset($row['types']) ? $row['types'] : null;
            $record->privacy_type_term = isset($row['privacy_type']) ? $row['privacy_type'] : null;
            $record->is_buy_membership = isset($row['buy_membership']) ? $row['buy_membership'] : null;
            $record->phone_no = isset($row['phone']) ? $row['phone'] : null;
            $record->detail = isset($row['info']) ? $row['info'] : null;
            $record->website = isset($row['website']) ? $row['website'] : null;
            $record->is_verified = isset($row['verified']) ? $row['verified'] : null;
            $record->cost_type_term = isset($row['cost']) ? $row['cost'] : null;
            $record->play_location_term = isset($row['play_location']) ? $row['play_location'] : null;
            $record->is_required_membership = isset($row['need_membership']) ? $row['need_membership'] : null;
            $record->total_courts = isset($row['num_courts']) ? $row['num_courts'] : null;
            $record->total_reviews = isset($row['num_reviews']) ? $row['num_reviews'] : null;
            $record->goggle_place_id = isset($row['google_place_id']) ? $row['google_place_id'] : null;
            $record->created_by = auth()->user()->user_id;
            $record->status_term = isset($row['status']) ? $row['status'] : null;
            $record->avg_rating = isset($row['rating']) ? $row['rating'] : null;
            $record->status_term = config('custom.status_term.active');
            $record->approval_status = config('custom.approval_status.accept');
            $record->is_bookable = 0;
            $record->is_activity = 1;
            $record->save();

            return $record;

        } catch (\Exception $e) {

            Log::info("model error ". print_r($e->getMessage(), true));

        }
    }
}
