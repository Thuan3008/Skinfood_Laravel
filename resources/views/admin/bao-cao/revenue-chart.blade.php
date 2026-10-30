<section class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2"><div><h2 class="h5 mb-1">Doanh thu theo {{ $revenueChart['name'] }}</h2><small class="text-muted">{{ \Carbon\Carbon::parse($from)->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }} · {{ $paidOnly ? 'Chỉ đơn đã thanh toán' : 'Gồm cả đơn chưa thanh toán và đã hủy' }}</small></div><span class="badge bg-light">Đơn vị: VNĐ</span></div>
    <div class="card-body">
    @if(empty($revenueChart['rows']))
        <div class="text-center py-5 text-muted"><i class="fa-solid fa-chart-column fa-2x mb-3" aria-hidden="true"></i><p class="mb-0">Chưa có đơn hàng trong khoảng thời gian đã chọn.</p></div>
    @else
        <p class="small text-muted mb-3">Mỗi cột là một {{ $revenueChart['name'] }}. Rê chuột hoặc chạm vào cột để xem số tiền và số đơn. Khoảng không phát sinh đơn được hiển thị bằng 0.</p>
        <div style="overflow-x:auto"><div style="height:360px;min-width:{{ max(520, count($revenueChart['rows']) * 44) }}px"><canvas id="chartDay" role="img" aria-label="Biểu đồ cột doanh thu theo {{ $revenueChart['name'] }}. Số liệu chi tiết nằm bên dưới."></canvas></div></div>
        <p id="revenue-chart-error" class="text-muted mt-3 mb-0" hidden>Không tải được biểu đồ. Bạn có thể xem đầy đủ số liệu trong bảng bên dưới.</p>
        <details class="mt-4" id="revenue-chart-table"><summary class="fw-semibold" style="cursor:pointer">Xem bảng số liệu chi tiết</summary><div class="table-responsive mt-3" style="max-height:360px;overflow-y:auto"><table class="table mb-0"><thead><tr><th>Khoảng thời gian</th><th>Số đơn</th><th>Doanh thu</th></tr></thead><tbody>@foreach($revenueChart['rows'] as $row)<tr><td>{{ $row['range'] }}</td><td>{{ $row['orders'] }}</td><td>{{ number_format($row['revenue'], 0, ',', '.') }} đ</td></tr>@endforeach</tbody><tfoot><tr><th>Tổng cộng</th><td>{{ $totalOrders }}</td><td class="fw-bold">{{ number_format($totalRevenue, 0, ',', '.') }} đ</td></tr></tfoot></table></div></details>
    @endif
    </div>
</section>
<script id="revenue-chart-data" type="application/json">@json($revenueChart['rows'])</script>
