<?php

namespace App\Api\Controllers;


use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends BaseController
{

    public function getList(Request $request)
    {
        $data = Article::where(['status' => Status_Online, 'type_id' => $type = $request->input('type')])->get();
        return responseSuccess($data);
    }

}
