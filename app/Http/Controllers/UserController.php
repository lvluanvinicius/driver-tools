<?php
namespace App\Http\Controllers;

use App\Http\Requests\UserCreateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct(protected User $modelUser)
    {

    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $users = $this->modelUser->paginate(10);

            return Inertia::render('Users/Index', [
                'data' => $users,
            ]);

        } catch (\Exception $error) {
            return $this->errorResponse($error->getMessage(), 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Cria um novo registro.
     * @author Luan Santos <lvluansantos@gmail.com>
     *
     * @param UserCreateRequest $request
     * @return void
     */
    public function store(UserCreateRequest $request)
    {
        try {
            $data = $request->only(['username', 'password', 'email', 'name']);

            if (! $create = $this->modelUser->create($data)) {
                return $this->errorResponse('Erro ao tentar criar a conta!');
            }

            return $this->successResponse($create, 'Cadastro criado com sucesso.');

        } catch (\Exception $error) {
            return $this->errorResponse($error->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
