<?php
namespace App\Http\Controllers;

use App\Jobs\DestroyFilesJob;
use App\Models\Files;
use App\Services\Integration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class FileController extends Controller
{
    public function __construct(protected Files $modelFiles)
    {

    }

    /**
     * Recupera os registros de arquivos.
     * @author Luan Santos <lvluansantos@gmail.com>
     *
     * @param Request $request
     * @return InertiaResponse
     * @throws \Exception
     */
    public function index(Request $request, string | null $uuid = null): InertiaResponse
    {
        $integration = new Integration();
        $data        = $integration->files($request->session()->get('token'), $uuid, $request->only('page', 'paginate', 'search'));

        return Inertia::render('Files/Index', [
            'data'        => $data['data'],
            'breadcrumbs' => $data['breadcrumbs'],
            'uuid'        => $uuid,
        ]);
    }

    /**
     * Insere um novo registro.
     * @author Luan Santos <lvluansantos@gmail.com>
     *
     * @param Request $request
     * @param string | null $uuid
     * @return RedirectResponse
     * @throws \Exception
     */
    public function store(Request $request, string | null $uuid = null): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $data = $request->only(['name']);

        try {
            $data['isFile'] = 'N';

            $integration = new Integration();

            $create = $integration->fileCreate($request->session()->get('token'), $uuid, $data);

            if (isset($create['status_code']) && $create['status_code'] === 422) {
                return redirect()->back()->withErrors($create['errors'])->with([
                    'error' => $create['error'],
                ]);
            }

            if (isset($create['error'])) {
                return redirect()->back()->with([
                    'error' => $create['error'],
                ]);
            }

            if (isset($create['status_code']) && $create['status_code'] === 200) {
                if ($uuid) {
                    return to_route("app.files.folder.index", ['uuid' => $uuid])->with([
                        "success" => $create['message'],
                    ]);
                }

                return to_route("app.files.index")->with([
                    "success" => $create['message'],
                ]);

            }

            throw new \Exception('Houve um erro desconhecido durante sua solicitação, por favor, tente novamente mais tarde.');
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
     * @var Request $request
     * @param string $uuid
     * @param string | null $parent
     * @return RedirectResponse
     * @throws \Exception
     */
    public function update(Request $request, string $uuid, string | null $parent = null): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $data = $request->only(['name']);

        try {
            $integration = new Integration();

            $create = $integration->fileUpdate($request->session()->get('token'), $uuid, $parent, $data);

            if (isset($create['status_code']) && $create['status_code'] === 422) {
                return redirect()->back()->withErrors($create['errors'])->with([
                    'error' => $create['error'],
                ]);
            }

            if (isset($create['error'])) {
                return redirect()->back()->with([
                    'error' => $create['error'],
                ]);
            }

            if (isset($create['status_code']) && $create['status_code'] === 200) {
                if ($parent) {
                    return to_route("app.files.folder.index", ['uuid' => $parent])->with([
                        "success" => $create['message'],
                    ]);
                }

                return to_route("app.files.index")->with([
                    "success" => $create['message'],
                ]);

            }

            throw new \Exception('Houve um erro desconhecido durante sua solicitação, por favor, tente novamente mais tarde.');
        } catch (\Exception $error) {
            return Redirect::back()->with([
                'error' => $error->getMessage(),
            ]);
        }
    }

    /**
     * Exclui um registro e o arquivo correspondente.
     * @author Luan Santos <lvluansantos@gmail.com>
     *
     * @param Request $request
     * @param string $uuid
     * @param string | null $parent
     * @return RedirectResponse
     */
    public function destroy(Request $request, string $uuid, string | null $parent = null): RedirectResponse
    {
        try {
            $integration = new Integration();

            $delete = $integration->fileDelete($request->session()->get('token'), $uuid, $parent);

            if (isset($delete['error'])) {
                return redirect()->back()->with([
                    'error' => $delete['error'],
                ]);
            }

            if (isset($delete['status_code']) && $delete['status_code'] === 200) {
                if (isset($delete['data']) && isset($delete['data']['path'])) {
                    if ($delete['data']['path'] != null) {
                        DestroyFilesJob::dispatch($delete['data']['path']);
                    }
                }

                if ($parent) {
                    return to_route("app.files.folder.index", ['uuid' => $parent])->with([
                        "success" => $delete['message'],
                    ]);
                }

                return to_route("app.files.index")->with([
                    "success" => $delete['message'],
                ]);

            }

            throw new \Exception('Houve um erro desconhecido durante sua solicitação, por favor, tente novamente mais tarde.');
        } catch (\Exception $error) {
            $errorMessage = $error->getMessage();

            $error->getCode() == '23000' && $errorMessage = "Não é possível excluir o arquivo, pois ele está vinculado a outro registro.";

            return Redirect::back()->with([
                'error' => $errorMessage,
            ]);
        }
    }
}
