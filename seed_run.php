<?php
/**
 * Seed database with UTF-8 data via PDO
 */
require_once __DIR__ . '/config/database.php';

$pdo = getDBConnection();
$pdo->exec("SET NAMES utf8mb4");

echo "Seeding users...\n";
$pdo->exec("DELETE FROM customers");
$pdo->exec("DELETE FROM users");

$hash = '$2y$10$yhk8nOBkBLfpQOKjaqfHTe/uiuKQJ9i.Qdwm5VMU825MxlkRqxtQu';

$users = [
    [1, 'admin', $hash, 'Admin', 'admin@mhshop.vn'],
    [2, 'customer01', $hash, 'Customer', 'an.nguyen@gmail.com'],
    [3, 'customer02', $hash, 'Customer', 'bich.tran@gmail.com'],
    [4, 'customer03', $hash, 'Customer', 'dung.le@gmail.com'],
    [5, 'customer04', $hash, 'Customer', 'ha.pham@gmail.com'],
    [6, 'customer05', $hash, 'Customer', 'tuan.hoang@gmail.com'],
    [7, 'customer06', $hash, 'Customer', 'lan.vu@gmail.com'],
    [8, 'customer07', $hash, 'Customer', 'bao.dang@gmail.com'],
    [9, 'customer08', $hash, 'Customer', 'giang.bui@gmail.com'],
    [10, 'customer09', $hash, 'Customer', 'huyen.ngo@gmail.com'],
    [11, 'customer10', $hash, 'Customer', 'khoa.do@gmail.com'],
    [12, 'customer11', $hash, 'Customer', 'mai.ly@gmail.com'],
    [13, 'customer12', $hash, 'Customer', 'nam.trinh@gmail.com'],
    [14, 'customer13', $hash, 'Customer', 'linh.phan@gmail.com'],
    [15, 'customer14', $hash, 'Customer', 'minh.ho@gmail.com'],
    [16, 'customer15', $hash, 'Customer', 'ngoc.duong@gmail.com'],
    [17, 'customer16', $hash, 'Customer', 'phong.mai@gmail.com'],
    [18, 'customer17', $hash, 'Customer', 'quan.to@gmail.com'],
    [19, 'customer18', $hash, 'Customer', 'thanh.chu@gmail.com'],
    [20, 'customer19', $hash, 'Customer', 'uyen.lam@gmail.com'],
    [21, 'customer20', $hash, 'Customer', 'vinh.dinh@gmail.com'],
    [22, 'customer21', $hash, 'Customer', 'yen.cao@gmail.com'],
    [23, 'customer22', $hash, 'Customer', 'long.nguyen2@gmail.com'],
    [24, 'customer23', $hash, 'Customer', 'vy.tran@gmail.com'],
    [25, 'customer24', $hash, 'Customer', 'anh.le@gmail.com'],
    [26, 'customer25', $hash, 'Customer', 'huy.pham@gmail.com'],
];

$stmt = $pdo->prepare("INSERT INTO users (user_id, username, password_hash, role, email, status) VALUES (?, ?, ?, ?, ?, 'Active')");
foreach ($users as $u) {
    $stmt->execute($u);
}
echo "  " . count($users) . " users inserted.\n";

