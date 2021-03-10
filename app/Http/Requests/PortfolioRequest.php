<?php

namespace App\Http\Requests;

class PortfolioRequest extends Base
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'images' => 'required',
            'member_ids' => 'required',
            'title' => 'required',
            'job_ids' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'is_still_active' => 'required',
            'member' => 'required',
            'budget' => 'required',
            'reach_number' => 'required',
            'view_count' => 'required',
            'like_count' => 'required',
            'comment_count' => 'required',
            'cpa_count' => 'required',
            'video_link' => 'required',
            'work_link' => 'required',
            'work_description' => 'required'
        ];
    }
}
