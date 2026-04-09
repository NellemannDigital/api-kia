"use client"

import { Car, Channel, TestDriveChannel } from '@/types/Car';
import * as routes from '@/routes/cars/index';
import { Head, router } from '@inertiajs/react';
import { useState, useEffect, useRef, useCallback } from 'react';
import axios from 'axios';

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
import { Progress } from "@/components/ui/progress"

import { RefreshCcw } from 'lucide-react';
import { toast } from "sonner"

interface Props { cars: Car[] }

type SyncState = { batchId: string | null, progress: number, finished: boolean }

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Cars', href: '/cars' }];

// --- Channel renderer ---
const isChannelActive = (channel?: Channel | TestDriveChannel, type: 'regular' | 'test' = 'regular') => {
  if (!channel) return false
  const now = new Date()
  if (type === 'regular') {
    const fromOk = !(channel as Channel).open_from || now >= new Date((channel as Channel).open_from)
    const toOk = !(channel as Channel).open_to || now <= new Date((channel as Channel).open_to)
    return fromOk && toOk
  } else {
    return !(channel as TestDriveChannel).test_start || now >= new Date((channel as TestDriveChannel).test_start)
  }
}

// --- Define all channels in one array ---
const channelsMeta: { key: keyof Car['channels']; label: string; type: 'regular' | 'test' }[] = [
  { key: 'master_channel', label: 'Master', type: 'regular' },
  { key: 'web_channel', label: 'Web', type: 'regular' },
  { key: 'dealer_channel', label: 'Dealer', type: 'regular' },
  { key: 'price_channel', label: 'Price', type: 'regular' },
  { key: 'test_drive_channel', label: 'Test Drive', type: 'test' },
]

export default function Index({ cars }: Props) {
  const [syncStates, setSyncStates] = useState<Record<number, SyncState>>({})
  const intervals = useRef<Record<number, ReturnType<typeof setInterval>>>({})

  const startSync = useCallback((carId: number) => {
    router.post(routes.sync(carId), {}, {
      preserveScroll: true,
      onSuccess: (page: any) => {
        const batchId = page.props?.flash?.batch_id
        if (!batchId) return toast.error('No batch id returned')

        setSyncStates(prev => ({ 
          ...prev, 
          [carId]: { batchId, progress: 0, finished: false } 
        }))
        toast.success(`Sync started for car #${carId}`)
      },
      onError: () => toast.error('Failed to start sync')
    })
  }, [])

  // --- Polling intervals ---
  useEffect(() => {
    Object.entries(syncStates).forEach(([carIdStr, state]) => {
      const carId = Number(carIdStr)
      if (!state.batchId || state.finished || intervals.current[carId]) return

      intervals.current[carId] = setInterval(async () => {
        try {
          const { data } = await axios.get(`/batches/${state.batchId}`)
          setSyncStates(prev => {
            const finishedNow = data.finished && !prev[carId].finished
            if (finishedNow) {
              toast.success(`Sync completed for car #${carId}`)
              clearInterval(intervals.current[carId])
              delete intervals.current[carId]
            }
            return {
              ...prev,
              [carId]: {
                ...prev[carId],
                progress: data.progress,
                finished: data.finished
              }
            }
          })
        } catch {
          clearInterval(intervals.current[carId])
          delete intervals.current[carId]
          toast.error(`Error fetching progress for car #${carId}`)
        }
      }, 1500)
    })

    return () => {
      Object.values(intervals.current).forEach(clearInterval)
      intervals.current = {}
    }
  }, [syncStates])

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

                {channelsMeta.map((ch) => (
                  <TableHead key={ch.key} className="text-center">{ch.label}</TableHead>
                ))}

                <TableHead>Synced at</TableHead>
                <TableHead></TableHead>
                <TableHead>Sync</TableHead>
              </TableRow>
            </TableHeader>

            <TableBody>
              {cars.map(car => {
                const state = syncStates[car.struct_id]

                return (
                  <TableRow key={car.id}>
                    <TableCell className="px-4">{car.name}</TableCell>
                    <TableCell>{car.year}</TableCell>
                    <TableCell>{car.struct_id}</TableCell>

                    {channelsMeta.map((ch) => (
                      <TableCell key={ch.key} className="text-center">
                        {isChannelActive(car.channels[ch.key], ch.type) ? '✅' : '❌'}
                      </TableCell>
                    ))}

                    <TableCell>{car.synced_at}</TableCell>

                    <TableCell className="w-24">
                      {state && !state.finished && <Progress value={state.progress} />}
                    </TableCell>

                    <TableCell>
                      <Button
                        size="icon"
                        onClick={() => startSync(car.struct_id)}
                        disabled={state && !state.finished}
                      >
                        <RefreshCcw
                          className={state && !state.finished ? "animate-spin" : ""}
                          size={16}
                        />
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