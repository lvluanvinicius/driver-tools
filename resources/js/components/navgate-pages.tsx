import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbList,
} from '@/components/ui/breadcrumb';
import { Link } from '@inertiajs/react';

export interface LinkProps {
    title: string;
    path: string;
    active: boolean;
}

interface NavigatePagesProps {
    links: LinkProps[];
}

export function NavigatePages({ links }: NavigatePagesProps) {
    return (
        <Breadcrumb className="font-semibold text-muted-foreground">
            <BreadcrumbList>
                {links.map(function (lk, index) {
                    return (
                        <BreadcrumbItem key={index}>
                            {!lk.active ? (
                                <Link href={lk.path}>
                                    {lk.title} {!lk.active ? '/' : null}
                                </Link>
                            ) : (
                                <span className="text-primary">
                                    {lk.title} {!lk.active ? '/' : null}
                                </span>
                            )}
                        </BreadcrumbItem>
                    );
                })}
            </BreadcrumbList>
        </Breadcrumb>
    );
}
