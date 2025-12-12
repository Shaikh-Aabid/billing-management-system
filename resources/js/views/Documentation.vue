<template>
    <div class="documentation-page">
        <!-- Header -->
        <v-app-bar color="primary" density="comfortable">
            <v-btn icon variant="text" @click="goBack">
                <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            <v-app-bar-title>
                <v-icon class="mr-2">mdi-book-open-page-variant</v-icon>
                BillFlow Documentation
            </v-app-bar-title>
            <v-spacer></v-spacer>
            <v-btn icon variant="text" @click="scrollToTop">
                <v-icon>mdi-arrow-up</v-icon>
            </v-btn>
        </v-app-bar>

        <v-container class="py-8" style="max-width: 1200px;">
            <!-- Table of Contents Card -->
            <v-card class="mb-6" variant="outlined">
                <v-card-title class="d-flex align-center">
                    <v-icon color="primary" class="mr-2">mdi-format-list-bulleted</v-icon>
                    Table of Contents
                </v-card-title>
                <v-card-text>
                    <v-row>
                        <v-col v-for="(section, index) in tableOfContents" :key="index" cols="12" sm="6" md="4">
                            <v-btn
                                variant="text"
                                color="primary"
                                class="text-none justify-start"
                                block
                                @click="scrollToSection(section.id)"
                            >
                                <v-icon size="small" class="mr-2">{{ section.icon }}</v-icon>
                                {{ section.title }}
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>

            <!-- Section 1: Project Overview -->
            <v-card id="project-overview" class="mb-6">
                <v-card-title class="d-flex align-center bg-primary text-white">
                    <v-icon class="mr-2">mdi-information</v-icon>
                    1. Project Overview
                </v-card-title>
                <v-card-text class="pa-6">
                    <p class="text-body-1 mb-4">
                        <strong>BillFlow</strong> is a comprehensive GST-compliant billing management system designed for Indian businesses.
                        It enables users to manage parties (customers/suppliers), products, generate GST invoices, and create e-way bills.
                    </p>
                    <v-alert color="primary" variant="tonal" class="mb-4">
                        <div class="font-weight-bold mb-2">Key Highlights</div>
                        <v-row dense>
                            <v-col cols="12" sm="6" v-for="feature in keyFeatures" :key="feature">
                                <div class="d-flex align-center">
                                    <v-icon color="success" size="small" class="mr-2">mdi-check-circle</v-icon>
                                    <span>{{ feature }}</span>
                                </div>
                            </v-col>
                        </v-row>
                    </v-alert>
                </v-card-text>
            </v-card>

            <!-- Section 2: Technology Stack -->
            <v-card id="technology-stack" class="mb-6">
                <v-card-title class="d-flex align-center bg-primary text-white">
                    <v-icon class="mr-2">mdi-layers</v-icon>
                    2. Technology Stack
                </v-card-title>
                <v-card-text class="pa-6">
                    <v-row>
                        <v-col cols="12" md="6">
                            <h3 class="text-h6 mb-3 d-flex align-center">
                                <v-icon color="error" class="mr-2">mdi-server</v-icon>
                                Backend
                            </h3>
                            <v-table density="compact">
                                <thead>
                                    <tr>
                                        <th>Technology</th>
                                        <th>Version</th>
                                        <th>Purpose</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="tech in backendStack" :key="tech.name">
                                        <td class="font-weight-medium">{{ tech.name }}</td>
                                        <td><v-chip size="x-small" color="error" variant="tonal">{{ tech.version }}</v-chip></td>
                                        <td>{{ tech.purpose }}</td>
                                    </tr>
                                </tbody>
                            </v-table>
                        </v-col>
                        <v-col cols="12" md="6">
                            <h3 class="text-h6 mb-3 d-flex align-center">
                                <v-icon color="success" class="mr-2">mdi-vuejs</v-icon>
                                Frontend
                            </h3>
                            <v-table density="compact">
                                <thead>
                                    <tr>
                                        <th>Technology</th>
                                        <th>Version</th>
                                        <th>Purpose</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="tech in frontendStack" :key="tech.name">
                                        <td class="font-weight-medium">{{ tech.name }}</td>
                                        <td><v-chip size="x-small" color="success" variant="tonal">{{ tech.version }}</v-chip></td>
                                        <td>{{ tech.purpose }}</td>
                                    </tr>
                                </tbody>
                            </v-table>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>

            <!-- Section 3: System Architecture -->
            <v-card id="system-architecture" class="mb-6">
                <v-card-title class="d-flex align-center bg-primary text-white">
                    <v-icon class="mr-2">mdi-sitemap</v-icon>
                    3. System Architecture
                </v-card-title>
                <v-card-text class="pa-6">
                    <v-sheet rounded="lg" class="pa-4 mb-4 overflow-x-auto" color="grey-darken-4">
                        <pre class="text-caption text-white" style="font-family: 'Courier New', monospace; line-height: 1.4;">{{ architectureDiagram }}</pre>
                    </v-sheet>

                    <h3 class="text-h6 mb-3 mt-6">Directory Structure</h3>
                    <v-sheet rounded="lg" class="pa-4 overflow-x-auto" color="grey-darken-4">
                        <pre class="text-caption text-white" style="font-family: 'Courier New', monospace; line-height: 1.4;">{{ directoryStructure }}</pre>
                    </v-sheet>
                </v-card-text>
            </v-card>

            <!-- Section 4: Database Structure -->
            <v-card id="database-structure" class="mb-6">
                <v-card-title class="d-flex align-center bg-primary text-white">
                    <v-icon class="mr-2">mdi-database</v-icon>
                    4. Database Structure
                </v-card-title>
                <v-card-text class="pa-6">
                    <h3 class="text-h6 mb-3">Entity Relationship Diagram (ERD)</h3>
                    <v-sheet rounded="lg" class="pa-4 mb-6 overflow-x-auto" color="grey-darken-4">
                        <pre class="text-caption text-white" style="font-family: 'Courier New', monospace; line-height: 1.3; font-size: 11px;">{{ erdDiagram }}</pre>
                    </v-sheet>

                    <h3 class="text-h6 mb-3">Table Definitions</h3>
                    <v-expansion-panels variant="accordion">
                        <v-expansion-panel v-for="table in databaseTables" :key="table.name">
                            <v-expansion-panel-title>
                                <v-icon color="primary" class="mr-2">mdi-table</v-icon>
                                {{ table.name }}
                            </v-expansion-panel-title>
                            <v-expansion-panel-text>
                                <v-table density="compact">
                                    <thead>
                                        <tr>
                                            <th>Column</th>
                                            <th>Type</th>
                                            <th>Constraints</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="col in table.columns" :key="col.name">
                                            <td class="font-weight-medium">{{ col.name }}</td>
                                            <td><code>{{ col.type }}</code></td>
                                            <td>{{ col.constraints }}</td>
                                        </tr>
                                    </tbody>
                                </v-table>
                            </v-expansion-panel-text>
                        </v-expansion-panel>
                    </v-expansion-panels>
                </v-card-text>
            </v-card>

            <!-- Section 5: Flow Charts -->
            <v-card id="flow-charts" class="mb-6">
                <v-card-title class="d-flex align-center bg-primary text-white">
                    <v-icon class="mr-2">mdi-chart-timeline-variant</v-icon>
                    5. Application Flow Charts
                </v-card-title>
                <v-card-text class="pa-6">
                    <v-expansion-panels variant="accordion">
                        <v-expansion-panel v-for="flow in flowCharts" :key="flow.title">
                            <v-expansion-panel-title>
                                <v-icon color="primary" class="mr-2">{{ flow.icon }}</v-icon>
                                {{ flow.title }}
                            </v-expansion-panel-title>
                            <v-expansion-panel-text>
                                <v-sheet rounded="lg" class="pa-4 overflow-x-auto" color="grey-darken-4">
                                    <pre class="text-caption text-white" style="font-family: 'Courier New', monospace; line-height: 1.3; font-size: 11px;">{{ flow.diagram }}</pre>
                                </v-sheet>
                            </v-expansion-panel-text>
                        </v-expansion-panel>
                    </v-expansion-panels>
                </v-card-text>
            </v-card>

            <!-- Section 6: API Documentation -->
            <v-card id="api-documentation" class="mb-6">
                <v-card-title class="d-flex align-center bg-primary text-white">
                    <v-icon class="mr-2">mdi-api</v-icon>
                    6. API Documentation
                </v-card-title>
                <v-card-text class="pa-6">
                    <v-expansion-panels variant="accordion">
                        <v-expansion-panel v-for="api in apiEndpoints" :key="api.category">
                            <v-expansion-panel-title>
                                <v-icon color="primary" class="mr-2">{{ api.icon }}</v-icon>
                                {{ api.category }}
                            </v-expansion-panel-title>
                            <v-expansion-panel-text>
                                <v-table density="compact">
                                    <thead>
                                        <tr>
                                            <th style="width: 100px;">Method</th>
                                            <th>Endpoint</th>
                                            <th>Description</th>
                                            <th style="width: 80px;">Auth</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="endpoint in api.endpoints" :key="endpoint.path">
                                            <td>
                                                <v-chip :color="getMethodColor(endpoint.method)" size="x-small" variant="flat">
                                                    {{ endpoint.method }}
                                                </v-chip>
                                            </td>
                                            <td><code class="text-primary">{{ endpoint.path }}</code></td>
                                            <td>{{ endpoint.description }}</td>
                                            <td>
                                                <v-icon :color="endpoint.auth ? 'success' : 'grey'" size="small">
                                                    {{ endpoint.auth ? 'mdi-lock' : 'mdi-lock-open' }}
                                                </v-icon>
                                            </td>
                                        </tr>
                                    </tbody>
                                </v-table>
                            </v-expansion-panel-text>
                        </v-expansion-panel>
                    </v-expansion-panels>
                </v-card-text>
            </v-card>

            <!-- Section 7: Features -->
            <v-card id="features" class="mb-6">
                <v-card-title class="d-flex align-center bg-primary text-white">
                    <v-icon class="mr-2">mdi-star</v-icon>
                    7. Features
                </v-card-title>
                <v-card-text class="pa-6">
                    <v-row>
                        <v-col v-for="category in featureCategories" :key="category.title" cols="12" md="6">
                            <v-card variant="outlined" class="h-100">
                                <v-card-title class="text-subtitle-1 d-flex align-center">
                                    <v-icon :color="category.color" class="mr-2">{{ category.icon }}</v-icon>
                                    {{ category.title }}
                                </v-card-title>
                                <v-card-text>
                                    <v-list density="compact">
                                        <v-list-item v-for="feature in category.features" :key="feature" class="px-0">
                                            <template v-slot:prepend>
                                                <v-icon size="small" color="success">mdi-check</v-icon>
                                            </template>
                                            <v-list-item-title class="text-body-2">{{ feature }}</v-list-item-title>
                                        </v-list-item>
                                    </v-list>
                                </v-card-text>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>

            <!-- Section 8: Installation Guide -->
            <v-card id="installation-guide" class="mb-6">
                <v-card-title class="d-flex align-center bg-primary text-white">
                    <v-icon class="mr-2">mdi-download</v-icon>
                    8. Installation Guide
                </v-card-title>
                <v-card-text class="pa-6">
                    <v-alert color="info" variant="tonal" class="mb-4">
                        <div class="font-weight-bold mb-2">Prerequisites</div>
                        <v-row dense>
                            <v-col cols="6" sm="3" v-for="prereq in prerequisites" :key="prereq">
                                <v-chip color="info" variant="flat" size="small" class="mr-2">{{ prereq }}</v-chip>
                            </v-col>
                        </v-row>
                    </v-alert>

                    <v-row>
                        <v-col cols="12" md="6">
                            <h3 class="text-h6 mb-3">Backend Setup</h3>
                            <v-sheet rounded="lg" class="pa-4" color="grey-darken-4">
                                <pre class="text-caption text-success" style="font-family: 'Courier New', monospace;">{{ backendSetup }}</pre>
                            </v-sheet>
                        </v-col>
                        <v-col cols="12" md="6">
                            <h3 class="text-h6 mb-3">Frontend Setup</h3>
                            <v-sheet rounded="lg" class="pa-4" color="grey-darken-4">
                                <pre class="text-caption text-success" style="font-family: 'Courier New', monospace;">{{ frontendSetup }}</pre>
                            </v-sheet>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>

            <!-- Footer -->
            <v-card variant="outlined">
                <v-card-text class="text-center py-4">
                    <p class="text-caption text-medium-emphasis mb-1">Documentation Version: 1.0</p>
                    <p class="text-caption text-medium-emphasis mb-1">Last Updated: December 2024</p>
                    <p class="text-caption text-medium-emphasis">BillFlow Development Team</p>
                </v-card-text>
            </v-card>
        </v-container>
    </div>
