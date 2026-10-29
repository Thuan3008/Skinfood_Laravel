@if ($paginator->hasPages())
    @php
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();

        if ($lastPage <= 5) {
            $visiblePages = range(1, $lastPage);
        } elseif ($currentPage <= 3) {
            $visiblePages = [1, 2, 3, 4, $lastPage];
        } elseif ($currentPage >= $lastPage - 2) {
            $visiblePages = [1, $lastPage - 3, $lastPage - 2, $lastPage - 1, $lastPage];
        } else {
            $visiblePages = [1, $currentPage - 1, $currentPage, $currentPage + 1, $lastPage];
        }

        $visiblePages = array_values(array_unique($visiblePages));
        sort($visiblePages);
    @endphp

    <nav class="d-flex justify-items-center justify-content-between" aria-label="Phân trang">
        <div class="d-flex justify-content-between flex-fill d-sm-none">
            <ul class="pagination mb-0">
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link">Trước</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">Trước</a></li>
                @endif

                @if ($paginator->hasMorePages())
                    <li class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Sau</a></li>
                @else
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link">Sau</span></li>
                @endif
            </ul>
        </div>

        <div class="d-none flex-sm-fill d-sm-flex align-items-sm-center justify-content-sm-between gap-3">
            <div class="small text-muted text-nowrap">
                Hiển thị <span class="fw-semibold">{{ $paginator->firstItem() }}</span>
                đến <span class="fw-semibold">{{ $paginator->lastItem() }}</span>
                trong <span class="fw-semibold">{{ $paginator->total() }}</span> kết quả
            </div>

            <div class="d-flex align-items-center gap-3 flex-wrap justify-content-end">
                <ul class="pagination mb-0">
                    @if ($paginator->onFirstPage())
                        <li class="page-item disabled" aria-disabled="true" aria-label="Trang trước">
                            <span class="page-link" aria-hidden="true">&lsaquo;</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Trang trước">&lsaquo;</a>
                        </li>
                    @endif

                    @foreach ($visiblePages as $index => $page)
                        @if ($index > 0 && $page - $visiblePages[$index - 1] > 1)
                            <li class="page-item disabled" aria-disabled="true"><span class="page-link">…</span></li>
                        @endif

                        @if ($page === $currentPage)
                            <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a></li>
                        @endif
                    @endforeach

                    @if ($paginator->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Trang sau">&rsaquo;</a>
                        </li>
                    @else
                        <li class="page-item disabled" aria-disabled="true" aria-label="Trang sau">
                            <span class="page-link" aria-hidden="true">&rsaquo;</span>
                        </li>
                    @endif
                </ul>

                <form method="GET" action="{{ $paginator->path() }}" class="d-flex align-items-center gap-2 page-jump-form">
                    @foreach (request()->except($paginator->getPageName()) as $key => $value)
                        @if (is_scalar($value))
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @elseif (is_array($value))
                            @foreach ($value as $item)
                                @if (is_scalar($item))
                                    <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                    <label for="pageJump-{{ $paginator->getPageName() }}" class="small text-muted text-nowrap mb-0">Đến trang</label>
                    <input
                        id="pageJump-{{ $paginator->getPageName() }}"
                        type="number"
                        name="{{ $paginator->getPageName() }}"
                        class="form-control form-control-sm text-center"
                        min="1"
                        max="{{ $lastPage }}"
                        value="{{ $currentPage }}"
                        aria-label="Nhập số trang từ 1 đến {{ $lastPage }}"
                        style="width:72px"
                        required
                    >
                    <button type="submit" class="btn btn-outline-primary btn-sm text-nowrap">Đi</button>
                </form>
            </div>
        </div>
    </nav>
@endif
