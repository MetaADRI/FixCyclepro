import 'leaflet/dist/leaflet.css';
import Alpine from 'alpinejs';
import api, { isLoggedIn, cachedUser, storeUser, clearSession, fetchUser } from './api';
import { createMap, makePin, reverseGeocode, searchPlaces } from './map';
import createBookingTracker from './booking-tracker';

window.Fixapi = api;
window.Alpine = Alpine;

/* ---------- global helpers ---------- */

let toastTimer = null;
function toast(message) {
    let el = document.querySelector('.toast');
    if (!el) {
        el = document.createElement('div');
        el.className = 'toast';
        document.body.appendChild(el);
    }
    el.textContent = message || '';
    el.classList.remove('opacity-0');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => el.classList.add('opacity-0'), 3000);
}
window.toast = toast;

function pickFormat(n) {
    if (n === null || n === undefined || Number.isNaN(Number(n))) return '0.00';
    return Number(n).toFixed(2);
}

function fmt(num) {
    if (num === null || num === undefined || num === '') return '0.00';
    const n = Number(num);
    return Number.isNaN(n) ? String(num) : n.toFixed(2);
}

/* ---------- shared Alpine data ---------- */

Alpine.store('auth', {
    user: cachedUser(),
    loading: false,
    async bootstrap() {
        if (!isLoggedIn()) return;
        try {
            this.loading = true;
            this.user = this.user || {};
            const r = await fetchUser();
            if (r.ok) this.user = r.data;
        } catch (e) {
            /* session expired — api.js already redirected */
        } finally {
            this.loading = false;
        }
    },
    logout() {
        clearSession();
        this.user = null;
        location.href = '/login';
    },
});

Alpine.store('theme', {
    primary: '#E33A45',
    accent: '#0EA5A4',
    logo: null,
    async load() {
        try {
            const r = await api.post('/api/user/configuration');
            if (!r.ok) return;
            const themeConfig =
                (r.data && (r.data.theme_cofig || r.data.theme_config)) || {};
            const color = themeConfig.primary_color_user || themeConfig.primary_color;
            if (color) {
                this.primary = color;
                const rgb = hexToRgb(color);
                if (rgb) {
                    document.documentElement.style.setProperty(
                        '--brand',
                        `${rgb.r} ${rgb.g} ${rgb.b}`
                    );
                }
                document.querySelector('meta[name="theme-color"]')?.setAttribute(
                    'content',
                    color
                );
            }
            if (themeConfig.user_app_logo) this.logo = themeConfig.user_app_logo;
            if (themeConfig.accent_color) this.accent = themeConfig.accent_color;
            Alpine.store('auth').applyBrandedClass();
        } catch (e) {
            /* keep defaults */
        }
    },
});

