<?php

namespace Wikko\Googlesheet\Controllers;

use Illuminate\Http\Request;
use Acelle\Http\Controllers\Controller as BaseController;
use Acelle\Model\Plugin;

class DashboardController extends BaseController
{
    public function index(Request $request)
    {
        // Get the plugin record in the plugin table
        $plugin = Plugin::where('name', 'wikko/googlesheet')->first();

        // View files are available in the storage/app/plugins/wikko/googlesheet/resources/views/ folder
        // Remember to use the googlesheet:: prefix for specifying view
        return view('googlesheet::index', [
            'plugin' => $plugin,
        ]);
    }
}
