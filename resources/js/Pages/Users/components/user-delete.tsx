import {
    AlertDialog,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { router } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';
import { useState } from 'react';

export function UserDelete({ user }: { user: string }) {
    const [open, setOpen] = useState(false);
    function handleDelete() {
        router.delete(route('app.users.destroy', user));
        setOpen(false);
    }

    return (
        <AlertDialog open={open} onOpenChange={setOpen}>
            <AlertDialogTrigger asChild>
                <Button
                    size={'icon'}
                    variant={'destructive'}
                    title="Remover arquivo"
                    className="h-8 w-8"
                >
                    <Trash2 />
                </Button>
            </AlertDialogTrigger>
            <AlertDialogContent>
                <AlertDialogTitle>Deseja prosseguir?</AlertDialogTitle>
                <AlertDialogDescription>
                    Confirma a exclusão desse usuário?
                </AlertDialogDescription>

                <AlertDialogFooter>
                    <Button onClick={handleDelete}>Confirmar</Button>
                    <AlertDialogCancel>Cancelar</AlertDialogCancel>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    );
}
