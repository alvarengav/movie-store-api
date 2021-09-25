<?php

namespace App\Http\Controllers;

use App\Models\Rent;
use App\Services\ModelNotFoundResponse;

class RentsController extends Controller
{
    use ModelNotFoundResponse;
    /**
     * return all rents
     */
    public function index()
    {
        $builder = Rent::with(['movie','user']);
        if (request('rented')) {
            return request()->boolean('rented')
                ? $builder->rented()->paginate(request('perPage'))
                : $builder->returned()->paginate(request('perPage'));
        }
        return $builder->paginate(request('perPage'));
    }

    /**
     * @param int $id - sale_id
     */
    public function show($id)
    {
        $rent = Rent::with(['movie', 'user'])->find($id);
        $this->errorModelJsonResponse($rent);
        return $rent;
    }
}
