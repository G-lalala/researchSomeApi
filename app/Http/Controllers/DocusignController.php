<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class DocusignController extends BaseController
{
    protected $first = 'hi myname is G-lalala';

    /**
     * 初見
     */
    public function index() 
    {
        return view('index')->with([
            'first' => $this->first
        ]);
    }
}
