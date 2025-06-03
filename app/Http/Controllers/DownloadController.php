<?php
namespace App\Http\Controllers;

use App\Models\Files;
use App\Services\Integration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    public function __construct(protected Files $modelFiles)
    {

    }

    /**
     * Efetua o download de um arquivo.
     * @author Luan Santos <lvluansantos@gmail.com>
     *
     * @param string $uuid
     * @return null | StreamedResponse
     */
    public function index(Request $request, string $uuid): null | StreamedResponse
    {
        try {
            $integration = new Integration();
            $response    = $integration->getFolder($request->session()->get('token'), $uuid);

            $file = null;

            if (isset($response['status']) && $response['status'] && isset($response['data'])) {
                $file = $response['data'];
            }

            if (! $file) {
                throw new \Exception('Registro de arquivo não encontrado!');
            }

            /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
            $disk = Storage::disk('driver_tool');

            if (! $disk->exists($file['path'])) {
                throw new \Exception('Dados do arquivo inexistente do diretório de armazenamento!');
            }

            return $disk->download($file['path'], $file['name']);

        } catch (\Exception $error) {
            return null;
        }
    }
}
