# نظام إدارة الموارد والمعدات العمومية — موارد (Mawared)

نظام ويب لإدارة جرد الأصول وتخصيص العهد داخل **وزارة التحول الرقمي وابتكار الإدارة العمومية (MTNIMA)** — موريتانيا.

يهدف المشروع إلى رقمنة دورة حياة العتاد الحكومي: من التسجيل في المخزن، إلى التخصيص للموظفين، مع تتبع الحالة والسجل التاريخي للعهد.

## التقنيات

| المكوّن | الإصدار / الأداة |
|---------|------------------|
| PHP | ^8.3 |
| Laravel | ^13 |
| قاعدة البيانات | SQLite (افتراضي) |
| الواجهة | Blade، RTL، `public/css/theme.css` |
| الاختبارات | PHPUnit |

---

## الميزات الحالية

| الوحدة | المسار | الوصف |
|--------|--------|-------|
| **لوحة التحكم** | `/dashboard` | إحصائيات (إجمالي، مخزن، نشط، صيانة) ونظرة عامة |
| **إدارة المخزون** | `/inventory` | عرض الأصول، بحث، وتصفح |
| **تسجيل العتاد** | `/assets/create` | إضافة / تعديل / حذف: الاسم، النوع، الرقم التسلسلي، الحالة |
| **إدارة التخصيص** | `/assignments` | ربط أصول **مخزن** بموظف وقسم وتاريخ تخصيص |
| **سجل العهد** | `/assignment-history` | الأرشيف الكامل للتخصيصات (نشطة وسابقة) |
| **إدارة الصيانة** | `/maintenances` | تتبع أعطال الأجهزة وطلبات الإصلاح |
| **Asset 360** | `/assets/{asset}` | عرض شامل للجهاز وسجل العهد والصيانة |
| **التقارير** | `/reports` | تحليلات وإحصائيات |
| **الأقسام** | `/departments` | CRUD للأقسام الإدارية |
| **الموظفون** | `/employees` | CRUD للموظفين وربطهم بالأقسام |
| **إدارة المستخدمين** | `/users` | CRUD للحسابات *(المسؤول التقني)* |
| **إدارة الأدوار** | `/roles` | إنشاء أدوار مخصصة وتعيين الصلاحيات *(المسؤول التقني)* |
| **الإعدادات** | `/settings` | مركز الإدارة: الأدوار، فهرس الصلاحيات، معلومات النظام |
| **الملف الشخصي** | `/profile` | بيانات المستخدم والدور |

### محرك الحالة الآلي

| الحدث | التأثير على العتاد |
|-------|-------------------|
| **تخصيص عهدة** | `مخزن` → `نشط` + إنشاء سجل في `assignments` و `assignment_histories` |
| **سحب عهدة** | حذف من `assignments` + تعيين `returned_date` في السجل + `نشط` → `مخزن` |
| **فتح صيانة** | `مخزن` → `في الصيانة` + سجل في `maintenances` |
| **إكمال / إلغاء صيانة** | `في الصيانة` → `مخزن` + `maintenance_end_date` |

> حالة **«نشط»** تُدار فقط عبر تخصيص العهدة.  
> حالة **«في الصيانة»** تُدار فقط عبر وحدة الصيانة.

جميع عمليات التخصيص والسحب والصيانة تُنفَّذ داخل `DB::transaction` مع `lockForUpdate` لضمان اتساق البيانات.

---

## وحدة الأقسام

إدارة الهيكل التنظيمي للوزارة.

- **المسار:** `/departments`
- **العمليات:** إنشاء، تعديل، حذف (soft delete)، عرض القائمة
- **الحقول:** `name`
- **الاستخدام:** كل موظف ينتمي إلى قسم؛ يُستخدم القسم تلقائياً عند تخصيص العهدة

---

## وحدة الموظفون

سجل الموظفين المستفيدين من العهد.

