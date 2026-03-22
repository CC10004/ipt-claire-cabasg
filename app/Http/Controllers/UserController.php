<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::all());
    }

    public function store(Request $request)
    {
        $data = $request->only(['name', 'email']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Profile photo: FORM-DATA
        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo')->store('profiles', 'public');
        }

        // Profile photo: JSON Base64
        elseif ($request->filled('profile_photo') && str_starts_with($request->profile_photo, 'data:image')) {
            $data['profile_photo'] = $this->storeBase64Image($request->profile_photo);
        }

        $user = User::create($data);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }

    public function show(string $id)
    {
        return response()->json(User::findOrFail($id));
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $data = [];

        // Update simple fields safely
        foreach (['name', 'email', 'password'] as $field) {
            if ($request->filled($field)) {
                $data[$field] = $field === 'password' ? Hash::make($request->password) : $request->$field;
            }
        }

        // Profile photo: FORM-DATA
        if ($request->hasFile('profile_photo')) {
            $this->deleteOldProfilePhoto($user);
            $data['profile_photo'] = $request->file('profile_photo')->store('profiles', 'public');
        }

        // Profile photo: JSON Base64
        elseif ($request->filled('profile_photo') && str_starts_with($request->profile_photo, 'data:image')) {
            $this->deleteOldProfilePhoto($user);
            $data['profile_photo'] = $this->storeBase64Image($request->profile_photo);
        }

        // Update only if there is data
        if (!empty($data)) {
            $user->update($data);
        }

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user->fresh()
        ]);
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $this->deleteOldProfilePhoto($user);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    private function storeBase64Image(string $base64Image): string
    {
        $image = preg_replace('#^data:image/\w+;base64,#i', '', $base64Image);
        $image = str_replace(' ', '+', $image);
        $imageName = uniqid() . '.png';
        Storage::disk('public')->put('profiles/' . $imageName, base64_decode($image));
        return 'profiles/' . $imageName;
    }

    private function deleteOldProfilePhoto(User $user): void
    {
        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }
    }
}