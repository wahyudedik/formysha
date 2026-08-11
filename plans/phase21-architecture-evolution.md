# Phase 21 — Architecture Evolution

## Implementation Plan

**Status:** ✅ Completed
**Depends on:** Phase 20 (current: 693 tests, 1668 assertions)
**Completed:** 2026-08-11 (current: 774 tests, 1871 assertions)

---

## Analisis Kesenjangan (Gap Analysis)

### Yang Sudah Ada

| Komponen | Status | Model/File |
|----------|--------|------------|
| Child model | ✅ Ada | `app/Models/Child.php` — `user_id`, `tenant_id`, relationships |
| FamilyMember model | ✅ Ada | `app/Models/FamilyMember.php` — `child_id`, `user_id`, `permission_level` |
| Tenant model | ✅ Ada | `app/Models/Tenant.php` — UUID, `type` (TenantType enum) |
| PatientLink model | ✅ Ada | `app/Models/PatientLink.php` — child ↔ facility link |
| Referral model | ✅ Ada | `app/Models/Referral.php` — facility-to-facility referral |
| Staff model | ✅ Ada | `app/Models/Staff.php` — staff ↔ tenant |
| AuditLog model | ✅ Ada | `app/Models/AuditLog.php` — basic audit trail |
| FamilyMemberPermission | ✅ Ada | `app/Enums/FamilyMemberPermission.php` — View, Edit, Admin |
| PatientLinkStatus | ✅ Ada | `app/Enums/PatientLinkStatus.php` — Pending, Active, Revoked |
| ReferralStatus | ✅ Ada | `app/Enums/ReferralStatus.php` — Pending, Accepted, Completed, Cancelled |
| TenantType | ✅ Ada | `app/Enums/TenantType.php` — Family, Hospital, Clinic, etc. |

### Yang Perlu Dibangun

| Komponen | Prioritas | Deskripsi |
|----------|-----------|-----------|
| **Connection model** | 🔴 High | Model baru — hubungan umum family ↔ organization dengan permission |
| **ConnectionStatus enum** | 🔴 High | Active, Pending, Referred |
| **ConnectionPermission enum** | 🔴 High | View, Comment, Edit, Manage |
| **ConnectionService** | 🔴 High | Service layer untuk semua operasi Connection |
| **ConnectionController** | 🔴 High | CRUD + approval/rejection flow |
| **Connection migration** | 🔴 High | Tabel `connections` baru |
| **Enhanced AuditTrail** | 🟡 Medium | Extend AuditLog dengan `connection_id`, `description`, `permission_level` |
| **ActivityHistory model** | 🟡 Medium | Model baru — log aktivitas di kedua sisi (B2C & B2B) |
| **FamilyTreeService** | 🟡 Medium | Service untuk build & query family tree |
| **B2BAssistedRegistration** | 🟡 Medium | Extend PatientLink dengan invitation flow |
| **ReferralReward model** | 🟢 Low | Tracking reward/milestone untuk referral |
| **Connection views** | 🟡 Medium | Blade views untuk manage connections |

---

## Arsitektur Data

### ER Diagram

