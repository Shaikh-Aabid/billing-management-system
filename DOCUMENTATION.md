# BillFlow - GST Billing Management System

## Complete Project Documentation

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Technology Stack](#2-technology-stack)
3. [System Architecture](#3-system-architecture)
4. [Database Structure](#4-database-structure)
5. [Application Flow Charts](#5-application-flow-charts)
6. [API Documentation](#6-api-documentation)
7. [Frontend Structure](#7-frontend-structure)
8. [Features](#8-features)
9. [Installation Guide](#9-installation-guide)

---

## 1. Project Overview

**BillFlow** is a comprehensive GST-compliant billing management system designed for Indian businesses. It enables users to manage parties (customers/suppliers), products, generate GST invoices, and create e-way bills.

### Key Highlights
- Multi-user support with isolated data
- GST-compliant invoicing (CGST/SGST/IGST)
- E-Way Bill generation for goods > ₹50,000
- Professional PDF export
- Dashboard with analytics
- Dark/Light theme support

---

## 2. Technology Stack

### Backend
| Technology | Version | Purpose |
|------------|---------|---------|
| Laravel | 12.x | PHP Framework |
| PHP | 8.2+ | Server-side language |
| SQLite/MySQL | - | Database |
| Laravel Sanctum | 4.x | API Authentication |
| DomPDF | 3.x | PDF Generation |

### Frontend
| Technology | Version | Purpose |
|------------|---------|---------|
| Vue.js | 3.5 | Frontend Framework |
| Vuetify | 3.11 | UI Component Library |
| Pinia | 3.0 | State Management |
| Vue Router | 4.6 | Client-side Routing |
| ApexCharts | 5.3 | Data Visualization |
| Axios | 1.13 | HTTP Client |
| Vite | 7.0 | Build Tool |

---

## 3. System Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────────────────┐
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
│  │  └─────────────────────────┬───────────────────────────────┘  │  │
│  │                            │                                  │  │
│  │  ┌─────────────────────────▼───────────────────────────────┐  │  │
│  │  │                    Controllers                          │  │  │
│  │  │  Auth │ Party │ Product │ Bill │ EwayBill │ Settings   │  │  │
│  │  └─────────────────────────┬───────────────────────────────┘  │  │
│  │                            │                                  │  │
│  │  ┌─────────────────────────▼───────────────────────────────┐  │  │
│  │  │                 Authorization Policies                  │  │  │
│  │  │         BillPolicy │ PartyPolicy │ ProductPolicy        │  │  │
│  │  └─────────────────────────┬───────────────────────────────┘  │  │
│  │                            │                                  │  │
│  │  ┌─────────────────────────▼───────────────────────────────┐  │  │
│  │  │                 Eloquent Models                         │  │  │
│  │  │  User │ Party │ Product │ Bill │ BillItem │ EwayBill   │  │  │
│  │  └─────────────────────────┬───────────────────────────────┘  │  │
│  └────────────────────────────┼──────────────────────────────────┘  │
└───────────────────────────────┼─────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────────┐
│                        DATABASE (SQLite/MySQL)                      │
│  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌───────────────┐  │
│  │  users  │ │ parties │ │products │ │  bills  │ │  eway_bills   │  │
│  └─────────┘ └─────────┘ └─────────┘ └─────────┘ └───────────────┘  │
│  ┌─────────────┐ ┌─────────────┐ ┌───────────────────────────────┐  │
│  │ bill_items  │ │  settings   │ │   personal_access_tokens      │  │
│  └─────────────┘ └─────────────┘ └───────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────────┘
```

### Directory Structure

```
billing-software/
├── app/
│   ├── Http/Controllers/Api/
│   │   ├── AuthController.php
│   │   ├── BillController.php
│   │   ├── EwayBillController.php
│   │   ├── PartyController.php
│   │   ├── ProductController.php
│   │   └── SettingsController.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Party.php
│   │   ├── Product.php
│   │   ├── Bill.php
│   │   ├── BillItem.php
│   │   ├── EwayBill.php
│   │   └── Setting.php
│   └── Policies/
│       ├── BillPolicy.php
│       ├── PartyPolicy.php
│       └── ProductPolicy.php
├── database/migrations/
├── resources/
│   ├── js/
│   │   ├── views/
│   │   ├── components/
│   │   ├── stores/
│   │   ├── router/
│   │   └── plugins/
│   └── views/pdf/
├── routes/
│   ├── api.php
│   └── web.php
└── public/
```

---

## 4. Database Structure

### Entity Relationship Diagram (ERD)

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                              DATABASE SCHEMA                                    │
└─────────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────┐
│       USERS         │
├─────────────────────┤
│ PK  id              │
│     name            │
│     email (unique)  │
│     password        │
│     business_name   │
│     gstin           │
│     address         │
│     city            │
│     state           │
│     state_code      │
│     pincode         │
│     phone           │
│     email_verified  │
│     created_at      │
│     updated_at      │
└────────┬────────────┘
         │
         │ 1:N
         ▼
┌─────────────────────┐       ┌─────────────────────┐       ┌─────────────────────┐
│      PARTIES        │       │      PRODUCTS       │       │      SETTINGS       │
├─────────────────────┤       ├─────────────────────┤       ├─────────────────────┤
│ PK  id              │       │ PK  id              │       │ PK  id              │
│ FK  user_id ────────┼───────│ FK  user_id ────────┼───────│ FK  user_id         │
│     name            │       │     name            │       │     key             │
│     gstin           │       │     description     │       │     value           │
│     address         │       │     hsn_code        │       │     created_at      │
│     city            │       │     unit            │       │     updated_at      │
│     state           │       │     price           │       └─────────────────────┘
│     state_code      │       │     gst_rate        │
│     pincode         │       │     stock_quantity  │
│     contact_number  │       │     min_stock_level │
│     email           │       │     is_active       │
│     party_type      │       │     created_at      │
│     created_at      │       │     updated_at      │
│     updated_at      │       │     deleted_at      │
│     deleted_at      │       └────────┬────────────┘
└────────┬────────────┘                │
         │                             │
         │ 1:N                         │ 1:N
         ▼                             │
┌─────────────────────┐                │
│       BILLS         │                │
├─────────────────────┤                │
│ PK  id              │                │
│ FK  user_id         │                │
│ FK  party_id ───────┘                │
│     bill_number     │                │
│     bill_date       │                │
│     bill_type       │                │
│     subtotal        │                │
│     cgst            │                │
│     sgst            │                │
│     igst            │                │
│     gst_amount      │                │
│     total_amount    │                │
│     is_inter_state  │                │
│     notes           │                │
│     status          │                │
│     created_at      │                │
│     updated_at      │                │
│     deleted_at      │                │
└────────┬────────────┘                │
         │                             │
         │ 1:N                         │
         ▼                             │
┌─────────────────────┐                │
│     BILL_ITEMS      │                │
├─────────────────────┤                │
│ PK  id              │                │
│ FK  bill_id ────────┘                │
│ FK  product_id ──────────────────────┘
│     hsn_code        │
│     quantity        │
│     unit            │
│     price           │
│     gst_rate        │
│     cgst            │
│     sgst            │
│     igst            │
│     amount          │
│     created_at      │
│     updated_at      │
└─────────────────────┘

┌─────────────────────┐
│     EWAY_BILLS      │
├─────────────────────┤
│ PK  id              │
│ FK  bill_id ────────┼─── (1:1 with BILLS)
│     eway_bill_number│
│     transport_mode  │
│     vehicle_number  │
│     vehicle_type    │
│     transporter_id  │
│     transporter_name│
│     distance        │
│     from_address    │
│     from_pincode    │
│     to_address      │
│     to_pincode      │
│     generated_at    │
│     valid_until     │
│     status          │
│     created_at      │
│     updated_at      │
└─────────────────────┘
```

### Table Definitions

#### 1. users
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT |
| name | VARCHAR(255) | NOT NULL |
| email | VARCHAR(255) | NOT NULL, UNIQUE |
| password | VARCHAR(255) | NOT NULL |
| business_name | VARCHAR(255) | NULLABLE |
| gstin | VARCHAR(15) | NULLABLE |
| address | TEXT | NULLABLE |
| city | VARCHAR(100) | NULLABLE |
| state | VARCHAR(100) | NULLABLE |
| state_code | VARCHAR(2) | NULLABLE |
| pincode | VARCHAR(6) | NULLABLE |
| phone | VARCHAR(15) | NULLABLE |
| email_verified_at | TIMESTAMP | NULLABLE |
| remember_token | VARCHAR(100) | NULLABLE |
| created_at | TIMESTAMP | NULLABLE |
| updated_at | TIMESTAMP | NULLABLE |

#### 2. parties
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT |
| user_id | BIGINT | FOREIGN KEY → users(id) |
| name | VARCHAR(255) | NOT NULL |
| gstin | VARCHAR(15) | NULLABLE |
| address | TEXT | NOT NULL |
| city | VARCHAR(100) | NULLABLE |
| state | VARCHAR(100) | NULLABLE |
| state_code | VARCHAR(2) | NULLABLE |
| pincode | VARCHAR(6) | NULLABLE |
| contact_number | VARCHAR(15) | NULLABLE |
| email | VARCHAR(255) | NULLABLE |
| party_type | ENUM('customer','supplier') | DEFAULT 'customer' |
| created_at | TIMESTAMP | NULLABLE |
| updated_at | TIMESTAMP | NULLABLE |
| deleted_at | TIMESTAMP | NULLABLE (Soft Delete) |

#### 3. products
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT |
| user_id | BIGINT | FOREIGN KEY → users(id) |
| name | VARCHAR(255) | NOT NULL |
| description | TEXT | NULLABLE |
| hsn_code | VARCHAR(8) | NOT NULL |
| unit | VARCHAR(20) | DEFAULT 'Nos' |
| price | DECIMAL(12,2) | NOT NULL |
| gst_rate | DECIMAL(5,2) | DEFAULT 18.00 |
| stock_quantity | INTEGER | NULLABLE |
| min_stock_level | INTEGER | NULLABLE |
| is_active | BOOLEAN | DEFAULT TRUE |
| created_at | TIMESTAMP | NULLABLE |
| updated_at | TIMESTAMP | NULLABLE |
| deleted_at | TIMESTAMP | NULLABLE (Soft Delete) |

#### 4. bills
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT |
| user_id | BIGINT | FOREIGN KEY → users(id) |
| party_id | BIGINT | FOREIGN KEY → parties(id) RESTRICT |
| bill_number | VARCHAR(50) | NOT NULL, UNIQUE |
| bill_date | DATE | NOT NULL |
| bill_type | ENUM('invoice','quotation','delivery_challan') | DEFAULT 'invoice' |
| subtotal | DECIMAL(15,2) | DEFAULT 0 |
| cgst | DECIMAL(15,2) | DEFAULT 0 |
| sgst | DECIMAL(15,2) | DEFAULT 0 |
| igst | DECIMAL(15,2) | DEFAULT 0 |
| gst_amount | DECIMAL(15,2) | DEFAULT 0 |
| total_amount | DECIMAL(15,2) | DEFAULT 0 |
| is_inter_state | BOOLEAN | DEFAULT FALSE |
| notes | TEXT | NULLABLE |
| status | ENUM('draft','sent','paid','cancelled') | DEFAULT 'draft' |
| created_at | TIMESTAMP | NULLABLE |
| updated_at | TIMESTAMP | NULLABLE |
| deleted_at | TIMESTAMP | NULLABLE (Soft Delete) |

#### 5. bill_items
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT |
| bill_id | BIGINT | FOREIGN KEY → bills(id) CASCADE |
| product_id | BIGINT | FOREIGN KEY → products(id) RESTRICT |
| hsn_code | VARCHAR(8) | NOT NULL |
| quantity | DECIMAL(12,3) | NOT NULL |
| unit | VARCHAR(20) | NOT NULL |
| price | DECIMAL(12,2) | NOT NULL |
| gst_rate | DECIMAL(5,2) | NOT NULL |
| cgst | DECIMAL(12,2) | DEFAULT 0 |
| sgst | DECIMAL(12,2) | DEFAULT 0 |
| igst | DECIMAL(12,2) | DEFAULT 0 |
| amount | DECIMAL(15,2) | NOT NULL |
| created_at | TIMESTAMP | NULLABLE |
| updated_at | TIMESTAMP | NULLABLE |

#### 6. eway_bills
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT |
| bill_id | BIGINT | FOREIGN KEY → bills(id) CASCADE |
| eway_bill_number | VARCHAR(50) | NOT NULL, UNIQUE |
| transport_mode | ENUM('Road','Rail','Air','Ship') | DEFAULT 'Road' |
| vehicle_number | VARCHAR(20) | NOT NULL |
| vehicle_type | VARCHAR(50) | NULLABLE |
| transporter_id | VARCHAR(15) | NULLABLE |
| transporter_name | VARCHAR(255) | NULLABLE |
| distance | INTEGER | NOT NULL |
| from_address | TEXT | NOT NULL |
| from_pincode | VARCHAR(6) | NULLABLE |
| to_address | TEXT | NOT NULL |
| to_pincode | VARCHAR(6) | NULLABLE |
| generated_at | TIMESTAMP | NOT NULL |
| valid_until | TIMESTAMP | NOT NULL |
| status | ENUM('active','cancelled','expired') | DEFAULT 'active' |
| created_at | TIMESTAMP | NULLABLE |
| updated_at | TIMESTAMP | NULLABLE |

#### 7. settings
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT |
| user_id | BIGINT | FOREIGN KEY → users(id) CASCADE |
| key | VARCHAR(255) | NOT NULL |
| value | TEXT | NULLABLE |
| created_at | TIMESTAMP | NULLABLE |
| updated_at | TIMESTAMP | NULLABLE |
| **UNIQUE** | (user_id, key) | |

### Indexes

```sql
-- parties
INDEX idx_parties_user_name (user_id, name)
INDEX idx_parties_gstin (gstin)

-- products
INDEX idx_products_user_name (user_id, name)
INDEX idx_products_hsn (hsn_code)

-- bills
INDEX idx_bills_user_date (user_id, bill_date)
INDEX idx_bills_number (bill_number)
INDEX idx_bills_status (status)

-- bill_items
INDEX idx_bill_items_bill (bill_id)

-- eway_bills
INDEX idx_eway_number (eway_bill_number)
INDEX idx_eway_vehicle (vehicle_number)
INDEX idx_eway_status (status)
```

---

## 5. Application Flow Charts

### 5.1 Authentication Flow

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                          AUTHENTICATION FLOW                                │
└─────────────────────────────────────────────────────────────────────────────┘

                              ┌─────────────┐
                              │   START     │
                              └──────┬──────┘
                                     │
                                     ▼
                         ┌───────────────────────┐
                         │  User visits website  │
                         └───────────┬───────────┘
                                     │
                                     ▼
                         ┌───────────────────────┐
                         │  Check localStorage   │
                         │    for auth token     │
                         └───────────┬───────────┘
                                     │
                    ┌────────────────┴────────────────┐
                    │                                 │
                    ▼                                 ▼
           ┌───────────────┐                ┌───────────────┐
           │  Token exists │                │ No token found│
           └───────┬───────┘                └───────┬───────┘
                   │                                │
                   ▼                                ▼
        ┌─────────────────────┐          ┌─────────────────────┐
        │  Validate token     │          │  Redirect to Login  │
        │  GET /api/user      │          │       page          │
        └──────────┬──────────┘          └──────────┬──────────┘
                   │                                │
         ┌─────────┴─────────┐                      │
         │                   │                      ▼
         ▼                   ▼           ┌─────────────────────┐
  ┌─────────────┐    ┌─────────────┐     │  User enters        │
  │Token valid  │    │Token invalid│     │  credentials        │
  └──────┬──────┘    └──────┬──────┘     └──────────┬──────────┘
         │                  │                       │
         │                  ▼                       ▼
         │         ┌─────────────────┐    ┌─────────────────────┐
         │         │ Clear localStorage│   │  POST /api/login   │
         │         │ Redirect to Login │   └──────────┬──────────┘
         │         └─────────────────┘              │
         │                                ┌─────────┴─────────┐
         ▼                                │                   │
┌─────────────────┐                       ▼                   ▼
│   Go to         │              ┌───────────────┐   ┌───────────────┐
│   Dashboard     │              │  Credentials  │   │  Credentials  │
└─────────────────┘              │    Valid      │   │   Invalid     │
                                 └───────┬───────┘   └───────┬───────┘
                                         │                   │
                                         ▼                   ▼
                                ┌─────────────────┐  ┌───────────────┐
                                │  Store token &  │  │ Show error    │
                                │  user in store  │  │ message       │
                                └────────┬────────┘  └───────────────┘
                                         │
                                         ▼
                                ┌─────────────────┐
                                │   Redirect to   │
                                │   Dashboard     │
                                └─────────────────┘
```

### 5.2 Bill Creation Flow

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           BILL CREATION FLOW                                │
└─────────────────────────────────────────────────────────────────────────────┘

                              ┌─────────────┐
                              │   START     │
                              └──────┬──────┘
                                     │
                                     ▼
                         ┌───────────────────────┐
                         │  Click "Create Bill" │
                         └───────────┬───────────┘
                                     │
                                     ▼
                         ┌───────────────────────┐
                         │  Load Create Bill     │
                         │  page with form       │
                         └───────────┬───────────┘
                                     │
               ┌─────────────────────┼─────────────────────┐
               │                     │                     │
               ▼                     ▼                     ▼
    ┌─────────────────┐   ┌─────────────────┐   ┌─────────────────┐
    │ Fetch all       │   │ Fetch all       │   │ Initialize      │
    │ parties         │   │ products        │   │ empty bill      │
    │ GET /parties/all│   │ GET /products/all│   │ form            │
    └────────┬────────┘   └────────┬────────┘   └────────┬────────┘
             │                     │                     │
             └─────────────────────┴─────────────────────┘
                                   │
                                   ▼
                         ┌───────────────────────┐
                         │  User fills form:     │
                         │  - Select Party       │
                         │  - Select Bill Date   │
                         │  - Select Bill Type   │
                         │  - Inter/Intra State  │
                         └───────────┬───────────┘
                                     │
                                     ▼
                         ┌───────────────────────┐
                         │  Add Line Items       │◄──────────────┐
                         │  - Select Product     │               │
                         │  - Enter Quantity     │               │
                         │  - Auto-fill HSN,     │               │
                         │    Price, GST Rate    │               │
                         └───────────┬───────────┘               │
                                     │                           │
                                     ▼                           │
                         ┌───────────────────────┐               │
                         │  Calculate GST        │               │
                         │  - If Inter-state:    │               │
                         │    IGST = Rate%       │               │
                         │  - If Intra-state:    │               │
                         │    CGST = Rate/2      │               │
                         │    SGST = Rate/2      │               │
                         └───────────┬───────────┘               │
                                     │                           │
                                     ▼                           │
                         ┌───────────────────────┐               │
                         │  Update totals:       │               │
                         │  - Subtotal           │               │
                         │  - GST Amount         │               │
                         │  - Grand Total        │               │
                         └───────────┬───────────┘               │
                                     │                           │
                                     ▼                           │
                         ┌───────────────────────┐      ┌────────┴────────┐
                         │  More items to add?   │──Yes─►│  Add another    │
                         └───────────┬───────────┘      │  line item      │
                                     │ No               └─────────────────┘
                                     ▼
                         ┌───────────────────────┐
                         │  Click "Create Bill"  │
                         └───────────┬───────────┘
                                     │
                                     ▼
                         ┌───────────────────────┐
                         │  Validate form data   │
                         └───────────┬───────────┘
                                     │
                    ┌────────────────┴────────────────┐
                    │                                 │
                    ▼                                 ▼
           ┌───────────────┐                ┌───────────────┐
           │ Validation    │                │ Validation    │
           │   Passed      │                │   Failed      │
           └───────┬───────┘                └───────┬───────┘
                   │                                │
                   ▼                                ▼
        ┌─────────────────────┐          ┌─────────────────────┐
        │  POST /api/bills    │          │  Show validation    │
        │  with bill data     │          │  error messages     │
        └──────────┬──────────┘          └─────────────────────┘
                   │
                   ▼
        ┌─────────────────────┐
        │  Server processes:  │
        │  - Generate bill #  │
        │  - Create bill      │
        │  - Create items     │
        │  - Calculate totals │
        │  (Transaction)      │
        └──────────┬──────────┘
                   │
                   ▼
        ┌─────────────────────┐
        │  Return created     │
        │  bill with items    │
        └──────────┬──────────┘
                   │
                   ▼
        ┌─────────────────────┐
        │  Redirect to        │
        │  View Bill page     │
        └──────────┬──────────┘
                   │
                   ▼
              ┌─────────┐
              │   END   │
              └─────────┘
```

### 5.3 E-Way Bill Generation Flow

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                       E-WAY BILL GENERATION FLOW                            │
└─────────────────────────────────────────────────────────────────────────────┘

                              ┌─────────────┐
                              │   START     │
                              └──────┬──────┘
                                     │
                                     ▼
                         ┌───────────────────────┐
                         │  View Bill Details    │
                         │  page                 │
                         └───────────┬───────────┘
                                     │
                                     ▼
                         ┌───────────────────────┐
                         │  Check bill amount    │
                         │  > ₹50,000?           │
                         └───────────┬───────────┘
                                     │
                    ┌────────────────┴────────────────┐
                    │                                 │
                    ▼                                 ▼
           ┌───────────────┐                ┌───────────────┐
           │     Yes       │                │      No       │
           │ Show E-Way    │                │ Hide E-Way    │
           │ Bill button   │                │ Bill button   │
           └───────┬───────┘                └───────────────┘
                   │
                   ▼
        ┌─────────────────────┐
        │  Check if E-Way     │
        │  bill exists?       │
        └──────────┬──────────┘
                   │
         ┌─────────┴─────────┐
         │                   │
         ▼                   ▼
  ┌─────────────┐    ┌─────────────┐
  │   Exists    │    │ Not exists  │
  │Show details │    │Show create  │
  │& PDF button │    │   button    │
  └─────────────┘    └──────┬──────┘
                            │
                            ▼
                  ┌─────────────────────┐
                  │  Click "Generate    │
                  │  E-Way Bill"        │
                  └──────────┬──────────┘
                             │
                             ▼
                  ┌─────────────────────┐
                  │  Open E-Way Bill    │
                  │  Dialog Form        │
                  └──────────┬──────────┘
                             │
                             ▼
                  ┌─────────────────────┐
                  │  User fills form:   │
                  │  - Transport Mode   │
                  │  - Vehicle Number   │
                  │  - Distance (KM)    │
                  │  - From Address     │
                  │  - To Address       │
                  │  - Transporter Info │
                  └──────────┬──────────┘
                             │
                             ▼
                  ┌─────────────────────┐
                  │  Click "Generate"   │
                  └──────────┬──────────┘
                             │
                             ▼
                  ┌─────────────────────┐
                  │  Validate:          │
                  │  - Vehicle format   │
                  │  - Distance > 0     │
                  │  - Addresses filled │
                  └──────────┬──────────┘
                             │
                             ▼
                  ┌─────────────────────┐
                  │  POST /api/bills/   │
                  │  {id}/eway-bill     │
                  └──────────┬──────────┘
                             │
                             ▼
                  ┌─────────────────────┐
                  │  Server processes:  │
                  │  - Generate E-Way # │
                  │  - Calculate valid  │
                  │    until date       │
                  │    (1 day/100km)    │
                  │  - Create record    │
                  └──────────┬──────────┘
                             │
                             ▼
                  ┌─────────────────────┐
                  │  Close dialog       │
                  │  Show success       │
                  │  Refresh bill view  │
                  └──────────┬──────────┘
                             │
                             ▼
                        ┌─────────┐
                        │   END   │
                        └─────────┘
```

### 5.4 PDF Download Flow

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           PDF DOWNLOAD FLOW                                 │
└─────────────────────────────────────────────────────────────────────────────┘

                              ┌─────────────┐
                              │   START     │
                              └──────┬──────┘
                                     │
                                     ▼
                         ┌───────────────────────┐
                         │  User clicks PDF      │
                         │  download button      │
                         └───────────┬───────────┘
                                     │
                                     ▼
                         ┌───────────────────────┐
                         │  Frontend calls       │
                         │  downloadBillPdf(id)  │
                         └───────────┬───────────┘
                                     │
                                     ▼
                         ┌───────────────────────┐
                         │  Axios GET request    │
                         │  /api/bills/{id}/pdf  │
                         │  responseType: 'blob' │
                         └───────────┬───────────┘
                                     │
                                     ▼
                         ┌───────────────────────┐
                         │  Server receives      │
                         │  request              │
                         └───────────┬───────────┘
                                     │
                                     ▼
                         ┌───────────────────────┐
                         │  Authorize user       │
                         │  (BillPolicy::view)   │
                         └───────────┬───────────┘
                                     │
                                     ▼
                         ┌───────────────────────┐
                         │  Load bill with       │
                         │  relations:           │
                         │  - party              │
                         │  - items.product      │
                         │  - user               │
                         └───────────┬───────────┘
                                     │
                                     ▼
                         ┌───────────────────────┐
                         │  DomPDF renders       │
                         │  bill.blade.php       │
                         │  template             │
                         └───────────┬───────────┘
                                     │
                                     ▼
                         ┌───────────────────────┐
                         │  Generate filename:   │
                         │  {PartyName}-{BillNo} │
                         │  .pdf                 │
                         └───────────┬───────────┘
                                     │
                                     ▼
                         ┌───────────────────────┐
                         │  Set response headers │
                         │  Content-Type: pdf    │
                         │  Content-Disposition  │
                         └───────────┬───────────┘
                                     │
                                     ▼
                         ┌───────────────────────┐
                         │  Return PDF binary    │
                         │  to frontend          │
                         └───────────┬───────────┘
                                     │
                                     ▼
                         ┌───────────────────────┐
                         │  Frontend creates     │
                         │  Blob URL             │
                         └───────────┬───────────┘
                                     │
                                     ▼
                         ┌───────────────────────┐
                         │  Create hidden <a>    │
                         │  element and trigger  │
                         │  download             │
                         └───────────┬───────────┘
                                     │
                                     ▼
                         ┌───────────────────────┐
                         │  Browser downloads    │
                         │  PDF file             │
                         └───────────┬───────────┘
                                     │
                                     ▼
                              ┌─────────────┐
                              │     END     │
                              └─────────────┘
```

### 5.5 GST Calculation Flow

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                          GST CALCULATION FLOW                               │
└─────────────────────────────────────────────────────────────────────────────┘

                              ┌─────────────┐
                              │   START     │
                              └──────┬──────┘
                                     │
                                     ▼
                         ┌───────────────────────┐
                         │  For each line item:  │
                         │  Get quantity, price, │
                         │  GST rate             │
                         └───────────┬───────────┘
                                     │
                                     ▼
                         ┌───────────────────────┐
                         │  Calculate:           │
                         │  Item Subtotal =      │
                         │  Quantity × Price     │
                         └───────────┬───────────┘
                                     │
                                     ▼
                         ┌───────────────────────┐
                         │  Calculate:           │
                         │  GST Amount =         │
                         │  Subtotal × (Rate/100)│
                         └───────────┬───────────┘
                                     │
                                     ▼
                         ┌───────────────────────┐
                         │  Check: Is bill       │
                         │  Inter-State?         │
                         └───────────┬───────────┘
                                     │
                    ┌────────────────┴────────────────┐
                    │                                 │
                    ▼                                 ▼
           ┌───────────────┐                ┌───────────────┐
           │   YES (IGST)  │                │   NO (CGST+   │
           │               │                │     SGST)     │
           └───────┬───────┘                └───────┬───────┘
                   │                                │
                   ▼                                ▼
        ┌─────────────────────┐          ┌─────────────────────┐
        │  IGST = GST Amount  │          │  CGST = GST Amt / 2 │
        │  CGST = 0           │          │  SGST = GST Amt / 2 │
        │  SGST = 0           │          │  IGST = 0           │
        └──────────┬──────────┘          └──────────┬──────────┘
                   │                                │
                   └────────────────┬───────────────┘
                                    │
                                    ▼
                         ┌───────────────────────┐
                         │  Item Total =         │
                         │  Subtotal + GST Amount│
                         └───────────┬───────────┘
                                     │
                                     ▼
                         ┌───────────────────────┐
                         │  Accumulate totals:   │
                         │  Bill Subtotal +=     │
                         │    Item Subtotal      │
                         │  Bill CGST += CGST    │
                         │  Bill SGST += SGST    │
                         │  Bill IGST += IGST    │
                         └───────────┬───────────┘
                                     │
                                     ▼
                         ┌───────────────────────┐
                         │  More items?          │
                         └───────────┬───────────┘
                                     │
                    ┌────────────────┴────────────────┐
                    │ Yes                             │ No
                    ▼                                 ▼
           ┌───────────────┐                ┌───────────────┐
           │  Process next │                │  Calculate    │
           │  item         │                │  final totals │
           └───────────────┘                └───────┬───────┘
                                                    │
                                                    ▼
                                         ┌───────────────────────┐
                                         │  GST Amount =         │
                                         │  CGST + SGST + IGST   │
                                         │                       │
                                         │  Grand Total =        │
                                         │  Subtotal + GST Amt   │
                                         └───────────┬───────────┘
                                                     │
                                                     ▼
                                              ┌─────────────┐
                                              │     END     │
                                              └─────────────┘
```

---

## 6. API Documentation

### Authentication Endpoints

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| POST | `/api/register` | Register new user | No |
| POST | `/api/login` | User login | No |
| POST | `/api/logout` | User logout | Yes |
| GET | `/api/user` | Get current user | Yes |

### Party Endpoints

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/parties` | List parties (paginated) | Yes |
| GET | `/api/parties/all` | List all parties | Yes |
| POST | `/api/parties` | Create party | Yes |
| GET | `/api/parties/{id}` | Get party details | Yes |
| PUT | `/api/parties/{id}` | Update party | Yes |
| DELETE | `/api/parties/{id}` | Delete party | Yes |

### Product Endpoints

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/products` | List products (paginated) | Yes |
| GET | `/api/products/all` | List all active products | Yes |
| POST | `/api/products` | Create product | Yes |
| GET | `/api/products/{id}` | Get product details | Yes |
| PUT | `/api/products/{id}` | Update product | Yes |
| DELETE | `/api/products/{id}` | Delete product | Yes |

### Bill Endpoints

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/bills` | List bills (paginated) | Yes |
| GET | `/api/bills/stats` | Get dashboard statistics | Yes |
| POST | `/api/bills` | Create bill | Yes |
| GET | `/api/bills/{id}` | Get bill details | Yes |
| PUT | `/api/bills/{id}` | Update bill | Yes |
| DELETE | `/api/bills/{id}` | Delete bill | Yes |
| GET | `/api/bills/{id}/pdf` | Download bill PDF | Yes |
| POST | `/api/bills/{id}/eway-bill` | Generate e-way bill | Yes |

### E-Way Bill Endpoints

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/eway-bills` | List e-way bills (paginated) | Yes |
| GET | `/api/eway-bills/{id}` | Get e-way bill details | Yes |
| GET | `/api/eway-bills/{id}/pdf` | Download e-way bill PDF | Yes |

### Settings Endpoints

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/settings` | Get all settings | Yes |
| PUT | `/api/settings/business` | Update business info | Yes |
| PUT | `/api/settings/account` | Update account info | Yes |
| PUT | `/api/settings/password` | Change password | Yes |
| PUT | `/api/settings/bill` | Update bill settings | Yes |

---

## 7. Frontend Structure

### Vue Components

```
resources/js/
├── App.vue                    # Main layout component
├── app.js                     # Entry point
├── bootstrap.js               # Axios configuration
│
├── views/
│   ├── Dashboard.vue          # Analytics dashboard
│   ├── Login.vue              # Login page
│   ├── Register.vue           # Registration page
│   ├── Parties.vue            # Party management
│   ├── Products.vue           # Product management
│   ├── Bills.vue              # Bill listing
│   ├── CreateBill.vue         # Bill creation form
│   ├── ViewBill.vue           # Bill details view
│   ├── EwayBills.vue          # E-way bill listing
│   └── Settings.vue           # User settings
│
├── components/
│   ├── PartyDialog.vue        # Party create/edit modal
│   ├── ProductDialog.vue      # Product create/edit modal
│   └── EwayBillDialog.vue     # E-way bill generation modal
│
├── stores/
│   ├── auth.js                # Authentication state
│   ├── bill.js                # Bills & e-way bills state
│   ├── party.js               # Parties state
│   └── product.js             # Products state
│
├── router/
│   └── index.js               # Vue Router configuration
│
└── plugins/
    └── vuetify.js             # Vuetify theme configuration
```

### Pinia Stores

| Store | State | Actions |
|-------|-------|---------|
| auth | user, token, loading, error | register, login, logout, fetchUser |
| party | parties, currentParty, pagination | fetchParties, createParty, updateParty, deleteParty |
| product | products, currentProduct, pagination | fetchProducts, createProduct, updateProduct, deleteProduct |
| bill | bills, ewayBills, stats, pagination | fetchBills, createBill, downloadPdf, generateEwayBill |

---

## 8. Features

### Core Features
- User registration and authentication
- Multi-user data isolation
- Party (Customer/Supplier) management
- Product catalog with HSN codes
- GST-compliant invoice generation
- E-Way bill generation and management
- Professional PDF export
- Dashboard with analytics

### GST Compliance
- GSTIN validation (15-character format)
- HSN code support (4-8 digits)
- GST rates: 0%, 5%, 12%, 18%, 28%
- CGST/SGST for intra-state transactions
- IGST for inter-state transactions
- HSN-wise tax summary in invoices

### E-Way Bill Features
- Automatic threshold check (₹50,000)
- Vehicle number validation (Indian format)
- Distance-based validity calculation
- Transport mode selection
- Status tracking (Active/Expired)

### Security
- Token-based authentication (Sanctum)
- User authorization policies
- Input validation
- CSRF protection
- Soft deletes for data recovery

---

## 9. Installation Guide

### Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+
- npm

### Backend Setup

```bash
# Clone repository
cd /path/to/billing-software

# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# (Optional) Seed sample data
php artisan db:seed
```

### Frontend Setup

```bash
# Install Node dependencies
npm install

# Development mode
npm run dev

# Production build
npm run build
```

### Running the Application

```bash
# Start Laravel development server
php artisan serve

# In another terminal, start Vite dev server
npm run dev
```

Access the application at: `http://localhost:8000`

---

## 10. Environment Configuration

Key `.env` variables:

```env
APP_NAME=BillFlow
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=sqlite
# For MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=billing
# DB_USERNAME=root
# DB_PASSWORD=

SESSION_DRIVER=file
CACHE_STORE=file
```

---

## License

This project is proprietary software.

---

**Documentation Version:** 1.0
**Last Updated:** December 2024
**Author:** BillFlow Development Team
