const TOKEN_KEY = 'fixcycle_token';
const USER_KEY = 'fixcycle_user';

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
    const headers = { Accept: 'application/json' };
    const t = token();
    if (t) headers['Authorization'] = 'Bearer ' + t;
    const res = await fetch(path, {
        method: 'POST',
        headers,
        body: JSON.stringify(params),
    });
    const body = await res.json().catch(() => ({}));
    return normalize(body, res.status);
}

export async function get(path) {
    const headers = { Accept: 'application/json' };
    const t = token();
    if (t) headers['Authorization'] = 'Bearer ' + t;
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
    post,
    get,
    login,
    fetchUser,
    logout,
};