```
┌─────────────┐     ┌──────────────┐     ┌──────────────┐
│    users     │────<│  children    │────<│  connections  │
│             │     │              │     │              │
│ id (PK)     │     │ id (PK)      │     │ id (PK)      │
│ name        │     │ user_id (FK) │     │ child_id(FK) │
│ email       │     │ tenant_id    │     │ tenant_id(FK)│
│ role        │     │ name         │     │ status       │
└─────────────┘     │ slug         │     │ permission   │
       │            └──────────────┘     │ invited_by   │
       │                   │             │ invited_at   │
       │                   │             │ accepted_at  │
       │            ┌──────┴───────┐     │ expires_at   │
       │            │family_members│     └──────┬───────┘
       │            │              │            │
       │            │ id (PK)      │     ┌──────┴───────┐
       │            │ child_id(FK) │     │ activity_    │
       │            │ user_id (FK) │     │   history    │
       │            │ permission   │     │              │
       │            │ relationship │     │ id (PK)      │
       │            └──────────────┘     │ connection_id│
       │                                 │ user_id(FK)  │
       │            ┌──────────────┐     │ action       │
       └───────────>│  audit_logs  │     │ entity_type  │
                    │              │     │ entity_id    │
                    │ id (PK)      │     │ ip_address   │
                    │ tenant_id    │     │ user_agent   │
                    │ user_id(FK)  │     │ metadata     │
                    │ event        │     │ created_at   │
                    │ connection_id│     └──────────────┘
                    │ description  │
                    │ permission   │
                    │ ip_address   │
                    │ user_agent   │
                    │ created_at   │
                    └──────────────┘

┌─────────────┐     ┌──────────────┐
│   tenants   │────<│  connections │
│             │     │              │
│ id (UUID PK)│     │ tenant_id(FK)│
│ type        │     │ child_id(FK) │
│ name        │     └──────────────┘
│ slug        │
└─────────────┘
```

### Tabel `connections` (Baru)

```sql
CREATE TABLE connections (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    child_id BIGINT UNSIGNED NOT NULL,
    tenant_id CHAR(36) NOT NULL,
    status ENUM('active','pending','referred') DEFAULT 'pending',
    permission ENUM('view','comment','edit','manage') DEFAULT 'view',
    invited_by BIGINT UNSIGNED NULL,
    invited_at TIMESTAMP NULL,
    accepted_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    notes TEXT NULL,
    metadata JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (child_id) REFERENCES children(id) ON DELETE CASCADE,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (invited_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_child_status (child_id, status),
    INDEX idx_tenant_status (tenant_id, status),
    UNIQUE KEY unique_child_tenant (child_id, tenant_id)
);
```

### Tabel `activity_history` (Baru)

```sql
CREATE TABLE activity_history (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    connection_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(100) NULL,
    entity_id BIGINT UNSIGNED NULL,
    description TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    metadata JSON NULL,
    created_at TIMESTAMP NULL,

    FOREIGN KEY (connection_id) REFERENCES connections(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_connection_created (connection_id, created_at),
    INDEX idx_user_created (user_id, created_at),
    INDEX idx_action (action)
);
```

### Alter `audit_logs` (Extend)

```sql
ALTER TABLE audit_logs
    ADD COLUMN connection_id BIGINT UNSIGNED NULL AFTER user_id,
    ADD COLUMN description TEXT NULL AFTER event,
    ADD COLUMN permission VARCHAR(20) NULL AFTER description,
    ADD FOREIGN KEY (connection_id) REFERENCES connections(id) ON DELETE SET NULL,
    ADD INDEX idx_connection (connection_id);
```

---

## Sub-Phase Breakdown

### Sub-Phase 21.1 — Enums & Migration

**Goal:** Buat enum baru dan migration untuk tabel `connections` dan `activity_history`.

**Files to create/modify:**

| File | Action |
|------|--------|
| `app/Enums/ConnectionStatus.php` | CREATE — Active, Pending, Referred |
| `app/Enums/ConnectionPermission.php` | CREATE — View, Comment, Edit, Manage |
| `database/migrations/2026_08_11_000001_create_connections_table.php` | CREATE |
| `database/migrations/2026_08_11_000002_create_activity_history_table.php` | CREATE |
| `database/migrations/2026_08_11_000003_extend_audit_logs_table.php` | CREATE |

**Enum specs:**

```
ConnectionStatus:
  - Active  = 'active'   — label: 'Aktif'
  - Pending = 'pending'   — label: 'Menunggu'
  - Referred = 'referred' — label: 'Direferensikan'

ConnectionPermission:
  - View    = 'view'    — label: 'Hanya Lihat', level: 1
  - Comment = 'comment' — label: 'Lihat & Komentar', level: 2
  - Edit    = 'edit'    — label: 'Lihat, Komentar & Edit', level: 3
  - Manage  = 'manage'  — label: 'Akses Penuh', level: 4
```

