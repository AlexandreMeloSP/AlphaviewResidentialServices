export function getXsrfToken(): string {
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    if (!match) {
        return '';
    }
    return decodeURIComponent(match[1]);
}
