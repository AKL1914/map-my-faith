<?php

namespace App\Http\Controllers;

/**
 * Class HomeController
 *
 * This controller handles the display of the home and activation views.
 */
class HomeController extends Controller
{
    /**
     * Display the welcome view.
     *
     * This method returns the main welcome page of the application.
     *
     * @return \Illuminate\View\View The welcome view.
     */
    public function index()
    {
        return view('welcome');
    }

    /**
     * Display the activation view.
     *
     * This method returns the activation page of the application.
     *
     * @return \Illuminate\View\View The activation view.
     */
    public function activate()
    {
        return view('activate.index');
    }
}
