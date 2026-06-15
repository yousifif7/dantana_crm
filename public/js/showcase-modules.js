/**
 * Dantata Foods — Showcase module loaders & UI helpers
 */
const ShowcaseModules = (function () {
    const PAGE_LOADERS = {
        'notifications': loadNotifications,
        'reports': () => {},
        'attendance': loadAttendance,
        'escalations': loadEscalations,
        'audit-logs': loadAuditLogs,
        'procurement': loadProcurement,
    };

    function showLoading() {
        const el = document.getElementById('loadingOverlay');
        if (el) el.classList.add('active');
    }

    function hideLoading() {
        const el = document.getElementById('loadingOverlay');
        if (el) el.classList.remove('active');
    }

    function statusClass(status) {
        const s = (status || '').toLowerCase().replace(/\s+/g, '_');
        return statusClassFrom ? statusClassFrom(status) : ('status-' + s);
    }

    async function updateNotificationBadge() {
        try {
            const data = await apiFetch('/api/notifications/unread');
            const badge = document.getElementById('notificationBadge');
            const count = data.count || 0;
            if (badge) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.classList.toggle('visible', count > 0);
            }
        } catch (e) { /* silent */ }
    }

    async function loadNotifications() {
        showLoading();
        try {
            const data = await apiFetch('/api/notifications');
            const list = normalizeList(data);
            const container = document.getElementById('notificationsList');
            if (!container) return;

            if (!list.length) {
                container.innerHTML = '<div class="empty-state"><div class="empty-state-icon">🔔</div><p>No notifications yet</p></div>';
                return;
            }

            container.innerHTML = list.map(n => `
                <div class="notification-item ${n.is_read ? '' : 'unread'}" onclick="ShowcaseModules.markRead(${n.id})">
                    <div class="notif-title">${escapeHtml(n.title || n.type || 'Notification')}</div>
                    <div class="notif-msg">${escapeHtml(n.message || '')}</div>
                    <div class="notif-time">${escapeHtml(formatDateTime(n.created_at))}</div>
                </div>
            `).join('');
        } catch (e) {
            showToast(parseApiError(e, 'Failed to load notifications'), 'error');
        } finally {
            hideLoading();
            updateNotificationBadge();
        }
    }

    async function markRead(id) {
        await handleAction(async () => {
            await apiFetch(`/api/notifications/${id}/read`, { method: 'POST' });
            await loadNotifications();
        }, { successMessage: 'Notification marked as read', errorMessage: 'Failed to mark notification' });
    }

    async function markAllRead() {
        await handleAction(async () => {
            await apiFetch('/api/notifications/read-all', { method: 'POST' });
            await loadNotifications();
        }, { successMessage: 'All notifications marked as read', errorMessage: 'Failed to mark notifications' });
    }

    async function loadAttendance() {
        showLoading();
        try {
            const data = await apiFetch('/api/attendance/my-records');
            const list = normalizeList(data);
            const tbody = document.getElementById('attendanceTableBody');
            if (!tbody) return;

            if (!list.length) {
                tbody.innerHTML = '<tr><td colspan="5" class="empty-state">No attendance records yet. Check in to get started.</td></tr>';
                return;
            }

            tbody.innerHTML = list.map(r => `
                <tr>
                    <td class="cell-date">${escapeHtml(formatDate(r.attendance_date))}</td>
                    <td class="cell-date">${escapeHtml(formatTime(r.check_in_time))}</td>
                    <td class="cell-date">${escapeHtml(formatTime(r.check_out_time))}</td>
                    <td><span class="status-badge ${statusClass(r.status)}">${escapeHtml(formatStatus(r.status))}</span></td>
                    <td class="cell-muted">${escapeHtml(r.remarks || '—')}</td>
                </tr>
            `).join('');
        } catch (e) {
            showToast(parseApiError(e, 'Failed to load attendance'), 'error');
        } finally {
            hideLoading();
        }
    }

    async function checkIn() {
        showLoading();
        try {
            await handleAction(async () => {
                await apiFetch('/api/attendance/check-in', { method: 'POST' });
                await loadAttendance();
            }, { successMessage: 'Checked in successfully', errorMessage: 'Check-in failed' });
        } finally {
            hideLoading();
        }
    }

    async function checkOut() {
        showLoading();
        try {
            await handleAction(async () => {
                await apiFetch('/api/attendance/check-out', { method: 'POST' });
                await loadAttendance();
            }, { successMessage: 'Checked out successfully', errorMessage: 'Check-out failed' });
        } finally {
            hideLoading();
        }
    }

    async function loadEscalations() {
        showLoading();
        try {
            const data = await apiFetch('/api/escalations');
            const list = normalizeList(data);
            const tbody = document.getElementById('escalationsTableBody');
            if (!tbody) return;

            if (!list.length) {
                tbody.innerHTML = '<tr><td colspan="5" class="empty-state">No escalations — all clear!</td></tr>';
                return;
            }

            tbody.innerHTML = list.map(e => {
                const from = e.from_user ? `${e.from_user.first_name || ''} ${e.from_user.last_name || ''}`.trim() : '—';
                const actions = e.status === 'pending'
                    ? `<button class="btn-primary btn-sm" onclick="ShowcaseModules.resolveEscalation(${e.id})">Resolve</button>`
                    : '—';
                return `<tr>
                    <td>${escapeHtml(e.escalatable_type || '')} #${e.escalatable_id || ''}</td>
                    <td>${escapeHtml(e.reason || '')}</td>
                    <td>${escapeHtml(from)}</td>
                    <td><span class="status-badge ${statusClass(e.status)}">${escapeHtml(formatStatus(e.status))}</span></td>
                    <td class="action-btns">${actions}</td>
                </tr>`;
            }).join('');
        } catch (e) {
            showToast(parseApiError(e, 'Failed to load escalations'), 'error');
        } finally {
            hideLoading();
        }
    }

    async function resolveEscalation(id) {
        if (!confirm('Mark this escalation as resolved?')) return;
        showLoading();
        try {
            await handleAction(async () => {
                await apiFetch(`/api/escalations/${id}/resolve`, { method: 'POST' });
                await loadEscalations();
            }, { successMessage: 'Escalation resolved', errorMessage: 'Failed to resolve escalation' });
        } finally {
            hideLoading();
        }
    }

    async function loadAuditLogs() {
        showLoading();
        try {
            const module = document.getElementById('auditModuleFilter')?.value || '';
            let path = '/api/audit-logs';
            if (module) path += `?module=${encodeURIComponent(module)}`;
            const data = await apiFetch(path);
            const list = normalizeList(data);
            const tbody = document.getElementById('auditTableBody');
            if (!tbody) return;

            if (!list.length) {
                tbody.innerHTML = '<tr><td colspan="5" class="empty-state">No audit logs found</td></tr>';
                return;
            }

            tbody.innerHTML = list.map(l => {
                const user = l.user ? `${l.user.first_name || ''} ${l.user.last_name || ''}`.trim() : 'System';
                return `<tr>
                    <td class="cell-date">${escapeHtml(formatDateTime(l.created_at))}</td>
                    <td>${escapeHtml(user)}</td>
                    <td><span class="status-badge status-pending">${escapeHtml(formatStatus(l.module))}</span></td>
                    <td>${escapeHtml(formatStatus(l.action))}</td>
                    <td class="cell-muted">${escapeHtml(l.auditable_type || '')} ${l.auditable_id ? '#' + l.auditable_id : ''}</td>
                </tr>`;
            }).join('');
        } catch (e) {
            showToast(parseApiError(e, 'Failed to load audit logs'), 'error');
        } finally {
            hideLoading();
        }
    }

    async function loadProcurement() {
        showLoading();
        try {
            const [orders, stats] = await Promise.all([
                apiFetch('/api/procurement'),
                apiFetch('/api/procurement/summary/statistics').catch(() => ({})),
            ]);
            const list = normalizeList(orders);

            const pending = document.getElementById('poPending');
            const value = document.getElementById('poTotalValue');
            if (pending) pending.textContent = stats.pending ?? '—';
            if (value) value.textContent = stats.total_value != null ? formatCurrency(stats.total_value) : '—';

            const tbody = document.getElementById('procurementTableBody');
            if (!tbody) return;

            if (!list.length) {
                tbody.innerHTML = '<tr><td colspan="6" class="empty-state">No purchase orders yet</td></tr>';
                return;
            }

            tbody.innerHTML = list.map(o => {
                let actions = '';
                if (o.can_approve) {
                    actions += `<button class="btn-primary btn-sm" onclick="ShowcaseModules.approvePO(${o.id})">Approve</button>`;
                    actions += `<button class="btn-delete btn-sm" onclick="ShowcaseModules.rejectPO(${o.id})">Reject</button>`;
                }
                if (o.status === 'approved') {
                    actions += `<button class="btn-edit btn-sm" onclick="ShowcaseModules.fulfillPO(${o.id})">Fulfill</button>`;
                }
                if (o.can_update) actions += `<button class="btn-secondary btn-sm" onclick="ShowcaseModules.editPO(${o.id})">Edit</button>`;
                if (o.can_delete && o.status !== 'fulfilled') {
                    actions += `<button class="btn-delete btn-sm" onclick="ShowcaseModules.cancelPO(${o.id})">Cancel</button>`;
                }

                return `<tr>
                    <td>${escapeHtml(o.po_number || '')}</td>
                    <td>${escapeHtml(o.vendor_name || '')}</td>
                    <td>${formatCurrency(o.total_amount)}</td>
                    <td><span class="status-badge ${statusClass(o.status)}">${escapeHtml(formatStatus(o.status))}</span></td>
                    <td class="cell-date">${escapeHtml(formatDate(o.expected_delivery_date))}</td>
                    <td class="action-btns">${actions}</td>
                </tr>`;
            }).join('');
        } catch (e) {
            showToast(parseApiError(e, 'Failed to load procurement'), 'error');
        } finally {
            hideLoading();
        }
    }

    function openPOModal() {
        document.getElementById('poForm')?.reset();
        window.currentPOEditId = null;
        const h = document.querySelector('#poModal .modal-header');
        if (h) h.textContent = 'New Purchase Order';
        document.getElementById('poModal')?.classList.add('active');
    }

    async function savePO(e) {
        e.preventDefault();
        const payload = {
            vendor_name: document.getElementById('poVendor').value,
            description: document.getElementById('poDescription').value || null,
            total_amount: Number(document.getElementById('poAmount').value),
            category: document.getElementById('poCategory').value || null,
            expected_delivery_date: document.getElementById('poDelivery').value || null,
            notes: document.getElementById('poNotes').value || null,
            line_items: [{
                name: document.getElementById('poItemName').value || 'General',
                quantity: Number(document.getElementById('poQty').value || 1),
                unit_price: Number(document.getElementById('poUnitPrice').value || document.getElementById('poAmount').value),
            }],
        };

        const isEdit = !!window.currentPOEditId;
        showLoading();
        try {
            await handleAction(async () => {
                if (isEdit) {
                    await apiFetch(`/api/procurement/${window.currentPOEditId}`, { method: 'PUT', body: payload });
                } else {
                    await apiFetch('/api/procurement', { method: 'POST', body: payload });
                }
                window.currentPOEditId = null;
                closeModal('poModal');
                await loadProcurement();
            }, {
                successMessage: isEdit ? 'Purchase order updated' : 'Purchase order created',
                errorMessage: 'Failed to save purchase order',
            });
        } finally {
            hideLoading();
        }
    }

    async function editPO(id) {
        await handleAction(async () => {
            const o = await apiFetch(`/api/procurement/${id}`);
            document.getElementById('poVendor').value = o.vendor_name || '';
            document.getElementById('poDescription').value = o.description || '';
            document.getElementById('poAmount').value = o.total_amount || '';
            document.getElementById('poCategory').value = o.category || '';
            document.getElementById('poDelivery').value = o.expected_delivery_date || '';
            document.getElementById('poNotes').value = o.notes || '';
            const item = (o.line_items && o.line_items[0]) || {};
            document.getElementById('poItemName').value = item.name || '';
            document.getElementById('poQty').value = item.quantity || 1;
            document.getElementById('poUnitPrice').value = item.unit_price || '';
            window.currentPOEditId = id;
            const h = document.querySelector('#poModal .modal-header');
            if (h) h.textContent = 'Edit Purchase Order';
            document.getElementById('poModal')?.classList.add('active');
        }, { successMessage: null, errorMessage: 'Failed to load purchase order' });
    }

    async function approvePO(id) {
        if (!confirm('Approve this purchase order?')) return;
        await handleAction(async () => {
            await apiFetch(`/api/procurement/${id}/approve`, { method: 'POST' });
            await loadProcurement();
        }, { successMessage: 'Purchase order approved', errorMessage: 'Failed to approve purchase order' });
    }

    async function rejectPO(id) {
        if (!confirm('Reject this purchase order?')) return;
        await handleAction(async () => {
            await apiFetch(`/api/procurement/${id}/reject`, { method: 'POST' });
            await loadProcurement();
        }, { successMessage: 'Purchase order rejected', errorMessage: 'Failed to reject purchase order' });
    }

    async function fulfillPO(id) {
        if (!confirm('Mark as fulfilled?')) return;
        await handleAction(async () => {
            await apiFetch(`/api/procurement/${id}/fulfill`, { method: 'POST' });
            await loadProcurement();
        }, { successMessage: 'Purchase order fulfilled', errorMessage: 'Failed to fulfill purchase order' });
    }

    async function cancelPO(id) {
        if (!confirm('Cancel this purchase order?')) return;
        await handleAction(async () => {
            await apiFetch(`/api/procurement/${id}`, { method: 'DELETE' });
            await loadProcurement();
        }, { successMessage: 'Purchase order cancelled', errorMessage: 'Failed to cancel purchase order' });
    }

    async function previewReport(type) {
        const start = document.getElementById('reportStartDate')?.value;
        const end = document.getElementById('reportEndDate')?.value;
        if (!start || !end) {
            showToast('Select start and end dates', 'warning');
            return;
        }
        showLoading();
        try {
            await handleAction(async () => {
                const data = await apiFetch(`/api/reports/${type}?start_date=${start}&end_date=${end}`);
                const el = document.getElementById('reportPreview');
                if (!el) return;
                el.style.display = 'block';
                if (type === 'financial') {
                    el.innerHTML = `
                        <h3 style="margin-bottom:12px;">Financial Summary</h3>
                        <div class="metrics-grid" style="grid-template-columns:repeat(4,1fr);">
                            <div class="metric-card"><div class="metric-label">Revenue</div><div class="metric-value">${formatCurrency(data.revenue)}</div></div>
                            <div class="metric-card"><div class="metric-label">Expenses</div><div class="metric-value">${formatCurrency(data.expenses)}</div></div>
                            <div class="metric-card"><div class="metric-label">Net Profit</div><div class="metric-value">${formatCurrency(data.net_profit)}</div></div>
                            <div class="metric-card"><div class="metric-label">Margin</div><div class="metric-value">${data.profit_margin}%</div></div>
                        </div>`;
                } else if (type === 'production') {
                    const s = data.summary || {};
                    el.innerHTML = `
                        <h3 style="margin-bottom:12px;">Production Summary</h3>
                        <div class="metrics-grid" style="grid-template-columns:repeat(4,1fr);">
                            <div class="metric-card"><div class="metric-label">Batches</div><div class="metric-value">${s.total_batches || 0}</div></div>
                            <div class="metric-card"><div class="metric-label">Quantity</div><div class="metric-value">${Number(s.total_quantity || 0).toLocaleString()}L</div></div>
                            <div class="metric-card"><div class="metric-label">Efficiency</div><div class="metric-value">${Math.round(s.avg_efficiency || 0)}%</div></div>
                            <div class="metric-card"><div class="metric-label">Downtime</div><div class="metric-value">${s.total_downtime || 0}h</div></div>
                        </div>`;
                } else if (type === 'inventory') {
                    el.innerHTML = `
                        <h3 style="margin-bottom:12px;">Inventory Summary</h3>
                        <p>Total items: <strong>${data.total_items}</strong> · Total value: <strong>${formatCurrency(data.total_value || 0)}</strong></p>
                        <p>Low stock alerts: <strong>${(data.low_stock_items || []).length}</strong></p>`;
                }
            }, { successMessage: 'Report loaded', errorMessage: 'Failed to load report' });
        } finally {
            hideLoading();
        }
    }

    async function exportReport(type) {
        const start = document.getElementById('reportStartDate')?.value;
        const end = document.getElementById('reportEndDate')?.value;
        if (!start || !end) {
            showToast('Select start and end dates', 'warning');
            return;
        }

        const token = window.apiToken || localStorage.getItem('api_token');
        const url = `/api/reports/export/${type}?start_date=${start}&end_date=${end}`;
        showLoading();
        try {
            const res = await fetch(url, {
                headers: { Authorization: 'Bearer ' + token, Accept: 'application/pdf' },
            });
            if (!res.ok) {
                const text = await res.text();
                throw new Error(text);
            }
            const blob = await res.blob();
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = `${type}-report-${start}-to-${end}.pdf`;
            a.click();
            showToast('PDF exported successfully', 'success');
        } catch (e) {
            showToast(parseApiError(e, 'PDF export failed'), 'error');
        } finally {
            hideLoading();
        }
    }

    function onPageShow(pageId) {
        const loader = PAGE_LOADERS[pageId];
        if (loader) loader();
    }

    function init() {
        updateNotificationBadge();

        const poForm = document.getElementById('poForm');
        if (poForm) poForm.onsubmit = savePO;

        const today = new Date();
        const monthAgo = new Date(today);
        monthAgo.setMonth(monthAgo.getMonth() - 1);
        const startEl = document.getElementById('reportStartDate');
        const endEl = document.getElementById('reportEndDate');
        if (startEl && !startEl.value) startEl.value = monthAgo.toISOString().split('T')[0];
        if (endEl && !endEl.value) endEl.value = today.toISOString().split('T')[0];
    }

    return {
        onPageShow, init, loadNotifications, markRead, markAllRead,
        loadAttendance, checkIn, checkOut, loadEscalations, resolveEscalation,
        loadAuditLogs, loadProcurement, openPOModal, savePO, editPO,
        approvePO, rejectPO, fulfillPO, cancelPO, previewReport, exportReport,
        updateNotificationBadge, showLoading, hideLoading,
    };
})();

document.addEventListener('DOMContentLoaded', function () {
    ShowcaseModules.init();
});