- **المسار:** `/employees`
- **العمليات:** إنشاء، تعديل، حذف (soft delete)، عرض القائمة
- **الحقول:** `name`، `department_id` (FK → `departments`)
- **الاستخدام:** عند التخصيص يُختار الموظف من قائمة منسدلة؛ يُنسخ اسمه واسم قسمه إلى سجل العهدة

---

## سجل العهد (Assignment History)

أرشيف دائم لجميع دورات التخصيص — لا يُحذف عند سحب العهدة.

- **المسار:** `/assignment-history`
- **يعرض:** العتاد، الموظف، القسم، تاريخ التخصيص، تاريخ الإرجاع، مدة العهدة
- **المنطق:**
  - عند **التخصيص:** يُنشأ سجل جديد بـ `returned_date = null`
  - عند **السحب:** يُحدَّث السجل النشط بتاريخ الإرجاع
- **الجدول:** `assignment_histories`

---

## وحدة الصيانة (Maintenance)

تتبع أعطال الأجهزة وطلبات الإصلاح — **مصدر الحقيقة** لحالة `في الصيانة`.

- **المسار:** `/maintenances`
- **العمليات:** إنشاء طلب، تعديل (مفتوح فقط)، إكمال، إلغاء — **لا حذف** (السجل يُحفظ كأرشيف)
- **الحقول:** `asset_id`, `issue_description`, `priority`, `technician_name`, `status`, `maintenance_start_date`, `maintenance_end_date`, `notes`
- **القواعد:**
  - فتح الصيانة مسموح فقط لأصل بحالة **مخزن** وليس له عهدة نشطة
  - طلب **مفتوح واحد** لكل أصل (`قيد الانتظار` أو `قيد التنفيذ`)
  - عند **الفتح:** `asset.status` → `في الصيانة`
  - عند **الإكمال / الإلغاء:** `asset.status` → `مخزن` + تعيين `maintenance_end_date`
- **فني الصيانة:** `technician_name` نص حر (بدون حساب دخول)
- **الجدول:** `maintenances`

---

## Asset 360

صفحة **قراءة فقط** تجمع كل بيانات الجهاز في مكان واحد.

- **المسار:** `/assets/{asset}`
- **يعرض:**
  - تفاصيل الجهاز (الاسم، الفئة، S/N، تواريخ التسجيل)
  - الحالة الحالية
  - العهدة النشطة أو طلب الصيانة المفتوح (إن وُجد)
  - سجل العهد الكامل (`assignment_histories`)
  - سجل الصيانة الكامل (`maintenances`)
- **الدخول:** رابط «عرض» من صفحة **إدارة المخزون** (`/inventory`)
- **لا يغيّر** منطق التخصيص أو الصيانة — عرض فقط

---

## الممثلون (UML Actors)

النظام يميّز بين **ممثلين بحسابات دخول** و**ممثلين تشغيليين** (بدون حساب).

| الممثل | Actor | حساب دخول | تمثيل في النظام |
|--------|-------|-----------|-----------------|
| **المسؤول التقني** | Technical Administrator | ✅ نعم | `users.role_id` → `roles.slug = technical_admin` |
| **مشرف المخزون** | Inventory Supervisor | ✅ نعم | `users.role_id` → `roles.slug = inventory_supervisor` |
| **أمين المخزن** | Warehouse Keeper | ✅ نعم | `users.role_id` → `roles.slug = warehouse_keeper` |
| **مدير القسم** | Department Manager | ❌ لا | سجلات `departments` |
| **الموظف** | Employee | ❌ لا | سجلات `employees` + العهد |
| **فني الصيانة** | Maintenance Technician | ❌ لا | `maintenances.technician_name` |

> **ثلاثة** أدوار دخول نظامية (+ أدوار مخصصة): المسؤول التقني، مشرف المخزون، وأمين المخزن.  
> مدير القسم والموظف وفني الصيانة **ممثلون تشغيليون** — يُدارون عبر السجلات وسير العمل.