</template>

<script setup>
import { useRouter } from 'vue-router';

const router = useRouter();

const goBack = () => {
    router.push('/login');
};

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const scrollToSection = (id) => {
    const element = document.getElementById(id);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
};

const getMethodColor = (method) => {
    const colors = {
        GET: 'success',
        POST: 'primary',
        PUT: 'warning',
        DELETE: 'error',
    };
    return colors[method] || 'grey';
};

const tableOfContents = [
    { id: 'project-overview', title: 'Project Overview', icon: 'mdi-information' },
    { id: 'technology-stack', title: 'Technology Stack', icon: 'mdi-layers' },
    { id: 'system-architecture', title: 'System Architecture', icon: 'mdi-sitemap' },
    { id: 'database-structure', title: 'Database Structure', icon: 'mdi-database' },
    { id: 'flow-charts', title: 'Flow Charts', icon: 'mdi-chart-timeline-variant' },
    { id: 'api-documentation', title: 'API Documentation', icon: 'mdi-api' },
    { id: 'features', title: 'Features', icon: 'mdi-star' },
    { id: 'installation-guide', title: 'Installation Guide', icon: 'mdi-download' },
];

const keyFeatures = [
    'Multi-user support with isolated data',
    'GST-compliant invoicing (CGST/SGST/IGST)',
    'E-Way Bill generation for goods > ₹50,000',
    'Professional PDF export',
    'Dashboard with analytics',
    'Dark/Light theme support',
];

