<?php
namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ProfileController extends Controller
{
    /**
     * Retorna o display de edição do perfil.
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        $data = $request->user();

        return inertia('Profile/Index', [
            'profile' => $data,
        ]);
    }

    /**
     * Atualiza o perfil de um usuário logado.
     * @author Luan Santos <lvluansantos@gmail.com>
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        try {
            $user = $request->user();

            $data = $request->only(['username', 'password', 'email', 'name']);

            if (array_key_exists('password', $data)) {
                if ($data['password'] == null || $data['password'] == '') {
                    unset($data['password']);
                }
            }

            if (! $user->update($data)) {
                throw new \Exception('Erro ao tentar criar a conta!');
            }

            return to_route('app.profile.index')->with([
                'success' => 'Perfil atualizado com sucesso!',
            ]);

        } catch (\Exception $error) {
            return Redirect::back()->with([
                'error' => $error->getMessage(),
            ]);
        }
    }
}
