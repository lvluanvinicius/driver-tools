<?php
namespace App\Http\Controllers;

use App\Models\Files;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
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
        $currentFolder = $this->modelFiles->where('uuid', $uuid)->where('is_folder', 'Y')->first() ?: null;

        $data = $this->modelFiles->query()
            ->where('parent_id', $currentFolder?->id)
            ->orderByRaw("CASE WHEN is_folder = 'Y' THEN 0 ELSE 1 END")
            ->orderBy('name', 'desc')
            ->paginate(10);

        $breadcrumbs = $currentFolder?->path_to_root ?? collect();

        return Inertia::render('Files/Index', [
            'data'        => $data,
            'breadcrumbs' => $breadcrumbs,
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

        // Recupera a pasta atual se estiver dentro de uma pasta.
        $currentFolder = $this->modelFiles->where('uuid', $uuid)->where('is_folder', 'Y')->first() ?: null;

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $data = [
                'name'      => $request->name,
                'user_id'   => $request->user()->id,
                'is_folder' => 'Y',
                'path'      => null,
                'parent_id' => $currentFolder?->id,
            ];

            if ($request->has('parent_id')) {
                $data['parent_id'] = $request->parent_id;
            }

            $this->modelFiles->create($data);

            if ($uuid) {
                return to_route("app.files.folder.index", ['uuid' => $uuid])->with([
                    "success" => "Pasta criada com sucesso.",
                ]);
            }

            return to_route("app.files.index")->with([
                "success" => "Pasta criada com sucesso.",
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
     * @var Request $request
     * @param string $uuid
     * @param string | null $parent
     * @return RedirectResponse
     * @throws \Exception
     */
    public function update(Request $request, string $uuid, string | null $parent = null): RedirectResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            if (! $file = $this->modelFiles->where('uuid', $uuid)->first()) {
                throw new \Exception('Arquivo não encontrado.');
            }

            if (! $file->update(['name' => $request->name])) {
                throw new \Exception('Houve um erro ao editar os registros em banco.');
            }

            if ($parent) {
                return to_route("app.files.folder.index", ['uuid' => $parent])->with([
                    "success" => ($file->is_folder == 'Y' ? 'Pasta' : 'Arquivo') . " atualiz" . ($file->is_folder == 'Y' ? 'a' : 'o') . " com sucesso.",
                ]);
            }

            return to_route("app.files.index")->with([
                "success" => ($file->is_folder == 'Y' ? 'Pasta' : 'Arquivo') . " atualiz" . ($file->is_folder == 'Y' ? 'a' : 'o') . " com sucesso.",
            ]);

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
     * @param string $uuid
     * @param string | null $parent
     * @return RedirectResponse
     */
    public function destroy(string $uuid, string | null $parent = null): RedirectResponse
    {
        try {
            if (! $file = $this->modelFiles->where('uuid', $uuid)->first()) {
                throw new \Exception('Arquivo não encontrado.');
            }

            $path     = $file->path;
            $isFolder = $file->is_folder;

            if (! $file->delete()) {
                throw new \Exception('Houve um erro ao tentar excluír o arquivo.');
            }

            $isFolder == 'N' && Storage::disk('driver_tool')->delete($path);

            if ($parent) {
                return to_route("app.files.folder.index", ['uuid' => $parent])->with([
                    "success" => ($isFolder == 'Y' ? 'Pasta' : 'Arquivo') . " excluíd" . ($isFolder == 'Y' ? 'a' : 'o') . " com sucesso.",
                ]);
            }

            return to_route("app.files.index")->with([
                "success" => ($isFolder == 'Y' ? 'Pasta' : 'Arquivo') . " excluíd" . ($isFolder == 'Y' ? 'a' : 'o') . " com sucesso.",
            ]);
        } catch (\Exception $error) {
            $errorMessage = $error->getMessage();

            $error->getCode() == '23000' && $errorMessage = "Não é possível excluir o arquivo, pois ele está vinculado a outro registro.";

            return Redirect::back()->with([
                'error' => $errorMessage,
            ]);
        }
    }
}