const backendStack = [
    { name: 'Laravel', version: '12.x', purpose: 'PHP Framework' },
    { name: 'PHP', version: '8.2+', purpose: 'Server-side language' },
    { name: 'SQLite/MySQL', version: '-', purpose: 'Database' },
    { name: 'Laravel Sanctum', version: '4.x', purpose: 'API Authentication' },
    { name: 'DomPDF', version: '3.x', purpose: 'PDF Generation' },
];

const frontendStack = [
    { name: 'Vue.js', version: '3.5', purpose: 'Frontend Framework' },
    { name: 'Vuetify', version: '3.11', purpose: 'UI Component Library' },
    { name: 'Pinia', version: '3.0', purpose: 'State Management' },
    { name: 'Vue Router', version: '4.6', purpose: 'Client-side Routing' },
    { name: 'ApexCharts', version: '5.3', purpose: 'Data Visualization' },
    { name: 'Axios', version: '1.13', purpose: 'HTTP Client' },
    { name: 'Vite', version: '7.0', purpose: 'Build Tool' },
];

const architectureDiagram = `┌─────────────────────────────────────────────────────────────────────┐
│                           CLIENT (Browser)                          │
│  ┌───────────────────────────────────────────────────────────────┐  │
│  │                    Vue.js 3 + Vuetify 3                       │  │
│  │  ┌─────────┐  ┌─────────┐  ┌─────────┐  ┌─────────────────┐   │  │
│  │  │  Views  │  │Components│  │ Router  │  │  Pinia Stores   │   │  │
│  │  └────┬────┘  └────┬────┘  └────┬────┘  └────────┬────────┘   │  │
│  │       │            │            │                │            │  │
│  │       └────────────┴────────────┴────────────────┘            │  │
│  │                           │                                    │  │
│  │                    Axios HTTP Client                          │  │
│  └───────────────────────────┼───────────────────────────────────┘  │
└──────────────────────────────┼──────────────────────────────────────┘
                               │ REST API (JSON)
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│                        SERVER (Laravel 12)                          │
│  ┌───────────────────────────────────────────────────────────────┐  │
│  │                     API Routes (api.php)                      │  │
│  │  ┌─────────────────────────────────────────────────────────┐  │  │
│  │  │              Sanctum Auth Middleware                    │  │  │
│  │  └─────────────────────────────────────────────────────────┘  │  │
│  │                            │                                  │  │
│  │  Controllers → Policies → Models                              │  │
│  └───────────────────────────────────────────────────────────────┘  │
└───────────────────────────────┼─────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────────┐
│                        DATABASE (SQLite/MySQL)                      │
│  users │ parties │ products │ bills │ bill_items │ eway_bills      │
└─────────────────────────────────────────────────────────────────────┘`;

