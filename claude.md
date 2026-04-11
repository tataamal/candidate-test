# CLT Toolbox — Claude Code Instructions

## Project Overview

CLT Toolbox adalah platform manajemen CLT (Cross-Laminated Timber) berbasis Laravel 13.
Sistem ini mengelola data Supplier, Layup, dan Layer dengan sistem autentikasi berbasis
Sanctum token yang disimpan di httpOnly cookie.

---

## Tech Stack

| Layer          | Technology                        |
| -------------- | --------------------------------- |
| Backend        | Laravel 13                        |
| Authentication | Laravel Sanctum (httpOnly Cookie) |
| API Docs       | Scribe                            |
| Frontend       | Blade + Tailwind CSS              |
| Font           | Plus Jakarta Sans                 |
| Database       | MySQL                             |
| Export/Import  | Maatwebsite Excel (planned)       |

---

## Architecture Pattern

Proyek ini menggunakan **Repository + Service Pattern**:

```
Request → Controller → Service → Repository → Model → DB
```

| Layer       | Responsibility                             | Location                       |
| ----------- | ------------------------------------------ | ------------------------------ |
| Controller  | Terima request, return response, authorize | `app/Http/Controllers/`        |
| Service     | Business logic                             | `app/Services/`                |
| Repository  | Query ke database                          | `app/Repositories/`            |
| Interface   | Kontrak repository                         | `app/Repositories/Interfaces/` |
| Model       | Definisi tabel & relasi                    | `app/Models/`                  |
| FormRequest | Validasi input                             | `app/Http/Requests/`           |
| Resource    | Format JSON response                       | `app/Http/Resources/`          |
| Policy      | Otorisasi per model                        | `app/Policies/`                |

---

## Database Structure

### `users`

```
id, name, email, password, role (enum: admin|user|supplier), timestamps
```

### `suppliers`

```
id, user_id (FK → users), name, timestamps
```

### `clt_layups`

```
id, supplier_id (FK → suppliers), name, timestamps
```

### `clt_layers`

```
id, layup_id (FK → clt_layups), layer_order, thickness, width, angle, timestamps
```

---

## Business Rules & Roles

### Role: `admin`

- Akses penuh ke semua fitur
- CRUD semua Supplier, Layup, Layer
- Export semua data (tidak terbatas)
- Import untuk semua supplier
- Membuat akun Supplier baru

### Role: `user`

- Read-only — hanya bisa melihat data
- Tidak bisa create, update, delete apapun
- Bisa akses dokumentasi API

### Role: `supplier`

- Bisa tambah/edit/hapus Layup **miliknya sendiri**
- Bisa tambah/edit/hapus Layer **di bawah Layup miliknya**
- Tidak bisa mengakses data Supplier lain
- Bisa export data **miliknya sendiri**
- Bisa import Layup & Layer **untuk dirinya sendiri**
- Tidak bisa create Supplier baru

### Supplier Creation Flow (Admin Only)

```
Admin POST /api/v1/suppliers { name, email, password? }
    ↓
User::firstOrCreate(email) → role: supplier
    ↓
Supplier::create({ name, user_id })
    ↓
Return Supplier + User data
```

> Jika email sudah terdaftar → ambil user existing, buat Supplier baru untuknya.
> Password nullable — jika user sudah ada, password lama tetap dipakai.

---

## Authentication Flow

```
POST /api/v1/login { email, password }
    ↓
Return httpOnly Cookie: auth_token (60 menit)
    ↓
Setiap request → AuthenticateFromCookie middleware
    ↓
Cookie di-decode (urldecode %7C → |) → set Authorization header
    ↓
Sanctum validasi token
```

### Middleware Stack (api)

```php
HandleCors::class
EnsureFrontendRequestsAreStateful::class
AuthenticateFromCookie::class  // custom middleware
```

### Token Storage

- **Web/Browser**: httpOnly Cookie (`auth_token`, 60 menit, SameSite: strict)
- **Mobile/Postman**: Bearer token dari response body (local env only)
- **Security**: Cookie tidak bisa diakses JS — aman dari XSS

