<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => ['nullable', 'exists:schools,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'level' => ['nullable', 'string', 'max:50'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::create([
            'school_id' => $validated['school_id'] ?? null,
            'name' => $validated['name'],
            'email' => Str::lower($validated['email']),
            'password' => $validated['password'],
            'role' => 'student',
            'level' => $validated['level'] ?? null,
            'avatar_initial' => Str::upper(Str::substr($validated['name'], 0, 1)),
        ]);

        return response()->json([
            'message' => 'Registrasi berhasil.',
            'data' => [
                'user' => $user->load('school'),
                'token' => $user->createToken($validated['device_name'] ?? 'android')->plainTextToken,
            ],
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::where('email', Str::lower($validated['email']))->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password tidak sesuai.'],
            ]);
        }

        $deviceName = $validated['device_name'] ?? 'android';
        $user->tokens()->where('name', $deviceName)->delete();

        return response()->json([
            'message' => 'Login berhasil.',
            'data' => [
                'user' => $user->load('school'),
                'token' => $user->createToken($deviceName)->plainTextToken,
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $request->user()->load('school'),
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'school_id' => ['sometimes', 'nullable', 'exists:schools,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'level' => ['sometimes', 'nullable', 'string', 'max:50'],
        ]);

        if (isset($validated['email'])) {
            $validated['email'] = Str::lower($validated['email']);
        }

        if (isset($validated['name'])) {
            $validated['avatar_initial'] = Str::upper(Str::substr($validated['name'], 0, 1));
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'data' => $user->fresh()->load('school'),
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $request->user()->update(['password' => $validated['password']]);

        return response()->json(['message' => 'Password berhasil diperbarui.']);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logout berhasil.']);
    }
}
