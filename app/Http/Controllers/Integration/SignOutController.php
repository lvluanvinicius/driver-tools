<?php
namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use App\Services\Integration;
use Illuminate\Http\Request;

class SignOutController extends Controller
{
    /**
     * Encerra a sessão de um usuário.
     * @author Luan Santos <lvluansantos@gmail.com>
     *
     * @param \Illuminate\Http\Request $request
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        try {
            $integration = new Integration();

            $signOut = $integration->signOut($request->session()->get('token'));

            if (isset($signOut['status_code']) && $signOut['status_code'] == 200) {
                if (isset($signOut['response']) && isset($signOut['response']['status']) && $signOut['response']['status']) {
                    $request->session()->invalidate();
                    $request->session()->regenerate();
                    return to_route('login');
                } else {
                    return redirect()->back()->with([
                        'error' => $signOut['response']['message'],
                    ]);
                }

            }

            throw new \Exception('Houve um erro desconhecido ao tentar encerrar sua sessão. Por favor, tente novamente mais tarde.');
        } catch (\Exception $error) {
            return redirect()->back()->with([
                'error' => $error->getMessage(),
            ]);
        }
    }
}
