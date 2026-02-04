import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
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
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import { Braces, LayoutGrid, List, RefreshCcw } from 'lucide-react';
import AppLogo from './app-logo';
import { toast } from "sonner";
import axios from 'axios';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Cars',
        href: '/cars',
        icon: List,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Swagger',
        href: '/swagger',
        icon: Braces,
    }
];

export function AppSidebar() {
    const runPimDataSync = async () => {
        try {
            const { data } = await axios.post('/sync-pim-data');
            toast.success(`Sync started: ${data.output}`);
        } catch (err: any) {
            toast.error(`Failed to start sync: ${err.message}`);
        }
    };

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <div className="px-1 py-2">
                    <div className="text-xs leading-none text-zinc-400">Synchronization</div>
                </div>
                <div className="relative flex w-full min-w-0 flex-col p-2 group-data-[collapsible=icon]:p-0 mt-auto">
                    <div className="w-full text-sm">
                        <ul className="flex w-full min-w-0 flex-col gap-1">
                            <li className="relative">
                                <button
                                    onClick={runPimDataSync}
                                    className="flex w-full items-center gap-2 overflow-hidden rounded-md p-2 text-left outline-hidden ring-sidebar-ring transition-[width,height,padding] 
                                        focus-visible:ring-2 active:bg-sidebar-accent active:text-sidebar-accent-foreground disabled:pointer-events-none disabled:opacity-50 
                                        aria-disabled:pointer-events-none aria-disabled:opacity-50 group-data-[collapsible=icon]:size-8! group-data-[collapsible=icon]:p-2! 
                                        [&>span:last-child]:truncate [&>svg]:size-4 [&>svg]:shrink-0 hover:bg-sidebar-accent h-8 text-sm text-neutral-600 hover:text-neutral-800 
                                        dark:text-neutral-300 dark:hover:text-neutral-100 cursor-pointer"
                                    >
                                    <RefreshCcw className="h-5 w-5" />
                                    <span>Struct PIM</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
