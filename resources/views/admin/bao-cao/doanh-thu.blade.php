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
            <form method="get" action="{{ route('admin.bao-cao.doanh-thu') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">Từ ngày</label>
                    <input type="date" name="from" value="{{ $from }}" class="form-control" />
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">Đến ngày</label>
                    <input type="date" name="to" value="{{ $to }}" class="form-control" />
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

    <div class="card shadow-sm mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
            <h6 class="m-0 font-weight-bold text-primary">Biểu đồ biến động doanh thu</h6>
        </div>
        <div class="card-body">
            <div class="chart-area" style="height: 320px;">
                <canvas id="chartDay"></canvas>
            </div>
            @if($totalOrders == 0)
                <div class="text-center text-muted mt-3">
                    <i class="fas fa-inbox fa-3x mb-2"></i>
                    <p>Chưa có dữ liệu phát sinh trong khoảng thời gian này.</p>
                </div>
            @endif
        </div>
    </div>

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
        try {
            var labels = {!! $labelsByDay !!};
            var data = {!! $dataByDay !!};

            const formatCurrency = (v) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(v);

            var ctxEl = document.getElementById('chartDay');
            if (ctxEl) {
                var ctx = ctxEl.getContext('2d');

                if (Array.isArray(labels) && Array.isArray(data) && labels.length > 0 && data.length > 0) {
                    var drawData = data.slice();
                    var drawLabels = labels.slice();
                    if (drawData.length === 1) {
                        drawData.push(drawData[0]);
                        drawLabels.push('');
                    }

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: drawLabels,
                            datasets: [{
                                label: 'Doanh thu',
                                data: drawData,
                                borderColor: '#4e73df',
                                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                                pointBackgroundColor: '#4e73df',
                                pointBorderColor: '#fff',
                                pointHoverBackgroundColor: '#fff',
                                pointHoverBorderColor: '#4e73df',
                                fill: true,
                                tension: 0.3,
                                pointRadius: 5,
                                pointHoverRadius: 7,
                                borderWidth: 2
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: "rgb(255,255,255)",
                                    bodyColor: "#858796",
                                    titleColor: '#6e707e',
                                    borderColor: '#dddfeb',
                                    borderWidth: 1,
                                    padding: 12,
                                    displayColors: false,
                                    callbacks: {
                                        label: function(context) {
                                            return 'Doanh thu: ' + formatCurrency(context.raw);
                                        }
                                    }
                                }
                            },
                            layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } },
                            scales: {
                                x: { grid: { display: false, drawBorder: false }, ticks: { maxTicksLimit: 7 } },
                                y: {
                                    ticks: {
                                        maxTicksLimit: 5,
                                        padding: 10,
                                        callback: (v) => formatCurrency(v)
                                    },
                                    grid: { color: "rgb(234, 236, 244)", drawBorder: false, borderDash: [2], zeroLineBorderDash: [2] }
                                }
                            }
                        }
                    });
                }
            }
        } catch (err) {
            console.error('Chart Error:', err);
        }
    });
</script>
@endpush