const directoryStructure = `billing-software/
├── app/
│   ├── Http/Controllers/Api/
│   │   ├── AuthController.php
│   │   ├── BillController.php
│   │   ├── EwayBillController.php
│   │   ├── PartyController.php
│   │   ├── ProductController.php
│   │   └── SettingsController.php
│   ├── Models/
│   │   ├── User.php, Party.php, Product.php
│   │   ├── Bill.php, BillItem.php, EwayBill.php
│   │   └── Setting.php
│   └── Policies/
│       └── BillPolicy.php, PartyPolicy.php, ProductPolicy.php
├── database/migrations/
├── resources/
│   ├── js/ (views, components, stores, router, plugins)
│   └── views/pdf/ (bill.blade.php, eway-bill.blade.php)
└── routes/ (api.php, web.php)`;

const erdDiagram = `┌─────────────────────┐
│       USERS         │ ──────┬────────┬────────┐
├─────────────────────┤       │        │        │
│ id, name, email     │       │ 1:N    │ 1:N    │ 1:N
│ business_name, gstin│       ▼        ▼        ▼
│ address, state_code │  ┌─────────┐ ┌─────────┐ ┌─────────┐
└─────────────────────┘  │ PARTIES │ │PRODUCTS │ │SETTINGS │
                         └────┬────┘ └────┬────┘ └─────────┘
                              │           │
                              │ 1:N       │ 1:N
                              ▼           │
                         ┌─────────┐      │
                         │  BILLS  │ ─────┼──────┐
                         └────┬────┘      │      │
                              │           │      │
                              │ 1:N       │      │ 1:1
                              ▼           │      ▼
                         ┌───────────┐    │ ┌───────────┐
                         │BILL_ITEMS │◄───┘ │EWAY_BILLS │
                         └───────────┘      └───────────┘`;

