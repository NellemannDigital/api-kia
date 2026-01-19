"use client"

import { Head } from '@inertiajs/react'
import { Car } from '@/types/Car'
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { toast } from "sonner"
import { useState, useEffect, useRef } from 'react';
import axios from 'axios';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table"
import { Button } from "@/components/ui/button"
import { RefreshCcw } from 'lucide-react';
import { Progress } from "@/components/ui/progress"

interface Props { cars: Car[] }

type ChannelData = { open_from?: string | null, open_to?: string | null, open_internal?: boolean }
type SyncState = { batchId: string | null, progress: number, finished: boolean }

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Cars', href: '/cars' }];

export default function Index({ cars }: Props) {
  const [syncStates, setSyncStates] = useState<Record<number, SyncState>>({})
  const intervals = useRef<Record<number, NodeJS.Timer>>({})

  const startSync = async (carId: number) => {
    try {
      const { data } = await axios.post(`/cars/sync/${carId}`)
      setSyncStates(prev => ({ ...prev, [carId]: { batchId: data.batch_id, progress: 0, finished: false } }))
    } catch {
      toast.error('Failed to start sync')
    }
  }

  // Polling for batch progress
  useEffect(() => {
    Object.entries(syncStates).forEach(([carIdStr, state]) => {
      const carId = Number(carIdStr)
      if (!state.batchId || state.finished || intervals.current[carId]) return

      intervals.current[carId] = setInterval(async () => {
        try {
          const { data } = await axios.get(`/batches/${state.batchId}`)
          setSyncStates(prev => {
            const finishedNow = data.finished && !prev[carId].finished
            if (finishedNow) toast.success(`Sync completed for car #${carId}`)
            if (finishedNow) clearInterval(intervals.current[carId])
            if (finishedNow) delete intervals.current[carId]

            return { ...prev, [carId]: { ...prev[carId], progress: data.progress, finished: data.finished } }
          })
        } catch {
          clearInterval(intervals.current[carId])
          delete intervals.current[carId]
          toast.error(`Error fetching progress for car #${carId}`)
        }
      }, 1500)
    })

    return () => { Object.values(intervals.current).forEach(clearInterval); intervals.current = {} }
  }, [syncStates])

  const renderChannel = (channel?: ChannelData) => channel && (!channel.open_from || new Date() >= new Date(channel.open_from)) && (!channel.open_to || new Date() <= new Date(channel.open_to)) ? '✅' : '❌'

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Cars" />
      <div className="p-4">
        <div className="border rounded-xl overflow-hidden">
          <Table>
            <TableHeader>
              <TableRow className="font-bold">
                  <TableHead className="px-4">Name</TableHead>
                  <TableHead>Year</TableHead>
                  <TableHead>Struct ID</TableHead>
                  {['Master','Web','Dealer','Price','Test Drive'].map((h, i) =>
                    <TableHead key={i} className="text-center">{h}</TableHead>
                  )}
                  <TableHead>Synced at</TableHead>
                  <TableHead></TableHead>
                  <TableHead>Sync</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {cars.map(car => {
                const state = syncStates[car.struct_id]
                const channels = ['master_channel','web_channel','dealer_channel','price_channel','test_drive_channel'] as const
                return (
                  <TableRow key={car.id}>
                    <TableCell className="px-4">{car.name}</TableCell>
                    <TableCell>{car.year}</TableCell>
                    <TableCell>{car.struct_id}</TableCell>
                    {channels.map(ch => <TableCell key={ch} className="text-center">{renderChannel(car.channels[ch])}</TableCell>)}
                    <TableCell>{car.synced_at}</TableCell>
                    <TableCell className="w-24">{state && !state.finished && <Progress value={state.progress}/>}</TableCell>
                    <TableCell>
                      <Button size="icon" onClick={() => startSync(car.struct_id)} disabled={state && !state.finished}>
                        <RefreshCcw className={state && !state.finished ? "animate-spin" : ""} size={9}/>
                      </Button>
                    </TableCell>
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
