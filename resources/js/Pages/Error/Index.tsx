import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { DefaultApp } from '@/layouts/DefaultApp';
import { Head } from '@inertiajs/react';

export default function Index({
    error,
    code,
}: {
    error: string;
    code: number;
}) {
    return (
        <DefaultApp>
            <Head title="Erro" />
            <Card className="bg-secondary p-4">
                <CardHeader>
                    <CardTitle className="text-xl font-bold">
                        Oooooops! Erro {code}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <p className="text-muted-foreground">{error}</p>
                </CardContent>
            </Card>
        </DefaultApp>
    );
}
