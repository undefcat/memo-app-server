<?php

namespace App\Providers;

use App\Models\Memo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class MorphMapServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Relation::morphMap([
            'memo' => Memo::class,
        ]);
    }
}
