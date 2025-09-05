@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Nuovo Tag</h2>
            <a class="btn btn-secondary" href="{{ route('tags.index') }}"> Indietro</a>
        </div>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger mt-2">
        <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
    </div>
@endif

<form action="{{ route('tags.store') }}" method="POST" class="mt-3 card card-body">
    @csrf
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="name" class="form-label"><strong>Nome Tag:</strong></label>
            <input type="text" name="name" class="form-control" placeholder="Es. Cliente VIP" value="{{ old('name') }}" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="color" class="form-label"><strong>Colore:</strong></label>
            <input type="color" name="color" class="form-control form-control-color" value="{{ old('color', '#563d7c') }}" required>
        </div>
        <div class="col-md-12 text-center mt-3">
            <button type="submit" class="btn btn-primary">Salva Tag</button>
        </div>
    </div>
</form>
@endsection