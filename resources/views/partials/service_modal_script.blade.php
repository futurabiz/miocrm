<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Funzione per generare i campi del form ---
    function buildFields(fields, container, data = {}) {
        container.innerHTML = '';
        if (!fields || fields.length === 0) {
            container.innerHTML = '<p>Questo servizio non ha campi personalizzati.</p>';
            return;
        }

        fields.forEach(field => {
            let inputHtml = '';
            const fieldName = `custom_fields_data[${field.name}]`;
            const value = data[field.name] || '';
            let fieldGroupHtml = '';
            
            switch(field.type) {
                case 'textarea':
                    inputHtml = `<textarea name="${fieldName}" class="form-control">${value}</textarea>`;
                    fieldGroupHtml = `<div class="mb-3"><label class="form-label">${field.label}</label>${inputHtml}</div>`;
                    break;
                case 'options':
                    const options = field.options ? field.options.split(',').map(opt => opt.trim()) : [];
                    let optionsHtml = '<option value="">Scegli...</option>';
                    options.forEach(opt => {
                        const selected = opt == value ? 'selected' : '';
                        optionsHtml += `<option value="${opt}" ${selected}>${opt}</option>`;
                    });
                    inputHtml = `<select name="${fieldName}" class="form-select">${optionsHtml}</select>`;
                    fieldGroupHtml = `<div class="mb-3"><label class="form-label">${field.label}</label>${inputHtml}</div>`;
                    break;
                case 'checkbox':
                    const checked = value == 1 ? 'checked' : '';
                    inputHtml = `<div class="form-check"><input type="hidden" name="${fieldName}" value="0"><input class="form-check-input" type="checkbox" name="${fieldName}" value="1" id="${fieldName}_${Math.random()}" ${checked}><label class="form-check-label" for="${fieldName}_${Math.random()}">${field.label}</label></div>`;
                    fieldGroupHtml = inputHtml;
                    break;
                default:
                    inputHtml = `<input type="${field.type}" name="${fieldName}" class="form-control" value="${value}">`;
                    fieldGroupHtml = `<div class="mb-3"><label class="form-label">${field.label}</label>${inputHtml}</div>`;
                    break;
            }
            container.insertAdjacentHTML('beforeend', fieldGroupHtml);
        });
    }

    // --- Logica per il MODAL DI AGGIUNTA ---
    const addServiceSelector = document.getElementById('add_service_type_selector');
    const addFieldsContainer = document.getElementById('add-custom-fields-container');
    if(addServiceSelector) {
        addServiceSelector.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (!selectedOption.value) {
                addFieldsContainer.innerHTML = '';
                return;
            }
            const fields = JSON.parse(selectedOption.getAttribute('data-fields') || '[]');
            buildFields(fields, addFieldsContainer);
        });
    }

    // --- Logica per il MODAL DI MODIFICA ---
    const editServiceModalElement = document.getElementById('editServiceModal');
    if (editServiceModalElement) {
        const editServiceModal = new bootstrap.Modal(editServiceModalElement);
        const editForm = document.getElementById('editServiceForm');
        const editFieldsContainer = document.getElementById('edit-custom-fields-container');
        const allServiceTypes = @json($serviceTypes ?? []);

        document.querySelectorAll('.edit-service-btn').forEach(button => {
            button.addEventListener('click', function() {
                const serviceTypeId = this.dataset.serviceTypeId;
                const serviceData = JSON.parse(this.dataset.serviceData || '{}');
                const updateUrl = this.dataset.updateUrl;

                const serviceType = allServiceTypes.find(st => st.id == serviceTypeId);
                if (!serviceType) return;

                editForm.action = updateUrl;
                buildFields(serviceType.fields_schema, editFieldsContainer, serviceData);
                editServiceModal.show();
            });
        });
    }
});
</script>