---

### Sub-Phase 21.2 — Models & Relationships

**Goal:** Buat Connection model dan update model yang ada.

**Files to create/modify:**

| File | Action |
|------|--------|
| `app/Models/Connection.php` | CREATE — Model baru dengan relationships |
| `app/Models/ActivityHistory.php` | CREATE — Model baru untuk activity log |
| `app/Models/Child.php` | MODIFY — Tambah `connections()` relationship |
| `app/Models/Tenant.php` | MODIFY — Tambah `connections()` relationship |
| `app/Models/User.php` | MODIFY — Tambah `connections()` relationship |
| `app/Models/AuditLog.php` | MODIFY — Tambah `connection()` relationship |

**Connection model relationships:**

```
Connection:
  - child(): BelongsTo → Child
  - tenant(): BelongsTo → Tenant
  - invitedBy(): BelongsTo → User
  - activities(): HasMany → ActivityHistory
  - auditLogs(): HasMany → AuditLog
```

**ActivityHistory model relationships:**

```
ActivityHistory:
  - connection(): BelongsTo → Connection
  - user(): BelongsTo → User
```

**Methods on Connection:**

```
- isActive(): bool
- isPending(): bool
- isReferred(): bool
- hasPermission(ConnectionPermission $perm): bool
- canView(): bool
- canComment(): bool
- canEdit(): bool
- canManage(): bool
- approve(): void
- reject(): void
- revoke(): void
- updatePermission(ConnectionPermission $perm): void
```

---

### Sub-Phase 21.3 — ConnectionService

**Goal:** Buat service layer untuk semua operasi Connection.

**Files to create:**

| File | Action |
|------|--------|
| `app/Services/ConnectionService.php` | CREATE |

**Methods:**

```
ConnectionService:
  - create(Child $child, Tenant $tenant, ConnectionPermission $perm, ?User $invitedBy): Connection
  - approve(Connection $connection): void
  - reject(Connection $connection): void
  - revoke(Connection $connection): void
  - updatePermission(Connection $connection, ConnectionPermission $perm): void
  - getByChild(Child $child): Collection
  - getByTenant(Tenant $tenant): Collection
  - getActiveConnections(Child $child): Collection
  - getActiveConnections(Tenant $tenant): Collection
  - hasConnection(Child $child, Tenant $tenant): bool
  - logActivity(Connection $connection, User $user, string $action, ?Model $entity, ?string $description): ActivityHistory
  - getActivityHistory(Connection $connection, int $limit): Collection
  - checkExpiredConnections(): int (for scheduled command)
```

---

### Sub-Phase 21.4 — ConnectionController (Web)

**Goal:** Buat controller untuk manage connections di web interface.

**Files to create:**

| File | Action |
|------|--------|
| `app/Http/Controllers/ConnectionController.php` | CREATE |

**Routes (di `routes/web.php`):**

```
GET    /children/{child}/connections              → index
GET    /children/{child}/connections/create        → create
POST   /children/{child}/connections               → store
GET    /children/{child}/connections/{connection}  → show
PUT    /children/{child}/connections/{connection}  → update
DELETE /children/{child}/connections/{connection}  → destroy
POST   /children/{child}/connections/{connection}/approve  → approve
POST   /children/{child}/connections/{connection}/reject   → reject
POST   /children/{child}/connections/{connection}/revoke   → revoke
```

**Views to create:**

| File | Action |
|------|--------|
| `resources/views/connections/index.blade.php` | CREATE |
| `resources/views/connections/create.blade.php` | CREATE |
| `resources/views/connections/show.blade.php` | CREATE |
| `resources/views/connections/edit.blade.php` | CREATE |

---

### Sub-Phase 21.5 — ConnectionController (API)

**Goal:** Buat API endpoints untuk connections.

**Files to create/modify:**

