<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class Center extends Model
{
    protected $table = 'center';

    public $timestamps = false;

    /**
     * 根据参数搜索当前乡村项目
     * @param null $slug
     * @return mixed
     */
    public static function current($slug = null)
    {
        if (!$slug) {
            $slug = Input::get('slug');
        }
        return self::where('slug', $slug)->first();
    }

    public function banners()
    {
        return $this->hasMany(Banner::class);
    }

    public function usedBanner()
    {
        return $this->banners()->where('status', Status_Online)
            ->select('id', 'title','subtitle','subtitle1','description', DB::raw("concat('" . Upload_Domain . "', image) as image"), DB::raw("concat('" . Upload_Domain . "', bigImage) as bigImage"),'sort')
            ->orderBy('sort', 'asc')->get();
    }

}
