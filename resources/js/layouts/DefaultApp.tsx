import { AppSidebar } from '@/components/app-sidebar';
import { SiteHeader } from '@/components/site-header';
import { useTheme } from '@/components/theme-provider';
import { SidebarInset, SidebarProvider } from '@/components/ui/sidebar';
import { usePage } from '@inertiajs/react';
import { ReactNode } from 'react';
import { toast, ToastContainer } from 'react-toastify';

function Toaster() {
    const { theme } = useTheme();
    return <ToastContainer theme={theme} position="top-right" />;
}

interface DefaultAppProps {
    children: ReactNode;
}

export function DefaultApp({ children }: DefaultAppProps) {
    const { flash } = usePage().props;
    const responseMessage = flash as {
        success?: string;
        error?: string;
    };

    if (responseMessage.error) {
        toast.error(responseMessage.error, {
            className: 'bg-white dark:bg-secondary',
        });
    }

    if (responseMessage.success) {
        toast.success(responseMessage.success, {
            className: 'bg-white dark:bg-secondary',
        });
    }

    return (
        <>
            <Toaster />
            <SidebarProvider>
                <AppSidebar />
                <SidebarInset className="overflow-y-auto">
                    <SiteHeader />
                    <main className="px-8 py-4">{children}</main>
                </SidebarInset>
            </SidebarProvider>
        </>
    );
}
