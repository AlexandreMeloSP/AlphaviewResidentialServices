export const ROUTE_PREFIX = '/alphaview';

export function storageUrl(path: string): string {
    return `${ROUTE_PREFIX}/storage/${path}`;
}

export const API_BASE = `${ROUTE_PREFIX}/api`;
