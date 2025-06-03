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
import { useForm } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { FormEvent, useState } from 'react';

interface FolderCreateProps {
    parentId: string | null;
}

export function FolderCreate({ parentId }: FolderCreateProps) {
    const [open, setOpen] = useState(false);

    const routeCreate = parentId
        ? route('app.files.folder.store', parentId)
        : route('app.files.folder.store');

    const { data, setData, errors, post, reset, processing } = useForm({
        name: '',
    });

    function handleSubmit(e: FormEvent<HTMLFormElement>) {
        e.preventDefault();

        post(routeCreate, {
            onSuccess() {
                reset();
                setOpen(false);
            },
        });
    }

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                <Button size={'sm'} title="Criar nova pasta">
                    <Plus /> Nova Pasta
                </Button>
            </DialogTrigger>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Nova Pasta</DialogTitle>
                    <DialogDescription>
                        De um nome para a nova pasta que você está criando.
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
                        <DialogClose className="h-8">Cancelar</DialogClose>
                        <Button className="h-8 min-w-[5rem]" type="submit">
                            {processing ? 'Aguarde...' : 'Criar'}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
