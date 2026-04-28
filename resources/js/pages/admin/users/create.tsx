"use client"

import * as React from "react"
import { Head, useForm, usePage } from "@inertiajs/react"
import AppLayout from "@/layouts/app-layout"
import { toast } from "sonner"
import { Copy } from "lucide-react"
import HeadingSmall from '@/components/heading-small';

import {
  Field,
  FieldDescription,
  FieldLabel
} from "@/components/ui/field"

import { Input } from "@/components/ui/input"
import { Button } from "@/components/ui/button"

const breadcrumbs = [
  { title: "Users", href: "/admin/users" },
  { title: "Create new", href: "/admin/users/create" },
]

export default function CreateUser() {
  const { props } = usePage<any>()

  const form = useForm({
    name: "",
    email: "",
  })

  const submit = (e: React.FormEvent) => {
    e.preventDefault()

    form.post("/admin/users", {
      onSuccess: () => {
        toast.success("User created successfully")
        form.reset()
      },
      onError: () => {
        toast.error("Something went wrong")
      },
    })
  }

  const copyToClipboard = async () => {
    if (!props.flash?.reset_link) return

    await navigator.clipboard.writeText(props.flash.reset_link)
    toast.success("Link copied to clipboard")
  }

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Create user" />

      <div className="p-4 space-y-8">

        {/* FORM */}
        <form onSubmit={submit} className="space-y-2">
          <div className="grid sm:grid-cols-4 gap-4">

            {/* Name */}
            <Field className="sm:col-span-2" data-invalid={!!form.errors.name}>
              <FieldLabel htmlFor="name">Name</FieldLabel>
              <Input
                id="name"
                value={form.data.name}
                onChange={(e) => form.setData("name", e.target.value)}
                aria-invalid={!!form.errors.name}
              />
              {form.errors.name && (
                <FieldDescription>{form.errors.name}</FieldDescription>
              )}
            </Field>

            {/* Email */}
            <Field className="sm:col-span-2" data-invalid={!!form.errors.email}>
              <FieldLabel htmlFor="email">Email</FieldLabel>
              <Input
                id="email"
                type="email"
                value={form.data.email}
                onChange={(e) => form.setData("email", e.target.value)}
                aria-invalid={!!form.errors.email}
              />
              {form.errors.email && (
                <FieldDescription>{form.errors.email}</FieldDescription>
              )}
            </Field>

          </div>

          <Button type="submit" disabled={form.processing}>
            Create user
          </Button>
        </form>

        {/* RESET LINK BLOCK */}
        {props.flash?.reset_link && (
          <div className="rounded-lg border p-4">
            
            <HeadingSmall
                title="Set password link"
            />

            <div className="flex items-center gap-2">
              <a
                href={props.flash.reset_link}
                target="_blank"
                className="text-white underline break-all flex-1"
              >
                {props.flash.reset_link}
              </a>

              <Button
                type="button"
                size="icon"
                variant="outline"
                onClick={copyToClipboard}
              >
                <Copy className="w-4 h-4" />
              </Button>
            </div>

          </div>
        )}

      </div>
    </AppLayout>
  )
}