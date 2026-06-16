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
| **إدارة المستخدمين** | `/users` | CRUD للحسابات *(المسؤول التقني فقط)* |
| **الإعدادات** | `/settings` | إعدادات النظام |
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
| **المسؤول التقني** | Technical Administrator | ✅ نعم | `users.role = technical_admin` |
| **أمين المخزن** | Warehouse Keeper | ✅ نعم | `users.role = warehouse_keeper` |
| **مدير القسم** | Department Manager | ❌ لا | سجلات `departments` |
| **الموظف** | Employee | ❌ لا | سجلات `employees` + العهد |
| **فني الصيانة** | Maintenance Technician | ❌ لا | `maintenances.technician_name` |

> **فقط** المسؤول التقني وأمين المخزن لديهما حسابات دخول (`/login`) حالياً.  
> مدير القسم والموظف وفني الصيانة **ممثلون تشغيليون** — يُدارون عبر السجلات وسير العمل من قبل المستخدمين المسجّلين.

**مخططات UML:** [`docs/uml/actors.puml`](docs/uml/actors.puml) · [`docs/uml/use-case-overview.puml`](docs/uml/use-case-overview.puml)  
**توثيق تفصيلي:** [`docs/ACTORS.md`](docs/ACTORS.md)

---

## الأدوار والصلاحيات *(حسابات الدخول فقط)*

| الدور | القيمة في DB | الصلاحيات |
|-------|--------------|-----------|
| **المسؤول التقني** | `technical_admin` | جميع الصفحات + إدارة المستخدمين (`/users`) |
| **أمين المخزن** | `warehouse_keeper` | المخزون، التخصيص، الصيانة، السجل، التقارير، الأقسام، الموظفون، الإعدادات |

- **المصادقة:** جلسة (Session) عبر `/login`
- **Middleware:** `EnsureRole` — يقيّد `/users/*` على `technical_admin`

### حسابات تجريبية (بعد `migrate --seed`)

| الدور | البريد | كلمة المرور |
|-------|--------|-------------|
| المسؤول التقني | admin@mtnima.gov.mr | password |
| أمين المخزن | storekeeper@mtnima.gov.mr | password |

---

## ملخص مخطط قاعدة البيانات

### `users`
| العمود | النوع | ملاحظات |
|--------|-------|---------|
| id | bigint | PK |
| name | string | |
| email | string | unique |
| role | string | `technical_admin` \| `warehouse_keeper` |
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

**النتيجة الحالية:** 22 اختباراً · 75 assertion

| الملف | النوع | الغرض |
|-------|-------|--------|
| `tests/Feature/AssignmentStateTest.php` | Feature | محرك حالة العهد (تخصيص، سحب، تحقق) |
| `tests/Feature/MaintenanceStateTest.php` | Feature | محرك حالة الصيانة (فتح، إكمال، إلغاء) |
| `tests/Feature/Asset360Test.php` | Feature | صفحة Asset 360 (عرض، صلاحيات، 404) |
| `tests/Feature/ExampleTest.php` | Feature | إعادة توجيه الضيف إلى `/login` |
| `tests/Unit/ExampleTest.php` | Unit | اختبار PHPUnit أساسي |

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
│   │                       Report, User, Settings, Profile
│   ├── Http/Middleware/    EnsureRole
│   └── Models/             Asset, Assignment, AssignmentHistory, Maintenance,
│                           Department, Employee, User
├── docs/
│   ├── ACTORS.md           UML actors & login vs operational
│   └── uml/                PlantUML diagrams
├── database/
│   ├── migrations/
│   ├── factories/          Asset, Assignment, Department, Employee, Maintenance, User
│   └── seeders/
├── resources/views/
│   ├── assets/             create, edit, show (Asset 360), _form
│   ├── maintenances/       index, create, edit, _form
│   ├── assignment-history/
│   └── components/         sidebar, logo, badges
├── public/css/theme.css    هوية MTNIMA
├── routes/web.php
└── tests/
    ├── Feature/            AssignmentStateTest, MaintenanceStateTest, Asset360Test, ExampleTest
    └── Unit/               ExampleTest
```

---

## الترخيص

MIT — انظر ملف `composer.json`.
