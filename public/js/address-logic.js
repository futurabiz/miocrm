// public/js/address-logic.js (Versione Finale e Generica)

document.addEventListener('DOMContentLoaded', function () {
    // Cerca il form specifico che contiene i campi indirizzo
    const addressForm = document.querySelector('form[data-address-form="true"]');

    // Se non c'è un form con quell'attributo, interrompe lo script
    if (!addressForm) {
        return;
    }

    // Leggi i dati iniziali dal dataset del form (per la modalità modifica)
    const initialData = {
        regionId: addressForm.dataset.regionId,
        provinceId: addressForm.dataset.provinceId,
        cityId: addressForm.dataset.cityId,
        postalCodeId: addressForm.dataset.postalCodeId,
    };

    const regionSelect = addressForm.querySelector('.address-region');
    const provinceSelect = addressForm.querySelector('.address-province');
    const citySelect = addressForm.querySelector('.address-city');
    const postalCodeSelect = addressForm.querySelector('#address_postal_code');
    const postalCodeContainer = addressForm.querySelector('#postal-code-container');

    if (!regionSelect || !provinceSelect || !citySelect || !postalCodeSelect) {
        return;
    }

    function populateSelect(selectElement, items, placeholder, selectedValue = null) {
        selectElement.innerHTML = `<option value="">-- ${placeholder} --</option>`;
        items.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.text;
            if (item.id == selectedValue) {
                option.selected = true;
            }
            selectElement.appendChild(option);
        });
        selectElement.disabled = false;
    }
    
    function triggerChange(element) {
        element.dispatchEvent(new Event('change'));
    }

    // --- LOGICA DI CARICAMENTO E PRESELEZIONE ---

    fetch('/api/locations/regions')
        .then(response => response.json())
        .then(data => {
            populateSelect(regionSelect, data.results, 'Seleziona Regione', initialData.regionId);
            if (initialData.regionId) {
                triggerChange(regionSelect);
            }
        });

    regionSelect.addEventListener('change', function () {
        const regionId = this.value;
        provinceSelect.disabled = true;
        citySelect.disabled = true;
        postalCodeContainer.style.display = 'none';

        if (!regionId) return;

        fetch(`/api/locations/provinces/${regionId}`)
            .then(response => response.json())
            .then(data => {
                populateSelect(provinceSelect, data.results, 'Seleziona Provincia', initialData.provinceId);
                if (initialData.provinceId) {
                    triggerChange(provinceSelect);
                }
            });
    });

    provinceSelect.addEventListener('change', function () {
        const provinceId = this.value;
        citySelect.disabled = true;
        postalCodeContainer.style.display = 'none';

        if (!provinceId) return;

        fetch(`/api/locations/cities/${provinceId}`)
            .then(response => response.json())
            .then(data => {
                populateSelect(citySelect, data.results, 'Seleziona Comune', initialData.cityId);
                if (initialData.cityId) {
                    triggerChange(citySelect);
                }
            });
    });

    citySelect.addEventListener('change', function() {
        const cityId = this.value;
        postalCodeSelect.disabled = true;
        
        if (!cityId) {
            postalCodeContainer.style.display = 'none';
            return;
        }

        fetch(`/api/locations/city/${cityId}`)
            .then(response => response.json())
            .then(cityData => {
                if (cityData && cityData.postal_codes && cityData.postal_codes.length > 0) {
                    const postalCodeItems = cityData.postal_codes.map(pc => ({ id: pc.id, text: pc.code }));
                    populateSelect(postalCodeSelect, postalCodeItems, 'Seleziona CAP', initialData.postalCodeId);
                    postalCodeContainer.style.display = 'block';
                } else {
                    postalCodeContainer.style.display = 'none';
                }
            });
    });
});