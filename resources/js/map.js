import L from 'leaflet';

export function makePin(color, label = '') {
    const bg = color || '#E33A45';
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

export async function reverseGeocode(latlng) {
    try {
        const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latlng.lat}&lon=${latlng.lng}&zoom=16`;
        const res = await fetch(url, { headers: { Accept: 'application/json' } });
        if (!res.ok) return '';
        const body = await res.json();
        return body.display_name || body.name || '';
    } catch {
        return '';
    }
}

export async function searchPlaces(query) {
    try {
        const url = `https://nominatim.openstreetmap.org/search?format=jsonv2&limit=6&q=${encodeURIComponent(query)}`;
        const res = await fetch(url, { headers: { Accept: 'application/json' } });
        if (!res.ok) return [];
        const body = await res.json();
        return body.map((p) => ({
            label: p.display_name,
            latlng: L.latLng(parseFloat(p.lat), parseFloat(p.lon)),
        }));
    } catch {
        return [];
    }
}

export default {
    makePin,
    createMap,
    reverseGeocode,
    searchPlaces,
};