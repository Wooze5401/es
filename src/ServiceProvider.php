<?php

/*
 * This file is part of the overtrue/weather.
 *
 * (c) wooze
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Wooze\Es;

use Elasticsearch\ClientBuilder as ESClientBuilder;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/Config/es_indices.php' => config_path('es_indices.php'),
        ]);
    }

    public function register()
    {
        $this->app->singleton('es', function () {

            $builder = ESClientBuilder::create()->setHosts(config('database.elasticsearch.hosts'));

            $builder->setLogger(app('log')->driver('es'));

            return $builder->build();
        });
    }
}
