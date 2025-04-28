import { LinkProps, NavigatePages } from '@/components/navgate-pages';
import { DefaultApp } from '@/layouts/DefaultApp';
import { Head } from '@inertiajs/react';
import { FormCreate } from './components/form-update';

export default function Create() {
    const links: LinkProps[] = [
        {
            title: 'Usuários',
            path: route('app.users.index'),
            active: false,
        },
        {
            title: 'Novo Usuário',
            path: '',
            active: true,
        },
    ];

    return (
        <DefaultApp>
            <Head title="Novo Usuário" />
            <div className="w-full">
                <NavigatePages links={links} />
            </div>
            <div className="mt-4 rounded-xl bg-white px-8 py-4 dark:bg-secondary xl:max-w-[60vw]">
                <div>
                    <h2 className="text-lg">Novo Usuário</h2>
                </div>
                <FormCreate />
            </div>
        </DefaultApp>
    );
}
