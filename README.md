# GS25 / PTITSHOP Admin

Ứng dụng quản trị PHP thuần dùng MySQL.

## Tài khoản demo

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

3. Vào phpMyAdmin hoặc công cụ quản lý database, import lần lượt các file:

```text
database.sql
seed.sql
docker/mysql/init/03-product-images.sql
```

4. Kiểm tra cấu hình database trong `config/database.php`.

Mặc định project dùng:

- Database: `web`
- User: `root`
- Password: rỗng
- Host: `localhost`

5. Mở trình duyệt:

```text
http://localhost/PHP_WEB/admin/login.php
```

Nếu bạn đổi tên thư mục project, thay `PHP_WEB` trong URL bằng đúng tên thư mục đó.

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

Thông tin database trong Docker:

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
