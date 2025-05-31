<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - Document Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --primary-light: rgba(79, 70, 229, 0.1);
            --success-color: #10b981;
            --danger-color: #ef4444;
            --border-radius: 0.5rem;
            --box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --sidebar-width: 280px;
            --header-height: 70px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
            color: #1f2937;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* Layout */
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background-color: white;
            border-right: 1px solid #e5e7eb;
            position: fixed;
            height: 100vh;
            z-index: 100;
            transition: all 0.3s ease;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .sidebar-content {
            padding: 1rem 0;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu-header {
            padding: 0.75rem 1.5rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            font-weight: 600;
        }

        .sidebar-menu-item {
            padding: 0.5rem 1.5rem;
        }

        .sidebar-menu-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: #4b5563;
            text-decoration: none;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .sidebar-menu-link:hover {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }

        .sidebar-menu-link.active {
            background-color: var(--primary-light);
            color: var(--primary-color);
            font-weight: 600;
        }

        .sidebar-menu-icon {
            margin-right: 0.75rem;
        }

        .file-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .file-item {
            padding: 0.5rem 1.5rem;
        }

        .file-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: #4b5563;
            text-decoration: none;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
            font-size: 0.875rem;
            border-left: 3px solid transparent;
        }

        .file-link:hover {
            background-color: #f3f4f6;
            color: #1f2937;
            border-left-color: #d1d5db;
        }

        .file-link.active {
            background-color: var(--primary-light);
            color: var(--primary-color);
            border-left-color: var(--primary-color);
        }

        .file-icon {
            margin-right: 0.75rem;
            color: #9ca3af;
        }

        .file-name {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .file-date {
            font-size: 0.75rem;
            color: #9ca3af;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding-top: var(--header-height);
            transition: all 0.3s ease;
        }

        /* Header */
        .header {
            position: fixed;
            top: 0;
            right: 0;
            left: var(--sidebar-width);
            height: var(--header-height);
            background-color: white;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            z-index: 99;
            transition: all 0.3s ease;
        }

        .header-left {
            display: flex;
            align-items: center;
        }

        .toggle-sidebar {
            display: none;
            background: none;
            border: none;
            color: #6b7280;
            cursor: pointer;
            padding: 0.5rem;
            margin-right: 1rem;
            border-radius: 0.375rem;
        }

        .toggle-sidebar:hover {
            background-color: #f3f4f6;
            color: #1f2937;
        }

        .page-title {
            font-weight: 600;
            font-size: 1.25rem;
            color: #111827;
            margin: 0;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .admin-profile:hover {
            background-color: #f3f4f6;
        }

        .admin-avatar {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e5e7eb;
        }

        .admin-info {
            display: flex;
            flex-direction: column;
        }

        .admin-name {
            font-weight: 600;
            font-size: 0.875rem;
            color: #111827;
        }

        .admin-role {
            font-size: 0.75rem;
            color: #6b7280;
        }

        /* Content */
        .content {
            padding: 1.5rem;
        }

        .content-header {
            margin-bottom: 1.5rem;
        }

        .content-title {
            font-weight: 600;
            font-size: 1.5rem;
            color: #111827;
            margin-bottom: 0.5rem;
        }

        .content-subtitle {
            color: #6b7280;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            font-size: 1.125rem;
            color: #111827;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-header-actions {
            display: flex;
            gap: 0.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Upload Form Styles */
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #374151;
        }

        .form-control {
            border-radius: 0.375rem;
            border: 1px solid #d1d5db;
            padding: 0.625rem 0.75rem;
            font-size: 0.95rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }

        .form-control.is-invalid {
            border-color: var(--danger-color);
        }

        .text-muted {
            color: #6b7280 !important;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 0.375rem;
            font-weight: 500;
            padding: 0.625rem 1.25rem;
            transition: all 0.2s ease;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        .progress-container {
            margin: 1.5rem 0;
        }

        .progress {
            height: 0.5rem;
            border-radius: 1rem;
            background-color: #e5e7eb;
            margin-bottom: 0.5rem;
            overflow: hidden;
        }

        .progress-bar {
            background-color: var(--primary-color);
            border-radius: 1rem;
            transition: width 0.3s ease;
        }

        .progress-text {
            font-size: 0.875rem;
            color: #6b7280;
            text-align: right;
        }

        #upload-status {
            display: none;
            margin-top: 1.5rem;
            border-radius: var(--border-radius);
            padding: 1.25rem;
            animation: fadeIn 0.3s ease-in-out;
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #065f46;
        }

        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #b91c1c;
        }

        #document-details {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }

        .file-input-wrapper {
            position: relative;
            margin-bottom: 1rem;
        }

        .file-input-wrapper .form-control {
            padding-right: 7rem;
        }

        .file-count {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background-color: #e5e7eb;
            color: #4b5563;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 500;
        }

        /* File Details */
        .file-details {
            display: none;
        }

        .file-details-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .file-details-icon {
            width: 3rem;
            height: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--primary-light);
            color: var(--primary-color);
            border-radius: 0.5rem;
            margin-right: 1rem;
        }

        .file-details-info {
            flex: 1;
        }

        .file-details-title {
            font-weight: 600;
            font-size: 1.25rem;
            color: #111827;
            margin-bottom: 0.25rem;
        }

        .file-details-meta {
            display: flex;
            gap: 1rem;
            color: #6b7280;
            font-size: 0.875rem;
        }

        .file-details-meta-item {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .file-details-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .file-details-content {
            margin-top: 1.5rem;
        }

        .file-preview {
            width: 100%;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .file-preview img {
            width: 100%;
            height: auto;
        }

        .file-preview-placeholder {
            height: 200px;
            background-color: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 1.5rem;
        }

        .stat-card-title {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 0.5rem;
        }

        .stat-card-value {
            font-size: 1.5rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 0.5rem;
        }

        .stat-card-description {
            font-size: 0.875rem;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .stat-card-icon {
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--primary-light);
            color: var(--primary-color);
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .header {
                left: 0;
            }

            .toggle-sidebar {
                display: block;
            }

            .stats-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .header {
                padding: 0 1rem;
            }

            .content {
                padding: 1rem;
            }

            .admin-info {
                display: none;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                    </svg>
                    <span>DocManager</span>
                </div>
            </div>
            <!-- Sidebar Content -->
            <div class="sidebar-content">

                <ul class="sidebar-menu">
                    <li class="sidebar-menu-item">
                        <a href="#" class="sidebar-menu-link active" id="dashboard-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sidebar-menu-icon">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="#" class="sidebar-menu-link" id="upload-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sidebar-menu-icon">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                            Upload Document
                        </a>
                    </li>
                </ul>

                <!-- Recent Documents -->
                <div class="sidebar-menu-header">Recent Documents</div>
                    <ul class="file-list" id="file-list">
                        @foreach($documents->take(5) as $document)
                        <li class="file-item">
                            <a href="#" class="file-link" data-file-id="{{ $document->id }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="file-icon">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                                <span class="file-name">{{ Str::limit($document->title, 20) }}</span>
                                <span class="file-date">{{ $document->created_at->diffForHumans() }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <!-- end Sidebar Content -->

        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="header-left">
                    <button class="toggle-sidebar" id="toggle-sidebar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
                    <h1 class="page-title" id="page-title">Dashboard</h1>
                </div>
                <div class="header-right">
                    <div class="admin-profile">
                        <img src="/placeholder.svg?height=40&width=40" alt="Admin" class="admin-avatar">
                        <div class="admin-info">
                            <div class="admin-name">John Doe</div>
                            <div class="admin-role">Administrator</div>
                        </div>
                    </div>
                </div>
            </header>
            <!-- end Header -->

            <!-- Content -->
            <div class="content">
                <!-- Dashboard View -->
                <div id="dashboard-view">
                    <div class="content-header">
                        <h2 class="content-title">Document Management Dashboard</h2>
                        <p class="content-subtitle">Overview of your document management system</p>
                    </div>
                    <!-- insight card -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                            </div>
                            <div class="stat-card-title">Total Documents</div>
                            <div class="stat-card-value">{{ $stats['total_documents'] }}</div>
                            <div class="stat-card-description">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #10b981;">
                                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                                    <polyline points="17 6 23 6 23 12"></polyline>
                                </svg>
                                <span>12% increase this month</span>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </div>
                            <div class="stat-card-title">Uploaded Today</div>
                            <div class="stat-card-value">{{ $stats['uploaded_today'] }}</div>
                            <div class="stat-card-description">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #10b981;">
                                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                                    <polyline points="17 6 23 6 23 12"></polyline>
                                </svg>
                                <span>3 more than yesterday</span>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                                </svg>
                            </div>
                            <!-- ... icon remains same ... -->
                            <div class="stat-card-title">Categories</div>
                            <div class="stat-card-value">{{ $stats['categories'] }}</div>
                            <div class="stat-card-description">
                                <span>Most used: {{ $documents->groupBy('category')->sortDesc()->keys()->first() }}</span>
                            </div>

                        </div>

                        <div class="stat-card">
                            <div class="stat-card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                            </div>
                            <div class="stat-card-title">Storage Used</div>
                            <div class="stat-card-value">1.2 GB</div>
                            <div class="stat-card-description">
                                <span>of 5 GB (24%)</span>
                            </div>
                        </div>
                    </div>
                    <!-- end insight card -->

                    <!-- main contnt recent documents -->
                    <div class="card">
                        <div class="card-header">
                            Recent Documents
                            <div class="card-header-actions">
                                <button class="btn btn-sm btn-primary" id="new-upload-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="17 8 12 3 7 8"></polyline>
                                        <line x1="12" y1="3" x2="12" y2="15"></line>
                                    </svg>
                                    New Upload
                                </button>
                            </div>
                        </div>
                        <!-- table content -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Date</th>
                                            <th>Size</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($documents as $document)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; color: #4f46e5;">
                                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                        <polyline points="14 2 14 8 20 8"></polyline>
                                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                                        <polyline points="10 9 9 9 8 9"></polyline>
                                                    </svg>
                                                    {{ $document->title }}
                                                </div>
                                            </td>
                                            <td>{{ $document->category ?? 'Uncategorized' }}</td>
                                            <td>{{ $document->created_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                @if(Storage::exists($document->filepath))
                                                {{ round(Storage::size($document->filepath) / 1024, 1) }} KB
                                                @else
                                                N/A
                                                @endif
                                            </td>
                                            <td>
                                                @if($document->status === 'completed')
                                                <span class="badge bg-success">Completed</span>
                                                @elseif($document->status === 'failed')
                                                <span class="badge bg-danger">Failed</span>
                                                @else
                                                <span class="badge bg-warning text-dark">Processing</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('school-assistant.getDocument', $document->id) }}" class="btn btn-sm btn-outline-secondary view-file-btn">View</a>
                                                    <form action="{{ route('school-assistant.delete', $document->id) }}" method="post">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- end table content -->
                    </div>
                </div>

                <!-- Upload View -->
                <div id="upload-view" style="display: none;">
                    <div class="content-header">
                        <h2 class="content-title">Upload New Document</h2>
                        <p class="content-subtitle">Add a new document to your collection</p>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <svg xmlns="http://www.w3.org/2000/svg" class="me-2" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: -5px;">
                                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <path d="M12 18v-6"></path>
                                <path d="M8 15h8"></path>
                            </svg>
                            Document Upload Form
                        </div>
                        <div class="card-body">
                            <form id="uploadForm" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4">
                                    <label for="title" class="form-label">Document Title</label>
                                    <input type="text" class="form-control" id="title" name="title" placeholder="Enter document title" required>
                                    <div class="invalid-feedback" id="title-error"></div>
                                </div>

                                <div class="mb-4">
                                    <label for="category" class="form-label">Category (Optional)</label>
                                    <input type="text" class="form-control" id="category" name="category" placeholder="Enter category">
                                    <div class="invalid-feedback" id="category-error"></div>
                                </div>

                                <div class="mb-4">
                                    <label for="documents" class="form-label">Document Files</label>
                                    <div class="file-input-wrapper">
                                        <input type="file" multiple class="form-control" id="documents" name="documents[]" required>
                                        <span class="file-count" id="file-count">No files</span>
                                    </div>
                                    <div class="invalid-feedback" id="document-error"></div>
                                    <small class="text-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: -3px; margin-right: 4px;">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" y1="16" x2="12" y2="12"></line>
                                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                        </svg>
                                        Accepted formats: PDF, DOC, DOCX, TXT (Max: 10MB)
                                    </small>
                                </div>

                                <div class="progress-container" style="display: none;">
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <div class="progress-text">0%</div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: -3px; margin-right: 6px;">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="17 8 12 3 7 8"></polyline>
                                            <line x1="12" y1="3" x2="12" y2="15"></line>
                                        </svg>
                                        Upload Document
                                    </button>
                                </div>
                            </form>

                            <div id="upload-status" class="alert" role="alert">
                                <h5 id="status-message" class="mb-2 fw-bold"></h5>
                                <div id="document-details"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- File Details View -->
                <div id="file-details-view" style="display: none;">
                    <div class="content-header">
                        <h2 class="content-title">File Details</h2>
                        <p class="content-subtitle">View and manage document information</p>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div id="file-details-title">Document Information</div>
                            <div class="card-header-actions">
                                <button class="btn btn-sm btn-outline-secondary" id="back-to-dashboard">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;">
                                        <line x1="19" y1="12" x2="5" y2="12"></line>
                                        <polyline points="12 19 5 12 12 5"></polyline>
                                    </svg>
                                    Back to Dashboard
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="file-details-header">
                                <div class="file-details-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                        <polyline points="10 9 9 9 8 9"></polyline>
                                    </svg>
                                </div>
                                <div class="file-details-info">
                                    <h3 class="file-details-title" id="file-name">Annual Report 2023.pdf</h3>
                                    <div class="file-details-meta">
                                        <div class="file-details-meta-item">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                            </svg>
                                            <span id="file-date">Today, 10:30 AM</span>
                                        </div>
                                        <div class="file-details-meta-item">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                                            </svg>
                                            <span id="file-category">Financial</span>
                                        </div>
                                        <div class="file-details-meta-item">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="4 17 10 11 4 5"></polyline>
                                                <line x1="12" y1="19" x2="20" y2="19"></line>
                                            </svg>
                                            <span id="file-size">4.2 MB</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="file-details-actions">
                                <button class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="7 10 12 15 17 10"></polyline>
                                        <line x1="12" y1="15" x2="12" y2="3"></line>
                                    </svg>
                                    Download
                                </button>
                                <button class="btn btn-outline-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                    Edit
                                </button>
                                <button class="btn btn-outline-danger">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>
                                    Delete
                                </button>
                            </div>

                            <div class="file-details-content">
                                <div class="file-preview">
                                    <div class="file-preview-placeholder">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                            <line x1="16" y1="13" x2="8" y2="13"></line>
                                            <line x1="16" y1="17" x2="8" y2="17"></line>
                                            <polyline points="10 9 9 9 8 9"></polyline>
                                        </svg>
                                        <p class="mt-2">Preview not available</p>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header">Document History</div>
                                    <div class="card-body">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>Uploaded</strong>
                                                    <div class="text-muted">by John Doe</div>
                                                </div>
                                                <span>Today, 10:30 AM</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>Processing Completed</strong>
                                                    <div class="text-muted">Document indexed</div>
                                                </div>
                                                <span>Today, 10:32 AM</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>Viewed</strong>
                                                    <div class="text-muted">by John Doe</div>
                                                </div>
                                                <span>Today, 11:15 AM</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Setup CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Toggle sidebar on mobile
            $('#toggle-sidebar').on('click', function() {
                $('#sidebar').toggleClass('show');
            });

            // Navigation
            $('#dashboard-link').on('click', function(e) {
                e.preventDefault();
                $('.sidebar-menu-link').removeClass('active');
                $(this).addClass('active');
                $('#page-title').text('Dashboard');
                $('#dashboard-view').show();
                $('#upload-view').hide();
                $('#file-details-view').hide();
            });

            $('#upload-link, #new-upload-btn').on('click', function(e) {
                e.preventDefault();
                $('.sidebar-menu-link').removeClass('active');
                $('#upload-link').addClass('active');
                $('#page-title').text('Upload Document');
                $('#dashboard-view').hide();
                $('#upload-view').show();
                $('#file-details-view').hide();
            });

            // File list click
            $('.file-link, .view-file-btn').on('click', function(e) {
                e.preventDefault();
                const fileId = $(this).data('file-id');

                // Fetch file details from your API
                fetch(`http://localhost:8000/api/school-assistant/documents/${fileId}`)
                    .then(response => response.json())
                    .then(document => {
                        $('#file-name').text(document.title);
                        $('#file-category').text(document.category || 'Uncategorized');
                        $('#file-date').text(new Date(document.created_at).toLocaleString());
                        $('#file-size').text(
                            document.file_size ?
                            `${(document.file_size / 1024).toFixed(1)} KB` :
                            'N/A'
                        );
                        $('#file-details-title').text(document.title);

                        // Update status badge
                        let statusBadge = '';
                        if (document.status === 'completed') {
                            statusBadge = '<span class="badge bg-success">Completed</span>';
                        } else if (document.status === 'failed') {
                            statusBadge = '<span class="badge bg-danger">Failed</span>';
                        } else {
                            statusBadge = '<span class="badge bg-warning text-dark">Processing</span>';
                        }

                        // Update history
                        let historyItems = `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Uploaded</strong>
                        <div class="text-muted">by System</div>
                    </div>
                    <span>${new Date(document.created_at).toLocaleString()}</span>
                </li>
            `;

                        if (document.processed_at) {
                            historyItems += `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Processing ${document.status}</strong>
                            <div class="text-muted">${document.chunk_count ? document.chunk_count + ' chunks' : ''}</div>
                        </div>
                        <span>${new Date(document.processed_at).toLocaleString()}</span>
                    </li>
                `;
                        }

                        $('.file-details-content .list-group').html(historyItems);

                        // Show the view
                        $('.sidebar-menu-link').removeClass('active');
                        $('.file-link').removeClass('active');
                        $(`.file-link[data-file-id="${fileId}"]`).addClass('active');

                        $('#page-title').text('File Details');
                        $('#dashboard-view').hide();
                        $('#upload-view').hide();
                        $('#file-details-view').show();
                    })
                    .catch(error => {
                        console.error('Error fetching document:', error);
                        alert('Failed to load document details');
                    });
            });

            // Back to dashboard
            $('#back-to-dashboard').on('click', function(e) {
                e.preventDefault();
                $('#dashboard-link').trigger('click');
            });

            // Update file count when files are selected
            $('#documents').on('change', function() {
                const fileCount = this.files.length;
                $('#file-count').text(fileCount > 0 ? fileCount + ' file' + (fileCount > 1 ? 's' : '') + ' selected' : 'No files');
            });

            // Form submission
            // Update file count when files are selected
            $('#documents').on('change', function() {
                const fileCount = this.files.length;
                $('#file-count').text(fileCount > 0 ? fileCount + ' file' + (fileCount > 1 ? 's' : '') + ' selected' : 'No files');
            });

            $('#uploadForm').on('submit', function(e) {
                e.preventDefault();

                // Reset error messages
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
                $('#upload-status').hide();

                let formData = new FormData(this);

                $.ajax({
                    url: '{{ route("school-assistant.upload") }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = ((evt.loaded / evt.total) * 100);
                                $('.progress-container').show();
                                $('.progress-bar').width(percentComplete + '%');
                                $('.progress-text').text(Math.round(percentComplete) + '%');
                            }
                        }, false);
                        return xhr;
                    },
                    beforeSend: function() {
                        $('#upload-status').hide();
                        $('.progress-container').show();
                        $('button[type="submit"]').prop('disabled', true).html(`
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span class="ms-2">Uploading...</span>
                        `);
                    },
                    success: function(response) {
                        $('button[type="submit"]').prop('disabled', false).html(`
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: -3px; margin-right: 6px;">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                            Upload Document
                        `);

                        $('#upload-status').removeClass('alert-danger').addClass('alert-success').show();
                        $('#status-message').html(`
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: -4px; margin-right: 8px;">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            ${response.message}
                        `);

                        let details = `
                            <div class="mt-3">
                                <div class="mb-2"><strong>Title:</strong> ${response.document.title}</div>
                                <div class="mb-2"><strong>Category:</strong> ${response.document.category || 'None'}</div>
                                <div class="d-flex align-items-center">
                                    <span class="me-2"><strong>Status:</strong></span>
                                    <span class="badge bg-warning text-dark">Processing...</span>
                                </div>
                            </div>
                        `;

                        $('#document-details').html(details);
                        $('#uploadForm')[0].reset();
                        $('#file-count').text('No files');
                        $('.progress-container').hide();
                    },
                    error: function(xhr) {
                        $('button[type="submit"]').prop('disabled', false).html(`
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: -3px; margin-right: 6px;">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                            Upload Document
                        `);
                        $('.progress-container').hide();

                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key).addClass('is-invalid');
                                $('#' + key + '-error').text(value[0]);
                            });

                            $('#upload-status').removeClass('alert-success').addClass('alert-danger').show();
                            $('#status-message').html(`
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: -4px; margin-right: 8px;">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                                There were errors with your submission.
                            `);
                            $('#document-details').html('');
                        } else {
                            $('#upload-status').removeClass('alert-success').addClass('alert-danger').show();
                            $('#status-message').html(`
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: -4px; margin-right: 8px;">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                                An error occurred while uploading your document.
                            `);
                            $('#document-details').html('Please try again later.');
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>