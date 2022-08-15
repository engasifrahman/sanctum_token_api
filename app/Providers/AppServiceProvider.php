<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('setResponse', function ($status = true, $message = null, $errors = null, $data = null, $status_code = 200) {
            Config::set('constants.success', $status);
            Config::set('constants.status_code', $status_code);
            Config::set('constants.message', $message);
            Config::set('constants.errors', $errors);
            Config::set('constants.data', $data);
        });

        Response::macro('getResponse', function($headers = null){
            $reponseArray = [
                "success"   => Config::get('constants.success'),
                "code"      => Config::get('constants.status_code'),
                "message"   => Config::get('constants.message'),
                "errors"    => Config::get('constants.errors'),
                "data"      => Config::get('constants.data')
            ];

            return Response::json($reponseArray, Config::get('constants.status_code'));
        });
    }
}
