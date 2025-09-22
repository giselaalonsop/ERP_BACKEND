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
        // (Opcional) ya que usas LoginRequest, la validación se ejecuta antes de entrar aquí.
        $request->validated();

        $email    = $request->input('email');
        $remember = $request->boolean('remember');

        Log::info('Attempting login for user: ' . $email);

        // Reglas de negocio antes de intentar autenticar
        $user = User::where('email', $email)->first();

        if ($user && (int)$user->habilitar === 0) {
            Log::warning('User disabled: ' . $user->email);
            return response()->json(['message' => 'Usuario deshabilitado'], 403);
        }

        // Intenta autenticar (valida hash y hace login)
        $ok = Auth::attempt(
            ['email' => $email, 'password' => $request->input('password')],
            $remember
        );

        if (!$ok) {
            // No filtres si fue email o password → mensaje único
            Log::warning('Invalid credentials for: ' . $email);
            return response()->json(['message' => 'Credenciales inválidas'], 422);
        }

        // Prevención de fijación de sesión → SIEMPRE después de autenticar
        $request->session()->regenerate();

        Log::info('User logged in: ' . Auth::user()->email);

        // Para SPA: 204 No Content (sin redirección)
        return response()->noContent();
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
