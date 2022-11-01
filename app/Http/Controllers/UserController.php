<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Response;
use App\Models\Role;
use App\Models\User;
use App\Resources\RoleCollection;
use App\Resources\UserCollection;
use App\Resources\UserResource;
use Illuminate\Support\Facades\Hash;

/**
 * REST-API контроллер для взаимодействия с пользователями.
 */
class UserController extends Controller
{
    /**
     * Инициализация контроллера.
     */
    public function __construct()
    {
        $this->authorizeResource(User::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \App\Http\Response
     */
    public function index()
    {
        return Response::success()->data([
            'users' => UserCollection::makeArray(
                User::with(['role'])->get()
            )]
        );
    }
    
    /**
     * Список доступных ролей.
     * @return \App\Http\Response
     */
    public function roles()
    {
        return Response::success()->data([
            'roles' => RoleCollection::makeArray(
                Role::get()
            )]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreUserRequest  $request
     * @return \App\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $user = new User;
        $user->role_id = $request->input('role_id');
        $user->login = $request->input('login');
        $user->password = Hash::make($request->input('password'));
        $user->save();

        $user->load(['role', 'projects']);

        return Response::success()
            ->message('Новый пользователь был успешно добавлен!')
            ->data(['user' => UserResource::makeArray($user)]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \App\Http\Response
     */
    public function show(User $user)
    {
        $user->load(['role', 'projects']);

        return Response::success()->data([
            'user' => UserResource::makeArray($user)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\User\UpdateUserRequest  $request
     * @param  \App\Models\User  $user
     * @return \App\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $user->fill($request->onlyFilled()->except(['projects', 'password']));

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        if ($request->filled('projects')) {
            $user->projects()->sync($request->input('projects', []));
        }

        $user->save();

        $user->load(['role', 'projects']);

        return Response::success()
            ->message('Пользователь был успешно обновлен!')
            ->data(['integration' => UserResource::makeArray($user)]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \App\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return Response::success()->message('Пользователь удален.');
    }
}
