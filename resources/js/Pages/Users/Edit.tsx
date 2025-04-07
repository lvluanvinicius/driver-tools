import { DefaultApp } from '@/layouts/DefaultApp';
import { UserInterface } from '@/types/user';
import { FormEdit } from './components/form-edit';

export default function Edit({ user }: { user: UserInterface }) {
    return (
        <DefaultApp>
            <div className="mt-4 rounded-xl bg-white px-8 py-4 dark:bg-secondary xl:max-w-[60vw]">
                <div>
                    <h2 className="text-lg">Novo Usuário</h2>
                </div>
                <FormEdit user={user} />
            </div>
        </DefaultApp>
    );
}
