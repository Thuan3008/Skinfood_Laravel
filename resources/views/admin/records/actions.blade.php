<div class="admin-record-actions">
    <a href="{{ route('admin.records.show', [$resource, $id]) }}" class="btn btn-sm btn-outline-info" data-admin-popup title="Xem" aria-label="Xem"><i class="fa-solid fa-eye" aria-hidden="true"></i></a>
    <a href="{{ route('admin.records.edit', [$resource, $id]) }}" class="btn btn-sm btn-outline-primary" data-admin-popup title="Sửa" aria-label="Sửa"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i></a>
    <form method="post" action="{{ route('admin.records.hide', [$resource, $id]) }}" data-admin-hide data-confirm="Xóa khỏi danh sách? Dữ liệu và tồn kho vẫn được giữ nguyên.">
        @csrf @method('delete')
        <button class="btn btn-sm btn-outline-danger" title="Xóa" aria-label="Xóa"><i class="fa-solid fa-trash" aria-hidden="true"></i></button>
    </form>
</div>
