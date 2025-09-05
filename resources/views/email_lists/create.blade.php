@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Nuova Lista Email</h2>
            <a class="btn btn-secondary" href="{{ route('email_lists.index') }}"> Indietro</a>
        </div>
    </div>
</div>
@if ($errors->any())
    <div class="alert alert-danger mt-2">
        <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
    </div>
@endif
<form action="{{ route('email_lists.store') }}" method="POST" class="mt-3 card card-body">
    @csrf
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="name" class="form-label"><strong>Nome Lista:</strong></label>
            <input type="text" name="name" class="form-control" placeholder="Es. Newsletter Mensile" value="{{ old('name') }}" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="description" class="form-label"><strong>Descrizione:</strong></label>
            <input type="text" name="description" class="form-control" placeholder="A cosa serve questa lista?" value="{{ old('description') }}">
        </div>
        <div class="col-md-12 text-center mt-3">
            <button type="submit" class="btn btn-primary">Salva Lista</button>
        </div>
    </div>
</form>
@endsection
