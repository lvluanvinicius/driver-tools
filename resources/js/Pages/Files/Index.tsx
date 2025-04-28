import { TablePaginate } from '@/components/table-paginate';
import { Button } from '@/components/ui/button';
import { DefaultApp } from '@/layouts/DefaultApp';
import { ApiResponse } from '@/types/api';
import { FileInterface } from '@/types/file';
import { Head, Link } from '@inertiajs/react';
import { Folder } from 'lucide-react';
import { FolderCreate } from './components/folder-create';
import { TableFiles } from './components/table-files';

interface FileUploaderProps {
    data: ApiResponse<FileInterface[]>;
    breadcrumbs: FileInterface[] | [];
    uuid: string | null;
}

export default function FileUploader({
    data,
    breadcrumbs,
    uuid,
}: FileUploaderProps) {
    const parent =
        breadcrumbs.length <= 0 ? null : breadcrumbs[breadcrumbs.length - 1];

    return (
        <DefaultApp>
            <Head title="Arquivos" />
            <div className="mb-4 flex w-full items-center justify-between">
                <div className="justify-end">
                    {parent ? (
                        <Link
                            href={route('app.files.folder.index', parent.uuid)}
                            className="flex items-center gap-2 text-lg tracking-tight hover:text-gray-500 dark:text-white dark:hover:text-gray-300"
                        >
                            <Folder strokeWidth={0} fill={'orange'} />
                            {parent.name}
                        </Link>
                    ) : uuid ? (
                        <Link
                            href={route('app.files.index')}
                            className="flex items-center gap-2 text-lg tracking-tight hover:text-gray-500 dark:text-white dark:hover:text-gray-300"
                        >
                            <Folder strokeWidth={0} fill={'orange'} /> /
                        </Link>
                    ) : (
                        <div className="flex items-center gap-2 text-lg tracking-tight hover:text-gray-500 dark:text-white dark:hover:text-gray-300">
                            <Folder strokeWidth={0} fill={'orange'} /> Início
                        </div>
                    )}
                </div>

                <div className="flex items-center gap-2">
                    <FolderCreate parentId={uuid} />

                    <Link
                        href={
                            uuid
                                ? route('app.files.upload', uuid)
                                : route('app.files.upload')
                        }
                    >
                        <Button size={'sm'}>Adicionar Arquivo +</Button>
                    </Link>
                </div>
            </div>
            <TableFiles data={data.data} parentId={uuid} />
            <div className="mt-4 px-4 py-2">
                <TablePaginate paginate={data} />
            </div>
        </DefaultApp>
    );
}
