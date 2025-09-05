@if($services->isEmpty())
    <p>Nessun servizio associato a questo cliente.</p>
@else
    @foreach($services as $service)
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>{{ $service->serviceType->name }}</strong>
                <div>
                    <button type="button" class="btn btn-primary btn-sm edit-service-btn" data-service-type-id="{{ $service->service_type_id }}" data-service-data="{{ json_encode($service->custom_fields_data) }}" data-update-url="{{ route('customer-services.update', $service->id) }}">Modifica</button>
                    <form action="{{ route('customer-services.destroy', $service->id) }}" method="POST" onsubmit="return confirm('Sei sicuro?');" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Rimuovi</button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                @if(!empty($service->custom_fields_data))
                    <dl class="row mb-0">
                    @foreach($service->serviceType->fields_schema as $field)
                        <dt class="col-sm-3">{{ $field['label'] }}:</dt>
                        <dd class="col-sm-9">
                            @if($field['type'] === 'checkbox')
                                {{ ($service->custom_fields_data[$field['name']] ?? 0) == 1 ? 'Sì' : 'No' }}
                            @else
                                {{ $service->custom_fields_data[$field['name']] ?? 'N/D' }}
                            @endif
                        </dd>
                    @endforeach
                    </dl>
                @else
                    <p class="mb-0">Nessun dettaglio aggiuntivo per questo servizio.</p>
                @endif
            </div>
        </div>
    @endforeach
@endif