| File | Action |
|------|--------|
| `app/Http/Controllers/Api/ConnectionApiController.php` | CREATE |
| `app/Http/Resources/ConnectionResource.php` | CREATE |
| `routes/api.php` | MODIFY — tambah connection routes |

**API Routes:**

```
GET    /api/children/{child}/connections              → index
POST   /api/children/{child}/connections               → store
GET    /api/children/{child}/connections/{connection}  → show
PUT    /api/children/{child}/connections/{connection}  → update
DELETE /api/children/{child}/connections/{connection}  → destroy
POST   /api/children/{child}/connections/{connection}/approve  → approve
POST   /api/children/{child}/connections/{connection}/reject   → reject
POST   /api/children/{child}/connections/{connection}/revoke   → revoke
GET    /api/children/{child}/connections/{connection}/activities → activities
```

---

### Sub-Phase 21.6 — B2B Assisted Registration Flow

**Goal:** Extend PatientLink dengan invitation flow yang lebih lengkap.

**Files to modify:**

| File | Action |
|------|--------|
| `app/Models/PatientLink.php` | MODIFY — tambah `sendInvitation()`, `claimProfile()` methods |
| `app/Http/Controllers/FacilityAdmin/PatientLinkController.php` | MODIFY — extend with invitation flow |
| `app/Services/ConnectionService.php` | MODIFY — tambah `assistedRegistration()` method |

**Flow:**

```
1. Facility creates patient profile (existing: PatientLink)
2. Facility sends invitation (email/WhatsApp) — new
3. Family receives invitation with link code
4. Family creates account (if new) or logs in
5. Family claims profile — new
6. Connection is created automatically — new
7. Permission is set by family — new
```

---

### Sub-Phase 21.7 — Family Tree Service & Visualization

**Goal:** Buat service untuk build family tree dan views untuk visualisasi.

**Files to create:**

| File | Action |
|------|--------|
| `app/Services/FamilyTreeService.php` | CREATE |
| `resources/views/family-tree/index.blade.php` | CREATE |

**FamilyTreeService methods:**

```
FamilyTreeService:
  - getTree(Child $child): array (hierarchical data)
  - getFamilyMembers(Child $child): Collection
  - getConnections(Child $child): Collection
  - getOrganizations(Child $child): Collection
  - getTimeline(Child $child, int $limit): Collection
  - getAccessHistory(Child $child, int $limit): Collection
```

**Routes:**

```
GET /children/{child}/family-tree → FamilyTreeController@index
```

---

### Sub-Phase 21.8 — Enhanced Audit Trail

**Goal:** Extend AuditLog dengan connection context dan description.

**Files to modify:**

| File | Action |
|------|--------|
| `app/Services/AuditService.php` | MODIFY — tambah `logWithConnection()` method |
| `app/Models/AuditLog.php` | MODIFY — tambah `connection()` relationship |

**Enhanced audit format:**

```
WHO: [user_id] — [user_name]
WHAT: [action] — [entity_type] [entity_id]
WHEN: [timestamp]
WHERE: [ip_address] — [device] — [user_agent]
WHY: [reason] (optional)
PERMISSION: [permission_level] — [connection_id]
```

---

### Sub-Phase 21.9 — Referral Enhancement

**Goal:** Extend Referral untuk support B2B → B2C referrals.

**Files to create/modify:**

| File | Action |
|------|--------|
| `app/Enums/ReferralType.php` | CREATE — FacilityToFacility, FacilityToFamily |
| `app/Models/Referral.php` | MODIFY — tambah `type` field |
| `database/migrations/2026_08_11_000004_add_type_to_referrals_table.php` | CREATE |
| `app/Services/ReferralService.php` | CREATE |

**ReferralService methods:**

