<div class="table-responsive">
    <table class="table table-bordered table-striped" style="font-size: 0.9rem;">
        <tbody>
            <tr>
                <th style="width: 25%;">Nome Opportunità</th>
                <td>{{ $opportunity->name ?? '---' }}</td>
            </tr>
            <tr>
                <th>Fase</th>
                <td><span class="badge bg-info text-dark">{{ $opportunity->stage ?? '---' }}</span></td>
            </tr>
            <tr>
                <th>Importo Base</th>
                <td>€ {{ number_format($opportunity->amount ?? 0, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Valore Totale (con servizi)</th>
                <td class="fw-bold fs-6">€ {{ number_format($opportunity->total_value, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Data Chiusura Prevista</th>
                {{-- CORREZIONE: Aggiunto operatore nullsafe --}}
                <td>{{ $opportunity->closing_date?->format('d/m/Y') ?? '---' }}</td>
            </tr>
            <tr>
                <th>Azienda Collegata</th>
                <td>
                    @if($opportunity->company)
                        <a href="{{ route('companies.show', $opportunity->company->id) }}">{{ $opportunity->company->name }}</a>
                    @else
                        ---
                    @endif
                </td>
            </tr>
             <tr>
                <th>Contatto di Riferimento</th>
                <td>
                    @if($opportunity->contact)
                        <a href="{{ route('contacts.show', $opportunity->contact->id) }}">{{ $opportunity->contact->getFullNameAttribute() }}</a>
                    @else
                        ---
                    @endif
                </td>
            </tr>
            <tr>
                <th>Descrizione</th>
                <td>{{ $opportunity->description ?? '---' }}</td>
            </tr>
            <tr>
                <th>Data Creazione</th>
                {{-- CORREZIONE: Aggiunto operatore nullsafe --}}
                <td>{{ $opportunity->created_at?->format('d/m/Y H:i') ?? '---' }}</td>
            </tr>
            <tr>
                <th>Ultima Modifica</th>
                {{-- CORREZIONE: Aggiunto operatore nullsafe --}}
                <td>{{ $opportunity->updated_at?->format('d/m/Y H:i') ?? '---' }}</td>
            </tr>
        </tbody>
    </table>
</div>