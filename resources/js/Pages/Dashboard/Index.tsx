import { DefaultApp } from '@/layouts/DefaultApp';

export function Page() {
    return (
        <DefaultApp>
            <div>teste</div>
        </DefaultApp>
    );
}

export default function Index() {
    return <Page />;
}
