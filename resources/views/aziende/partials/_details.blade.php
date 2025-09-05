<div class="table-responsive">
    <table class="table table-bordered table-striped" style="font-size: 0.9rem;">
        <tbody>
            <tr>
                <th style="width: 25%;">Nome Azienda</th>
                <td>{{ $company->name }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>
                    @if($company->email)
                        <a href="mailto:{{ $company->email }}">{{ $company->email }}</a>
                    @else
                        N/D
                    @endif
                </td>
            </tr>
            <tr>
                <th>Telefono</th>
                <td>{{ $company->phone ?? 'N/D' }}</td>
            </tr>
            <tr>
                <th>Partita IVA</th>
                <td>{{ $company->vat_number ?? 'N/D' }}</td>
            </tr>
            <tr>
                <th>Codice Fiscale</th>
                <td>{{ $company->company_tax_code ?? 'N/D' }}</td>
            </tr>
            <tr>
                <th>Settore</th>
                <td>{{ $company->industry ?? 'N/D' }}</td>
            </tr>
            <tr>
                <th>Contatto Principale</th>
                <td>
                    @if($company->mainContact)
                        <a href="{{ route('contacts.show', $company->mainContact->id) }}">{{ $company->mainContact->full_name }}</a>
                    @else
                        N/D
                    @endif
                </td>
            </tr>
            <tr>
                <th>Assegnato a</th>
                <td>{{ $company->assignedTo?->name ?? 'N/D' }}</td>
            </tr>
            <tr>
                <th>Data Creazione</th>
                {{-- CORREZIONE: Aggiunto operatore nullsafe --}}
                <td>{{ $company->created_at?->format('d/m/Y H:i') ?? 'N/D' }}</td>
            </tr>
            <tr>
                <th>Ultima Modifica</th>
                {{-- CORREZIONE: Aggiunto operatore nullsafe --}}
                <td>{{ $company->updated_at?->format('d/m/Y H:i') ?? 'N/D' }}</td>
            </tr>
        </tbody>
    </table>
</div>