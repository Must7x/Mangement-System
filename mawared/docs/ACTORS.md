# Actors — Mawared (موارد)

Documentation for UML actors in the MTNIMA public assets management system.

## Actor summary

| Actor | Arabic | Login account | Representation in system |
|-------|--------|---------------|--------------------------|
| **Technical Administrator** | المسؤول التقني | Yes (`users`, role `technical_admin`) | Authenticated user; manages users and all modules |
| **Warehouse Keeper** | أمين المخزن | Yes (`users`, role `warehouse_keeper`) | Authenticated user; day-to-day inventory, assignments, maintenance |
| **Department Manager** | مدير القسم | No | Operational actor; organizational context via `departments` |
| **Employee** | الموظف | No | Operational actor; record in `employees`, linked to assignments |
| **Maintenance Technician** | فني الصيانة | No | Operational actor; `technician_name` on `maintenances` (free text) |

## System login actors vs operational actors

### System login actors (authenticated)

Only these two actors have **system login accounts** today:

1. **Technical Administrator**
2. **Warehouse Keeper**

They authenticate via `/login` (session-based). Roles are stored in `users.role`.

### Operational actors (no login)

These three actors are **not user accounts**. They are represented through **records and workflows** managed by login actors:

| Operational actor | How the system represents them | Typical workflow touchpoints |
|-------------------|--------------------------------|----------------------------|
| **Department Manager** | `departments` records define organizational units | Departments CRUD; department name copied into assignment/maintenance context |
| **Employee** | `employees` records (`name`, `department_id`) | Selected when assigning assets; appears in `assignments`, `assignment_histories`, Asset 360 |
| **Maintenance Technician** | `maintenances.technician_name` (free text) | Named when opening/updating a maintenance request; visible in maintenance history and Asset 360 |

> **Important:** Department Manager, Employee, and Maintenance Technician may exist in the real world as people, but they **do not log into Mawared** in the current implementation. Warehouse Keepers and Technical Administrators enter and maintain their data on their behalf.

## Permissions (login actors only)

| Capability | Technical Administrator | Warehouse Keeper |
|------------|-------------------------|------------------|
| Login | Yes | Yes |
| Dashboard, inventory, Asset 360 | Yes | Yes |
| Assignments & assignment history | Yes | Yes |
| Maintenance module | Yes | Yes |
| Departments & employees CRUD | Yes | Yes |
| Reports & settings | Yes | Yes |
| User account management (`/users`) | Yes | No |

## UML diagrams

PlantUML sources (render with [PlantUML](https://plantuml.com/) or a VS Code extension):

- [`uml/actors.puml`](uml/actors.puml) — actor diagram with login vs operational distinction
- [`uml/use-case-overview.puml`](uml/use-case-overview.puml) — use cases linked to all five actors

## Mapping to code

| Actor | Code / schema |
|-------|----------------|
| Technical Administrator | `App\Enums\UserRole::TechnicalAdmin`, `User::isTechnicalAdmin()` |
| Warehouse Keeper | `App\Enums\UserRole::WarehouseKeeper` |
| Department Manager | `App\Models\Department` (no dedicated user role) |
| Employee | `App\Models\Employee` |
| Maintenance Technician | `maintenances.technician_name` (not FK to `users`) |

## Future consideration (out of scope today)

Operational actors could later receive login accounts (e.g. Employee self-service portal, Technician work orders). The current UML includes them as **external operational actors** to reflect real-world roles without implying implemented authentication.
