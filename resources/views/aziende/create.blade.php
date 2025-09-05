@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Crea Nuova Azienda</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('companies.store') }}" method="POST" data-address-form="true">
        @csrf

        @foreach ($blocks as $block)
            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">{{ $block->name }}</h6></div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($block->fields as $field)
                            <x-form-field :field="$field" :users="$users ?? []" />
                        @endforeach

                        @if (str_contains($block->name, 'Località'))
                            @include('partials._address_fields')
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary">Crea Azienda</button>
            <a href="{{ route('companies.index') }}" class="btn btn-secondary">Annulla</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/address-logic.js') }}"></script>
<script>
$(document).ready(function() {
    function initializeSelect2(selector) {
        if (!$(selector).length) return;
        $(selector).select2({
            theme: "bootstrap-5",
            placeholder: $(selector).data('placeholder'),
            ajax: { 
                url: $(selector).data('ajax-url'), 
                dataType: 'json', 
                delay: 250, 
                processResults: r => ({results: r.results || r}) 
            }
        });
    }
    initializeSelect2('select[data-ajax-url]');
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
@endpush