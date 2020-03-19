<?php

namespace App\Api\Controllers;

use App\Models\Banner;

class BannerController extends BaseController
{

    public function getList()
    {
        $banners = Banner::where(['status' => Status_Online])->get();
        return responseSuccess($banners);
    }



}
