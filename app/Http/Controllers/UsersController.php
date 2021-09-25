<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Requests\UpdateUserPasswordRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\AuthorizationGate;
use App\Services\ModelNotFoundResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    use ModelNotFoundResponse;
    use AuthorizationGate;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!$this->authorizedAdmin()) {
            return response('Unauthorized', 401);
        }
        return User::paginate(request('perPage'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $attributes = $request->validated();
        return User::createUser($attributes);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        $this->errorModelJsonResponse($user);
        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateUserRequest $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $attributes = $request->validated();
        if (!$attributes) return ['updated' => false];
        $userLogged = $request->user();
        $userToUpdate = $userLogged->id === $id
            ? $userLogged
            : User::find($id);
        $this->errorModelJsonResponse($userToUpdate);

        if (Gate::allows('update', $userToUpdate)) {
            foreach ($attributes as $attrKey => $attrValue) {
                $userToUpdate[$attrKey] = $attrValue;
            }
            if ($userToUpdate->isClean()) return ['updated' => false];
            $userToUpdate->update();
            return [
                'updated' => true,
                'user' => $userToUpdate->fresh()
            ];
        }

        //user no authorize to update
        return response()->json([
            'error' => 'No authorized to udpate user'
        ], 422);
    }

    /**
     * only updated by admin
     * @param \App\Http\Requests\UpdateUserPasswordRequest $request
     * @param int $user_id
     */
    public function updatePassword(UpdateUserPasswordRequest $request, $user_id)
    {
        if (!$this->authorizedAdmin()) $this->errorModelJsonResponse(null, ['error' => 'Unauthorized'], 401);
        $user = User::find($user_id);
        $this->errorModelJsonResponse($user);
        $password = Hash::make($request->password);
        $user->password = $password;
        return ['updated' => true];
    }

    /**
     * @param \App\Http\Requests\UpdateRoleRequest $request
     * @param int user_id
     */
    public function updateRole(UpdateRoleRequest $request, $id)
    {
        if (!$this->authorizedAdmin()) {
            $this->errorModelJsonResponse(null, [
                'error' => 'Unauthorized'
            ], 401);
        }
        $logged_user = $request->user();
        $user_to_update = null;
        if ($logged_user->id == $id) {
            $user_to_update = $logged_user;
        } else {
            $user_to_update = User::find($id);
        }
        $this->errorModelJsonResponse($user_to_update);

        $attributes = $request->validated();
        $role = $attributes['role'];
        if ($user_to_update->verifyAdminsBeforeChange($role)) {
            $user_to_update->role = $role;
            $user_to_update->save();
            return $user_to_update->fresh();
        }

        //no more admins
        return response()->json([
            'error' => 'there are no more administrator users'
        ], 422);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!$this->authorizedAdmin()) {
            return response()->json([
                'error' => 'No permission to delete'
            ], 403);
        }

        $userToDelete = User::find($id);
        $this->errorModelJsonResponse($userToDelete);

        $userLogged = request()->user();
        if ($userLogged->id !== $userToDelete->id) {
            return $userToDelete->delete();
        }

        return response()->json([
            'error' => 'Action no allowed'
        ], 422);
    }
}
