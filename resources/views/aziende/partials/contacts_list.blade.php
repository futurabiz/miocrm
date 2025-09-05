<table class="table table-hover table-sm">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Ruolo</th>
            <th>Email</th>
            <th>Telefono</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @forelse($contacts as $contact)
            <tr>
                <td>{{ $contact->first_name }} {{ $contact->last_name }}</td>
                <td>{{ $contact->role }}</td>
                <td>{{ $contact->email }}</td>
                <td>{{ $contact->phone }}</td>
                <td class="text-end">
                    <a href="{{ route('contacts.show', $contact->id) }}" class="btn btn-info btn-sm">Visualizza</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">Nessun contatto collegato a questa azienda.</td>
            </tr>
        @endforelse
    </tbody>
</table>
{{-- CORREZIONE: Usiamo $company invece di $azienda --}}
<a href="{{ route('contacts.create', ['company_id' => $company->id]) }}" class="btn btn-primary btn-sm mt-2">
    <i class="bi bi-plus-circle"></i> Aggiungi Nuovo Contatto
</a>