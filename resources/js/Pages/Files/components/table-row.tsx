import { TableCell, TableRow } from '@/components/ui/table';
import { formatFileSize } from '@/tools/formatter';
import { FileInterface } from '@/types/file';
import { Link } from '@inertiajs/react';
import { FileArchive, Folder } from 'lucide-react';
import { FileDelete } from './file-delete';
import { FileDownload } from './file-download';
import { FileEditName } from './file-edit-name';

export function TableFilesRow({
    data,
    parentId,
}: {
    data: FileInterface;
    parentId: string | null;
}) {
    return (
        <TableRow className="border-b-4 border-background">
            <TableCell className="whitespace-nowrap !py-2">
                {data.is_folder == 'Y' ? (
                    <Link
                        href={route('app.files.folder.index', data.uuid)}
                        className="flex items-center gap-2 hover:text-blue-500"
                    >
                        <Folder strokeWidth={0} fill={'orange'} />
                        {data.name}
                    </Link>
                ) : (
                    <div className="flex items-center gap-2">
                        <FileArchive className="text-blue-500" />
                        {data.name}
                    </div>
                )}
            </TableCell>
            <TableCell className="whitespace-nowrap !py-2">
                {data.is_folder == 'Y' ? '-' : formatFileSize(data.size)}
            </TableCell>
            <TableCell className="whitespace-nowrap !py-2">
                {data.created_at}
            </TableCell>
            <TableCell className="whitespace-nowrap !py-2">
                <div className="flex items-center gap-2">
                    <div className="flex min-w-[10rem] items-center justify-end gap-2">
                        {data.is_folder == 'N' && (
                            <FileDownload fileId={data.uuid} />
                        )}

                        <FileEditName file={data} parentId={parentId} />

                        <FileDelete file={data} parentId={parentId} />
                    </div>
                </div>
            </TableCell>
        </TableRow>
    );
}
