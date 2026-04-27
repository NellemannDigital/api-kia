"use client"

import * as routes from '@/routes/synchronization/index';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import { RequestPayload } from '@inertiajs/core';

import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import HeadingSmall from '@/components/heading-small';
import { toast } from "sonner";
import { RefreshCcw } from 'lucide-react';
import { Checkbox } from "@/components/ui/checkbox";
import { Button } from '@/components/ui/button';
import { Label } from "@/components/ui/label";
import { Field, FieldGroup } from "@/components/ui/field";

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Synchronization', href: '/synchronization' }
];

export default function Index() {
  const [selectedPimJobs, setSelectedPimJobs] = useState<string[]>([]);
  const [loading, setLoading] = useState(false);

  const togglePimJob = (job: string) => {
    setSelectedPimJobs(prev =>
      prev.includes(job) ? prev.filter(j => j !== job) : [...prev, job]
    );
  };

  const startSync = (route: { url: () => string }, payload?: RequestPayload) => {
    setLoading(true);
    router.post(route.url(), payload ?? {}, {
      preserveScroll: true,
      onSuccess: () => toast.success('Sync started.'),
      onError: (errors) => toast.error(`Failed to start sync: ${JSON.stringify(errors)}`),
      onFinish: () => setLoading(false),
    });
  };

  const startGeneration = (route: { url: () => string }, payload?: RequestPayload) => {
    setLoading(true);
    router.post(route.url(), payload ?? {}, {
      preserveScroll: true,
      onSuccess: () => toast.success('Generation started.'),
      onError: (errors) => toast.error(`Failed to start generation: ${JSON.stringify(errors)}`),
      onFinish: () => setLoading(false),
    });
  };

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Synchronization" />
      <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <div className="grid auto-rows-min gap-4 md:grid-cols-3">

          {/* PIM */}
          <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4 space-y-4">
            <HeadingSmall title="Struct PIM" description="Start Struct PIM synchronization" />
            <FieldGroup className="border rounded-lg gap-3 p-3">
              {['cars', 'configurations', 'accessories'].map(job => (
                <Field key={job} orientation="horizontal">
                  <Checkbox
                    id={`job-${job}`}
                    name={`job-${job}`}
                    checked={selectedPimJobs.includes(job)}
                    onCheckedChange={() => togglePimJob(job)}
                    className="cursor-pointer"
                  />
                  <Label htmlFor={`job-${job}`} className="cursor-pointer capitalize">{job}</Label>
                </Field>
              ))}
            </FieldGroup>
            <Button
              onClick={() => startSync(routes.pim, { jobs: selectedPimJobs })}
              disabled={selectedPimJobs.length === 0 || loading}
              className="flex items-center gap-2"
            >
              <RefreshCcw className="h-5 w-5" /> Sync
            </Button>
          </div>

          {/* Dealers */}
          <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4 space-y-4">
            <HeadingSmall title="Dealers" description="Start dealers synchronization" />
            <Button
              onClick={() => startSync(routes.dealers)}
              disabled={loading}
              className="flex items-center gap-2"
            >
              <RefreshCcw className="h-5 w-5" /> Sync
            </Button>
          </div>

          {/* Price Lists */}
          <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4 space-y-4">
            <HeadingSmall title="Price Lists" description="Start price list generation" />
            <Button
              onClick={() => startGeneration(routes.priceList)}
              disabled={loading}
              className="flex items-center gap-2"
            >
              <RefreshCcw className="h-5 w-5" /> Generate
            </Button>
          </div>

          {/* Used Cars */}
          <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4 space-y-4">
            <HeadingSmall title="Used Cars" description="Start used cars synchronization" />
            <Button
              onClick={() => startSync(routes.usedCars)}
              disabled={loading}
              className="flex items-center gap-2"
            >
              <RefreshCcw className="h-5 w-5" /> Sync
            </Button>
          </div>

          {/* Stock Cars */}
          <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4 space-y-4">
            <HeadingSmall title="Stock Cars" description="Start stock cars synchronization" />
            <Button
              onClick={() => startSync(routes.stockCars)}
              disabled={loading}
              className="flex items-center gap-2"
            >
              <RefreshCcw className="h-5 w-5" /> Sync
            </Button>
          </div>

        </div>
      </div>
    </AppLayout>
  );
}