---

## API Routes

```
POST   /api/v1/register
POST   /api/v1/login
POST   /api/v1/logout                          [auth:sanctum]
GET    /api/v1/me                              [auth:sanctum]

GET    /api/v1/suppliers                       [auth:sanctum]
POST   /api/v1/suppliers                       [auth:sanctum] admin only
GET    /api/v1/suppliers/{supplier}            [auth:sanctum]
PUT    /api/v1/suppliers/{supplier}            [auth:sanctum] admin only
DELETE /api/v1/suppliers/{supplier}            [auth:sanctum] admin only

GET    /api/v1/suppliers/{supplier}/layups                    [auth:sanctum]
POST   /api/v1/suppliers/{supplier}/layups                    [auth:sanctum] admin|supplier(own)
GET    /api/v1/suppliers/{supplier}/layups/{layup}            [auth:sanctum]
PUT    /api/v1/suppliers/{supplier}/layups/{layup}            [auth:sanctum] admin|supplier(own)
DELETE /api/v1/suppliers/{supplier}/layups/{layup}            [auth:sanctum] admin|supplier(own)

GET    /api/v1/suppliers/{supplier}/layups/{layup}/layers              [auth:sanctum]
POST   /api/v1/suppliers/{supplier}/layups/{layup}/layers              [auth:sanctum] admin|supplier(own)
GET    /api/v1/suppliers/{supplier}/layups/{layup}/layers/{layer}      [auth:sanctum]
PUT    /api/v1/suppliers/{supplier}/layups/{layup}/layers/{layer}      [auth:sanctum] admin|supplier(own)
DELETE /api/v1/suppliers/{supplier}/layups/{layup}/layers/{layer}      [auth:sanctum] admin|supplier(own)

GET    /api/v1/suppliers/{supplier}/export     [auth:sanctum] admin|supplier(own) [PLANNED]
POST   /api/v1/suppliers/{supplier}/import     [auth:sanctum] admin|supplier(own) [PLANNED]
```

---

## Policy Map

| Model    | Policy         | Admin  | User         | Supplier     |
| -------- | -------------- | ------ | ------------ | ------------ |
| Supplier | SupplierPolicy | ✅ all | 👁️ view only | 👁️ view only |
| Layup    | LayupPolicy    | ✅ all | 👁️ view only | ✅ own only  |
| Layer    | LayerPolicy    | ✅ all | 👁️ view only | ✅ own only  |

> `before()` method di setiap Policy — admin selalu return `true`.

---

## Model Relationships

```php
User        → hasOne(Supplier)
Supplier    → belongsTo(User)
Supplier    → hasMany(Layup)
Layup       → belongsTo(Supplier)
Layup       → hasMany(Layers)
Layers      → belongsTo(Layup)
```

---

## Export / Import Plan (PLANNED)

### Export

```
GET /api/v1/suppliers/{supplier}/export
→ Download .xlsx

Format Excel:
├── Sheet 1: Layups  [No, Layup Name, Created At]
└── Sheet 2: Layers  [No, Layup Name, Order, Thickness, Width, Angle, Created At]

Admin   → export semua supplier
Supplier → export miliknya saja
```

### Import

```
POST /api/v1/suppliers/{supplier}/import
Body: multipart/form-data { file: .xlsx }

Template Excel:
├── Sheet 1: Layups
└── Sheet 2: Layers

Admin   → import untuk supplier manapun
Supplier → import untuk dirinya saja
```

Library: `maatwebsite/excel`

---

## Frontend Structure (Blade + Tailwind)

