@extends('layouts.app')
@section('title', 'Trò chuyện với SkinFood')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4"><h1 class="h3 mb-0">Trao đổi với SkinFood</h1></div>
    @include('partials.contact-chat', ['isAdminChat' => false])
</div>
@endsection
