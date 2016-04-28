<?php

namespace Hero\Http\Controllers;

use View;

class SiteController extends Controller
{
    /**
     * Get landing page of your application.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return View::make('index');
    }
}
