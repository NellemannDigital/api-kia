export interface Dealer {
  id: number
  name: string
  display_name: string
  street_name: string
  street_number: string
  zip_code: string
  city: string
  types?: DealerTypes
  tools?: DealerTools
  special_opening_hours?: DealerSpecialOpeningHour[]
  synced_at: string
}

type DealerTypes = {
  b2c?: boolean
  b2b?: boolean
  service?: boolean
}

type DealerTools = {
  test_drive?: boolean
  sales_advisor?: boolean
  insurance_calculator?: boolean
  book_service?: boolean
}

type DealerSpecialOpeningHour = {
  date?: string
  opening_time?: string
  closing_time?: string
  closed?: boolean | null
  display_name?: string | null
}
