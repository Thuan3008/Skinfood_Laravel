<?php

namespace App\Support;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminLists
{
    public static function config(string $resource): array
    {
        $config = match ($resource) {
            'san-pham' => ['TenSP', null, ['TenSP','MaSP'], []],
            'danh-muc' => ['TenDM', null, ['TenDM','MaDM'], ['has_products' => ['Sản phẩm', ['yes'=>'Có sản phẩm','no'=>'Chưa có sản phẩm']]]],
            'thuong-hieu' => ['TenTH', null, ['TenTH','QuocGia','MaTH'], ['QuocGia'=>['Quốc gia', \App\Models\ThuongHieu::whereNotNull('QuocGia')->where('QuocGia','!=','')->distinct()->orderBy('QuocGia')->pluck('QuocGia','QuocGia')->all()]]],
            'loai-sp' => ['TenLoai', null, ['TenLoai','MaLoai','danhMuc.TenDM'], ['MaDM'=>['Danh mục', \App\Models\DanhMuc::orderBy('TenDM')->pluck('TenDM','MaDM')->all()]]],
            'nha-cung-cap' => ['TenNCC', null, ['TenNCC','SoDienThoai','Email','DiaChi','NguoiLienHe','MaNCC'], ['has_email'=>['Email', ['yes'=>'Có email','no'=>'Chưa có email']]]],
            'nguoi-dung' => ['HoTen', 'NgayTao', ['HoTen','SoDienThoai','MaND','taiKhoans.TenDangNhap'], ['GioiTinh'=>['Giới tính', ['Nam'=>'Nam','Nữ'=>'Nữ','Khác'=>'Khác']]]],
            'tai-khoan' => ['TenDangNhap', null, ['TenDangNhap','MaTK','nguoiDung.HoTen'], ['VaiTro'=>['Vai trò', ['Admin'=>'Admin','User'=>'User']], 'TrangThai'=>['Trạng thái', ['active'=>'Hoạt động','inactive'=>'Ngừng hoạt động']]]],
            'don-hang' => ['TenNguoiNhan', 'NgayDat', ['MaDH','TenNguoiNhan','SoDienThoai','nguoiDung.HoTen'], ['TrangThaiThanhToan'=>['Thanh toán', array_combine(['Chưa thanh toán','Đã chuyển khoản - chờ xác nhận','Đã thanh toán','Đã hủy'], ['Chưa thanh toán','Đã chuyển khoản - chờ xác nhận','Đã thanh toán','Đã hủy'])], 'PhuongThucThanhToan'=>['Phương thức', ['COD'=>'COD','BankTransfer'=>'Chuyển khoản','QR'=>'QR']]]],
            'danh-gia' => ['NoiDung', 'NgayDanhGia', ['NoiDung','MaDG','sanPham.TenSP','nguoiDung.HoTen'], ['Diem'=>['Số sao', [1=>'1 sao',2=>'2 sao',3=>'3 sao',4=>'4 sao',5=>'5 sao']], 'DuocApprove'=>['Duyệt', [1=>'Đã duyệt',0=>'Chờ duyệt']]]],
            'lien-he' => ['HoTen', 'NgayGui', ['HoTen','Email','SoDienThoai','NoiDung','MaLH'], ['reply'=>['Phản hồi', ['yes'=>'Đã phản hồi','no'=>'Chờ phản hồi']]]],
            'khuyen-mai' => ['TenChuongTrinh', 'NgayBatDau', ['TenChuongTrinh','MaCode','MaKM'], ['TrangThai'=>['Trạng thái', [1=>'Hoạt động',0=>'Tắt']], 'LoaiGiamGia'=>['Loại giảm', ['PhanTram'=>'Phần trăm','TienMat'=>'Số tiền']]]],
            'phieu-nhap' => ['MaPN', 'NgayNhap', ['MaPN','GhiChu','nhaCungCap.TenNCC'], ['MaNCC'=>['Nhà cung cấp', \App\Models\NhaCungCap::orderBy('TenNCC')->pluck('TenNCC','MaNCC')->all()]]],
            'phieu-xuat' => ['NguoiNhanHang', 'NgayXuat', ['MaPX','NguoiNhanHang','SoDienThoaiNguoiNhan','DiaChiNguoiNhan','GhiChu'], ['has_note'=>['Ghi chú', ['yes'=>'Có ghi chú','no'=>'Không có ghi chú']]]],
            default => abort(404),
        };
        [$name, $date, $search, $filters] = $config;
        $sorts = ['newest'=>'Mới nhất','oldest'=>'Cũ nhất','name_asc'=>'Tên A–Z','name_desc'=>'Tên Z–A'];
        if ($resource === 'phieu-nhap') { $sorts['name_asc'] = 'Mã phiếu tăng dần'; $sorts['name_desc'] = 'Mã phiếu giảm dần'; }
        if ($resource === 'danh-gia') { $sorts['name_asc'] = 'Nội dung A–Z'; $sorts['name_desc'] = 'Nội dung Z–A'; }
        if (in_array($resource, ['don-hang','phieu-nhap','phieu-xuat'])) $sorts += ['total_asc'=>'Tổng tiền tăng dần','total_desc'=>'Tổng tiền giảm dần'];
        if ($resource === 'san-pham') $sorts += ['price_asc'=>'Giá tăng dần','price_desc'=>'Giá giảm dần','stock_asc'=>'Tồn kho tăng dần','stock_desc'=>'Tồn kho giảm dần'];
        if ($resource === 'danh-gia') $sorts += ['rating_asc'=>'Số sao tăng dần','rating_desc'=>'Số sao giảm dần'];
        return compact('name','date','search','filters','sorts');
    }

