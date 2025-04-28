import { LinkProps, NavigatePages } from '@/components/navgate-pages';
import { DefaultApp } from '@/layouts/DefaultApp';
import { UserInterface } from '@/types/user';
import { Head } from '@inertiajs/react';
import { FormEdit } from './components/form-edit';

export default function Edit({ user }: { user: UserInterface }) {
    const links: LinkProps[] = [
        {
            title: 'Usuários',
            path: route('app.users.index'),
            active: false,
        },
        {
            title: user.name,
            path: '',
            active: true,
        },
    ];

    return (
        <DefaultApp>
            <Head title={user.name} />

            <div className="w-full">
                <NavigatePages links={links} />
            </div>
            <div className="mt-4 rounded-xl bg-white px-8 py-4 dark:bg-secondary xl:max-w-[60vw]">
                <div>
                    <h2 className="text-lg">Novo Usuário</h2>
                </div>
                <FormEdit user={user} />
            </div>
        </DefaultApp>
    );
}
