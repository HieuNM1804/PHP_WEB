# GS25 / PTITSHOP Admin

Ứng dụng quản trị PHP thuần dùng MySQL.

## Tài khoản đăng nhập web admin

Dùng tài khoản này để đăng nhập vào trang quản trị:

- Username: `admin`
- Password: `admin123`

## Cách 1: Chạy bằng Laragon

Yêu cầu:

- Laragon
- PHP 8.x
- MySQL hoặc MariaDB

Các bước chạy:

1. Clone project vào thư mục `www` của Laragon:

```bash
git clone https://github.com/HieuNM1804/PHP_WEB.git C:\laragon\www\PHP_WEB
```

2. Mở Laragon và start Apache/Nginx + MySQL.

Mở phpMyAdmin của Laragon:

- Bấm nút **Database** trong Laragon, hoặc mở URL phpMyAdmin mà Laragon cung cấp.
- Thường đăng nhập bằng user MySQL local, ví dụ `root` và password rỗng.

3. Kiểm tra cấu hình database trong `config/database.php`.

Mặc định project dùng:

- Database: `web`
- User: `root`
- Password: rỗng
- Host: `localhost`
- Port: `3306`

Nếu MySQL của máy bạn có mật khẩu, sửa `DB_USER` và `DB_PASS` trước khi import database. Nếu MySQL chạy port khác, ví dụ `3308`, sửa `DB_PORT` trong `config/database.php`:

```php
define('DB_PORT', envValue('DB_PORT', '3308'));
```

Nên giữ `DB_NAME` là `web` nếu dùng các file SQL có sẵn.

4. Import database.

Cách nhanh nhất là chạy một lệnh trong terminal tại thư mục project:

```bash
php setup_database.php --yes
```

Lệnh này sẽ tự tạo database, tạo bảng, thêm dữ liệu mẫu và gán ảnh sản phẩm. Nếu terminal không nhận lệnh `php`, hãy mở Terminal của Laragon rồi chạy lại.

Hoặc import thủ công bằng phpMyAdmin/công cụ quản lý database theo đúng thứ tự:

```text
database.sql
seed.sql
docker/mysql/init/03-product-images.sql
```

5. Mở trình duyệt:

```text
http://localhost/PHP_WEB/admin/login.php
```

Nếu bạn đổi tên thư mục project, thay `PHP_WEB` trong URL bằng đúng tên thư mục đó.

Lưu ý: đây là URL web chạy qua Apache/Nginx của Laragon. Không mở web bằng port MySQL như `3306` hoặc `3308`.

Lưu ý khi chạy bằng Laragon:

- File `.env` chỉ dùng cho Docker. Laragon đọc cấu hình database trong `config/database.php`.
- Nếu MySQL của bạn không dùng `root`, có mật khẩu hoặc chạy port khác `3306`, sửa lại `DB_USER`, `DB_PASS`, `DB_PORT` trong `config/database.php`.
- Nếu database `web` đã tồn tại hoặc từng import lỗi tiếng Việt, hãy xóa/drop database `web` rồi import lại bằng lệnh setup hoặc import thủ công 3 file SQL theo đúng thứ tự.
- Nếu bật Virtual Host của Laragon, bạn cũng có thể mở theo domain Laragon tạo, ví dụ `http://php_web.test/admin/login.php`.
- Nếu Apache/MySQL trong Laragon không start được, thường là do trùng port `80`, `443` hoặc `3306` với phần mềm khác.

## Cách 2: Chạy bằng Docker

Yêu cầu:

- Docker Desktop hoặc Docker Engine có Docker Compose

Các bước chạy:

1. Clone project:

```bash
git clone https://github.com/HieuNM1804/PHP_WEB.git
cd PHP_WEB
```

2. Chạy Docker:

```bash
docker compose up -d --build
```

Docker sẽ tự tạo MySQL và tự import dữ liệu mẫu từ:

```text
database.sql
seed.sql
docker/mysql/init/03-product-images.sql
```

Sau khi chạy xong:

- Web admin: http://localhost:8080/admin/login.php
- phpMyAdmin: http://localhost:8081
- MySQL từ máy host: `127.0.0.1:3307`

Vào phpMyAdmin Docker tại:

```text
http://localhost:8081
```

Project đang cấu hình phpMyAdmin Docker tự kết nối database bằng user `gs25_user`, nên thường không cần nhập lại tài khoản. Nếu phpMyAdmin yêu cầu đăng nhập, dùng thông tin database Docker bên dưới.

Thông tin database Docker:

Dùng thông tin này khi mở phpMyAdmin hoặc kết nối MySQL bằng DBeaver/MySQL Workbench. Đây không phải tài khoản đăng nhập web admin.

- Database: `web`
- User: `gs25_user`
- Password: `gs25_pass`
- Root password: `root`

Nếu máy đang dùng sẵn các cổng `8080`, `8081` hoặc `3307`, sửa port trực tiếp trong file `.env`.

Ví dụ:

```env
WEB_PORT=8090
DB_PORT=3308
PHPMYADMIN_PORT=8091
```

Sau khi đổi port, chạy lại:

```bash
docker compose up -d
```

Không cần xóa database khi chỉ đổi port.

### Build lại Docker

Nếu sửa code PHP, Dockerfile hoặc cấu hình build, chạy:

```bash
docker compose up -d --build
```

Lệnh này build lại web container nhưng không xóa database.

### Xóa database và import lại từ đầu

Chỉ dùng khi muốn reset dữ liệu mẫu hoặc database đã import lỗi:

```bash
docker compose down -v
docker compose up -d --build
```

Lưu ý: `down -v` sẽ xóa dữ liệu MySQL và upload phát sinh trong Docker volume. Nếu đã thêm dữ liệu mới cần giữ, hãy backup trước khi chạy lệnh này.

Nếu dữ liệu tiếng Việt bị lỗi mã hóa sau lần chạy cũ, chạy lại hai lệnh reset ở trên để Docker import lại database bằng UTF-8.
