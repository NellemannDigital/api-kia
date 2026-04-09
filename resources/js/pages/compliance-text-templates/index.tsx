"use client"

import { ComplianceTextTemplate } from '@/types/ComplianceTextTemplate';
import * as routes from '@/routes/compliance-text-templates/index';
import { Head, Link, router } from '@inertiajs/react';

import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';

import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table"
import { Button } from "@/components/ui/button"

import { Pencil, Trash, Plus } from 'lucide-react';
import { toast } from "sonner"

interface Props { complianceTextTemplates: ComplianceTextTemplate[] }

const breadcrumbs: BreadcrumbItem[] = [
    { 
        title: 'Compliance text templates', 
        href: '/compliance-text-templates' 
    },
];

export default function Index({ complianceTextTemplates }: Props) {

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Compliance text templates" />
      
      <div className="p-4">
        <div className="border rounded-xl overflow-hidden">
          <Table>
            <TableHeader>
              <TableRow className="font-bold">
                <TableHead className="px-4">Variant</TableHead>
                <TableHead>Template</TableHead>
                <TableHead>Version</TableHead>
                <TableHead>Valid from</TableHead>
                <TableHead>Valid to</TableHead>
                <TableHead>Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {complianceTextTemplates.map(template => {
                return (
                  <TableRow key={template.id}>
                    <TableCell className="px-4">{template.variant}</TableCell>
                    <TableCell className="line-clamp-3 truncate max-w-200">{template.template}</TableCell>
                    <TableCell>{template.version}</TableCell>
                    <TableCell>
                      {template.valid_from ? new Date(template.valid_from).toLocaleDateString() : ""}
                    </TableCell>
                    <TableCell>
                      {template.valid_to ? new Date(template.valid_to).toLocaleDateString() : ""}
                    </TableCell>
                    <TableCell className="flex gap-2">
                      <Link href={routes.edit(template.id)}>
                        <Button size="icon">
                          <Pencil />
                          <span className="sr-only">Edit</span>
                        </Button>
                      </Link>
                      <Button
                          size="icon"
                          onClick={() => {
                            if (confirm('Are you sure you want to delete this template?')) {
                              router.delete(routes.destroy(template.id), {
                                onSuccess: () => {
                                  toast.success('Template deleted successfully');
                                },
                                onError: (err: any) => {
                                  toast.error(`Failed to delete template: ${err.message}`);
                                },
                              });
                            }
                          }}
                        >
                          <Trash />
                          <span className="sr-only">Delete</span>
                        </Button>
                    </TableCell>
                  </TableRow>
                )
              })}
            </TableBody>
          </Table>
        </div>
      </div>

      <Link href={routes.create()}>
        <Button className="absolute top-4 right-4" size="sm">
          <Plus /> New
        </Button>
      </Link>

    </AppLayout>
  )
}