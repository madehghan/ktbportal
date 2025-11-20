#!/bin/bash

# Script to fix storage permissions and symlink for Laravel application
# Run this script on your server after deployment

echo "🔧 در حال رفع مشکلات storage..."

# Get the project directory (adjust if needed)
PROJECT_DIR="$(pwd)"

# Create storage directories if they don't exist
echo "📁 ایجاد پوشه‌های storage..."
mkdir -p "$PROJECT_DIR/storage/app/public/avatars"
mkdir -p "$PROJECT_DIR/storage/app/public/messages"
mkdir -p "$PROJECT_DIR/storage/app/public/project_files"
mkdir -p "$PROJECT_DIR/storage/framework/cache"
mkdir -p "$PROJECT_DIR/storage/framework/sessions"
mkdir -p "$PROJECT_DIR/storage/framework/views"
mkdir -p "$PROJECT_DIR/storage/logs"

# Set proper permissions for storage directories
echo "🔐 تنظیم مجوزهای storage..."
chmod -R 775 "$PROJECT_DIR/storage"
chmod -R 775 "$PROJECT_DIR/bootstrap/cache"

# Set ownership (adjust user:group based on your server)
# For CloudPanel, usually www-data:www-data or cloudpanel:cloudpanel
# Uncomment and adjust the line below based on your server setup
# chown -R www-data:www-data "$PROJECT_DIR/storage"
# chown -R www-data:www-data "$PROJECT_DIR/bootstrap/cache"

# Create symlink if it doesn't exist
if [ ! -L "$PROJECT_DIR/public/storage" ]; then
    echo "🔗 ایجاد symlink برای public/storage..."
    if [ -e "$PROJECT_DIR/public/storage" ]; then
        echo "⚠️  فایل یا پوشه public/storage از قبل وجود دارد. در حال حذف..."
        rm -rf "$PROJECT_DIR/public/storage"
    fi
    ln -s "$PROJECT_DIR/storage/app/public" "$PROJECT_DIR/public/storage"
    echo "✅ Symlink ایجاد شد"
else
    echo "✅ Symlink از قبل وجود دارد"
fi

# Verify symlink
if [ -L "$PROJECT_DIR/public/storage" ]; then
    echo "✅ Symlink به درستی ایجاد شده است"
    ls -la "$PROJECT_DIR/public/storage"
else
    echo "❌ خطا در ایجاد symlink"
    exit 1
fi

# Check if directories are writable
echo "🔍 بررسی مجوزهای نوشتن..."
if [ -w "$PROJECT_DIR/storage/app/public/avatars" ]; then
    echo "✅ پوشه avatars قابل نوشتن است"
else
    echo "❌ پوشه avatars قابل نوشتن نیست!"
    echo "لطفاً دستور زیر را اجرا کنید:"
    echo "chmod -R 775 $PROJECT_DIR/storage/app/public/avatars"
fi

echo ""
echo "✅ تمام! حالا باید تصاویر پروفایل به درستی آپلود شوند."
echo ""
echo "اگر هنوز مشکل دارید، لطفاً مجوزهای زیر را بررسی کنید:"
echo "1. اطمینان حاصل کنید که پوشه storage قابل نوشتن است"
echo "2. اطمینان حاصل کنید که symlink public/storage به درستی ایجاد شده است"
echo "3. لاگ‌های Laravel را در storage/logs/laravel.log بررسی کنید"

