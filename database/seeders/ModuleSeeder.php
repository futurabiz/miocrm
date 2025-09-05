<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ModuleBlock;
use App\Models\ModuleField;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Avvio seeder definitivo per la struttura dei moduli...');
        
        $this->command->info('1. Cancellazione della vecchia struttura dei moduli...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ModuleField::truncate();
        ModuleBlock::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info('==> Vecchia struttura eliminata.');

        $this->command->info('2. Creazione della nuova struttura ordinata...');
        
        $modules = [
            // ==================== LEAD ====================
            \App\Models\Lead::class => [
                'Informazioni Principali' => [
                    ['name' => 'first_name', 'label' => 'Nome', 'type' => 'text', 'is_required' => true],
                    ['name' => 'last_name', 'label' => 'Cognome', 'type' => 'text', 'is_required' => true],
                    ['name' => 'company_id', 'label' => 'Azienda Collegata', 'type' => 'related_company'],
                    ['name' => 'assigned_to_id', 'label' => 'Assegnato a', 'type' => 'related_user'],
                ],
                'Dettagli Contatto' => [
                    ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
                    ['name' => 'phone', 'label' => 'Telefono Fisso', 'type' => 'tel'],
                    ['name' => 'mobile_phone', 'label' => 'Cellulare', 'type' => 'tel'],
                ],
                'Stato e Fonte' => [
                    ['name' => 'status', 'label' => 'Stato', 'type' => 'select', 'is_required' => true, 'options' => json_encode(['Nuovo' => 'Nuovo', 'Contattato' => 'Contattato', 'In Lavorazione' => 'In Lavorazione', 'Qualificato' => 'Qualificato', 'Non Qualificato' => 'Non Qualificato'])],
                    ['name' => 'source', 'label' => 'Fonte', 'type' => 'select', 'options' => json_encode(['Web' => 'Web', 'Fiera' => 'Fiera', 'Referral' => 'Referral', 'Telefono' => 'Telefono'])],
                ],
                'Dati Anagrafici' => [
                    ['name' => 'gender', 'label' => 'Genere', 'type' => 'select', 'options' => json_encode(['M' => 'Maschio', 'F' => 'Femmina'])],
                    ['name' => 'birthdate', 'label' => 'Data di Nascita', 'type' => 'date'],
                    ['name' => 'city_code', 'label' => 'Comune di Nascita', 'type' => 'related_city'],
                    ['name' => 'codice_fiscale', 'label' => 'Codice Fiscale', 'type' => 'text'],
                ],
                // Manteniamo il blocco unificato per coerenza
                'Indirizzo e Località' => [
                    ['name' => 'address_street', 'label' => 'Indirizzo', 'type' => 'text'],
                    ['name' => 'address_street_number', 'label' => 'Numero Civico', 'type' => 'text'],
                ],
                'Note' => [
                    ['name' => 'description', 'label' => 'Note', 'type' => 'textarea'],
                ],
            ],
            
            // ==================== CONTATTI (versione corretta) ====================
            \App\Models\Contact::class => [
                'Informazioni Principali' => [
                    ['name' => 'salutation', 'label' => 'Saluto', 'type' => 'select', 'options' => json_encode(['Sig.' => 'Sig.', 'Sig.ra' => 'Sig.ra', 'Dott.' => 'Dott.', 'Dott.ssa' => 'Dott.ssa'])],
                    ['name' => 'first_name', 'label' => 'Nome', 'type' => 'text', 'is_required' => true],
                    ['name' => 'last_name', 'label' => 'Cognome', 'type' => 'text', 'is_required' => true],
                    ['name' => 'company_id', 'label' => 'Azienda', 'type' => 'related_company'],
                    ['name' => 'role', 'label' => 'Ruolo', 'type' => 'select', 'options' => json_encode(['Decision maker' => 'Decision maker', 'Influencer' => 'Influencer', 'Operativo' => 'Operativo', 'Altro' => 'Altro'])],
                    ['name' => 'assigned_to_id', 'label' => 'Assegnato a', 'type' => 'related_user'],
                ],
                'Dettagli Contatto' => [
                    ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'is_required' => true],
                    ['name' => 'phone', 'label' => 'Telefono', 'type' => 'tel'],
                    ['name' => 'mobile_phone', 'label' => 'Cellulare', 'type' => 'tel'],
                ],
                'Dati Anagrafici' => [
                    ['name' => 'gender', 'label' => 'Genere', 'type' => 'select', 'options' => json_encode(['M' => 'Maschio', 'F' => 'Femmina'])],
                    ['name' => 'birthdate', 'label' => 'Data di Nascita', 'type' => 'date'],
                    ['name' => 'city_code', 'label' => 'Comune di Nascita', 'type' => 'related_city'],
                    ['name' => 'codice_fiscale', 'label' => 'Codice Fiscale', 'type' => 'text'],
                ],
                'Indirizzo e Località' => [
                    ['name' => 'address_street', 'label' => 'Indirizzo', 'type' => 'text'],
                    ['name' => 'address_street_number', 'label' => 'Numero Civico', 'type' => 'text'],
                ],
                'Note' => [
                     ['name' => 'description', 'label' => 'Note', 'type' => 'textarea'],
                ]
            ],

            // ==================== AZIENDE ====================
            \App\Models\Company::class => [
                'Informazioni Principali' => [
                    ['name' => 'name', 'label' => 'Nome Azienda', 'type' => 'text', 'is_required' => true],
                    ['name' => 'assigned_to_id', 'label' => 'Assegnato a', 'type' => 'related_user'],
                    ['name' => 'main_contact_id', 'label' => 'Contatto Principale', 'type' => 'related_contact'],
                ],
                'Dettagli Contatto' => [
                    ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
                    ['name' => 'phone', 'label' => 'Telefono', 'type' => 'tel'],
                    ['name' => 'pec_address', 'label' => 'Indirizzo PEC', 'type' => 'email'],
                    ['name' => 'website', 'label' => 'Sito Web', 'type' => 'text'],
                ],
                'Dati Fiscali e Settore' => [
                    ['name' => 'vat_number', 'label' => 'Partita IVA', 'type' => 'text'],
                    ['name' => 'company_tax_code', 'label' => 'Codice Fiscale Azienda', 'type' => 'text'],
                    ['name' => 'sdi_code', 'label' => 'Codice SDI', 'type' => 'text'],
                    ['name' => 'legal_form', 'label' => 'Forma Giuridica', 'type' => 'text'],
                    ['name' => 'industry', 'label' => 'Settore', 'type' => 'select', 'options' => json_encode(['Tecnologia' => 'Tecnologia', 'Servizi' => 'Servizi', 'Manifatturiero' => 'Manifatturiero'])],
                    ['name' => 'number_of_employees', 'label' => 'Numero Dipendenti', 'type' => 'text'],
                ],
                'Indirizzo e Località' => [ // Segnaposto per partial Blade
                    ['name' => 'address_street', 'label' => 'Indirizzo', 'type' => 'text'],
                    ['name' => 'address_street_number', 'label' => 'Numero Civico', 'type' => 'text'],
                ],
                'Note' => [
                    ['name' => 'description', 'label' => 'Note', 'type' => 'textarea'],
                ]
            ],

            // ==================== OPPORTUNITÀ ====================
            \App\Models\Opportunity::class => [
                'Informazioni Principali' => [
                    ['name' => 'name', 'label' => 'Nome Opportunità', 'type' => 'text', 'is_required' => true],
                    ['name' => 'company_id', 'label' => 'Azienda', 'type' => 'related_company', 'is_required' => true],
                    ['name' => 'contact_id', 'label' => 'Contatto di Riferimento', 'type' => 'related_contact'],
                    ['name' => 'assigned_to_id', 'label' => 'Assegnato a', 'type' => 'related_user'],
                ],
                'Dettagli Monetari e Temporali' => [
                    ['name' => 'stage', 'label' => 'Fase', 'type' => 'select', 'is_required' => true, 'options' => json_encode(['Qualificazione' => 'Qualificazione', 'Proposta' => 'Proposta', 'Negoziazione' => 'Negoziazione', 'Chiusa Vinta' => 'Chiusa Vinta', 'Chiusa Persa' => 'Chiusa Persa'])],
                    ['name' => 'amount', 'label' => 'Importo Previsto (€)', 'type' => 'number'],
                    ['name' => 'closing_date', 'label' => 'Data di Chiusura Prevista', 'type' => 'date'],
                ],
                'Note' => [
                    ['name' => 'description', 'label' => 'Note', 'type' => 'textarea'],
                ]
            ],
        ];

        DB::transaction(function () use ($modules) {
            foreach ($modules as $modelClass => $blocks) {
                $blockOrder = 1;
                foreach ($blocks as $blockName => $fields) {
                    $block = ModuleBlock::create([
                        'module_class' => $modelClass,
                        'name' => $blockName,
                        'order' => $blockOrder++,
                    ]);
                    
                    if (!empty($fields)) {
                        $fieldOrder = 1;
                        foreach ($fields as $fieldData) {
                            ModuleField::create(array_merge($fieldData, [
                                'module_block_id' => $block->id,
                                'is_standard' => true,
                                'is_visible' => true,
                                'order' => $fieldData['order'] ?? $fieldOrder++,
                                'is_required' => $fieldData['is_required'] ?? false,
                            ]));
                        }
                    }
                }
            }
        });
        
        $this->command->info('==> Nuova struttura creata con successo.');
    }
}