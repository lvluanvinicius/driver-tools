import { DefaultApp } from '@/layouts/DefaultApp';
import { Link } from '@inertiajs/react';

export function Page() {
    return (
        <DefaultApp>
            <div>
                <h1 className="text-2xl font-bold">Bem vindo!</h1>
                <h3>
                    Segue para os arquivos:{' '}
                    <Link href={route('app.files.index')}>Arquivos {'->'}</Link>
                </h3>
            </div>
        </DefaultApp>
    );
}

export default function Index() {
    return <Page />;
}
