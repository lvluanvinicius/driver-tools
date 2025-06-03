import { DefaultApp } from '@/layouts/DefaultApp';
import { FileInterface } from '@/types/file';
import Dropzone from 'dropzone';
import { useEffect, useRef } from 'react';

import { Head, Link, usePage } from '@inertiajs/react';
import 'dropzone/dist/dropzone.css';
import { Undo2 } from 'lucide-react';

interface FileUploaderInterface {
    uuid: string | null;
    folder: FileInterface | null;
    csrf_token: string;
}

export default function FileUploader({
    uuid,
    folder,
    csrf_token,
}: FileUploaderInterface) {
    const { props } = usePage();

    const dropzoneRef = useRef<HTMLDivElement>(null);
    const dropzoneInstance = useRef<Dropzone | null>(null);

    useEffect(() => {
        if (!dropzoneRef.current) return;

        Dropzone.autoDiscover = false;

        // Evita reanexar múltiplas vezes
        if (dropzoneInstance.current) {
            dropzoneInstance.current.destroy();
            dropzoneInstance.current = null;
        }

        dropzoneInstance.current = new Dropzone(dropzoneRef.current, {
            url: uuid
                ? route('app.files.upload', uuid)
                : route('app.files.upload'),
            method: 'post',
            paramName: 'file',
            chunking: true,
            forceChunking: true,
            chunkSize: 5 * 1024 * 1024,
            retryChunks: true,
            retryChunksLimit: 5,
            parallelChunkUploads: false,
            maxFiles: 5,
            maxFilesize: 20480,
            addRemoveLinks: true,
            timeout: 0,
            acceptedFiles:
                'image/*,application/pdf,.zip,.rar,.docx,.xlsx,.csv,.mp4,.mkv',
            headers: {
                'X-CSRF-TOKEN': csrf_token,
            },
            init: function () {
                this.on('addedfile', (file) => {
                    console.log('Arquivo adicionado:', file);
                });

                this.on('uploadprogress', (_, progress) => {
                    console.log(`Progresso: ${progress.toFixed(2)}%`);
                });

                this.on('success', (_, response) => {
                    console.log('Upload finalizado:', response);
                });

                this.on('error', (_, errorMessage) => {
                    console.error('Erro no upload:', errorMessage);
                });
            },
        });

        return () => {
            if (dropzoneInstance.current) {
                dropzoneInstance.current.destroy();
                dropzoneInstance.current = null;
            }
        };
    }, [uuid]);

    return (
        <DefaultApp>
            <Head title="Novo Arquivo" />
            <div className="flex flex-col justify-center">
                <Link
                    href={
                        folder
                            ? route('app.files.folder.index', folder.uuid)
                            : route('app.files.index')
                    }
                    className="mb-4 flex w-[13rem] items-center gap-2 rounded-lg px-4 py-1 hover:bg-gray-200 dark:hover:bg-gray-500"
                >
                    <Undo2 /> {folder ? folder.name : 'Raiz (/)'}
                </Link>
            </div>
            <div
                ref={dropzoneRef}
                className="dropzone rounded-xl border border-dashed p-8 text-center dark:bg-secondary"
            />
        </DefaultApp>
    );
}
