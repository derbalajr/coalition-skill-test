$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function initializeTable(products) {
    renderTable(products);

    // Attach a one-time submit event handler for the form
    $('#product-form').off('submit').on('submit', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.post('/products', formData, function (response) {
            if (response.success) {
                renderTable(response.data);

                // Clear the form inputs after submission
                $('#product-form')[0].reset();
            }
        }).fail(function (xhr) {
            console.error('Error:', xhr.responseText);
        });
    });
}

function renderTable(data) {
    let rows = '';
    let totalSum = 0;

    data.forEach((item, index) => {
        rows += `
            <tr>
                <td>${item.product_name}</td>
                <td>${item.quantity}</td>
                <td>${item.price.toFixed(2)}</td>
                <td>${item.datetime}</td>
                <td>${item.total_value.toFixed(2)}</td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="editItem(${index})">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteItem(${index})">Delete</button>
                </td>
            </tr>
        `;
        totalSum += item.total_value;
    });

    $('#product-table-body').html(rows);
    $('#sum-total-value').text(totalSum.toFixed(2));
}

function deleteItem(index) {
    if (confirm('Are you sure you want to delete this item?')) {
        $.ajax({
            url: `/products/${index}`,
            type: 'DELETE',
            success: function (response) {
                if (response.success) {
                    renderTable(response.data);
                }
            },
            error: function (xhr) {
                console.error('Error:', xhr.responseText);
            }
        });
    }
}

function editItem(index) {
    $.get(`/products/${index}`, function (response) {
        const item = response.item;

        $('#product_name').val(item.product_name);
        $('#quantity').val(item.quantity);
        $('#price').val(item.price);

        // Attach a one-time submit handler for editing
        $('#product-form').off('submit').on('submit', function (e) {
            e.preventDefault();
            const formData = $(this).serialize();

            $.ajax({
                url: `/products/${index}`,
                type: 'PUT',
                data: formData,
                success: function (response) {
                    if (response.success) {
                        renderTable(response.data);
                        $('#product-form')[0].reset();

                        initializeTable(response.data);
                    }
                },
                error: function (xhr) {
                    console.error('Error:', xhr.responseText);
                }
            });
        });
    }).fail(function (xhr) {
        console.error('Error:', xhr.responseText);
    });
}