const databaseTables = [
    {
        name: 'users',
        columns: [
            { name: 'id', type: 'BIGINT', constraints: 'PRIMARY KEY, AUTO_INCREMENT' },
            { name: 'name', type: 'VARCHAR(255)', constraints: 'NOT NULL' },
            { name: 'email', type: 'VARCHAR(255)', constraints: 'NOT NULL, UNIQUE' },
            { name: 'password', type: 'VARCHAR(255)', constraints: 'NOT NULL' },
            { name: 'business_name', type: 'VARCHAR(255)', constraints: 'NULLABLE' },
            { name: 'gstin', type: 'VARCHAR(15)', constraints: 'NULLABLE' },
            { name: 'address, city, state', type: 'VARCHAR/TEXT', constraints: 'NULLABLE' },
            { name: 'state_code, pincode, phone', type: 'VARCHAR', constraints: 'NULLABLE' },
        ],
    },
    {
        name: 'parties',
        columns: [
            { name: 'id', type: 'BIGINT', constraints: 'PRIMARY KEY, AUTO_INCREMENT' },
            { name: 'user_id', type: 'BIGINT', constraints: 'FOREIGN KEY → users(id)' },
            { name: 'name', type: 'VARCHAR(255)', constraints: 'NOT NULL' },
            { name: 'gstin', type: 'VARCHAR(15)', constraints: 'NULLABLE' },
            { name: 'address', type: 'TEXT', constraints: 'NOT NULL' },
            { name: 'city, state, state_code', type: 'VARCHAR', constraints: 'NULLABLE' },
            { name: 'party_type', type: 'ENUM', constraints: "DEFAULT 'customer'" },
            { name: 'deleted_at', type: 'TIMESTAMP', constraints: 'NULLABLE (Soft Delete)' },
        ],
    },
    {
        name: 'products',
        columns: [
            { name: 'id', type: 'BIGINT', constraints: 'PRIMARY KEY, AUTO_INCREMENT' },
            { name: 'user_id', type: 'BIGINT', constraints: 'FOREIGN KEY → users(id)' },
            { name: 'name', type: 'VARCHAR(255)', constraints: 'NOT NULL' },
            { name: 'hsn_code', type: 'VARCHAR(8)', constraints: 'NOT NULL' },
            { name: 'unit', type: 'VARCHAR(20)', constraints: "DEFAULT 'Nos'" },
            { name: 'price', type: 'DECIMAL(12,2)', constraints: 'NOT NULL' },
            { name: 'gst_rate', type: 'DECIMAL(5,2)', constraints: 'DEFAULT 18.00' },
            { name: 'is_active', type: 'BOOLEAN', constraints: 'DEFAULT TRUE' },
        ],
    },
    {
        name: 'bills',
        columns: [
            { name: 'id', type: 'BIGINT', constraints: 'PRIMARY KEY, AUTO_INCREMENT' },
            { name: 'user_id', type: 'BIGINT', constraints: 'FOREIGN KEY → users(id)' },
            { name: 'party_id', type: 'BIGINT', constraints: 'FOREIGN KEY → parties(id)' },
            { name: 'bill_number', type: 'VARCHAR(50)', constraints: 'NOT NULL, UNIQUE' },
            { name: 'bill_date', type: 'DATE', constraints: 'NOT NULL' },
            { name: 'subtotal, cgst, sgst, igst', type: 'DECIMAL(15,2)', constraints: 'DEFAULT 0' },
            { name: 'gst_amount, total_amount', type: 'DECIMAL(15,2)', constraints: 'DEFAULT 0' },
            { name: 'is_inter_state', type: 'BOOLEAN', constraints: 'DEFAULT FALSE' },
        ],
    },
    {
        name: 'bill_items',
        columns: [
            { name: 'id', type: 'BIGINT', constraints: 'PRIMARY KEY, AUTO_INCREMENT' },
            { name: 'bill_id', type: 'BIGINT', constraints: 'FOREIGN KEY → bills(id) CASCADE' },
            { name: 'product_id', type: 'BIGINT', constraints: 'FOREIGN KEY → products(id)' },
            { name: 'hsn_code', type: 'VARCHAR(8)', constraints: 'NOT NULL' },
            { name: 'quantity', type: 'DECIMAL(12,3)', constraints: 'NOT NULL' },
            { name: 'price, amount', type: 'DECIMAL(12,2)', constraints: 'NOT NULL' },
            { name: 'gst_rate, cgst, sgst, igst', type: 'DECIMAL', constraints: 'GST breakdown' },
        ],
    },
    {
        name: 'eway_bills',
        columns: [
            { name: 'id', type: 'BIGINT', constraints: 'PRIMARY KEY, AUTO_INCREMENT' },
            { name: 'bill_id', type: 'BIGINT', constraints: 'FOREIGN KEY → bills(id) CASCADE' },
            { name: 'eway_bill_number', type: 'VARCHAR(50)', constraints: 'NOT NULL, UNIQUE' },
            { name: 'transport_mode', type: 'ENUM', constraints: "Road, Rail, Air, Ship" },
            { name: 'vehicle_number', type: 'VARCHAR(20)', constraints: 'NOT NULL' },
            { name: 'distance', type: 'INTEGER', constraints: 'NOT NULL (in km)' },
            { name: 'from_address, to_address', type: 'TEXT', constraints: 'NOT NULL' },
            { name: 'generated_at, valid_until', type: 'TIMESTAMP', constraints: 'NOT NULL' },
        ],
    },
];

