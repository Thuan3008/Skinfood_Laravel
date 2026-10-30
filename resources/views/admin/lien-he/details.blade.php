@extends('layouts.admin')
@section('title', 'Trao đổi liên hệ #'.$contact->MaLH)
@section('content')
<div class="container py-3">
    <h1 class="h4 mb-4">Liên hệ #{{ $contact->MaLH }}</h1>
    @include('partials.contact-chat', ['isAdminChat' => true, 'canReply' => $canReply ?? true])
</div>
@endsection
