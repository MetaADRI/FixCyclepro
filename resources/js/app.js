import 'leaflet/dist/leaflet.css';
import Alpine from 'alpinejs';
import api, {
    isLoggedIn,
    cachedUser,
    storeUser,
    clearSession,
    fetchUser,
    currency,
    currencySymbol,
    money,
} from './api';
import { staticCountries, flagEmoji } from './countries';
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

function fmtMoney(num) {
    return money(num);
}

window.fmtMoney = fmtMoney;
window.money = money;
window.currency = currency;
window.currencySymbol = currencySymbol;

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
    primary: '#287D1B',
    accent: '#F26C00',
    logo: null,
    currency: currency(),
    currencySymbol: currencySymbol(),
    applyBrandedClass() {
        const rgb = hexToRgb(this.accent);
        if (rgb) {
            document.documentElement.style.setProperty('--accent', `${rgb.r} ${rgb.g} ${rgb.b}`);
        }
    },
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
            if (themeConfig.user_app_logo) {
                this.logo = themeConfig.user_app_logo;
                const img = document.getElementById('web-logo');
                const fb = document.getElementById('web-logo-fallback');
                if (img && fb) {
                    img.src = themeConfig.user_app_logo;
                    img.onerror = () => {
                        img.style.display = 'none';
                        fb.style.display = 'flex';
                    };
                    img.style.display = '';
                    fb.style.display = 'none';
                }
            }
            if (themeConfig.accent_color) this.accent = themeConfig.accent_color;
            if (themeConfig.currency) this.currency = themeConfig.currency;
            if (themeConfig.currency_symbol) this.currencySymbol = themeConfig.currency_symbol;
            this.applyBrandedClass();
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
    email: '',
    password: '',
    showPassword: false,
    loading: false,
    error: '',
    notice: new URLSearchParams(location.search).get('registered')
        ? 'Account created. Sign in to continue.'
        : '',
    async submit() {
        this.error = '';
        if (!this.email || !this.password) {
            this.error = 'Enter your email and password.';
            return;
        }
        this.loading = true;
        try {
            const r = await api.loginWithEmail(this.email.trim(), this.password);
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
    showPassword: false,
    country: null,
    countryQuery: '',
    countriesOpen: false,
    countriesLoading: true,
    countries: [],
    loading: false,
    error: '',
    async init() {
        await this.loadCountries();
    },
    async loadCountries() {
        this.countriesLoading = true;
        try {
            const r = await api.post('/api/user/countryList', {});
            const list = r.ok && Array.isArray(r.data) ? r.data : [];
            this.countries = list.map((c) => {
                const iso = c.country_code || c.isoCode || c.iso_code || '';
                const dial = c.phonecode ? '+' + String(c.phonecode).replace(/[^\d]/g, '') : '';
                return {
                    id: c.id,
                    name: c.name || c.countryName || iso || 'Country',
                    iso,
                    dial,
                    flag: flagEmoji(iso),
                    currency: c.currency || null,
                    min: c.minNumPhone || null,
                    max: c.maxNumPhone || null,
                };
            });
        } catch (e) {
            this.countries = [];
        } finally {
            if (!this.countries.length) this.countries = staticCountries();
            this.countriesLoading = false;
        }
    },
    get filteredCountries() {
        const q = (this.countryQuery || '').trim().toLowerCase();
        if (!q) return this.countries;
        return this.countries.filter(
            (c) =>
                c.name.toLowerCase().includes(q) ||
                (c.iso || '').toLowerCase().includes(q) ||
                (c.dial || '').includes(q)
        );
    },
    selectCountry(c) {
        this.country = c;
        this.countryQuery = '';
        this.countriesOpen = false;
        this.error = '';
    },
    toggleCountries() {
        this.countriesOpen = !this.countriesOpen;
        if (this.countriesOpen) {
            this.$nextTick(() => this.$refs.countrySearch && this.$refs.countrySearch.focus());
        }
    },
    async submit() {
        this.error = '';
        if (!this.country) {
            this.error = 'Please select your country to continue.';
            return;
        }
        if (!this.country.id && !this.country.iso) {
            this.error = 'We could not load a valid country. Refresh the page and try again.';
            return;
        }
        if (!this.phone || !this.password || !this.email) {
            this.error = 'Enter your phone number, email and password.';
            return;
        }
        this.loading = true;
        try {
            const dial = this.country.dial || '';
            const phone = dial + String(this.phone).replace(/^0+/, '').replace(/\s+/g, '');
            const first_name =
                this.first_name.trim() ||
                (this.email.includes('@') ? this.email.split('@')[0] : 'Customer');
            const r = await api.post('/api/user/signup', {
                first_name,
                last_name: this.last_name.trim(),
                country_id: this.country.id || null,
                country_code: this.country.iso || null,
                email: this.email.trim(),
                phone,
                password: this.password,
                requested_from: 'web',
            });
            if (!r.ok) {
                this.error = r.message || 'Signup failed';
                return;
            }
            if (r.data && (r.data.access_token || r.data.token)) {
                localStorage.setItem('fixcycle_token', r.data.access_token || r.data.token);
                await Alpine.store('auth').bootstrap();
                location.href = '/app';
            } else {
                location.href = '/login?registered=1';
            }
        } catch (e) {
            this.error = e.message || 'Something went wrong';
        } finally {
            this.loading = false;
        }
    },
}));

