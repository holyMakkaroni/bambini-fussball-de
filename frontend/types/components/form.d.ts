export interface FormLabel {
  for: string,
  label?: string,
  required?: boolean
}

export interface FormInput {
  modelValue?: string | number,
  label?: string,
  type: string,
  id: string,
  placeholder?: string,
  required?: boolean,
  withIcon?: boolean,
  autocomplete?: string
}

export interface FormCheckbox {
  modelValue?: boolean|[]|string,
  value: boolean|string,
  label?: string,
  id: string,
  required?: boolean,
  checked?: boolean
}