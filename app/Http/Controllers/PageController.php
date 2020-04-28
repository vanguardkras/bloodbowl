<?php

namespace App\Http\Controllers;

use App\Services\ImageUploader;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function main(Request $request)
    {
        return view('main');
    }
}
