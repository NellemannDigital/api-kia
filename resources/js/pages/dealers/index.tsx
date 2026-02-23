"use client"

import { Head } from '@inertiajs/react';
import { Dealer } from '@/types/Dealer';
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

interface Props { dealers: Dealer[] }

type SyncState = { batchId: string | null, progress: number, finished: boolean }

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dealers', href: '/dealers' }];

export default function Index({ dealers }: Props) {
  const renderTypes = (type: Boolean) => type ? '✅' : '❌'


  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Cars" />
      <div className="p-4">
        <div className="border rounded-xl overflow-hidden">
          <Table>
            <TableHeader>
              <TableRow className="font-bold">
                <TableHead className="px-4">Name</TableHead>
                <TableHead>Address</TableHead>
                {['B2C', 'B2B', 'Service'].map((h, i) =>
                  <TableHead key={i} className="text-center">{h}</TableHead>
                )}
                <TableHead>Synced at</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {dealers.map(dealer => {
                const types = ['b2c', 'b2b', 'service'] as const
                return (
                  <TableRow key={dealer.id}>
                    <TableCell className="px-4">{dealer.name}</TableCell>
                    <TableCell>
                      {dealer.street_name} {dealer.street_number} <br />
                      {dealer.zip_code} {dealer.city}
                    </TableCell>
                    {types.map(ty => <TableCell key={ty} className="text-center">{renderTypes(dealer.types[ty])}</TableCell>)}
                    <TableCell>{dealer.synced_at}</TableCell>
                  </TableRow>
                )
              })}
            </TableBody>
          </Table>
        </div>
      </div>
    </AppLayout>
  )
}