**مخططات UML:** [`docs/uml/actors.puml`](docs/uml/actors.puml) · [`docs/uml/use-case-overview.puml`](docs/uml/use-case-overview.puml)  
**توثيق تفصيلي:** [`docs/ACTORS.md`](docs/ACTORS.md)

---

## الأدوار والصلاحيات *(Dynamic RBAC)*

الصلاحيات مخزّنة في قاعدة البيانات: `roles`، `permissions`، `role_permission`، و`users.role_id`.

| الدور | slug | الصلاحيات |
|-------|------|-----------|
| **المسؤول التقني** | `technical_admin` | المستخدمون، الأدوار، الإعدادات — **بدون** وحدات تشغيلية |
| **مشرف المخزون** | `inventory_supervisor` | تشغيل كامل + الأقسام + الموظفون + حذف المعدات |
| **أمين المخزن** | `warehouse_keeper` | عمليات يومية — **بدون** حذف المعدات أو إدارة الهيكل التنظيمي |

- **المصادقة:** جلسة (Session) عبر `/login`
- **Middleware:** `EnsurePermission` — يحمي المسارات حسب slug الصلاحية
- **أدوار مخصصة:** يمكن للمسؤول التقني إنشاؤها من `/roles`
- **التفاصيل:** [`docs/ACTORS.md`](docs/ACTORS.md) · [`config/permissions.php`](config/permissions.php)

### حسابات تجريبية (بعد `migrate --seed`)

| الدور | البريد | كلمة المرور |
|-------|--------|-------------|
| المسؤول التقني | admin@mtnima.gov.mr | password |
| أمين المخزن | storekeeper@mtnima.gov.mr | password |
| مشرف المخزون | supervisor@mtnima.gov.mr | password |

---

## ملخص مخطط قاعدة البيانات

### `roles`
| العمود | النوع | ملاحظات |
|--------|-------|---------|
| id | bigint | PK |
| name | string | |
| slug | string | unique — `technical_admin`, … |
| description | text | nullable |
| is_system | boolean | الأدوار النظامية لا تُحذف |
| timestamps | | |

### `permissions`
| العمود | النوع | ملاحظات |
|--------|-------|---------|
| id | bigint | PK |
| slug | string | unique — e.g. `assets.view` |
| group | string | للتجميع في الواجهة |
| timestamps | | |

### `role_permission`
| العمود | النوع | ملاحظات |
|--------|-------|---------|
| role_id | FK | → `roles.id` |
| permission_id | FK | → `permissions.id` |

### `users`
| العمود | النوع | ملاحظات |
|--------|-------|---------|
| id | bigint | PK |
| first_name, last_name | string | |
| name | string | مُولَّد تلقائياً |
| phone, job_title, employee_number | string | |
| email | string | unique |
| role_id | FK | → `roles.id` |
| password | string | |
| timestamps | | |

### `assets`
| العمود | النوع | ملاحظات |
|--------|-------|---------|
| id | bigint | PK |
| name | string | |
| type | string | |
| serial_number | string | unique |
| status | string | `مخزن` \| `نشط` \| `في الصيانة` |
| timestamps | | |

### `departments`
| العمود | النوع | ملاحظات |
|--------|-------|---------|
| id | bigint | PK |
| name | string | |
| deleted_at | timestamp | soft delete |
| timestamps | | |

### `employees`
| العمود | النوع | ملاحظات |
|--------|-------|---------|
| id | bigint | PK |
| name | string | |
| department_id | FK | → `departments.id` (nullOnDelete) |
| deleted_at | timestamp | soft delete |
| timestamps | | |

### `assignments` *(العهد النشطة)*
| العمود | النوع | ملاحظات |
|--------|-------|---------|
| id | bigint | PK |
| asset_id | FK | → `assets.id` (unique، cascadeOnDelete) |
| employee_id | FK | → `employees.id` (nullOnDelete) |
| employee_name | string | نسخة محفوظة |
| department | string | اسم القسم محفوظ |
| assigned_date | date | |
| timestamps | | |

