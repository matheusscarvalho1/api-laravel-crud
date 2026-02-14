<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController
{
    /**
     * Display a listing of the resource.
     */
    public function index() // GET /users
    {
        //return User::all();
        //return User::select('name', 'email')->get();
        return UserResource::collection(User::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) // POST /users
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) // GET /users/1
    {
        return new UserResource(User::where('id', $id)->first());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) // PUT/PATCH /users/1
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) // DELETE /users/1
    {
        //
    }
}
