<div class="table-responsive">
    <table class="table table-bordered table-striped" style="font-size: 0.9rem;">
        <tbody>
            <tr>
                <th style="width: 25%;">Nome Completo</th>
                <td>{{ $lead->first_name }} {{ $lead->last_name }}</td>
            </tr>
            <tr>
                <th>Azienda</th>
                <td>{{ optional($lead->company)->name ?? 'N/D' }}</td>
            </tr>
            <tr>
                <th>Stato</th>
                <td><span class="badge bg-info text-dark">{{ $lead->status ?? 'N/D' }}</span></td>
            </tr>
            <tr>
                <th>Fonte Lead</th>
                <td>{{ $lead->source ?? 'N/D' }}</td>
            </tr>
            <tr>
                <th>Ruolo</th>
                <td>{{ $lead->role ?? 'N/D' }}</td>
            </tr>
             <tr>
                <th>Email</th>
                {{-- CORREZIONE: Si usa il tag HTML standard <a> per creare il link mailto --}}
                <td>
                    @if($lead->email)
                        <a href="mailto:{{ $lead->email }}">{{ $lead->email }}</a>
                    @else
                        N/D
                    @endif
                </td>
            </tr>
            <tr>
                <th>Telefono Fisso</th>
                <td>{{ $lead->phone ?? 'N/D' }}</td>
            </tr>
            <tr>
                <th>Cellulare</th>
                <td>{{ $lead->mobile_phone ?? 'N/D' }}</td>
            </tr>
            <tr>
                <th>Assegnato a</th>
                <td>{{ optional($lead->assignedTo)->name ?? 'N/D' }}</td>
            </tr>
            <tr>
                <th>Descrizione</th>
                <td>{{ $lead->description ?? 'Nessuna descrizione.' }}</td>
            </tr>
            <tr>
                <th>Data Creazione</th>
                <td>{{ $lead->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <th>Ultima Modifica</th>
                <td>{{ $lead->updated_at->format('d/m/Y H:i') }}</td>
            </tr>
        </tbody>
    </table>
</div>