export interface Dealer {
  id: number
  name: string
  street_name: string,
  street_number: string,
  zip_code: string,
  city: string,
  types?: DealerTypes
  tools?: DealerTools
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