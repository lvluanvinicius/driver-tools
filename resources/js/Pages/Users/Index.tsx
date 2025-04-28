import { TablePaginate } from '@/components/table-paginate';
import { Button } from '@/components/ui/button';
import { DefaultApp } from '@/layouts/DefaultApp';
import { ApiResponse } from '@/types/api';
import { UserInterface } from '@/types/user';
import { Head, Link } from '@inertiajs/react';
import { TableUsers } from './components/table-users';

interface UsersProps {
    data: ApiResponse<UserInterface[]>;
}

export default function Users({ data }: UsersProps) {
    return (
        <DefaultApp>
            <Head title="Usuários" />
            <div className="mb-4 flex w-full items-center justify-between">
                <div className="justify-end">Usuários</div>

                <div className="flex items-center gap-2">
                    <Link href={route('app.users.create')}>
                        <Button>+ Novo</Button>
                    </Link>
                </div>
            </div>
            <TableUsers data={data.data} />
            <div className="mt-4 px-4 py-2">
                <TablePaginate paginate={data} />
            </div>
        </DefaultApp>
    );
}
