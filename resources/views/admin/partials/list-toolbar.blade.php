@php
    $listConfig = \App\Support\AdminLists::config($resource);
    $extrasOnly = $extrasOnly ?? false;
@endphp
@unless($extrasOnly)<form method="get" action="{{ url()->current() }}" class="row g-3 admin-filter-bar mb-4">@endunless
@unless($extrasOnly)
<div class="col-12 col-md-6 col-xl-4"><label class="form-label" for="list-query">Tìm kiếm</label><div class="input-group"><span class="input-group-text"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></span><input id="list-query" class="form-control" type="search" name="q" value="{{ request('q') }}" placeholder="Tìm theo tên, mã hoặc thông tin liên quan…" maxlength="200"></div></div>
@foreach($listConfig['filters'] as $field => [$label,$options])
<div class="col-6 col-md-3 col-xl-2"><label class="form-label" for="list-{{ $field }}">{{ $label }}</label><select class="form-select" name="f_{{ $field }}" id="list-{{ $field }}"><option value="">Tất cả</option>@foreach($options as $value => $text)<option value="{{ $value }}" @selected((string) request('f_'.$field,'') === (string) $value)>{{ $text }}</option>@endforeach</select></div>
@endforeach
@if($listConfig['date'])
<div class="col-6 col-md-3 col-xl-2"><label class="form-label" for="list-from">Từ ngày</label><input class="form-control" type="date" id="list-from" name="_from" value="{{ request('_from') }}"></div>
<div class="col-6 col-md-3 col-xl-2"><label class="form-label" for="list-to">Đến ngày</label><input class="form-control" type="date" id="list-to" name="_to" value="{{ request('_to') }}"></div>
@endif
@endunless
<div class="col-6 col-md-3 col-xl-2"><label class="form-label" for="list-sort">Sắp xếp</label><select class="form-select" id="list-sort" name="_sort">@foreach($listConfig['sorts'] as $value => $label)<option value="{{ $value }}" @selected(request('_sort','newest') === $value)>{{ $label }}</option>@endforeach</select></div>
@unless($extrasOnly)
<div class="col-12 col-sm-auto d-flex align-items-end gap-2 admin-filter-actions"><button class="btn btn-primary" type="submit"><i class="fa-solid fa-filter me-1" aria-hidden="true"></i>Áp dụng</button><a class="btn btn-outline-secondary" href="{{ url()->current() }}"><i class="fa-solid fa-rotate-left me-1" aria-hidden="true"></i>Đặt lại</a></div>
</form>
@endunless
