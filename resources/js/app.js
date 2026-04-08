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
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            z-index: 99999;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        #${LOADER_ID}.show {
            opacity: 1;
        }
        #${LOADER_ID} .loader-bar {
            height: 100%;
            background: var(--accent, #34c759);
            width: 0%;
            box-shadow: 0 0 10px var(--accent, #34c759), 0 0 5px var(--accent, #34c759);
            transition: width 0.4s ease;
        }
        #${LOADER_ID}.loading .loader-bar {
            width: 80%;
            transition: width 10s cubic-bezier(0, 1, 1, 1);
        }
        #${LOADER_ID}.done .loader-bar {
            width: 100%;
            transition: width 0.3s ease;
        }
    `;

    const loader = document.createElement('div');
    loader.id = LOADER_ID;
    loader.innerHTML = `
        <div class="loader-bar"></div>
    `;

    document.head.appendChild(style);
    document.body.appendChild(loader);
}

function showGlobalLoader() {
    const loader = document.getElementById(LOADER_ID);
    if (loader) {
        loader.classList.remove('done');
        loader.classList.add('show');
        
        // Force reflow
        void loader.offsetWidth;
        
        loader.classList.add('loading');
    }
}

function hideGlobalLoader() {
    const loader = document.getElementById(LOADER_ID);
    if (loader) {
        loader.classList.add('done');
        setTimeout(() => {
            loader.classList.remove('show', 'loading', 'done');
        }, 300);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    createGlobalLoader();
    
    // Support for normal navigation
    window.addEventListener('beforeunload', () => {
        showGlobalLoader();
    });

    window.addEventListener('pageshow', () => {
        hideGlobalLoader();
    });
    
    // Support for AJAX if applicable (e.g., fetch, XMLHTTPRequest)
    const originalFetch = window.fetch;
    window.fetch = async function(...args) {
        showGlobalLoader();
        try {
            const response = await originalFetch.apply(this, args);
            return response;
        } finally {
            hideGlobalLoader();
        }
    };
});