```
ReferralService:
  - createFacilityReferral(Child $child, Tenant $from, Tenant $to, User $staff, string $reason): Referral
  - createFamilyReferral(Child $child, Tenant $from, string $email, string $phone): Referral
  - acceptReferral(Referral $referral): void
  - completeReferral(Referral $referral): void
  - cancelReferral(Referral $referral): void
  - getReferralStats(Tenant $tenant): array
  - getRewardMilestones(Tenant $tenant): array
```

---

### Sub-Phase 21.10 — Middleware & Authorization

**Goal:** Buat middleware untuk Connection-based access control.

**Files to create:**

| File | Action |
|------|--------|
| `app/Http/Middleware/EnsureConnectionPermission.php` | CREATE |

**Middleware logic:**

```
EnsureConnectionPermission:
  - Check if user has active connection to the child
  - Check if connection has required permission level
  - If owner (user_id = child.user_id), always allow
  - If family member, check FamilyMemberPermission
  - If organization staff, check ConnectionPermission
```

**Register in `bootstrap/app.php`:**

```
'connection.permission' => EnsureConnectionPermission::class
```

---

### Sub-Phase 21.11 — Tests

**Goal:** Tulis tests untuk semua fitur baru.

**Files to create:**

| File | Tests |
|------|-------|
| `tests/Feature/ConnectionTest.php` | ~15 tests — CRUD, approve, reject, revoke, permissions |
| `tests/Feature/Api/ConnectionApiTest.php` | ~10 tests — API endpoints |
| `tests/Unit/Services/ConnectionServiceTest.php` | ~10 tests — service methods |
| `tests/Unit/Services/FamilyTreeServiceTest.php` | ~8 tests — tree building |
| `tests/Unit/Services/ReferralServiceTest.php` | ~8 tests — referral flow |
| `tests/Feature/ActivityHistoryTest.php` | ~6 tests — activity logging |

**Expected total:** ~57 new tests

---

### Sub-Phase 21.12 — i18n & Documentation

**Goal:** Tambah translation keys dan update dokumentasi.

**Files to modify:**

| File | Action |
|------|--------|
| `lang/id/app.php` | MODIFY — tambah connection, activity, tree keys |
| `lang/en/app.php` | MODIFY — tambah English translations |
| `README.md` | MODIFY — update Phase 21 status |
| `AGENTS.md` | MODIFY — tambah Connection conventions |
| `FEATURES.md` | MODIFY — update Phase 21 features |
| `ROADMAP.md` | MODIFY — update Phase 21 status |

---

## Execution Order

```
21.1  Enums & Migration
  ↓
21.2  Models & Relationships
  ↓
21.3  ConnectionService
  ↓
21.4  ConnectionController (Web)
  ↓
21.5  ConnectionController (API)
  ↓
21.6  B2B Assisted Registration
  ↓
21.7  Family Tree Service & Visualization
  ↓
21.8  Enhanced Audit Trail
  ↓
21.9  Referral Enhancement
  ↓
21.10 Middleware & Authorization
  ↓
21.11 Tests
  ↓
21.12 i18n & Documentation
```

---

## File Summary

### New Files (18)

| # | File | Type |
|---|------|------|
| 1 | `app/Enums/ConnectionStatus.php` | Enum |
| 2 | `app/Enums/ConnectionPermission.php` | Enum |
| 3 | `app/Enums/ReferralType.php` | Enum |
| 4 | `database/migrations/2026_08_11_000001_create_connections_table.php` | Migration |
| 5 | `database/migrations/2026_08_11_000002_create_activity_history_table.php` | Migration |
| 6 | `database/migrations/2026_08_11_000003_extend_audit_logs_table.php` | Migration |
| 7 | `database/migrations/2026_08_11_000004_add_type_to_referrals_table.php` | Migration |
| 8 | `app/Models/Connection.php` | Model |
| 9 | `app/Models/ActivityHistory.php` | Model |
| 10 | `app/Services/ConnectionService.php` | Service |
| 11 | `app/Services/FamilyTreeService.php` | Service |
| 12 | `app/Services/ReferralService.php` | Service |
| 13 | `app/Http/Controllers/ConnectionController.php` | Controller |
| 14 | `app/Http/Controllers/Api/ConnectionApiController.php` | API Controller |
| 15 | `app/Http/Resources/ConnectionResource.php` | API Resource |
| 16 | `app/Http/Middleware/EnsureConnectionPermission.php` | Middleware |
| 17 | `resources/views/connections/index.blade.php` | View |
| 18 | `resources/views/connections/create.blade.php` | View |
| 19 | `resources/views/connections/show.blade.php` | View |
| 20 | `resources/views/connections/edit.blade.php` | View |
| 21 | `resources/views/family-tree/index.blade.php` | View |

