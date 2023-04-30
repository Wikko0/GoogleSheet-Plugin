<?php

namespace Wikko\Googlesheet;

use Illuminate\Support\ServiceProvider as Base;
use Acelle\Library\Facades\Hook;

class ServiceProvider extends Base
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Register views path
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'googlesheet');

        // Register routes file
        $this->loadRoutesFrom(__DIR__.'/../routes.php');

        // Register translation file
        $this->loadTranslationsFrom(storage_path('app/data/plugins/wikko/googlesheet/lang/'), 'googlesheet');

        // Register models
        $this->loadModelsFrom(__DIR__.'/../Model');

        // Register the translation file against Acelle translation management
        Hook::register('add_translation_file', function() {
            return [
                "id" => '#wikko/googlesheet_translation_file',
                "plugin_name" => "wikko/googlesheet",
                "file_title" => "Translation for wikko/googlesheet plugin",
                "translation_folder" => storage_path('app/data/plugins/wikko/googlesheet/lang/'),
                "file_name" => "messages.php",
                "master_translation_file" => realpath(__DIR__.'/../resources/lang/en/messages.php'),
            ];
        });
    }

    protected function loadModelsFrom($path)
    {
        foreach (glob($path . '/*.php') as $file) {
            $class = 'Wikko\\Googlesheet\\Model\\'.basename($file, '.php');
            if (class_exists($class)) {
                $factory_path = dirname($file) . '/Factories';
                if (is_dir($factory_path)) {
                    $this->app->make(ModelFactory::class)->load($factory_path);
                }
            }
        }
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
