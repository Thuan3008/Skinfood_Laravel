@extends('layouts.admin')

@section('title', 'Báo cáo doanh thu chi tiết')

@section('content')
<style>
    .card-dashboard {
        border: none;
        border-radius: 12px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        transition: transform 0.2s;
    }

    .card-dashboard:hover {
        transform: translateY(-2px);
    }

    .icon-circle {
        height: 4rem;
        width: 4rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
    }

    .bg-gradient-primary {
        background: linear-gradient(45deg, #4e73df, #224abe);
        color: white;
    }

    .bg-gradient-success {
        background: linear-gradient(45deg, #1cc88a, #13855c);
        color: white;
    }

    .table th {
        background-color: #f8f9fc;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        color: #858796;
    }
</style>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="text-gray-800 fw-bold border-start border-4 border-primary ps-3 mb-1">Báo cáo doanh thu</h3>
            <p class="text-muted small ps-3 mb-0">Theo dõi hiệu suất kinh doanh chi tiết</p>
        </div>
        <div>
            <a href="{{ route('admin.bao-cao.doanh-thu', ['from' => $from, 'to' => $to, 'paidOnly' => $paidOnly ? 'true' : 'false', 'export' => 'csv']) }}" class="btn btn-success shadow-sm">
                <i class="fas fa-download fa-sm text-white-50 me-1"></i> Xuất Excel/CSV
            </a>
        </div>
    </div>

    <!-- <details class="mb-3">
        <summary class="btn btn-sm btn-outline-secondary">Show Debug Info</summary>
        <div class="card mt-2 p-3 bg-light font-monospace small">
            Labels: {{ $labelsByDay }} <br />
            Data: {{ $dataByDay }} <br />
            PaidOnly: {{ $paidOnly ? 'true' : 'false' }}
        </div>
    </details> -->

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form id="revenue-date-filter" method="get" action="{{ route('admin.bao-cao.doanh-thu') }}" class="row g-3 align-items-end" novalidate>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">Từ ngày</label>
                    <input type="date" name="from" value="{{ old('from', $from) }}" class="form-control" required />
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">Đến ngày</label>
                    <input type="date" name="to" value="{{ old('to', $to) }}" class="form-control" required />
                </div>
                <div class="col-md-3">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" value="true" id="paidOnly" name="paidOnly" @checked($paidOnly) />
                        <label class="form-check-label" for="paidOnly">Chỉ đơn đã thanh toán</label>
                    </div>
                </div>
                <div class="col-md-3 text-end">
                    <button class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i> Lọc dữ liệu</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card card-dashboard h-100 py-2 border-start border-5 border-success">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Tổng Doanh Thu
                            </div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totalRevenue, 0, ',', '.') }} đ
                            </div>
                            <div class="mt-2">
                                @if($paidOnly)
                                    <span class="badge bg-success"><i class="fas fa-check-circle"></i> Đã thực thu</span>
                                @else
                                    <span class="badge bg-secondary">Tạm tính (Cả chưa thanh toán)</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle bg-gradient-success">
                                <i class="fas fa-dollar-sign text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card card-dashboard h-100 py-2 border-start border-5 border-primary">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng Đơn Hàng
                            </div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">
                                {{ $totalOrders }} <small class="text-muted fs-6">đơn</small>
                            </div>
                            <p class="mb-0 text-muted small mt-2">Trong khoảng thời gian đã chọn</p>
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle bg-gradient-primary">
                                <i class="fas fa-shopping-cart text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.bao-cao.revenue-chart')

    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header py-3 bg-white border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-box me-1"></i> Top Sản Phẩm Bán Chạy</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-striped table-hover mb-0 align-middle">
                            <thead class="sticky-top bg-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Sản phẩm</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-end pe-3">Doanh thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($productList as $idx => $p)
                                    <tr>
                                        <td class="text-center text-muted small">{{ $idx + 1 }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $p['TenSP'] }}</div>
                                            <div class="small text-muted">ID: {{ $p['MaSP'] }}</div>
                                        </td>
                                        <td class="text-center"><span class="badge bg-light text-dark border">{{ $p['Quantity'] }}</span></td>
                                        <td class="text-end pe-3 fw-bold text-success">
                                            {{ number_format($p['Revenue'], 0, ',', '.') }} đ
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-4 text-muted">Không có dữ liệu</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header py-3 bg-white border-bottom">
                    <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-tags me-1"></i> Doanh thu theo Danh mục</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Danh mục</th>
                                    <th class="text-center">Đã bán</th>
                                    <th class="text-end pe-3">Tổng tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categoryList as $c)
                                    <tr>
                                        <td><span class="fw-bold">{{ $c['TenDM'] }}</span></td>
                                        <td class="text-center">{{ $c['Quantity'] }}</td>
                                        <td class="text-end pe-3">{{ number_format($c['Revenue'], 0, ',', '.') }} đ</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center py-4 text-muted">Không có dữ liệu</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const filter = document.getElementById('revenue-date-filter');
        const fromInput = filter.elements.from;
        const toInput = filter.elements.to;
        const shiftDay = (value, offset) => {
            if (!value) return '';
            const day = new Date(value + 'T00:00:00Z');
            day.setUTCDate(day.getUTCDate() + offset);
            return day.toISOString().slice(0, 10);
        };
        const updateDateLimits = () => {
            fromInput.max = shiftDay(toInput.value, -1);
            toInput.min = shiftDay(fromInput.value, 1);
        };
        fromInput.addEventListener('change', updateDateLimits);
        toInput.addEventListener('change', updateDateLimits);
        updateDateLimits();
        filter.addEventListener('submit', event => {
            let message = '';
            if (!fromInput.value || !toInput.value) message = 'Vui lòng chọn đầy đủ Từ ngày và Đến ngày.';
            else if (fromInput.value >= toInput.value) message = 'Từ ngày phải nhỏ hơn Đến ngày.';
            else if (!fromInput.validity.valid || !toInput.validity.valid) message = 'Khoảng ngày không hợp lệ.';
            if (message) {
                event.preventDefault();
                window.showToast(message, 'error');
                fromInput.focus();
            }
        });
        const rows = JSON.parse(document.getElementById('revenue-chart-data').textContent);
        if (!rows.length) return;
        const currency = value => new Intl.NumberFormat('vi-VN').format(value) + ' đ';
        const compact = value => {
            const n = Number(value);
            const divisor = Math.abs(n) >= 1e9 ? 1e9 : (Math.abs(n) >= 1e6 ? 1e6 : (Math.abs(n) >= 1e3 ? 1e3 : 1));
            return new Intl.NumberFormat('vi-VN', {maximumFractionDigits:1}).format(n / divisor) + ({1000000000:' tỷ',1000000:' triệu',1000:' nghìn',1:' đ'}[divisor]);
        };
        try {
            if (typeof Chart === 'undefined') throw new Error('Chart unavailable');
            new Chart(document.getElementById('chartDay'), {
                type:'bar',
                data:{labels:rows.map(row => row.label), datasets:[{label:'Doanh thu',data:rows.map(row => row.revenue),backgroundColor:'#688d9c',hoverBackgroundColor:'#334e68',borderRadius:5,maxBarThickness:42}]},
                options:{responsive:true,maintainAspectRatio:false,animation:false,interaction:{mode:'index',intersect:false},
                    plugins:{legend:{display:false},tooltip:{backgroundColor:'#fff',titleColor:'#263548',bodyColor:'#43536a',borderColor:'#dce3ec',borderWidth:1,padding:14,displayColors:false,callbacks:{title:items => rows[items[0].dataIndex].range,label:context => ['Doanh thu: '+currency(context.raw),'Số đơn: '+rows[context.dataIndex].orders]}}},
                    scales:{x:{grid:{display:false},border:{display:false},ticks:{color:'#667386',maxRotation:0,autoSkip:true,maxTicksLimit:12}},y:{beginAtZero:true,border:{display:false},title:{display:true,text:'Doanh thu (VNĐ)',color:'#667386'},ticks:{color:'#667386',maxTicksLimit:6,callback:compact},grid:{color:'#edf0f5'}}}
                }
            });
        } catch (_) {
            document.getElementById('revenue-chart-error').hidden = false;
            document.getElementById('revenue-chart-table').open = true;
        }
    });
</script>
@endpush