### Modified Files (12)

| # | File | Changes |
|---|------|---------|
| 1 | `app/Models/Child.php` | Add `connections()` relationship |
| 2 | `app/Models/Tenant.php` | Add `connections()` relationship |
| 3 | `app/Models/User.php` | Add `connections()` relationship |
| 4 | `app/Models/AuditLog.php` | Add `connection()` relationship |
| 5 | `app/Models/PatientLink.php` | Add invitation/claim methods |
| 6 | `app/Models/Referral.php` | Add `type` field |
| 7 | `app/Services/AuditService.php` | Add `logWithConnection()` |
| 8 | `app/Http/Controllers/FacilityAdmin/PatientLinkController.php` | Extend invitation flow |
| 9 | `routes/web.php` | Add connection & family-tree routes |
| 10 | `routes/api.php` | Add connection API routes |
| 11 | `bootstrap/app.php` | Register connection.permission middleware |
| 12 | `lang/id/app.php` + `lang/en/app.php` | Add translation keys |

### New Tests (6 files, ~57 tests)

| # | File | Tests |
|---|------|-------|
| 1 | `tests/Feature/ConnectionTest.php` | ~15 |
| 2 | `tests/Feature/Api/ConnectionApiTest.php` | ~10 |
| 3 | `tests/Unit/Services/ConnectionServiceTest.php` | ~10 |
| 4 | `tests/Unit/Services/FamilyTreeServiceTest.php` | ~8 |
| 5 | `tests/Unit/Services/ReferralServiceTest.php` | ~8 |
| 6 | `tests/Feature/ActivityHistoryTest.php` | ~6 |

---

## Estimated Impact

- **New tests:** ~81 tests (18 new test files)
- **Total tests after Phase 21:** 774 tests, 1871 assertions
- **New database tables:** 2 (connections, activity_history)
- **Altered tables:** 2 (audit_logs, referrals)
- **New enums:** 3 (ConnectionStatus, ConnectionPermission, ReferralType)
- **New models:** 2 (Connection, ActivityHistory)
- **New services:** 3 (ConnectionService, FamilyTreeService, ReferralService)
- **New controllers:** 2 (ConnectionController, ConnectionApiController)
- **New views:** 5 (connections/* + family-tree/*)
- **New middleware:** 1 (EnsureConnectionPermission)

---

## Implementation Summary

**Total Files Created:** 18
**Total Files Modified:** 12
**Total New Tests:** 81 (across 6 test files)
**Final Test Count:** 774 tests, 1871 assertions — all passing

### Sub-Phase Completion

| Sub-Phase | Description | Status |
|-----------|-------------|--------|
| 21.1 | Enums & Migration | ✅ |
| 21.2 | Models & Relationships | ✅ |
| 21.3 | ConnectionService | ✅ |
| 21.4 | ConnectionController (Web) | ✅ |
| 21.5 | ConnectionController (API) | ✅ |
| 21.6 | B2B Assisted Registration Flow | ✅ |
| 21.7 | Family Tree Service & Visualization | ✅ |
| 21.8 | Enhanced Audit Trail | ✅ |
| 21.9 | Referral Enhancement | ✅ |
| 21.10 | Middleware & Authorization | ✅ |
| 21.11 | Tests | ✅ |
| 21.12 | i18n & Documentation | ✅ |
