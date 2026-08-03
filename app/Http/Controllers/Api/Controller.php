<?php

namespace App\Http\Controllers\Api;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use ValidatesRequests;
}
