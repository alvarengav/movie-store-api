<?php

namespace App\Services;

use Illuminate\Support\Facades\Gate;

trait AuthorizationGate
{
    private function authorizedAdmin()
    {
        //only user admin
        return Gate::allows('sales-rents-auth');
    }
}
