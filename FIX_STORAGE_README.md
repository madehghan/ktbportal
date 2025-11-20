# راهنمای رفع مشکل آپلود تصاویر پروفایل

## مشکل
تصاویر پروفایل در پوشه آپلود ذخیره نمی‌شوند و نمایش داده نمی‌شوند.

## علت مشکل
این مشکل معمولاً به دلایل زیر رخ می‌دهد:
1. پوشه `storage/app/public/avatars` وجود ندارد یا مجوز نوشتن ندارد
2. Symlink از `public/storage` به `storage/app/public` وجود ندارد
3. مجوزهای پوشه `storage` به درستی تنظیم نشده‌اند

## راه حل

### روش 1: استفاده از اسکریپت (پیشنهادی)

1. فایل `fix-storage-permissions.sh` را به سرور خود آپلود کنید
2. دستورات زیر را در ترمینال سرور اجرا کنید:

```bash
# رفتن به پوشه پروژه
cd /path/to/your/project

# قابل اجرا کردن اسکریپت
chmod +x fix-storage-permissions.sh

# اجرای اسکریپت
./fix-storage-permissions.sh
```

### روش 2: دستی

اگر نمی‌توانید از اسکریپت استفاده کنید، دستورات زیر را به ترتیب اجرا کنید:

```bash
# 1. رفتن به پوشه پروژه
cd /path/to/your/project

# 2. ایجاد پوشه avatars در صورت عدم وجود
mkdir -p storage/app/public/avatars

# 3. تنظیم مجوزهای storage
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# 4. ایجاد symlink (اگر وجود ندارد)
# ابتدا بررسی کنید که آیا symlink وجود دارد:
ls -la public/storage

# اگر symlink وجود ندارد یا خراب است:
rm -rf public/storage  # فقط اگر فایل/پوشه عادی است
ln -s ../storage/app/public public/storage

# 5. تنظیم مالکیت (متناسب با سرور خود تنظیم کنید)
# برای CloudPanel معمولاً:
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
# یا
chown -R cloudpanel:cloudpanel storage
chown -R cloudpanel:cloudpanel bootstrap/cache
```

### روش 3: استفاده از Artisan (Laravel)

```bash
# ایجاد symlink
php artisan storage:link

# اطمینان از وجود پوشه avatars
mkdir -p storage/app/public/avatars
chmod -R 775 storage/app/public/avatars
```

## بررسی

پس از اجرای دستورات، موارد زیر را بررسی کنید:

1. **بررسی وجود symlink:**
   ```bash
   ls -la public/storage
   ```
   باید چیزی شبیه این ببینید:
   ```
   lrwxrwxrwx ... storage -> ../storage/app/public
   ```

2. **بررسی مجوزهای پوشه:**
   ```bash
   ls -la storage/app/public/
   ```
   باید پوشه `avatars` را ببینید و مجوزها باید `drwxrwxr-x` یا مشابه باشند.

3. **بررسی قابلیت نوشتن:**
   ```bash
   touch storage/app/public/avatars/test.txt
   rm storage/app/public/avatars/test.txt
   ```
   اگر خطایی نداد، پوشه قابل نوشتن است.

## تست

پس از رفع مشکل:
1. وارد پنل کاربری شوید
2. به بخش پروفایل بروید
3. یک تصویر پروفایل آپلود کنید
4. بررسی کنید که تصویر نمایش داده می‌شود

## لاگ‌ها

اگر هنوز مشکل دارید، لاگ‌های Laravel را بررسی کنید:

```bash
tail -f storage/logs/laravel.log
```

سپس دوباره آپلود را امتحان کنید و خطاها را در لاگ ببینید.

## نکات مهم برای CloudPanel

1. **مالکیت فایل‌ها:** معمولاً باید `www-data` یا `cloudpanel` باشد
2. **مجوزها:** پوشه `storage` باید `775` باشد
3. **Symlink:** باید به صورت نسبی ایجاد شود: `../storage/app/public`
4. **PHP Settings:** اطمینان حاصل کنید که `upload_max_filesize` و `post_max_size` به اندازه کافی بزرگ هستند

## پشتیبانی

اگر پس از انجام این مراحل مشکل حل نشد:
1. لاگ‌های `storage/logs/laravel.log` را بررسی کنید
2. مجوزهای پوشه‌ها را دوباره بررسی کنید
3. اطمینان حاصل کنید که PHP می‌تواند در پوشه `storage` بنویسد