const flowCharts = [
    {
        title: '5.1 Authentication Flow',
        icon: 'mdi-login',
        diagram: `                    ┌─────────────┐
                    │   START     │
                    └──────┬──────┘
                           │
                           ▼
               ┌───────────────────────┐
               │  Check localStorage   │
               │    for auth token     │
               └───────────┬───────────┘
                           │
          ┌────────────────┴────────────────┐
          │ Token exists        No token    │
          ▼                                 ▼
  ┌─────────────────┐              ┌─────────────────┐
  │ Validate token  │              │ Show Login page │
  │ GET /api/user   │              └────────┬────────┘
  └────────┬────────┘                       │
           │                                ▼
    ┌──────┴──────┐                ┌─────────────────┐
    ▼             ▼                │ POST /api/login │
  Valid       Invalid              └────────┬────────┘
    │             │                         │
    │             ▼                  ┌──────┴──────┐
    │     Clear & Redirect           ▼             ▼
    │                            Valid         Invalid
    ▼                              │              │
┌───────────┐                      ▼              ▼
│ Dashboard │◄─────── Store token & redirect   Show Error`,
    },
    {
        title: '5.2 Bill Creation Flow',
        icon: 'mdi-file-document-plus',
        diagram: `                    ┌─────────────┐
                    │ Create Bill │
                    └──────┬──────┘
                           │
          ┌────────────────┼────────────────┐
          ▼                ▼                ▼
    Fetch Parties    Fetch Products   Init Form
          │                │                │
          └────────────────┴────────────────┘
                           │
                           ▼
               ┌───────────────────────┐
               │  User fills form:     │
               │  - Select Party       │
               │  - Bill Date & Type   │
               │  - Add Line Items     │
               └───────────┬───────────┘
                           │
                           ▼
               ┌───────────────────────┐
               │  For each item:       │
               │  - Calculate GST      │
               │  - If Inter-state:    │
               │    IGST = Rate%       │
               │  - If Intra-state:    │
               │    CGST = SGST = ½    │
               └───────────┬───────────┘
                           │
                           ▼
               ┌───────────────────────┐
               │  POST /api/bills      │
               │  Server creates bill  │
               │  with items in txn    │
               └───────────┬───────────┘
                           │
                           ▼
               ┌───────────────────────┐
               │  Redirect to View     │
               └───────────────────────┘`,
    },
    {
        title: '5.3 GST Calculation Flow',
        icon: 'mdi-calculator',
        diagram: `                    ┌─────────────┐
                    │ Line Item   │
                    └──────┬──────┘
                           │
                           ▼
               ┌───────────────────────┐
               │ Subtotal = Qty × Price│
               └───────────┬───────────┘
                           │
                           ▼
               ┌───────────────────────┐
               │ GST = Subtotal × Rate%│
               └───────────┬───────────┘
                           │
          ┌────────────────┴────────────────┐
          │                                 │
          ▼                                 ▼
  ┌───────────────┐                ┌───────────────┐
  │ Inter-State   │                │ Intra-State   │
  │   (IGST)      │                │ (CGST + SGST) │
  └───────┬───────┘                └───────┬───────┘
          │                                │
          ▼                                ▼
  IGST = GST Amount              CGST = SGST = GST/2
  CGST = SGST = 0                IGST = 0
          │                                │
          └────────────────┬───────────────┘
                           │
                           ▼
               ┌───────────────────────┐
               │ Total = Subtotal + GST│
               └───────────────────────┘`,
    },
];

