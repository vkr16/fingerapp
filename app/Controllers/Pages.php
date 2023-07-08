<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Pages extends BaseController
{
    public function errorNotFound()
    {
        return view('errors/custom/404');
    }
}
