
1. YÊU CẦU HỆ THỐNG
- PHP 7.4 hoặc cao hơn
- MySQL 5.7 hoặc cao hơn (hoặc MariaDB 10.2+)
- Web Server: VertrigoServ/Xampp

2. Cấu hình Database
Bước 1: Sau khi đã cài VertrigoServ hay Xampp truy cập phpmyadmin tại http://localhost/phpmyadmin/
Bước 2: Chọn tab Import 
Buóc 3: Tải file script tạo database được đặt tại database/schema.sql rồi ấn thực hiện
Bước 4: Kiểm tra lại database little_bone đảm bảo gồm 16 bảng
Bước 5: Cấu hình kết nối database và API của google cloud trong file config:
   - Mở file: config/config.php
   - Cập nhật thông tin kết nối:
     * DB_HOST: localhost
     * DB_NAME: little_bone    
     * DB_USER: your_username
     * DB_PASS: your_password  
     * GOOGLE_MAPS_API_KEY: your_api_key
   - Nếu không cấu hình API_KEY, chức năng liên quan đến gg map sẽ lỗi

3. Đảm bảo thư mục dự án little_bone được đặt tại folder www của VertrigoServ hoặc htdocs của Xampp. Trong trường hợp đổi tên thư mục dự án cần cấu hình lại trong file .htaccess

4. Truy cập hệ thống bằng http://localhost/little_bone/

5. Cấu hình tìa khoản
Sử dụng tài khoản người dùng để đặt phòng:
- username: user001
- password: 66668888
Sử dụng tài khoản nhân viên khách sạn để quản lý:
- username: hotel_staff1
- password: tldtt010