const apiEndpoints = [
    {
        category: 'Authentication',
        icon: 'mdi-shield-account',
        endpoints: [
            { method: 'POST', path: '/api/register', description: 'Register new user', auth: false },
            { method: 'POST', path: '/api/login', description: 'User login', auth: false },
            { method: 'POST', path: '/api/logout', description: 'User logout', auth: true },
            { method: 'GET', path: '/api/user', description: 'Get current user', auth: true },
        ],
    },
    {
        category: 'Parties',
        icon: 'mdi-account-group',
        endpoints: [
            { method: 'GET', path: '/api/parties', description: 'List parties (paginated)', auth: true },
            { method: 'GET', path: '/api/parties/all', description: 'List all parties', auth: true },
            { method: 'POST', path: '/api/parties', description: 'Create party', auth: true },
            { method: 'GET', path: '/api/parties/{id}', description: 'Get party details', auth: true },
            { method: 'PUT', path: '/api/parties/{id}', description: 'Update party', auth: true },
            { method: 'DELETE', path: '/api/parties/{id}', description: 'Delete party', auth: true },
        ],
    },
    {
        category: 'Products',
        icon: 'mdi-package-variant',
        endpoints: [
            { method: 'GET', path: '/api/products', description: 'List products (paginated)', auth: true },
            { method: 'GET', path: '/api/products/all', description: 'List all active products', auth: true },
            { method: 'POST', path: '/api/products', description: 'Create product', auth: true },
            { method: 'PUT', path: '/api/products/{id}', description: 'Update product', auth: true },
            { method: 'DELETE', path: '/api/products/{id}', description: 'Delete product', auth: true },
        ],
    },
    {
        category: 'Bills',
        icon: 'mdi-receipt-text',
        endpoints: [
            { method: 'GET', path: '/api/bills', description: 'List bills (paginated)', auth: true },
            { method: 'GET', path: '/api/bills/stats', description: 'Get dashboard statistics', auth: true },
            { method: 'POST', path: '/api/bills', description: 'Create bill', auth: true },
            { method: 'GET', path: '/api/bills/{id}', description: 'Get bill details', auth: true },
            { method: 'GET', path: '/api/bills/{id}/pdf', description: 'Download bill PDF', auth: true },
            { method: 'POST', path: '/api/bills/{id}/eway-bill', description: 'Generate e-way bill', auth: true },
        ],
    },
    {
        category: 'E-Way Bills',
        icon: 'mdi-truck-delivery',
        endpoints: [
            { method: 'GET', path: '/api/eway-bills', description: 'List e-way bills (paginated)', auth: true },
            { method: 'GET', path: '/api/eway-bills/{id}', description: 'Get e-way bill details', auth: true },
            { method: 'GET', path: '/api/eway-bills/{id}/pdf', description: 'Download e-way bill PDF', auth: true },
        ],
    },
];

