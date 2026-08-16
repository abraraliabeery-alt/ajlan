# Ajlan & Bros Warehouses — Laravel

منصة عجلان وإخوانه العقارية للمستودعات الصناعية، مبنية فعليًا باستخدام **Laravel 12** وBlade وEloquent.

## المميزات

- 25 قطعة صناعية و269 وحدة مستودعات، تُنشأ من خلال Seeders.
- بنية ترجمة صحيحة: جدول أساسي للعقار وجدول مستقل للترجمات.
- العربية والإنجليزية والصينية، مع دعم RTL للعربية.
- الوضع الداكن والفاتح مع حفظ اختيار الزائر.
- جميع الصور والفيديوهات والشعارات داخل المشروع.
- صفحة مستقلة لكل قطعة، وعناوين ووصف SEO مختلف لكل عقار.
- Canonical وhreflang وOpen Graph وTwitter Cards.
- Schema.org للعقارات والأسئلة الشائعة، وملفا sitemap.xml وrobots.txt.
- نموذج طلب مستودع يحفظ الطلبات في قاعدة البيانات.
- تصميم متجاوب للجوال والكمبيوتر، وجميع الأرقام معروضة بالصيغة الإنجليزية.

## المتطلبات

- PHP 8.2 أو أحدث.
- Composer 2.
- امتداد PHP الخاص بـ SQLite، أو MySQL.

## التشغيل المحلي السريع

على Windows يمكنك أيضًا النقر مرتين على `setup.bat` بعد تثبيت PHP وComposer؛ سيجهز قاعدة البيانات ويشغل المشروع تلقائيًا.

### Windows

يفضل استخدام Laravel Herd أو XAMPP مع Composer. بعد فك الضغط افتح PowerShell داخل مجلد المشروع ثم نفذ:

```bash
copy .env.example .env
composer install
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

افتح: `http://127.0.0.1:8000`

### macOS / Linux

```bash
cp .env.example .env
composer install
touch database/database.sqlite
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## قاعدة البيانات

- `properties`: البيانات الأساسية المشتركة بين اللغات.
- `property_translations`: الاسم والرابط والوصف وبيانات SEO لكل لغة.
- `property_media`: الصور والفيديوهات المرتبطة بالعقار أو المجموعة.
- `property_media_translations`: النص البديل للصور بكل لغة.
- `inquiries`: طلبات العملاء.

للتبديل إلى MySQL، عدّل بيانات `DB_*` في ملف `.env` ثم نفّذ:

```bash
php artisan migrate --seed
```

## أهم المجلدات

- `app/Models`: موديلات Eloquent.
- `app/Http/Controllers`: صفحات الموقع واستقبال الطلبات.
- `database/migrations`: هيكل الجداول.
- `database/seeders`: بيانات العقارات والوسائط.
- `resources/views`: واجهات Blade.
- `lang`: الترجمات الثلاث.
- `public/media`: جميع الصور والفيديوهات.
- `public/brand`: الشعارات الرسمية.

## قبل النشر

غيّر `APP_URL` في ملف `.env` إلى الدومين النهائي للموقع، ثم نفّذ:

```bash
php artisan optimize
```

هذا ضروري حتى تُنشأ روابط Canonical وSitemap وhreflang بالدومين الصحيح. الظهور في النتائج الأولى يعتمد أيضًا على عمر الدومين والمنافسة والروابط الخارجية والمحتوى المستمر؛ المشروع يجهز الأساس التقني القوي لكنه لا يستطيع ضمان ترتيب ثابت بمفرده.

لا يتضمن الملف مجلد `vendor` لأنه ينشأ تلقائيًا من خلال `composer install`.
