import { transformSearchParams } from '@/tools/urls';
import { ApiResponse } from '@/types/api';
import { router, usePage } from '@inertiajs/react';
import {
    ChevronFirst,
    ChevronLast,
    ChevronLeft,
    ChevronRight,
} from 'lucide-react';
import { Button } from './ui/button';

interface TablePaginateProps {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    paginate: ApiResponse<any>;
}

export function TablePaginate({ paginate }: TablePaginateProps) {
    const { url } = usePage();
    const totalPages = Math.ceil(paginate.total / paginate.per_page);

    const handlePaginate = (action: 'previous' | 'next' | 'last' | 'first') => {
        const params: Record<string, string | number> = {};
        const uri = url.split('?')[0];
        const currentQuery = url.split('?')[1];
        // Inserindo todos os parametros dentro do objeto params.
        new URLSearchParams(currentQuery).forEach((v, k) => (params[k] = v));

        switch (action) {
            case 'next':
                if (params.page) {
                    const p = parseInt(params.page as string);

                    if (p < totalPages) {
                        params.page = p + 1;
                    }
                } else {
                    params.page = 2;
                }
                break;

            case 'previous':
                if (params.page) {
                    const p = parseInt(params.page as string);

                    if (p <= 2) {
                        delete params.page;
                    } else {
                        params.page = p - 1;
                    }
                }
                break;

            case 'last':
                params.page = totalPages;
                break;

            case 'first':
                delete params.page;
                break;

            default:
                break;
        }

        router.get(`${uri}?${transformSearchParams({ ...params })}`);
    };

    return (
        <div className="flex w-full items-center justify-between">
            <div />

            <div className="flex items-center gap-1">
                <Button
                    size={'icon'}
                    variant={'outline'}
                    onClick={() => handlePaginate('previous')}
                    disabled={paginate.current_page <= 1}
                >
                    <ChevronLeft />
                </Button>

                <Button
                    size={'icon'}
                    variant={'outline'}
                    onClick={() => handlePaginate('first')}
                    disabled={paginate.current_page <= 1}
                >
                    <ChevronFirst />
                </Button>

                <Button disabled size={'icon'}>
                    {paginate.current_page}
                </Button>

                <Button
                    size={'icon'}
                    variant={'outline'}
                    onClick={() => handlePaginate('last')}
                    disabled={paginate.current_page >= totalPages}
                >
                    <ChevronLast />
                </Button>

                <Button
                    size={'icon'}
                    variant={'outline'}
                    onClick={() => handlePaginate('next')}
                    disabled={paginate.current_page >= totalPages}
                >
                    <ChevronRight />
                </Button>
            </div>
        </div>
    );
}
