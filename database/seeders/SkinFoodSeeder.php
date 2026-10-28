<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SkinFoodSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('DanhMuc')->upsert([
            ['MaDM'=>1,'TenDM'=>'Chăm sóc da'], ['MaDM'=>2,'TenDM'=>'Trang điểm'],
            ['MaDM'=>3,'TenDM'=>'Chăm sóc tóc'], ['MaDM'=>4,'TenDM'=>'Nước hoa'],
            ['MaDM'=>5,'TenDM'=>'Chăm sóc cơ thể'], ['MaDM'=>6,'TenDM'=>'Thực phẩm chức năng'],
            ['MaDM'=>7,'TenDM'=>'Dụng cụ làm đẹp'],
        ], ['MaDM'], ['TenDM']);
        DB::table('ThuongHieu')->upsert([
            ['MaTH'=>1,'TenTH'=>'Innisfree','QuocGia'=>'Hàn Quốc'], ['MaTH'=>2,'TenTH'=>"L'Oreal",'QuocGia'=>'Pháp'],
            ['MaTH'=>3,'TenTH'=>'The Ordinary','QuocGia'=>'Canada'], ['MaTH'=>4,'TenTH'=>'Cocoon','QuocGia'=>'Việt Nam'],
            ['MaTH'=>5,'TenTH'=>"Pond's",'QuocGia'=>'Mỹ'], ['MaTH'=>6,'TenTH'=>'La Roche-Posay','QuocGia'=>'Pháp'],
            ['MaTH'=>7,'TenTH'=>'Romand','QuocGia'=>'Hàn Quốc'], ['MaTH'=>8,'TenTH'=>'Black Rouge','QuocGia'=>'Hàn Quốc'],
            ['MaTH'=>9,'TenTH'=>'Emmié','QuocGia'=>'Việt Nam'],
            ['MaTH'=>10,'TenTH'=>'COSRX','QuocGia'=>'Hàn Quốc'], ['MaTH'=>11,'TenTH'=>'CeraVe','QuocGia'=>'Mỹ'],
            ['MaTH'=>12,'TenTH'=>'Bioderma','QuocGia'=>'Pháp'], ['MaTH'=>13,'TenTH'=>'Maybelline','QuocGia'=>'Mỹ'],
            ['MaTH'=>14,'TenTH'=>'Laneige','QuocGia'=>'Hàn Quốc'],
        ], ['MaTH'], ['TenTH', 'QuocGia']);
        DB::table('LoaiSP')->upsert([
            ['MaLoai'=>1,'TenLoai'=>'Serum','MaDM'=>1], ['MaLoai'=>2,'TenLoai'=>'Kem dưỡng','MaDM'=>1],
            ['MaLoai'=>3,'TenLoai'=>'Son môi','MaDM'=>2], ['MaLoai'=>4,'TenLoai'=>'Dầu gội','MaDM'=>3],
            ['MaLoai'=>5,'TenLoai'=>'Sữa rửa mặt','MaDM'=>1], ['MaLoai'=>6,'TenLoai'=>'Kem chống nắng','MaDM'=>1],
            ['MaLoai'=>7,'TenLoai'=>'Nước hoa nam','MaDM'=>4], ['MaLoai'=>8,'TenLoai'=>'Nước hoa nữ','MaDM'=>4],
            ['MaLoai'=>9,'TenLoai'=>'Sữa tắm','MaDM'=>5], ['MaLoai'=>10,'TenLoai'=>'Dưỡng thể','MaDM'=>5],
            ['MaLoai'=>11,'TenLoai'=>'Viên uống','MaDM'=>6], ['MaLoai'=>12,'TenLoai'=>'Máy rửa mặt','MaDM'=>7],
            ['MaLoai'=>13,'TenLoai'=>'Nước cân bằng Toner','MaDM'=>1], ['MaLoai'=>14,'TenLoai'=>'Mặt nạ dưỡng da','MaDM'=>1],
            ['MaLoai'=>15,'TenLoai'=>'Tẩy trang','MaDM'=>1], ['MaLoai'=>16,'TenLoai'=>'Kem mắt','MaDM'=>1],
            ['MaLoai'=>17,'TenLoai'=>'Tẩy tế bào chết da mặt','MaDM'=>1], ['MaLoai'=>18,'TenLoai'=>'Son dưỡng','MaDM'=>2],
            ['MaLoai'=>19,'TenLoai'=>'Miếng dán mụn','MaDM'=>1], ['MaLoai'=>20,'TenLoai'=>'Kem nền và Cushion','MaDM'=>2],
            ['MaLoai'=>21,'TenLoai'=>'Che khuyết điểm','MaDM'=>2], ['MaLoai'=>22,'TenLoai'=>'Phấn phủ','MaDM'=>2],
            ['MaLoai'=>23,'TenLoai'=>'Má hồng','MaDM'=>2], ['MaLoai'=>24,'TenLoai'=>'Mascara','MaDM'=>2],
            ['MaLoai'=>25,'TenLoai'=>'Chì kẻ mày','MaDM'=>2], ['MaLoai'=>26,'TenLoai'=>'Tẩy tế bào chết da đầu','MaDM'=>3],
            ['MaLoai'=>27,'TenLoai'=>'Mặt nạ tóc','MaDM'=>3], ['MaLoai'=>28,'TenLoai'=>'Xịt dưỡng tóc','MaDM'=>3],
            ['MaLoai'=>29,'TenLoai'=>'Dầu dưỡng tóc','MaDM'=>3], ['MaLoai'=>30,'TenLoai'=>'Kem dưỡng tay chân','MaDM'=>5],
            ['MaLoai'=>31,'TenLoai'=>'Khử mùi cơ thể','MaDM'=>5], ['MaLoai'=>32,'TenLoai'=>'Kẹp mi','MaDM'=>7],
        ], ['MaLoai'], ['TenLoai', 'MaDM']);

        DB::table('NguoiDung')->upsert([
            ['MaND'=>1,'HoTen'=>'Quản trị SkinFood','SoDienThoai'=>'0909000999','DiaChi'=>'TP.HCM','GioiTinh'=>'Nam','Avatar'=>'1.jpg','NgayTao'=>now()],
            ['MaND'=>2,'HoTen'=>'Khách hàng Demo','SoDienThoai'=>'0911222333','DiaChi'=>'140 Lê Trọng Tấn, TP.HCM','GioiTinh'=>'Nữ','Avatar'=>'2.jpg','NgayTao'=>now()],
        ], ['MaND'], ['HoTen', 'SoDienThoai', 'DiaChi', 'GioiTinh', 'Avatar', 'NgayTao']);
        DB::table('TaiKhoan')->updateOrInsert(
            ['TenDangNhap'=>'admin@skinfood.com'],
            ['MatKhauHash'=>Hash::make('123456'),'VaiTro'=>'Admin','TrangThai'=>'active','MaND'=>1]
        );
        DB::table('TaiKhoan')->updateOrInsert(
            ['TenDangNhap'=>'user@skinfood.com'],
            ['MatKhauHash'=>Hash::make('123456'),'VaiTro'=>'User','TrangThai'=>'active','MaND'=>2]
        );
        DB::table('NhaCungCap')->upsert([
            ['MaNCC'=>1,'TenNCC'=>'Tổng kho Mỹ Phẩm Chính Hãng','DiaChi'=>'Quận 7, TP.HCM','SoDienThoai'=>'0909123123','Email'=>'kho@skinfood.vn','NguoiLienHe'=>'Nguyễn An'],
            ['MaNCC'=>2,'TenNCC'=>'K-Beauty Việt Nam','DiaChi'=>'Quận 1, TP.HCM','SoDienThoai'=>'0909888777','Email'=>'sales@kbeauty.vn','NguoiLienHe'=>'Trần Minh'],
        ], ['MaNCC'], ['TenNCC', 'DiaChi', 'SoDienThoai', 'Email', 'NguoiLienHe']);

        $products = [
            ['Dầu gội Pond’s Phục hồi hư tổn',185000,3,5,4,'dau_goi/1.jpg','Phục hồi cấu trúc tóc hư tổn từ sâu bên trong.',0],
            ['Dầu gội Gừng Thảo mộc Ngăn rụng tóc',250000,3,4,4,'dau_goi/2.jpg','Chiết xuất gừng tươi giúp làm ấm da đầu.',10],
            ['Dầu gội Dược liệu cổ truyền Dáng Diễm',160000,3,4,4,'dau_goi/3.jpg','Kết hợp bồ kết, hương nhu và cỏ mần trầu.',0],
            ['Bộ đôi Gội Xả Thảo mộc Wings Up',320000,3,4,4,'dau_goi/4.jpg','Chăm sóc tóc toàn diện.',5],
            ['Dầu gội nước hoa I.D Salon Grade',450000,3,5,4,'dau_goi/5.jpg','Hương nước hoa sang trọng.',0],
            ['Dầu gội phủ bạc thảo dược',290000,3,4,4,'dau_goi/6.jpg','Phủ bạc an toàn từ thảo dược.',0],
            ['Dầu gội cốt Dừa tươi',140000,3,5,4,'dau_goi/7.jpg','Tinh dầu dừa cấp ẩm cho tóc.',12],
            ['Dầu gội xả 2 trong 1 Herbal Care',150000,3,4,4,'dau_goi/8.jpg','Làm sạch và dưỡng xả trong một bước.',0],
            ['Dầu gội Bưởi Cocoon',195000,3,4,4,'dau_goi/9.jpg','Tinh dầu vỏ bưởi giúp giảm gãy rụng.',8],
            ['Dầu gội Kelina Bạch quả & Trà xanh',280000,3,4,4,'dau_goi/10.jpg','Chống oxy hóa và bảo vệ tóc.',0],
            ['Kem chống nắng phục hồi Emmié',320000,1,9,6,'Kem/1.jpg','Bảo vệ da toàn diện với Vitamin B5.',15],
            ['Tinh chất Vitamin C tươi dưỡng trắng',280000,1,3,1,'Kem/2.jpg','Làm mờ thâm và đều màu da.',0],
            ['Kem dưỡng ẩm 5X Ceramide',350000,1,3,2,'Kem/3.jpg','Phục hồi hàng rào bảo vệ da.',10],
            ['Gel dưỡng ẩm Pond’s Hydra Active',195000,1,5,2,'Kem/4.jpg','Cấp nước và giữ ẩm dài lâu.',0],
            ['Kem dưỡng ẩm Cetaphil',285000,1,6,2,'Kem/5.jpg','Dành cho da khô và da nhạy cảm.',0],
            ['Serum La Roche-Posay Hyalu B5',980000,1,6,1,'Kem/6.jpg','Cấp ẩm tầng sâu, phục hồi da.',20],
            ['Serum Balance Vitamin C',160000,1,3,1,'Kem/7.jpg','Dưỡng sáng da hiệu quả.',0],
            ['Son Kem Lì Lilybyred Mood Liar',180000,2,7,3,'Son_Moi/1.jpg','Chất son velvet mềm mịn.',0],
            ['Son Romand Milk Tea Velvet Tint',190000,2,7,3,'Son_Moi/2.jpg','Tông màu trà sữa ấm áp.',10],
            ['Son Romand Zero Velvet Tint',175000,2,7,3,'Son_Moi/3.jpg','Chất son nhẹ tênh.',0],
            ['Son Peripera Ink The Velvet',150000,2,7,3,'Son_Moi/4.jpg','Bám màu lâu và mềm môi.',5],
            ['Son Bóng Black Rouge Half N Half',210000,2,8,3,'Son_Moi/5.jpg','Hiệu ứng căng mọng tráng gương.',0],
            ['Sữa rửa mặt trà xanh',135000,1,1,5,'srm_traxanh.jpg','Làm sạch dịu nhẹ cho da.',0],
            ['Kem chống nắng dưỡng ẩm',265000,1,6,6,'kem_chong_nang.jpg','Chống nắng phổ rộng.',10],
            ['Sữa tắm dưỡng ẩm',180000,5,2,9,'sua_tam.jpg','Làm sạch và dưỡng ẩm cơ thể.',0],
        ];
        foreach ($products as $index => $p) {
            DB::table('SanPham')->updateOrInsert(
                ['MaSP'=>$index+1],
                ['TenSP'=>$p[0],'GiaBan'=>$p[1],'MaDM'=>$p[2],'MaTH'=>$p[3],'MaLoai'=>$p[4],'HinhAnh'=>$p[5],'MoTa'=>$p[6],'GiamGia'=>$p[7],'SoLuongTon'=>50,'TrangThaiSanPham'=>'Còn hàng','NgayNhapKho'=>now()]
            );
        }

        $newProducts = [
            [26,'Toner trà xanh Innisfree Balancing 200ml',289000,1,1,13,'products/new/skincare-set.jpg',8,'Mọi loại da','Da thiếu ẩm, dầu thừa','Không áp dụng','Trà xanh Beauty Green Tea, betaine, panthenol',7,4.8,'200 ml','<h4>Nước cân bằng dịu nhẹ cho da hằng ngày</h4><p>Công thức trà xanh giúp làm sạch lượng cặn còn sót lại sau bước rửa mặt, cân bằng độ ẩm và chuẩn bị da hấp thu serum tốt hơn.</p><ul><li>Kết cấu lỏng, thấm nhanh và không gây nhờn.</li><li>Phù hợp da thường, da hỗn hợp và da dầu thiếu nước.</li><li>Cách dùng: thấm ra bông hoặc vỗ trực tiếp sau khi rửa mặt, sáng và tối.</li></ul>'],
            [27,'Toner Glycolic 7% The Ordinary',365000,1,3,13,'products/new/skincare-white.jpg',10,'Da thường, da dầu','Bề mặt sần, xỉn màu','Không áp dụng','Glycolic Acid 7%, nha đam, nhân sâm',3,4.7,'240 ml','<h4>Toner tẩy tế bào chết hóa học</h4><p>Glycolic Acid 7% hỗ trợ làm mịn bề mặt, cải thiện vẻ xỉn màu và hạn chế bít tắc. Công thức có thành phần làm dịu để giảm cảm giác khô căng.</p><ul><li>Dùng buổi tối, 2 đến 3 lần mỗi tuần.</li><li>Không dùng cùng buổi với retinol hoặc acid mạnh khác.</li><li>Ban ngày cần sử dụng kem chống nắng.</li></ul>'],
            [28,'Mặt nạ đất sét thanh lọc lỗ chân lông',245000,1,4,14,'products/new/body-scrub.jpg',5,'Da dầu, da hỗn hợp','Dầu thừa, lỗ chân lông','Không áp dụng','Đất sét kaolin, tràm trà, allantoin',2,4.6,'100 g','<h4>Mặt nạ làm sạch sâu vùng da nhiều dầu</h4><p>Đất sét kaolin hấp thụ bã nhờn và hỗ trợ làm sạch lỗ chân lông trong khi allantoin giúp da dễ chịu sau khi rửa.</p><ul><li>Thoa lớp mỏng trên da sạch và tránh vùng mắt.</li><li>Để 8 đến 10 phút, không chờ mặt nạ khô nứt hoàn toàn.</li><li>Dùng 1 đến 2 lần mỗi tuần.</li></ul>'],
            [29,'Mặt nạ giấy Ceramide phục hồi da',45000,1,6,14,'products/new/skincare-white.jpg',0,'Da khô, da nhạy cảm','Khô căng, hàng rào da yếu','Không áp dụng','Ceramide NP, panthenol, beta-glucan',3,4.8,'25 ml','<h4>Mặt nạ cấp ẩm và phục hồi nhanh</h4><p>Tinh chất ceramide và panthenol cấp ẩm, hỗ trợ hàng rào bảo vệ da và giảm cảm giác khô căng sau khi tiếp xúc điều hòa hoặc nắng nóng.</p><ul><li>Đắp 15 phút trên da sạch.</li><li>Vỗ phần tinh chất còn lại đến khi thấm.</li><li>Không cần rửa lại với nước.</li></ul>'],
            [30,'Serum Niacinamide 10% kiểm soát dầu',325000,1,3,1,'products/new/skincare-set.jpg',12,'Da dầu, da hỗn hợp','Dầu thừa, lỗ chân lông','Không áp dụng','Niacinamide 10%, Zinc PCA 1%',7,4.9,'30 ml','<h4>Tinh chất cân bằng dầu và làm đều màu da</h4><p>Niacinamide nồng độ 10% hỗ trợ điều tiết dầu, củng cố hàng rào bảo vệ và cải thiện bề mặt da không đều màu.</p><ul><li>Dùng 2 đến 3 giọt sau toner.</li><li>Có thể sử dụng sáng và tối.</li><li>Phù hợp chu trình chăm sóc da tối giản.</li></ul>'],
            [31,'Serum Retinol 0.5% tái tạo da ban đêm',420000,1,3,1,'products/new/cream-perfume.jpg',0,'Da thường, da lão hóa','Nếp nhăn, bề mặt kém mịn','Không áp dụng','Retinol 0.5%, squalane, vitamin E',3,4.7,'30 ml','<h4>Serum chống lão hóa dùng vào buổi tối</h4><p>Retinol 0.5% hỗ trợ chu kỳ tái tạo, làm mịn bề mặt và cải thiện dấu hiệu lão hóa sớm. Nền squalane giảm cảm giác khô.</p><ul><li>Bắt đầu 2 lần mỗi tuần rồi tăng dần.</li><li>Không dùng cho phụ nữ đang mang thai.</li><li>Luôn chống nắng kỹ vào ban ngày.</li></ul>'],
            [32,'Kem dưỡng Hyaluronic phục hồi 72 giờ',389000,1,6,2,'products/new/skincare-white.jpg',15,'Da khô, da nhạy cảm','Thiếu nước, bong tróc','Không áp dụng','Hyaluronic Acid đa tầng, ceramide, glycerin',14,4.9,'50 ml','<h4>Kem dưỡng khóa ẩm không bí da</h4><p>Hyaluronic Acid đa tầng kết hợp ceramide cấp nước tức thì và hạn chế thất thoát độ ẩm, mang lại bề mặt mềm mịn.</p><ul><li>Dùng ở bước cuối chu trình sáng và tối.</li><li>Không chứa hương liệu nồng.</li><li>Phù hợp cả khi da đang nhạy cảm.</li></ul>'],
            [33,'Kem chống nắng Sensitive SPF50 PA++++',335000,1,6,6,'products/new/skincare-set.jpg',10,'Da nhạy cảm','Tác hại tia UV','Không áp dụng','Màng lọc UV phổ rộng, panthenol, vitamin E',7,4.8,'50 ml','<h4>Chống nắng phổ rộng dành cho da nhạy cảm</h4><p>Sản phẩm bảo vệ da trước UVA và UVB, có kết cấu kem mỏng, hạn chế vệt trắng và không gây nặng mặt.</p><ul><li>Thoa lượng đủ trước khi ra nắng 15 phút.</li><li>Thoa lại sau 2 đến 3 giờ khi hoạt động ngoài trời.</li><li>Có thể dùng dưới lớp trang điểm.</li></ul>'],
            [34,'Sữa dưỡng thể không hương liệu 200ml',310000,5,6,10,'products/new/body-lotion.jpg',0,'Mọi loại da','Khô ráp cơ thể','Không áp dụng','Glycerin, niacinamide, dầu hạnh nhân',7,4.8,'200 ml','<h4>Dưỡng thể dịu nhẹ cho làn da khô</h4><p>Kết cấu sữa mềm mượt bổ sung độ ẩm mà không để lại màng dính. Công thức không hương liệu phù hợp da nhạy cảm.</p><ul><li>Thoa sau khi tắm khi da còn hơi ẩm.</li><li>Tập trung ở khuỷu tay, đầu gối và vùng khô.</li><li>Dùng hằng ngày.</li></ul>'],
            [35,'Tẩy tế bào chết cơ thể đường mịn',265000,5,4,10,'products/new/body-scrub.jpg',8,'Mọi loại da','Da cơ thể sần ráp','Không áp dụng','Đường mịn, dầu dừa, bơ hạt mỡ',2,4.6,'250 g','<h4>Tẩy da chết vật lý với hạt đường mịn</h4><p>Hạt đường hòa tan giúp làm sạch lớp sừng già, dầu dừa và bơ hạt mỡ giữ da mềm sau khi tắm.</p><ul><li>Massage nhẹ trên da ướt từ 2 đến 3 phút.</li><li>Không dùng trên vùng da trầy xước.</li><li>Sử dụng 1 đến 2 lần mỗi tuần.</li></ul>'],
            [36,'Nước hoa nữ Floral Peony EDP',690000,4,2,8,'products/new/perfume-floral.jpg',12,'Không áp dụng','Hương thơm cá nhân','Hồng hoa mẫu đơn','Hoa mẫu đơn, cam bergamot, xạ hương',7,4.7,'50 ml','<h4>Hương hoa thanh lịch cho ban ngày</h4><p>Mở đầu tươi sáng với cam bergamot, chuyển sang hoa mẫu đơn mềm mại và kết thúc bằng xạ hương sạch.</p><ul><li>Nồng độ Eau de Parfum.</li><li>Độ lưu hương tham khảo 5 đến 7 giờ.</li><li>Phù hợp đi làm và gặp gỡ ban ngày.</li></ul>'],
            [37,'Nước hoa nữ Amber Rose EDP',820000,4,2,8,'products/new/perfume-gold.jpg',5,'Không áp dụng','Hương thơm cá nhân','Hổ phách ấm','Hoa hồng, vani, hổ phách',7,4.8,'50 ml','<h4>Hương hoa hồng và hổ phách ấm áp</h4><p>Sự kết hợp giữa hoa hồng, vani và hổ phách tạo cảm giác sang trọng, phù hợp buổi tối hoặc tiết trời mát.</p><ul><li>Nồng độ Eau de Parfum.</li><li>Xịt lên cổ tay và vùng sau tai.</li><li>Không chà xát sau khi xịt.</li></ul>'],
            [38,'Nước hoa nam Golden Wood EDP',890000,4,2,7,'products/new/perfume-black.jpg',10,'Không áp dụng','Hương thơm cá nhân','Gỗ và hổ phách','Gỗ tuyết tùng, tiêu đen, hổ phách',7,4.9,'75 ml','<h4>Hương gỗ mạnh mẽ dành cho nam</h4><p>Tiêu đen mở đầu sắc nét, lớp hương giữa là gỗ tuyết tùng và nền hổ phách mang lại cảm giác ấm, lịch lãm.</p><ul><li>Nồng độ Eau de Parfum.</li><li>Thích hợp buổi tối và sự kiện.</li><li>Lưu hương tham khảo 6 đến 8 giờ.</li></ul>'],
            [39,'Nước hoa Unisex Rain Fresh',750000,4,1,8,'products/new/perfume-fresh.jpg',0,'Không áp dụng','Hương thơm cá nhân','Tươi mát thủy sinh','Cam chanh, hương nước, xạ hương trắng',7,4.6,'50 ml','<h4>Hương thơm tươi mát dùng được cho mọi giới</h4><p>Cam chanh và hương nước tạo cảm giác trong trẻo, lớp xạ hương trắng nhẹ nhàng phù hợp sử dụng hằng ngày.</p><ul><li>Nồng độ Eau de Toilette.</li><li>Phù hợp thời tiết nóng ẩm.</li><li>Có thể xịt lại sau 4 đến 5 giờ.</li></ul>'],
            [40,'Bộ cọ trang điểm chuyên nghiệp 12 cây',420000,7,9,12,'products/new/makeup-brushes.jpg',15,'Không áp dụng','Trang điểm nền và mắt','Không áp dụng','Sợi cọ tổng hợp mềm, cán nhôm',7,4.8,'12 cây','<h4>Bộ cọ đầy đủ cho trang điểm cá nhân</h4><p>Bộ 12 cọ gồm cọ nền, phủ phấn, má hồng, tạo khối và nhóm cọ mắt. Sợi tổng hợp mềm, ít hút sản phẩm.</p><ul><li>Vệ sinh cọ mỗi tuần bằng dung dịch dịu nhẹ.</li><li>Phơi ngang tại nơi thoáng mát.</li><li>Kèm túi đựng tiện lợi.</li></ul>'],
            [41,'Máy rửa mặt silicone Sonic Mini',590000,7,9,12,'2.jpg',10,'Mọi loại da','Làm sạch sâu','Không áp dụng','Silicone kháng khuẩn, pin sạc USB',7,4.7,'Máy mini','<h4>Thiết bị làm sạch da bằng rung sonic</h4><p>Bề mặt silicone mềm kết hợp nhiều mức rung hỗ trợ làm sạch bụi bẩn và kem chống nắng tốt hơn thao tác bằng tay.</p><ul><li>Chống nước chuẩn sinh hoạt hằng ngày.</li><li>Dùng tối đa 60 giây mỗi lần.</li><li>Rửa sạch và để khô sau khi sử dụng.</li></ul>'],
            [42,'Viên uống Collagen Peptide và Vitamin C',545000,6,2,11,'3.jpg',0,'Không áp dụng','Hỗ trợ da và tóc','Không áp dụng','Collagen peptide, vitamin C, biotin',7,4.6,'60 viên','<h4>Thực phẩm bổ sung collagen peptide</h4><p>Công thức collagen peptide kết hợp vitamin C và biotin hỗ trợ duy trì độ đàn hồi của da, tóc và móng trong chế độ ăn cân bằng.</p><ul><li>Dùng 2 viên mỗi ngày sau bữa ăn.</li><li>Không thay thế chế độ dinh dưỡng đa dạng.</li><li>Người đang điều trị bệnh nên hỏi ý kiến chuyên môn.</li></ul>'],
            [43,'Mặt nạ ngủ môi Berry Repair',189000,2,7,3,'son_duong_moi.jpg',5,'Mọi loại da','Môi khô, bong tróc','Đỏ berry','Bơ hạt mỡ, dầu jojoba, vitamin E',7,4.8,'20 g','<h4>Mặt nạ môi dưỡng ẩm qua đêm</h4><p>Kết cấu balm tạo lớp khóa ẩm, làm mềm vảy khô và giúp môi mịn hơn vào buổi sáng.</p><ul><li>Thoa lớp mỏng trước khi ngủ.</li><li>Có thể dùng lượng ít như son dưỡng ban ngày.</li><li>Hương berry nhẹ.</li></ul>'],
            [44,'Tinh dầu bưởi dưỡng tóc giảm gãy rụng',175000,3,4,4,'tinh_dau_buoi.jpg',10,'Da đầu thường, da đầu dầu','Tóc yếu, dễ gãy','Không áp dụng','Tinh dầu vỏ bưởi, vitamin B5, caffeine',7,4.7,'100 ml','<h4>Xịt dưỡng chân tóc từ tinh dầu bưởi</h4><p>Dung dịch nhẹ hỗ trợ nuôi dưỡng chân tóc, giảm cảm giác khô da đầu và cải thiện tình trạng tóc yếu do tạo kiểu.</p><ul><li>Xịt vào chân tóc sạch rồi massage nhẹ.</li><li>Không cần xả lại.</li><li>Dùng một đến hai lần mỗi ngày.</li></ul>'],
            [45,'Dầu xả Keratin phục hồi tóc hư tổn',225000,3,2,4,'dau_goi/8.jpg',8,'Tóc khô, tóc nhuộm','Khô xơ, chẻ ngọn','Không áp dụng','Keratin thủy phân, dầu argan, panthenol',7,4.8,'300 ml','<h4>Dầu xả làm mềm tóc sau khi gội</h4><p>Keratin thủy phân và dầu argan giúp bề mặt tóc mượt hơn, hạn chế rối và giảm khô xơ do nhiệt hoặc nhuộm.</p><ul><li>Thoa từ thân đến ngọn tóc.</li><li>Ủ 2 đến 3 phút rồi xả sạch.</li><li>Dùng sau mỗi lần gội.</li></ul>'],
        ];
        foreach ($newProducts as $p) {
            DB::table('SanPham')->updateOrInsert(['MaSP'=>$p[0]], [
                'TenSP'=>$p[1], 'GiaBan'=>$p[2], 'MaDM'=>$p[3], 'MaTH'=>$p[4], 'MaLoai'=>$p[5],
                'HinhAnh'=>$p[6], 'GiamGia'=>$p[7], 'LoaiDa'=>$p[8], 'VanDeChiRi'=>$p[9],
                'TonDaMau'=>$p[10], 'ThanhPhanChiNhYeu'=>$p[11], 'SoLanSuDungMoiTuan'=>$p[12],
                'DoTinCay'=>$p[13], 'KichCoTieuChuan'=>$p[14], 'MoTa'=>$p[15], 'SoLuongTon'=>60,
                'TrangThaiSanPham'=>'Còn hàng', 'NgayNhapKho'=>now(),
            ]);
        }

        $catalogProducts = [
            [46,'Dầu tẩy trang Squalane dịu nhẹ 200ml',345000,1,3,15,'products/catalog-2026/09.png',5,'Mọi loại da','Lớp trang điểm, kem chống nắng','Không áp dụng','Squalane, dầu hướng dương, vitamin E',7,4.8,'200 ml','<h4>Làm sạch lớp trang điểm mà không khô căng</h4><p>Kết cấu dầu nhẹ hòa tan kem chống nắng, bã nhờn và lớp nền lâu trôi, nhũ hóa nhanh khi gặp nước.</p><ul><li>Massage trên mặt khô 60 giây.</li><li>Thêm nước để nhũ hóa rồi rửa sạch.</li><li>Dùng mỗi tối trước sữa rửa mặt.</li></ul>'],
            [47,'Nước tẩy trang Micellar da nhạy cảm 400ml',395000,1,12,15,'products/catalog-2026/04.jpg',8,'Da nhạy cảm, da thường','Bụi bẩn, trang điểm nhẹ','Không áp dụng','Micelle, glycerin, chiết xuất dưa leo',7,4.8,'400 ml','<h4>Nước làm sạch không cần chà xát mạnh</h4><p>Các hạt micelle cuốn trôi bụi bẩn và lớp trang điểm hằng ngày, đồng thời glycerin giúp hạn chế khô da.</p><ul><li>Thấm ướt bông và lau nhẹ theo chiều da.</li><li>Lặp lại đến khi bông sạch.</li><li>Nên rửa lại nếu da dễ bí tắc.</li></ul>'],
            [48,'Sữa rửa mặt Salicylic Acid 2% cho da dầu',275000,1,10,5,'products/catalog-2026/07.jpg',10,'Da dầu, da hỗn hợp','Bít tắc, mụn đầu đen','Không áp dụng','Salicylic Acid 2%, betaine, allantoin',7,4.7,'150 ml','<h4>Gel làm sạch hỗ trợ thông thoáng lỗ chân lông</h4><p>Salicylic Acid hỗ trợ loại bỏ dầu thừa trong lỗ chân lông trong khi betaine duy trì cảm giác mềm da.</p><ul><li>Dùng một lần mỗi ngày trong tuần đầu.</li><li>Massage 30 giây và rửa kỹ.</li><li>Giảm tần suất nếu da khô căng.</li></ul>'],
            [49,'Serum Vitamin C 15% dưỡng sáng 30ml',459000,1,2,1,'products/catalog-2026/07.jpg',12,'Da thường, da xỉn màu','Thâm sau mụn, màu da không đều','Không áp dụng','Vitamin C 15%, ferulic acid, vitamin E',7,4.9,'30 ml','<h4>Tinh chất chống oxy hóa dùng buổi sáng</h4><p>Vitamin C kết hợp ferulic acid hỗ trợ làm sáng, cải thiện thâm sau mụn và bảo vệ da trước tác nhân môi trường.</p><ul><li>Dùng 2 đến 3 giọt sau toner.</li><li>Bảo quản nơi mát, tránh ánh sáng.</li><li>Luôn thoa kem chống nắng sau đó.</li></ul>'],
            [50,'Serum Peptide phục hồi đàn hồi 30ml',525000,1,3,1,'products/catalog-2026/09.png',5,'Da khô, da lão hóa','Kém đàn hồi, nếp nhăn nhỏ','Không áp dụng','Multi-peptide, amino acid, hyaluronic acid',14,4.9,'30 ml','<h4>Tinh chất peptide cấp ẩm và làm mịn</h4><p>Phức hợp peptide và amino acid hỗ trợ bề mặt da săn chắc, trong khi hyaluronic acid bổ sung độ ẩm.</p><ul><li>Dùng sáng hoặc tối sau toner.</li><li>Có thể kết hợp niacinamide.</li><li>Khóa ẩm bằng kem dưỡng.</li></ul>'],
            [51,'Kem mắt Caffeine giảm bọng 20ml',329000,1,3,16,'products/catalog-2026/09.png',0,'Mọi loại da','Bọng mắt, vùng mắt mệt mỏi','Không áp dụng','Caffeine 3%, peptide, panthenol',14,4.6,'20 ml','<h4>Kem gel nhẹ cho vùng da quanh mắt</h4><p>Caffeine hỗ trợ cải thiện vẻ mệt mỏi tạm thời, peptide và panthenol giữ vùng mắt mềm ẩm.</p><ul><li>Dùng lượng bằng hạt gạo cho mỗi bên.</li><li>Vỗ nhẹ bằng ngón áp út.</li><li>Tránh bôi sát viền mắt.</li></ul>'],
            [52,'Kem dưỡng Cica Panthenol phục hồi da 50ml',415000,1,6,2,'products/catalog-2026/09.png',15,'Da nhạy cảm, da khô','Đỏ rát, hàng rào da yếu','Không áp dụng','Centella asiatica, panthenol 5%, ceramide',14,4.9,'50 ml','<h4>Kem dưỡng làm dịu và củng cố hàng rào da</h4><p>Cica, panthenol và ceramide hỗ trợ giảm cảm giác khô rát, phục hồi độ ẩm sau treatment hoặc thời tiết hanh khô.</p><ul><li>Dùng ở bước cuối sáng và tối.</li><li>Thoa dày hơn tại vùng bong tróc.</li><li>Không chứa hạt tẩy da chết.</li></ul>'],
            [53,'Kem chống nắng Tone Up SPF50+ PA++++',379000,1,1,6,'products/catalog-2026/09.png',10,'Da thường, da hỗn hợp','Tia UV, da xỉn màu','Hồng tự nhiên','Màng lọc UV phổ rộng, niacinamide, vitamin E',7,4.7,'50 ml','<h4>Chống nắng nâng tông nhẹ cho da</h4><p>Công thức bảo vệ phổ rộng kết hợp hiệu ứng hồng trong trẻo, phù hợp dùng hằng ngày hoặc làm lớp lót trang điểm.</p><ul><li>Thoa đủ lượng trước nắng 15 phút.</li><li>Tán từng lớp mỏng để đều màu.</li><li>Thoa lại sau 2 đến 3 giờ ngoài trời.</li></ul>'],
            [54,'Tinh chất BHA 2% làm sạch lỗ chân lông',389000,1,10,17,'products/catalog-2026/07.jpg',5,'Da dầu, da hỗn hợp','Mụn đầu đen, bề mặt sần','Không áp dụng','Salicylic Acid 2%, trà xanh, allantoin',3,4.8,'30 ml','<h4>Tẩy tế bào chết hóa học không rửa lại</h4><p>BHA tan trong dầu hỗ trợ làm sạch bít tắc và cải thiện bề mặt da sần khi dùng đều đặn.</p><ul><li>Dùng buổi tối 2 đến 3 lần mỗi tuần.</li><li>Không dùng cùng tối với retinol.</li><li>Ban ngày chống nắng kỹ.</li></ul>'],
            [55,'Mặt nạ ngủ Hyaluronic cấp nước 60ml',435000,1,14,14,'products/catalog-2026/09.png',8,'Da khô, da thiếu nước','Khô căng, xỉn màu','Không áp dụng','Hyaluronic acid, beta-glucan, squalane',3,4.8,'60 ml','<h4>Mặt nạ khóa ẩm qua đêm</h4><p>Kết cấu gel mát bổ sung độ ẩm và hạn chế mất nước trong lúc ngủ, giúp da mềm hơn vào sáng hôm sau.</p><ul><li>Dùng ở bước cuối buổi tối.</li><li>Thoa lớp vừa đủ, không cần rửa.</li><li>Sử dụng 2 đến 3 lần mỗi tuần.</li></ul>'],
            [56,'Son dưỡng Ceramide không màu',165000,2,14,18,'Son_Moi/1.jpg',5,'Mọi loại da','Môi khô, bong tróc','Không màu','Ceramide, bơ hạt mỡ, vitamin E',7,4.8,'3.5 g','<h4>Son dưỡng phục hồi môi khô</h4><p>Ceramide và bơ hạt mỡ tạo lớp bảo vệ mềm, hạn chế mất ẩm và làm dịu vùng môi bong tróc.</p><ul><li>Thoa lại khi môi khô.</li><li>Có thể dùng trước son màu.</li><li>Không tạo màu hay độ bóng quá mức.</li></ul>'],
            [57,'Miếng dán mụn Hydrocolloid ban đêm 36 miếng',119000,1,10,19,'products/catalog-2026/09.png',0,'Da mụn','Mụn đầu trắng đã gom cồi','Không áp dụng','Hydrocolloid y tế, màng bảo vệ siêu mỏng',7,4.7,'36 miếng','<h4>Miếng dán bảo vệ nốt mụn khỏi bụi bẩn</h4><p>Hydrocolloid hút dịch bề mặt và hạn chế tay chạm vào nốt mụn, phù hợp dùng qua đêm.</p><ul><li>Làm sạch và lau khô vùng mụn.</li><li>Dán 6 đến 8 giờ rồi thay miếng mới.</li><li>Không dùng trên vết thương sâu.</li></ul>'],
            [58,'Cushion Glow Fit SPF50+ PA+++',425000,2,7,20,'Son_Moi/2.jpg',12,'Da thường, da khô','Nền không đều màu','21 Natural Beige','Hyaluronic acid, niacinamide, màng lọc UV',7,4.8,'15 g','<h4>Phấn nước nền căng bóng tự nhiên</h4><p>Chất nền mỏng giúp làm đều màu và tạo hiệu ứng căng sáng, có chỉ số chống nắng hỗ trợ cho ngày ít hoạt động ngoài trời.</p><ul><li>Dặm từng lớp bằng bông mút.</li><li>Tập trung vùng trung tâm khuôn mặt.</li><li>Không thay thế kem chống nắng chuyên dụng.</li></ul>'],
            [59,'Kem nền Matte Cover 24H',299000,2,13,20,'Son_Moi/3.jpg',10,'Da dầu, da hỗn hợp','Dầu thừa, nền nhanh xuống tông','120 Classic Ivory','Silica, niacinamide, vitamin E',7,4.7,'30 ml','<h4>Kem nền lì che phủ trung bình đến cao</h4><p>Công thức kiểm soát bóng dầu cho lớp nền mịn, có thể tăng độ che phủ bằng cách chồng lớp mỏng.</p><ul><li>Lắc đều trước khi dùng.</li><li>Tán nhanh bằng mút ẩm hoặc cọ.</li><li>Tẩy trang kỹ vào cuối ngày.</li></ul>'],
            [60,'Che khuyết điểm Fit Me Concealer',185000,2,13,21,'Son_Moi/4.jpg',5,'Mọi loại da','Quầng thâm, vết thâm nhỏ','20 Sand','Glycerin, silica, vitamin E',7,4.7,'6.8 ml','<h4>Che khuyết điểm lỏng dễ tán</h4><p>Độ che phủ vừa giúp làm đều quầng thâm và các vùng sắc tố mà không tạo lớp nền quá dày.</p><ul><li>Chấm lượng nhỏ lên vùng cần che.</li><li>Đợi vài giây rồi tán bằng mút.</li><li>Cố định bằng phấn mỏng.</li></ul>'],
            [61,'Phấn phủ bột kiềm dầu Air Blur',239000,2,8,22,'Son_Moi/5.jpg',8,'Da dầu, da hỗn hợp','Bóng dầu, lỗ chân lông','Trong suốt','Silica siêu mịn, bột gạo, vitamin E',7,4.8,'8 g','<h4>Phấn phủ trong suốt làm mịn bề mặt</h4><p>Hạt phấn siêu mịn hỗ trợ hút dầu và làm mờ bề mặt lỗ chân lông mà không đổi màu nền.</p><ul><li>Dùng cọ lớn lấy lượng nhỏ.</li><li>Phủ vùng chữ T và dưới mắt.</li><li>Có thể dặm lại trong ngày.</li></ul>'],
            [62,'Má hồng kem Juicy Cheek',215000,2,7,23,'Son_Moi/1.jpg',10,'Mọi loại da','Khuôn mặt thiếu sức sống','Peach Coral','Squalane, vitamin E, sắc tố khoáng',7,4.8,'5 g','<h4>Má hồng kem hiệu ứng trong trẻo</h4><p>Chất kem mỏng dễ tán, tạo màu đào san hô tự nhiên và có thể tăng độ đậm theo từng lớp.</p><ul><li>Chấm hai điểm nhỏ lên gò má.</li><li>Tán bằng ngón tay hoặc mút.</li><li>Có thể dùng trên môi.</li></ul>'],
            [63,'Mascara Lash Lift chống trôi',225000,2,13,24,'Son_Moi/2.jpg',5,'Mọi loại da','Mi ngắn, dễ lem','Đen tự nhiên','Sáp thực vật, panthenol, polymer chống trôi',7,4.7,'8 ml','<h4>Mascara làm dài và giữ cong mi</h4><p>Đầu chải mảnh tách sợi mi rõ, polymer chống trôi hạn chế lem trong thời tiết nóng ẩm.</p><ul><li>Bấm mi trước khi chải.</li><li>Chải từ chân mi theo đường zigzag.</li><li>Dùng tẩy trang mắt chuyên dụng.</li></ul>'],
            [64,'Chì kẻ mày Slim Brow 1.5mm',139000,2,8,25,'Son_Moi/3.jpg',0,'Mọi loại da','Lông mày thưa','Nâu xám','Sáp carnauba, sắc tố khoáng, vitamin E',7,4.6,'0.08 g','<h4>Chì mảnh tạo sợi mày tự nhiên</h4><p>Đầu chì 1.5mm dễ phẩy sợi và định hình đuôi mày, đầu còn lại có cọ chải tiện lợi.</p><ul><li>Vẽ nhẹ theo chiều lông mày.</li><li>Chải lại để màu tán đều.</li><li>Đậy nắp sau khi dùng.</li></ul>'],
            [65,'Tẩy tế bào chết da đầu Muối biển',265000,3,4,26,'products/catalog-2026/03.jpg',8,'Da đầu dầu','Bã nhờn, cặn tạo kiểu','Không áp dụng','Muối biển mịn, bạc hà, panthenol',2,4.7,'200 g','<h4>Làm sạch sâu da đầu trước bước gội</h4><p>Muối biển mịn hỗ trợ loại bỏ cặn tạo kiểu và dầu thừa, bạc hà mang lại cảm giác thoáng mát.</p><ul><li>Dùng trên da đầu ướt.</li><li>Massage nhẹ 1 phút rồi xả.</li><li>Dùng mỗi tuần một lần.</li></ul>'],
            [66,'Mặt nạ tóc Argan phục hồi chuyên sâu',335000,3,2,27,'products/catalog-2026/03.jpg',12,'Tóc khô, tóc nhuộm','Khô xơ, chẻ ngọn','Không áp dụng','Dầu argan, keratin, bơ hạt mỡ',2,4.9,'300 ml','<h4>Ủ tóc làm mềm sợi khô xơ</h4><p>Dầu argan và bơ hạt mỡ bổ sung lipid, keratin thủy phân hỗ trợ bề mặt tóc mượt và dễ chải hơn.</p><ul><li>Thoa từ thân đến ngọn sau khi gội.</li><li>Ủ 5 đến 10 phút.</li><li>Xả kỹ bằng nước mát.</li></ul>'],
            [67,'Xịt bảo vệ tóc trước nhiệt 200ml',289000,3,2,28,'products/catalog-2026/04.jpg',5,'Mọi loại tóc','Hư tổn do máy sấy, máy uốn','Không áp dụng','Panthenol, protein lúa mì, polymer bảo vệ nhiệt',7,4.8,'200 ml','<h4>Xịt dưỡng nhẹ trước khi tạo kiểu</h4><p>Lớp polymer mỏng hỗ trợ giảm tác động trực tiếp của nhiệt và giúp tóc bớt rối khi sấy.</p><ul><li>Xịt đều lên tóc ẩm từ khoảng cách 20cm.</li><li>Chải nhẹ trước khi sấy.</li><li>Không xịt trực tiếp vào da đầu.</li></ul>'],
            [68,'Dầu dưỡng tóc Camellia bóng mượt',315000,3,1,29,'products/catalog-2026/04.jpg',10,'Tóc khô, tóc xù','Xơ rối, thiếu độ bóng','Không áp dụng','Dầu hoa trà, squalane, vitamin E',7,4.8,'80 ml','<h4>Dầu dưỡng nhẹ cho thân và ngọn tóc</h4><p>Dầu hoa trà và squalane làm mềm bề mặt, tăng độ bóng mà không tạo cảm giác quá nặng khi dùng đúng lượng.</p><ul><li>Xoa 1 đến 2 giọt trong lòng bàn tay.</li><li>Vuốt lên ngọn tóc ẩm hoặc khô.</li><li>Tránh bôi sát chân tóc.</li></ul>'],
            [69,'Kem dưỡng tay Shea Butter 75ml',149000,5,4,30,'products/catalog-2026/05.jpg',5,'Mọi loại da','Tay khô, bong tróc','Không áp dụng','Bơ hạt mỡ, glycerin, dầu hạnh nhân',7,4.8,'75 ml','<h4>Kem tay mềm mượt không quá nhờn</h4><p>Bơ hạt mỡ và glycerin bổ sung độ ẩm cho bàn tay thường xuyên tiếp xúc xà phòng hoặc điều hòa.</p><ul><li>Thoa sau khi rửa tay.</li><li>Massage thêm vùng móng và biểu bì.</li><li>Dùng lại bất cứ khi nào khô.</li></ul>'],
            [70,'Lăn khử mùi Niacinamide Bright 50ml',175000,5,5,31,'products/catalog-2026/05.jpg',10,'Mọi loại da','Mùi cơ thể, vùng da xỉn màu','Không áp dụng','Niacinamide, zinc PCA, allantoin',7,4.6,'50 ml','<h4>Lăn khử mùi dịu nhẹ hằng ngày</h4><p>Zinc PCA hỗ trợ kiểm soát mùi, niacinamide chăm sóc bề mặt da dưới cánh tay mà không dùng hạt tẩy.</p><ul><li>Dùng trên da sạch và khô.</li><li>Chờ khô trước khi mặc áo.</li><li>Không dùng ngay sau khi cạo nếu da rát.</li></ul>'],
            [71,'Dầu tắm hạnh nhân cho da khô 400ml',329000,5,2,9,'products/catalog-2026/05.jpg',8,'Da khô, da nhạy cảm','Khô căng sau khi tắm','Không áp dụng','Dầu hạnh nhân, glycerin, vitamin E',7,4.8,'400 ml','<h4>Dầu tắm làm sạch dịu nhẹ</h4><p>Kết cấu dầu chuyển sữa khi gặp nước, làm sạch cơ thể đồng thời giảm cảm giác căng khô sau khi tắm.</p><ul><li>Massage trên da ướt.</li><li>Xả sạch bằng nước ấm.</li><li>Phù hợp sử dụng hằng ngày.</li></ul>'],
            [72,'Kem gót chân Urea 10% phục hồi nứt nẻ',189000,5,11,30,'products/catalog-2026/05.jpg',0,'Da khô','Gót chân dày sừng, nứt nẻ','Không áp dụng','Urea 10%, ceramide, bơ hạt mỡ',7,4.7,'100 ml','<h4>Kem làm mềm vùng da dày sừng</h4><p>Urea 10% hỗ trợ làm mềm lớp sừng, ceramide và bơ hạt mỡ duy trì độ ẩm cho gót chân.</p><ul><li>Thoa mỗi tối lên da sạch.</li><li>Mang vớ cotton để tăng khóa ẩm.</li><li>Không dùng trên vết nứt đang chảy máu.</li></ul>'],
            [73,'Nước hoa Unisex Citrus Musk EDT',625000,4,2,8,'products/catalog-2026/05.jpg',10,'Không áp dụng','Hương thơm cá nhân','Cam chanh sạch','Cam bergamot, chanh vàng, xạ hương trắng',7,4.7,'50 ml','<h4>Hương cam chanh trong trẻo cho ngày nóng</h4><p>Mở đầu tươi sáng với bergamot và chanh, nền xạ hương trắng tạo cảm giác sạch, dùng được cho mọi giới.</p><ul><li>Nồng độ Eau de Toilette.</li><li>Xịt lên điểm mạch cách da 15cm.</li><li>Lưu hương tham khảo 4 đến 5 giờ.</li></ul>'],
            [74,'Nước hoa nam Ocean Cedar EDP',785000,4,2,7,'products/catalog-2026/05.jpg',5,'Không áp dụng','Hương thơm cá nhân','Biển và gỗ','Hương biển, gỗ tuyết tùng, hổ phách',7,4.8,'75 ml','<h4>Hương biển hiện đại trên nền gỗ ấm</h4><p>Lớp hương biển mở đầu mát, chuyển dần sang gỗ tuyết tùng và hổ phách, phù hợp đi làm hoặc buổi tối.</p><ul><li>Nồng độ Eau de Parfum.</li><li>Xịt 2 đến 4 lần tùy không gian.</li><li>Không chà xát cổ tay sau khi xịt.</li></ul>'],
            [75,'Kẹp bấm mi cong tự nhiên',99000,7,9,32,'products/new/makeup-brushes.jpg',0,'Mọi loại da','Mi thẳng, khó giữ cong','Không áp dụng','Khung thép không gỉ, đệm silicone mềm',7,4.7,'1 chiếc','<h4>Kẹp mi ôm sát đường cong mắt</h4><p>Khung thép nhẹ và đệm silicone mềm giúp tạo độ cong tự nhiên trước khi chuốt mascara.</p><ul><li>Đặt sát chân mi và bấm nhẹ 5 giây.</li><li>Không dùng sau khi chuốt mascara.</li><li>Thay đệm silicone khi bị chai cứng.</li></ul>'],
        ];
        foreach ($catalogProducts as $p) {
            DB::table('SanPham')->updateOrInsert(['MaSP'=>$p[0]], [
                'TenSP'=>$p[1], 'GiaBan'=>$p[2], 'MaDM'=>$p[3], 'MaTH'=>$p[4], 'MaLoai'=>$p[5],
                'HinhAnh'=>$p[6], 'GiamGia'=>$p[7], 'LoaiDa'=>$p[8], 'VanDeChiRi'=>$p[9],
                'TonDaMau'=>$p[10], 'ThanhPhanChiNhYeu'=>$p[11], 'SoLanSuDungMoiTuan'=>$p[12],
                'DoTinCay'=>$p[13], 'KichCoTieuChuan'=>$p[14], 'MoTa'=>$p[15], 'SoLuongTon'=>random_int(25, 90),
                'TrangThaiSanPham'=>'Còn hàng', 'NgayNhapKho'=>now(),
            ]);
        }

        DB::table('KhuyenMai')->upsert([
            ['MaKM'=>1,'TenChuongTrinh'=>'Chào mừng khách mới','MaCode'=>'WELCOME10','LoaiGiamGia'=>'PhanTram','GiaTriGiam'=>10,'GiamToiDa'=>100000,'DonHangToiThieu'=>200000,'SoLuongPhatHanh'=>100,'SoLuongDaDung'=>0,'NgayBatDau'=>now()->subDay(),'NgayKetThuc'=>now()->addMonths(3),'TrangThai'=>true],
            ['MaKM'=>2,'TenChuongTrinh'=>'Giảm 50K','MaCode'=>'GIAM50K','LoaiGiamGia'=>'TienMat','GiaTriGiam'=>50000,'GiamToiDa'=>50000,'DonHangToiThieu'=>500000,'SoLuongPhatHanh'=>50,'SoLuongDaDung'=>0,'NgayBatDau'=>now()->subDay(),'NgayKetThuc'=>now()->addMonth(),'TrangThai'=>true],
        ], ['MaKM'], ['TenChuongTrinh', 'MaCode', 'LoaiGiamGia', 'GiaTriGiam', 'GiamToiDa', 'DonHangToiThieu', 'SoLuongPhatHanh', 'NgayBatDau', 'NgayKetThuc', 'TrangThai']);
        DB::table('KhuyenMai_SanPham')->insertOrIgnore([['MaKM'=>1,'MaSP'=>11],['MaKM'=>1,'MaSP'=>13],['MaKM'=>1,'MaSP'=>30],['MaKM'=>2,'MaSP'=>16],['MaKM'=>2,'MaSP'=>38]]);
        DB::table('DanhGia')->updateOrInsert(
            ['MaSP'=>11,'MaND'=>2],
            ['NoiDung'=>'Sản phẩm dùng tốt, đóng gói đẹp.','Diem'=>5,'NgayDanhGia'=>now(),'DuocApprove'=>true]
        );
    }
}
