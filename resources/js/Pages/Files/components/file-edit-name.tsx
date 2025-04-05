import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { FileInterface } from '@/types/file';
import { useForm } from '@inertiajs/react';
import { Edit } from 'lucide-react';
import { FormEvent, useEffect, useState } from 'react';

export function FileEditName({
    file,
    parentId,
}: {
    file: FileInterface;
    parentId: string | null;
}) {
    const [open, setOpen] = useState(false);
    const { data, setData, errors, put, recentlySuccessful } = useForm({
        name: file.name,
    });

    const routeCreate = route('app.files.a.update', [file.uuid, parentId]);

    function handleSubmit(e: FormEvent<HTMLFormElement>) {
        e.preventDefault();

        put(routeCreate);
    }

    useEffect(() => {
        if (recentlySuccessful) {
            setOpen(false);
        }
    }, [recentlySuccessful]);

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                <Button
                    size={'icon'}
                    title="Editar nome do arquivo"
                    className="h-8 w-8"
                >
                    <Edit />
                </Button>
            </DialogTrigger>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Alterar Nome</DialogTitle>
                    <DialogDescription>
                        Você está editando apenas o nome.
                    </DialogDescription>
                </DialogHeader>

                <form onSubmit={handleSubmit} className="flex flex-col gap-4">
                    <div className="flex flex-col gap-2">
                        <span>Nome</span>
                        <Input
                            value={data.name}
                            onChange={(e) =>
                                setData('name', e.currentTarget.value)
                            }
                        />

                        {errors.name && (
                            <p className="text-xs text-red-500">
                                {errors.name}
                            </p>
                        )}
                    </div>

                    <DialogFooter>
                        <DialogClose>Cancelar</DialogClose>
                        <Button type="submit">Atualizar</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
