@php
    $listPaginator = collect([$products ?? null, $categories ?? null, $types ?? null, $brands ?? null, $suppliers ?? null, $accounts ?? null, $users ?? null, $orders ?? null, $reviews ?? null, $contacts ?? null, $promotions ?? null, $phieus ?? null])->first(fn ($item) => $item instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator);
@endphp
@if($listPaginator)
<div class="admin-page-context">
    @if($listPaginator)<span class="admin-record-count">{{ number_format($listPaginator->total(), 0, ',', '.') }} bản ghi <span aria-hidden="true">·</span> Trang {{ $listPaginator->currentPage() }}/{{ $listPaginator->lastPage() }}</span>@endif
</div>
@endif
