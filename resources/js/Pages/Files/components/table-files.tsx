import {
    Table,
    TableBody,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { FileInterface } from '@/types/file';
import { TableFilesRow } from './table-row';

interface TableFilesProps {
    data: FileInterface[];
    parentId: string | null;
}

export function TableFiles({ data, parentId }: TableFilesProps) {
    return (
        <div className="bg-white dark:bg-secondary">
            <Table>
                <TableHeader className="border-b-4 border-background">
                    <TableRow className="border-b-4 border-background">
                        <TableHead className="whitespace-nowrap py-4">
                            Nome
                        </TableHead>
                        <TableHead className="whitespace-nowrap py-4">
                            Tamanho
                        </TableHead>
                        <TableHead className="whitespace-nowrap py-4">
                            Enviado em
                        </TableHead>
                        <TableHead className="whitespace-nowrap py-4"></TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>
                    {data.map(function (d, index) {
                        return (
                            <TableFilesRow
                                key={index}
                                data={d}
                                parentId={parentId}
                            />
                        );
                    })}
                </TableBody>
            </Table>
        </div>
    );
}
