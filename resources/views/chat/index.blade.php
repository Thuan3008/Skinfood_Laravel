@extends('layouts.app')
@section('title','Tư vấn SkinFood')
@section('content')
<div class="container py-5" style="max-width:760px"><h1>Tư vấn chăm sóc da</h1><div id="chat-messages" class="border rounded p-3 mb-3" style="min-height:180px"></div><form id="chat-form"><div class="input-group"><input id="chat-input" class="form-control" placeholder="Nhập câu hỏi..." required><button class="btn btn-danger">Gửi</button></div></form></div>
@push('scripts')<script>document.getElementById('chat-form').addEventListener('submit',async e=>{e.preventDefault();const input=document.getElementById('chat-input'),box=document.getElementById('chat-messages');box.insertAdjacentHTML('beforeend','<p><strong>Bạn:</strong> '+input.value.replace(/[&<>]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;'}[c]))+'</p>');const r=await fetch('{{ route('chat.message') }}',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({message:input.value})});const d=await r.json();box.insertAdjacentHTML('beforeend','<p><strong>SkinFood:</strong> '+d.message+'</p>');input.value='';});</script>@endpush
@endsection
