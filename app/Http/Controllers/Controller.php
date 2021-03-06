<?php

namespace App\Http\Controllers;

use App\Models\Center;
use App\Models\CenterUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Support\MessageBag;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    protected $request;
    protected $mid;
    protected $center;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->mid = Auth::guard('admin')->user()->id;
        $cu = CenterUser::where('user_id', $this->mid)->value('center_id');
        $this->center =  $cu ? $cu : Center::where('slug', GLOBAL_CENTER)->value('id');
    }

}
