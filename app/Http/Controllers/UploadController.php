<?php
namespace App\Http\Controllers;

use App\Models\Files;
use App\Services\Integration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class UploadController extends Controller
{
    public function __construct(protected Files $modelFiles)
    {
    }

    public function index(Request $request, string | null $uuid = null): InertiaResponse | RedirectResponse
    {
        try {
            $integration = new Integration();
            $response    = $integration->getFolder($request->session()->get('token'), $uuid);

            $currentFolder = null;

            if (isset($response['status']) && $response['status']) {

                if (isset($response['data'])) {
                    $currentFolder = $response['data'];

                    return Inertia::render('Files/Upload/Index', [
                        'uuid'       => $uuid,
                        'folder'     => $currentFolder,
                        'csrf_token' => csrf_token(),
                    ]);
                }

                return Inertia::render('Files/Upload/Index', [
                    'uuid'       => $uuid,
                    'folder'     => $currentFolder,
                    'csrf_token' => csrf_token(),
                ]);

            }

            $error = "Houve um erro desconhecido durante a solicitação.";
            $code  = 400;

            if (isset($response['error'])) {
                $error = $response['error'];
                $code  = $response['status_code'];
            }

            return Inertia::render('Error/Index', [
                'error'      => $error,
                'code'       => $code,
                'csrf_token' => csrf_token(),
            ]);
        } catch (\Exception $error) {
            return Inertia::render('Error/Index', [
                'error'      => $error->getMessage(),
                'code'       => 500,
                'csrf_token' => csrf_token(),
            ]);

        }
    }

    /**
     * Efetua o upload de arquivos.
     * @author Luan Santos <lvluansantos@gmail.com>
     *
     * @param Request $request
     * $param string|null $uuid
     * @return JsonResponse
     */
    public function store(Request $request, string | null $uuid = null): JsonResponse
    {
        try {
            $integration = new Integration();
            $response    = $integration->getFolder($request->session()->get('token'), $uuid);

            $currentFolder = null;

            if (isset($response['status']) && $response['status'] && isset($response['data'])) {
                $currentFolder = $response['data'];
            }

            $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));

            if (! $receiver->isUploaded()) {
                return response()->json(['error' => 'Nenhum arquivo foi enviado!'], 400);
            }

            $save = $receiver->receive();

            // Se ainda estiver recebendo os chunks, retorna progresso (Dropzone espera status 200!)
            if (! $save->isFinished()) {
                return response()->json([
                    'status' => true,
                    'done'   => $save->handler()->getPercentageDone(), // opcional: Dropzone não usa, mas é bom
                ]);
            }

            // Upload completo: agora salva de fato
            $file = $save->getFile();

            $data = [
                'path'      => null,
                'name'      => $file->getClientOriginalName(),
                'size'      => $file->getSize(),
                'type'      => $file->gettype(),
                'mime_type' => $file->getMimeType(),
                'ext'       => $file->getClientOriginalExtension(),
                'parent_id' => $currentFolder ? $currentFolder['id'] : null,
                'isFile'    => 'S',
            ];

            // Nome único para salvar o arquivo
            $fileName = sha1(Str::random(191));

            // Armazena usando o driver definido.
            $data['path'] = $file->storeAs('', $fileName, 'driver_tool');

            // Limpa o diretório do chunks.
            if (File::isDirectory(storage_path('app/private/chunks'))) {
                File::cleanDirectory(storage_path('app/private/chunks'));
            }

            // Salva os dados no banco.
            $create = $integration->fileCreate($request->session()->get('token'), $uuid, $data);

            if (isset($create['status_code']) && $create['status_code'] === 422) {
                return response()->json([
                    'success' => false,
                    'message' => $create['error'],
                ], status: 400);
            }

            if (isset($create['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => $create['error'],
                ], 400);
            }

            if (isset($create['status_code']) && $create['status_code'] === 200) {
                if (isset($create['status'])) {
                    if ($create['status']) {
                        return $this->successResponse($create, 'Arquivos enviados com sucesso!');
                    }
                }
            }

            Storage::disk('driver_tool')->delete($fileName);
            return response()->json(['success' => false, 'error' => 'Erro ao salvar no banco.'], 400);

        } catch (\Exception $error) {
            return response()->json([
                'success' => false,
                'message' => $error->getMessage(),
            ], 500);
        }
    }

}
