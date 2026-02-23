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
  const [selectedPimJobs, setSelectedPimJobs] = useState<string[]>([]);

  const togglePimJob = (job: string) => {
    setSelectedPimJobs(prev =>
      prev.includes(job) ? prev.filter(j => j !== job) : [...prev, job]
    );
  };

  const startPimSync = async () => {
    if (selectedPimJobs.length === 0) {
      toast.error('Select at least one job before syncing.');
      return;
    }

    try {
      const { data } = await axios.post(
        '/synchronization/pim',
        { jobs: selectedPimJobs }
      );
      toast.success(`Sync started: ${data.output}`);
    } catch (err: any) {
      toast.error(`Failed to start sync: ${err.message}`);
    } finally {}
  };

  const startDealerSync = async () => {
    try {
      const { data } = await axios.post(
        '/synchronization/dealers'
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
                description="Start Struct PIM synchronization"
            />
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
                  onClick={startPimSync}
                  disabled={selectedPimJobs.length === 0}
                  className="flex items-center gap-2 cursor-pointer"
              >
                  <RefreshCcw className="h-5 w-5" />
                  Start Sync
              </Button>
          </div>
          <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4 space-y-4">
            <HeadingSmall
                title="Dealers"
                description="Start dealers synchronization"
            />
              <Button
                  onClick={startDealerSync}
                  className="flex items-center gap-2 cursor-pointer"
              >
                  <RefreshCcw className="h-5 w-5" />
                  Start Sync
              </Button>
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
