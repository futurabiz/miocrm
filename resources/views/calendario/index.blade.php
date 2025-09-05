@extends('layouts.app')

@push('styles')
<style>
    #calendar {
        max-width: 1100px;
        margin: 40px auto;
    }
    
    /* --- NUOVI STILI PER IL FORM NEL POPUP --- */
    /* Contenitore principale del nostro form custom */
    .swal2-html-container {
        overflow: visible !important; /* Permette al dropdown di essere visibile */
    }
    /* Stile per ogni gruppo (etichetta + input) */
    .crm-form-group {
        margin-bottom: 1rem;
        text-align: left;
    }
    .crm-form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: bold;
        color: #333;
    }
    .crm-form-group input,
    .crm-form-group select {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #ddd;
        border-radius: 0.375rem;
        box-sizing: border-box; /* Previene problemi di larghezza */
        font-size: 1em;
    }

    /* Regola per la responsività del popup */
    @media (max-width: 768px) {
        .swal2-popup {
            width: 90% !important;
        }
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Calendario Attività</h2>
        </div>
        <p class="text-muted">Clicca su una data per creare un evento, trascina gli eventi per spostarli, clicca su un evento per scegliere se modificarlo o eliminarlo.</p>
    </div>
</div>

<div id="calendar"></div>
@endsection

@push('scripts')
<!-- Librerie Esterne -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    // Passiamo i dati dal controller a JavaScript in modo sicuro
    const CRM_DATA = {
        contacts: @json($contatti),
        companies: @json($aziende)
    };

    const CALENDAR_ROUTES = {
        fetch_events: '{{ url("/calendario/eventi") }}',
        store_activity: '{{ url("/calendario/attivita") }}',
        base_activity_url: '{{ url("/calendario/attivita") }}'
    };

    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function formatDateTimeLocal(date) {
            if (!date) return '';
            const localDate = new Date(date.getTime() - (date.getTimezoneOffset() * 60000));
            return localDate.toISOString().slice(0, 16);
        }

        // Funzione per creare il dropdown delle relazioni
        function createRelatedToDropdown(selectedValue = '') {
            let options = '<option value="">Nessuno</option>';
            options += '<optgroup label="Aziende">';
            CRM_DATA.companies.forEach(company => {
                const value = `company-${company.id}`;
                const selected = value === selectedValue ? 'selected' : '';
                options += `<option value="${value}" ${selected}>${company.name}</option>`;
            });
            options += '</optgroup>';
            options += '<optgroup label="Contatti">';
            CRM_DATA.contacts.forEach(contact => {
                const value = `contact-${contact.id}`;
                const selected = value === selectedValue ? 'selected' : '';
                options += `<option value="${value}" ${selected}>${contact.first_name} ${contact.last_name}</option>`;
            });
            options += '</optgroup>';
            return `<div class="crm-form-group"><label for="swal-related">Collega a</label><select id="swal-related">${options}</select></div>`;
        }


        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'it',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            editable: true,
            selectable: true,
            events: CALENDAR_ROUTES.fetch_events,
            eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },

            // --- CREAZIONE EVENTO ---
            select: async function(info) {
                const { value: formValues } = await Swal.fire({
                    title: 'Crea Nuova Attività',
                    html: `
                        <div class="crm-form-group"><label for="swal-title">Titolo</label><input id="swal-title" placeholder="Titolo dell'attività" required></div>
                        <div class="crm-form-group"><label for="swal-type">Tipo</label><select id="swal-type"><option value="task">Task</option><option value="meeting">Riunione</option><option value="call">Chiamata</option></select></div>
                        ${createRelatedToDropdown()}
                        <div class="crm-form-group"><label for="swal-start">Inizio</label><input type="datetime-local" id="swal-start" value="${formatDateTimeLocal(info.start)}"></div>
                        <div class="crm-form-group"><label for="swal-end">Fine</label><input type="datetime-local" id="swal-end" value="${formatDateTimeLocal(info.end)}"></div>
                    `,
                    focusConfirm: false,
                    preConfirm: () => {
                        const title = document.getElementById('swal-title').value;
                        if (!title) {
                            Swal.showValidationMessage('Il titolo è obbligatorio');
                            return false;
                        }
                        return {
                            title: title,
                            type: document.getElementById('swal-type').value,
                            start_time: document.getElementById('swal-start').value,
                            end_time: document.getElementById('swal-end').value,
                            related_to: document.getElementById('swal-related').value
                        }
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Salva',
                    cancelButtonText: 'Annulla'
                });

                if (formValues) {
                    try {
                        await axios.post(CALENDAR_ROUTES.store_activity, formValues, { headers: { 'X-CSRF-TOKEN': csrfToken } });
                        calendar.refetchEvents();
                        Swal.fire('Salvato!', 'La nuova attività è stata creata.', 'success');
                    } catch (error) {
                        console.error('Errore durante il salvataggio:', error);
                        Swal.fire('Errore', 'Impossibile salvare l\'attività.', 'error');
                    }
                }
            },

            // --- AGGIORNAMENTO EVENTO (DRAG & DROP) ---
            eventDrop: async function(info) {
                try {
                    const updateUrl = `${CALENDAR_ROUTES.base_activity_url}/${info.event.id}`;
                    await axios.put(updateUrl, {
                        title: info.event.title.split(' (')[0], // Rimuove il nome del contatto/azienda dal titolo
                        start_time: info.event.start.toISOString(),
                        end_time: info.event.end ? info.event.end.toISOString() : null,
                        related_to: `${info.event.extendedProps.activityable_type ? info.event.extendedProps.activityable_type.split('\\').pop().toLowerCase() : ''}-${info.event.extendedProps.activityable_id}`
                    }, { headers: { 'X-CSRF-TOKEN': csrfToken } });
                } catch (error) {
                    console.error('Errore durante l\'aggiornamento:', error);
                    Swal.fire('Errore', 'Impossibile aggiornare l\'attività.', 'error');
                    info.revert();
                }
            },
            
            // --- CLICK SU UN EVENTO (MODIFICA O ELIMINA) ---
            eventClick: async function(info) {
                const result = await Swal.fire({
                    title: info.event.title,
                    text: 'Cosa vuoi fare?',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Elimina',
                    confirmButtonColor: '#d33',
                    denyButtonText: `Modifica`,
                    cancelButtonText: 'Annulla'
                });

                if (result.isConfirmed) { // Elimina
                    try {
                        const deleteUrl = `${CALENDAR_ROUTES.base_activity_url}/${info.event.id}`;
                        await axios.delete(deleteUrl, { headers: { 'X-CSRF-TOKEN': csrfToken } });
                        info.event.remove();
                        Swal.fire('Eliminato!', 'L\'attività è stata cancellata.', 'success');
                    } catch (error) {
                        console.error('Errore durante l\'eliminazione:', error);
                        Swal.fire('Errore', 'Impossibile eliminare l\'attività.', 'error');
                    }
                } else if (result.isDenied) { // Modifica
                    const props = info.event.extendedProps;
                    const selectedValue = props.activityable_type ? `${props.activityable_type.split('\\').pop().toLowerCase()}-${props.activityable_id}` : '';
                    
                    const { value: formValues } = await Swal.fire({
                        title: 'Modifica Attività',
                        html: `
                            <div class="crm-form-group"><label for="swal-title">Titolo</label><input id="swal-title" value="${info.event.title.split(' (')[0]}"></div>
                            ${createRelatedToDropdown(selectedValue)}
                            <div class="crm-form-group"><label for="swal-start">Inizio</label><input type="datetime-local" id="swal-start" value="${formatDateTimeLocal(info.event.start)}"></div>
                            <div class="crm-form-group"><label for="swal-end">Fine</label><input type="datetime-local" id="swal-end" value="${formatDateTimeLocal(info.event.end)}"></div>
                        `,
                        focusConfirm: false,
                        preConfirm: () => ({
                            title: document.getElementById('swal-title').value,
                            start_time: document.getElementById('swal-start').value,
                            end_time: document.getElementById('swal-end').value,
                            related_to: document.getElementById('swal-related').value
                        }),
                        showCancelButton: true,
                        confirmButtonText: 'Salva Modifiche'
                    });

                    if(formValues) {
                        try {
                            const updateUrl = `${CALENDAR_ROUTES.base_activity_url}/${info.event.id}`;
                            await axios.put(updateUrl, formValues, { headers: { 'X-CSRF-TOKEN': csrfToken } });
                            calendar.refetchEvents();
                            Swal.fire('Aggiornato!', 'L\'attività è stata modificata.', 'success');
                        } catch (error) {
                            console.error('Errore durante la modifica:', error);
                            Swal.fire('Errore', 'Impossibile salvare le modifiche.', 'error');
                        }
                    }
                }
            }
        });

        calendar.render();
    });
</script>
@endpush
