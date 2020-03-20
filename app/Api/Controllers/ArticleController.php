<?php

namespace App\Api\Controllers;


use App\Models\Article;
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
        $data = Article::where($where)->paginate($perPage, ['*'], 'page', $page);
        return responseSuccess($data);
    }

}
