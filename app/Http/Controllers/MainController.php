<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
    private $_app_data;

    public function __construct()
    {
        // load app_data.php file from app folder
        $this->_app_data = require(app_path('app_data.php'));
    }
    public function show_data()
    {
        return response()->json($this->_app_data);
    }
}
