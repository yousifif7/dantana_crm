<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dantata Foods - Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/showcase.css') }}">
    <script>window.INITIAL_PAGE = @json($page ?? null); window.ACTIVE_PAGE_ID = @json($activePageId ?? 'ai-dashboard');</script>
    <script src="{{ asset('js/app-utils.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .sidebar {
            width: 200px;
            background-color: #1e3a2e;
            color: white;
            padding: 30px 0;
            display: flex;
            flex-direction: column;
        }

        .logo-section {
            padding: 0 20px 30px;
            text-align: center;
        }

        .logo-icon {
            width: 60px;
            height: 60px;
            border: 2px solid #d4af37;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
        }

        .logo-icon svg {
            width: 35px;
            height: 35px;
            fill: #d4af37;
        }

        .logo-text {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .logo-subtext {
            font-size: 12px;
            letter-spacing: 3px;
            margin-top: 5px;
        }

        .nav-menu {
            list-style: none;
        }

        .nav-item {
            padding: 15px 25px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: background-color 0.2s;
            color: rgba(255, 255, 255, 0.7);
        }

        .nav-item:hover, .nav-item.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .nav-item.active {
            border-left: 3px solid #4a9d7e;
        }

        .main-content {
            flex: 1;
            overflow-y: auto;
            padding: 40px 10px;
        }

        .page {
            display: none;
        }

        .page.active {
            display: block;
        }

        .page-title {
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 30px;
            color: #1e3a2e;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
            margin-bottom: 40px;
        }

        .metric-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .metric-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        .metric-value {
            font-size: 20px;
            font-weight: 700;
            color: #1e3a2e;
        }

        .charts-section {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 25px;
            margin-bottom: 40px;
        }

        .chart-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .chart-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #1e3a2e;
        }

        .transactions-table {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 15px;
            color: #1e3a2e;
            font-weight: 600;
            border-bottom: 1px solid #e0e0e0;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            color: #333;
        }

        .inventory-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
            margin-bottom: 40px;
        }

        .stock-table-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-in-stock {
            background-color: #d4edda;
            color: #155724;
        }

        .status-on-order {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-low-stock {
            background-color: #f8d7da;
            color: #721c24;
        }

        .efficiency-circle {
            width: 180px;
            height: 180px;
            margin: 30px auto;
            position: relative;
        }

        .efficiency-circle svg {
            transform: rotate(-90deg);
        }

        .efficiency-number {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 42px;
            font-weight: 700;
            color: #2d7a5f;
        }

        .process-list {
            background: white;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .process-panel .table-scroll {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .process-table {
            width: 100%;
            min-width: 720px;
            border-collapse: collapse;
        }

        .process-table th,
        .process-table td {
            padding: 14px 12px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }

        .process-table th {
            font-weight: 600;
            color: #1e3a2e;
            border-bottom: 2px solid #e0e0e0;
            white-space: nowrap;
        }

        .process-table .actions-col {
            min-width: 280px;
        }

        .process-table .action-btns {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .process-item {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 120px;
            padding: 20px 15px;
            border-bottom: 1px solid #f0f0f0;
            align-items: center;
        }

        .process-item:last-child {
            border-bottom: none;
        }

        .process-header {
            font-weight: 600;
            color: #1e3a2e;
            padding-bottom: 15px;
            border-bottom: 2px solid #e0e0e0;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: inline-flex;
            margin-right: 10px;
            vertical-align: middle;
        }

        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-progress {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-pending {
            background-color: #e7e7e7;
            color: #666;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .action-button {
            background: white;
            padding: 30px;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 16px;
            font-weight: 500;
        }

        .action-button:hover {
            border-color: #4a9d7e;
            background-color: #f8fffe;
        }

        /* Buttons, tables, forms — styled in showcase.css */

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .action-btns {
            display: flex;
            gap: 10px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-header {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #1e3a2e;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }
    </style>
    <script>
        // Defensive wiring for Create Division button (kept outside CSS)
        try {
            document.addEventListener('DOMContentLoaded', function() {
                const btn = document.getElementById('btnCreateDivision');
                if (btn) btn.addEventListener('click', function(e){ e.preventDefault(); if (typeof openDepartmentModal === 'function') openDepartmentModal(); else {
                    const modal = document.getElementById('departmentCreateModal'); if (modal) modal.classList.add('active');
                }});
            });
        } catch (e) { console.error('Failed to attach Create Division handler', e); }
    </script>
</head>
<body>
    <aside class="sidebar">
        <div class="logo-section">
            <div class="logo-icon">
                <svg viewBox="0 0 24 24">
                    <path d="M12 2C11.5 2 11 2.19 10.59 2.59L2.59 10.59C1.8 11.37 1.8 12.63 2.59 13.41L10.59 21.41C11.37 22.2 12.63 22.2 13.41 21.41L21.41 13.41C22.2 12.63 22.2 11.37 21.41 10.59L13.41 2.59C13 2.19 12.5 2 12 2M12 4L20 12L12 20L4 12L12 4M9 9V15H11V13H13C13.55 13 14 12.55 14 12V10C14 9.45 13.55 9 13 9H9M11 11H12V11.5H11V11Z"/>
                </svg>
            </div>
            <div class="logo-text">DANTATA</div>
            <div class="logo-subtext">FOODS</div>
        </div>
        <ul class="nav-menu">
            <li class="nav-section-label">Operations</li>
            <li class="nav-item active" onclick="navigateTo('ai-dashboard', this)">
                <span>📊</span> <span>Dashboard</span>
            </li>
            <li class="nav-item" onclick="navigateTo('oil-production', this)">
                <span>💧</span> <span>Oil Production</span>
            </li>
            <li class="nav-item" onclick="navigateTo('food-division', this)">
                <span>🌾</span> <span>Food Division</span>
            </li>
            <li class="nav-item" onclick="navigateTo('inventory', this)">
                <span>📦</span> <span>Inventory</span>
            </li>
            <li class="nav-item" onclick="navigateTo('procurement', this)">
                <span>🛒</span> <span>Procurement</span>
            </li>
            <li class="nav-item" onclick="navigateTo('process', this)">
                <span>⚙️</span> <span>Processes</span>
            </li>
            <li class="nav-section-label">People</li>
            <li class="nav-item" onclick="navigateTo('staff', this)">
                <span>👥</span> <span>Staff</span>
            </li>
            <li class="nav-item" onclick="navigateTo('attendance', this)">
                <span>🕐</span> <span>Attendance</span>
            </li>
            <li class="nav-section-label">Admin</li>
            <li class="nav-item" onclick="navigateTo('reports', this)">
                <span>📋</span> <span>Reports</span>
            </li>
            <li class="nav-item" onclick="navigateTo('notifications', this)">
                <span>🔔</span> <span>Notifications</span>
            </li>
            <li class="nav-item" onclick="navigateTo('escalations', this)">
                <span>⚠️</span> <span>Escalations</span>
            </li>
            <li class="nav-item" onclick="navigateTo('audit-logs', this)">
                <span>📜</span> <span>Audit Logs</span>
            </li>
        </ul>
    </aside>

    <div class="top-bar">
        <div class="top-bar-title">Dantata Foods · Unified Business Management System</div>
        <button class="notification-btn" onclick="navigateTo('notifications', null)" title="Notifications">
            🔔
            <span id="notificationBadge" class="notification-badge">0</span>
        </button>
        <div id="loggedInArea" class="user-chip" style="display:none;">
            <div class="user-avatar" id="userAvatar">U</div>
            <span id="currentUserName" style="font-weight:600;font-size:14px;"></span>
            <button id="logoutBtn" class="btn-secondary btn-xs">Logout</button>
        </div>
        <button id="loginBtn" class="btn-primary" style="display:none;">Login</button>
    </div>

    <div id="loadingOverlay" class="loading-overlay"><div class="spinner"></div></div>

    <main class="main-content">
        <!-- Legacy user area hidden by showcase.css -->
        <div id="userArea" style="display:none;"></div>

        <!-- Overview Page -->
        <div id="overview" class="page">
            <div class="section-header">
                <h1 class="page-title" style="margin: 0;">Overview</h1>
            </div>

            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-label">Total Employees</div>
                    <div class="metric-value" id="overviewTotalUsers">{{ $metrics['user_count'] ?? 0 }}</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Departments</div>
                    <div class="metric-value" id="overviewDepartments">{{ $metrics['departments_count'] ?? 0 }}</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Inventory Items</div>
                    <div class="metric-value" id="overviewInventoryItems">{{ $metrics['inventory_items'] ?? 0 }}</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Production Records</div>
                    <div class="metric-value" id="overviewProduction">{{ $metrics['production_records'] ?? 0 }}</div>
                </div>
            </div>

                <!-- Department/Division Modal removed from inside the page to be rendered globally later -->

            <div class="charts-section">
                <div class="chart-card">
                    <div class="chart-title">Quick View</div>
                    <p style="color:#666;">Summary of core counts. Click sections in the sidebar to view details.</p>
                </div>
                <div class="chart-card">
                    <div class="chart-title">Recent Activity</div>
                    <div id="overviewRecentActivity">
                        <p style="color:#666;">Recent transactions, production and process activity will appear here after loading.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Dashboard Page -->
        <div id="ai-dashboard" class="page{{ ($activePageId ?? 'ai-dashboard') === 'ai-dashboard' ? ' active' : '' }}">
            <div class="section-header">
                <h1 class="page-title" style="margin: 0;">Dashboard</h1>
                <button class="btn-primary" onclick="openTransactionModal()">Add Transaction</button>
            </div>
            <p class="page-subtitle">Financial overview, revenue tracking, and transaction management</p>
            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-label">Total Revenue</div>
                    <div class="metric-value" id="totalRevenue">—</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Total Expenses</div>
                    <div class="metric-value" id="totalExpenses">—</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Net Profit</div>
                    <div class="metric-value" id="netProfit">—</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Profit Margin</div>
                    <div class="metric-value" id="profitMargin">—</div>
                </div>
            </div>

            <div class="charts-section">
                <div class="chart-card">
                    <div class="chart-title">Revenue & Expenses</div>
                    <canvas id="revenueChart"></canvas>
                </div>
                <div class="chart-card">
                    <div class="chart-title">Expense Breakdown</div>
                    <canvas id="expenseChart"></canvas>
                </div>
            </div>

            <div class="transactions-table">
                <div class="chart-title">Recent Transactions</div>
                <table>
                    <thead>
                        <tr>
                            <th>Client Payment</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="transactionsTableBody">
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Inventory Page -->
        <div id="inventory" class="page{{ ($activePageId ?? '') === 'inventory' ? ' active' : '' }}">
            <div class="section-header">
                <h1 class="page-title" style="margin: 0;">Inventory</h1>
                <button class="btn-primary" onclick="openInventoryModal()">Add Item</button>
            </div>
            
            <div class="inventory-grid">
                <div class="metric-card">
                    <div class="metric-label">Total Inventory</div>
                    <div class="metric-value" id="totalInventory">—</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Current Stock</div>
                    <div class="metric-value" id="currentStock">12,000</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Reorder Level</div>
                    <div class="metric-value">1,500</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Inventory Turnover</div>
                    <div class="metric-value">4.5</div>
                </div>
            </div>

            <div class="stock-table-section">
                <div class="chart-card">
                    <div class="chart-title">Stock Details</div>
                    <table>
                        <thead>
                            <tr>
                    <th>Item</th>
                    <th>Category</th>
                    <th>Department</th>
                    <th>Stock</th>
                    <th>Reorder L</th>
                    <th>Status</th>
                    <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="inventoryTableBody">
                        </tbody>
                    </table>
                </div>
                <div class="chart-card">
                    <div class="chart-title">Analysis</div>
                    <canvas id="inventoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Staff Management Page -->
    <div id="staff" class="page staff-page{{ ($activePageId ?? '') === 'staff' ? ' active' : '' }}">
            <div class="section-header">
                <h1 class="page-title" style="margin: 0;">Staff Management</h1>
                <div class="staff-toolbar">
                    <button class="btn-primary" onclick="openStaffModal()">Add Employee</button>
                    <button id="btnCreateDivision" class="btn-secondary" onclick="openDepartmentModal()">Create Division</button>
                    <button class="btn-secondary" onclick="openRolesModal()">Manage Roles & Permissions</button>
                </div>
                <script>
                    // Attach a robust click handler immediately after the buttons render.
                    // This ensures the Create Division button works even if inline onclick
                    // handlers are blocked or JS earlier in the file errors out.
                    (function(){
                        try {
                            const btn = document.getElementById('btnCreateDivision');
                            if (!btn) return;
                            // remove any duplicate handlers to avoid double calls
                            btn.replaceWith(btn.cloneNode(true));
                            const newBtn = document.getElementById('btnCreateDivision');
                            newBtn.addEventListener('click', function(e){
                                e.preventDefault();
                                try {
                                    if (typeof window.openDepartmentModal === 'function') {
                                        console.debug('Create Division clicked - invoking openDepartmentModal');
                                        window.openDepartmentModal();
                                    } else {
                                        console.warn('openDepartmentModal not defined, opening modal directly');
                                        const modal = document.getElementById('departmentCreateModal');
                                        if (modal) modal.classList.add('active');
                                    }
                                } catch (err) {
                                    console.error('Error opening department modal', err);
                                    const modal = document.getElementById('departmentCreateModal');
                                    if (modal) modal.classList.add('active');
                                }
                            });
                        } catch (e) { console.error('Failed to attach create-division handler', e); }
                    })();
                </script>
            </div>
            
            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-label">Total employees</div>
                    <div class="metric-value" id="totalEmployees">{{ $metrics['user_count'] ?? 0 }}</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Monthly Attendance</div>
                    <div class="metric-value">85%</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Open Positions</div>
                    <div class="metric-value">4</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Retention</div>
                    <div class="metric-value">92%</div>
                </div>
            </div>

            <div class="staff-details-grid">
                <div class="chart-card staff-summary-card">
                    <div class="chart-title">Staff Details</div>
                    <div class="table-scroll">
                        <table class="staff-summary-table">
                            <thead>
                                <tr>
                                    <th>Department</th>
                                    <th>Employees</th>
                                    <th>Avg Age</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="staffTableBody">
                                @foreach($departments ?? [] as $department)
                                    <tr>
                                        <td>{{ $department->name }}</td>
                                        <td>{{ $department->users_count ?? $department->users()->count() }}</td>
                                        <td>{{ round($department->users()->avg('age') ?? 0, 1) }}</td>
                                        <td>
                                            <button type="button" class="btn-edit btn-sm" onclick="viewDepartment({{ $department->id }})">View</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="chart-card staff-departments-card">
                    <div class="chart-title">Departments</div>
                    <div class="table-scroll">
                        <table class="staff-departments-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Contact Email</th>
                                    <th>Phone</th>
                                    <th>City</th>
                                    <th>Country</th>
                                    <th>Employees</th>
                                    <th class="actions-col">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="departmentsDetailBody">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="chart-card staff-chart-card">
                    <div class="chart-title">Absenteeism</div>
                    <canvas id="absenteeismChart"></canvas>
                </div>
            </div>

            <div class="chart-card staff-employees-card">
                <div class="chart-title">All Employees</div>
                <div class="table-scroll">
                    <table class="staff-employees-table">
                        <thead>
                            <tr>
                                <th>Employee ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th class="actions-col">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="employeesTableBody">
                            <tr><td colspan="7" class="empty-state">Loading employees…</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @php
            // Try to find a department that represents Food Division (case-insensitive match)
            $foodDept = null;
            foreach($departments ?? [] as $d) {
                if (isset($d->name) && stripos($d->name, 'food') !== false) { $foodDept = $d; break; }
            }
        @endphp

        <div id="food-division" class="page{{ ($activePageId ?? '') === 'food-division' ? ' active' : '' }}">
            <div class="section-header">
                <h1 class="page-title" style="margin: 0;">Food Division</h1>
                <div style="display:flex;gap:8px;align-items:center;">
                    <button class="btn-primary" onclick="openInventoryModal('food')">Add Food Item</button>
                    <button class="btn-secondary" onclick="openStaffModal()">Add Employee</button>
                </div>
            </div>

            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-label">Total Food Items</div>
                    <div class="metric-value" id="foodTotalItems">0</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Total Stock</div>
                    <div class="metric-value" id="foodTotalStock">0</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Below Reorder</div>
                    <div class="metric-value" id="foodBelowReorder">0</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Distinct Departments</div>
                    <div class="metric-value" id="foodDeptCount">0</div>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-title">Food Inventory</div>
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Department</th>
                            <th>Stock</th>
                            <th>Unit</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="foodInventoryBody">
                        <!-- populated by JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Oil Production Page -->
        <div id="oil-production" class="page{{ ($activePageId ?? '') === 'oil-production' ? ' active' : '' }}">
            <div class="section-header">
                <h1 class="page-title" style="margin: 0;">Oil Production</h1>
                <button class="btn-primary" onclick="openProductionModal()">Add Production</button>
            </div>
            <p style="color: #666; margin-bottom: 30px;">Overview of oil production</p>
            
            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-label">Total Production</div>
                    <div class="metric-value">—</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Production Today</div>
                    <div class="metric-value">—</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Downtime</div>
                    <div class="metric-value">—</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Efficiency</div>
                    <div class="metric-value">—</div>
                </div>
            </div>

            <div class="charts-section">
                <div class="chart-card">
                    <div class="chart-title">Production</div>
                    <canvas id="productionChart"></canvas>
                </div>
                <div class="chart-card">
                    <div class="chart-title">Process Efficiency</div>
                    <div class="efficiency-circle">
                        <svg width="180" height="180">
                            <circle cx="90" cy="90" r="70" fill="none" stroke="#e0e0e0" stroke-width="12"/>
                            <circle cx="90" cy="90" r="70" fill="none" stroke="#3a8d6b" stroke-width="12" 
                                    stroke-dasharray="440" stroke-dashoffset="35" stroke-linecap="round"/>
                        </svg>
                        <div class="efficiency-number">92%</div>
                    </div>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-title">Production Records</div>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Quantity (L)</th>
                            <th>Efficiency</th>
                            <th>Downtime</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productionTableBody">
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Process Management Page -->
        <div id="process" class="page{{ ($activePageId ?? '') === 'process' ? ' active' : '' }}">
            <div class="section-header">
                <h1 class="page-title" style="margin: 0;">Process Management</h1>
                <button class="btn-primary" onclick="openProcessModal()">New Process</button>
            </div>
            
            <div class="data-panel process-panel">
                <div class="chart-title" style="margin-bottom: 20px;">All Processes</div>
                <div class="table-scroll">
                    <table class="process-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Due Date</th>
                                <th class="actions-col">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="processTableBody">
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="chart-title">Quick Actions</div>
            <div class="quick-actions">
                <div class="action-button" onclick="openProcessModal('production')">
                    <span style="font-size: 24px;">➕</span>
                    <span>Add New Production Batch</span>
                </div>
                <div class="action-button" onclick="openProcessModal('delivery')">
                    <span style="font-size: 24px;">🚚</span>
                    <span>Create Delivery Order</span>
                </div>
            </div>
        </div>

        <!-- Procurement Page -->
        <div id="procurement" class="page{{ ($activePageId ?? '') === 'procurement' ? ' active' : '' }}">
            <div class="section-header">
                <h1 class="page-title" style="margin:0;">Procurement</h1>
                <button class="btn-primary" onclick="ShowcaseModules.openPOModal()">New Purchase Order</button>
            </div>
            <p class="page-subtitle">Manage vendor orders, approvals, and fulfillment</p>
            <div class="metrics-grid">
                <div class="metric-card"><div class="metric-label">Pending Approval</div><div class="metric-value" id="poPending">—</div></div>
                <div class="metric-card"><div class="metric-label">Approved Value</div><div class="metric-value" id="poTotalValue">—</div></div>
            </div>
            <div class="data-panel">
                <table>
                    <thead><tr><th>PO #</th><th>Vendor</th><th>Amount</th><th>Status</th><th>Delivery</th><th>Actions</th></tr></thead>
                    <tbody id="procurementTableBody"></tbody>
                </table>
            </div>
        </div>

        <!-- Attendance Page -->
        <div id="attendance" class="page{{ ($activePageId ?? '') === 'attendance' ? ' active' : '' }}">
            <div class="section-header"><h1 class="page-title" style="margin:0;">Attendance</h1></div>
            <p class="page-subtitle">Track daily check-ins and attendance records</p>
            <div class="check-in-card">
                <h3>Today's Attendance</h3>
                <p>Check in when you arrive and check out when you leave.</p>
                <div class="attendance-actions">
                    <button class="btn-primary" onclick="ShowcaseModules.checkIn()">Check In</button>
                    <button class="btn-secondary btn-sm check-out-btn" onclick="ShowcaseModules.checkOut()">Check Out</button>
                </div>
            </div>
            <div class="data-panel">
                <div class="chart-title">My Records</div>
                <table>
                    <thead><tr><th>Date</th><th>Check In</th><th>Check Out</th><th>Status</th><th>Remarks</th></tr></thead>
                    <tbody id="attendanceTableBody"></tbody>
                </table>
            </div>
        </div>

        <!-- Reports Page -->
        <div id="reports" class="page{{ ($activePageId ?? '') === 'reports' ? ' active' : '' }}">
            <div class="section-header"><h1 class="page-title" style="margin:0;">Reports</h1></div>
            <p class="page-subtitle">Generate and export business intelligence reports</p>
            <div class="filter-bar">
                <label>From <input type="date" id="reportStartDate"></label>
                <label>To <input type="date" id="reportEndDate"></label>
            </div>
            <div class="metrics-grid" style="grid-template-columns:repeat(3,1fr);">
                <div class="report-card">
                    <h3>📊 Financial Report</h3>
                    <p>Revenue, expenses, profit margin and transaction breakdown.</p>
                    <div class="btn-group">
                        <button class="btn-secondary" onclick="ShowcaseModules.previewReport('financial')">Preview</button>
                        <button class="btn-primary" onclick="ShowcaseModules.exportReport('financial')">Export PDF</button>
                    </div>
                </div>
                <div class="report-card">
                    <h3>🏭 Production Report</h3>
                    <p>Batch output, efficiency metrics and downtime analysis.</p>
                    <div class="btn-group">
                        <button class="btn-secondary" onclick="ShowcaseModules.previewReport('production')">Preview</button>
                        <button class="btn-primary" onclick="ShowcaseModules.exportReport('production')">Export PDF</button>
                    </div>
                </div>
                <div class="report-card">
                    <h3>📦 Inventory Report</h3>
                    <p>Stock levels, valuation and low-stock alerts.</p>
                    <div class="btn-group">
                        <button class="btn-secondary" onclick="ShowcaseModules.previewReport('inventory')">Preview</button>
                    </div>
                </div>
            </div>
            <div id="reportPreview" class="data-panel" style="display:none;margin-top:24px;"></div>
        </div>

        <!-- Notifications Page -->
        <div id="notifications" class="page{{ ($activePageId ?? '') === 'notifications' ? ' active' : '' }}">
            <div class="section-header">
                <h1 class="page-title" style="margin:0;">Notifications</h1>
                <button class="btn-secondary" onclick="ShowcaseModules.markAllRead()">Mark All Read</button>
            </div>
            <p class="page-subtitle">System alerts, approvals, and activity updates</p>
            <div class="data-panel" id="notificationsList" style="padding:0;"></div>
        </div>

        <!-- Escalations Page -->
        <div id="escalations" class="page{{ ($activePageId ?? '') === 'escalations' ? ' active' : '' }}">
            <div class="section-header"><h1 class="page-title" style="margin:0;">Escalations</h1></div>
            <p class="page-subtitle">Pending items requiring management attention</p>
            <div class="data-panel">
                <table>
                    <thead><tr><th>Item</th><th>Reason</th><th>From</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody id="escalationsTableBody"></tbody>
                </table>
            </div>
        </div>

        <!-- Audit Logs Page -->
        <div id="audit-logs" class="page{{ ($activePageId ?? '') === 'audit-logs' ? ' active' : '' }}">
            <div class="section-header"><h1 class="page-title" style="margin:0;">Audit Logs</h1></div>
            <p class="page-subtitle">Complete activity trail across all modules</p>
            <div class="filter-bar">
                <select id="auditModuleFilter" onchange="ShowcaseModules.loadAuditLogs()">
                    <option value="">All Modules</option>
                    <option value="finance">Finance</option>
                    <option value="hr">HR</option>
                    <option value="inventory">Inventory</option>
                    <option value="production">Production</option>
                    <option value="procurement">Procurement</option>
                    <option value="process">Process</option>
                    <option value="attendance">Attendance</option>
                </select>
                <button class="btn-secondary" onclick="ShowcaseModules.loadAuditLogs()">Refresh</button>
            </div>
            <div class="data-panel">
                <table>
                    <thead><tr><th>Timestamp</th><th>User</th><th>Module</th><th>Action</th><th>Target</th></tr></thead>
                    <tbody id="auditTableBody"></tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Transaction Modal -->
    <div id="transactionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Add Transaction</div>
            <form id="transactionForm" onsubmit="saveTransaction(event)">
                <div class="form-group">
                    <label>Type</label>
                    <select id="transactionType" required>
                        <option value="">Select type</option>
                        <option value="revenue">Revenue</option>
                        <option value="expense">Expense</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <input type="text" id="transactionDesc" required>
                </div>
                <div class="form-group">
                    <label>Amount (₦)</label>
                    <input type="number" step="0.01" id="transactionAmount" required>
                </div>
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" id="transactionDate" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <input type="text" id="transactionCategory">
                </div>
                <div class="form-group">
                    <label>Client / Payee</label>
                    <input type="text" id="transactionClient">
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    <textarea id="transactionNotes" rows="3"></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal('transactionModal')">Cancel</button>
                    <button type="submit" class="btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Inventory Modal -->
    <div id="inventoryModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Add Inventory Item</div>
            <form id="inventoryForm" onsubmit="saveInventory(event)">
                <div class="form-group">
                    <label>Item Name</label>
                    <input type="text" id="inventoryItem" required>
                </div>
                <div class="form-group">
                    <label>Stock Quantity</label>
                    <input type="number" id="inventoryStock" required>
                </div>
                <div class="form-group">
                    <label>Unit of Measure</label>
                    <input type="text" id="inventoryUnit" required placeholder="e.g., pcs, kg, liters">
                </div>
                <div class="form-group">
                    <label>Unit Price</label>
                    <input type="number" step="0.01" id="inventoryPrice">
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select id="inventoryCategory">
                        <option value="">-- Select --</option>
                        <option value="food">Food</option>
                        <option value="raw_material">Raw Material</option>
                        <option value="consumable">Consumable</option>
                        <option value="equipment">Equipment</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Department</label>
                    <select id="inventoryDepartment">
                        <option value="">-- None --</option>
                        @foreach(($departments ?? collect()) as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea id="inventoryDescription" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label>Maximum Level</label>
                    <input type="number" id="inventoryMax">
                </div>
                <div class="form-group">
                    <label>Reorder Level</label>
                    <input type="number" id="inventoryReorder" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select id="inventoryStatus" required>
                        <option value="in_stock">In Stock</option>
                        <option value="on_order">On Order</option>
                        <option value="low_stock">Low Stock</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal('inventoryModal')">Cancel</button>
                    <button type="submit" class="btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Adjust Stock Modal -->
    <div id="adjustStockModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Adjust Stock</div>
            <p style="margin-bottom:16px;color:#5c6b63;">Item: <strong id="adjustStockItemName"></strong> · Current: <strong id="adjustStockCurrent"></strong></p>
            <form id="adjustStockForm" onsubmit="saveAdjustStock(event)">
                <input type="hidden" id="adjustStockItemId">
                <div class="form-group">
                    <label>Adjustment Type</label>
                    <select id="adjustStockType" required>
                        <option value="in">Stock In (+)</option>
                        <option value="out">Stock Out (−)</option>
                        <option value="adjustment">Set Quantity</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Quantity</label>
                    <input type="number" id="adjustStockQty" min="0" required>
                </div>
                <div class="form-group">
                    <label>Reason</label>
                    <input type="text" id="adjustStockReason" required placeholder="e.g. Received shipment, Used in production">
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal('adjustStockModal')">Cancel</button>
                    <button type="submit" class="btn-primary">Apply Adjustment</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Staff Modal (Add Employee) -->
    <div id="staffModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Add Employee</div>
            @if(session('success'))
                <div style="padding:10px;background:#d4edda;color:#155724;margin-bottom:10px;border-radius:6px;">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div style="padding:10px;background:#f8d7da;color:#721c24;margin-bottom:10px;border-radius:6px;">
                    <ul style="margin:0;padding-left:18px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form id="staffForm" method="POST" action="{{ route('dashboard.users.store') }}">
                @csrf
                <div class="form-group">
                    <label>Employee ID <span style="font-weight:400;color:#888;">(optional — auto-generated)</span></label>
                    <input type="text" name="employee_id" placeholder="Leave blank to auto-generate">
                </div>
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" required>
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Password <span id="staffPasswordHint" style="font-weight:400;color:#888;"></span></label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role_id" required>
                        <option value="">Select role</option>
                        @foreach($roles ?? [] as $role)
                            <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Department</label>
                    <select name="department_id">
                        <option value="">None</option>
                        @foreach($departments ?? [] as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal('staffModal')">Cancel</button>
                    <button type="submit" class="btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Department/Division Modal (global, same placement as Staff Modal) -->
    <div id="departmentCreateModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Create Division</div>
            <form id="departmentForm" method="POST" action="{{ route('dashboard.departments.store') }}">
                @csrf
                <div class="form-group">
                    <label>Division Name</label>
                    <input type="text" id="deptName" required>
                </div>
                <div class="form-group">
                    <label>Code</label>
                    <input type="text" id="deptCode" required maxlength="10">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea id="deptDescription" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea id="deptAddress" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" id="deptPhone">
                </div>
                <div class="form-group">
                    <label>Contact Email</label>
                    <input type="email" id="deptEmail">
                </div>
                <div style="display:flex;gap:8px;">
                    <div class="form-group" style="flex:1;">
                        <label>City</label>
                        <input type="text" id="deptCity">
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>State</label>
                        <input type="text" id="deptState">
                    </div>
                </div>
                <div style="display:flex;gap:8px;">
                    <div class="form-group" style="flex:1;">
                        <label>Postal Code</label>
                        <input type="text" id="deptPostal">
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Country</label>
                        <input type="text" id="deptCountry">
                    </div>
                </div>
                <div class="form-group">
                    <label>Extra Info</label>
                    <textarea id="deptExtra" rows="2"></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal('departmentCreateModal')">Cancel</button>
                    <button type="submit" class="btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

        <!-- Roles & Permissions Modal -->
        <div id="rolesModal" class="modal">
            <div class="modal-content modal-content-wide">
                <div class="modal-header">Roles & Permissions</div>
                <div class="roles-modal-body">
                    <aside class="roles-modal-sidebar">
                        <div class="roles-panel-title">Roles</div>
                        <div id="rolesList" class="roles-list"></div>
                        <div class="roles-create-form">
                            <input type="text" id="newRoleName" placeholder="New role display name" />
                            <button type="button" class="btn-primary" onclick="createRole()">Create Role</button>
                        </div>
                    </aside>
                    <div class="roles-modal-main">
                        <div id="rolePermissionsArea">
                            <div class="roles-panel-title" id="selectedRoleTitle">Select a role to edit permissions</div>
                            <div id="permissionsContainer" class="permissions-container"></div>
                            <div class="role-members-section">
                                <div class="roles-panel-title">Role Members</div>
                                <div id="roleMembersList" class="role-members-list"></div>
                            </div>
                        </div>
                        <div class="roles-modal-actions">
                            <button type="button" class="btn-secondary" onclick="closeModal('rolesModal')">Close</button>
                            <button type="button" class="btn-primary" onclick="saveRolePermissions()">Save Permissions</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Department View Modal -->
        <div id="departmentModal" class="modal">
            <div class="modal-content">
                <div class="modal-header" id="departmentModalHeader">Department</div>
                <div id="departmentModalBody">
                    <p id="departmentDescription" style="color:#666;"></p>
                    <div style="margin-top:10px;">
                        <table style="width:100%;border-collapse:collapse;">
                            <thead>
                                <tr style="text-align:left;border-bottom:1px solid #e0e0e0;"><th>Name</th><th>Email</th><th>Role</th></tr>
                            </thead>
                            <tbody id="departmentUsersBody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="form-actions" style="margin-top:20px;">
                    <button type="button" class="btn-secondary" onclick="closeModal('departmentModal')">Close</button>
                </div>
            </div>
        </div>

    <!-- Production Modal -->
    <div id="productionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Add Production Record</div>
            <form id="productionForm" onsubmit="saveProduction(event)">
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" id="productionDate" required>
                </div>
                <div class="form-group">
                    <label>Quantity (Liters)</label>
                    <input type="number" id="productionQuantity" required>
                </div>
                <div class="form-group">
                    <label>Efficiency (%)</label>
                    <input type="number" id="productionEfficiency" min="0" max="100" required>
                </div>
                <div class="form-group">
                    <label>Downtime (hours)</label>
                    <input type="number" step="0.01" id="productionDowntime" min="0" value="0">
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    <textarea id="productionNotes" rows="3"></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal('productionModal')">Cancel</button>
                    <button type="submit" class="btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Process Modal -->
    <div id="processModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Add Process</div>
            <form id="processForm" onsubmit="saveProcess(event)">
                <div class="form-group">
                    <label>Process Name</label>
                    <input type="text" id="processName" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select id="processStatus" required>
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Assigned To</label>
                    <select id="processAssigned" name="assigned_to" required>
                        <option value="">Select staff</option>
                        @foreach($users ?? [] as $u)
                            <option value="{{ $u->id }}">{{ $u->first_name }} {{ $u->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Due Date</label>
                    <input type="date" id="processDueDate" required>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal('processModal')">Cancel</button>
                    <button type="submit" class="btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Purchase Order Modal -->
    <div id="poModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">New Purchase Order</div>
            <form id="poForm" onsubmit="ShowcaseModules.savePO(event)">
                <div class="form-group">
                    <label>Vendor Name</label>
                    <input type="text" id="poVendor" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea id="poDescription" rows="2"></textarea>
                </div>
                <div style="display:flex;gap:12px;">
                    <div class="form-group" style="flex:1;">
                        <label>Item Name</label>
                        <input type="text" id="poItemName" placeholder="e.g. Raw materials">
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Quantity</label>
                        <input type="number" id="poQty" value="1" min="1">
                    </div>
                </div>
                <div style="display:flex;gap:12px;">
                    <div class="form-group" style="flex:1;">
                        <label>Unit Price (₦)</label>
                        <input type="number" id="poUnitPrice" step="0.01" min="0">
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Total Amount (₦)</label>
                        <input type="number" id="poAmount" step="0.01" min="0" required>
                    </div>
                </div>
                <div style="display:flex;gap:12px;">
                    <div class="form-group" style="flex:1;">
                        <label>Category</label>
                        <select id="poCategory">
                            <option value="raw_materials">Raw Materials</option>
                            <option value="packaging">Packaging</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="services">Services</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Expected Delivery</label>
                        <input type="date" id="poDelivery">
                    </div>
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    <textarea id="poNotes" rows="2"></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal('poModal')">Cancel</button>
                    <button type="submit" class="btn-primary">Save Order</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Expose roles map for quick client-side lookups
        window.allUsers = {!! json_encode(($users ?? collect())->mapWithKeys(function($u){ return [$u->id => ($u->first_name . ' ' . $u->last_name)]; })) !!};
        window.allRoles = {!! json_encode(($roles ?? collect())->mapWithKeys(function($r){ return [$r->id => ($r->display_name ?? $r->name)]; })) !!};
        // Expose departments (including their users + roles) for quick client-side viewing
        window.allDepartments = {!! json_encode($departments ?? collect()) !!};

    </script>

    <script>
        // Navigation — updates URL path and active page
        function navigateTo(pageId, el) {
            AppUtils.navigateToPage(pageId, el);
        }

        window.onPageNavigate = function(pageId) {
            if (pageId === 'staff') loadEmployees();
            if (typeof ShowcaseModules !== 'undefined') ShowcaseModules.onPageShow(pageId);
        };

        // Modal helpers
        function openTransactionModal() {
            try {
                document.getElementById('transactionForm')?.reset();
                // clear edit state
                window.currentTransactionEditId = null;
                const header = document.querySelector('#transactionModal .modal-header'); if (header) header.textContent = 'Add Transaction';
                document.getElementById('transactionModal')?.classList.add('active');
            } catch (e) { console.error('openTransactionModal', e); }
        }

        function openInventoryModal(category = null) {
            try {
                document.getElementById('inventoryForm')?.reset();
                // clear edit state
                window.currentInventoryEditId = null;
                const header = document.querySelector('#inventoryModal .modal-header');
                if (header) header.textContent = category === 'food' ? 'Add Food Item' : 'Add Inventory Item';
                // preselect category if provided
                if (category && document.getElementById('inventoryCategory')) {
                    document.getElementById('inventoryCategory').value = category;
                } else if (document.getElementById('inventoryCategory')) {
                    document.getElementById('inventoryCategory').value = '';
                }
                // clear department selector
                if (document.getElementById('inventoryDepartment')) document.getElementById('inventoryDepartment').value = '';

                document.getElementById('inventoryModal')?.classList.add('active');
            } catch (e) { console.error('openInventoryModal', e); }
        }

        // On page load, restore last active page (if any)
        // (handled after auth in main DOMContentLoaded below)

        function openStaffModal() {
            try {
                document.getElementById('staffForm')?.reset();
                window.currentEmployeeEditId = null;
                const form = document.getElementById('staffForm');
                if (form) {
                    const pwd = form.querySelector('[name="password"]');
                    if (pwd) { pwd.required = true; pwd.placeholder = ''; }
                    const empId = form.querySelector('[name="employee_id"]');
                    if (empId) empId.required = false;
                }
                const header = document.querySelector('#staffModal .modal-header');
                if (header) header.textContent = 'Add Employee';
                document.getElementById('staffModal')?.classList.add('active');
            } catch (e) { console.error('openStaffModal', e); }
        }

        function openDepartmentModal() {
            try {
                document.getElementById('departmentForm')?.reset();
                const header = document.querySelector('#departmentCreateModal .modal-header'); if (header) header.textContent = 'Create Division';
                window.currentDepartmentEditId = null;
                const modal = document.getElementById('departmentCreateModal');
                if (modal) {
                    modal.classList.add('active');
                }
                // focus first input for convenience
                setTimeout(() => { try { document.getElementById('deptName')?.focus(); } catch(e){} }, 80);
                // ensure the form will POST to create by default (remove method override)
                const form = document.getElementById('departmentForm');
                if (form) {
                    form.action = '/api/departments';
                    // remove hidden _method if present
                    const methodInput = form.querySelector('input[name="_method"]');
                    if (methodInput) methodInput.remove();
                }
            } catch (e) { console.error('openDepartmentModal', e); }
        }

        async function saveDepartment(e) {
            e.preventDefault();
            const payload = {
                name: document.getElementById('deptName').value,
                code: document.getElementById('deptCode').value,
                description: document.getElementById('deptDescription')?.value || null,
                address: document.getElementById('deptAddress')?.value || null,
                phone: document.getElementById('deptPhone')?.value || null,
                contact_email: document.getElementById('deptEmail')?.value || null,
                city: document.getElementById('deptCity')?.value || null,
                state: document.getElementById('deptState')?.value || null,
                postal_code: document.getElementById('deptPostal')?.value || null,
                country: document.getElementById('deptCountry')?.value || null,
                extra_info: document.getElementById('deptExtra')?.value || null,
            };
            const isEdit = !!window.currentDepartmentEditId;
            await handleAction(async () => {
                if (isEdit) {
                    await apiFetch(`/api/departments/${window.currentDepartmentEditId}`, { method: 'PUT', body: payload });
                } else {
                    await apiFetch('/api/departments', { method: 'POST', body: payload });
                }
                window.currentDepartmentEditId = null;
                closeModal('departmentCreateModal');
                await loadAllData();
            }, {
                successMessage: isEdit ? 'Division updated' : 'Division created',
                errorMessage: 'Failed to save division',
            });
        }

        function openProductionModal() {
            try {
                document.getElementById('productionForm')?.reset();
                window.currentProductionEditId = null;
                const header = document.querySelector('#productionModal .modal-header'); if (header) header.textContent = 'Add Production Record';
                document.getElementById('productionModal')?.classList.add('active');
            } catch (e) { console.error('openProductionModal', e); }
        }

        function openProcessModal(type) {
            try {
                // optionally prefill based on type
                if (type) document.getElementById('processName').value = type;
                document.getElementById('processForm')?.reset();
                // clear edit state when opening a fresh modal
                window.currentProcessEditId = null;
                const header = document.querySelector('#processModal .modal-header'); if (header) header.textContent = 'Add Process';
                document.getElementById('processModal')?.classList.add('active');
            } catch (e) { console.error('openProcessModal', e); }
        }

        function closeModal(id) {
            try {
                const el = document.getElementById(id);
                if (el) {
                    el.classList.remove('active');
                    try { el.style.display = ''; el.style.zIndex = ''; } catch(e){}
                }
            } catch (e) { console.error('closeModal', e); }
        }

        // apiFetch, showToast, parseApiError, handleAction — provided by app-utils.js

        // Load all data in parallel
        async function loadAllData() {
            try {
                const [metrics, transactions, inventory, departments, production, productionStats, processes] = await Promise.all([
                    apiFetch('/api/dashboard/statistics').catch(() => apiFetch('/api/dashboard')),
                    apiFetch('/api/transactions'),
                    apiFetch('/api/inventory?with_movements=1'),
                    apiFetch('/api/departments'),
                    apiFetch('/api/production'),
                    apiFetch('/api/production/summary/statistics').catch(() => ({})),
                    apiFetch('/api/processes')
                ]);

                renderMetrics(metrics || {});
                renderTransactions(normalizeList(transactions));
                renderInventory(normalizeList(inventory));
                renderDepartments(normalizeList(departments));
                renderProduction(normalizeList(production));
                renderProductionStats(productionStats || {});
                renderProcesses(normalizeList(processes));

                initChartsWithData(metrics, transactions, inventory, production);
                loadFoodDivision();
                loadEmployees();
            } catch (err) {
                console.error('Failed to load dashboard data', err);
                showToast(parseApiError(err, 'Failed to load dashboard data'), 'error');
            }
        }

        async function loadEmployees() {
            try {
                const data = await apiFetch('/api/users');
                renderEmployees(normalizeList(data));
            } catch (err) {
                showToast(parseApiError(err, 'Failed to load employees'), 'error');
            }
        }

        function renderEmployees(list) {
            const tbody = document.getElementById('employeesTableBody');
            if (!tbody) return;
            if (!list.length) {
                tbody.innerHTML = '<tr><td colspan="7" class="empty-state">No employees found</td></tr>';
                return;
            }
            tbody.innerHTML = list.map(u => {
                const name = `${u.first_name || ''} ${u.last_name || ''}`.trim();
                const role = u.role?.display_name || u.role?.name || '—';
                const dept = u.department?.name || '—';
                const active = u.is_active !== false;
                let actions = `<button class="btn-edit btn-sm" onclick="editEmployee(${u.id})">Edit</button>`;
                if (active) {
                    actions += `<button class="btn-delete btn-sm" onclick="deactivateEmployee(${u.id})">Deactivate</button>`;
                } else {
                    actions += `<button class="btn-primary btn-sm" onclick="activateEmployee(${u.id})">Activate</button>`;
                }
                return `<tr>
                    <td>${escapeHtml(u.employee_id || '')}</td>
                    <td>${escapeHtml(name)}</td>
                    <td>${escapeHtml(u.email || '')}</td>
                    <td>${escapeHtml(role)}</td>
                    <td>${escapeHtml(dept)}</td>
                    <td><span class="status-badge ${active ? 'status-approved' : 'status-rejected'}">${active ? 'Active' : 'Inactive'}</span></td>
                    <td class="actions-col"><div class="action-btns">${actions}</div></td>
                </tr>`;
            }).join('');
        }

        async function editEmployee(id) {
            await handleAction(async () => {
                const u = await apiFetch(`/api/users/${id}`);
                document.getElementById('staffForm')?.reset();
                const form = document.getElementById('staffForm');
                if (!form) return;
                form.querySelector('[name="employee_id"]').value = u.employee_id || '';
                form.querySelector('[name="first_name"]').value = u.first_name || '';
                form.querySelector('[name="last_name"]').value = u.last_name || '';
                form.querySelector('[name="email"]').value = u.email || '';
                form.querySelector('[name="role_id"]').value = u.role_id || '';
                form.querySelector('[name="department_id"]').value = u.department_id || '';
                form.querySelector('[name="password"]').required = false;
                const hint = document.getElementById('staffPasswordHint');
                if (hint) hint.textContent = '(leave blank to keep current)';
                window.currentEmployeeEditId = id;
                const header = document.querySelector('#staffModal .modal-header');
                if (header) header.textContent = 'Edit Employee';
                document.getElementById('staffModal')?.classList.add('active');
            }, { successMessage: null, errorMessage: 'Failed to load employee' });
        }

        async function deactivateEmployee(id) {
            if (!confirm('Deactivate this employee?')) return;
            await handleAction(async () => {
                await apiFetch(`/api/users/${id}/deactivate`, { method: 'POST' });
                await loadEmployees();
                await loadAllData();
            }, { successMessage: 'Employee deactivated', errorMessage: 'Failed to deactivate employee' });
        }

        async function activateEmployee(id) {
            await handleAction(async () => {
                await apiFetch(`/api/users/${id}/activate`, { method: 'POST' });
                await loadEmployees();
                await loadAllData();
            }, { successMessage: 'Employee activated', errorMessage: 'Failed to activate employee' });
        }

        function openAdjustStockModal(id, name, currentQty) {
            document.getElementById('adjustStockItemId').value = id;
            document.getElementById('adjustStockItemName').textContent = name || 'Item';
            document.getElementById('adjustStockCurrent').textContent = Number(currentQty || 0).toLocaleString();
            document.getElementById('adjustStockForm')?.reset();
            document.getElementById('adjustStockItemId').value = id;
            document.getElementById('adjustStockModal')?.classList.add('active');
        }

        async function saveAdjustStock(e) {
            e.preventDefault();
            const id = document.getElementById('adjustStockItemId').value;
            const payload = {
                quantity: Number(document.getElementById('adjustStockQty').value),
                type: document.getElementById('adjustStockType').value,
                reason: document.getElementById('adjustStockReason').value,
            };
            await handleAction(async () => {
                await apiFetch(`/api/inventory/${id}/adjust`, { method: 'POST', body: payload });
                closeModal('adjustStockModal');
                await loadAllData();
            }, { successMessage: 'Stock adjusted successfully', errorMessage: 'Failed to adjust stock' });
        }

        async function completeProcess(id) {
            await handleAction(async () => {
                await apiFetch(`/api/processes/${id}/complete`, { method: 'POST' });
                await loadAllData();
            }, { successMessage: 'Process marked as completed', errorMessage: 'Failed to complete process' });
        }

        // Renders a compact combined recent-activity list into the Overview page.
        function renderOverviewActivities(transactions = [], processes = [], production = []) {
            const container = document.getElementById('overviewRecentActivity');
            if (!container) return;

            // Normalize to arrays and pick top 5 from each source, ordered by created/updated time
            const tx = (transactions || []).slice(0,5).map(t => ({
                type: 'Transaction',
                title: t.description || t.title || t.name || 'Transaction',
                date: t.transaction_date || t.created_at || t.date || '',
                summary: (t.amount !== undefined) ? formatCurrency(t.amount) : ''
            }));

            const pr = (processes || []).slice(0,5).map(p => ({
                type: 'Process',
                title: p.name || p.title || 'Process',
                date: p.updated_at || p.due_date || p.created_at || '',
                summary: p.status || ''
            }));

            const pd = (production || []).slice(0,5).map(p => ({
                type: 'Production',
                title: p.title || (p.production_date ? `Production ${p.production_date}` : 'Production'),
                date: p.production_date || p.created_at || '',
                summary: (p.quantity !== undefined) ? (Number(p.quantity).toLocaleString() + 'L') : ''
            }));

            // Combine and sort by date (newest first) when date available
            const combined = tx.concat(pr, pd).sort((a,b) => {
                const ad = a.date ? new Date(a.date).getTime() : 0;
                const bd = b.date ? new Date(b.date).getTime() : 0;
                return bd - ad;
            }).slice(0, 10);

            if (combined.length === 0) {
                container.innerHTML = '<p style="color:#666;">No recent activity yet.</p>';
                return;
            }

            container.innerHTML = '<ul style="list-style:none;padding-left:0;margin:0;">' + combined.map(it => `
                <li style="padding:8px 0;border-bottom:1px solid #f0f0f0;">
                    <div style="font-weight:600;color:#1e3a2e;">${escapeHtml(it.type)}: ${escapeHtml(it.title)}</div>
                    <div style="color:#666;font-size:13px;margin-top:4px;">${escapeHtml(it.summary)} ${it.date ? ' • <span class="cell-date">' + escapeHtml(formatDate(it.date)) + '</span>' : ''}</div>
                </li>
            `).join('') + '</ul>';
        }
        function renderMetrics(m) {
            // Expecting keys: total_revenue, total_expenses, net_profit, profit_margin, total_employees
            if (!m) return;

            // Support multiple shapes returned by different role-based endpoints:
            // - { total_revenue, total_expenses, ... }
            // - { metrics: { total_revenue, ... } }
            // - { counts: { users: N } }
            const top = m;
            const inner = (m.metrics && typeof m.metrics === 'object') ? m.metrics : m;
            const counts = (m.counts && typeof m.counts === 'object') ? m.counts : null;

            const tr = inner.total_revenue ?? top.total_revenue ?? null;
            const te = inner.total_expenses ?? top.total_expenses ?? null;
            const np = inner.net_profit ?? top.net_profit ?? null;
            const pm = inner.profit_margin ?? top.profit_margin ?? null;
            const usersCount = counts?.users ?? inner.total_employees ?? top.total_employees ?? null;

            if (tr !== null && tr !== undefined) document.getElementById('totalRevenue').textContent = formatCurrency(tr);
            if (te !== null && te !== undefined) document.getElementById('totalExpenses').textContent = formatCurrency(te);
            if (np !== null && np !== undefined) document.getElementById('netProfit').textContent = formatCurrency(np);
            if (pm !== null && pm !== undefined) document.getElementById('profitMargin').textContent = Math.round(Number(pm)) + '%';
            if (usersCount !== null && usersCount !== undefined) {
                const el = document.getElementById('totalEmployees'); if (el) el.textContent = usersCount;
                const el2 = document.getElementById('overviewTotalUsers'); if (el2) el2.textContent = usersCount;
            }

            // Additional overview counters
            const depCount = counts?.departments ?? inner.departments_count ?? top.departments_count ?? null;
            if (depCount !== null && depCount !== undefined) {
                const el = document.getElementById('overviewDepartments'); if (el) el.textContent = depCount;
            }

            const invItems = inner.inventory?.total_items ?? top.inventory_items ?? null;
            if (invItems !== null && invItems !== undefined) {
                const el = document.getElementById('overviewInventoryItems'); if (el) el.textContent = invItems;
            }

            const prodCount = inner.production?.total ?? top.production_records ?? null;
            if (prodCount !== null && prodCount !== undefined) {
                const el = document.getElementById('overviewProduction'); if (el) el.textContent = prodCount;
            }
        }

        // formatCurrency, formatDate, formatStatus — provided by app-utils.js

        function normalizeList(data) {
            if (!data) return [];
            if (Array.isArray(data)) return data;
            if (data.data && Array.isArray(data.data)) return data.data;
            return [];
        }

        function renderTransactions(list) {
            const tbody = document.getElementById('transactionsTableBody');
            tbody.innerHTML = (list || []).map(t => {
                const desc = t.description || t.title || t.name || '-';
                const amount = t.amount ?? t.total ?? 0;
                const date = formatDate(t.transaction_date || t.date || t.created_at);
                const status = (t.status || 'pending').toLowerCase();
                let actions = '';
                const canUpdate = (typeof t.can_update === 'undefined') ? true : Boolean(t.can_update);
                const canView = (typeof t.can_view === 'undefined') ? true : Boolean(t.can_view);
                const canDelete = (typeof t.can_delete === 'undefined') ? false : Boolean(t.can_delete);
                const canApprove = Boolean(t.can_approve);

                if (canUpdate && status === 'pending') actions += `<button class="btn-edit btn-sm" onclick="editTransaction(${t.id})">Edit</button>`;
                if (canView) actions += `<button class="btn-secondary btn-sm" onclick="viewTransaction(${t.id})">View</button>`;
                if (canApprove) {
                    actions += `<button class="btn-primary btn-sm" onclick="approveTransaction(${t.id})">Approve</button>`;
                    actions += `<button class="btn-delete btn-sm" onclick="rejectTransaction(${t.id})">Reject</button>`;
                }
                if (canDelete && status === 'pending') actions += `<button class="btn-delete btn-sm" onclick="deleteTransaction(${t.id})">Delete</button>`;

                return `
                    <tr>
                        <td>${escapeHtml(desc)}</td>
                        <td>${formatCurrency(amount)}</td>
                        <td class="cell-date">${date}</td>
                        <td><span class="status-badge status-${status}">${formatStatus(status)}</span></td>
                        <td class="action-btns">${actions}</td>
                    </tr>
                `;
            }).join('');
        }

        function renderInventory(list) {
            const tbody = document.getElementById('inventoryTableBody');
            tbody.innerHTML = (list || []).map(i => {
                const status = (i.status || 'unknown').toLowerCase();
                const statusClass = status === 'in_stock' ? 'status-in-stock' : status === 'on_order' ? 'status-on-order' : 'status-low-stock';
                return `
                    <tr>
                        <td>${escapeHtml(i.name || '')}</td>
                        <td>${escapeHtml(i.category || '')}</td>
                        <td>${escapeHtml((i.department && i.department.name) || (i.department_id ? ('#' + i.department_id) : ''))}</td>
                        <td>${Number(i.stock_quantity || 0).toLocaleString()}</td>
                        <td>${Number(i.reorder_level || 0).toLocaleString()}</td>
                        <td>${escapeHtml(i.unit_of_measure || '')}</td>
                        <td><span class="status-badge ${statusClass}">${formatStatus(i.status)}</span></td>
                        <td class="action-btns">
                            <button class="btn-edit btn-sm" onclick="editInventory(${i.id})">Edit</button>
                            <button class="btn-secondary btn-sm" onclick="viewInventory(${i.id})">View</button>
                            <button class="btn-primary btn-sm" onclick="openAdjustStockModal(${i.id}, '${escapeHtml(i.name || '').replace(/'/g, "\\'")}', ${i.stock_quantity || 0})">Adjust</button>
                            <button class="btn-delete btn-sm" onclick="deleteInventory(${i.id})">Delete</button>
                        </td>
                    </tr>
                `;
            }).join('');

            // compute metrics
            const totalUnits = (list || []).reduce((sum, it) => sum + Number(it.stock_quantity || 0), 0);
            const totalValue = (list || []).reduce((sum, it) => sum + (Number(it.stock_quantity || 0) * Number(it.unit_price || 0)), 0);
            const belowReorder = (list || []).filter(it => (it.stock_quantity ?? 0) <= (it.reorder_level ?? 0)).length;
            const outMovements = (list || []).reduce((sum, it) => sum + ((it.movements || []).filter(m => m.movement_type === 'out').length || 0), 0);
            const turnover = (list && list.length) ? (outMovements / Math.max(1, list.length)) : 0;

            const totalEl = document.getElementById('totalInventory');
            const currentEl = document.getElementById('currentStock');
            const reorderEl = document.querySelector('.inventory-grid .metric-card:nth-child(3) .metric-value');
            const turnoverEl = document.querySelector('.inventory-grid .metric-card:nth-child(4) .metric-value');

            if (totalEl) {
                // show total monetary value if unit prices available, otherwise show units
                if (totalValue > 0) totalEl.textContent = '₦' + totalValue.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                else totalEl.textContent = totalUnits.toLocaleString();
            }
            if (currentEl) currentEl.textContent = totalUnits.toLocaleString();
            if (reorderEl) reorderEl.textContent = belowReorder.toLocaleString();
            if (turnoverEl) turnoverEl.textContent = turnover ? turnover.toFixed(2) : '0.00';
        }

        // Load and render Food Division (inventory items with category=food)
        async function loadFoodDivision() {
            try {
                const data = await apiFetch('/api/inventory?category=food&with_movements=1');
                // the inventory API returns paginated results; data.data may be present
                const list = Array.isArray(data.data) ? data.data : (Array.isArray(data) ? data : (data.items || []));
                renderFoodDivision(list);
            } catch (err) {
                console.error('Failed to load food division inventory', err);
            }
        }

        function renderFoodDivision(list) {
            const tbody = document.getElementById('foodInventoryBody');
            if (!tbody) return;
            tbody.innerHTML = (list || []).map(i => {
                const status = (i.status || 'unknown').toLowerCase();
                const statusClass = status === 'in_stock' ? 'status-in-stock' : status === 'on_order' ? 'status-on-order' : 'status-low-stock';
                const deptName = (i.department && i.department.name) ? i.department.name : (i.department_id ? ('#' + i.department_id) : '');
                return `
                    <tr>
                        <td>${escapeHtml(i.name || '')}</td>
                        <td>${escapeHtml(deptName)}</td>
                        <td>${Number(i.stock_quantity || 0).toLocaleString()}</td>
                        <td>${escapeHtml(i.unit_of_measure || '')}</td>
                        <td><span class="status-badge ${statusClass}">${formatStatus(i.status)}</span></td>
                        <td class="action-btns">
                            <button class="btn-edit btn-sm" onclick="editInventory(${i.id})">Edit</button>
                            <button class="btn-secondary btn-sm" onclick="viewInventory(${i.id})">View</button>
                            <button class="btn-primary btn-sm" onclick="openAdjustStockModal(${i.id}, '${escapeHtml(i.name || '').replace(/'/g, "\\'")}', ${i.stock_quantity || 0})">Adjust</button>
                            <button class="btn-delete btn-sm" onclick="deleteInventory(${i.id})">Delete</button>
                        </td>
                    </tr>
                `;
            }).join('');

            // metrics
            const totalItems = (list || []).length;
            const totalStock = (list || []).reduce((s, it) => s + Number(it.stock_quantity || 0), 0);
            const belowReorder = (list || []).filter(it => (it.stock_quantity ?? 0) <= (it.reorder_level ?? 0)).length;
            const deptCount = new Set((list || []).map(it => it.department_id).filter(Boolean)).size;

            const totalEl = document.getElementById('foodTotalItems'); if (totalEl) totalEl.textContent = totalItems.toLocaleString();
            const stockEl = document.getElementById('foodTotalStock'); if (stockEl) stockEl.textContent = totalStock.toLocaleString();
            const belowEl = document.getElementById('foodBelowReorder'); if (belowEl) belowEl.textContent = belowReorder.toLocaleString();
            const deptEl = document.getElementById('foodDeptCount'); if (deptEl) deptEl.textContent = deptCount.toLocaleString();
        }

        async function viewInventory(id) {
            try {
                const item = await apiFetch(`/api/inventory/${id}`);
                if (!item) throw new Error('Not found');

                // Create or populate the inventory view modal
                let modal = document.getElementById('inventoryViewModal');
                if (!modal) {
                    const html = `
                    <div id="inventoryViewModal" class="modal">
                        <div class="modal-content">
                            <div class="modal-header">Inventory Item</div>
                            <div style="margin-bottom:10px;"><strong id="viewInvName"></strong></div>
                            <div style="color:#333;">
                                <div><strong>Description:</strong> <div id="viewInvDesc" style="display:inline-block;margin-left:6px;color:#333;"></div></div>
                                <div><strong>Stock Quantity:</strong> <span id="viewInvStock"></span></div>
                                <div><strong>Reorder Level:</strong> <span id="viewInvReorder"></span></div>
                                <div><strong>Unit:</strong> <span id="viewInvUnit"></span></div>
                                <div><strong>Unit Price:</strong> <span id="viewInvPrice"></span></div>
                                <div><strong>Status:</strong> <span id="viewInvStatus"></span></div>
                                <div style="margin-top:8px;"><strong>Movements:</strong>
                                    <div id="viewInvMovements" style="white-space:pre-wrap;margin-top:6px;color:#555;"></div>
                                </div>
                            </div>
                            <div class="form-actions" style="margin-top:18px;"><button type="button" class="btn-secondary" onclick="closeModal('inventoryViewModal')">Close</button></div>
                        </div>
                    </div>`;
                    document.body.insertAdjacentHTML('beforeend', html);
                    modal = document.getElementById('inventoryViewModal');
                }

                // Populate fields (use safe fallbacks)
                const nameEl = document.getElementById('viewInvName'); if (nameEl) nameEl.textContent = item.name || item.item_name || '';
                const descEl = document.getElementById('viewInvDesc'); if (descEl) descEl.textContent = item.description || item.desc || '';
                const stockEl = document.getElementById('viewInvStock'); if (stockEl) stockEl.textContent = Number(item.stock_quantity ?? item.qty ?? 0).toLocaleString();
                const reorderEl = document.getElementById('viewInvReorder'); if (reorderEl) reorderEl.textContent = Number(item.reorder_level ?? item.minimum ?? 0).toLocaleString();
                const unitEl = document.getElementById('viewInvUnit'); if (unitEl) unitEl.textContent = item.unit_of_measure || item.unit || '';
                const priceEl = document.getElementById('viewInvPrice'); if (priceEl) priceEl.textContent = (item.unit_price !== undefined && item.unit_price !== null) ? '₦' + Number(item.unit_price).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) : '';
                const statusEl = document.getElementById('viewInvStatus'); if (statusEl) statusEl.textContent = item.status || '';

                const mvEl = document.getElementById('viewInvMovements');
                if (mvEl) {
                    const movements = item.movements || item.movements_data || [];
                    if (Array.isArray(movements) && movements.length) {
                        mvEl.innerHTML = '<ul style="padding-left:16px;margin:0;">' + movements.map(m => `
                            <li>${escapeHtml(m.movement_type || m.type || '')} • ${escapeHtml((m.quantity ?? m.qty ?? ''))} • <span class="cell-date">${formatDate(m.date || m.movement_date)}</span>${m.note ? ' • ' + escapeHtml(m.note) : ''}</li>
                        `).join('') + '</ul>';
                    } else {
                        mvEl.textContent = 'No movements recorded.';
                    }
                }

                modal.classList.add('active');
            } catch (err) {
                console.error('Failed to load inventory', err);
                showToast(parseApiError(err, 'Failed to load inventory item'), 'error');
            }
        }

        async function editInventory(id) {
            await handleAction(async () => {
                const item = await apiFetch(`/api/inventory/${id}`);
                document.getElementById('inventoryItem').value = item.name || '';
                document.getElementById('inventoryStock').value = item.stock_quantity ?? 0;
                document.getElementById('inventoryReorder').value = item.reorder_level ?? 0;
                if (document.getElementById('inventoryDescription')) document.getElementById('inventoryDescription').value = item.description || '';
                if (document.getElementById('inventoryMax')) document.getElementById('inventoryMax').value = item.maximum_level ?? '';
                if (document.getElementById('inventoryUnit')) document.getElementById('inventoryUnit').value = item.unit_of_measure || '';
                if (document.getElementById('inventoryPrice')) document.getElementById('inventoryPrice').value = item.unit_price ?? '';
                if (document.getElementById('inventoryStatus')) document.getElementById('inventoryStatus').value = item.status || 'in_stock';
                if (document.getElementById('inventoryCategory')) document.getElementById('inventoryCategory').value = item.category || '';
                if (document.getElementById('inventoryDepartment')) document.getElementById('inventoryDepartment').value = item.department_id ?? '';
                const header = document.querySelector('#inventoryModal .modal-header');
                if (header) header.textContent = 'Edit Inventory Item';
                window.currentInventoryEditId = id;
                document.getElementById('inventoryModal')?.classList.add('active');
            }, { successMessage: null, errorMessage: 'Failed to load inventory item' });
        }

        async function editProduction(id) {
            await handleAction(async () => {
                const rec = await apiFetch(`/api/production/${id}`);
                document.getElementById('productionDate').value = rec.production_date ? rec.production_date.split('T')[0] : (rec.production_date || '');
                document.getElementById('productionQuantity').value = rec.quantity ?? '';
                document.getElementById('productionEfficiency').value = rec.efficiency_percentage ?? rec.efficiency ?? '';
                if (document.getElementById('productionDowntime')) document.getElementById('productionDowntime').value = rec.downtime_hours ?? 0;
                if (document.getElementById('productionNotes')) document.getElementById('productionNotes').value = rec.notes || '';

                window.currentProductionEditId = id;
                const header = document.querySelector('#productionModal .modal-header'); if (header) header.textContent = 'Edit Production Record';
                document.getElementById('productionModal')?.classList.add('active');
            }, { successMessage: null, errorMessage: 'Failed to load production record' });
        }

        async function deleteProduction(id) {
            if (!confirm('Delete this production record? This action can be undone.')) return;
            await handleAction(async () => {
                await apiFetch(`/api/production/${id}`, { method: 'DELETE' });
                await loadAllData();
            }, { successMessage: 'Production record deleted', errorMessage: 'Failed to delete production record' });
        }

        async function deleteInventory(id) {
            if (!confirm('Delete this inventory item? This action can be undone.')) return;
            await handleAction(async () => {
                await apiFetch(`/api/inventory/${id}`, { method: 'DELETE' });
                await loadAllData();
            }, { successMessage: 'Inventory item deleted', errorMessage: 'Failed to delete item' });
        }

        function renderDepartments(list) {
                    const tbody = document.getElementById('staffTableBody');

                    // If the API returned an empty list or the items lack user counts,
                    // fall back to the server-rendered `window.allDepartments` which was
                    // injected by Blade and contains counts via `withCount('users')`.
                    let renderList = list;
                    const needsFallback = !renderList || (Array.isArray(renderList) && renderList.length === 0) ||
                        (Array.isArray(renderList) && renderList.every(d => (d.users_count === undefined || d.users_count === 0) && (!Array.isArray(d.users) || d.users.length === 0)));

                    if (needsFallback && window.allDepartments && Array.isArray(window.allDepartments) && window.allDepartments.length > 0) {
                        renderList = window.allDepartments;
                    }

                    tbody.innerHTML = (renderList || []).map(d => {
                        // d may be an Eloquent-serialized object from the server (with users_count)
                        // or an API response; try multiple fallbacks.
                        const usersCount = (d.users_count !== undefined && d.users_count !== null)
                            ? d.users_count
                            : (Array.isArray(d.users) ? d.users.length : (d.user_count ?? 0));
                        const avgAge = d.avg_age ?? (d.users && Array.isArray(d.users) && d.users.length ? (d.users.reduce((s,u)=>s+(u.age || 0),0) / d.users.length).toFixed(1) : 0);
                        return `
                            <tr>
                                <td>${escapeHtml(d.name)}</td>
                                <td>${usersCount}</td>
                                <td>${avgAge}</td>
                                <td>
                                    <div class="action-btns">
                                        <button type="button" class="btn-edit btn-sm" onclick="viewDepartment(${d.id})">View</button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    }).join('');
                    // Also populate the detailed departments table if present
                    const detailTbody = document.getElementById('departmentsDetailBody');
                    if (detailTbody) {
                        detailTbody.innerHTML = (renderList || []).map(d => {
                            const usersCount = (d.users_count !== undefined && d.users_count !== null)
                                ? d.users_count
                                : (Array.isArray(d.users) ? d.users.length : (d.user_count ?? 0));
                            const code = d.code || '';
                            const email = d.contact_email || '';
                            const phone = d.phone || '';
                            const city = d.city || '';
                            const country = d.country || '';
                            return `
                                <tr>
                                    <td>${escapeHtml(d.name)}</td>
                                    <td>${escapeHtml(code)}</td>
                                    <td>${escapeHtml(email)}</td>
                                    <td>${escapeHtml(phone)}</td>
                                    <td>${escapeHtml(city)}</td>
                                    <td>${escapeHtml(country)}</td>
                                            <td>${usersCount}</td>
                                            <td class="actions-col">
                                                <div class="action-btns">
                                                    <button type="button" class="btn-edit btn-sm" onclick="viewDepartment(${d.id})">View</button>
                                                    <button type="button" class="btn-secondary btn-sm" onclick="editDepartment(${d.id})">Edit</button>
                                                    <button type="button" class="btn-delete btn-sm" onclick="deleteDepartment(${d.id})">Delete</button>
                                                </div>
                                            </td>
                                </tr>
                            `;
                        }).join('');
                    }
        }

        // Show department details and its users in a modal. Attempts to use
        // the server-provided `window.allDepartments` map first, otherwise falls
        // back to fetching the department via the API.
        async function viewDepartment(id) {
            try {
                const dept = await apiFetch(`/api/departments/${id}`);

                const header = document.getElementById('departmentModalHeader');
                const desc = document.getElementById('departmentDescription');
                const body = document.getElementById('departmentUsersBody');
                if (header) header.textContent = dept.name || 'Department';
                if (desc) desc.textContent = dept.description || '';

                const users = Array.isArray(dept.users) ? dept.users : (dept.users?.data || []);
                if (!users.length) {
                    body.innerHTML = '<tr><td colspan="3" style="padding:12px;color:#666;">No employees in this department</td></tr>';
                } else {
                    body.innerHTML = users.map(u => {
                        const role = (u.role && (u.role.display_name || u.role.name))
                            || (u.role_id && window.allRoles ? window.allRoles[u.role_id] : null)
                            || '—';
                        return `<tr><td style="padding:8px 6px;">${escapeHtml((u.first_name||'') + ' ' + (u.last_name||''))}</td><td style="padding:8px 6px;">${escapeHtml(u.email || '')}</td><td style="padding:8px 6px;">${escapeHtml(role)}</td></tr>`;
                    }).join('');
                }

                document.getElementById('departmentModal')?.classList.add('active');
            } catch (err) {
                console.error('Failed to load department', err);
                showToast(parseApiError(err, 'Failed to load department details'), 'error');
            }
        }

        // Edit an existing department: load into the create modal for editing
        async function editDepartment(id) {
            try {
                const d = await apiFetch(`/api/departments/${id}`);
                if (!d) throw new Error('Not found');
                // populate form fields
                document.getElementById('deptName').value = d.name || '';
                document.getElementById('deptCode').value = d.code || '';
                document.getElementById('deptDescription').value = d.description || '';
                document.getElementById('deptAddress').value = d.address || '';
                document.getElementById('deptPhone').value = d.phone || '';
                document.getElementById('deptEmail').value = d.contact_email || '';
                document.getElementById('deptCity').value = d.city || '';
                document.getElementById('deptState').value = d.state || '';
                document.getElementById('deptPostal').value = d.postal_code || '';
                document.getElementById('deptCountry').value = d.country || '';
                document.getElementById('deptExtra').value = d.extra_info || '';
                // Set edit mode and open modal. Saving will be performed via AJAX (saveDepartment).
                window.currentDepartmentEditId = id;
                const header = document.querySelector('#departmentCreateModal .modal-header'); if (header) header.textContent = 'Edit Division';
                const modal = document.getElementById('departmentCreateModal'); if (modal) modal.classList.add('active');
            } catch (err) {
                console.error('Failed to load department for edit', err);
                showToast(parseApiError(err, 'Failed to load division for editing'), 'error');
            }
        }

        async function deleteDepartment(id) {
            if (!confirm('Delete this division? This action cannot be undone.')) return;
            await handleAction(async () => {
                await apiFetch(`/api/departments/${id}`, { method: 'DELETE' });
                await loadAllData();
            }, { successMessage: 'Division deleted', errorMessage: 'Failed to delete division' });
        }

        // Food Division helpers: create or mark an existing department as Food Division
        async function createFoodDivision() {
            if (!confirm('Create a new department named "Food Division"?')) return;
            await handleAction(async () => {
                const res = await apiFetch('/api/departments', { method: 'POST', body: { name: 'Food Division', code: 'FOOD', description: 'Auto-created Food Division' } });
                if (res && res.id) window.location.reload();
            }, { successMessage: 'Food Division created', errorMessage: 'Failed to create Food Division' });
        }

        async function setSelectedAsFood() {
            const sel = document.getElementById('chooseDeptForFood');
            if (!sel) { showToast('No department select found', 'warning'); return; }
            const id = sel.value;
            if (!id) { showToast('Select a department first', 'warning'); return; }
            if (!confirm('Rename selected department to "Food Division"?')) return;
            await handleAction(async () => {
                await apiFetch(`/api/departments/${id}`, { method: 'PUT', body: { name: 'Food Division' } });
                window.location.reload();
            }, { successMessage: 'Department renamed to Food Division', errorMessage: 'Failed to rename department' });
        }

        // Roles & Permissions management
    let rolesCache = [];
    let permissionsCache = [];
    let usersCache = [];
        let selectedRoleId = null;

        function openRolesModal() {
            document.getElementById('rolesModal')?.classList.add('active');
            loadRolesAndPermissions();
        }

        async function loadRolesAndPermissions() {
            try {
                const [roles, permsRes, users] = await Promise.all([
                    apiFetch('/api/roles'),
                    apiFetch('/api/permissions'),
                    apiFetch('/api/users')
                ]);

                rolesCache = roles || [];
                permissionsCache = (permsRes && (permsRes.permissions || permsRes)) || [];
                usersCache = users || [];

                // render roles list
                const rl = document.getElementById('rolesList');
                if (rl) {
                    rl.innerHTML = (rolesCache || []).map(r => `
                        <div class="role-list-item${Number(selectedRoleId) === Number(r.id) ? ' active' : ''}" data-role-id="${r.id}" onclick="selectRole(${r.id})">
                            <div class="role-list-name">${escapeHtml(r.display_name || r.name)}</div>
                            <div class="role-list-desc">${escapeHtml((r.description || ''))}</div>
                        </div>
                    `).join('');
                }

                // clear permissions area and members
                document.getElementById('permissionsContainer').innerHTML = '';
                document.getElementById('roleMembersList').innerHTML = '';
                document.getElementById('selectedRoleTitle').textContent = 'Select a role to edit permissions';
            } catch (err) {
                console.error('Failed to load roles/permissions', err);
                showToast(parseApiError(err, 'Failed to load roles or permissions'), 'error');
            }
        }

        async function selectRole(id) {
            try {
                selectedRoleId = id;
                document.querySelectorAll('#rolesList .role-list-item').forEach(el => {
                    el.classList.toggle('active', Number(el.dataset.roleId) === Number(id));
                });
                // find role in cache (if role has permissions preloaded they may be available)
                let role = rolesCache && rolesCache.find(r => r.id == id);
                if (!role || !role.permissions) {
                    role = await apiFetch(`/api/roles/${id}`);
                }

                // update title
                document.getElementById('selectedRoleTitle').textContent = (role.display_name || role.name || 'Role') + ' — Permissions';

                // render permissions grouped by module if available in permissionsCache
                const container = document.getElementById('permissionsContainer');
                if (!container) return;

                // Build lookup of existing permissions for role
                const rolePerms = (role.permissions || []).reduce((m,p)=>{ m[p.id] = p; return m; }, {});

                // PermissionsCache may be an array of permission resources
                const grouped = (Array.isArray(permissionsCache) ? permissionsCache : []).reduce((g,p)=>{
                    const mod = p.module || 'General';
                    g[mod] = g[mod] || [];
                    g[mod].push(p);
                    return g;
                }, {});

                let html = '';
                for (const mod of Object.keys(grouped)) {
                    html += `<div class="permissions-module"><div class="permissions-module-title">${escapeHtml(mod)}</div>`;
                    html += '<div class="permissions-list">';
                    for (const p of grouped[mod]) {
                        const has = !!rolePerms[p.id];
                        const access = rolePerms[p.id]?.pivot?.access_level || rolePerms[p.id]?.access_level || 'view';
                        html += `
                            <label class="permission-row">
                                <input type="checkbox" data-perm-id="${p.id}" ${has ? 'checked' : ''} onchange="onPermToggle(this)">
                                <div class="permission-info">
                                    <div class="permission-name">${escapeHtml(p.display_name || p.name)}</div>
                                    <div class="permission-desc">${escapeHtml(p.description || '')}</div>
                                </div>
                                <select class="permission-access-select" data-perm-id-select="${p.id}" ${has ? '' : 'disabled'}>
                                    <option value="view" ${access==='view'?'selected':''}>View</option>
                                    <option value="edit" ${access==='edit'?'selected':''}>Edit</option>
                                    <option value="approve" ${access==='approve'?'selected':''}>Approve</option>
                                    <option value="full" ${access==='full'?'selected':''}>Full</option>
                                </select>
                            </label>
                        `;
                    }
                    html += '</div></div>';
                }

                container.innerHTML = html;

                // render members
                const membersEl = document.getElementById('roleMembersList');
                if (membersEl) {
                    const memberHtml = (usersCache || []).map(u => {
                        const has = (u.role_id && Number(u.role_id) === Number(id));
                        return `<div class="role-member-row">
                            <div class="role-member-info">
                                <div>${escapeHtml((u.first_name||'') + ' ' + (u.last_name||''))}</div>
                                <div class="role-member-email">${escapeHtml(u.email||'')}</div>
                            </div>
                            <div class="role-member-actions">
                                ${has ? `<button type="button" class="btn-secondary btn-sm" onclick="removeRoleFromUser(${u.id})">Remove</button>` : `<button type="button" class="btn-primary btn-sm" onclick="assignRoleToUser(${u.id})">Assign</button>`}
                            </div>
                        </div>`;
                    }).join('');
                    membersEl.innerHTML = memberHtml || '<div class="empty-hint">No users found.</div>';
                }
            } catch (err) {
                console.error('Failed to load role details', err);
                showToast(parseApiError(err, 'Failed to load role details'), 'error');
            }
        }

        async function assignRoleToUser(userId) {
            if (!selectedRoleId) { showToast('Select a role first', 'warning'); return; }
            await handleAction(async () => {
                await apiFetch(`/api/users/${userId}`, { method: 'PUT', body: { role_id: selectedRoleId } });
                const u = usersCache.find(x => x.id == userId);
                if (u) u.role_id = selectedRoleId;
                await selectRole(selectedRoleId);
            }, { successMessage: 'Role assigned to user', errorMessage: 'Failed to assign role' });
        }

        async function removeRoleFromUser(userId) {
            if (!selectedRoleId) { showToast('Select a role first', 'warning'); return; }
            await handleAction(async () => {
                await apiFetch(`/api/users/${userId}`, { method: 'PUT', body: { role_id: null } });
                const u = usersCache.find(x => x.id == userId);
                if (u) u.role_id = null;
                await selectRole(selectedRoleId);
            }, { successMessage: 'Role removed from user', errorMessage: 'Failed to remove role' });
        }

        function onPermToggle(cb) {
            const pid = cb.getAttribute('data-perm-id');
            const sel = document.querySelector(`select[data-perm-id-select="${pid}"]`);
            if (sel) sel.disabled = !cb.checked;
        }

        async function saveRolePermissions() {
            if (!selectedRoleId) { showToast('No role selected', 'warning'); return; }
            await handleAction(async () => {
                const container = document.getElementById('permissionsContainer');
                const checks = container.querySelectorAll('input[type=checkbox][data-perm-id]');
                const payload = { permissions: [] };
                checks.forEach(ch => {
                    if (ch.checked) {
                        const pid = ch.getAttribute('data-perm-id');
                        const sel = container.querySelector(`select[data-perm-id-select="${pid}"]`);
                        const access = sel ? sel.value : 'view';
                        payload.permissions.push({ permission_id: Number(pid), access_level: access });
                    }
                });
                await apiFetch(`/api/roles/${selectedRoleId}/permissions`, { method: 'POST', body: payload });
                await loadRolesAndPermissions();
            }, { successMessage: 'Permissions saved', errorMessage: 'Failed to save permissions' });
        }

        async function createRole() {
            const name = (document.getElementById('newRoleName')?.value || '').trim();
            if (!name) { showToast('Enter a role name', 'warning'); return; }
            await handleAction(async () => {
                const res = await apiFetch('/api/roles', { method: 'POST', body: { display_name: name, name: name.toLowerCase().replace(/\s+/g, '_') } });
                document.getElementById('newRoleName').value = '';
                await loadRolesAndPermissions();
                if (res && res.id) selectRole(res.id);
            }, { successMessage: 'Role created', errorMessage: 'Failed to create role' });
        }

        function renderProduction(list) {
            const tbody = document.getElementById('productionTableBody');
            tbody.innerHTML = (list || []).map(p => {
                const date = formatDate(p.production_date || p.date || p.created_at);
                const qty = Number(p.quantity || p.amount || 0).toLocaleString() + ' L';
                const eff = Math.round(Number(p.efficiency_percentage ?? p.efficiency ?? p.efficiency_percent ?? 0)) + '%';
                const downtime = Number(p.downtime_hours ?? 0).toLocaleString() + ' hrs';
                const status = (p.status || 'pending').toLowerCase();
                let actions = `<button class="btn-edit btn-sm" onclick="editProduction(${p.id})">Edit</button>
                        <button class="btn-secondary btn-sm" onclick="viewProduction(${p.id})">View</button>`;
                if (status === 'pending') {
                    actions += `<button class="btn-primary btn-sm" onclick="approveProduction(${p.id})">Approve</button>
                        <button class="btn-delete btn-sm" onclick="rejectProduction(${p.id})">Reject</button>`;
                }
                actions += `<button class="btn-delete btn-sm" onclick="deleteProduction(${p.id})">Delete</button>`;
                return `
                <tr>
                    <td class="cell-date">${date}</td>
                    <td>${qty}</td>
                    <td>${eff}</td>
                    <td>${downtime}</td>
                    <td><span class="status-badge status-${status}">${formatStatus(status)}</span></td>
                    <td class="action-btns">${actions}</td>
                </tr>
            `}).join('');
        }

        async function approveProduction(id) {
            if (!confirm('Approve this production record?')) return;
            await handleAction(async () => {
                await apiFetch(`/api/production/${id}/approve`, { method: 'POST' });
                await loadAllData();
            }, { successMessage: 'Production record approved', errorMessage: 'Failed to approve production' });
        }

        async function rejectProduction(id) {
            if (!confirm('Reject this production record?')) return;
            await handleAction(async () => {
                await apiFetch(`/api/production/${id}/reject`, { method: 'POST' });
                await loadAllData();
            }, { successMessage: 'Production record rejected', errorMessage: 'Failed to reject production' });
        }

        function renderProductionStats(s) {
            // Expecting keys: total_production, avg_efficiency, total_downtime, total_batches
            try {
                const totalEl = document.querySelector('#oil-production .metric-card:nth-child(1) .metric-value');
                const todayEl = document.querySelector('#oil-production .metric-card:nth-child(2) .metric-value');
                const downtimeEl = document.querySelector('#oil-production .metric-card:nth-child(3) .metric-value');
                const efficiencyEl = document.querySelector('#oil-production .metric-card:nth-child(4) .metric-value');

                if (totalEl && s.total_production !== undefined) {
                    totalEl.textContent = Number(s.total_production || 0).toLocaleString() + 'L';
                }
                // production today: try stats.total_production_today or derive from recent records if absent
                if (todayEl) {
                    if (s.total_production_today !== undefined) {
                        todayEl.textContent = Number(s.total_production_today || 0).toLocaleString() + 'L';
                    } else if (s.total_production !== undefined && s.total_batches !== undefined) {
                        // fallback: average per batch
                        const avg = (Number(s.total_production || 0) / Math.max(1, Number(s.total_batches || 1))); 
                        todayEl.textContent = Math.round(avg).toLocaleString() + 'L';
                    }
                }

                if (downtimeEl && s.total_downtime !== undefined) {
                    downtimeEl.innerHTML = (Number(s.total_downtime || 0)).toLocaleString() + '<span style="font-size:20px;">hrs</span>';
                }

                if (efficiencyEl && s.avg_efficiency !== undefined) {
                    efficiencyEl.textContent = Math.round(Number(s.avg_efficiency || 0)) + '%';
                }
            } catch (e) {
                console.error('renderProductionStats error', e);
            }
        }

        function renderProcesses(list) {
            const container = document.getElementById('processTableBody');
            if (!container) return;
            if (!list || !list.length) {
                container.innerHTML = '<tr><td colspan="5" class="empty-state">No processes found</td></tr>';
                return;
            }
            container.innerHTML = list.map(p => {
                const rawStatus = (p.status || 'pending').toString();
                const statusKey = rawStatus.toLowerCase();
                const statusClass = statusKey === 'completed' ? 'status-completed'
                    : (statusKey.includes('progress') ? 'status-in_progress' : 'status-pending');
                const assignedName = p.assigned_to_name || (p.assigned_to && window.allUsers ? window.allUsers[p.assigned_to] : '') || '—';
                const dueDate = formatDate(p.due_date || p.dueDate);
                return `
                    <tr>
                        <td>${escapeHtml(p.name || p.title || '')}</td>
                        <td><span class="status-badge ${statusClass}">${formatStatus(rawStatus)}</span></td>
                        <td><span class="user-avatar"></span>${escapeHtml(assignedName)}</td>
                        <td class="cell-date">${dueDate}</td>
                        <td class="action-btns">
                            <button class="btn-secondary btn-sm" onclick="viewProcess(${p.id})">View</button>
                            <button class="btn-edit btn-sm" onclick="editProcess(${p.id})">Edit</button>
                            ${statusKey !== 'completed' ? `<button class="btn-primary btn-sm" onclick="completeProcess(${p.id})">Complete</button>` : ''}
                            <button class="btn-delete btn-sm" onclick="deleteProcess(${p.id})">Delete</button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        // Edit an existing process: load data and open modal in edit mode
        async function editProcess(id) {
            try {
                const p = await apiFetch(`/api/processes/${id}`);
                if (!p) throw new Error('Not found');

                // Prefill modal
                document.getElementById('processName').value = p.name || '';
                if (document.getElementById('processStatus')) document.getElementById('processStatus').value = p.status || '';
                if (document.getElementById('processAssigned')) document.getElementById('processAssigned').value = p.assigned_to ?? p.assigned_to_id ?? '';
                if (document.getElementById('processDueDate')) document.getElementById('processDueDate').value = (p.due_date || p.dueDate || '').split('T')[0];

                window.currentProcessEditId = id;
                const header = document.querySelector('#processModal .modal-header'); if (header) header.textContent = 'Edit Process';
                document.getElementById('processModal')?.classList.add('active');
            } catch (err) {
                console.error('Failed to load process for edit', err);
                showToast(parseApiError(err, 'Failed to load process'), 'error');
            }
        }

        async function viewProcess(id) {
            await handleAction(async () => {
                const p = await apiFetch(`/api/processes/${id}`);
                let modal = document.getElementById('processViewModal');
                if (!modal) {
                    const html = `
                    <div id="processViewModal" class="modal">
                        <div class="modal-content">
                            <div class="modal-header">Process Details</div>
                            <div style="color:#333;display:grid;gap:8px;">
                                <div><strong>Name:</strong> <span id="viewProcessName"></span></div>
                                <div><strong>Status:</strong> <span id="viewProcessStatus"></span></div>
                                <div><strong>Assigned to:</strong> <span id="viewProcessAssigned"></span></div>
                                <div><strong>Due date:</strong> <span id="viewProcessDue"></span></div>
                                <div><strong>Description:</strong>
                                    <div id="viewProcessDesc" style="white-space:pre-wrap;margin-top:4px;color:#555;"></div>
                                </div>
                            </div>
                            <div class="form-actions" style="margin-top:18px;"><button type="button" class="btn-secondary" onclick="closeModal('processViewModal')">Close</button></div>
                        </div>
                    </div>`;
                    document.body.insertAdjacentHTML('beforeend', html);
                    modal = document.getElementById('processViewModal');
                }
                const assignedName = p.assigned_to_name || (p.assigned_to && window.allUsers ? window.allUsers[p.assigned_to] : '') || '—';
                document.getElementById('viewProcessName').textContent = p.name || p.title || '';
                document.getElementById('viewProcessStatus').textContent = p.status || '';
                document.getElementById('viewProcessAssigned').textContent = assignedName;
                document.getElementById('viewProcessDue').textContent = formatDate(p.due_date || p.dueDate);
                document.getElementById('viewProcessDesc').textContent = p.description || p.notes || '—';
                modal.classList.add('active');
            }, { successMessage: null, errorMessage: 'Failed to load process' });
        }

        async function deleteProcess(id) {
            if (!confirm('Delete this process? This action cannot be undone.')) return;
            await handleAction(async () => {
                await apiFetch(`/api/processes/${id}`, { method: 'DELETE' });
                await loadAllData();
            }, { successMessage: 'Process deleted', errorMessage: 'Failed to delete process' });
        }

        // Simple escape to avoid injecting HTML
        function escapeHtml(str){
            if (!str && str !== 0) return '';
            return String(str).replace(/[&<>"]/g, function(m){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[m]); });
        }

        // Form submission handlers - call API endpoints then reload
        async function saveTransaction(e) {
            e.preventDefault();
            const payload = {
                type: document.getElementById('transactionType').value,
                description: document.getElementById('transactionDesc').value,
                amount: Number(document.getElementById('transactionAmount').value),
                transaction_date: document.getElementById('transactionDate').value,
                category: document.getElementById('transactionCategory')?.value || null,
                client_name: document.getElementById('transactionClient')?.value || null,
                notes: document.getElementById('transactionNotes')?.value || null,
            };
            const isEdit = !!window.currentTransactionEditId;
            await handleAction(async () => {
                if (isEdit) {
                    await apiFetch(`/api/transactions/${window.currentTransactionEditId}`, { method: 'PUT', body: payload });
                } else {
                    await apiFetch('/api/transactions', { method: 'POST', body: payload });
                }
                await loadAllData();
                closeModal('transactionModal');
                window.currentTransactionEditId = null;
                const header = document.querySelector('#transactionModal .modal-header');
                if (header) header.textContent = 'Add Transaction';
            }, {
                successMessage: isEdit ? 'Transaction updated' : 'Transaction created',
                errorMessage: 'Failed to save transaction',
            });
        }

        // Prefill transaction modal for editing
        async function editTransaction(id) {
            try {
                const t = await apiFetch(`/api/transactions/${id}`);
                if (!t) throw new Error('Not found');

                document.getElementById('transactionType').value = t.type || '';
                document.getElementById('transactionDesc').value = t.description || '';
                document.getElementById('transactionAmount').value = t.amount ?? t.total ?? '';
                document.getElementById('transactionDate').value = (t.transaction_date || t.date || '').split('T')[0];
                if (document.getElementById('transactionCategory')) document.getElementById('transactionCategory').value = t.category || '';
                if (document.getElementById('transactionClient')) document.getElementById('transactionClient').value = t.client_name || '';
                if (document.getElementById('transactionNotes')) document.getElementById('transactionNotes').value = t.notes || '';

                window.currentTransactionEditId = id;
                const header = document.querySelector('#transactionModal .modal-header'); if (header) header.textContent = 'Edit Transaction';
                document.getElementById('transactionModal')?.classList.add('active');
            } catch (err) {
                console.error('Failed to load transaction for edit', err);
                showToast(parseApiError(err, 'Failed to load transaction'), 'error');
            }
        }

        // View a transaction in a read-only modal
        async function viewTransaction(id) {
            try {
                const t = await apiFetch(`/api/transactions/${id}`);
                if (!t) throw new Error('Not found');

                // Create or populate the view modal
                let modal = document.getElementById('transactionViewModal');
                if (!modal) {
                    // inject modal HTML
                    const html = `
                    <div id="transactionViewModal" class="modal">
                        <div class="modal-content">
                            <div class="modal-header">Transaction Details</div>
                            <div style="margin-bottom:10px;"><strong id="viewTxType"></strong></div>
                            <div style="color:#333;"><div><strong>Description:</strong> <span id="viewTxDesc"></span></div><div><strong>Amount:</strong> <span id="viewTxAmount"></span></div><div><strong>Date:</strong> <span id="viewTxDate"></span></div><div><strong>Category:</strong> <span id="viewTxCategory"></span></div><div><strong>Client:</strong> <span id="viewTxClient"></span></div><div style="margin-top:8px;"><strong>Notes:</strong><div id="viewTxNotes" style="white-space:pre-wrap;margin-top:6px;color:#555;"></div></div></div>
                            <div class="form-actions" style="margin-top:18px;"><button type="button" class="btn-secondary" onclick="closeModal('transactionViewModal')">Close</button></div>
                        </div>
                    </div>`;
                    document.body.insertAdjacentHTML('beforeend', html);
                    modal = document.getElementById('transactionViewModal');
                }

                document.getElementById('viewTxType').textContent = (t.type || '').toUpperCase();
                document.getElementById('viewTxDesc').textContent = t.description || '';
                document.getElementById('viewTxAmount').textContent = formatCurrency(t.amount || 0);
                document.getElementById('viewTxDate').textContent = t.transaction_date || t.date || '';
                document.getElementById('viewTxCategory').textContent = t.category || '';
                document.getElementById('viewTxClient').textContent = t.client_name || '';
                document.getElementById('viewTxNotes').textContent = t.notes || '';

                modal.classList.add('active');
            } catch (err) {
                console.error('Failed to load transaction', err);
                showToast(parseApiError(err, 'Failed to load transaction'), 'error');
            }
        }

        async function approveTransaction(id) {
            if (!confirm('Approve this transaction?')) return;
            await handleAction(async () => {
                await apiFetch(`/api/transactions/${id}/approve`, { method: 'POST' });
                await loadAllData();
            }, { successMessage: 'Transaction approved', errorMessage: 'Failed to approve transaction' });
        }

        async function rejectTransaction(id) {
            const reason = prompt('Rejection reason (optional):') || '';
            if (!confirm('Reject this transaction?')) return;
            await handleAction(async () => {
                await apiFetch(`/api/transactions/${id}/reject`, { method: 'POST', body: { reason } });
                await loadAllData();
            }, { successMessage: 'Transaction rejected', errorMessage: 'Failed to reject transaction' });
        }

        async function deleteTransaction(id) {
            if (!confirm('Delete this transaction? This action cannot be undone.')) return;
            await handleAction(async () => {
                await apiFetch(`/api/transactions/${id}`, { method: 'DELETE' });
                await loadAllData();
            }, { successMessage: 'Transaction deleted', errorMessage: 'Failed to delete transaction' });
        }

        async function saveInventory(e) {
            e.preventDefault();
            const payload = {
                name: document.getElementById('inventoryItem').value,
                description: (document.getElementById('inventoryDescription')?.value) || null,
                stock_quantity: Number(document.getElementById('inventoryStock').value || 0),
                reorder_level: Number(document.getElementById('inventoryReorder').value || 0),
                maximum_level: document.getElementById('inventoryMax')?.value ? Number(document.getElementById('inventoryMax').value) : null,
                unit_of_measure: document.getElementById('inventoryUnit').value,
                unit_price: document.getElementById('inventoryPrice')?.value ? Number(document.getElementById('inventoryPrice').value) : null,
                status: document.getElementById('inventoryStatus').value,
                category: document.getElementById('inventoryCategory')?.value || null,
                department_id: document.getElementById('inventoryDepartment')?.value || null
            };
            const isEdit = !!window.currentInventoryEditId;
            await handleAction(async () => {
                if (isEdit) {
                    await apiFetch(`/api/inventory/${window.currentInventoryEditId}`, { method: 'PUT', body: payload });
                } else {
                    await apiFetch('/api/inventory', { method: 'POST', body: payload });
                }
                await loadAllData();
                window.currentInventoryEditId = null;
                closeModal('inventoryModal');
            }, {
                successMessage: isEdit ? 'Inventory item updated' : 'Inventory item created',
                errorMessage: 'Failed to save inventory item',
            });
        }

        async function saveStaff(e) {
            e.preventDefault();
            const form = document.getElementById('staffForm');
            if (!form) return;

            const fd = new FormData(form);
            const payload = Object.fromEntries(fd.entries());
            const isEdit = !!window.currentEmployeeEditId;

            if (!isEdit) {
                payload.password_confirmation = payload.password;
                payload.hire_date = new Date().toISOString().split('T')[0];
                if (!payload.employee_id) delete payload.employee_id;
            } else {
                delete payload.password;
                delete payload.password_confirmation;
            }

            await handleAction(async () => {
                if (isEdit) {
                    await apiFetch(`/api/users/${window.currentEmployeeEditId}`, { method: 'PUT', body: payload });
                } else {
                    await apiFetch('/api/users', { method: 'POST', body: payload });
                }
                window.currentEmployeeEditId = null;
                const hint = document.getElementById('staffPasswordHint');
                if (hint) hint.textContent = '';
                await loadAllData();
                await loadEmployees();
                closeModal('staffModal');
                const header = document.querySelector('#staffModal .modal-header');
                if (header) header.textContent = 'Add Employee';
            }, {
                successMessage: isEdit ? 'Employee updated' : 'Employee created',
                errorMessage: 'Failed to save employee',
            });
        }

        async function saveProduction(e) {
            e.preventDefault();
            const payload = {
                production_date: document.getElementById('productionDate').value,
                quantity: Number(document.getElementById('productionQuantity').value),
                efficiency_percentage: Number(document.getElementById('productionEfficiency').value),
                downtime_hours: Number(document.getElementById('productionDowntime').value || 0),
                notes: document.getElementById('productionNotes')?.value || null,
            };
            const isEdit = !!window.currentProductionEditId;
            await handleAction(async () => {
                if (isEdit) {
                    await apiFetch(`/api/production/${window.currentProductionEditId}`, { method: 'PUT', body: payload });
                } else {
                    await apiFetch('/api/production', { method: 'POST', body: payload });
                }
                await loadAllData();
                window.currentProductionEditId = null;
                closeModal('productionModal');
            }, {
                successMessage: isEdit ? 'Production record updated' : 'Production record created',
                errorMessage: 'Failed to save production',
            });
        }

        async function viewProduction(id) {
            await handleAction(async () => {
                const p = await apiFetch(`/api/production/${id}`);
                let modal = document.getElementById('productionViewModal');
                if (!modal) {
                    const html = `
                    <div id="productionViewModal" class="modal">
                        <div class="modal-content">
                            <div class="modal-header">Production Details</div>
                            <div style="margin-bottom:10px;"><strong id="viewProdTitle"></strong></div>
                            <div style="color:#333;">
                                <div><strong>Date:</strong> <span id="viewProdDate"></span></div>
                                <div><strong>Quantity:</strong> <span id="viewProdQuantity"></span></div>
                                <div><strong>Efficiency:</strong> <span id="viewProdEfficiency"></span></div>
                                <div><strong>Downtime:</strong> <span id="viewProdDowntime"></span></div>
                                <div style="margin-top:8px;"><strong>Notes:</strong>
                                    <div id="viewProdNotes" style="white-space:pre-wrap;margin-top:6px;color:#555;"></div>
                                </div>
                            </div>
                            <div class="form-actions" style="margin-top:18px;"><button type="button" class="btn-secondary" onclick="closeModal('productionViewModal')">Close</button></div>
                        </div>
                    </div>`;
                    document.body.insertAdjacentHTML('beforeend', html);
                    modal = document.getElementById('productionViewModal');
                }
                const titleEl = document.getElementById('viewProdTitle'); if (titleEl) titleEl.textContent = p.title || `Production ${p.production_date || ''}`;
                const dateEl = document.getElementById('viewProdDate'); if (dateEl) dateEl.textContent = formatDate(p.production_date || p.date || p.created_at);
                const qtyEl = document.getElementById('viewProdQuantity'); if (qtyEl) qtyEl.textContent = (p.quantity !== undefined ? Number(p.quantity).toLocaleString() + ' L' : (p.amount !== undefined ? Number(p.amount).toLocaleString() + ' L' : ''));
                const effEl = document.getElementById('viewProdEfficiency'); if (effEl) effEl.textContent = (p.efficiency_percentage ?? p.efficiency ?? p.efficiency_percent) ? String(p.efficiency_percentage ?? p.efficiency ?? p.efficiency_percent) + '%' : '';
                const downEl = document.getElementById('viewProdDowntime'); if (downEl) downEl.textContent = (p.downtime_hours !== undefined ? String(p.downtime_hours) + ' hrs' : '');
                const notesEl = document.getElementById('viewProdNotes'); if (notesEl) notesEl.textContent = p.notes || '';
                modal.classList.add('active');
            }, { successMessage: null, errorMessage: 'Failed to load production' });
        }

        async function saveProcess(e) {
            e.preventDefault();
            const payload = {
                name: document.getElementById('processName').value,
                status: document.getElementById('processStatus').value,
                assigned_to: document.getElementById('processAssigned').value,
                due_date: document.getElementById('processDueDate').value
            };
            const isEdit = !!window.currentProcessEditId;
            await handleAction(async () => {
                if (isEdit) {
                    await apiFetch(`/api/processes/${window.currentProcessEditId}`, { method: 'PUT', body: payload });
                } else {
                    await apiFetch('/api/processes', { method: 'POST', body: payload });
                }
                await loadAllData();
                closeModal('processModal');
                window.currentProcessEditId = null;
                const header = document.querySelector('#processModal .modal-header');
                if (header) header.textContent = 'Add Process';
            }, {
                successMessage: isEdit ? 'Process updated' : 'Process created',
                errorMessage: 'Failed to save process',
            });
        }

        // Charts initializer that accepts server data (basic wiring)
        let revenueChart, expenseChart, inventoryChartInstance, absenteeismChartInstance, productionChartInstance;
        function initChartsWithData(metrics = {}, transactions = [], inventory = [], production = []){
            // For now, reuse existing static chart code but feed any simple arrays if available.
            // Clear any existing charts
            try { revenueChart?.destroy(); expenseChart?.destroy(); inventoryChartInstance?.destroy(); absenteeismChartInstance?.destroy(); productionChartInstance?.destroy(); } catch(e){}

            // Minimal charts using placeholder/fallbacks if server data not shaped
            const revenueCtx = document.getElementById('revenueChart');
            if (revenueCtx) {
                revenueChart = new Chart(revenueCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
                        datasets: [{label: 'Revenue', data: [0,0,0,0,0,0,0], borderColor:'#2d7a5f', backgroundColor:'rgba(45,122,95,0.1)', fill:true}]
                    }, options:{responsive:true, plugins:{legend:{display:false}}}
                });
            }

            const expenseCtx = document.getElementById('expenseChart');
            if (expenseCtx) {
                expenseChart = new Chart(expenseCtx.getContext('2d'), {type:'doughnut', data:{labels:['Payroll','Marketing','Operations','Utilities'], datasets:[{data:[40,25,20,15], backgroundColor:['#1e3a2e','#2d5a47','#4a8068','#7fa895']}]}, options:{responsive:true, plugins:{legend:{position:'right'}}}});
            }

            const inventoryCtx = document.getElementById('inventoryChart');
            if (inventoryCtx) {
                inventoryChartInstance = new Chart(inventoryCtx.getContext('2d'), {type:'line', data:{labels:['Mon','Tue','Wed','Thu','Fri','Sat'], datasets:[{data:[0,0,0,0,0,0], borderColor:'#2d7a5f', backgroundColor:'rgba(45,122,95,0.1)', fill:true}]}, options:{responsive:true, plugins:{legend:{display:false}}}});
            }

            const absenteeismCtx = document.getElementById('absenteeismChart');
            if (absenteeismCtx) {
                absenteeismChartInstance = new Chart(absenteeismCtx.getContext('2d'), {type:'line', data:{labels:['Mon','Tue','Wed','Thu','Fri','Sat','Sun'], datasets:[{data:[0,0,0,0,0,0,0], borderColor:'#2d7a5f', backgroundColor:'rgba(45,122,95,0.1)', fill:true}]}, options:{responsive:true, plugins:{legend:{display:false}}}});
            }

            const productionCtx = document.getElementById('productionChart');
            if (productionCtx) {
                productionChartInstance = new Chart(productionCtx.getContext('2d'), {type:'bar', data:{labels:['Jan 20','Jan 21','Jan 22'], datasets:[{data:[0,0,0], backgroundColor:'#4a9d7e'}]}, options:{responsive:true, plugins:{legend:{display:false}}}});
            }
        }

        // Authentication helpers
        async function checkAuth() {
            const token = localStorage.getItem('api_token');
            if (!token) {
                window.currentUser = null;
                updateUserArea(null);
                return false;
            }

            window.apiToken = token;
            try {
                const user = await apiFetch('/api/me');
                window.currentUser = user;
                updateUserArea(user);
                return true;
            } catch (err) {
                window.currentUser = null;
                updateUserArea(null);
                return false;
            }
        }

        function updateUserArea(user) {
            const loginBtn = document.getElementById('loginBtn');
            const loggedInArea = document.getElementById('loggedInArea');
            const nameEl = document.getElementById('currentUserName');
            const avatarEl = document.getElementById('userAvatar');
            if (user) {
                if (loginBtn) loginBtn.style.display = 'none';
                if (loggedInArea) loggedInArea.style.display = 'inline-flex';
                const name = user.first_name ? `${user.first_name} ${user.last_name}` : (user.name || user.email || 'User');
                if (nameEl) nameEl.textContent = name;
                if (avatarEl) avatarEl.textContent = (user.first_name?.[0] || user.email?.[0] || 'U').toUpperCase();
                if (typeof ShowcaseModules !== 'undefined') ShowcaseModules.updateNotificationBadge();
            } else {
                if (loginBtn) loginBtn.style.display = 'inline-block';
                if (loggedInArea) loggedInArea.style.display = 'none';
                if (nameEl) nameEl.textContent = '';
            }
        }

        // No inline login modal on dashboard; login is handled on the separate /login page.

        async function logout(e) {
            if (e) e.preventDefault();
            try {
                await apiFetch('/api/logout', { method: 'POST' });
            } catch (err) {
                console.warn('Logout request failed', err);
            }
            localStorage.removeItem('api_token');
            window.apiToken = null;
            window.currentUser = null;
            updateUserArea(null);
            showToast('Signed out successfully', 'success');
        }

        // Wire form submissions to our handlers (forms still have onsubmit attr; override to be safe)
        document.addEventListener('DOMContentLoaded', function () {
            AppUtils.initRouter();

            const page = AppUtils.resolveInitialPage();
            const nav = Array.from(document.querySelectorAll('.nav-item')).find(n => {
                const onclick = n.getAttribute('onclick') || '';
                return onclick.includes(`'${page}'`) || onclick.includes(`"${page}"`);
            });
            AppUtils.navigateToPage(page, nav || null, true);

            const txForm = document.getElementById('transactionForm'); if (txForm) txForm.onsubmit = saveTransaction;
            const invForm = document.getElementById('inventoryForm'); if (invForm) invForm.onsubmit = saveInventory;
            const staffForm = document.getElementById('staffForm'); if (staffForm) staffForm.onsubmit = saveStaff;
            const prodForm = document.getElementById('productionForm'); if (prodForm) prodForm.onsubmit = saveProduction;
            const procForm = document.getElementById('processForm'); if (procForm) procForm.onsubmit = saveProcess;
            const deptForm = document.getElementById('departmentForm'); if (deptForm) deptForm.onsubmit = saveDepartment;

            const loginBtn = document.getElementById('loginBtn'); if (loginBtn) loginBtn.onclick = () => window.location.href = '/login';
            const logoutBtn = document.getElementById('logoutBtn'); if (logoutBtn) logoutBtn.onclick = async (e) => {
                await logout(e);
                window.location.href = '/login';
            };

            checkAuth().then(authenticated => {
                if (authenticated) {
                    loadAllData();
                } else {
                    window.location.href = '/login';
                }
            });
        });
    </script>
    @if($errors->any())
        <script>
            // If validation errors were returned from the server when submitting the
            // Add Employee form, open the modal so the user sees and can fix them.
            document.addEventListener('DOMContentLoaded', function(){
                try { openStaffModal(); } catch(e) { console.error(e); }
            });
        </script>
    @endif
    <script src="{{ asset('js/showcase-modules.js') }}"></script>
</body>
</html>