echo "Seeding customers...\n";
$customers = [
    [1, 2, 'Nguyễn Văn An', '0901234567', '12 Lê Lợi, Q1, TP.HCM'],
    [2, 3, 'Trần Thị Bích', '0912345678', '45 Trần Phú, Hà Đông, HN'],
    [3, 4, 'Lê Hoàng Dũng', '0923456789', '78 Nguyễn Huệ, Q1, TP.HCM'],
    [4, 5, 'Phạm Ngọc Hà', '0934567890', '23 Bà Triệu, Hoàn Kiếm, HN'],
    [5, 6, 'Hoàng Minh Tuấn', '0945678901', '56 Điện Biên Phủ, Q3, TP.HCM'],
    [6, 7, 'Vũ Thị Lan', '0956789012', '89 Kim Mã, Ba Đình, HN'],
    [7, 8, 'Đặng Quốc Bảo', '0967890123', '34 Hai Bà Trưng, Q1, TP.HCM'],
    [8, 9, 'Bùi Hương Giang', '0978901234', '67 Cầu Giấy, HN'],
    [9, 10, 'Ngô Thanh Huyền', '0989012345', '12 Nguyễn Trãi, Thanh Xuân, HN'],
    [10, 11, 'Đỗ Minh Khoa', '0990123456', '45 Lý Tự Trọng, Q1, TP.HCM'],
    [11, 12, 'Lý Thị Mai', '0911223344', '78 Phố Huế, Hai Bà Trưng, HN'],
    [12, 13, 'Trịnh Đức Nam', '0922334455', '23 Võ Văn Tần, Q3, TP.HCM'],
    [13, 14, 'Phan Thùy Linh', '0933445566', '56 Thái Hà, Đống Đa, HN'],
    [14, 15, 'Hồ Quang Minh', '0944556677', '89 Pasteur, Q1, TP.HCM'],
    [15, 16, 'Dương Thị Ngọc', '0955667788', '12 Xuân Thủy, Cầu Giấy, HN'],
    [16, 17, 'Mai Văn Phong', '0966778899', '34 Lê Duẩn, Q1, TP.HCM'],
    [17, 18, 'Tô Hải Quân', '0977889900', '67 Giải Phóng, Hoàng Mai, HN'],
    [18, 19, 'Chu Thị Thanh', '0988990011', '45 Nguyễn Văn Cừ, Q5, TP.HCM'],
    [19, 20, 'Lâm Bảo Uyên', '0999001122', '78 Láng Hạ, Đống Đa, HN'],
    [20, 21, 'Đinh Công Vinh', '0900112233', '23 Cách Mạng T8, Q3, TP.HCM'],
    [21, 22, 'Cao Thị Yến', '0911334455', '56 Trường Chinh, Thanh Xuân, HN'],
    [22, 23, 'Nguyễn Hải Long', '0922445566', '89 Nguyễn Thị Minh Khai, Q1, TP.HCM'],
    [23, 24, 'Trần Khánh Vy', '0933556677', '12 Hoàng Quốc Việt, Cầu Giấy, HN'],
    [24, 25, 'Lê Phương Anh', '0944667788', '34 Nam Kỳ Khởi Nghĩa, Q1, TP.HCM'],
    [25, 26, 'Phạm Đức Huy', '0955778899', '67 Đội Cấn, Ba Đình, HN'],
];

$stmt = $pdo->prepare("INSERT INTO customers (customer_id, user_id, customer_name, phone, address) VALUES (?, ?, ?, ?, ?)");
foreach ($customers as $c) {
    $stmt->execute($c);
}
echo "  " . count($customers) . " customers inserted.\n";

echo "Seeding categories...\n";
$pdo->exec("DELETE FROM products");
$pdo->exec("DELETE FROM categories");
$categories = [
    [1, 'Áo Thun', 'Áo thun nam nữ các loại'],
    [2, 'Áo Sơ Mi', 'Áo sơ mi công sở và casual'],
    [3, 'Quần Jeans', 'Quần jeans nam nữ các form'],
    [4, 'Đầm', 'Đầm váy nữ thanh lịch'],
    [5, 'Áo Khoác', 'Áo khoác nhẹ và ấm áp'],
    [6, 'Phụ Kiện', 'Túi xách, mũ, thắt lưng'],
];
$stmt = $pdo->prepare("INSERT INTO categories (category_id, category_name, description, status) VALUES (?, ?, ?, 'Active')");
foreach ($categories as $c) { $stmt->execute($c); }
echo "  " . count($categories) . " categories inserted.\n";

echo "Seeding suppliers...\n";
$pdo->exec("DELETE FROM suppliers");
$suppliers = [
    [1, 'Công ty TNHH Dệt May Việt Tiến', '02838123456', 'viettien@supplier.vn', 'TP. Hồ Chí Minh'],
    [2, 'Công ty CP Thời Trang Ninomaxx', '02421234567', 'ninomaxx@supplier.vn', 'Hà Nội'],
    [3, 'Nhà Cung Cấp Vải Cao Cấp Alpha', '0901234567', 'alpha@supplier.vn', 'Bình Dương'],
    [4, 'Công ty May Mặc Hà Thành', '02438765432', 'hathanh@supplier.vn', 'Hà Nội'],
    [5, 'Xưởng May Sài Gòn Fashion', '02839876543', 'sgfashion@supplier.vn', 'TP. Hồ Chí Minh'],
    [6, 'Công ty Phụ Kiện Thời Trang VN', '0912345678', 'accessory@supplier.vn', 'Đà Nẵng'],
];
$stmt = $pdo->prepare("INSERT INTO suppliers (supplier_id, supplier_name, phone, email, address, status) VALUES (?, ?, ?, ?, ?, 'Active')");
foreach ($suppliers as $s) { $stmt->execute($s); }
echo "  " . count($suppliers) . " suppliers inserted.\n";

