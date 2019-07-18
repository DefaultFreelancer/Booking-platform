<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Cache;

class LocalizationController extends Controller
{
    public function getLangDir()
    {
        $dir    = './../resources/lang';
        $files2 = array_diff(scandir($dir), array('..', '.','.DS_Store'));

        return $files2;
    }
}
