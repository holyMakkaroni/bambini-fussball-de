import type { components as mainComponents } from './storeApiTypes'

export type Schemas = {
  Category: mainComponents['schemas']['Category'] & {
    customFields?: {
      properties?: string[];
    };
  };
};

export type components = mainComponents & {
  schemas: Schemas;
};

export type operations = {
  'searchPropertyGroup post /search/property-group':
    | {
    contentType?: 'application/json';
    accept?: 'application/json';
    body: components['schemas']['Criteria'];
    response: {
      elements: components['schemas']['PropertyGroup'][];
    } & components['schemas']['EntitySearchResult'];
    responseCode: 200;
  }
    | {
    contentType?: 'application/json';
    accept: 'application/vnd.api+json';
    body: components['schemas']['Criteria'];
    response: {
      elements: components['schemas']['PropertyGroup'][];
    } & components['schemas']['EntitySearchResult'];
    responseCode: 200;
  }
    | {
    contentType: 'application/vnd.api+json';
    accept?: 'application/json';
    body: components['schemas']['Criteria'];
    response: {
      elements: components['schemas']['PropertyGroup'][];
    } & components['schemas']['EntitySearchResult'];
    responseCode: 200;
  }
    | {
    contentType: 'application/vnd.api+json';
    accept: 'application/vnd.api+json';
    body: components['schemas']['Criteria'];
    response: {
      elements: components['schemas']['PropertyGroup'][];
    } & components['schemas']['EntitySearchResult'];
    responseCode: 200;
  };
  'searchPropertyGroupOption post /search/property-group-option':
    | {
    contentType?: 'application/json';
    accept?: 'application/json';
    body: components['schemas']['Criteria'];
    response: {
      elements: components['schemas']['PropertyGroupOption'][];
    } & components['schemas']['EntitySearchResult'];
    responseCode: 200;
  }
    | {
    contentType?: 'application/json';
    accept: 'application/vnd.api+json';
    body: components['schemas']['Criteria'];
    response: {
      elements: components['schemas']['PropertyGroupOption'][];
    } & components['schemas']['EntitySearchResult'];
    responseCode: 200;
  }
    | {
    contentType: 'application/vnd.api+json';
    accept?: 'application/json';
    body: components['schemas']['Criteria'];
    response: {
      elements: components['schemas']['PropertyGroupOption'][];
    } & components['schemas']['EntitySearchResult'];
    responseCode: 200;
  }
    | {
    contentType: 'application/vnd.api+json';
    accept: 'application/vnd.api+json';
    body: components['schemas']['Criteria'];
    response: {
      elements: components['schemas']['PropertyGroupOption'][];
    } & components['schemas']['EntitySearchResult'];
    responseCode: 200;
  };
  'readProductManufacturer post /product-manufacturer':
    | {
    contentType?: 'application/json';
    accept?: 'application/json';
    body?: components['schemas']['Criteria'];
    response: {
      elements: components['schemas']['ProductManufacturer'][];
    } & components['schemas']['EntitySearchResult'];
    responseCode: 200;
  }
    | {
    contentType?: 'application/json';
    accept: 'application/vnd.api+json';
    body?: components['schemas']['Criteria'];
    response: {
      elements: components['schemas']['ProductManufacturer'][];
    } & components['schemas']['EntitySearchResult'];
    responseCode: 200;
  }
    | {
    contentType: 'application/vnd.api+json';
    accept?: 'application/json';
    body?: components['schemas']['Criteria'];
    response: {
      elements: components['schemas']['ProductManufacturer'][];
    } & components['schemas']['EntitySearchResult'];
    responseCode: 200;
  }
    | {
    contentType: 'application/vnd.api+json';
    accept: 'application/vnd.api+json';
    body?: components['schemas']['Criteria'];
    response: {
      elements: components['schemas']['ProductManufacturer'][];
    } & components['schemas']['EntitySearchResult'];
    responseCode: 200;
  };
};
