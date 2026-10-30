<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $email = $this->input('TenDangNhap') ?? $this->input('username') ?? $this->input('Email');

        $this->merge([
            'HoTen' => $this->input('HoTen') ?? $this->input('name'),
            'TenDangNhap' => is_string($email) ? mb_strtolower(trim($email)) : $email,
            'MatKhau' => $this->input('MatKhau') ?? $this->input('password'),
            'NhapLaiMatKhau' => $this->input('NhapLaiMatKhau') ?? $this->input('password_confirmation'),
            'SoDienThoai' => $this->input('SoDienThoai') ?? $this->input('phone'),
            'DiaChi' => $this->input('DiaChi') ?? $this->input('address'),
            'GioiTinh' => $this->input('GioiTinh', 'Nam'),
            'Avatar' => $this->input('Avatar', '1.jpg'),
        ]);
    }

    public function rules(): array
    {
        return [
            'HoTen' => ['bail', 'required', 'string', 'min:2', 'max:100'],
            'TenDangNhap' => ['bail', 'required', 'string', 'email:rfc', 'max:100', Rule::unique('TaiKhoan', 'TenDangNhap')],
            'MatKhau' => [
                'bail', 'required', 'string', 'min:8', 'max:72',
                'regex:/\A(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).+\z/s',
                function ($attribute, $value, $fail) {
                    if (strlen($value) > 72) {
                        $fail('Mật khẩu quá dài. Vui lòng rút ngắn mật khẩu.');
                    }
                },
            ],
            'NhapLaiMatKhau' => ['bail', 'required', 'string', 'same:MatKhau'],
            'SoDienThoai' => ['bail', 'nullable', 'string', 'regex:/\A(?:0|\+84)[35789][0-9]{8}\z/'],
            'DiaChi' => ['nullable', 'string', 'max:255'],
            'GioiTinh' => ['required', Rule::in(['Nam', 'Nữ', 'Khác'])],
            'NgaySinh' => ['bail', 'nullable', 'date_format:Y-m-d', 'before_or_equal:today'],
            'Avatar' => ['required', Rule::in(['1.jpg', '2.jpg', '3.jpg', '4.jpg'])],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Vui lòng nhập :attribute.',
            'string' => ':attribute phải là chuỗi ký tự.',
            'HoTen.min' => 'Họ và tên phải có ít nhất 2 ký tự.',
            'HoTen.max' => 'Họ và tên không được vượt quá 100 ký tự.',
            'TenDangNhap.email' => 'Email đăng nhập không đúng định dạng.',
            'TenDangNhap.max' => 'Email đăng nhập không được vượt quá 100 ký tự.',
            'TenDangNhap.unique' => 'Email này đã được đăng ký. Vui lòng dùng email khác.',
            'MatKhau.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'MatKhau.max' => 'Mật khẩu không được vượt quá 72 ký tự.',
            'MatKhau.regex' => 'Mật khẩu phải có chữ hoa, chữ thường và số.',
            'NhapLaiMatKhau.same' => 'Mật khẩu xác nhận không khớp.',
            'SoDienThoai.regex' => 'Số điện thoại di động không hợp lệ (ví dụ: 0901234567 hoặc +84901234567).',
            'DiaChi.max' => 'Địa chỉ không được vượt quá 255 ký tự.',
            'GioiTinh.in' => 'Giới tính không hợp lệ.',
            'NgaySinh.date_format' => 'Ngày sinh không hợp lệ.',
            'NgaySinh.before_or_equal' => 'Ngày sinh không được lớn hơn ngày hiện tại.',
            'Avatar.in' => 'Vui lòng chọn một ảnh đại diện có sẵn.',
        ];
    }

    public function attributes(): array
    {
        return [
            'HoTen' => 'họ và tên', 'TenDangNhap' => 'email đăng nhập',
            'MatKhau' => 'mật khẩu', 'NhapLaiMatKhau' => 'mật khẩu xác nhận',
            'SoDienThoai' => 'số điện thoại', 'DiaChi' => 'địa chỉ',
            'GioiTinh' => 'giới tính', 'NgaySinh' => 'ngày sinh', 'Avatar' => 'ảnh đại diện',
        ];
    }
}
