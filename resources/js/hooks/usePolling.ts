import { useEffect, useRef, useCallback } from 'react';

export function usePolling(callback: () => void, interval: number = 3000) {
    const savedCallback = useRef(callback);
    const intervalId = useRef<ReturnType<typeof setInterval> | null>(null);

    useEffect(() => {
        savedCallback.current = callback;
    }, [callback]);

    const start = useCallback(() => {
        if (intervalId.current) return;
        intervalId.current = setInterval(() => {
            savedCallback.current();
        }, interval);
    }, [interval]);

    const stop = useCallback(() => {
        if (intervalId.current) {
            clearInterval(intervalId.current);
            intervalId.current = null;
        }
    }, []);

    useEffect(() => {
        start();
        return stop;
    }, [start, stop]);

    return { start, stop };
}
