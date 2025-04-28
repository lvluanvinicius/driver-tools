import { DefaultApp } from '@/layouts/DefaultApp';
import { UserInterface } from '@/types/user';
import { Head } from '@inertiajs/react';
import { FormUpdate } from './components/form-update';

export default function Index({ profile }: { profile: UserInterface }) {
    return (
        <DefaultApp>
            <Head title={profile.name} />
            <div className="mb-4 flex w-full flex-col">
                <div className="w-full">
                    <h1 className="text-2xl font-bold">{profile.name}</h1>
                </div>

                <div className="mt-4 rounded-xl bg-white px-8 py-4 dark:bg-secondary xl:max-w-[60vw]">
                    <FormUpdate user={profile} />
                </div>
            </div>
        </DefaultApp>
    );
}
