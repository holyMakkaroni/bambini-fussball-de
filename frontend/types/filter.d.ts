export type Refinement = {
  [key: string]: string[];
};

export type QueryParams = {
  [key: string]: string;
};

export type FilterGroup =
  | 'default'
  | 'toggle'
  | 'range'

type RefinementType =
  | 'text'
  | 'toggle'
  | 'range'

export type Refinements = {
  label: string;
  name: string;
  seoKey: string;
  displayType: RefinementType;
}

export interface Filter {
  [key: string]: Refinements[]
}