/* ---------- shared session helpers ---------- */

const SEGMENT_KEY = 'fixcycle_segment';

function segmentStore() {
    try {
        return JSON.parse(localStorage.getItem(SEGMENT_KEY) || 'null');
    } catch (e) {
        return null;
    }
}

function saveSegment(seg) {
    localStorage.setItem(SEGMENT_KEY, JSON.stringify(seg || null));
}

const STATUS_TEXT = {
    1000: 'Looking for driver',
    1001: 'Searching for driver',
    1012: 'Searching for driver',
    1002: 'Driver assigned',
    1003: 'Driver arriving',
    1004: 'Trip started',
    1005: 'Ride completed',
    1006: 'Completed',
    1007: 'Completed',
    1008: 'Cancelled',
    1019: 'Searching for driver',
};

function statusText(s) {
    return STATUS_TEXT[s] || 'Booking status ' + (s || '—');
}

function dropPayload(drops) {
    const arr = (drops || [])
        .filter((d) => d && d.lat)
        .map((d) => ({
            drop_address: d.label || d.address || '',
            drop_latitude: String(d.lat),
            drop_longitude: String(d.lng),
        }));
    return arr.length ? JSON.stringify(arr) : '';
}

/* ---------- home screen (services for current area) ---------- */

Alpine.data('homeScreen', () => ({
    locating: true,
    coords: null,
    address: '',
    segments: [],
    loading: true,
    error: '',
    searching: false,
    searchTerm: '',
    results: [],
    activeSegment: segmentStore(),
    async init() {
        this.activeSegment = segmentStore();
        await this.locate();
        await this.loadSegments();
    },
    locate() {
        this.locating = true;
        this.error = '';
        return new Promise((resolve) => {
            if (!navigator.geolocation) {
                this.locating = false;
                return resolve();
            }
            navigator.geolocation.getCurrentPosition(
                async (pos) => {
                    this.coords = { lat: pos.coords.latitude, lng: pos.coords.longitude };
                    try {
                        const a = await reverseGeocode({
                            lat: pos.coords.latitude,
                            lng: pos.coords.longitude,
                        });
                        if (a) this.address = a;
                    } catch (e) {}
                    this.locating = false;
                    resolve();
                },
                () => {
                    this.locating = false;
                    resolve();
                },
                { enableHighAccuracy: true, timeout: 12000 }
            );
        });
    },
    async loadSegments() {
        this.loading = true;
        this.error = '';
        this.segments = [];
        try {
            if (!this.coords) {
                this.error = 'Location unavailable. Search for an area to continue.';
                return;
            }
            const r = await api.post('/api/user/country-area-segments', {
                latitude: String(this.coords.lat),
                longitude: String(this.coords.lng),
            });
            if (!r.ok) {
                this.error = r.message || 'No services available in this area yet.';
                return;
            }
            this.segments = (Array.isArray(r.data) ? r.data : []).map((s) => ({
                segment_id: s.segment_id,
                slag: s.slag || '',
                name: s.name || s.slag || 'Service',
                icon: s.segment_icon || '',
                gradient_1: s.segment_background_gradient_1 || '#FFF0D9',
                gradient_2: s.segment_background_gradient_2 || '#FFFCEF',
            }));
            if (this.segments.length && this.activeSegment) {
                this.activeSegment = this.segments.find(
                    (s) => Number(s.segment_id) === Number(this.activeSegment.segment_id)
                ) || this.segments[0];
            }
        } catch (e) {
            this.error = e.message || 'Could not load services.';
        } finally {
            this.loading = false;
        }
    },
    choose(seg) {
        saveSegment({ segment_id: seg.segment_id, name: seg.name, slag: seg.slag });
        location.href = '/app/booking/new?segment=' + seg.segment_id;
    },
    async search() {
        const q = (this.searchTerm || '').trim();
        if (!q) return;
        this.searching = true;
        try {
            this.results = await searchPlaces(q);
        } catch (e) {
            this.results = [];
        } finally {
            this.searching = false;
        }
    },
    async usePlace(p) {
        this.results = [];
        this.searchTerm = p.label;
        this.coords = { lat: p.latlng.lat, lng: p.latlng.lng };
        this.address = p.label;
        await this.loadSegments();
    },
    retry() {
        this.init();
    },
}));

