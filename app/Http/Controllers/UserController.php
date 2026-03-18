<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\NewUserNotification;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return User::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);

            $user->assignRole('Admin'); 

            // comment this first if unsure
            // $user->notify(new NewUserNotification());

            return response()->json([
                'message' => 'User created successfully',
                'user' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return User::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
    $user = User::findOrFail($id);

    // Only update fillable fields
    $user->fill($request->only($user->getFillable()));

    // Force save and check result
    if ($user->save()) {
        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    } else {
        return response()->json([
            'message' => 'Failed to update user',
            'user' => $user
        ], 500);
    }

    //$user->notify(new NewUserNotification());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
}
