<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Services\AuthorizationGate;
use App\Services\ModelNotFoundResponse;

class SalesController extends Controller
{
    use ModelNotFoundResponse;
    use AuthorizationGate;
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!$this->authorizedAdmin()) {
            return response('Unauthorized', 401);
        }
        return Sale::with(['movie', 'user'])->paginate(request('perPage'));
    }

    /**
     * @param int $id sale_id
     */
    public function show($id)
    {
        $sale = Sale::with(['movie', 'user'])->find($id);
        $this->errorModelJsonResponse($sale);
        return $sale;
    }
}
