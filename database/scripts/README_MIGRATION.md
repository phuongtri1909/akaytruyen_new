# Hướng dẫn Chuyển đổi Dữ liệu từ File SQL Cũ

## Tổng quan

Script này giúp bạn chuyển đổi dữ liệu từ file SQL cũ (có sẵn cấu trúc và data) sang cấu trúc mới của các migration mà không thay đổi gì cả.

### Chuyển đổi:
1. **Users table**: Chuyển dữ liệu users từ SQL cũ sang cấu trúc mới
2. **User Bans**: Chuyển các cột ban (`ban_login`, `ban_comment`, `ban_rate`, `ban_read`) sang bảng `user_bans`
3. **Ban IPs**: Chuyển từ bảng `banned_ips` sang `ban_ips`
4. **Categories**: Chuyển dữ liệu categories
5. **Stories**: Chuyển dữ liệu stories
6. **Categories Stories**: Chuyển từ `categorie_storie` sang `categories_stories`
7. **Chapters**: Chuyển dữ liệu chapters
8. **Comments**: Chuyển dữ liệu comments
9. **Comment Reactions**: Chuyển dữ liệu comment_reactions
10. **Comment Edit Histories**: Chuyển dữ liệu comment_edit_histories
11. **Donates**: Chuyển dữ liệu donates
12. **Donations**: Chuyển dữ liệu donations
13. **Live Chats**: Chuyển dữ liệu livechat

## 🚀 Cách sử dụng ĐƠN GIẢN

### Bước 1: XÓA TOÀN BỘ DATABASE (nếu cần)
```bash
php artisan migrate:fresh
```

### Bước 2: Chạy Migration mới
```bash
php artisan migrate
```

### Bước 3: Đặt file SQL cũ vào đúng vị trí
```bash
# Copy file SQL cũ vào storage/app/
cp "đường/dẫn/đến/file/akaytruyen (2).sql" storage/app/akaytruyen.sql
```

### Bước 4: Chuyển đổi dữ liệu
```bash
php artisan migrate:convert-old-sql
```

## 📋 Các bước thực hiện

1. **Copy file SQL cũ** vào `storage/app/akaytruyen.sql`
2. **Import file SQL** vào database tạm thời
3. **Chuyển đổi dữ liệu** từ cấu trúc cũ sang mới
4. **Dọn dẹp** database tạm thời
5. **Hiển thị kết quả** số lượng records đã chuyển đổi

## 🔍 Kiểm tra dữ liệu
```bash
# Kiểm tra số lượng records trong các bảng
php artisan tinker
>>> DB::table('users')->count()
>>> DB::table('user_bans')->count()
>>> DB::table('ban_ips')->count()
>>> DB::table('categories')->count()
>>> DB::table('stories')->count()
>>> DB::table('categories_stories')->count()
>>> DB::table('chapters')->count()
>>> DB::table('comments')->count()
>>> DB::table('comment_reactions')->count()
>>> DB::table('comment_edit_histories')->count()
>>> DB::table('donates')->count()
>>> DB::table('donations')->count()
>>> DB::table('live_chats')->count()
```

## 📝 Workflow thực tế

### Chuyển đổi dữ liệu:
1. **Chạy migration mới**: `php artisan migrate`
2. **Đặt file SQL cũ**: Copy vào `storage/app/akaytruyen.sql`
3. **Chuyển đổi dữ liệu**: `php artisan migrate:convert-old-sql`
4. **Deploy code mới lên server**

### Lợi ích:
- ✅ **Đơn giản**: Chỉ cần 2 lệnh
- ✅ **An toàn**: Tự động tạo database tạm thời
- ✅ **Không thay đổi**: Giữ nguyên cấu trúc migration mới
- ✅ **Chi tiết**: Hiển thị kết quả chuyển đổi

## Lưu ý quan trọng

1. **Backup dữ liệu**: Luôn backup database trước khi chạy migration
2. **Test trên môi trường dev**: Chạy thử trên môi trường development trước
3. **Kiểm tra dữ liệu**: Sau khi migration, kiểm tra kỹ dữ liệu đã được chuyển đổi đúng
4. **File SQL**: Đảm bảo file SQL cũ có đầy đủ cấu trúc và dữ liệu

## Xử lý lỗi thường gặp

### Lỗi: File không tồn tại
- Kiểm tra đường dẫn file SQL có đúng không

### Lỗi: Không thể import file SQL
- Kiểm tra quyền truy cập database
- Kiểm tra file SQL có bị lỗi không

### Lỗi: Database connection
- Kiểm tra cấu hình database trong `.env`

### Lỗi: Foreign key constraint
- Script đã được cải thiện để xử lý foreign key constraints
- Tự động kiểm tra và chỉ chấp nhận ID hợp lệ

### Lỗi: Dữ liệu không hợp lệ
- Script sẽ tự động chuyển đổi hoặc bỏ qua dữ liệu không đúng định dạng
- Xử lý các giá trị null một cách an toàn
- Chuyển đổi hoặc bỏ qua các giá trị không phải số trong cột số

## Files đã tạo

1. **`database/scripts/convert_data_simple.php`** - Script chuyển đổi dữ liệu
2. **`app/Console/Commands/ConvertOldSqlData.php`** - Artisan command
3. **`database/scripts/README_MIGRATION.md`** - Hướng dẫn này
