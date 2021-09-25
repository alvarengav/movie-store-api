<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class MovieScope implements Scope
{

    /**
     * guest user only available movie
     */
    public function apply(Builder $builder, Model $model)
    {
        $user = auth()->user();
        if (!$user) {
            $builder->where('availability', true);
        }
    }
}
