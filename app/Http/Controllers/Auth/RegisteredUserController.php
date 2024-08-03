<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cedula' => ['required', 'string', 'max:20', 'unique:' . User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'location' => ['required', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'rol' => ['required', 'string'],
        ]);

        $permissions = $request->permissions ? json_encode($request->permissions) : json_encode([]);

        if (Auth::check()) {
            if (Auth::user()->rol != 'admin') {
                return response()->json(['message' => 'No tienes permisos para realizar esta acción'], 403);
            }

            $user = User::create([
                'name' => $request->name,
                'cedula' => $request->cedula,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'rol' => $request->rol,
                'location' => $request->location,
                'permissions' => $permissions,
            ]);

            event(new Registered($user));

            return response($user, 201);
        } else {
            $adminExists = User::where('rol', 'admin')->exists();
            $rol = $adminExists ? 'user' : 'admin';

            if ($adminExists) {
                return response()->json(['message' => 'Inicie sesión como admin para realizar esta acción'], 403);
            }

            $user = User::create([
                'name' => $request->name,
                'cedula' => $request->cedula,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'rol' => $rol,
                'location' => $request->location,
                'permissions' => $permissions,
            ]);

            event(new Registered($user));
            Auth::login($user);

            return response($user, 201);
        }
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if ($user) {
            $data = $request->only(['name', 'cedula', 'email', 'rol', 'location', 'permissions']);
            if (isset($data['permissions'])) {
                $data['permissions'] = json_encode($data['permissions']);
            }
            $user->update($data);
            return response()->json($user);
        }

        return response()->json(['message' => 'User not found'], 404);
    }
}
