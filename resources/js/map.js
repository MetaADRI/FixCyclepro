import L from 'leaflet';

export function makePin(color, label = '') {
    const bg = color || '#287D1B';
    const html = `
        <div class="web-pin" title="${label}">
            <svg width="40" height="48" viewBox="0 0 40 48">
                <path fill="${bg}" stroke="#ffffff" stroke-width="2"
                    d="M20 2C11.2 2 4 9.2 4 18c0 11.5 16 28 16 28s16-16.5 16-28C36 9.2 28.8 2 20 2z"/>
                <circle cx="20" cy="18" r="6" fill="#ffffff" opacity="0.95"/>
            </svg>
        </div>`;
    return L.divIcon({
        html,
        className: '',
        iconSize: [40, 48],
        iconAnchor: [20, 46],
        popupAnchor: [0, -46],
    });
}

export function makeCarIcon(bearing = 0) {
    const deg = Number(bearing) || 0;
    const html = `
        <div class="web-car" style="transform: rotate(${deg}deg)">
            <svg width="34" height="34" viewBox="0 0 34 34">
                <circle cx="17" cy="17" r="15" fill="#287D1B" stroke="#ffffff" stroke-width="2"/>
                <path d="M17 7l5.5 17-5.5-3.5L11.5 24 17 7z" fill="#ffffff"/>
            </svg>
        </div>`;
    return L.divIcon({
        html,
        className: '',
        iconSize: [34, 34],
        iconAnchor: [17, 17],
    });
}

export function createMap(el, { center, zoom = 14, onClick = null } = {}) {
    const map = L.map(el, {
        zoomControl: false,
        attributionControl: true,
    }).setView(center || [6.5244, 3.3792], zoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(map);

    if (onClick) {
        map.on('click', (e) => onClick(e.latlng));
    }

    return map;
}

/* ---- Geocoding & reverse geocoding via OpenStreetMap (no API key) ---- */

async function fetchJson(url, timeoutMs = 8000) {
    const ctrl = new AbortController();
    const timer = setTimeout(() => ctrl.abort(), timeoutMs);
    try {
        const res = await fetch(url, {
            headers: { Accept: 'application/json' },
            signal: ctrl.signal,
        });
        if (!res.ok) return null;
        return await res.json();
    } catch {
        return null;
    } finally {
        clearTimeout(timer);
    }
}

export async function reverseGeocode(latlng) {
    const body = await fetchJson(
        `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latlng.lat}&lon=${latlng.lng}&zoom=16`,
        6000
    );
    if (!body) return '';
    return body.display_name || body.name || '';
}

export async function searchPlaces(query) {
    const body = await fetchJson(
        `https://nominatim.openstreetmap.org/search?format=jsonv2&limit=6&q=${encodeURIComponent(query)}`,
        10000
    );
    if (!Array.isArray(body)) return [];
    return body.map((p) => ({
        label: p.display_name,
        latlng: L.latLng(parseFloat(p.lat), parseFloat(p.lon)),
    }));
}

/* ---- Free routing via the public OSRM demo server (no API key) ---- */

export function decodePolyline(encoded) {
    if (!encoded || typeof encoded !== 'string') return [];
    const points = [];
    let index = 0;
    let lat = 0;
    let lng = 0;
    while (index < encoded.length) {
        let result = 0;
        let shift = 0;
        let byte;
        do {
            byte = encoded.charCodeAt(index++) - 63;
            result |= (byte & 0x1f) << shift;
            shift += 5;
        } while (byte >= 0x20);
        lat += result & 1 ? ~(result >> 1) : result >> 1;

        result = 0;
        shift = 0;
        do {
            byte = encoded.charCodeAt(index++) - 63;
            result |= (byte & 0x1f) << shift;
            shift += 5;
        } while (byte >= 0x20);
        lng += result & 1 ? ~(result >> 1) : result >> 1;

        points.push([lat / 1e5, lng / 1e5]);
    }
    return points;
}

export async function fetchRoute(from, to) {
    const points = Array.isArray(from) ? from.filter(Boolean) : [from, to].filter(Boolean);
    if (points.length < 2) return null;
    const coords = points.map((p) => `${p.lng},${p.lat}`).join(';');
    const url =
        `https://router.project-osrm.org/route/v1/driving/${coords}` +
        '?overview=full&geometries=geojson';
    const body = await fetchJson(url, 12000);
    const route = body && body.routes && body.routes[0];
    if (!route || !route.geometry) return null;
    return {
        coordinates: (route.geometry.coordinates || []).map(([lng, lat]) => [lat, lng]),
        distance: route.distance,
        duration: route.duration,
    };
}

export default {
    makePin,
    makeCarIcon,
    createMap,
    reverseGeocode,
    searchPlaces,
    decodePolyline,
    fetchRoute,
};