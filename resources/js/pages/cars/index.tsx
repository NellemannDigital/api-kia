"use client"

import { Car, Channel, TestDriveChannel } from '@/types/Car'
import * as routes from '@/routes/cars/index'
import { Head, router } from '@inertiajs/react'
import { useState, useEffect, useRef, useCallback } from 'react'
import axios from 'axios'
import { route } from 'ziggy-js';

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

import { Button } from "@/components/ui/button"
import { Progress } from "@/components/ui/progress"
import { RefreshCcw, ScanEye } from 'lucide-react'
import { toast } from "sonner"

interface Props {
  cars: Car[]
}

type JobType = 'sync'

type JobState = {
  batchId: string | null
  progress: number
  finished: boolean
}

type SyncState = Record<number, Partial<Record<JobType, JobState>>>

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Cars', href: '/cars' }
]

// --- Channel renderer ---
const isChannelActive = (
  channel?: Channel | TestDriveChannel,
  type: 'regular' | 'test' = 'regular'
) => {
  if (!channel) return false
  const now = new Date()

  if (type === 'regular') {
    const fromOk = !(channel as Channel).open_from || now >= new Date((channel as Channel).open_from)
    const toOk = !(channel as Channel).open_to || now <= new Date((channel as Channel).open_to)
    return fromOk && toOk
  }

  const testChannel = channel as TestDriveChannel

  const startOk =
    !testChannel.booking_start ||
    now >= new Date(testChannel.booking_start)

  const endOk =
    !testChannel.booking_end ||
    now <= new Date(testChannel.booking_end)

  return startOk && endOk
}

// --- Channels ---
const channelsMeta: {
  key: keyof Car['channels']
  label: string
  type: 'regular' | 'test'
}[] = [
    { key: 'master_channel', label: 'Master', type: 'regular' },
    { key: 'web_channel', label: 'Web', type: 'regular' },
    { key: 'dealer_channel', label: 'Dealer', type: 'regular' },
    { key: 'price_channel', label: 'Price', type: 'regular' },
    { key: 'test_drive_channel', label: 'Test Drive', type: 'test' },
  ]

export default function Index({ cars }: Props) {
  const [syncStates, setSyncStates] = useState<SyncState>({})
  const intervals = useRef<Record<string, ReturnType<typeof setInterval>>>({})

  // --- Generic job starter ---
  const startJob = useCallback(
    (
      carId: number,
      job: JobType,
      routeFn: typeof routes.sync,
      successMsg: string
    ) => {
      router.post(routeFn({ id: carId }), {}, {
        preserveScroll: true,
        onSuccess: (page: any) => {
          const batchId = page.props?.flash?.batch_id
          if (!batchId) return toast.error('No batch id returned')

          setSyncStates(prev => ({
            ...prev,
            [carId]: {
              ...prev[carId],
              [job]: {
                batchId,
                progress: 0,
                finished: false
              }
            }
          }))

          toast.success(successMsg)
        },
        onError: () => toast.error(`Failed to start ${job}`)
      })
    },
    []
  )

  const startSync = (carId: number) =>
    startJob(carId, 'sync', routes.sync, `Sync started for car #${carId}`)

  // --- Polling per job ---
  useEffect(() => {
    Object.entries(syncStates).forEach(([carIdStr, jobs]) => {
      const carId = Number(carIdStr)

      Object.entries(jobs || {}).forEach(([job, state]) => {
        if (!state?.batchId || state.finished) return

        const key = `${carId}-${job}`

        if (intervals.current[key]) return

        intervals.current[key] = setInterval(async () => {
          try {
            const { data } = await axios.get(`/batches/${state.batchId}`)

            setSyncStates(prev => {
              const current = prev[carId]?.[job as JobType]

              const finishedNow = data.finished && !current?.finished

              if (finishedNow) {
                toast.success(`${job} completed for car #${carId}`)
                clearInterval(intervals.current[key])
                delete intervals.current[key]
              }

              return {
                ...prev,
                [carId]: {
                  ...prev[carId],
                  [job]: {
                    ...current!,
                    progress: data.progress,
                    finished: data.finished
                  }
                }
              }
            })
          } catch {
            clearInterval(intervals.current[key])
            delete intervals.current[key]
            toast.error(`Error fetching ${job} for car #${carId}`)
          }
        }, 1500)
      })
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

                {channelsMeta.map(ch => (
                  <TableHead key={ch.key} className="text-center">
                    {ch.label}
                  </TableHead>
                ))}

                <TableHead>Synced at</TableHead>
                <TableHead></TableHead>
                <TableHead className="text-center">Sync</TableHead>
                <TableHead className="text-center">Price List</TableHead>
              </TableRow>
            </TableHeader>

            <TableBody>
              {cars.map(car => {
                const sync = syncStates[car.struct_id]?.sync

                const syncRunning = !!sync && !sync.finished

                return (
                  <TableRow key={car.id}>
                    <TableCell className="px-4">{car.name}</TableCell>
                    <TableCell>{car.year}</TableCell>
                    <TableCell>{car.struct_id}</TableCell>

                    {channelsMeta.map(ch => (
                      <TableCell key={ch.key} className="text-center">
                        {isChannelActive(car.channels[ch.key], ch.type)
                          ? '✅'
                          : '❌'}
                      </TableCell>
                    ))}

                    <TableCell>{car.synced_at}</TableCell>

                    {/* Progress */}
                    <TableCell className="w-32 space-y-2">
                      {sync && !sync.finished && (
                        <Progress value={sync.progress} />
                      )}
                    </TableCell>

                    {/* Sync */}
                    <TableCell className="text-center">
                      <Button
                        size="icon"
                        variant="outline"
                        onClick={() => startSync(car.struct_id)}
                        disabled={syncRunning}
                      >
                        <RefreshCcw
                          className={syncRunning ? "animate-spin" : ""}
                          size={16}
                        />
                      </Button>
                    </TableCell>

                    {/* Price List */}
                    <TableCell className="text-center">
                      <Button
                          size="icon"
                          variant="outline"
                          asChild
                        >
                          <a
                            href={`${routes.prices(car.struct_id).url}?preview_date=2026-09-01`}
                            target="_blank"
                            rel="noreferrer"
                          >
                            <ScanEye size={16} />
                          </a>
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