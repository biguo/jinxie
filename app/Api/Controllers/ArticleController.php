<?php

namespace App\Api\Controllers;


use App\Models\Article;
use App\Models\Center;
use Illuminate\Support\Facades\Input;

class ArticleController extends BaseController
{

    public function getList()
    {
        $params = Input::all();
        $where = [['status', '=', Status_Online]];
        isset($params['type']) ? array_push($where, ['type_id', '=', $params['type']]) : null;
        $perPage = isset($params['perPage']) ? $params['perPage'] : 5;
        $page = isset($params['page']) ? $params['page'] : 1;
        if(isset($params['global'])){
            $gid = Center::where('slug', GLOBAL_CENTER)->value('id');
            if($params['global'] == '1'){
                array_push($where, ['center_id', '=', $gid]);
            }else{
                array_push($where, ['center_id', '!=', $gid]);
            }
        }
        $data = Article::where($where)->paginate($perPage, ['*'], 'page', $page);
        return responseSuccess($data);
    }

}
