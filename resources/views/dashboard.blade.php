<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dantata Foods - Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            border-radius: 4px;
            font-size: 13px;
            font-weight: 500;
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

        .staff-details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 40px;
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

        .btn-primary {
            background-color: #4a7cf6;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
        }

        .btn-primary:hover {
            background-color: #3a6ce6;
        }

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

        .btn-edit {
            background-color: #ffc107;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }

        .btn-edit:hover {
            background-color: #e0a800;
        }

        .btn-delete:hover {
            background-color: #c82333;
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

        .btn-secondary {
            background-color: #6c757d;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
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
            {{-- <li class="nav-item" onclick="navigateTo('overview', this)">
                <span>🏠</span> Overview
            </li> --}}
            <li class="nav-item active" onclick="navigateTo('ai-dashboard', this)">
                <span>📊</span> Dashboard
            </li>
            <li class="nav-item" onclick="navigateTo('oil-production', this)">
                <span>💧</span> Oil Production
            </li>
            <li class="nav-item" onclick="navigateTo('food-division', this)">
                <span>👥</span> Food Division
            </li>
            <li class="nav-item" onclick="navigateTo('inventory', this)">
                <span>📦</span> Inventory Management
            </li>
            <li class="nav-item" onclick="navigateTo('staff', this)">
                <span>📈</span> Staff Management
            </li>
            <li class="nav-item" onclick="navigateTo('process', this)">
                <span>⚙️</span> Process Management
            </li>
        </ul>
    </aside>

    <main class="main-content">
        <!-- Top-right user area (login/logout) -->
        <div id="userArea" style="position: fixed; top: 18px; right: 28px; z-index:1200;">
            <button id="loginBtn" class="btn-primary" style="display:none;">Login</button>
            <div id="loggedInArea" style="display:none; background: white; padding:8px 12px; border-radius:6px; box-shadow:0 1px 4px rgba(0,0,0,0.1);">
                <span id="currentUserName" style="margin-right:10px; font-weight:600;"></span>
                <button id="logoutBtn" class="btn-secondary">Logout</button>
            </div>
        </div>
        <br><br>

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
        <div id="ai-dashboard" class="page active">
            <div class="section-header">
                <h1 class="page-title" style="margin: 0;">Dashboard</h1>
                <button class="btn-primary" onclick="openTransactionModal()">Add Transaction</button>
            </div>
            
            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-label">Total Revenue</div>
                    <div class="metric-value" id="totalRevenue">$75,320</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Total Expenses</div>
                    <div class="metric-value" id="totalExpenses">$21,800</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Net Profit</div>
                    <div class="metric-value" id="netProfit">$53,520</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Profit Margin</div>
                    <div class="metric-value" id="profitMargin">71%</div>
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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="transactionsTableBody">
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Inventory Page -->
        <div id="inventory" class="page">
            <div class="section-header">
                <h1 class="page-title" style="margin: 0;">Inventory</h1>
                <button class="btn-primary" onclick="openInventoryModal()">Add Item</button>
            </div>
            
            <div class="inventory-grid">
                <div class="metric-card">
                    <div class="metric-label">Total Inventory</div>
                    <div class="metric-value" id="totalInventory">15,000</div>
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
    <div id="staff" class="page">
            <div class="section-header">
                <h1 class="page-title" style="margin: 0;">Staff Management</h1>
                <div style="display:flex;gap:8px;align-items:center;">
                    <button class="btn-primary" onclick="openStaffModal()">Add Employee</button>
                    <button class="btn-secondary" onclick="openRolesModal()">Manage Roles & Permissions</button>
                </div>
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
                <div class="chart-card">
                    <div class="chart-title">Staff Details</div>
                    <table>
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
                                    <td>{{ $department->users()->count() }}</td>
                                    <td>{{ round($department->users()->avg('age') ?? 0, 1) }}</td>
                                    <td>
                                        <button class="btn-edit" onclick="viewDepartment({{ $department->id }})">View</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="chart-card">
                    <div class="chart-title">Absenteeism</div>
                    <canvas id="absenteeismChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Food Division Page -->
        @php
            // Try to find a department that represents Food Division (case-insensitive match)
            $foodDept = null;
            foreach($departments ?? [] as $d) {
                if (isset($d->name) && stripos($d->name, 'food') !== false) { $foodDept = $d; break; }
            }
        @endphp

        <div id="food-division" class="page">
            <div class="section-header">
                <h1 class="page-title" style="margin: 0;">Food Division</h1>
                <button class="btn-primary" onclick="openStaffModal()">Add Employee</button>
            </div>

            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-label">Total employees (Food)</div>
                    <div class="metric-value">{{ $foodDept ? (isset($foodDept->users) ? count($foodDept->users) : ($foodDept->users_count ?? 0)) : 0 }}</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Active</div>
                    <div class="metric-value">{{ $foodDept ? (isset($foodDept->users) ? collect($foodDept->users)->where('is_active', 1)->count() : 'N/A') : 'N/A' }}</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Open Positions</div>
                    <div class="metric-value">0</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Avg Age</div>
                    <div class="metric-value">{{ $foodDept ? (isset($foodDept->users) ? round(collect($foodDept->users)->avg('age') ?: 0,1) : 'N/A') : 'N/A' }}</div>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-title">Food Division Staff</div>
                <table>
                    <thead>
                        <tr>
                            <th>Employee ID</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($foodDept && (isset($foodDept->users) ? count($foodDept->users) : ($foodDept->users_count ?? 0) > 0))
                            @foreach($foodDept->users as $u)
                                <tr>
                                    <td>{{ $u->employee_id ?? '' }}</td>
                                    <td>{{ ($u->first_name ?? '') . ' ' . ($u->last_name ?? '') }}</td>
                                    <td>{{ $u->role->display_name ?? $u->role->name ?? '' }}</td>
                                    <td>{{ $u->email ?? '' }}</td>
                                    <td>{!! ($u->is_active ?? false) ? '<span class="status-badge status-in-stock">Active</span>' : '<span class="status-badge status-pending">Inactive</span>' !!}</td>
                                    <td class="action-btns">
                                        <button class="btn-edit" onclick="openStaffModal();">Edit</button>
                                        <button class="btn-delete" onclick="/* implement delete user */">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6">No Food Division department found or no users assigned. Please ensure a department with "Food" in its name exists and has users.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Oil Production Page -->
        <div id="oil-production" class="page">
            <div class="section-header">
                <h1 class="page-title" style="margin: 0;">Oil Production</h1>
                <button class="btn-primary" onclick="openProductionModal()">Add Production</button>
            </div>
            <p style="color: #666; margin-bottom: 30px;">Overview of oil production</p>
            
            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-label">Total Production</div>
                    <div class="metric-value">50,000<span style="font-size: 20px;">L</span></div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Protection Today</div>
                    <div class="metric-value">1,200<span style="font-size: 20px;">L</span></div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Downtime</div>
                    <div class="metric-value">3<span style="font-size: 20px;">hrs</span></div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Efficiency</div>
                    <div class="metric-value">92%</div>
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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productionTableBody">
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Process Management Page -->
        <div id="process" class="page">
            <div class="section-header">
                <h1 class="page-title" style="margin: 0;">Process Management</h1>
                <button class="btn-primary" onclick="openProcessModal()">New Process</button>
            </div>
            
            <div class="process-list">
                <div class="chart-title" style="margin-bottom: 20px;">All Processes</div>
                <div class="process-item process-header">
                    <div>Name</div>
                    <div>Status</div>
                    <div>Assigned To</div>
                    <div>Due Date</div>
                    <div>Actions</div>
                </div>
                <div id="processTableBody">
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
                    <label>Amount ($)</label>
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
                    <label>Employee ID</label>
                    <input type="text" name="employee_id" required>
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
                    <label>Password</label>
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

        <!-- Roles & Permissions Modal -->
        <div id="rolesModal" class="modal">
            <div class="modal-content" style="max-width:900px;">
                <div class="modal-header">Roles & Permissions</div>
                <div style="display:flex;gap:16px;align-items:flex-start;">
                    <div style="flex:0 0 260px; border-right:1px solid #eee;padding-right:12px;">
                        <div style="font-weight:600;margin-bottom:8px;">Roles</div>
                        <div id="rolesList" style="max-height:360px;overflow:auto;"></div>
                        <div style="margin-top:12px;">
                            <input type="text" id="newRoleName" placeholder="New role display name" style="width:100%;padding:6px;margin-bottom:6px;" />
                            <button class="btn-primary" onclick="createRole()">Create Role</button>
                        </div>
                    </div>
                    <div style="flex:1; padding-left:12px;">
                        <div id="rolePermissionsArea">
                                <div style="font-weight:600;margin-bottom:8px;" id="selectedRoleTitle">Select a role to edit permissions</div>
                                <div id="permissionsContainer" style="max-height:260px;overflow:auto;border:1px solid #f0f0f0;padding:8px;border-radius:6px;background:#fff;"></div>
                                <div style="margin-top:10px;">
                                    <div style="font-weight:600;margin-bottom:6px;">Role Members</div>
                                    <div id="roleMembersList" style="max-height:120px;overflow:auto;border:1px dashed #eee;padding:8px;border-radius:6px;background:#fafafa;"></div>
                                </div>
                            </div>
                        <div style="margin-top:12px;text-align:right;">
                            <button class="btn-secondary" onclick="closeModal('rolesModal')">Close</button>
                            <button class="btn-primary" onclick="saveRolePermissions()">Save Permissions</button>
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

    <script>
        // Expose active users map to the client so JS can resolve user IDs to names
        window.allUsers = {!! json_encode(($users ?? collect())->mapWithKeys(function($u){ return [$u->id => ($u->first_name . ' ' . $u->last_name)]; })) !!};
        // Expose departments (including their users) for quick client-side viewing
        window.allDepartments = {!! json_encode($departments ?? collect()) !!};

    </script>

    <script>
        // Navigation helper used by sidebar nav items. Shows the requested page and marks nav active.
        function navigateTo(pageId, el) {
            try {
                // Mark nav item active
                document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
                if (el && el.classList) el.classList.add('active');

                // Show the page
                document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
                const target = document.getElementById(pageId);
                if (target) target.classList.add('active');

                // Scroll to top for better UX
                window.scrollTo({top:0, behavior:'smooth'});
            } catch (e) {
                console.error('navigateTo error', e);
            }
        }

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

        function openInventoryModal() {
            try {
                document.getElementById('inventoryForm')?.reset();
                // clear edit state
                window.currentInventoryEditId = null;
                const header = document.querySelector('#inventoryModal .modal-header');
                if (header) header.textContent = 'Add Inventory Item';
                document.getElementById('inventoryModal')?.classList.add('active');
            } catch (e) { console.error('openInventoryModal', e); }
        }

        function openStaffModal() {
            try {
                document.getElementById('staffForm')?.reset();
                document.getElementById('staffModal')?.classList.add('active');
            } catch (e) { console.error('openStaffModal', e); }
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
                if (el) el.classList.remove('active');
            } catch (e) { console.error('closeModal', e); }
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function apiFetch(path, opts = {}) {
            const token = window.apiToken || localStorage.getItem('api_token');
            const headers = Object.assign({
                'Accept': 'application/json',
            }, opts.headers || {});

            if (token) {
                headers['Authorization'] = 'Bearer ' + token;
            }

            // If sending a body object, ensure JSON header
            if (opts.body && typeof opts.body === 'object' && !(opts.body instanceof FormData)) {
                headers['Content-Type'] = 'application/json';
                opts.body = JSON.stringify(opts.body);
            }

            return fetch(path, Object.assign({
                credentials: 'same-origin',
                headers
            }, opts)).then(async res => {
                if (!res.ok) {
                    const text = await res.text();
                    // if unauthorized, clear token
                    if (res.status === 401) {
                        localStorage.removeItem('api_token');
                        window.apiToken = null;
                    }
                    throw new Error(`API ${path} returned ${res.status}: ${text}`);
                }
                return res.json().then(j => j.data ?? j);
            });
        }

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
                renderTransactions(transactions || []);
                renderInventory(inventory || []);
                renderDepartments(departments || []);
                renderProduction(production || []);
                renderProductionStats(productionStats || {});
                renderProcesses(processes || []);

                initChartsWithData(metrics, transactions, inventory, production);
            } catch (err) {
                console.error('Failed to load dashboard data', err);
            }
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
                    <div style="color:#666;font-size:13px;margin-top:4px;">${escapeHtml(it.summary)} ${it.date ? ' • ' + escapeHtml(it.date.split('T')[0]) : ''}</div>
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
            if (pm !== null && pm !== undefined) document.getElementById('profitMargin').textContent = (pm + '%');
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

        function formatCurrency(v) {
            if (v === null || v === undefined) return '-';
            return '$' + Number(v).toLocaleString();
        }

        function renderTransactions(list) {
            const tbody = document.getElementById('transactionsTableBody');
            tbody.innerHTML = (list || []).map(t => {
                const desc = t.description || t.title || t.name || '-';
                const amount = t.amount ?? t.total ?? 0;
                const date = t.transaction_date || t.date || t.created_at || '-';
                // Build action buttons based on permission flags returned by API
                // If the API doesn't include permission flags, default to showing
                // View/Edit so users can inspect and edit items in the UI. Delete
                // is shown only when the API explicitly allows it (to avoid
                // exposing a delete button that would 403 immediately).
                let actions = '';
                const canUpdate = (typeof t.can_update === 'undefined') ? true : Boolean(t.can_update);
                const canView = (typeof t.can_view === 'undefined') ? true : Boolean(t.can_view);
                const canDelete = (typeof t.can_delete === 'undefined') ? false : Boolean(t.can_delete);

                if (canUpdate) actions += `<button class="btn-edit" onclick="editTransaction(${t.id})">Edit</button>`;
                if (canView) actions += `<button class="btn-secondary" onclick="viewTransaction(${t.id})">View</button>`;
                if (canDelete) actions += `<button class="btn-delete" onclick="deleteTransaction(${t.id})">Delete</button>`;

                return `
                    <tr>
                        <td>${escapeHtml(desc)}</td>
                        <td>${formatCurrency(amount)}</td>
                        <td>${date}</td>
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
                        <td>${Number(i.stock_quantity || 0).toLocaleString()}</td>
                        <td>${Number(i.reorder_level || 0).toLocaleString()}</td>
                        <td>${escapeHtml(i.unit_of_measure || '')}</td>
                        <td><span class="status-badge ${statusClass}">${escapeHtml(i.status || '')}</span></td>
                        <td class="action-btns">
                            <button class="btn-edit" onclick="editInventory(${i.id})">Edit</button>
                            <button class="btn-secondary" onclick="viewInventory(${i.id})">View</button>
                            <button class="btn-delete" onclick="deleteInventory(${i.id})">Delete</button>
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
                if (totalValue > 0) totalEl.textContent = '$' + totalValue.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                else totalEl.textContent = totalUnits.toLocaleString();
            }
            if (currentEl) currentEl.textContent = totalUnits.toLocaleString();
            if (reorderEl) reorderEl.textContent = belowReorder.toLocaleString();
            if (turnoverEl) turnoverEl.textContent = turnover ? turnover.toFixed(2) : '0.00';
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
                const priceEl = document.getElementById('viewInvPrice'); if (priceEl) priceEl.textContent = (item.unit_price !== undefined && item.unit_price !== null) ? '$' + Number(item.unit_price).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) : '';
                const statusEl = document.getElementById('viewInvStatus'); if (statusEl) statusEl.textContent = item.status || '';

                const mvEl = document.getElementById('viewInvMovements');
                if (mvEl) {
                    const movements = item.movements || item.movements_data || [];
                    if (Array.isArray(movements) && movements.length) {
                        mvEl.innerHTML = '<ul style="padding-left:16px;margin:0;">' + movements.map(m => `
                            <li>${escapeHtml(m.movement_type || m.type || '')} • ${escapeHtml((m.quantity ?? m.qty ?? ''))} • ${(m.date || m.movement_date || '').split('T')[0] || ''}${m.note ? ' • ' + escapeHtml(m.note) : ''}</li>
                        `).join('') + '</ul>';
                    } else {
                        mvEl.textContent = 'No movements recorded.';
                    }
                }

                modal.classList.add('active');
            } catch (err) {
                console.error('Failed to load inventory', err);
                try {
                    const json = JSON.parse(err.message.replace(/^API .*?:\\s*/,''));
                    if (json && json.message) {
                        alert('Failed to load inventory: ' + json.message);
                        return;
                    }
                } catch (e) {}
                alert('Failed to load inventory item');
            }
        }

        async function editInventory(id) {
            try {
                const item = await apiFetch(`/api/inventory/${id}`);
                // prefill form
                document.getElementById('inventoryItem') .value = item.name || '';
                document.getElementById('inventoryStock').value = item.stock_quantity ?? 0;
                document.getElementById('inventoryReorder').value = item.reorder_level ?? 0;
                // optional fields: description, maximum_level, unit_of_measure, unit_price
                if (document.getElementById('inventoryDescription')) document.getElementById('inventoryDescription').value = item.description || '';
                if (document.getElementById('inventoryMax')) document.getElementById('inventoryMax').value = item.maximum_level ?? '';
                if (document.getElementById('inventoryUnit')) document.getElementById('inventoryUnit').value = item.unit_of_measure || '';
                if (document.getElementById('inventoryPrice')) document.getElementById('inventoryPrice').value = item.unit_price ?? '';
                if (document.getElementById('inventoryStatus')) document.getElementById('inventoryStatus').value = item.status || 'in_stock';

                // update modal header to editing mode
                const header = document.querySelector('#inventoryModal .modal-header');
                if (header) header.textContent = 'Edit Inventory Item';

                window.currentInventoryEditId = id;
                document.getElementById('inventoryModal')?.classList.add('active');
            } catch (err) {
                console.error('Failed to load inventory item', err);
                alert('Failed to load inventory item');
            }
        }

        async function editProduction(id) {
            try {
                const rec = await apiFetch(`/api/production/${id}`);
                document.getElementById('productionDate').value = rec.production_date ? rec.production_date.split('T')[0] : (rec.production_date || '');
                document.getElementById('productionQuantity').value = rec.quantity ?? '';
                document.getElementById('productionEfficiency').value = rec.efficiency_percentage ?? rec.efficiency ?? '';
                if (document.getElementById('productionDowntime')) document.getElementById('productionDowntime').value = rec.downtime_hours ?? 0;
                if (document.getElementById('productionNotes')) document.getElementById('productionNotes').value = rec.notes || '';

                window.currentProductionEditId = id;
                const header = document.querySelector('#productionModal .modal-header'); if (header) header.textContent = 'Edit Production Record';
                document.getElementById('productionModal')?.classList.add('active');
            } catch (err) {
                console.error('Failed to load production record', err);
                alert('Failed to load production record');
            }
        }

        async function deleteProduction(id) {
            if (!confirm('Delete this production record? This action can be undone.')) return;
            try {
                const res = await apiFetch(`/api/production/${id}`, { method: 'DELETE' });
                await loadAllData();
                if (res && res.message) alert(res.message);
            } catch (err) {
                console.error('Delete production failed', err);
                alert('Failed to delete production record');
            }
        }

        async function deleteInventory(id) {
            if (!confirm('Delete this inventory item? This action can be undone.')) return;
            try {
                const res = await apiFetch(`/api/inventory/${id}`, { method: 'DELETE' });
                await loadAllData();
                if (res && res.message) alert(res.message);
            } catch (err) {
                console.error('Delete failed', err);
                alert('Failed to delete item');
            }
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
                                    <button class="btn-edit" onclick="viewDepartment(${d.id})">View</button>
                                </td>
                            </tr>
                        `;
                    }).join('');
        }

        // Show department details and its users in a modal. Attempts to use
        // the server-provided `window.allDepartments` map first, otherwise falls
        // back to fetching the department via the API.
        async function viewDepartment(id) {
            try {
                let dept = null;
                if (window.allDepartments) {
                    // allDepartments may be an array or an object map
                    if (Array.isArray(window.allDepartments)) {
                        dept = window.allDepartments.find(x => x.id == id);
                    } else if (window.allDepartments[id]) {
                        dept = window.allDepartments[id];
                    }
                }
                if (!dept) {
                    // try API
                    dept = await apiFetch(`/api/departments/${id}`);
                }

                const header = document.getElementById('departmentModalHeader');
                const desc = document.getElementById('departmentDescription');
                const body = document.getElementById('departmentUsersBody');
                if (header) header.textContent = dept.name || 'Department';
                if (desc) desc.textContent = dept.description || '';

                const users = dept.users || dept.users_data || [];
                body.innerHTML = (users || []).map(u => {
                    const role = u.role ? (u.role.display_name || u.role.name) : (u.role_display_name || '');
                    return `<tr><td style="padding:8px 6px;">${escapeHtml((u.first_name||'') + ' ' + (u.last_name||''))}</td><td style="padding:8px 6px;">${escapeHtml(u.email || '')}</td><td style="padding:8px 6px;">${escapeHtml(role || '')}</td></tr>`;
                }).join('');

                document.getElementById('departmentModal')?.classList.add('active');
            } catch (err) {
                console.error('Failed to load department', err);
                alert('Failed to load department details');
            }
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
                        <div style="padding:8px;border-bottom:1px solid #f6f6f6;cursor:pointer;" onclick="selectRole(${r.id})">
                            <div style="font-weight:600;">${escapeHtml(r.display_name || r.name)}</div>
                            <div style="font-size:12px;color:#666;">${escapeHtml((r.description || ''))}</div>
                        </div>
                    `).join('');
                }

                // clear permissions area and members
                document.getElementById('permissionsContainer').innerHTML = '';
                document.getElementById('roleMembersList').innerHTML = '';
                document.getElementById('selectedRoleTitle').textContent = 'Select a role to edit permissions';
            } catch (err) {
                console.error('Failed to load roles/permissions', err);
                alert('Failed to load roles or permissions');
            }
        }

        async function selectRole(id) {
            try {
                selectedRoleId = id;
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
                    html += `<div style="margin-bottom:10px;"><div style="font-weight:700;margin-bottom:6px;">${escapeHtml(mod)}</div>`;
                    html += '<div style="display:flex;flex-direction:column;gap:6px;">';
                    for (const p of grouped[mod]) {
                        const has = !!rolePerms[p.id];
                        const access = rolePerms[p.id]?.pivot?.access_level || rolePerms[p.id]?.access_level || 'view';
                        html += `
                            <label style="display:flex;align-items:center;gap:8px;padding:6px;border:1px solid #fafafa;border-radius:4px;">
                                <input type="checkbox" data-perm-id="${p.id}" ${has ? 'checked' : ''} onchange="onPermToggle(this)">
                                <div style="flex:1;"><div style="font-weight:600;">${escapeHtml(p.display_name || p.name)}</div><div style="font-size:12px;color:#666;">${escapeHtml(p.description || '')}</div></div>
                                <select data-perm-id-select="${p.id}" style="width:120px;" ${has ? '' : 'disabled'}>
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
                        return `<div style="display:flex;align-items:center;justify-content:space-between;padding:6px;border-bottom:1px solid #f6f6f6;">
                            <div style="flex:1;">${escapeHtml((u.first_name||'') + ' ' + (u.last_name||''))} <div style="font-size:12px;color:#666;">${escapeHtml(u.email||'')}</div></div>
                            <div style="margin-left:12px;">
                                ${has ? `<button class="btn-secondary" onclick="removeRoleFromUser(${u.id})">Remove</button>` : `<button class="btn-primary" onclick="assignRoleToUser(${u.id})">Assign</button>`}
                            </div>
                        </div>`;
                    }).join('');
                    membersEl.innerHTML = memberHtml || '<div style="color:#666;">No users found.</div>';
                }
            } catch (err) {
                console.error('Failed to load role details', err);
                alert('Failed to load role details');
            }
        }

        async function assignRoleToUser(userId) {
            if (!selectedRoleId) { alert('Select a role first'); return; }
            try {
                const res = await apiFetch(`/api/users/${userId}`, { method: 'PUT', body: { role_id: selectedRoleId } });
                // update local cache and UI
                const u = usersCache.find(x => x.id == userId);
                if (u) u.role_id = selectedRoleId;
                await selectRole(selectedRoleId);
                if (res && res.id) alert('Role assigned to user');
            } catch (err) {
                console.error('Failed to assign role', err);
                alert('Failed to assign role to user');
            }
        }

        async function removeRoleFromUser(userId) {
            if (!selectedRoleId) { alert('Select a role first'); return; }
            try {
                const res = await apiFetch(`/api/users/${userId}`, { method: 'PUT', body: { role_id: null } });
                const u = usersCache.find(x => x.id == userId);
                if (u) u.role_id = null;
                await selectRole(selectedRoleId);
                if (res && res.id) alert('Role removed from user');
            } catch (err) {
                console.error('Failed to remove role', err);
                alert('Failed to remove role from user');
            }
        }

        function onPermToggle(cb) {
            const pid = cb.getAttribute('data-perm-id');
            const sel = document.querySelector(`select[data-perm-id-select="${pid}"]`);
            if (sel) sel.disabled = !cb.checked;
        }

        async function saveRolePermissions() {
            if (!selectedRoleId) { alert('No role selected'); return; }
            try {
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

                const res = await apiFetch(`/api/roles/${selectedRoleId}/permissions`, { method: 'POST', body: payload });
                // refresh roles cache and UI
                await loadRolesAndPermissions();
                if (res && res.message) alert(res.message);
            } catch (err) {
                console.error('Failed to save role permissions', err);
                alert('Failed to save permissions');
            }
        }

        async function createRole() {
            try {
                const name = (document.getElementById('newRoleName')?.value || '').trim();
                if (!name) { alert('Enter a role name'); return; }
                // Use API to create role - POST /api/roles
                const res = await apiFetch('/api/roles', { method: 'POST', body: { display_name: name, name: name.toLowerCase().replace(/\s+/g,'_') } });
                document.getElementById('newRoleName').value = '';
                await loadRolesAndPermissions();
                if (res && res.id) selectRole(res.id);
            } catch (err) {
                console.error('Failed to create role', err);
                alert('Failed to create role');
            }
        }

        function renderProduction(list) {
            const tbody = document.getElementById('productionTableBody');
            tbody.innerHTML = (list || []).map(p => {
                const date = p.production_date || p.date || p.created_at || '';
                const qty = Number(p.quantity || p.amount || 0).toLocaleString();
                const eff = (p.efficiency_percentage ?? p.efficiency ?? p.efficiency_percent) + '%';
                const downtime = (p.downtime_hours ?? 0) + ' hrs';
                return `
                <tr>
                    <td>${date}</td>
                    <td>${qty}</td>
                    <td>${eff}</td>
                    <td>${downtime}</td>
                    <td class="action-btns">
                        <button class="btn-edit" onclick="editProduction(${p.id})">Edit</button>
                        <button class="btn-secondary" onclick="viewProduction(${p.id})">View</button>
                        <button class="btn-delete" onclick="deleteProduction(${p.id})">Delete</button>
                    </td>
                </tr>
            `}).join('');
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
            container.innerHTML = (list || []).map(p => {
                const status = p.status || 'Pending';
                const statusClass = status === 'Completed' ? 'status-completed' : (status.toLowerCase().includes('progress') ? 'status-progress' : 'status-pending');
                // Resolve assigned user name: prefer API-provided name, otherwise map by id using window.allUsers
                const assignedName = p.assigned_to_name || (p.assigned_to ? (window.allUsers && window.allUsers[p.assigned_to] ? window.allUsers[p.assigned_to] : p.assigned_to) : '');
                return `
                    <div class="process-item">
                        <div>${escapeHtml(p.name || p.title || '')}</div>
                        <div><span class="status-badge ${statusClass}">${escapeHtml(status)}</span></div>
                        <div><span class="user-avatar"></span>${escapeHtml(assignedName)}</div>
                        <div>${escapeHtml(p.due_date || p.dueDate || '')}</div>
                        <div class="action-btns">
                            <button class="btn-edit" onclick="editProcess(${p.id})">Edit</button>
                            <button class="btn-delete" onclick="deleteProcess(${p.id})">Delete</button>
                        </div>
                    </div>
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
                alert('Failed to load process');
            }
        }

        async function deleteProcess(id) {
            if (!confirm('Delete this process? This action cannot be undone.')) return;
            try {
                const res = await apiFetch(`/api/processes/${id}`, { method: 'DELETE' });
                await loadAllData();
                if (res && res.message) alert(res.message);
            } catch (err) {
                console.error('Failed to delete process', err);
                try {
                    const json = JSON.parse(err.message.replace(/^API .*?:\s*/,''));
                    if (json && json.message) alert('Failed to delete process: ' + json.message);
                    else alert('Failed to delete process');
                } catch (e) {
                    alert('Failed to delete process');
                }
            }
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
            try {
                if (window.currentTransactionEditId) {
                    await apiFetch(`/api/transactions/${window.currentTransactionEditId}`, { method: 'PUT', body: payload });
                } else {
                    await apiFetch('/api/transactions', {method: 'POST', body: payload});
                }
                await loadAllData();
                closeModal('transactionModal');
                window.currentTransactionEditId = null;
                const header = document.querySelector('#transactionModal .modal-header'); if (header) header.textContent = 'Add Transaction';
            } catch (err) { console.error(err); alert('Failed to save transaction'); }
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
                alert('Failed to load transaction');
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
                // Try to surface API message if available
                try {
                    const json = JSON.parse(err.message.replace(/^API .*?:\s*/,''));
                    if (json && json.message) {
                        alert('Failed to load transaction: ' + json.message);
                        return;
                    }
                } catch(e){}
                alert('Failed to load transaction');
            }
        }

        // Delete a transaction
        async function deleteTransaction(id) {
            if (!confirm('Delete this transaction? This action cannot be undone.')) return;
            try {
                const res = await apiFetch(`/api/transactions/${id}`, { method: 'DELETE' });
                await loadAllData();
                if (res && res.message) alert(res.message);
            } catch (err) {
                console.error('Failed to delete transaction', err);
                try {
                    const json = JSON.parse(err.message.replace(/^API .*?:\s*/,''));
                    if (json && json.message) alert('Failed to delete transaction: ' + json.message);
                    else alert('Failed to delete transaction');
                } catch (e) {
                    alert('Failed to delete transaction');
                }
            }
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
                status: document.getElementById('inventoryStatus').value
            };

            try {
                let res;
                if (window.currentInventoryEditId) {
                    res = await apiFetch(`/api/inventory/${window.currentInventoryEditId}`, { method: 'PUT', body: payload });
                } else {
                    res = await apiFetch('/api/inventory', { method: 'POST', body: payload });
                }

                await loadAllData();
                window.currentInventoryEditId = null;
                closeModal('inventoryModal');

                // Show success feedback
                if (res && (res.id || res.message)) {
                    alert('Inventory saved successfully.');
                } else {
                    // fallback
                    alert('Inventory saved.');
                }
            } catch (err) {
                console.error(err);
                // err.message contains the API response text (may be JSON). Try to parse it.
                try {
                    const json = JSON.parse(err.message.replace(/^API .*?:\s*/,''));
                    if (json && json.errors) {
                        const messages = Object.values(json.errors).flat().join('\n');
                        alert('Failed to save inventory item:\n' + messages);
                    } else if (json && json.message) {
                        alert('Failed to save inventory item: ' + json.message);
                    } else {
                        alert('Failed to save inventory item');
                    }
                } catch (parseErr) {
                    alert('Failed to save inventory item: ' + (err.message || err));
                }
            }
        }

        async function saveStaff(e) {
            // Allow the form to post normally to a non-API web route (we wired
            // `action="{{ route('dashboard.users.store') }}"` in the Blade form).
            // Only intercept and send AJAX if the form action targets an API path.
            const form = document.getElementById('staffForm');
            if (!form) return true;

            const action = (form.getAttribute('action') || form.action || '').toString();
            const isApi = action.includes('/api/') || action.startsWith('http') && action.includes('/api/');

            if (!isApi) {
                // Let the browser submit the form normally to the web route so server
                // side validation & redirect/flash works (this avoids missing fields
                // like password_confirmation or hire_date that the API expects).
                return true; // do not call preventDefault
            }

            // Otherwise handle via AJAX to the API
            e.preventDefault();
            const fd = new FormData(form);
            const payload = Object.fromEntries(fd.entries());
            try {
                await apiFetch('/api/users', {method: 'POST', body: payload});
                await loadAllData();
                closeModal('staffModal');
            } catch (err) {
                console.error(err);
                alert('Failed to save employee');
            }
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

            try {
                let res;
                if (window.currentProductionEditId) {
                    res = await apiFetch(`/api/production/${window.currentProductionEditId}`, { method: 'PUT', body: payload });
                } else {
                    res = await apiFetch('/api/production', { method: 'POST', body: payload });
                }

                await loadAllData();
                window.currentProductionEditId = null;
                closeModal('productionModal');
                alert('Production record saved successfully');
                return res;
            } catch (err) {
                console.error(err);
                try {
                    const json = JSON.parse(err.message.replace(/^API .*?:\s*/,''));
                    if (json && json.errors) {
                        const messages = Object.values(json.errors).flat().join('\n');
                        alert('Failed to save production:\n' + messages);
                    } else if (json && json.message) {
                        alert('Failed to save production: ' + json.message);
                    } else {
                        alert('Failed to save production');
                    }
                } catch (parseErr) {
                    alert('Failed to save production: ' + (err.message || err));
                }
            }
        }

        async function viewProduction(id) {
            try {
                const p = await apiFetch(`/api/production/${id}`);
                if (!p) throw new Error('Not found');

                // Create or populate the production view modal
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

                // Populate fields (use safe fallbacks)
                const titleEl = document.getElementById('viewProdTitle'); if (titleEl) titleEl.textContent = p.title || `Production ${p.production_date || ''}`;
                const dateEl = document.getElementById('viewProdDate'); if (dateEl) dateEl.textContent = (p.production_date || p.date || p.created_at || '').split('T')[0] || '';
                const qtyEl = document.getElementById('viewProdQuantity'); if (qtyEl) qtyEl.textContent = (p.quantity !== undefined ? Number(p.quantity).toLocaleString() + ' L' : (p.amount !== undefined ? Number(p.amount).toLocaleString() + ' L' : ''));
                const effEl = document.getElementById('viewProdEfficiency'); if (effEl) effEl.textContent = (p.efficiency_percentage ?? p.efficiency ?? p.efficiency_percent) ? String(p.efficiency_percentage ?? p.efficiency ?? p.efficiency_percent) + '%' : '';
                const downEl = document.getElementById('viewProdDowntime'); if (downEl) downEl.textContent = (p.downtime_hours !== undefined ? String(p.downtime_hours) + ' hrs' : '');
                const notesEl = document.getElementById('viewProdNotes'); if (notesEl) notesEl.textContent = p.notes || '';

                modal.classList.add('active');
            } catch (err) {
                console.error('Failed to load production', err);
                try {
                    const json = JSON.parse(err.message.replace(/^API .*?:\\s*/,''));
                    if (json && json.message) {
                        alert('Failed to load production: ' + json.message);
                        return;
                    }
                } catch (e) {}
                alert('Failed to load production');
            }
        }


        async function saveProcess(e) {
            e.preventDefault();
            const payload = {
                name: document.getElementById('processName').value,
                status: document.getElementById('processStatus').value,
                assigned_to: document.getElementById('processAssigned').value,
                due_date: document.getElementById('processDueDate').value
            };
            try {
                let res;
                if (window.currentProcessEditId) {
                    res = await apiFetch(`/api/processes/${window.currentProcessEditId}`, { method: 'PUT', body: payload });
                } else {
                    res = await apiFetch('/api/processes', {method: 'POST', body: payload});
                }
                await loadAllData();
                closeModal('processModal');
                window.currentProcessEditId = null;
                const header = document.querySelector('#processModal .modal-header'); if (header) header.textContent = 'Add Process';
            } catch (err) { console.error(err); alert('Failed to save process'); }
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
            if (user) {
                if (loginBtn) loginBtn.style.display = 'none';
                if (loggedInArea) loggedInArea.style.display = 'inline-flex';
                if (nameEl) nameEl.textContent = user.first_name ? `${user.first_name} ${user.last_name}` : (user.name || user.email || 'User');
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
                await apiFetch('/api/logout', {method: 'POST'});
            } catch (err) {
                console.warn('Logout request failed', err);
            }
            localStorage.removeItem('api_token');
            window.apiToken = null;
            window.currentUser = null;
            updateUserArea(null);
            // Optionally reload data to show public view
            await loadAllData();
        }

        // Wire form submissions to our handlers (forms still have onsubmit attr; override to be safe)
        document.addEventListener('DOMContentLoaded', function(){
            // Override inline handlers
            const txForm = document.getElementById('transactionForm'); if (txForm) txForm.onsubmit = saveTransaction;
            const invForm = document.getElementById('inventoryForm'); if (invForm) invForm.onsubmit = saveInventory;
            const staffForm = document.getElementById('staffForm'); if (staffForm) staffForm.onsubmit = saveStaff;
            const prodForm = document.getElementById('productionForm'); if (prodForm) prodForm.onsubmit = saveProduction;
            const procForm = document.getElementById('processForm'); if (procForm) procForm.onsubmit = saveProcess;

            // Login button should take user to the dedicated login page; logout still calls API.
            const loginBtn = document.getElementById('loginBtn'); if (loginBtn) loginBtn.onclick = () => window.location.href = '/login';
            const logoutBtn = document.getElementById('logoutBtn'); if (logoutBtn) logoutBtn.onclick = async (e) => {
                await logout(e);
                // redirect to login page after logout
                window.location.href = '/login';
            };

            // Check auth first, then load data. If unauthenticated, redirect to the dedicated login page.
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
</body>
</html>