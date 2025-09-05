<div class="table-responsive">
    <table class="table table-bordered table-striped" style="font-size: 0.9rem;">
        <tbody>
            <tr>
                <th style="width: 25%;">Nome Completo</th>
                <td>{{ $contact->first_name }} {{ $contact->last_name }}</td>
            </tr>
            <tr>
                <th>Azienda</th>
                <td>
                    @if($contact->company)
                        <a href="{{ route('companies.show', $contact->company->id) }}">{{ $contact->company->name }}</a>
                    @else
                        N/D
                    @endif
                </td>
            </tr>
            <tr>
                <th>Ruolo</th>
                <td>{{ $contact->role ?? 'N/D' }}</td>
            </tr>
             <tr>
                <th>Email</th>
                <td>
                    @if($contact->email)
                        <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                    @else
                        N/D
                    @endif
                </td>
            </tr>
            <tr>
                <th>Telefono Fisso</th>
                <td>{{ $contact->phone ?? 'N/D' }}</td>
            </tr>
            <tr>
                <th>Cellulare</th>
                <td>{{ $contact->mobile_phone ?? 'N/D' }}</td>
            </tr>
            <tr>
                <th>Assegnato a</th>
                <td>{{ $contact->assignedTo?->name ?? 'N/D' }}</td>
            </tr>
            <tr>
                <th>Data Creazione</th>
                {{-- CORREZIONE: Aggiunto operatore nullsafe per prevenire errori su date nulle --}}
                <td>{{ $contact->created_at?->format('d/m/Y H:i') ?? 'N/D' }}</td>
            </tr>
            <tr>
                <th>Ultima Modifica</th>
                {{-- CORREZIONE: Aggiunto operatore nullsafe per prevenire errori su date nulle --}}
                <td>{{ $contact->updated_at?->format('d/m/Y H:i') ?? 'N/D' }}</td>
            </tr>
        </tbody>
    </table>
</div>