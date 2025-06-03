<?php
namespace App\Http\Controllers;

use App\Services\Integration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Response as InertiaResponse;

class ProfileController extends Controller
{
    /**
     * Retorna o display de edição do perfil.
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request): InertiaResponse
    {
        $data = $request->user();

        return inertia('Profile/Index', [
            'profile' => $data,
        ]);
    }

    /**
     * Atualiza os dados do usuário logado.
     * @author Luan Santos <lvluansantos@gmail.com>
     *
     * @param \Illuminate\Http\Request $request
     * @throws \Exception
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        try {
            $data = $request->only(['username', 'password', 'email', 'name']);

            if (array_key_exists('password', $data)) {
                if ($data['password'] == null || $data['password'] == '') {
                    unset($data['password']);
                }
            }

            $integration = new Integration();

            $update = $integration->updateProfile($request->session()->get('token'), $data);

            if (isset($update['status_code']) && $update['status_code'] === 422) {
                return redirect()->back()->withErrors($update['errors'])->with([
                    'error' => $update['error'],
                ]);
            }

            if (isset($update['error'])) {
                return redirect()->back()->with([
                    'error' => $update['error'],
                ]);
            }

            if (isset($update['status_code']) && $update['status_code'] == 200) {
                return to_route('app.profile.index')->with([
                    'success' => $update['message'],
                ]);
            }

            throw new \Exception('Houve um erro desconhecido durante sua solicitação, por favor, tente novamente mais tarde.');

        } catch (\Exception $error) {
            return redirect()->back()->with([
                'error' => $error->getMessage(),
            ]);
        }
    }
}
