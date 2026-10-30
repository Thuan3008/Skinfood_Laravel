<?php

namespace App\Http\Controllers;

use App\Models\NguoiDung;
use Illuminate\Http\Request;

class NguoiDungController extends Controller
{
    public function index(Request $request)
    {
        $users = \App\Support\AdminLists::paginate(NguoiDung::visibleInAdmin()->with('taiKhoans'), 'nguoi-dung');
        return view('admin.nguoi-dung.index', ['users' => $users, 'q' => request('q', '')]);
    }

    public function destroy(int $id)
    {
        return app(AdminRecordController::class)->hide(request(), 'nguoi-dung', $id);
    }

    public function show(int $id)
    {
        return redirect()->route('admin.nguoi-dung.index');
    }
}
