export interface Car {
  id: number
  trims: {
    struct_id: string,
  }
  struct_id: number
  web_id: string | null
  name: string
  year: string | null
  delivery_year: string | null
  channels: Channels
  synced_at: string
  campaign?: {
    name?: string
  } | null
}

type Channels = {
  master_channel: Channel
  web_channel: Channel
  price_channel: Channel
  dealer_channel: Channel
  test_drive_channel: TestDriveChannel
};

export type Channel = {
  open_from: string, 
  open_to: string
}

export type TestDriveChannel = {
    test_start: string,
    booking_start: string,
    booking_end: string
  }