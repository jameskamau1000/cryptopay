<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function __construct()
    {
        // Intentionally empty.
    }

    public static function middleware()
    {
        return [];
    }

}