    public static function paginate($query, string $resource, bool $search = true)
    {
        $request = request();
        $config = self::config($resource);
        $rules = ['q'=>'nullable|string|max:200', '_sort'=>['nullable', Rule::in(array_keys($config['sorts']))], '_per_page'=>'nullable|integer|in:10,20,50,100', '_from'=>'nullable|date_format:Y-m-d', '_to'=>'nullable|date_format:Y-m-d'];
        if ($request->filled('_from') && $request->filled('_to')) $rules['_from'] .= '|before:_to';
        foreach ($config['filters'] as $field => [$label,$options]) $rules['f_'.$field] = ['nullable', Rule::in(array_map('strval', array_keys($options)))];
        $validator = Validator::make($request->query(), $rules, ['_from.before'=>'Từ ngày phải nhỏ hơn Đến ngày.']);
        if ($validator->fails()) throw (new ValidationException($validator))->redirectTo($request->url());
        if ($search && $request->filled('q')) {
            $term = trim($request->query('q'));
            $query->where(function ($where) use ($term, $config) {
                foreach ($config['search'] as $field) {
                    if (str_contains($field,'.')) {
                        [$relation,$column] = explode('.',$field,2);
                        $where->orWhereHas($relation, fn ($related) => $related->where($column,'like','%'.$term.'%'));
                    } else $where->orWhere($field,'like','%'.$term.'%');
                }
            });
        }
        foreach ($config['filters'] as $field => $definition) {
            if (!$request->filled('f_'.$field)) continue;
            $value = $request->query('f_'.$field);
            if ($field === 'has_products') { $value === 'yes' ? $query->has('sanPhams') : $query->doesntHave('sanPhams'); }
            elseif (in_array($field,['has_email','has_note'])) {
                $column = $field === 'has_email' ? 'Email' : 'GhiChu';
                $query->where(fn ($builder) => $value === 'yes' ? $builder->whereNotNull($column)->where($column,'!=','') : $builder->whereNull($column)->orWhere($column,''));
            } elseif ($field === 'reply') {
                $query->where(function ($builder) use ($value) {
                    if ($value === 'yes') $builder->whereNotNull('PhanHoi')->where(fn ($status) => $status->whereNull('TrangThai')->orWhere('TrangThai','!=','Chờ phản hồi'));
                    else $builder->whereNull('PhanHoi')->orWhere('TrangThai','Chờ phản hồi');
                });
            } else $query->where($field,$value);
        }
        if ($config['date']) {
            if ($request->filled('_from')) $query->whereDate($config['date'],'>=',$request->query('_from'));
            if ($request->filled('_to')) $query->whereDate($config['date'],'<=',$request->query('_to'));
        }
        $key = $query->getModel()->getKeyName();
        $sort = $request->query('_sort','newest');
        [$column,$direction] = match ($sort) {
            'oldest'=>[$config['date'] ?? $key,'asc'], 'name_asc'=>[$config['name'],'asc'], 'name_desc'=>[$config['name'],'desc'],
            'total_asc'=>['TongTien','asc'], 'total_desc'=>['TongTien','desc'], 'price_asc'=>['GiaBan','asc'], 'price_desc'=>['GiaBan','desc'],
            'stock_asc'=>['SoLuongTon','asc'], 'stock_desc'=>['SoLuongTon','desc'], 'rating_asc'=>['Diem','asc'], 'rating_desc'=>['Diem','desc'],
            default=>[$config['date'] ?? $key,'desc'],
        };
        return $query->reorder($column,$direction)->orderBy($key,$direction)->paginate((int) $request->query('_per_page',10))->withQueryString();
    }
}
