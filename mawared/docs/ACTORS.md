# Actors — Mawared (موارد)

Documentation for UML actors and **dynamic RBAC** in the MTNIMA public assets management system.

## Actor summary

| Actor | Arabic | Login account | Representation in system |
|-------|--------|---------------|--------------------------|
| **Technical Administrator** | المسؤول التقني | Yes | `users.role_id` → system role `technical_admin` |
| **Inventory Supervisor** | مشرف المخزون | Yes | `users.role_id` → system role `inventory_supervisor` |
| **Warehouse Keeper** | أمين المخزن | Yes | `users.role_id` → system role `warehouse_keeper` |
| **Department Manager** | مدير القسم | No | Operational actor via `departments` |
| **Employee** | الموظف | No | Operational actor via `employees` + assignments |
| **Maintenance Technician** | فني الصيانة | No | Operational actor via `maintenances.technician_name` |

## Dynamic RBAC

Authorization is **database-driven**:

| Table | Purpose |
|-------|---------|
| `roles` | Named roles (3 system roles + custom roles) |
| `permissions` | Granular slugs (`assets.view`, `users.create`, …) |
| `role_permission` | Many-to-many pivot |
| `users.role_id` | Each user has **one** role |

Route protection uses `EnsurePermission` middleware. Permission definitions and system role matrices live in `config/permissions.php`.

### System login roles

| Role slug | Scope |
|-----------|--------|
| `technical_admin` | Users, roles, permissions (via role UI), settings — **no operational modules** |
| `inventory_supervisor` | Full operations: assets (incl. delete), assignments, maintenance, departments, employees, reports, custody receipts |
| `warehouse_keeper` | Daily operations only — **no** asset delete, departments, employees, users, roles, or settings |

Technical Administrators can create **custom roles** and assign permission checkboxes from `/roles`.

## System login actors vs operational actors

### Authenticated actors (3 roles)

1. **Technical Administrator** — `/users`, `/roles`, `/settings`
2. **Inventory Supervisor** — dashboard, inventory, assignments, maintenance, org structure, reports
3. **Warehouse Keeper** — dashboard, inventory, assignments, maintenance, reports (no delete assets, no org CRUD)

They authenticate via `/login` (session-based).

### Operational actors (no login)

| Operational actor | Representation | Workflow touchpoints |
|-------------------|----------------|----------------------|
| **Department Manager** | `departments` records | Department name copied into assignment context |
| **Employee** | `employees` records | Selected at assignment; appears in history and Asset 360 |
| **Maintenance Technician** | `maintenances.technician_name` | Named on maintenance requests |

## Permissions matrix (system roles)

| Capability | Technical Admin | Inventory Supervisor | Warehouse Keeper |
|------------|:---------------:|:--------------------:|:----------------:|
| Users CRUD | Yes | No | No |
| Roles CRUD | Yes | No | No |
| Settings | Yes | No | No |
| Dashboard & reports | No | Yes | Yes |
| Assets view/create/update | No | Yes | Yes |
| Assets delete | No | Yes | No |
| Assignments & history | No | Yes | Yes |
| Maintenance | No | Yes | Yes |
| Departments & employees | No | Yes | No |
| Custody receipts | No | Yes | Yes |

## Mapping to code

| Actor / concept | Code / schema |
|-----------------|---------------|
| Technical Administrator | `Role` slug `technical_admin`, `User::isTechnicalAdmin()` |
| Inventory Supervisor | `Role` slug `inventory_supervisor`, `User::isInventorySupervisor()` |
| Warehouse Keeper | `Role` slug `warehouse_keeper` |
| Permission check | `User::hasPermission('slug')`, `EnsurePermission` middleware |
| Role relation | `User::assignedRole()` → `Role::permissions()` |
| Department Manager | `App\Models\Department` |
| Employee | `App\Models\Employee` |
| Maintenance Technician | `maintenances.technician_name` |

## UML diagrams

- [`uml/actors.puml`](uml/actors.puml)
- [`uml/use-case-overview.puml`](uml/use-case-overview.puml)
- [`uml/class-diagram.puml`](uml/class-diagram.puml)
- [`uml/erd.puml`](uml/erd.puml)

## Future consideration

Operational actors could later receive login accounts. Categories, QR codes, notifications, and PDF exports are documented in README **Future Enhancements**.
