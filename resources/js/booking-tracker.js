import { post } from './api';

/**
 * Poll-based live booking tracker.
 *
 * Replaces the Android socket microservice with plain polling so the PWA needs
 * zero new infrastructure. To upgrade to Pusher/Soketi/WebSocket later, keep the
 * public API surface below (`createBookingTracker().start()/.stop()`) and swap
 * the implementation — every caller stays unchanged.
 */
export default function createBookingTracker(options = {}) {
    const {
        bookingId,
        intervalMs = 7000,
        maxBackoffMs = 30000,
        onPoll = () => {},
        onError = () => {},
    } = options;

    let timer = null;
    let stopped = true;

    async function tick(backoff) {
        if (stopped) return;
        try {
            const res = await post('/api/user/booking/tracking', { booking_id: bookingId });
            onPoll(res);
            backoff = intervalMs;
        } catch (e) {
            onError(e);
            backoff = Math.min(backoff * 2, maxBackoffMs);
        }
        if (!stopped) {
            timer = setTimeout(() => tick(backoff), backoff);
        }
    }

    return {
        start() {
            stopped = false;
            tick(intervalMs);
        },
        stop() {
            stopped = true;
            if (timer) clearTimeout(timer);
            timer = null;
        },
    };
}