/* ---------- booking flow: pickup → vehicle → estimate → pay → confirm ---------- */

Alpine.data('bookingFlow', () => ({
    segment: null,
    locating: true,
    coords: null,
    pickup: '',
    drop: '',
    searching: false,
    term: '',
    results: [],
    dropRows: [{ label: '', lat: null, lng: null }],
    serviceTypes: [],
    activeService: null,
    activeCategory: null,
    activeVehicle: null,
    carsLoading: true,
    carsError: '',
    estimate: null,
    estimating: false,
    checkout: null,
    paymentMethods: [
        { id: 1, slug: 'CASH', label: 'Cash' },
        { id: 3, slug: 'WALLET', label: 'Wallet' },
    ],
    paymentOption: [],
    payment_method_id: 1,
    payment_option_id: null,
    processing: false,
    error: '',
    async init() {
        const seg = segmentStore();
        const urlSeg = new URLSearchParams(location.search).get('segment');
        if (urlSeg) seg && saveSegment(seg);
        if (!seg) {
            this.error = 'Please choose a service from the home screen first.';
            return;
        }
        this.segment = seg;
        await this.locate();
        await this.loadConfig();
        await this.loadCars();
    },
    async loadConfig() {
        try {
            const r = await api.post('/api/user/configuration');
            if (r.ok && r.data && Array.isArray(r.data.paymentOption)) {
                this.paymentOption = r.data.paymentOption.map((o) => ({
                    id: o.id,
                    name: o.name || o.slug || o,
                    slug: o.slug || '',
                }));
            }
            const pm = r.data && r.data.payment_method;
            if (pm && pm.id) {
                this.paymentMethods = [{ id: pm.id, slug: pm.name, label: pm.name || 'Cash' }];
                if (pm.only_cash !== true) {
                    this.paymentMethods.push({ id: 3, slug: 'WALLET', label: 'Wallet' });
                    this.paymentMethods.push({ id: 4, slug: 'ONLINE', label: 'Online' });
                }
            }
        } catch (e) {
            /* keep defaults */
        }
    },
    locate() {
        this.locating = true;
        return new Promise((resolve) => {
            if (!navigator.geolocation) {
                this.locating = false;
                return resolve();
            }
            navigator.geolocation.getCurrentPosition(
                async (pos) => {
                    this.coords = { lat: pos.coords.latitude, lng: pos.coords.longitude };
                    try {
                        const a = await reverseGeocode({
                            lat: pos.coords.latitude,
                            lng: pos.coords.longitude,
                        });
                        if (a) this.pickup = a;
                    } catch (e) {}
                    this.locating = false;
                    resolve();
                },
                () => {
                    this.locating = false;
                    resolve();
                },
                { enableHighAccuracy: true, timeout: 12000 }
            );
        });
    },
    async loadCars() {
        this.carsLoading = true;
        this.carsError = '';
        this.serviceTypes = [];
        try {
            const r = await api.post('/api/user/cars', {
                latitude: String(this.coords.lat),
                longitude: String(this.coords.lng),
                segment_id: this.segment.segment_id,
            });
            if (!r.ok) {
                this.carsError = r.message || 'Could not load available services.';
                return;
            }
            this.areaId = r.data && r.data.id ? r.data.id : null;
            const raw = (r.data && r.data.service_types) || [];
            this.serviceTypes = raw.map((s) => {
                let vehicles = [];
                if (Array.isArray(s.vehicles)) {
                    vehicles = s.vehicles.map((v) => ({
                        id: v.id,
                        name: v.vehicleTypeName || v.name || 'Vehicle',
                        description: v.vehicleTypeDescription || '',
                        image: v.vehicleTypeImage || '',
                        ride_now: v.ride_now,
                        ride_later: v.ride_later,
                    }));
                } else if (Array.isArray(s.arr_category)) {
                    vehicles = s.arr_category.map((c) => ({
                        category: true,
                        category_id: c.category_id,
                        name: c.name || c.category_name || 'Category',
                        description: c.category_description || '',
                        image: c.category_image || '',
                        vehicles: Array.isArray(c.vehicles) ? c.vehicles : [],
                    }));
                }
                return {
                    id: s.id,
                    type: s.type,
                    serviceName: s.locale_service_name || s.serviceName || 'Service',
                    sequence: s.sequence || 0,
                    vehicles,
                };
            });
            if (this.serviceTypes.length) {
                this.activeService = this.serviceTypes[0];
                this.pickService(this.activeService);
            }
        } catch (e) {
            this.carsError = e.message || 'Could not load available services.';
        } finally {
            this.carsLoading = false;
        }
    },
    pickService(s) {
        this.activeService = s;
        this.activeCategory = null;
        this.activeVehicle = null;
        if (s && s.category) return;
        const ls = s && s.vehicles;
        if (Array.isArray(ls) && ls.length) this.activeVehicle = ls[0];
    },
    pickCategory(c) {
        this.activeCategory = c;
        this.activeVehicle = null;
        if (Array.isArray(c.vehicles) && c.vehicles.length) this.activeVehicle = c.vehicles[0];
    },
    addDrop() {
        if (this.dropRows.length >= 4) return;
        this.dropRows.push({ label: '', lat: null, lng: null });
    },
    removeDrop(i) {
        if (this.dropRows.length > 1) this.dropRows.splice(i, 1);
    },
    async searchFor(index) {
        const q = (this.term || '').trim();
        if (!q) return;
        this.searching = true;
        try {
            this.results = await searchPlaces(q);
        } catch (e) {
            this.results = [];
        } finally {
            this.searching = false;
        }
    },
    applyResult(p, index) {
        this.results = [];
        this.term = p.label;
        const row = this.dropRows[index];
        if (row) {
            row.label = p.label;
            row.lat = p.latlng.lat;
            row.lng = p.latlng.lng;
        } else {
            this.drop = p.label;
        }
    },
    setMainDrop(p) {
        this.results = [];
        this.term = p.label;
        this.drop = p.label;
    },
    currency() {
        return Alpine.store('theme').currency || currency();
    },
    money(value) {
        return money(value);
    },
    async estimateBooking() {
        this.error = '';
        if (!this.activeVehicle || !this.activeVehicle.id || !this.coords) return;
        this.estimating = true;
        this.estimate = null;
        this.checkout = null;
        try {
            const r = await api.post('/api/user/checkout', {
                segment_id: this.segment.segment_id,
                area: this.areaId,
                service_type: this.activeService.id,
                vehicle_type: this.activeVehicle.id,
                pickup_latitude: String(this.coords.lat),
                pickup_longitude: String(this.coords.lng),
                pick_up_location: this.pickup,
                total_drop_location: this.dropRows.length,
                drop_location: dropPayload(this.dropRows),
            });
            if (!r.ok) {
                throw new Error(r.message || 'Could not calculate the estimate.');
            }
            const co = r.data && (r.data.booking_checkouts || r.data.checkout);
            this.checkout = co;
            this.estimate = {
                id: co && co.id,
                bill: co && (co.estimate_bill || co.estimate),
                fares: r.data && (r.data.fare || r.data.fare_breakup || null),
            };
        } catch (e) {
            this.error = e.message || 'Could not calculate the estimate.';
        } finally {
            this.estimating = false;
        }
    },
    async setPayment() {
        this.error = '';
        this.processing = true;
        try {
            const payload = { checkout: this.checkout.id, payment_method_id: this.payment_method_id };
            if (Number(this.payment_method_id) === 4 && this.payment_option_id) {
                payload.payment_option_id = this.payment_option_id;
            }
            const r = await api.post('/api/user/checkout-payment', payload);
            if (!r.ok) throw new Error(r.message || 'Could not finish the booking.');
        } catch (e) {
            throw e;
        } finally {
            this.processing = false;
        }
    },
    async confirm() {
        this.error = '';
        this.processing = true;
        try {
            await this.setPayment();
            const c = await api.post('/api/user/confirm', {
                segment_id: this.segment.segment_id,
                checkout: this.checkout.id,
            });
            if (!c.ok) throw new Error(c.message || 'Could not confirm the booking.');
            const d = c.data || {};
            if (d.url) {
                window.location.href = d.url;
                return;
            }
            const id = d.id || d.booking_id;
            if (id) {
                location.href = '/app/booking/active/' + id;
            } else {
                location.href = '/app/bookings';
            }
        } catch (e) {
            this.error = e.message || 'Could not confirm the booking.';
        } finally {
            this.processing = false;
        }
    },
}));