function hexToRgb(hex) {
    const m = String(hex || '').replace('#', '').match(/^([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i);
    if (!m) return null;
    return { r: parseInt(m[1], 16), g: parseInt(m[2], 16), b: parseInt(m[3], 16) };
}

Alpine.data('appShell', () => ({
    nav: [
        { label: 'Home', path: '/app', icon: 'home' },
        { label: 'Bookings', path: '/app/bookings', icon: 'bookings' },
        { label: 'Wallet', path: '/app/wallet', icon: 'wallet' },
        { label: 'Profile', path: '/app/profile', icon: 'profile' },
        { label: 'Support', path: '/app/support', icon: 'support' },
    ],
    open: false,
    isActive(path) {
        return location.pathname === path || location.pathname.startsWith('/app');
    },
}));

/* ---------- auth pages ---------- */

Alpine.data('loginScreen', () => ({
    phone: '',
    password: '',
    email: '',
    mode: 'phone', // phone | email
    loading: false,
    error: '',
    async submit() {
        this.error = '';
        this.loading = true;
        try {
            const creds =
                this.mode === 'phone'
                    ? { phone: this.phone, password: this.password }
                    : { email: this.email, password: this.password };
            const r = await api.login(creds);
            if (!r.ok) {
                this.error = r.message || 'Login failed';
                return;
            }
            await Alpine.store('auth').bootstrap();
            location.href = '/app';
        } catch (e) {
            this.error = e.message || 'Something went wrong';
        } finally {
            this.loading = false;
        }
    },
    switchMode(mode) {
        this.mode = mode;
        this.error = '';
    },
}));

Alpine.data('otpScreen', () => ({
    userName: '',
    type: 1,
    for: 'PHONE',
    otp: '',
    loading: false,
    sending: false,
    error: '',
    sent: false,
    async send() {
        this.error = '';
        this.sending = true;
        try {
            const r = await api.post('/api/user/otp', {
                type: this.type,
                for: this.for,
                user_name: this.userName,
            });
            if (!r.ok) {
                this.error = r.message || 'Could not send OTP';
                return;
            }
            this.sent = true;
        } catch (e) {
            this.error = e.message || 'Could not send OTP';
        } finally {
            this.sending = false;
        }
    },
    async verify() {
        this.error = '';
        this.loading = true;
        try {
            const r = await api.post('/api/user/login/otp', {
                user_name: this.userName,
                otp: this.otp,
                type: this.type,
                for: this.for,
            });
            if (!r.ok) {
                this.error = r.message || 'Invalid code';
                return;
            }
            await Alpine.store('auth').bootstrap();
            location.href = '/app';
        } catch (e) {
            this.error = e.message || 'Something went wrong';
        } finally {
            this.loading = false;
        }
    },
    switchFor(value) {
        this.for = value;
        this.type = value === 'PHONE' ? 1 : 1;
        this.sent = false;
        this.error = '';
    },
}));

Alpine.data('signupScreen', () => ({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    password: '',
    loading: false,
    error: '',
    async submit() {
        this.error = '';
        this.loading = true;
        try {
            const r = await api.post('/api/user/signup', {
                first_name: this.first_name,
                last_name: this.last_name,
                email: this.email,
                phone: this.phone,
                password: this.password,
                requested_from: 'web',
            });
            if (!r.ok) {
                this.error = r.message || 'Signup failed';
                return;
            }
            if (r.data && (r.data.access_token || r.data.token)) {
                await Alpine.store('auth').bootstrap();
                location.href = '/app';
            } else {
                location.href = '/otp';
            }
        } catch (e) {
            this.error = e.message || 'Something went wrong';
        } finally {
            this.loading = false;
        }
    },
}));

/* ---------- support / faq ---------- */

Alpine.data('supportScreen', () => ({
    faqs: [],
    faqTypes: [],
    activeType: null,
    loading: true,
    page: null,
    slug: window.supportSlug || null,
    async init() {
        this.loading = true;
        try {
            if (this.slug) {
                const p = await api.post('/api/user/cms/pages', { slug: this.slug });
                if (p.ok) this.page = p.data;
            }
            const f = await api.post('/api/user/faq');
            if (f.ok) {
                this.faqs = Array.isArray(f.data) ? f.data : f.data?.faqs || [];
                this.faqTypes = Array.isArray(f.data)
                    ? []
                    : f.data?.faq_type || [];
            }
        } catch (e) {
            /* ignore */
        } finally {
            this.loading = false;
        }
    },
}));

window.createBookingTracker = createBookingTracker;
window.searchPlaces = searchPlaces;
window.reverseGeocode = reverseGeocode;
window.createMap = createMap;
window.makePin = makePin;
window.fmt = fmt;
window.pickFormat = pickFormat;
window.webUser = () => ({ user: cachedUser(), isLoggedIn: isLoggedIn() });

Alpine.start();

if ('serviceWorker' in navigator && location.protocol === 'https:') {
    navigator.serviceWorker.register('/sw.js').catch(() => {});
}
Alpine.store('theme').load?.();