import {
    Table,
    TableBody,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { UserInterface } from '@/types/user';
import { TableUsersRow } from './table-row';

interface TableFilesProps {
    data: UserInterface[];
}

export function TableUsers({ data }: TableFilesProps) {
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
                        return <TableUsersRow key={index} data={d} />;
                    })}
                </TableBody>
            </Table>
        </div>
    );
}
