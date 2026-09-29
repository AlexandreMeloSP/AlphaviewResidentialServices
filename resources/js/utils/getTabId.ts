const TAB_ID_KEY = 'alphaview_tab_id';

export function getTabId(): string {
    return sessionStorage.getItem(TAB_ID_KEY) || '';
}

export function setTabId(tabId: string): void {
    sessionStorage.setItem(TAB_ID_KEY, tabId);
}
