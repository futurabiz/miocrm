<?php

return [
    'available_models' => [
        'App\Models\Lead' => [
            'label' => 'Lead',
            'fields' => [
                'name' => ['label' => 'Nome', 'type' => 'text_searchable'],
                'email' => ['label' => 'Email', 'type' => 'text_searchable'],
                'phone' => ['label' => 'Telefono', 'type' => 'text_searchable'],
                'company_id' => ['label' => 'Azienda', 'type' => 'select_ajax_company'],
                'source' => ['label' => 'Fonte Lead', 'type' => 'options'],
                'status' => ['label' => 'Status', 'type' => 'text_searchable'],
            ],
            'shortcodes' => [
                '{{lead.name}}' => 'Nome del Lead',
                '{{lead.email}}' => 'Email del Lead',
                '{{company.name}}' => 'Nome Azienda Associata',
            ]
        ],
        'App\Models\Contact' => [
            'label' => 'Contatti',
            'fields' => [
                'first_name' => ['label' => 'Nome', 'type' => 'text_searchable'],
                'last_name' => ['label' => 'Cognome', 'type' => 'text_searchable'],
                'email' => ['label' => 'Email', 'type' => 'text_searchable'],
                'phone' => ['label' => 'Telefono', 'type' => 'text_searchable'],
                'source' => ['label' => 'Fonte Lead', 'type' => 'options'],
            ],
            'shortcodes' => [
                '{{contact.first_name}}' => 'Nome del Contatto',
                '{{contact.last_name}}' => 'Cognome del Contatto',
                '{{contact.email}}' => 'Email del Contatto',
                '{{company.name}}' => 'Nome Azienda Associata',
            ]
        ],
        'App\Models\Company' => [
            'label' => 'Aziende',
            'fields' => [
                'name' => ['label' => 'Nome Azienda', 'type' => 'text_searchable'],
                'email' => ['label' => 'Email', 'type' => 'text_searchable'],
                'phone' => ['label' => 'Telefono', 'type' => 'text_searchable'],
            ],
             'shortcodes' => [
                '{{company.name}}' => 'Nome Azienda',
                '{{company.email}}' => 'Email Azienda',
            ]
        ],
        'App\Models\Opportunity' => [
            'label' => 'Opportunità',
            'fields' => [
                'name' => ['label' => 'Nome Opportunità', 'type' => 'text_searchable'],
                'amount' => ['label' => 'Importo', 'type' => 'number_searchable'],
                'stage' => ['label' => 'Fase', 'type' => 'text_searchable'],
                'source' => ['label' => 'Fonte Lead', 'type' => 'options'],
            ],
             'shortcodes' => [
                '{{opportunity.name}}' => 'Nome Opportunità',
                '{{opportunity.amount}}' => 'Importo Opportunità',
                '{{contact.first_name}}' => 'Nome Contatto Associato',
            ]
        ],
    ],

    'field_options' => [
        'App\Models\Lead' => [ 'source' => ['Web', 'Fiera', 'Referral', 'Telefono'], ],
        'App\Models\Contact' => [ 'source' => ['Web', 'Fiera', 'Referral', 'Telefono'], ],
        'App\Models\Opportunity' => [ 'source' => ['Web', 'Fiera', 'Referral', 'Telefono'], ],
    ],

    'available_actions' => [
        'send_email' => [
            'label' => 'Invia Email da Template',
            'description' => 'Invia un\'email automatica usando un template',
            'parameters' => [
                'template_id' => ['type' => 'select', 'label' => 'Template Email', 'required' => true],
                'to' => ['type' => 'email', 'label' => 'A (opzionale, usa l\'email del record se vuoto)', 'required' => false],
                'subject' => ['type' => 'text', 'label' => 'Oggetto', 'required' => true],
                'body' => ['type' => 'editor', 'label' => 'Messaggio', 'required' => true],
            ]
        ],
        'update_field' => [
            'label' => 'Aggiorna Campo',
            'description' => 'Modifica un campo del record',
            'parameters' => [
                'field' => ['type' => 'select', 'label' => 'Campo da aggiornare', 'required' => true],
                'value' => ['type' => 'text', 'label' => 'Nuovo Valore', 'required' => true],
            ]
        ],
        'create_task' => [
            'label' => 'Crea una nuova Attività',
            'description' => 'Crea una nuova attività (task) associata',
            'parameters' => [
                'title' => ['type' => 'text', 'label' => 'Titolo', 'required' => true],
                'description' => ['type' => 'textarea', 'label' => 'Descrizione', 'required' => false],
                'due_date' => ['type' => 'date', 'label' => 'Scadenza', 'required' => false],
                'priority' => ['type' => 'select', 'label' => 'Priorità', 'options' => ['Bassa' => 'Bassa', 'Media' => 'Media', 'Alta' => 'Alta'], 'required' => true],
            ]
        ],
    ],
];