/* ---------- active booking / tracking ---------- */

Alpine.data('activeBooking', () => ({
    bookingId: null,
    booking: null,
    loading: true,
    error: '',
    reasons: [],
    showCancel: false,
    reason_id: null,
    cancelling: false,
    tracker: null,
    async init() {
        this.bookingId = window.bookingId || null;
        if (!this.bookingId) {
            this.error = 'No booking selected.';
            this.loading = false;
            return;
        }
        try {
            await this.loadReasons();
        } catch (e) {}
        const onOk = (data) => {
            const b = data && (data.booking || data);
            this.booking = b || this.booking;
            this.loading = false;
            if (this.isDone()) this.tracker && this.tracker.stop();
        };
        this.tracker = createBookingTracker(this.bookingId, onOk);
        this.tracker.start();
    },
    isDone() {
        const s = this.booking && this.booking.booking_status;
        return [1005, 1006, 1007, 1008].includes(Number(s));
    },
    async loadReasons() {
        const r = await api.post('/api/user/cancel-reasons');
        if (r.ok) {
            this.reasons = (Array.isArray(r.data) ? r.data : []).map((x) => ({
                id: x.id,
                reason: x.reason || x.name || 'Other',
            }));
        }
    },
    async cancel() {
        if (!this.reason_id) {
            this.error = 'Please choose a reason.';
            return;
        }
        this.cancelling = true;
        this.error = '';
        try {
            const r = await api.post('/api/user/booking/cancel', {
                booking_id: this.bookingId,
                cancel_reason_id: this.reason_id,
            });
            if (!r.ok) throw new Error(r.message || 'Could not cancel the booking.');
            this.showCancel = false;
            toast('Booking cancelled');
            this.tracker && this.tracker.stop();
            location.href = '/app/bookings';
        } catch (e) {
            this.error = e.message || 'Could not cancel the booking.';
        } finally {
            this.cancelling = false;
        }
    },
}));

