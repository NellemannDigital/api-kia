"use client"

import * as React from "react"
import { useForm, Head } from '@inertiajs/react'
import AppLayout from '@/layouts/app-layout'
import { toast } from "sonner"
import { CalendarIcon, X } from "lucide-react"
import { Calendar } from "@/components/ui/calendar"
import { Button } from "@/components/ui/button"

import {
  Field,
  FieldContent,
  FieldDescription,
  FieldGroup,
  FieldLabel,
  FieldTitle,
} from "@/components/ui/field"

import { Checkbox } from "@/components/ui/checkbox"
import { Textarea } from "@/components/ui/textarea"
import { Input } from '@/components/ui/input'
import {
  InputGroup,
  InputGroupAddon,
  InputGroupButton,
  InputGroupInput,
} from "@/components/ui/input-group"

import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover"

const breadcrumbs = [
  { title: 'Edit compliance text template', href: '/compliance-text-templates' }
]

function formatDate(date?: string | Date) {
  if (!date) return ''
  return new Date(date).toLocaleDateString()
}

type Props = {
  template: {
    id: number,
    variant: string,
    template: string,
    version: string,
    valid_from?: Date,
    valid_to?: Date,
    show_in_generator: Boolean
  }
}

export default function Edit({ template }: Props) {
  const [openFrom, setOpenFrom] = React.useState(false)
  const [dateFrom, setDateFrom] = React.useState<Date | undefined>(template.valid_from ? new Date(template.valid_from) : undefined)

  const [openTo, setOpenTo] = React.useState(false)
  const [dateTo, setDateTo] = React.useState<Date | undefined>(template.valid_to ? new Date(template.valid_to) : undefined)

  const form = useForm({
    variant: template.variant,
    template: template.template,
    version: template.version,
    valid_from: formatDate(template.valid_from),
    valid_to: formatDate(template.valid_to),
    show_in_generator: !!template.show_in_generator
  })

  React.useEffect(() => {
    form.setData('valid_from', formatDate(dateFrom))
  }, [dateFrom])

  React.useEffect(() => {
    form.setData('valid_to', formatDate(dateTo))
  }, [dateTo])

  const submit = (e: React.FormEvent) => {
    e.preventDefault()
    form.put(`/compliance-text-templates/${template.id}`, {
      onSuccess: () => {
        toast.success(`Compliance text template updated`);
      }
    })
  }

  

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Edit compliance text template" />
      <div className="p-4">
        <form onSubmit={submit} className="space-y-6">
          <div className="grid sm:grid-cols-4 gap-4">

            {/* Variant */}
            <Field className="sm:col-span-2" data-invalid={!!form.errors.variant}>
              <FieldLabel htmlFor="variant">Variant</FieldLabel>
              <Input
                id="variant"
                name="variant"
                value={form.data.variant}
                onChange={e => form.setData('variant', e.target.value)}
                aria-invalid={!!form.errors.variant}
              />
              {form.errors.variant && <FieldDescription>{form.errors.variant}</FieldDescription>}
            </Field>

            {/* Version */}
            <Field className="sm:col-span-2" data-invalid={!!form.errors.version}>
              <FieldLabel htmlFor="version">Version</FieldLabel>
              <Input
                id="version"
                name="version"
                value={form.data.version}
                onChange={e => form.setData('version', e.target.value)}
                aria-invalid={!!form.errors.version}
              />
              {form.errors.version && <FieldDescription>{form.errors.version}</FieldDescription>}
            </Field>

            {/* Template */}
            <Field className="sm:col-span-4" data-invalid={!!form.errors.template}>
              <FieldLabel htmlFor="template">Template</FieldLabel>
              <Textarea
                id="template"
                name="template"
                value={form.data.template}
                onChange={e => form.setData('template', e.target.value)}
                aria-invalid={!!form.errors.template}
              />
              {form.errors.template && <FieldDescription>{form.errors.template}</FieldDescription>}
            </Field>

            {/* Valid From */}
            <Field className="sm:col-span-1" data-invalid={!!form.errors.valid_from}>
              <FieldLabel htmlFor="valid_from">Valid from</FieldLabel>
              <InputGroup>
                <InputGroupInput
                  id="valid_from"
                  value={form.data.valid_from}
                  readOnly
                  placeholder="Select date"
                />
                <InputGroupAddon align="inline-end">
                  <Popover open={openFrom} onOpenChange={setOpenFrom}>
                    <PopoverTrigger asChild>
                      <InputGroupButton variant="ghost" size="icon-xs" aria-label="Select date">
                        <CalendarIcon />
                      </InputGroupButton>
                    </PopoverTrigger>
                    <PopoverContent className="w-auto overflow-hidden p-0" align="end">
                      <Calendar
                        mode="single"
                        selected={dateFrom}
                        onSelect={d => { setDateFrom(d); setOpenFrom(false) }}
                      />
                    </PopoverContent>
                  </Popover>
                </InputGroupAddon>
              </InputGroup>
              {form.errors.valid_from && <FieldDescription>{form.errors.valid_from}</FieldDescription>}
            </Field>

            {/* Valid To */}
            <Field className="sm:col-span-1" data-invalid={!!form.errors.valid_to}>
              <FieldLabel htmlFor="valid_to">Valid to</FieldLabel>

              <div className="flex gap-2">
                <InputGroup>
                  <InputGroupInput
                    id="valid_to"
                    value={form.data.valid_to}
                    readOnly
                    placeholder="Select date"
                  />
                  <InputGroupAddon align="inline-end">
                    <Popover open={openTo} onOpenChange={setOpenTo}>
                      <PopoverTrigger asChild>
                        <InputGroupButton variant="ghost" size="icon-xs" aria-label="Select date">
                          <CalendarIcon />
                        </InputGroupButton>
                      </PopoverTrigger>
                      <PopoverContent className="w-auto overflow-hidden p-0" align="end">
                        <Calendar
                          mode="single"
                          selected={dateTo}
                          onSelect={d => { setDateTo(d); setOpenTo(false) }}
                        />
                      </PopoverContent>
                    </Popover>
                  </InputGroupAddon>
                </InputGroup>
                <Button
                  variant="outline"
                  size="icon"
                  disabled={!dateTo}
                  onClick={() => {
                    setDateTo(null)
                    form.setData('valid_to', '')
                  }}
                >
                  <X />
                </Button>
              </div>  

              {form.errors.valid_to && <FieldDescription>{form.errors.valid_to}</FieldDescription>}

            </Field>

            {/* Show in generator */}
              <FieldGroup>
                <FieldLabel>
                  <Field orientation="horizontal" className="cursor-pointer">
                    <Checkbox
                      id="show_in_generator"
                      checked={form.data.show_in_generator}
                      onCheckedChange={(checked) =>
                        form.setData('show_in_generator', !!checked)
                      }
                    />
                    <FieldContent>
                      <FieldTitle>Show in generator</FieldTitle>
                    </FieldContent>
                  </Field>
                </FieldLabel>
              </FieldGroup>

          </div>

          <Button type="submit">Update</Button>

        </form>
      </div>
    </AppLayout>
  )
}