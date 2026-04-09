import { useState } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { Transition } from '@headlessui/react';
import { Clipboard, Check, Trash, Plus } from 'lucide-react';
import { toast } from 'sonner';

import * as routes from '@/routes/api-tokens/index';
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
  flash?: { token?: string };
  tokens?: Token[]; 
  errors?: Record<string, string>;
  [key: string]: any; 
}

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'API Tokens', href: '/settings/api-tokens' },
];

export default function ApiTokens() {
  const { props } = usePage<PageProps>();
  const flashToken = props.flash?.token;

  const [tokens, setTokens] = useState<Token[]>(props.tokens ?? []);
  const [tokenName, setTokenName] = useState('');
  const [copied, setCopied] = useState(false);

  const generateToken = (e: React.FormEvent) => {
    e.preventDefault();
    if (!tokenName.trim()) return;

    router.post<PageProps>(
      routes.store(),
      { name: tokenName },
      {
        onSuccess: (page) => {
          const props = page.props as PageProps;
          if (props.flash?.token) {
            setTokens(props.tokens ?? []);
            setTokenName('');
            toast.success('Token generated successfully');
          }
        },
        onError: (err: any) =>
          toast.error(`Failed to generate token: ${err.message}`),
      }
    );
  };

  const revokeToken = (id: number) => {
    if (!confirm('Are you sure you want to revoke this token?')) return;

    router.delete<PageProps>(
      routes.destroy(id),
      {
        onSuccess: (page) => {
          const props = page.props as PageProps;
          setTokens(props.tokens ?? []);
          toast.success('Token revoked successfully');
        },
        onError: (err: any) =>
          toast.error(`Failed to revoke token: ${err.message}`),
      }
    );
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

          {/* Generate Token Form */}
          <form onSubmit={generateToken} className="space-y-2">
            <Label htmlFor="name">Token name</Label>
            <div className="flex gap-4 mt-1">
              <Input
                id="name"
                name="name"
                placeholder="Enter token name"
                value={tokenName}
                onChange={(e) => setTokenName(e.target.value)}
                className="block"
              />
              <Button type="submit">Generate token</Button>
            </div>
            <InputError message={props.errors?.name} className="mt-1" />
          </form>

          {/* Flash Token Display */}
          <Transition
            show={!!flashToken}
            enter="transition ease-in-out duration-150"
            enterFrom="opacity-0"
            enterTo="opacity-100"
            leave="transition ease-in-out duration-150"
            leaveTo="opacity-0"
          >
            {flashToken && (
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
                            navigator.clipboard.writeText(flashToken);
                            setCopied(true);
                            setTimeout(() => setCopied(false), 2000);
                          }}
                        >
                          {copied ? <Check /> : <Clipboard />}
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
                    value={flashToken}
                    className="w-full pr-12"
                  />
                </div>
              </div>
            )}
          </Transition>

          {/* Existing Tokens */}
          {tokens.length > 0 && (
            <div className="mt-12 border rounded-xl overflow-hidden">
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
                        {token.last_used_at &&
                          `· Last used: ${token.last_used_at}`}
                      </p>
                    </div>

                    <Button
                      size="icon"
                      variant="outline"
                      onClick={() => revokeToken(token.id)}
                    >
                      <Trash />
                      <span className="sr-only">Revoke</span>
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