/* ---------- bookings list ---------- */

Alpine.data('bookingsScreen', () => ({
    segment: null,
    tab: 'ACTIVE',
    items: [],
    loading: true,
    error: '',
    async init() {
        this.segment = segmentStore();
        if (!this.segment) {
            this.error = 'Choose a service first to see bookings.';
            this.loading = false;
            return;
        }
        await this.load();
    },
    async load() {
        this.loading = true;
        this.error = '';
        this.items = [];
        try {
            const r = await api.post('/api/user/booking/history', {
                segment_id: this.segment.segment_id,
                request_type: this.tab,
            });
            if (!r.ok) {
                this.error = r.message || 'No bookings yet.';
                return;
            }
            this.items = (Array.isArray(r.data) ? r.data : []).map((b) => ({
                id: b.booking_id,
                ref: b.merchant_booking_id,
                title: b.highlighted_text || b.service_name || 'Booking',
                subtitle: b.highlighted_small_text || b.small_text || '',
                status: statusText(b.booking_status),
                estimate: b.estimate_bill,
                currency: b.currency || currency(),
                money: money(b.estimate_bill),
                is_in_drive: b.is_in_drive,
            }));
        } catch (e) {
            this.error = e.message || 'Could not load bookings.';
        } finally {
            this.loading = false;
        }
    },
    switchTab(t) {
        this.tab = t;
        this.load();
    },
}));

/* ---------- booking detail ---------- */

