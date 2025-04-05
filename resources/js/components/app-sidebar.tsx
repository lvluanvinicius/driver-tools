import {
    Files,
    HardDrive,
    LayoutDashboardIcon,
    SettingsIcon,
    Users,
} from 'lucide-react';
import * as React from 'react';

import { NavMain } from '@/components/nav-main';
import { NavSecondary } from '@/components/nav-secondary';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';

const data = {
    navMain: [
        {
            title: 'Painel',
            url: route('app.dashboard'),
            icon: LayoutDashboardIcon,
        },
        {
            title: 'Arquivos',
            url: route('app.files.index'),
            icon: Files,
        },
    ],
    navSecondary: [
        {
            title: 'Configurações',
            url: '#',
            icon: SettingsIcon,
        },
        {
            title: 'Usuários',
            url: route('app.users.index'),
            icon: Users,
        },
    ],
};

export function AppSidebar({ ...props }: React.ComponentProps<typeof Sidebar>) {
    return (
        <Sidebar collapsible="offcanvas" {...props}>
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton
                            asChild
                            className="data-[slot=sidebar-menu-button]:!p-1.5"
                        >
                            <div className="flex items-center">
                                <HardDrive className="h-5 w-5" />

                                <span className="text-base font-semibold">
                                    Driver Tools
                                </span>
                            </div>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>
            <SidebarContent>
                <NavMain items={data.navMain} />
                <NavSecondary items={data.navSecondary} className="mt-auto" />
            </SidebarContent>
            <SidebarFooter>
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
