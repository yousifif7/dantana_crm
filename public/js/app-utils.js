/**
 * Dantata UBMS — shared utilities: toasts, API errors, routing, apiFetch
 */
const AppUtils = (function () {
    const PAGE_TO_SLUG = {
        'ai-dashboard': '',
        'oil-production': 'production',
        'food-division': 'food',
        'inventory': 'inventory',
        'procurement': 'procurement',
        'process': 'processes',
        'staff': 'staff',
        'attendance': 'attendance',
        'reports': 'reports',
        'notifications': 'notifications',
        'escalations': 'escalations',
        'audit-logs': 'audit',
    };

    const SLUG_TO_PAGE = Object.fromEntries(
        Object.entries(PAGE_TO_SLUG).map(([page, slug]) => [slug || 'dashboard', page])
    );
    SLUG_TO_PAGE[''] = 'ai-dashboard';

    function ensureToastContainer() {
        if (document.getElementById('toastContainer')) return;
        const el = document.createElement('div');
        el.id = 'toastContainer';
        el.className = 'toast-container';
        el.setAttribute('aria-live', 'polite');
        document.body.appendChild(el);
    }

    function showToast(message, type = 'info', duration = 4500) {
        ensureToastContainer();
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        const icons = { success: '✓', error: '✕', warning: '⚠', info: 'ℹ' };
        toast.innerHTML = `
            <span class="toast-icon">${icons[type] || icons.info}</span>
            <span class="toast-message">${escapeHtml(String(message))}</span>
            <button class="toast-close" type="button" aria-label="Dismiss">&times;</button>
        `;
        container.appendChild(toast);
        requestAnimationFrame(() => toast.classList.add('show'));

        const remove = () => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        };

        toast.querySelector('.toast-close').onclick = remove;
        if (duration > 0) setTimeout(remove, duration);
    }

    function escapeHtml(str) {
        if (!str && str !== 0) return '';
        return String(str).replace(/[&<>"]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[m]));
    }

    function parseApiError(err, fallback = 'Something went wrong') {
        if (!err) return fallback;
        const raw = err.message || err.toString?.() || String(err);

        // Strip "API /path returned 422: " prefix
        const jsonPart = raw.replace(/^API\s+\S+\s+returned\s+\d+:\s*/i, '').trim();

        try {
            const data = JSON.parse(jsonPart);
            if (data.errors && typeof data.errors === 'object') {
                const messages = Object.entries(data.errors)
                    .map(([field, msgs]) => {
                        const list = Array.isArray(msgs) ? msgs : [msgs];
                        return `${field}: ${list.join(', ')}`;
                    });
                return messages.join('\n');
            }
            if (data.message) return data.message;
        } catch (e) {
            // not JSON — use cleaned text if readable
            if (jsonPart && jsonPart.length < 500 && !jsonPart.startsWith('<!')) {
                return jsonPart;
            }
        }

        if (raw.includes('401')) return 'Session expired. Please log in again.';
        if (raw.includes('403')) return 'You do not have permission for this action.';
        if (raw.includes('404')) return 'The requested resource was not found.';
        if (raw.includes('422')) return 'Validation failed. Please check your input.';
        if (raw.includes('500')) return 'Server error. Please try again later.';

        return fallback;
    }

    async function apiFetch(path, opts = {}) {
        const token = window.apiToken || localStorage.getItem('api_token');
        const headers = Object.assign({ Accept: 'application/json' }, opts.headers || {});

        if (token) headers.Authorization = 'Bearer ' + token;

        if (opts.body && typeof opts.body === 'object' && !(opts.body instanceof FormData)) {
            headers['Content-Type'] = 'application/json';
            opts.body = JSON.stringify(opts.body);
        }

        const method = (opts.method || 'GET').toUpperCase();
        if (method !== 'GET') {
            const meta = document.querySelector('meta[name="csrf-token"]');
            if (meta) headers['X-CSRF-TOKEN'] = meta.getAttribute('content');
        }

        const res = await fetch(path, Object.assign({ credentials: 'include', headers }, opts));

        if (!res.ok) {
            const text = await res.text();
            if (res.status === 401) {
                localStorage.removeItem('api_token');
                window.apiToken = null;
            }
            const err = new Error(`API ${path} returned ${res.status}: ${text}`);
            err.status = res.status;
            err.body = text;
            throw err;
        }

        if (res.status === 204) return null;
        const contentType = res.headers.get('content-type') || '';
        if (!contentType.includes('application/json')) return res;

        const json = await res.json();
        return json.data !== undefined ? json.data : json;
    }

    async function handleAction(action, options = {}) {
        const {
            successMessage = 'Action completed successfully',
            errorMessage = 'Action failed',
            showSuccess = true,
            onSuccess,
        } = options;

        try {
            const result = await action();
            if (showSuccess && successMessage) showToast(successMessage, 'success');
            if (onSuccess) await onSuccess(result);
            return result;
        } catch (err) {
            console.error(err);
            showToast(parseApiError(err, errorMessage), 'error');
            throw err;
        }
    }

    function pageToSlug(pageId) {
        const slug = PAGE_TO_SLUG[pageId];
        return slug === undefined ? pageId : slug;
    }

    function slugToPage(slug) {
        if (!slug) return 'ai-dashboard';
        return SLUG_TO_PAGE[slug] || null;
    }

    function updateUrl(pageId, replace = false) {
        const slug = pageToSlug(pageId);
        const path = slug ? `/dashboard/${slug}` : '/dashboard';
        const state = { page: pageId };
        if (replace) {
            history.replaceState(state, '', path);
        } else {
            history.pushState(state, '', path);
        }
    }

    function getPageFromUrl() {
        const path = window.location.pathname.replace(/\/+$/, '');
        const idx = path.lastIndexOf('/dashboard');
        if (idx === -1) return null;

        const rest = path.slice(idx + '/dashboard'.length);
        const slug = rest.startsWith('/') ? rest.slice(1).split('/')[0] : '';
        if (!slug) return 'ai-dashboard';

        return slugToPage(slug);
    }

    function activateNavItem(pageId) {
        document.querySelectorAll('.nav-item').forEach(n => {
            const onclick = n.getAttribute('onclick') || '';
            n.classList.toggle('active', onclick.includes(`'${pageId}'`));
        });
    }

    function navigateToPage(pageId, el, replace = false) {
        document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
        const target = document.getElementById(pageId);
        if (target) target.classList.add('active');

        if (el && el.classList) {
            document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
            el.classList.add('active');
        } else {
            activateNavItem(pageId);
        }

        updateUrl(pageId, replace);
        try { localStorage.setItem('dantata.activePage', pageId); } catch (e) {}
        window.scrollTo({ top: 0, behavior: 'smooth' });

        if (typeof window.onPageNavigate === 'function') {
            window.onPageNavigate(pageId);
        }
    }

    function initRouter() {
        window.addEventListener('popstate', () => {
            const pageId = getPageFromUrl() || localStorage.getItem('dantata.activePage') || 'ai-dashboard';
            navigateToPage(pageId, null, true);
        });
    }

    function resolveInitialPage() {
        const fromUrl = getPageFromUrl();
        if (fromUrl) return fromUrl;

        if (window.ACTIVE_PAGE_ID) return window.ACTIVE_PAGE_ID;

        if (window.INITIAL_PAGE) {
            const fromServer = slugToPage(window.INITIAL_PAGE);
            if (fromServer) return fromServer;
        }

        return localStorage.getItem('dantata.activePage') || 'ai-dashboard';
    }

    function formatDate(value) {
        if (!value) return '—';
        const d = new Date(value);
        if (Number.isNaN(d.getTime())) {
            const part = String(value).split('T')[0];
            return part || '—';
        }
        return d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
    }

    function formatDateTime(value) {
        if (!value) return '—';
        const d = new Date(value);
        if (Number.isNaN(d.getTime())) {
            return String(value).replace('T', ' ').slice(0, 16) || '—';
        }
        const date = d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
        const time = d.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
        return `${date} · ${time}`;
    }

    function formatTime(value) {
        if (!value) return '—';
        if (typeof value === 'string' && /^\d{1,2}:\d{2}/.test(value)) {
            return value.slice(0, 5);
        }
        const d = new Date(value);
        if (Number.isNaN(d.getTime())) return String(value);
        return d.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
    }

    function formatStatus(status) {
        if (!status) return '—';
        return String(status)
            .replace(/_/g, ' ')
            .toLowerCase()
            .replace(/\b\w/g, c => c.toUpperCase());
    }

    function formatCurrency(v) {
        if (v === null || v === undefined || v === '') return '—';
        return '₦' + Number(v).toLocaleString('en-NG', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }

    function statusClassFrom(status) {
        const s = (status || '').toLowerCase().replace(/\s+/g, '_');
        return 'status-' + s;
    }

    return {
        showToast,
        parseApiError,
        apiFetch,
        handleAction,
        navigateToPage,
        updateUrl,
        getPageFromUrl,
        resolveInitialPage,
        initRouter,
        escapeHtml,
        formatDate,
        formatDateTime,
        formatTime,
        formatStatus,
        formatCurrency,
        statusClassFrom,
        PAGE_TO_SLUG,
    };
})();

// Global aliases for inline handlers
window.showToast = AppUtils.showToast.bind(AppUtils);
window.parseApiError = AppUtils.parseApiError.bind(AppUtils);
window.apiFetch = AppUtils.apiFetch.bind(AppUtils);
window.handleAction = AppUtils.handleAction.bind(AppUtils);
window.formatDate = AppUtils.formatDate.bind(AppUtils);
window.formatDateTime = AppUtils.formatDateTime.bind(AppUtils);
window.formatTime = AppUtils.formatTime.bind(AppUtils);
window.formatStatus = AppUtils.formatStatus.bind(AppUtils);
window.formatCurrency = AppUtils.formatCurrency.bind(AppUtils);
window.statusClassFrom = AppUtils.statusClassFrom.bind(AppUtils);
