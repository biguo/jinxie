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
        if(isset($params['cid'])){
            array_push($where, ['center_id', '=', $params['cid']]);
        }
        $data = Article::where($where)->paginate($perPage, ['*'], 'page', $page);
        foreach ($data as $item){
            $item['content'] = str_replace(array("\r\n", "\r", "\n", "&nbsp;"), "", strip_tags(htmlspecialchars_decode($item['content'])));
        }
        return responseSuccess($data);
    }

    public function search()
    {
        $params = Input::all();
        $where = [['status', '=', Status_Online]];
        $perPage = isset($params['perPage']) ? $params['perPage'] : 5;
        $page = isset($params['page']) ? $params['page'] : 1;
        if(isset($params['kwd'])){
            array_push($where, ['title', 'like', '%'.$params['kwd'].'%']);
        }
        $data = Article::join('admin_users as u', 'article.mid', '=', 'u.id')->where($where)->paginate($perPage, ['article.id','title','u.name','article.created_at'], 'page', $page);
        return responseSuccess($data);
    }

    public function show($id)
    {
        $article = Article::where([['status', '=', Status_Online],['id','=',$id]])->first();
        if($article && $article->image){
            $article->image = Upload_Domain.$article->image;
        }else{
            $article->image = '';
        }
        return responseSuccess($article);
    }

    public function getCenters()
    {
        $params = Input::all();
        $query = Center::where([['slug','!=', GLOBAL_CENTER]]);
        if(isset($params['size'])){
            $query->limit($params['size']);
        }
        $centers = $query->get();
        return responseSuccess($centers);
    }

}
