<?php
namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use App\Http\Requests\Integration\SignInRequest;
use App\Services\Integration;
use Illuminate\Http\RedirectResponse;
use Inertia\Response as InertiaResponse;

class SignInController extends Controller
{
    public function index(): InertiaResponse
    {
        return inertia('SignIn/Index');
    }

    /**
     * @param \App\Http\Requests\Integration\SignInRequest $request
     * @return mixed|RedirectResponse
     */
    public function store(SignInRequest $request): RedirectResponse
    {
        try {
            $data = $request->only(['username', 'password']);

            $integration = new Integration();

            $response = $integration->signIn($data['username'], $data['password']);

            if ($response['status_code'] === 422) {
                return redirect()->back()->withErrors($response['errors'])->with([
                    'error' => $response['error'],
                ]);
            }

            if ($response['status_code'] === 200 && isset($response['response']) && isset($response['response']['data'])) {
                if ($response['response']['status']) {

                    // Criando sessão.
                    $request->session()->regenerate();

                    // Guardando dados de sessão.
                    session($response['response']['data']);

                    return redirect()->intended(route('app.dashboard', false));
                }

                throw new \Exception($response['response']['message']);
            }

            throw new \Exception($response['error']);
        } catch (\Exception $error) {
            return redirect()->back()->with([
                'error' => $error->getMessage(),
            ]);
        }
    }
}
