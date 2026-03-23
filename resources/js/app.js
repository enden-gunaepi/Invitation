import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const LOADER_ID = 'global-page-loader';

function createGlobalLoader() {
    if (document.getElementById(LOADER_ID)) {
        return;
    }

    const style = document.createElement('style');
    style.setAttribute('data-loader-style', 'true');
    style.textContent = `
        #${LOADER_ID} {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(14, 14, 16, 0.22);
            backdrop-filter: blur(2px);
            transition: opacity .18s ease;
        }
        #${LOADER_ID}.show {
            display: flex;
        }
        #${LOADER_ID} .loader-shell {
            min-width: 160px;
            border-radius: 14px;
            border: 1px solid rgba(255,255,255,.22);
            background: rgba(20, 20, 24, 0.88);
            color: #fff;
            padding: 14px 16px;
            text-align: center;
            box-shadow: 0 12px 34px rgba(0,0,0,.22);
        }
        #${LOADER_ID} .loader-dot {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            margin: 0 auto 8px;
            border: 3px solid rgba(255,255,255,.2);
            border-top-color: rgba(255,255,255,.92);
            animation: global-spin .8s linear infinite;
        }
        #${LOADER_ID} .loader-text {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .02em;
            opacity: .92;
        }
        @keyframes global-spin {
            to { transform: rotate(360deg); }
        }
    `;

    const loader = document.createElement('div');
    loader.id = LOADER_ID;
    loader.innerHTML = `
        <div class="loader-shell" role="status" aria-live="polite">
            <div class="loader-dot"></div>
            <div class="loader-text">Memuat halaman...</div>
        </div>
    `;

    document.head.appendChild(style);
    document.body.appendChild(loader);
}

function showGlobalLoader() {
    const loader = document.getElementById(LOADER_ID);
    if (loader) loader.classList.add('show');
}

function hideGlobalLoader() {
    const loader = document.getElementById(LOADER_ID);
    if (loader) loader.classList.remove('show');
}

function shouldHandleLink(link) {
    if (!link || !link.getAttribute) return false;
    const href = link.getAttribute('href') || '';
    if (!href || href.startsWith('#')) return false;
    if (link.hasAttribute('download')) return false;
    if (link.target && link.target.toLowerCase() === '_blank') return false;
    if (href.startsWith('mailto:') || href.startsWith('tel:') || href.startsWith('javascript:')) return false;

    try {
        const url = new URL(link.href, window.location.origin);
        if (url.origin !== window.location.origin) return false;
    } catch {
        return false;
    }

    return true;
}

document.addEventListener('DOMContentLoaded', () => {
    createGlobalLoader();

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a');
        if (!shouldHandleLink(link)) return;
        showGlobalLoader();
    });

    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement)) return;
        showGlobalLoader();
    });

    window.addEventListener('pageshow', () => {
        hideGlobalLoader();
    });
});
