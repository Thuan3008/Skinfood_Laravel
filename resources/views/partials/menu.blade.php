@php
    $allCategories = \App\Models\DanhMuc::with('loaiSanPhams')->get();
    // Giống Web_MyPham: Thanh ngang hiển thị các danh mục chính (5 danh mục), toàn bộ xem tại dropdown "DANH MỤC SẢN PHẨM"
    $mainCategories = $allCategories->take(5);
@endphp
<section id="f-menu">
    <div class="container">
        <div class="f-menu-wrap">

            <!-- Nút DANH MỤC SẢN PHẨM có dropdown -->
            <div class="f-menu-nav">
                <a href="{{ route('home') }}" class="f-menu-nav-btn f-menu-main-btn">
                    <svg fill="none" height="20" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg">
                        <line x1="3" x2="21" y1="12" y2="12"></line>
                        <line x1="3" x2="21" y1="6" y2="6"></line>
                        <line x1="3" x2="21" y1="18" y2="18"></line>
                    </svg>
                    <span> DANH MỤC SẢN PHẨM</span>
                </a>

                <!-- Menu ẩn, sẽ hiện khi hover -->
                <div class="dropdown-menu">
                    <ul class="submenu">
                        @foreach($allCategories as $cat)
                            <li>
                                <a href="{{ route('home.category', $cat->MaDM) }}">
                                    {{ $cat->TenDM }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Các danh mục chính lấy từ database (5 danh mục như Web_MyPham) -->
            <div class="f-menu-list">
                <ul class="new-main-menu">
                    @foreach($mainCategories as $dm)
                        <div class="f-menu-nav">
                            <a href="{{ route('home.category', $dm->MaDM) }}" class="f-menu-nav-btn">
                                <span>{{ mb_strtoupper($dm->TenDM, 'UTF-8') }}</span>
                            </a>

                            <div class="dropdown-menu">
                                <ul>
                                    @foreach($dm->loaiSanPhams->unique('TenLoai') as $loai)
                                        <li>
                                            <a href="{{ route('home.type', $loai->MaLoai) }}">
                                                {{ $loai->TenLoai }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endforeach
                </ul>
            </div>

        </div>
    </div>
</section>
