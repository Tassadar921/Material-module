const priceTaxFreeInput = document.querySelector('#material_priceTaxFree');
const priceTaxIncludedInput = document.querySelector('#material_priceTaxIncluded');
const vatSelect = document.querySelector('#material_vat');

function recalculatePriceTTC() {
    const priceHT = parseFloat(priceTaxFreeInput.value.replace(',', '.')) || 0;
    const vatValue = parseFloat(vatSelect.options[vatSelect.selectedIndex].getAttribute('data-rate').replace(',', '.')) || 0;

    const priceTTC = priceHT * (1 + vatValue);
    priceTaxIncludedInput.value = priceTTC.toFixed(2).replace('.', ',');

    console.log(vatSelect.options[vatSelect.selectedIndex]);
}

priceTaxFreeInput.addEventListener('input', recalculatePriceTTC);
vatSelect.addEventListener('change', recalculatePriceTTC);
