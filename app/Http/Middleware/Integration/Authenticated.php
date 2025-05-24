<?php
namespace App\Http\Middleware\Integration;

use App\Models\User;
use App\Services\Integration;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Authenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! ($request->session()->has('user') && $request->session()->has('token'))) {
            return redirect()->route('sign-in');
        }

        // Recuperando token de sessão.
        $userToken = $request->session()->get('token');

        // Validando sessão.
        $integration = new Integration();
        if ($integration->auther($userToken)) {

            // Recuperar dados de sessão do usuário.
            $sessionData = $integration->session($userToken);

            // Valida se os dados necessários estão incusos.
            if (isset($sessionData['user']) && isset($sessionData['permissions'])) {
                // Atualizando dados na sessão.
                $request->session()->put('user', $sessionData['user']);
                $request->session()->put('permissions', $sessionData['permissions']);
                $user = new User($sessionData['user']);
                Auth::setUser($user);
                return $next($request);
            }

        }

        return redirect()->route('sign-in');

    }
}
