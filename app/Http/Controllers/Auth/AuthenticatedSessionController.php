<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $user = User::where('email', $credentials['email'])->first();

        if ($user && $user->habilitar == 0) {
            return response()->json(['message' => 'User desahbilitado'], 403); // Cambiar el código de estado a 403 Forbidden o usar otro más adecuado
        }
        if (!$user) {
            return response()->json(['message' => 'Correo invalido'], 402);
        }
        if (!Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Clave incorrecta'], 401);
        }
        // para correo no existente en la base de datos de usuario



        $request->session()->regenerate();

        Auth::login($user);

        return response()->json(['message' => 'Login successful'], 200);
    }


    /**
     * Destroy an authenticated session.
     */
    public function getUsers(Request $request)
    {
        $users = User::where('habilitar', 1)->get();


        return response()->json($users);
    }
    public function inHabilitados(Request $request)
    {
        $users = User::where('habilitar', 0)->get();
        return response()->json($users);
    }
    public function habilitar($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->habilitar = 1;
            $user->save();

            return response()->json(['message' => 'User habilitado correctamente'], 200);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Error al habilitar el user', 'error' => $e->getMessage()], 500);
        }
    }
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if ($user) {
            $user->update($request->all());
            return response()->json($user);
        }
        return response()->json(['message' => 'User not found'], 404);
    }
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
    public function deshabilitar($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->habilitar = 0;
            $user->save();

            return response()->json(['message' => 'User deshabilitado correctamente'], 200);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Error al deshabilitar el user', 'error' => $e->getMessage()], 500);
        }
    }
}
