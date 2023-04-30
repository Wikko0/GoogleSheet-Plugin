<?php

namespace Wikko\Googlesheet;

use Illuminate\Support\ServiceProvider as Base;
use Acelle\Library\Facades\Hook;
use Config;
use Wikko\Googlesheet\Model\GoogleSettings;

class GoogleProvider extends Base
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
{
    $data = [
        'client_id' => \Acelle\Model\Setting::get('oauth.google_client_id') ?? '',
        'client_secret' => \Acelle\Model\Setting::get('oauth.google_client_secret') ?? '',
        'scopes' => [],
        'access_type' => 'online',
        'approval_prompt' => 'auto',
        'prompt' => 'consent',
        'service' => [
            'enable' => true,
            'file' => storage_path("credentials.json"),
        ]
    ];
    
    if (class_exists('Google\Service\Sheets')) {
        $data['scopes'] = [\Google\Service\Sheets::DRIVE, \Google\Service\Sheets::SPREADSHEETS];
    }

    Config::set('google', $data);
}

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    }
}
