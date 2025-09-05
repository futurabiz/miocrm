<table class="table table-hover table-sm">
    <thead>
        <tr>
            <th>Nome Opportunità</th>
            <th>Fase</th>
            <th>Valore</th>
            <th>Data Chiusura</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @forelse($opportunities as $opportunity)
            <tr>
                <td>{{ $opportunity->name }}</td>
                <td><span class="badge bg-primary">{{ $opportunity->stage }}</span></td>
                <td>€ {{ number_format($opportunity->amount, 2, ',', '.') }}</td>
                <td>{{ $opportunity->closing_date ? \Carbon\Carbon::parse($opportunity->closing_date)->format('d/m/Y') : '' }}</td>
                <td class="text-end">
                    <a href="{{ route('opportunities.show', $opportunity->id) }}" class="btn btn-info btn-sm">Visualizza</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">Nessuna opportunità collegata a questa azienda.</td>
            </tr>
        @endforelse
    </tbody>
</table>
{{-- CORREZIONE: Usiamo $company invece di $azienda --}}
<a href="{{ route('opportunities.create', ['company_id' => $company->id]) }}" class="btn btn-primary btn-sm mt-2">
    <i class="bi bi-plus-circle"></i> Aggiungi Nuova Opportunità
</a>