const featureCategories = [
    {
        title: 'Core Features',
        icon: 'mdi-cog',
        color: 'primary',
        features: [
            'User registration and authentication',
            'Multi-user data isolation',
            'Party (Customer/Supplier) management',
            'Product catalog with HSN codes',
            'GST-compliant invoice generation',
            'E-Way bill generation and management',
            'Professional PDF export',
            'Dashboard with analytics',
        ],
    },
    {
        title: 'GST Compliance',
        icon: 'mdi-certificate',
        color: 'success',
        features: [
            'GSTIN validation (15-character format)',
            'HSN code support (4-8 digits)',
            'GST rates: 0%, 5%, 12%, 18%, 28%',
            'CGST/SGST for intra-state transactions',
            'IGST for inter-state transactions',
            'HSN-wise tax summary in invoices',
        ],
    },
    {
        title: 'E-Way Bill Features',
        icon: 'mdi-truck',
        color: 'warning',
        features: [
            'Automatic threshold check (₹50,000)',
            'Vehicle number validation (Indian format)',
            'Distance-based validity calculation',
            'Transport mode selection',
            'Status tracking (Active/Expired)',
        ],
    },
    {
        title: 'Security',
        icon: 'mdi-shield-check',
        color: 'error',
        features: [
            'Token-based authentication (Sanctum)',
            'User authorization policies',
            'Input validation',
            'CSRF protection',
            'Soft deletes for data recovery',
        ],
    },
];

const prerequisites = ['PHP 8.2+', 'Composer', 'Node.js 18+', 'npm'];

const backendSetup = `# Clone repository
cd /path/to/billing-software

# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Start Laravel server
php artisan serve`;

const frontendSetup = `# Install Node dependencies
npm install

# Development mode
npm run dev

# Production build
npm run build

# Access at http://localhost:8000`;
</script>

<style scoped>
.documentation-page {
    background: linear-gradient(135deg, #F8FAFC 0%, #EEF2FF 50%, #F1F5F9 100%);
    min-height: 100vh;
}

pre {
    white-space: pre;
    overflow-x: auto;
    margin: 0;
}

code {
    background: rgba(99, 102, 241, 0.1);
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.85em;
}
</style>
