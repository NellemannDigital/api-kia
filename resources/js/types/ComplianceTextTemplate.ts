export interface ComplianceTextTemplate {
  id: number
  variant: string
  scope: string,
  template: string,
  version: string,
  valid_from: string,
  valid_to: string,
  show_in_generator: Boolean
}