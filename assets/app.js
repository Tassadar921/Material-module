import './app.css';
import $ from 'jquery';
import 'datatables.net-bs5';

$(document).ready(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const paramValue = urlParams.get('outOfStock');

    $('#materialTable').DataTable({
        serverSide: true,
        ajax: {
            url: `/material/data?outOfStock=${paramValue === 'true'}`,
            type: 'POST'
        },
        columns: [
            {data: 'name'},
            {data: 'priceTaxFree'},
            {data: 'priceTaxIncluded'},
            {data: 'quantity'},
            {
                data: 'id',
                render: (data, type, row) => {
                    return `
                        <a href="/material/${data}/increment">Incrémenter</a>
                        <a href="/material/${data}/decrement">Décrémenter</a>
                        <button class="view-btn" data-id="${data}" data-name="${row.name}" data-priceht="${row.priceTaxFree}" data-pricettc="${row.priceTaxIncluded}" data-quantity="${row.quantity}">Voir</button>
                        <a href="/material/${data}/edit">Modifier</a>
                        <a href="/material/${data}/pdf">PDF</a>
                    `;
                },
                orderable: false,
                searchable: false
            }
        ]
    });

    $('#materialTable tbody').on('click', '.view-btn', function () {
        const materialId = $(this).data('id');
        const materialName = $(this).data('name');
        const materialPriceHT = $(this).data('priceht');
        const materialPriceTTC = $(this).data('pricettc');
        const materialQuantity = $(this).data('quantity');

        $('#materialId').text(materialId);
        $('#materialName').text(materialName);
        $('#materialPriceHT').text(materialPriceHT);
        $('#materialPriceTTC').text(materialPriceTTC);
        $('#materialQuantity').text(materialQuantity);

        $('#materialModal').removeClass('hidden').css('display', 'flex');
    });

    $('#closeModal, #closeButton').on('click', function () {
        $('#materialModal').addClass('hidden').css('display', 'none');
    });
});