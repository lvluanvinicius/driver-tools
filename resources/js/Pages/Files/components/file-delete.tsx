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
import { FileInterface } from '@/types/file';
import { router } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';
import { useState } from 'react';

export function FileDelete({
    file,
    parentId,
}: {
    file: FileInterface;
    parentId: string | null;
}) {
    const [open, setOpen] = useState(false);
    function handleDelete() {
        router.delete(route('app.files.a.destroy', [file.uuid, parentId]));
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
                    Confirma a exclusão do arquivo {file.name}?
                </AlertDialogDescription>

                <AlertDialogFooter>
                    <Button onClick={handleDelete}>Confirmar</Button>
                    <AlertDialogCancel>Cancelar</AlertDialogCancel>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    );
}
