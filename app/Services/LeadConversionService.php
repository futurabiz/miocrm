<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\Contact;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

class LeadConversionService
{
    public function convert(Lead $lead): Contact
    {
        if ($lead->status === 'Convertito') {
            throw new \Exception('Questo lead è già stato convertito.');
        }

        return DB::transaction(function () use ($lead) {
            // 1. Crea il Contatto copiando tutti i dati condivisi
            $contact = Contact::create([
                'first_name' => $lead->first_name,
                'last_name'  => $lead->last_name,
                'salutation' => $lead->salutation,
                'codice_fiscale' => $lead->codice_fiscale,
                'email'      => $lead->email,
                'phone'      => $lead->phone,
                'mobile_phone' => $lead->mobile_phone,
                'role'       => $lead->role,
                'source'     => $lead->source,
                'description' => $lead->description,
                'assigned_to_id' => $lead->assigned_to_id,
            ]);

            // 2. Crea o trova l'Azienda e la collega
            // NOTA: Il campo 'company_id' del Lead non viene usato qui. Il collegamento viene fatto
            // tramite il nome dell'azienda se presente, e poi l'ID viene settato sul contatto.
            if ($lead->company_id) { // Assumendo che il lead possa essere collegato a un'azienda esistente
                 $contact->company()->associate($lead->company_id)->save();
            }

            // 3. Sposta le relazioni
            $lead->notes()->update(['notable_type' => Contact::class, 'notable_id' => $contact->id]);
            $lead->activities()->update(['activityable_type' => Contact::class, 'activityable_id' => $contact->id]);

            // 4. Aggiorna lo stato del Lead
            $lead->status = 'Convertito';
            $lead->save();

            return $contact;
        });
    }
}