### `assignment_histories` *(الأرشيف)*
| العمود | النوع | ملاحظات |
|--------|-------|---------|
| id | bigint | PK |
| asset_id | FK | → `assets.id` (nullOnDelete) |
| employee_id | FK | → `employees.id` (nullOnDelete) |
| employee_name | string | |
| department_name | string | nullable |
| assigned_date | date | |
| returned_date | date | nullable — `null` = عهدة نشطة |
| timestamps | | |

### `maintenances`
| العمود | النوع | ملاحظات |
|--------|-------|---------|
| id | bigint | PK |
| asset_id | FK | → `assets.id` (cascadeOnDelete) |
| issue_description | text | |
| priority | string | `MaintenancePriority` — انظر أدناه |
| technician_name | string | نص حر — فني الصيانة |
| status | string | `MaintenanceStatus` — default `قيد الانتظار` |
| maintenance_start_date | date | |
| maintenance_end_date | date | nullable — `null` = طلب مفتوح |
| notes | text | nullable |
| timestamps | | |

### Enums — الصيانة

**`MaintenancePriority`** (`app/Enums/MaintenancePriority.php`)

| القيمة | Constant |
|--------|----------|
| `منخفضة` | `Low` |
| `متوسطة` | `Medium` |
| `عالية` | `High` |
| `عاجلة` | `Urgent` |

**`MaintenanceStatus`** (`app/Enums/MaintenanceStatus.php`)

| القيمة | Constant | ملاحظات |
|--------|----------|---------|
| `قيد الانتظار` | `Pending` | مفتوح |
| `قيد التنفيذ` | `InProgress` | مفتوح |
| `مكتملة` | `Completed` | مغلق — أرشيف |
| `ملغاة` | `Cancelled` | مغلق — أرشيف |

---

## التثبيت والتشغيل

### المتطلبات

- PHP 8.3+
- Composer
- امتداد SQLite (أو MySQL/PostgreSQL مع تعديل `.env`)

### خطوات الإعداد

```bash
cd mawared
composer install
cp .env.example .env          # Windows: copy .env.example .env
php artisan key:generate
php artisan migrate --seed
```

(اختياري) بناء أصول Vite:

```bash
npm install && npm run build
```

### تشغيل الخادم

```bash
php artisan serve
```

ثم افتح: **http://127.0.0.1:8000**

**Windows — تشغيل سريع:** انقر مرتين على `serve.bat`

---

## الاختبارات

```bash
php artisan test
```

**النتيجة الحالية:** 56 اختباراً · 209 assertion

| الملف | النوع | الغرض |
|-------|-------|--------|
| `tests/Feature/AssignmentStateTest.php` | Feature | محرك حالة العهد |
| `tests/Feature/MaintenanceStateTest.php` | Feature | محرك حالة الصيانة |
| `tests/Feature/Asset360Test.php` | Feature | Asset 360 |
| `tests/Feature/RbacAccessTest.php` | Feature | صلاحيات الأدوار النظامية |
| `tests/Feature/RoleManagementTest.php` | Feature | إدارة الأدوار |
| `tests/Feature/PermissionAccessTest.php` | Feature | صلاحيات مخصصة |
| `tests/Feature/InventorySupervisorRoleTest.php` | Feature | مشرف المخزون |
| `tests/Feature/CustodyReceiptTest.php` | Feature | إيصال العهدة |
| `tests/Feature/LocaleTest.php` | Feature | تعدد اللغات |
| `tests/Feature/ExampleTest.php` | Feature | إعادة توجيه الضيف |
| `tests/Unit/ExampleTest.php` | Unit | PHPUnit أساسي |

### `AssignmentStateTest` (4)