Alpine.data('bookingDetail', () => ({
    bookingId: null,
    booking: null,
    loading: true,
    error: '',
    async init() {
        this.bookingId = window.bookingId || null;
        if (!this.bookingId) {
            this.error = 'No booking selected.';
            this.loading = false;
            return;
        }
        try {
            const r = await api.post('/api/user/booking/tracking', {
                booking_id: this.bookingId,
            });
            if (!r.ok) throw new Error(r.message || 'Could not load the booking.');
            this.booking = r.data && (r.data.booking || r.data);
        } catch (e) {
            this.error = e.message || 'Could not load the booking.';
        } finally {
            this.loading = false;
        }
    },
    money(value) {
        return money(value);
    },
}));

/* ---------- wallet ---------- */

Alpine.data('walletScreen', () => ({
    amount: '',
    options: [],
    payment_option_id: null,
    transactions: [],
    txLoading: true,
    adding: false,
    error: '',
    async init() {
        try {
            await Alpine.store('auth').bootstrap();
            const cfgRes = await api.post('/api/user/configuration');
            if (cfgRes.ok && Array.isArray(cfgRes.data) === false) {
                const opts = cfgRes.data && cfgRes.data.paymentOption;
                if (Array.isArray(opts)) {
                    this.options = opts.map((o) => ({
                        id: o.id,
                        slug: o.slug || o.name || o,
                        name: o.name || o.slug || o,
                    }));
                }
                if (this.options.length) this.payment_option_id = this.options[0].id;
            }
        } catch (e) {}
        await this.loadTransactions();
    },
    balance() {
        const u = Alpine.store('auth').user;
        if (!u || u.wallet_balance === null || u.wallet_balance === undefined) return '—';
        return money(u.wallet_balance);
    },
    money(value) {
        return money(value);
    },
    async loadTransactions() {
        this.txLoading = true;
        this.transactions = [];
        try {
            const r = await api.post('/api/user/wallet/transaction', { filter: 1 });
            if (r.ok && Array.isArray(r.data)) {
                this.transactions = r.data.map((t) => ({
                    id: t.id,
                    amount: t.amount !== null && t.amount !== undefined ? t.amount : t.wallet_amount,
                    label:
                        t.label_description ||
                        t.description ||
                        t.transaction_description ||
                        (t.type ? 'Wallet ' + t.type : 'Wallet'),
                    date:
                        t.created_at ||
                        t.date ||
                        t.transaction_date ||
                        '',
                    type: t.type || t.transaction_type || '',
                }));
            }
        } catch (e) {
            /* keep empty */
        } finally {
            this.txLoading = false;
        }
    },
    async add() {
        this.error = '';
        const amt = Number(this.amount);
        if (!amt || amt <= 0) {
            this.error = 'Enter a valid amount.';
            return;
        }
        this.adding = true;
        try {
            const payload = { amount: Number(amt.toFixed(2)) };
            if (this.payment_option_id) payload.payment_option_id = this.payment_option_id;
            const r = await api.post('/api/user/wallet/addMoney', payload);
            if (!r.ok) throw new Error(r.message || 'Could not add money.');
            toast('Money added to wallet');
            this.amount = '';
            await Alpine.store('auth').bootstrap();
            await this.loadTransactions();
        } catch (e) {
            this.error = e.message || 'Could not add money.';
        } finally {
            this.adding = false;
        }
    },
}));

/* ---------- profile ---------- */

Alpine.data('profileScreen', () => ({
    user: null,
    loading: true,
    error: '',
    async init() {
        this.loading = true;
        try {
            const r = await api.fetchUser();
            if (r.ok) this.user = r.data;
            else this.error = r.message || 'Could not load profile.';
        } catch (e) {
            this.error = e.message || 'Could not load profile.';
        } finally {
            this.loading = false;
        }
    },
    initials() {
        const u = this.user || {};
        return ((u.first_name || '') + ' ' + (u.last_name || '')).trim().slice(0, 2).toUpperCase() || 'U';
    },
    money(value) {
        return money(value);
    },
    logout() {
        api.logout().then(() => {
            location.href = '/login';
        });
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
    pages: [],
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
            const cfg = await api.post('/api/user/configuration');
            if (cfg.ok && cfg.data && Array.isArray(cfg.data.cms_pages)) {
                this.pages = cfg.data.cms_pages;
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