echo "Seeding products...\n";
$products = [
    [1, 'Áo Thun Trắng Classic', 1, 1, 299000, NULL, 50, 'Áo thun trắng cổ điển 100% cotton cao cấp.'],
    [2, 'Áo Thun Oversize Basic', 1, 1, 259000, 320000, 35, 'Áo thun oversize form rộng thoải mái.'],
    [3, 'Áo Thun Polo Nam', 1, 2, 349000, NULL, 28, 'Áo polo nam cổ bẻ thanh lịch.'],
    [4, 'Áo Thun Cổ Tròn Unisex', 1, 1, 199000, NULL, 60, 'Áo thun cổ tròn basic unisex.'],
    [5, 'Áo Thun In Họa Tiết', 1, 2, 279000, NULL, 40, 'Áo thun in họa tiết graphic trendy.'],
    [6, 'Áo Hoodie Graphic', 1, 3, 499000, 650000, 25, 'Áo hoodie graphic nỉ bông dày dặn.'],
    [7, 'Áo Thun Thể Thao Dry-Fit', 1, 2, 329000, NULL, 45, 'Áo thun thể thao thấm hút nhanh.'],
    [8, 'Áo Thun Cổ V Nam', 1, 1, 269000, 340000, 38, 'Áo thun cổ V nam tính.'],
    [9, 'Áo Thun Sọc Ngang', 1, 3, 289000, NULL, 30, 'Áo thun sọc ngang Breton cổ điển.'],
    [10, 'Áo Thun Tay Dài', 1, 2, 319000, NULL, 32, 'Áo thun tay dài minimalist.'],
    [11, 'Áo Sơ Mi Linen', 2, 2, 399000, NULL, 40, 'Áo sơ mi linen cao cấp.'],
    [12, 'Áo Sơ Mi Oxford', 2, 1, 429000, NULL, 25, 'Áo sơ mi Oxford dệt vải đặc trưng.'],
    [13, 'Áo Sơ Mi Kẻ Sọc', 2, 4, 379000, 450000, 35, 'Áo sơ mi kẻ sọc sang trọng.'],
    [14, 'Áo Sơ Mi Trắng Slim Fit', 2, 1, 359000, NULL, 42, 'Áo sơ mi trắng slim fit tôn dáng.'],
    [15, 'Áo Sơ Mi Caro Flannel', 2, 4, 449000, NULL, 20, 'Áo sơ mi caro flannel cotton.'],
    [16, 'Áo Sơ Mi Denim', 2, 3, 469000, 550000, 28, 'Áo sơ mi denim wash nhẹ.'],
    [17, 'Áo Sơ Mi Hawaii', 2, 5, 339000, NULL, 33, 'Áo sơ mi ngắn tay họa tiết Hawaii.'],
    [18, 'Áo Sơ Mi Đen Công Sở', 2, 1, 389000, NULL, 30, 'Áo sơ mi đen regular fit.'],
    [19, 'Áo Sơ Mi Lụa Nữ', 2, 5, 459000, NULL, 22, 'Áo sơ mi lụa nữ satin mềm mịn.'],
    [20, 'Áo Sơ Mi Bamboo', 2, 4, 419000, 490000, 26, 'Áo sơ mi sợi bamboo tự nhiên.'],
    [21, 'Quần Jeans Slim Fit', 3, 3, 549000, NULL, 45, 'Quần jeans slim fit co giãn.'],
    [22, 'Quần Jeans Skinny Nam', 3, 3, 499000, 620000, 30, 'Quần jeans skinny ôm sát.'],
    [23, 'Quần Jeans Straight Leg', 3, 5, 579000, NULL, 35, 'Quần jeans ống đứng cổ điển.'],
    [24, 'Quần Jeans Rách Gối', 3, 3, 459000, NULL, 28, 'Quần jeans rách gối cá tính.'],
    [25, 'Quần Jeans Baggy Nữ', 3, 2, 519000, NULL, 30, 'Quần jeans baggy nữ ống rộng.'],
    [26, 'Quần Jeans Đen Basic', 3, 3, 489000, NULL, 40, 'Quần jeans đen basic.'],
    [27, 'Quần Jeans Boyfriend', 3, 2, 539000, 650000, 22, 'Quần jeans boyfriend form rộng.'],
    [28, 'Quần Jeans Jogger', 3, 5, 479000, NULL, 25, 'Quần jeans jogger bo chun gấu.'],
    [29, 'Quần Jeans Cạp Cao Nữ', 3, 2, 529000, NULL, 32, 'Quần jeans cạp cao nữ form slim.'],
    [30, 'Quần Short Jeans', 3, 3, 349000, 420000, 38, 'Quần short jeans mùa hè.'],
    [31, 'Đầm Hoa Mùa Hè', 4, 1, 459000, NULL, 20, 'Đầm hoa nhẹ nhàng tươi sáng.'],
    [32, 'Đầm Midi Xếp Ly', 4, 2, 529000, NULL, 15, 'Đầm midi xếp ly chiffon.'],
    [33, 'Đầm Suông Công Sở', 4, 4, 479000, 580000, 25, 'Đầm suông công sở.'],
    [34, 'Đầm Maxi Đi Biển', 4, 5, 399000, NULL, 18, 'Đầm maxi dài rayon mềm mại.'],
    [35, 'Đầm Bodycon Dự Tiệc', 4, 5, 559000, NULL, 14, 'Đầm bodycon co giãn tốt.'],
    [36, 'Đầm Babydoll Caro', 4, 4, 429000, NULL, 20, 'Đầm babydoll caro cotton.'],
    [37, 'Đầm Hai Dây Lụa', 4, 5, 489000, 590000, 16, 'Đầm hai dây lụa satin.'],
    [38, 'Đầm Denim Casual', 4, 3, 449000, NULL, 22, 'Đầm denim casual năng động.'],
    [39, 'Đầm Len Mùa Đông', 4, 4, 599000, NULL, 12, 'Đầm len dệt kim ấm áp.'],
    [40, 'Đầm Wrap Chấm Bi', 4, 5, 469000, NULL, 18, 'Đầm wrap đắp chéo retro.'],
    [41, 'Áo Khoác Denim', 5, 3, 799000, 950000, 20, 'Áo khoác denim vintage.'],
    [42, 'Áo Khoác Trench', 5, 1, 1290000, NULL, 10, 'Trench coat dáng dài cao cấp.'],
    [43, 'Áo Khoác Bomber', 5, 4, 699000, NULL, 18, 'Bomber nylon chống nước nhẹ.'],
    [44, 'Áo Khoác Gió Nhẹ', 5, 2, 449000, 550000, 30, 'Áo khoác gió siêu nhẹ.'],
    [45, 'Áo Khoác Blazer Nam', 5, 1, 899000, NULL, 15, 'Blazer slim fit thanh lịch.'],
    [46, 'Áo Khoác Cardigan Len', 5, 4, 549000, NULL, 20, 'Cardigan len dệt kim.'],
    [47, 'Áo Khoác Parka', 5, 1, 1190000, 1490000, 8, 'Parka dáng dài lót bông dày.'],
    [48, 'Áo Khoác Da PU', 5, 3, 849000, NULL, 12, 'Áo khoác da PU biker.'],
    [49, 'Áo Khoác Vest Nữ', 5, 5, 659000, NULL, 16, 'Vest nữ oversized tweed.'],
    [50, 'Áo Khoác Hoodie Zip', 5, 2, 429000, NULL, 25, 'Hoodie zip nỉ cotton.'],
    [51, 'Túi Đeo Chéo Da', 6, 6, 359000, NULL, 40, 'Túi đeo chéo da PU nhỏ gọn.'],
    [52, 'Ba Lô Thời Trang', 6, 6, 489000, 590000, 25, 'Ba lô Oxford chống nước.'],
    [53, 'Mũ Bucket Hat', 6, 6, 179000, NULL, 50, 'Bucket hat cotton nhẹ.'],
    [54, 'Kính Mát Tròn Retro', 6, 6, 249000, NULL, 35, 'Kính mát retro UV400.'],
    [55, 'Thắt Lưng Da Bò', 6, 6, 299000, 380000, 30, 'Thắt lưng da bò thật.'],
    [56, 'Khăn Quàng Cổ Cashmere', 6, 6, 399000, NULL, 20, 'Khăn cashmere pha mềm mịn.'],
    [57, 'Ví Da Nam Compact', 6, 6, 279000, NULL, 45, 'Ví da PU compact nhỏ gọn.'],
    [58, 'Mũ Lưỡi Trai Snapback', 6, 6, 199000, NULL, 40, 'Snapback streetwear thêu logo.'],
    [59, 'Tất/Vớ Cotton Set 5 Đôi', 6, 6, 129000, 180000, 60, 'Set 5 đôi tất cotton.'],
    [60, 'Túi Tote Canvas', 6, 6, 229000, NULL, 35, 'Tote canvas minimalist.'],
];

$stmt = $pdo->prepare("INSERT INTO products (product_id, product_name, category_id, supplier_id, unit_price, old_price, stock_quantity, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Active')");
foreach ($products as $p) { $stmt->execute($p); }
echo "  " . count($products) . " products inserted.\n";

echo "\nDone! All data seeded successfully with UTF-8.\n";
