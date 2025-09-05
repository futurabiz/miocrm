<div class="table-responsive">
    <table class="table table-hover table-sm">
        <thead>
            <tr>
                <th>Nome Opportunità</th>
                <th>Fase</th>
                <th class="text-end">Importo</th>
                <th>Data Chiusura</th>
                <th width="50px"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($opportunities as $opportunity)
            <tr>
                <td>{{ $opportunity->name }}</td>
                <td><span class="badge bg-info text-dark">{{ $opportunity->stage }}</span></td>
                <td class="text-end">€ {{ number_format($opportunity->amount, 2, ',', '.') }}</td>
                <td>{{ $opportunity->closing_date ? $opportunity->closing_date->format('d/m/Y') : 'N/D' }}</td>
                <td>
                    <a href="{{ route('opportunita.show', $opportunity->id) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Vedi Dettagli">
                        <i class="bi bi-eye"></i>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Nessuna opportunità trovata.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>