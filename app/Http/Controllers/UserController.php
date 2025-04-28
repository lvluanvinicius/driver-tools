<?php
namespace App\Http\Controllers;

use App\Http\Requests\UserCreateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class UserController extends Controller
{
    public function __construct(protected User $modelUser)
    {

    }

    /**
     * Retorna a listagem de usuários.
     *
     * @return InertiaResponse
     */
    public function index(): InertiaResponse
    {
        $users = $this->modelUser->paginate(10);

        return Inertia::render('Users/Index', [
            'data' => $users,
        ]);

    }

    /**
     * Retorna o display de criação de um novo usuário.
     *
     * @return InertiaResponse
     */
    public function create(): InertiaResponse
    {
        return Inertia::render('Users/Create');
    }

    /**
     * Cria um novo registro.
     * @author Luan Santos <lvluansantos@gmail.com>
     *
     * @param UserCreateRequest $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function store(UserCreateRequest $request): RedirectResponse
    {
        try {
            $data = $request->only(['username', 'password', 'email', 'name']);

            if (! $this->modelUser->create($data)) {
                throw new \Exception('Erro ao tentar criar a conta!');
            }

            return to_route('app.users.index')->with([
                'success' => 'Usuário criado com sucesso!',
            ]);

        } catch (\Exception $error) {
            return Redirect::back()->with([
                'error' => $error->getMessage(),
            ]);
        }
    }

    /**
     * Retorna o display de edição de usuário.
     * @author Luan Santos <lvluansantos@gmail.com>
     *
     * @param string $uuid
     * @return InertiaResponse | RedirectResponse
     * @throws Exception
     */
    public function edit(string $uuid): InertiaResponse | RedirectResponse
    {
        try {
            if (! $user = $this->modelUser->where('uuid', $uuid)->first()) {
                throw new \Exception('Usuário não encontrado!');
            }

            return Inertia::render('Users/Edit', [
                'user' => $user,
            ]);

        } catch (\Exception $error) {
            return Redirect::back()->with([
                'error' => $error->getMessage(),
            ]);
        }
    }

    /**
     * Atualiza um registro.
     * @author Luan Santos <lvluansantos@gmail.com>
     *
     * @param Request $request
     * @param string $uuid
     * @return RedirectResponse
     * @throws Exception
     */
    public function update(Request $request, string $uuid): RedirectResponse
    {
        try {
            if (! $user = $this->modelUser->where('uuid', $uuid)->first()) {
                throw new \Exception('Usuário não encontrado!');
            }

            $data = $request->only(['username', 'password', 'email', 'name']);

            if (array_key_exists('password', $data)) {
                if ($data['password'] == null || $data['password'] == '') {
                    unset($data['password']);
                }
            }

            if (! $user->update($data)) {
                throw new \Exception('Erro ao tentar criar a conta!');
            }

            return to_route('app.users.index')->with([
                'success' => 'Usuário atualizado com sucesso!',
            ]);

        } catch (\Exception $error) {
            return Redirect::back()->with([
                'error' => $error->getMessage(),
            ]);
        }
    }

    /**
     * Exclui um registro.
     * @author Luan Santos <lvluansantos@gmail.com>
     *
     * @param string $uuid
     * @return RedirectResponse
     * @throws Exception
     */
    public function destroy(string $uuid): RedirectResponse
    {
        try {
            if (! $user = $this->modelUser->where('uuid', $uuid)->first()) {
                throw new \Exception('Usuário não encontrado!');
            }

            if (! $user->delete()) {
                throw new \Exception('Erro ao tentar excluír a conta!');
            }

            return to_route('app.users.index')->with([
                'success' => 'Usuário excluído com sucesso!',
            ]);

        } catch (\Exception $error) {
            $errorMessage = $error->getMessage();

            $error->getCode() == '23000' && $errorMessage = "Não é possível excluir o usuário, pois ele está vinculado a outro registro.";

            return Redirect::back()->with([
                'error' => $errorMessage,
            ]);
        }
    }
}
