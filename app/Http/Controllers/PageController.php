<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Services\ImageUploader;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Main page
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function main(Request $request)
    {
        $open_registrations = Competition::getOpenRegistration();
        return view('main', compact('open_registrations'));
    }
}
