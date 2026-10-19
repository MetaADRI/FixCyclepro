const TOKEN_KEY = 'fixcycle_token';
const USER_KEY = 'fixcycle_user';

// Endpoints that establish a session or are public. They authenticate with the
// merchant access pin (not a bearer token), so we must never attach a stored
// token to them — a stale token would otherwise be rejected by ApiMiddleware
// with "unauthorised request" before the access pin is ever checked.
const PUBLIC_PATHS = [
    '/api/user/login',
    '/api/user/login/otp',
    '/api/user/on-board',
    '/api/user/signup',
    '/api/user/socialsignin',
    '/api/user/socialsingup',
    '/api/user/countryList',
    '/api/user/configuration',
    '/api/user/fastlane-configuration',
];

function isPublicPath(path) {
    const p = String(path).split('?')[0];
    return PUBLIC_PATHS.includes(p);
}

function runtimeConfig() {
    return (typeof window !== 'undefined' && window.FixConfig) || {};
}

export function merchantId() {
    return runtimeConfig().merchantId || null;
}

export function accessPin() {
    return runtimeConfig().accessPin || null;
}

export function currency() {
    return runtimeConfig().currency || 'ZMW';
}

export function currencySymbol() {
    return runtimeConfig().currencySymbol || 'K';
}

export function money(value) {
    if (value === null || value === undefined || value === '') return '';
    const n = Number(value);
    if (Number.isNaN(n)) return '';
    const formatted = n.toLocaleString(undefined, {
        minimumFractionDigits: Number.isInteger(n) ? 0 : 2,
        maximumFractionDigits: 2,
    });
    return currencySymbol() + ' ' + formatted;
}

export function token() {
    return localStorage.getItem(TOKEN_KEY);
}

export function isLoggedIn() {
    return !!token();
}

export function clearSession() {
    localStorage.removeItem(TOKEN_KEY);
    localStorage.removeItem(USER_KEY);
}

export function cachedUser() {
    try {
        return JSON.parse(localStorage.getItem(USER_KEY) || 'null');
    } catch {
        return null;
    }
}

export function storeUser(user) {
    if (user) localStorage.setItem(USER_KEY, JSON.stringify(user));
}

export function redirectToLogin() {
    if (!location.pathname.startsWith('/login')) {
        clearSession();
        location.href = '/login';
    }
}

function normalize(body, status) {
    const result = body ? String(body.result) : '0';
    if (result === '999' || status === 401) {
        redirectToLogin();
        const e = new Error((body && body.message) || 'Session expired');
        e.unauthorized = true;
        throw e;
    }
    return {
        ok: result === '1',
        result,
        message: (body && body.message) || '',
        data: body && body.result ? body.data ?? null : null,
    };
}

export async function post(path, params = {}) {
    const headers = { Accept: 'application/json', 'Content-Type': 'application/json' };
    const t = token();
    if (t && !isPublicPath(path)) headers['Authorization'] = 'Bearer ' + t;

    const cfg = runtimeConfig();
    const payload = { ...params };
    if (cfg.accessPin && payload.access_pin === undefined) payload.access_pin = cfg.accessPin;
    if (cfg.merchantId && payload.merchant_id === undefined) payload.merchant_id = cfg.merchantId;

    const res = await fetch(path, {
        method: 'POST',
        headers,
        body: JSON.stringify(payload),
    });
    const body = await res.json().catch(() => ({}));
    return normalize(body, res.status);
}

export async function get(path) {
    const headers = { Accept: 'application/json' };
    const t = token();
    if (t && !isPublicPath(path)) headers['Authorization'] = 'Bearer ' + t;
    const res = await fetch(path, { headers });
    const body = await res.json().catch(() => ({}));
    return normalize(body, res.status);
}

export async function login(creds) {
    const r = await post('/api/user/login', { ...creds, requested_from: 'web' });
    if (r.ok) {
        const accessToken = r.data && (r.data.access_token || r.data.token);
        if (!accessToken) {
            throw new Error(r.message || 'No access token returned');
        }
        localStorage.setItem(TOKEN_KEY, accessToken);
        localStorage.removeItem(USER_KEY);
    }
    return r;
}

export async function loginWithEmail(email, password) {
    return login({
        phone: email,
        email,
        password,
        logintype: 'EMAIL',
        login_type: 'EMAIL',
    });
}

export async function fetchUser() {
    const r = await post('/api/user/details');
    if (r.ok) storeUser(r.data);
    return r;
}

export async function logout() {
    try {
        await post('/api/user/logout');
    } catch (e) {
        if (!e.unauthorized) throw e;
    } finally {
        clearSession();
    }
}

export default {
    token,
    isLoggedIn,
    clearSession,
    cachedUser,
    storeUser,
    redirectToLogin,
    merchantId,
    accessPin,
    currency,
    currencySymbol,
    money,
    post,
    get,
    login,
    loginWithEmail,
    fetchUser,
    logout,
};