- التخصيص يغيّر الحالة إلى `نشط`
- رفض التخصيص بدون `employee_id`
- السحب يعيد الحالة إلى `مخزن` ويحدّث `assignment_histories`
- منع تعيين `نشط` يدوياً من نموذج العتاد

### `MaintenanceStateTest` (8)

- فتح الصيانة يغيّر الحالة إلى `في الصيانة`
- التحقق من الحقول المطلوبة (`asset_id`, `issue_description`)
- رفض الصيانة لأصل `نشط` أو له طلب مفتوح
- الإكمال والإلغاء يعيدان الأصل إلى `مخزن`
- رفض تخصيص عهدة لأصل `في الصيانة`
- منع تعيين `في الصيانة` يدوياً من نموذج العتاد

### `Asset360Test` (8)

- منع الضيف من الوصول
- عرض تفاصيل الجهاز الأساسية
- عرض العهدة النشطة وسجل العهد والصيانة
- 404 للجهاز غير الموجود

### CI (GitHub Actions)

عند كل `push` أو `pull_request` على فرع `main`، يشغّل workflow [`.github/workflows/laravel.yml`](../.github/workflows/laravel.yml):

- PHP 8.3 · مجلد العمل `mawared/`
- `composer install` → `php artisan test`

---

## هيكل المشروع (ملخّص)

```
mawared/
├── app/
│   ├── Enums/              AssetStatus, UserRole, MaintenancePriority, MaintenanceStatus
│   ├── Http/Controllers/   Auth, Dashboard, Inventory, Asset, Assignment,
│   │                       AssignmentHistory, Maintenance, Department, Employee,
│   │                       Report, User, Role, Settings, Profile
│   ├── Http/Middleware/    EnsurePermission, SetLocale
│   └── Models/             Asset, Assignment, AssignmentHistory, Maintenance,
│                           Department, Employee, User, Role, Permission
├── config/
│   └── permissions.php     RBAC definitions & route map
├── docs/
│   ├── ACTORS.md           Actors & dynamic RBAC
│   └── uml/                PlantUML diagrams
├── database/
│   ├── migrations/
│   ├── factories/
│   └── seeders/            Permission, Role, RolePermission, User, …
├── resources/views/
│   ├── assets/             create, edit, show (Asset 360), _form
│   ├── maintenances/       index, create, edit, _form
│   ├── roles/              index, create, edit, _form
│   ├── assignment-history/
│   └── components/         sidebar, logo, badges
├── public/css/theme.css    هوية MTNIMA
├── routes/web.php
└── tests/
    ├── Feature/            10 feature test files (56 tests total)
    └── Unit/               ExampleTest
```

---

## Future Enhancements

The current release is **functionally complete** for core operations (RBAC, assets, assignments, assignment history, maintenance, Asset 360, custody receipt, reports, and multilingual UI). **Asset classification continues to use the existing `type` field** — no categories table or schema changes are planned for this version.

The following items are **out of scope** for the current codebase and documented here for a later phase:

| Enhancement | Summary |
|-------------|---------|
| **Categories module** | Structured asset categories (CRUD, FK on assets, migration from `type`, report/dashboard updates). Evaluated and **deferred** to avoid additional migrations, data backfill, test/UML/doc churn while the current type-based model remains sufficient. |
| **QR codes** | Printable labels linking physical assets to Asset 360 or inventory records. |
| **Notifications** | In-app or email alerts (e.g. maintenance due, assignment changes, low stock thresholds). |
| **PDF exports** | Printable/PDF reports and custody documents beyond the current browser-print receipt. |
| **Advanced analytics** | Deeper dashboards (trends, department-level KPIs, maintenance SLA, category/type breakdowns over time). |

> **Note:** Implementing Categories would touch migrations, seeders, RBAC, asset forms, inventory filters, reports, dashboard, tests, and UML diagrams. It is intentionally reserved for a future milestone.

---

## الترخيص

MIT — انظر ملف `composer.json`.
