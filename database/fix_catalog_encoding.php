<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$categories = [1=>'Chăm sóc da',2=>'Trang điểm',3=>'Chăm sóc tóc',4=>'Nước hoa',5=>'Chăm sóc cơ thể',6=>'Thực phẩm chức năng',7=>'Dụng cụ làm đẹp'];
$types = [1=>'Serum',2=>'Kem dưỡng',3=>'Son môi',4=>'Dầu gội',5=>'Sữa rửa mặt',6=>'Kem chống nắng',7=>'Nước hoa nam',8=>'Nước hoa nữ',9=>'Sữa tắm',10=>'Dưỡng thể',11=>'Viên uống',12=>'Máy rửa mặt'];
$brands = [9=>'Emmié'];
foreach ($categories as $id=>$name) DB::table('DanhMuc')->where('MaDM',$id)->update(['TenDM'=>$name]);
foreach ($types as $id=>$name) DB::table('LoaiSP')->where('MaLoai',$id)->update(['TenLoai'=>$name]);
foreach ($brands as $id=>$name) DB::table('ThuongHieu')->where('MaTH',$id)->update(['TenTH'=>$name]);
$products = [
1=>['Dầu gội Pond\'s Phục hồi hư tổn Soft & Smooth','Phục hồi cấu trúc tóc hư tổn từ sâu bên trong.','Keratin, Vitamin E'],
2=>['Dầu gội Gừng Thảo mộc Ngăn rụng tóc','Chiết xuất từ gừng tươi giúp làm ấm da đầu.','Gừng tươi, Rễ nhân sâm'],
3=>['Dầu gội Dược liệu cổ truyền Dáng Diễm','Kết hợp bồ kết, hương nhu và cỏ mần trầu.','Bồ kết, Hương nhu'],
4=>['Bộ đôi Gội Xả Thảo mộc Wings Up','Giải pháp chăm sóc tóc toàn diện 3 không.','Trà xanh, Bạc hà'],
5=>['Dầu gội nước hoa cao cấp I.D Salon Grade','Hương nước hoa Pháp sang trọng, lưu hương 72h.','Collagen, Argan Oil'],
6=>['Dầu gội phủ bạc thảo dược thiên nhiên','Phủ bạc an toàn, không gây xót da đầu.','Hà thủ ô, Bồ kết'],
7=>['Dầu gội cốt Dừa tươi dưỡng ẩm Coconut','Tinh dầu dừa nguyên chất giúp cấp ẩm siêu việt.','Tinh dầu dừa'],
8=>['Dầu gội xả 2 trong 1 Herbal Care','Làm sạch và dưỡng xả trong một bước.','12 loại thảo mộc'],
9=>['Dầu gội Bưởi Cocoon Kích thích mọc tóc','Tinh dầu vỏ bưởi giúp giảm gãy rụng.','Tinh dầu vỏ bưởi, B5'],
10=>['Dầu gội Kelina Bạch quả & Trà xanh','Chống oxy hóa, bảo vệ tóc khỏi tia UV.','Bạch quả, Trà xanh'],
11=>['Kem chống nắng phục hồi Emmié','KCN vật lý lai hóa học bảo vệ da toàn diện.','Vitamin B5, Niacinamide'],
12=>['Tinh chất Vitamin C tươi dưỡng trắng','Vitamin C nồng độ cao làm mờ thâm.','Vitamin C, HA'],
13=>['Kem dưỡng ẩm Skintific 5X Ceramide','Phục hồi hàng rào bảo vệ da thần tốc.','5 loại Ceramide'],
14=>['Gel dưỡng ẩm Pond\'s Hydra Active 72h','Dạng thạch Jelly cấp nước suốt 72h.','Nước khoáng, Vitamin E'],
15=>['Kem dưỡng ẩm Cetaphil Moisturizing Cream','Dành cho da khô và da nhạy cảm.','Dầu hạnh nhân, Vitamin E'],
16=>['Serum phục hồi La Roche-Posay Hyalu B5','Cấp ẩm tầng sâu, phục hồi da kích ứng.','B5, Hyaluronic Acid'],
17=>['Serum trắng da Balance Active Formula','Dưỡng trắng bình dân hiệu quả cao.','Vitamin C ổn định'],
18=>['Son Kem Lì Lilybyred Mood Liar','Chất son velvet xốp mềm, hương trái cây.','Tinh dầu Jojoba'],
22=>['Son Bóng Black Rouge Half N Half','Hiệu ứng căng mọng tráng gương.','Tinh dầu bơ'],
23=>['Kem Chống Nắng Bảo Vệ Da Toàn Diện','Kem chống nắng vật lý lai hóa học, bảo vệ da khỏi tia UV mạnh mẽ.',null],
24=>['Serum Đặc Trị Nám & Tàn Nhang','Tinh chất giúp mờ thâm nám, làm đều màu da sau 4 tuần.',null],
25=>['Kem Dưỡng Da Ban Đêm Phục Hồi','Cấp ẩm sâu và tái tạo da trong lúc ngủ.',null],
26=>['Nước Hoa Nữ Cao Cấp Floral Scent','Hương thơm hoa cỏ nhẹ nhàng, lưu hương 8-12 tiếng.',null],
27=>['Son Dưỡng Môi Mềm Mịn Hồng Hào','Giúp môi mềm mịn, giảm nứt nẻ và hồng hào tự nhiên.',null],
28=>['Sữa Rửa Mặt Trà Xanh Ngừa Mụn','Chiết xuất trà xanh giúp sạch sâu, kháng khuẩn và ngừa mụn.',null],
29=>['Sữa Rửa Mặt Dược Liệu Dịu Nhẹ','An toàn cho da nhạy cảm, không gây khô căng.',null],
30=>['Sữa Tắm Trắng Da Tinh Chất Sữa','Nuôi dưỡng làn da cơ thể trắng sáng, mịn màng.',null],
31=>['Tinh Dầu Bưởi Kích Thích Mọc Tóc','Giúp tóc chắc khỏe, giảm gãy rụng và kích thích mọc tóc con.',null],
32=>['Kem Dưỡng Da Tay & Body Chiết Xuất Dừa','Dưỡng ẩm chuyên sâu cho vùng da khô ráp.',null],
33=>['Sữa Dưỡng Thể Mịn Da Hương Nước Hoa','Thẩm thấu nhanh, không bết rít, hương thơm sang trọng.',null],
];
foreach ($products as $id=>$values) {
    $data=['TenSP'=>$values[0],'MoTa'=>$values[1]];
    if ($values[2] !== null) $data['ThanhPhanChiNhYeu']=$values[2];
    DB::table('SanPham')->where('MaSP',$id)->update($data);
}
echo "Catalog text repaired: ".count($products)." products, ".count($categories)." categories, ".count($types)." types.\n";