```
resources/views/
├── welcome.blade.php          ← Landing page dengan tombol Login/Register
├── auth/
│   ├── login.blade.php        ← Halaman login
│   └── register.blade.php     ← Halaman register
├── layouts/
│   ├── app.blade.php          ← Main layout (sidebar + navbar)
│   └── guest.blade.php        ← Layout untuk auth pages
├── dashboard.blade.php        ← Dashboard utama
├── profile/
│   └── index.blade.php        ← Halaman profil user
├── suppliers/
│   ├── index.blade.php        ← List supplier + search + export button
│   ├── create.blade.php       ← Form tambah supplier
│   └── show.blade.php         ← Detail supplier + list layup
├── layups/
│   ├── index.blade.php        ← List layup milik supplier
│   ├── create.blade.php       ← Form tambah layup
│   └── show.blade.php         ← Detail layup + list layer
└── layers/
    ├── index.blade.php        ← List layer milik layup
    └── create.blade.php       ← Form tambah layer
```

### Design System

- **Font**: Plus Jakarta Sans (300–800)
- **Primary**: Green (`#15803d` / green-700)
- **Background**: Stone (`#fafaf9` / stone-50)
- **Style**: Clean, professional, natural/forestry aesthetic
- **Responsive**: Mobile-first, semua layar

### Toast Notifications

Export/Import button yang belum diimplementasi → tampilkan toast:

```
"Fitur ini masih dalam pengembangan 🚧"
```

---

## Coding Conventions

### Controller

```php
// Selalu gunakan authorize() manual, bukan authorizeResource()
$this->authorize('create', Supplier::class);

// Selalu inject Service via constructor
public function __construct(protected SupplierService $service) {}
```

### Response Format

```json
// Success
{ "success": true, "message": "...", "data": {...} }

// List with pagination
{ "success": true, "data": [...], "meta": { "total", "per_page", "current_page", "last_page" } }

// Error
{ "success": false, "message": "..." }
```

### Naming

| Item        | Convention                     | Example                       |
| ----------- | ------------------------------ | ----------------------------- |
| Model       | Singular                       | `Supplier`, `Layup`, `Layers` |
| Controller  | Singular + Controller          | `SupplierController`          |
| Service     | Singular + Service             | `SupplierService`             |
| Repository  | Singular + Repository          | `SupplierRepository`          |
| Policy      | Singular + Policy              | `SupplierPolicy`              |
| FormRequest | Store/Update + Model + Request | `StoreSupplierRequest`        |
| Resource    | Singular + Resource            | `SupplierResource`            |

---

## Environment Variables

```env
APP_URL=http://127.0.0.1:8000
FRONTEND_URL=http://localhost:3000
SANCTUM_STATEFUL_DOMAINS=localhost:3000
SESSION_DRIVER=cookie
SESSION_LIFETIME=60
SCRIBE_AUTH_KEY=           # Token untuk Scribe response calls
```

---

## API Documentation

Docs tersedia di `/docs` (dilindungi middleware `web,auth`).
Generate ulang dengan:

```bash
php artisan scribe:generate
```

---

## Development Commands

```bash
# Jalankan server
php artisan serve

# Generate API docs
php artisan scribe:generate

# Clear semua cache
php artisan config:clear && php artisan cache:clear

# Jalankan migration
php artisan migrate

# Tinker
php artisan tinker

# Lihat semua route
php artisan route:list
```

---

## Current Status

| Feature                         | Status             |
| ------------------------------- | ------------------ |
| Migration & Model               | ✅ Done            |
| CRUD Supplier                   | ✅ Done            |
| CRUD Layup (nested)             | ✅ Done            |
| CRUD Layer (nested)             | ✅ Done            |
| Auth (httpOnly Cookie)          | ✅ Done            |
| Policy (Supplier, Layup, Layer) | ✅ Done            |
| Repository + Service Pattern    | ✅ Done (Supplier) |
| API Documentation (Scribe)      | ✅ Done            |
| Frontend Welcome Page           | ✅ Done            |
| Frontend Auth Pages             | 🔄 In Progress     |
| Frontend Dashboard              | 🔄 In Progress     |
| Frontend Supplier/Layup/Layer   | 🔄 In Progress     |
| Export Excel                    | 📋 Planned         |
| Import Excel                    | 📋 Planned         |
