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

    public function show($id)
    {
        $banner = Banner::where([['status', '=', Status_Online],['id','=',$id]])->first();
        if($banner && $banner->image){
            $banner->image = Upload_Domain.$banner->image;
        }else{
            $banner->image = '';
        }
        return responseSuccess($banner);
    }


}
