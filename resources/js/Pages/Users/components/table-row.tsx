import { TableCell, TableRow } from '@/components/ui/table';
import { UserInterface } from '@/types/user';

export function TableUsersRow({ data }: { data: UserInterface }) {
    return (
        <TableRow className="border-b-4 border-background">
            <TableCell className="whitespace-nowrap !py-2">
                {data.name}
            </TableCell>
            <TableCell className="whitespace-nowrap !py-2">
                {data.email}
            </TableCell>
            <TableCell className="whitespace-nowrap !py-2">
                {data.created_at}
            </TableCell>
            <TableCell className="whitespace-nowrap !py-2">
                <div className="flex items-center gap-2">
                    <div className="flex min-w-[10rem] items-center justify-end gap-2">
                        teste
                    </div>
                </div>
            </TableCell>
        </TableRow>
    );
}
