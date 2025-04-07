import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { cn } from '@/lib/utils';
import { useForm } from '@inertiajs/react';

export function FormCreate() {
    const { data, setData, errors, post, processing } = useForm({
        name: '',
        email: '',
        username: '',
        password: '',
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        post(route('app.users.store'));
    }

    return (
        <form onSubmit={submit}>
            <Separator className="my-4" />

            <Label className="grid gap-x-8 gap-y-6 sm:grid-cols-2">
                <div className="space-y-1">
                    <span className="text-base/7 font-semibold text-zinc-950 dark:text-white sm:text-sm/6">
                        Nome
                    </span>
                    <p className="text-base/6 text-zinc-500 dark:text-zinc-400 sm:text-sm/6">
                        Informe um nome e sobrenome para o usuário.
                    </p>
                </div>

                <div className="flex flex-col gap-2">
                    <Input
                        type="text"
                        value={data.name}
                        placeholder="Nome e Sobrenome"
                        className={cn(
                            'h-12 bg-background',
                            errors.name && 'border !border-destructive',
                        )}
                        onChange={(e) => setData('name', e.target.value)}
                    />
                    {errors.name && (
                        <p className="w-full text-destructive">{errors.name}</p>
                    )}
                </div>
            </Label>

            <Separator className="my-4" />

            <Label className="grid gap-x-8 gap-y-6 sm:grid-cols-2">
                <div className="space-y-1">
                    <span className="text-base/7 font-semibold text-zinc-950 dark:text-white sm:text-sm/6">
                        Usuário
                    </span>
                    <p className="text-base/6 text-zinc-500 dark:text-zinc-400 sm:text-sm/6">
                        Informe um nome de usuário para efetuar o login.
                    </p>
                </div>

                <div className="flex flex-col gap-2">
                    <Input
                        type="text"
                        value={data.username}
                        placeholder="Usuário para login"
                        className={cn(
                            'h-12 bg-background',
                            errors.username && 'border !border-destructive',
                        )}
                        onChange={(e) => setData('username', e.target.value)}
                    />
                    {errors.username && (
                        <p className="w-full text-destructive">
                            {errors.username}
                        </p>
                    )}
                </div>
            </Label>

            <Separator className="my-4" />

            <Label className="grid gap-x-8 gap-y-6 sm:grid-cols-2">
                <div className="space-y-1">
                    <span className="text-base/7 font-semibold text-zinc-950 dark:text-white sm:text-sm/6">
                        E-mail
                    </span>
                    <p className="text-base/6 text-zinc-500 dark:text-zinc-400 sm:text-sm/6">
                        Informe um e-mail para o usuário.
                    </p>
                </div>

                <div className="flex flex-col gap-2">
                    <Input
                        type="email"
                        value={data.email}
                        placeholder="E-mail"
                        className={cn(
                            'h-12 bg-background',
                            errors.email && 'border !border-destructive',
                        )}
                        onChange={(e) => setData('email', e.target.value)}
                    />
                    {errors.email && (
                        <p className="w-full text-destructive">
                            {errors.email}
                        </p>
                    )}
                </div>
            </Label>

            <Separator className="my-4" />

            <Label className="grid gap-x-8 gap-y-6 sm:grid-cols-2">
                <div className="space-y-1">
                    <span className="text-base/7 font-semibold text-zinc-950 dark:text-white sm:text-sm/6">
                        Senha
                    </span>
                    <p className="text-base/6 text-zinc-500 dark:text-zinc-400 sm:text-sm/6">
                        Informe um senha para acesso. Ela deve conter no mínimo
                        8 caracteres, com letras e números.
                    </p>
                </div>

                <div className="flex flex-col gap-2">
                    <Input
                        type="password"
                        value={data.password}
                        placeholder="Senha"
                        className={cn(
                            'h-12 bg-background',
                            errors.password && 'border !border-destructive',
                        )}
                        onChange={(e) => setData('password', e.target.value)}
                    />
                    {errors.password && (
                        <p className="w-full text-destructive">
                            {errors.password}
                        </p>
                    )}
                </div>
            </Label>

            <Separator className="my-4" />

            <div className="col-span-12 flex items-center justify-end">
                <Button
                    disabled={processing}
                    type="submit"
                    className="h-10 w-full text-[1rem] md:w-[10rem]"
                >
                    {processing ? 'Aguarde...' : 'Criar'}
                </Button>
            </div>
        </form>
    );
}
