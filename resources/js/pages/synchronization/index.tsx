"use client"

import { Head } from '@inertiajs/react'
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { toast } from "sonner"
import { useState } from 'react';
import axios from 'axios';
import { RefreshCcw } from 'lucide-react';
import { Checkbox } from "@/components/ui/checkbox"
import { Button } from '@/components/ui/button';
import HeadingSmall from '@/components/heading-small';
import {
  Field,
  FieldGroup,
} from "@/components/ui/field"
import { Label } from "@/components/ui/label"

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Synchronization',
    href: '/synchronization'
  }
];

export default function Index() {
  const [selectedJobs, setSelectedJobs] = useState<string[]>([]);

  const toggleJob = (job: string) => {
    setSelectedJobs(prev =>
      prev.includes(job) ? prev.filter(j => j !== job) : [...prev, job]
    );
  };

  const startSync = async () => {
    if (selectedJobs.length === 0) {
      toast.error('Vælg mindst ét job før sync');
      return;
    }

    try {
      const { data } = await axios.post(
        '/synchronization/pim',
        { jobs: selectedJobs }
      );
      toast.success(`Sync started: ${data.output}`);
    } catch (err: any) {
      toast.error(`Failed to start sync: ${err.message}`);
    } finally {}
  };

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Synchronization" />
      <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <div className="grid auto-rows-min gap-4 md:grid-cols-3">
          <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4 space-y-4">
            <HeadingSmall
                title="Struct PIM"
                description="Start synchronization from Struct PIM"
            />
              <FieldGroup className="border rounded-lg gap-3 p-3">
                {['cars', 'configurations', 'accessories'].map(job => (
                  <Field key={job} orientation="horizontal">
                    <Checkbox
                      id={`job-${job}`}
                      name={`job-${job}`}
                      checked={selectedJobs.includes(job)}
                      onCheckedChange={() => toggleJob(job)}
                      className="cursor-pointer"
                    />
                    <Label htmlFor={`job-${job}`} className="cursor-pointer capitalize">{job}</Label>
                  </Field>
                ))}
              </FieldGroup>
              <Button
                  onClick={startSync}
                  disabled={selectedJobs.length === 0}
                  className="flex items-center gap-2 cursor-pointer"
              >
                  <RefreshCcw className="h-5 w-5" />
                  Start Sync
              </Button>
          </div>
          <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            
          </div>
          <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            
          </div>
        </div>
        <div className="relative min-h-[100vh] flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
          
        </div>
      </div>
    </AppLayout>
  );
}
