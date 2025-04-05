import { Button } from '@/components/ui/button';
import { Download } from 'lucide-react';

export function FileDownload({ fileId }: { fileId: string }) {
    function download() {
        if (typeof window != 'undefined') {
            window.open(route('app.files.download', fileId), '_blank');
        }
    }

    return (
        <Button
            variant={'outline'}
            size={'icon'}
            title="Baixar arquivo"
            className="h-8 w-8"
            onClick={download}
        >
            <Download />
        </Button>
    );
}
