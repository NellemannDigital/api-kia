export interface Car {
  id: number
  struct_id: number
  web_id: string | null
  name: string
  year: string | null
  delivery_year: string | null
  channels: string[]
  campaign?: {
    name?: string
  } | null
}