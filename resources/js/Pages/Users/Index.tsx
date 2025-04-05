import { TablePaginate } from '@/components/table-paginate';
import { DefaultApp } from '@/layouts/DefaultApp';
import { ApiResponse } from '@/types/api';
import { UserInterface } from '@/types/user';
import { TableUsers } from './components/table-users';

interface UsersProps {
    data: ApiResponse<UserInterface[]>;
}

export default function Users({ data }: UsersProps) {
    return (
        <DefaultApp>
            <div className="mb-4 flex w-full items-center justify-between">
                <div className="justify-end">teste</div>

                <div className="flex items-center gap-2">teste</div>
            </div>
            <TableUsers data={data.data} />
            <div className="mt-4 px-4 py-2">
                <TablePaginate paginate={data} />
            </div>
        </DefaultApp>
    );
}
