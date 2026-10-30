<?php

namespace App\Http\Controllers;

use App\Support\AdminResources;
use App\Services\OrderPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminRecordController extends Controller
{
    public function show(string $resource, int $id)
    {
        [$model, $title] = AdminResources::get($resource);
        $record = $model::findOrFail($id);
        if ($resource === 'lien-he') return view('admin.lien-he.details', ['contact'=>$record, 'canReply'=>false]);
        return view('admin.records.show', compact('record', 'resource', 'title'));
    }

    public function edit(string $resource, int $id)
    {
        [$model, $title, $editRoute] = AdminResources::get($resource);
        $record = $model::findOrFail($id);
        if ($resource === 'lien-he') return view('admin.lien-he.details', ['contact'=>$record, 'canReply'=>true]);
        if ($editRoute) return redirect()->route($editRoute, $id);
        $fields = AdminResources::fields($resource);
        return view('admin.records.edit', compact('record', 'resource', 'title', 'fields'));
    }

    public function update(Request $request, string $resource, int $id)
    {
        [$model] = AdminResources::get($resource);
        $record = $model::findOrFail($id);
        $fields = AdminResources::fields($resource);
        abort_if(!$fields, 404);
        $data = $request->validate(array_map(fn ($field) => $field[1], $fields));
        if ($resource === 'don-hang') {
            app(OrderPaymentService::class)->updateStatus($id, $data['status']);
        } elseif ($resource === 'lien-he') {
            // Reuse the existing reply flow and its notification behavior.
            $request->merge(['reply' => $data['PhanHoi'], 'PhanHoi' => $data['PhanHoi']]);
            return app(LienHeController::class)->reply($request, $id);
        } elseif (in_array($resource, ['phieu-nhap', 'phieu-xuat'], true)) {
            $details = $request->validate([
                'details' => 'required|array|min:1', 'details.*.MaSP' => 'required|integer|distinct',
                'details.*.SoLuong' => 'required|integer|min:1', 'details.*.price' => 'required|numeric|min:0',
            ], [
                'details.*.SoLuong.min' => 'Số lượng phải lớn hơn 0, không được bằng 0 hoặc âm.',
                'details.*.SoLuong.integer' => 'Số lượng phải là số nguyên dương.',
            ])['details'];
            app(\App\Services\WarehouseAdjustmentService::class)->update($resource, $id, $data, $details);
        } else {
            $record->update($data);
        }
        if ($request->expectsJson()) return response()->json(['success' => true, 'message' => 'Đã cập nhật thông tin.']);
        return back()->with('success', 'Đã cập nhật thông tin.');
    }

    public function hide(Request $request, string $resource, int $id)
    {
        [$model] = AdminResources::get($resource);
        $record = $model::findOrFail($id);
        DB::table('AdminHiddenRecords')->updateOrInsert(
            ['ResourceTable' => $record->getTable(), 'RecordId' => $record->getKey()],
            ['HiddenBy' => $request->user()->MaND, 'HiddenAt' => now()]
        );
        $message = 'Đã ẩn khỏi danh sách. Dữ liệu vẫn được lưu trong hệ thống.';
        if ($request->expectsJson()) return response()->json(['success' => true, 'message' => $message]);
        return back()->with('success', $message);
    }
}
