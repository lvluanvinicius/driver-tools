import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Head, useForm } from '@inertiajs/react';
import { FormEvent } from 'react';

export function Form({ error }: { error: null | string }) {
    const { post, setData, data, errors } = useForm({
        username: '',
        password: '',
    });

    function handleSignIn(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();
        post(route('sign-in.store'));
    }

    return (
        <div className="border-3 fixed left-[50%] top-[50%] translate-x-[-50%] translate-y-[-50%]">
            <Head title="Login" />
            <form
                onSubmit={handleSignIn}
                className="flex h-[20rem] w-[25rem] flex-col justify-center gap-4 rounded-xl border px-8 shadow shadow-black/30 dark:bg-secondary dark:shadow-black"
            >
                <div className="flex flex-col gap-2">
                    <div className="flex w-full items-end justify-between gap-4">
                        <span>Usuário: </span>{' '}
                        {errors.username && (
                            <p className="text-xs text-red-600">
                                {errors.username}
                            </p>
                        )}
                    </div>
                    <Input
                        type="text"
                        value={data.username}
                        placeholder="Usuário"
                        onChange={(e) =>
                            setData('username', e.currentTarget.value)
                        }
                    />
                </div>

                <div className="flex flex-col gap-2">
                    <div className="flex w-full items-end justify-between gap-4">
                        <span>Senha:</span>
                        {errors.password && (
                            <p className="text-xs text-red-600">
                                {errors.password}
                            </p>
                        )}
                    </div>
                    <Input
                        type="password"
                        value={data.password}
                        placeholder="Senha"
                        onChange={(e) =>
                            setData('password', e.currentTarget.value)
                        }
                    />
                </div>

                {error && (
                    <p className="text-center text-xs text-red-500">{error}</p>
                )}

                <div className="flex flex-col gap-2">
                    <Button>Entrar</Button>
                </div>
            </form>
        </div>
    );
}
