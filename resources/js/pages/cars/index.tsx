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

interface Props {
  cars: Car[]
}

type ChannelData = {
  open_from?: string | null
  open_to?: string | null
  open_internal?: boolean
}

type SyncState = {
  batchId: string | null
  progress: number
  finished: boolean
}

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Cars', href: '/cars' },
];

function isChannelOpen(channel: ChannelData | null): boolean {
  if (!channel) return false
  const now = new Date()
  const from = channel.open_from ? new Date(channel.open_from) : null
  const to = channel.open_to ? new Date(channel.open_to) : null
  return (!from || now >= from) && (!to || now <= to)
}

// Custom hook til at håndtere sync progress
function useSync() {
  const [syncStates, setSyncStates] = useState<Record<number, SyncState>>({})
  const intervalsRef = useRef<Record<number, NodeJS.Timer>>({})

  const startSync = async (carId: number) => {
    try {
      const res = await axios.post(`/cars/sync/${carId}`)
      const batchId = res.data.batch_id

      setSyncStates(prev => ({
        ...prev,
        [carId]: { batchId, progress: 0, finished: false }
      }))
    } catch {
      toast.error('Failed to start sync')
    }
  }

  useEffect(() => {
    Object.entries(syncStates).forEach(([carIdStr, state]) => {
      const carId = Number(carIdStr)

      // Hvis sync allerede kører, gør intet
      if (!state.batchId || state.finished || intervalsRef.current[carId]) return

      intervalsRef.current[carId] = setInterval(async () => {
        try {
          const res = await axios.get(`/batches/${state.batchId}`)
          const { progress, finished } = res.data

          setSyncStates(prev => {
            const newState = {
              ...prev,
              [carId]: { ...prev[carId], progress, finished }
            }
            if (finished && !prev[carId].finished) {
              toast.success(`Sync completed for car #${carId}`)
              clearInterval(intervalsRef.current[carId])
              delete intervalsRef.current[carId]
            }
            return newState
          })
        } catch {
          clearInterval(intervalsRef.current[carId])
          delete intervalsRef.current[carId]
          toast.error(`Error fetching progress for car #${carId}`)
        }
      }, 1500)
    })

    return () => {
      Object.values(intervalsRef.current).forEach(clearInterval)
      intervalsRef.current = {}
    }
  }, [syncStates])

  return { syncStates, startSync }
}

export default function Index({ cars }: Props) {
  const { syncStates, startSync } = useSync()

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Cars" />

      <div className="p-4">
        <div className="border rounded-xl overflow-hidden">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead className="font-bold px-4">Name</TableHead>
                <TableHead className="font-bold">Year</TableHead>
                <TableHead className="font-bold">Struct ID</TableHead>
                <TableHead className="font-bold text-center w-20">Master</TableHead>
                <TableHead className="font-bold text-center w-20">Web</TableHead>
                <TableHead className="font-bold text-center w-20">Dealer</TableHead>
                <TableHead className="font-bold text-center w-20">Price</TableHead>
                <TableHead className="font-bold text-center w-20">Test Drive</TableHead>
                <TableHead className="font-bold text-right w-42 px-2"></TableHead>
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
                    <TableCell className="text-center">{isChannelOpen(car.channels.master_channel) ? '✅' : '❌'}</TableCell>
                    <TableCell className="text-center">{isChannelOpen(car.channels.web_channel) ? '✅' : '❌'}</TableCell>
                    <TableCell className="text-center">{isChannelOpen(car.channels.dealer_channel) ? '✅' : '❌'}</TableCell>
                    <TableCell className="text-center">{isChannelOpen(car.channels.price_channel) ? '✅' : '❌'}</TableCell>
                    <TableCell className="text-center">{isChannelOpen(car.channels.test_drive_channel) ? '✅' : '❌'}</TableCell>
                    <TableCell className="text-center flex justify-end items-center gap-4 px-2">
                      {state && !state.finished && <Progress value={state.progress} className="w-16"/>}
                      <Button
                        className="cursor-pointer"
                        size="icon"
                        onClick={() => startSync(car.struct_id)}
                        disabled={state && !state.finished}
                      >
                        <RefreshCcw className={state && !state.finished ? "animate-spin" : ""} />
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
