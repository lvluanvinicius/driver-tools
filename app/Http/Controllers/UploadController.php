<?php
namespace App\Http\Controllers;

use App\Models\Files;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    public function index(string | null $uuid = null): InertiaResponse
    {
        $currentFolder = $this->modelFiles->where('uuid', $uuid)->where('is_folder', 'Y')->first() ?: null;

        return Inertia::render('Files/Upload/Index', [
            'uuid'   => $uuid,
            'folder' => $currentFolder,
        ]);
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
            // Recupera a pasta atual se estiver dentro de uma pasta.
            $currentFolder = $this->modelFiles->where('uuid', $uuid)->where('is_folder', 'Y')->first() ?: null;

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
                'parent_id' => $currentFolder?->id,
                'user_id'   => $request->user()->id,
            ];

            // Nome único para salvar o arquivo
            $fileName = sha1(Str::random(191));

            // Armazena usando o driver definido.
            $data['path'] = $file->storeAs('', $fileName, 'driver_tool');

            // Salva os dados no banco.
            $create = $this->modelFiles->create($data);

            // Exclui arquivo se não foi salvo no banco.
            if (! $create) {
                Storage::disk('driver_tool')->delete($fileName);
                return response()->json(['error' => 'Erro ao salvar no banco.'], 500);
            }

            return $this->successResponse($create, 'Arquivos enviados com sucesso!');

        } catch (\Exception $error) {
            return response()->json([
                'success' => false,
                'message' => $error->getMessage(),
            ], 500);
        }
    }

}
