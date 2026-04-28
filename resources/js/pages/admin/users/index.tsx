"use client"

import * as React from "react"
import { Head, router } from "@inertiajs/react"
import AppLayout from "@/layouts/app-layout"
import { toast } from "sonner"
import { Trash2 } from "lucide-react"

import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table"

import { Button } from "@/components/ui/button"

import { type BreadcrumbItem } from "@/types"

interface User {
  id: number
  name: string
  email: string
  created_at: string
}

interface Props {
  users: User[]
  authUserId: number
}

const breadcrumbs: BreadcrumbItem[] = [
  { title: "Users", href: "/admin/users" }
]

export default function Index({ users, authUserId }: Props) {

  const deleteUser = (id: number) => {
    if (!confirm("Are you sure you want to delete this user?")) return

    router.delete(`/admin/users/${id}`, {
      preserveScroll: true,
      onSuccess: () => {
        toast.success("User deleted")
      },
      onError: () => {
        toast.error("Failed to delete user")
      }
    })
  }

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Users" />

      <div className="p-4">
        <div className="border rounded-xl overflow-hidden">

          <Table>
            <TableHeader>
              <TableRow className="font-bold">
                <TableHead className="px-4">Name</TableHead>
                <TableHead>Email</TableHead>
                <TableHead>Created</TableHead>
                <TableHead className="text-center">Actions</TableHead>
              </TableRow>
            </TableHeader>

            <TableBody>
              {users.map(user => (
                <TableRow key={user.id}>

                  {/* Name */}
                  <TableCell className="p-4.5">
                    {user.name}
                  </TableCell>

                  {/* Email */}
                  <TableCell>
                    {user.email}
                  </TableCell>

                  {/* Created */}
                  <TableCell>
                    {user.created_at}
                  </TableCell>

                  {/* Actions */}
                  <TableCell className="text-center">
                    {authUserId !== user.id && (
                      <Button
                        size="icon"
                        variant="outline"
                        onClick={() => deleteUser(user.id)}
                      >
                        <Trash2 size={16} />
                      </Button>
                    )}
                  </TableCell>

                </TableRow>
              ))}
            </TableBody>

          </Table>

        </div>
      </div>

    </AppLayout>
  )
}