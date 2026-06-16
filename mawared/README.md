# نظام إدارة الموارد والمعدات العمومية

نظام جرد وتخصيص العهد والأصول — وزارة التحول الرقمي، موريتانيا.

## التشغيل السريع (Windows)

1. افتح مجلد المشروع في مستكشف الملفات:
   `C:\Users\delli7\Projects\hand-mouse\mawared`
2. انقر مرتين على **`serve.bat`**
3. افتح المتصفح: http://127.0.0.1:8000

أو من PowerShell:

```powershell
cd C:\Users\delli7\Projects\hand-mouse\mawared
php artisan serve
```

## الإعداد الأول (مرة واحدة)

```bash
cd mawared
composer install
php artisan key:generate
php artisan migrate --seed
```

(اختياري) بناء واجهة Vite: `npm install && npm run build`

## حسابات الدخول

| الدور | البريد | كلمة المرور |
|--------|--------|-------------|
| المسؤول التقني | admin@mtnima.gov.mr | password |
| أمين المخزن | storekeeper@mtnima.gov.mr | password |

## المتطلبات الوظيفية

| المتطلب | التنفيذ |
|---------|---------|
| **المصادقة** | `/login` — حسابان: مسؤول تقني + أمين مخزن |
| **إدارة العتاد** | تسجيل / تعديل / حذف: الاسم، النوع، S/N، الحالة (`مخزن` \| `في الصيانة` يدوياً) |
| **العهد** | `/assignments` — ربط أجهزة **مخزن** فقط بموظف + قسم + تاريخ |
| **محرك الحالة** | إسناد عهدة → `مخزن` → `نشط` \| سحب عهدة → حذف السجل + `مخزن` (داخل `DB::transaction`) |

> حالة **«نشط»** لا تُعيَّن يدوياً من نموذج العتاد — فقط عبر تخصيص العهدة.

## الصفحات

- `/dashboard` — إحصائيات (إجمالي، مخزن، نشط، صيانة) + جدول جرد مع بحث وتصفح
- `/assignments` — سجل العهد وModal التخصيص
- `/assets/create` — تسجيل عتاد جديد
- `/profile` — الملف الشخصي والدور

## الاختبارات

```bash
php artisan test
```

## قاعدة البيانات

- `assets`: name, type, serial_number (unique), status
- `assignments`: asset_id (FK cascade, unique), employee_name, department, assigned_date
