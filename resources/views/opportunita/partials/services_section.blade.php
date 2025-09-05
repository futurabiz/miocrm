@if($model->serviceTypes->isNotEmpty())
    <table class="table table-sm table-hover">
        <thead>
            <tr>
                <th>Servizio</th>
                <th>Quantità</th>
                <th>Prezzo Unit.</th>
                <th>Sconto (%)</th>
                <th>Subtotale</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($model->serviceTypes as $service)
                @php
                    $subtotal = ($service->pivot->price ?? 0) * ($service->pivot->quantity ?? 1) * (1 - ($service->pivot->discount ?? 0) / 100);
                @endphp
                <tr>
                    <td>{{ $service->name }}</td>
                    <td>{{ $service->pivot->quantity }}</td>
                    <td>€ {{ number_format($service->pivot->price, 2, ',', '.') }}</td>
                    <td>{{ number_format($service->pivot->discount, 2, ',', '.') }} %</td>
                    <td><strong>€ {{ number_format($subtotal, 2, ',', '.') }}</strong></td>
                    <td class="text-end">
                        {{-- CORREZIONE: Usa la rotta corretta in inglese --}}
                        <form action="{{ route('opportunities.services.detach', ['opportunity' => $model->id, 'serviceType' => $service->id]) }}" method="POST" onsubmit="return confirm('Sei sicuro di voler rimuovere questo servizio?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p class="text-center">Nessun servizio collegato a questa opportunità.</p>
@endif

<hr>

<h5 class="mt-4">Aggiungi Nuovo Servizio</h5>
{{-- CORREZIONE: Usa la rotta corretta in inglese --}}
<form action="{{ route('opportunities.services.attach', $model->id) }}" method="POST">
    @csrf
    <div class="row g-3 align-items-end">
        <div class="col-md-5">
            <label for="service_type_id" class="form-label">Servizio</label>
            <select class="form-select" id="service_type_id" name="service_type_id" required>
                <option value="" selected disabled>Seleziona un servizio...</option>
                @foreach($allServices as $service)
                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="quantity" class="form-label">Quantità</label>
            <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" required>
        </div>
        <div class="col-md-2">
            <label for="price" class="form-label">Prezzo</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" placeholder="0.00" min="0" required>
        </div>
         <div class="col-md-2">
            <label for="discount" class="form-label">Sconto %</label>
            <input type="number" step="0.01" class="form-control" id="discount" name="discount" placeholder="0.00" min="0" max="100">
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-success">Aggiungi</button>
        </div>
    </div>
</form>