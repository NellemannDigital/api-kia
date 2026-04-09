"use client"

import { Head } from '@inertiajs/react'
import { Dealer } from '@/types/Dealer'
import AppLayout from '@/layouts/app-layout'
import { type BreadcrumbItem } from '@/types'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table"

interface Props {
  dealers: Dealer[]
}

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dealers', href: '/dealers' }
]

const TYPE_COLUMNS = [
  { key: 'b2c', label: 'B2C' },
  { key: 'b2b', label: 'B2B' },
  { key: 'service', label: 'Service' },
] as const

const TOOL_COLUMNS = [
  { key: 'test_drive', label: 'Test drive' },
  { key: 'sales_advisor', label: 'Sales advisor' },
  { key: 'insurance_calculator', label: 'Insurance calculator' },
  { key: 'book_service', label: 'Book service' },
] as const

const renderBoolean = (value?: boolean) => value ? '✅' : '❌'

export default function Index({ dealers }: Props) {
  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Dealers" />

      <div className="p-4">
        <div className="border rounded-xl overflow-hidden">
          <Table>
            <TableHeader>
              <TableRow className="font-bold">
                <TableHead className="px-4">Name</TableHead>
                <TableHead>Address</TableHead>

                {TYPE_COLUMNS.map(col => (
                  <TableHead key={col.key} className="text-center">
                    {col.label}
                  </TableHead>
                ))}

                {TOOL_COLUMNS.map(col => (
                  <TableHead key={col.key} className="text-center">
                    {col.label}
                  </TableHead>
                ))}

                <TableHead>Synced at</TableHead>
              </TableRow>
            </TableHeader>

            <TableBody>
              {dealers.map(dealer => (
                <TableRow key={dealer.id}>
                  <TableCell className="px-4">{dealer.name}</TableCell>

                  <TableCell>
                    {dealer.street_name} {dealer.street_number} <br />
                    {dealer.zip_code} {dealer.city}
                  </TableCell>

                  {TYPE_COLUMNS.map(col => (
                    <TableCell key={col.key} className="text-center">
                      {renderBoolean(dealer.types?.[col.key])}
                    </TableCell>
                  ))}

                  {TOOL_COLUMNS.map(col => (
                    <TableCell key={col.key} className="text-center">
                      {renderBoolean(dealer.tools?.[col.key])}
                    </TableCell>
                  ))}

                  <TableCell>{dealer.synced_at}</TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </div>
      </div>
    </AppLayout>
  )
}