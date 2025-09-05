@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Modifica Template Email</h2>
            <a class="btn btn-secondary" href="{{ route('email_templates.index') }}"> Indietro</a>
        </div>
    </div>
</div>
@if ($errors->any())
    <div class="alert alert-danger mt-2">
        <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
    </div>
@endif
<form action="{{ route('email_templates.update', $emailTemplate->id) }}" method="POST" class="mt-3 card card-body">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-12 mb-3">
            <label for="name" class="form-label"><strong>Nome Template (per uso interno):</strong></label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $emailTemplate->name) }}" required>
        </div>
        <div class="col-12 mb-3">
            <label for="subject" class="form-label"><strong>Oggetto dell'Email:</strong></label>
            <input type="text" name="subject" class="form-control" value="{{ old('subject', $emailTemplate->subject) }}" required>
            {{-- MODIFICA APPLICATA QUI: Aggiunta la @ per "escapare" la sintassi di Blade --}}
            <small class="form-text text-muted">Puoi usare segnaposto come: `@{{ contact.first_name }}`, `@{{ contact.last_name }}`, `@{{ contact.email }}`</small>
        </div>
        <div class="col-12 mb-3">
            <label for="body" class="form-label"><strong>Corpo dell'Email:</strong></label>
            <textarea name="body" class="form-control" rows="10" required>{{ old('body', $emailTemplate->body) }}</textarea>
            <small class="form-text text-muted">Questo campo supporta HTML. Per andare a capo, usa `<br>`.</small>
        </div>
        <div class="col-md-12 text-center mt-3">
            <button type="submit" class="btn btn-primary">Salva Modifiche</button>
        </div>
    </div>
</form>
@endsection
