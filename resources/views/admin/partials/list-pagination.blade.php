<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-4 mb-3">
    <small class="text-muted">Hiển thị {{ $paginator->firstItem() ?? 0 }}–{{ $paginator->lastItem() ?? 0 }} / {{ $paginator->total() }} bản ghi</small>
    @if($paginator->hasPages())
        {{ $paginator->onEachSide(1)->links('pagination::bootstrap-5') }}
    @else
        <nav aria-label="Phân trang"><ul class="pagination mb-0"><li class="page-item disabled"><span class="page-link">Trước</span></li><li class="page-item active"><span class="page-link" aria-current="page">1</span></li><li class="page-item disabled"><span class="page-link">Sau</span></li></ul></nav>
    @endif
</div>
