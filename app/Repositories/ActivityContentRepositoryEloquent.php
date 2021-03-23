<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Entities\ActivityContent;

/**
 * Class CareerRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ActivityContentRepositoryEloquent extends BaseRepository implements ActivityContentRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ActivityContent::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getFreshCareer($careerId)
    {
        $list = $this->where('career_id', $careerId)
            ->get();

        $result = [];
        foreach($list as $item) {
            if(empty($result[$item->key])) {
                $result[$item->key] = [
                    'key' => $item->key,
                    'key_title' => $item->key_title,
                    'key_description' => $item->key_description,
                    'list' => []
                ];
            }

            $result[$item->key]['list'][] = [
                'id' => $item->id,
                'is_checked' => false,
                'title' => $item->title,
                'free_text' => false
            ];
        }

        return array_values($result);
    }
}
