@extends('layouts.layout')

@section('title', 'Product Management')

@section('content')
    <h1>Product Management</h1>
    <form id="product-form" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="product_name" id="product_name" class="form-control" placeholder="Product Name"
                    required>
            </div>
            <div class="col-md-3">
                <input type="number" name="quantity" id="quantity" class="form-control" placeholder="Quantity" required>
            </div>
            <div class="col-md-3">
                <input type="number" step="0.01" name="price" id="price" class="form-control" placeholder="Price"
                    required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Datetime</th>
                <th>Total Value</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="product-table-body">
            <!-- Populated via Ajax -->
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-end"><strong>Total:</strong></td>
                <td id="sum-total-value"></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
@endsection

@push('scripts')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        const products = @json([]);
        $('#product-form').submit(function(e) {
            e.preventDefault();
            const formData = $(this).serialize();

            $.post('/products', formData, function(response) {
                if (response.success) {
                    console.log('Product added');
                }
            }).fail(function(xhr) {
                console.error('Error:', xhr.responseText);
            });
        });

        function editItem(index) {
            $.get(`/products/${index}`, function(response) {
                const item = response.item;
                console.log('Editing:', item);
            }).fail(function(xhr) {
                console.error('Error:', xhr.responseText);
            });
        }
    </script>
@endpush
