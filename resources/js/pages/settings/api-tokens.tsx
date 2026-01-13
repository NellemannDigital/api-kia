import { router, Form, Head, usePage } from '@inertiajs/react';
import { Transition } from '@headlessui/react';
import { useState } from 'react';
import { Clipboard, Check } from 'lucide-react';

import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import HeadingSmall from '@/components/heading-small';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import InputError from '@/components/input-error';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { type BreadcrumbItem } from '@/types';

interface Token {
    id: number;
    name: string;
    created_at: string;
    last_used_at?: string | null;
}

interface PageProps {
    flash: { token?: string };
    tokens: Token[];
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'API Tokens', href: '/settings/api-tokens' },
];

export default function ApiTokens() {
    const { props } = usePage<PageProps>();
    const token = props.flash?.token;
    const [copied, setCopied] = useState(false);
    const [tokens, setTokens] = useState<Token[]>(props.tokens);

    const revokeToken = (id: number) => {
    if (!confirm('Are you sure you want to revoke this token?')) return;

    router.delete(`/settings/api-tokens/${id}`, {
        onSuccess: () => {
            // Fjern token fra lokal state
            setTokens((prev) => prev.filter((token) => token.id !== id));
        },
    });
};

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="API Tokens" />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall
                        title="Generate API Token"
                        description="Generate API token for external access"
                    />

                    
                    <Form action="/settings/api-tokens" method='POST'>
                        <Label htmlFor="name">Token name</Label>
                        <div className="flex gap-4 mt-1">
                            <Input
                                id="name"
                                name="name"
                                placeholder="Enter token name"
                                className="block"
                            />
                            <Button type="submit">Generate token</Button>
                        </div>
                        <InputError message={props.errors?.name} className="mt-1" />
                    </Form>

                    <Transition
                        show={typeof token === 'string'}
                        enter="transition ease-in-out duration-150"
                        enterFrom="opacity-0"
                        enterTo="opacity-100"
                        leave="transition ease-in-out duration-150"
                        leaveTo="opacity-0"
                    >
                        {token && (
                            <div className="rounded-lg border p-4 mt-6">
                                <p className="text-sm">
                                    Copy this token now — you won’t see it again:
                                </p>
                                <div className="mt-2 relative w-full">
                                    <TooltipProvider>
                                        <Tooltip>
                                        <TooltipTrigger asChild>
                                            <Button
                                            type="button"
                                            variant="ghost"
                                            size="sm"
                                            className="absolute right-0.5 top-1/2 -translate-y-1/2"
                                            onClick={() => {
                                                if (token) {
                                                navigator.clipboard.writeText(token);
                                                setCopied(true);
                                                setTimeout(() => setCopied(false), 2000);
                                                }
                                            }}
                                            >
                                            {copied ? (
                                                <Check />
                                            ) : (
                                                <Clipboard />
                                            )}
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent side="top">
                                            <p>Copy to Clipboard</p>
                                        </TooltipContent>
                                        </Tooltip>
                                    </TooltipProvider>

                                    <Input
                                        type="text"
                                        readOnly
                                        value={token ?? ""}
                                        className="w-full pr-12"
                                    />
                                </div>
                            </div>
                        )}
                    </Transition>

                    {tokens && tokens.length > 0 && (
                        <div className="mt-12">
                            <HeadingSmall
                                title="Existing Tokens"
                                description="Active API tokens linked to your account"
                            />
                            <div className="space-y-2 mt-4">
                                {tokens.map((token) => (
                                    <div
                                        key={token.id}
                                        className="flex justify-between items-center p-3 border rounded-lg"
                                    >
                                        <div>
                                            <p className="text-sm font-medium">{token.name}</p>
                                            <p className="text-xs text-muted-foreground">
                                                Created: {token.created_at}{' '}
                                                {token.last_used_at && `· Last used: ${token.last_used_at}`}
                                            </p>
                                        </div>

                                        <Button
                                            type="button"
                                            variant="destructive"
                                            size="sm"
                                            onClick={() => revokeToken(token.id)}
                                        >
                                            Revoke
                                        </Button>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}

                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
