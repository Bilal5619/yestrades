@extends('admin.layout')

@section('content')
<div class="page-header">
    <h4 class="page-title">{{ __('Postal Codes') }}</h4>
    <ul class="breadcrumbs">
        <li class="nav-home">
            <a href="{{ route('admin.dashboard') }}">
                <i class="flaticon-home"></i>
            </a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="#">{{ __('Service Managment') }}</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="#">{{ __('Postal Codes') }}</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                <div class="card-title mb-0">{{ __('Postal Codes') }}</div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addPostalCodeModal">
                        <i class="fas fa-plus"></i> {{ __('Add Postal Code') }}
                    </button>
                    <button class="btn btn-danger btn-sm d-none bulk-delete"
                        data-href="{{ route('admin.postal_codes.bulk_delete') }}">
                        <i class="flaticon-interface-5"></i> {{ __('Delete') }}
                    </button>
                </div>
            </div>

            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-lg-12">
                        <div class="card card-secondary">
                            <div class="card-body">
                                <form id="defaultPostalPriceForm">
                                    @csrf
                                    <div class="row align-items-end">
                                        <div class="col-md-4">
                                            <div class="form-group mb-0">
                                                <label for="default_price">{{ __('Default Postal Surcharge') }}</label>
                                                <input type="number" step="0.01" class="form-control" id="default_price"
                                                    name="default_price"
                                                    value="{{ old('default_price', $defaultPostalPrice) }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="submit"
                                                class="btn btn-success w-100">{{ __('Save Default Price') }}</button>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="text-muted mb-0">
                                                {{ __('Fallback extra charge used by older booking flows when no defined entry matches.') }}
                                            </p>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        @if (count($postalCodes) == 0)
                            <h3 class="text-center mt-3">{{ __('NO POSTAL CODE FOUND') . '!' }}</h3>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped mt-2">
                                    <thead>
                                        <tr>
                                            <th scope="col">
                                                <input type="checkbox" class="bulk-check" data-val="all">
                                            </th>
                                            <th scope="col">{{ __('Postcode Area') }}</th>
                                            <th scope="col">{{ __('Extra Charge') }}</th>
                                            <th scope="col">{{ __('Applies To') }}</th>
                                            <th scope="col">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($postalCodes as $postalCode)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="bulk-check" data-val="{{ $postalCode->id }}">
                                                </td>
                                                <td>{{ $postalCode->postal_code }}</td>
                                                <td>{{ symbolPrice($postalCode->price) }}</td>
                                                <td>
                                                    @if ($postalCode->service_package_id)
                                                        {{ __('Package') }}:
                                                        {{ optional($packages->firstWhere('id', $postalCode->service_package_id))->title ?? '#' . $postalCode->service_package_id }}
                                                    @elseif ($postalCode->service_id)
                                                        {{ __('Service') }}:
                                                        {{ optional($services->firstWhere('service_id', $postalCode->service_id))->name ?? '#' . $postalCode->service_id }}
                                                    @elseif ($postalCode->service_category_id)
                                                        {{ __('Category') }}:
                                                        {{ optional($categories->firstWhere('id', $postalCode->service_category_id))->name ?? '#' . $postalCode->service_category_id }}
                                                    @else
                                                        {{ __('All services/packages') }}
                                                    @endif
                                                </td>
                                                <td>
                                                    <button class="btn btn-secondary btn-sm mr-1 edit-btn" data-toggle="modal"
                                                        data-target="#editPostalCodeModal" data-id="{{ $postalCode->id }}"
                                                        data-postal-code="{{ $postalCode->postal_code }}"
                                                        data-price="{{ $postalCode->price }}"
                                                        data-service-id="{{ $postalCode->service_id }}"
                                                        data-service-package-id="{{ $postalCode->service_package_id }}"
                                                        data-service-category-id="{{ $postalCode->service_category_id }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form class="deleteForm d-inline-block"
                                                        action="{{ route('admin.postal_codes.delete', $postalCode->id) }}"
                                                        method="post">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm deleteBtn">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="mt-3 text-center">
                    <div class="d-inline-block mx-auto">
                        {{ $postalCodes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Postal Code Modal -->
<div class="modal fade" id="addPostalCodeModal" tabindex="-1" role="dialog" aria-labelledby="addPostalCodeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPostalCodeModalLabel">{{ __('Add Postcode Surcharge') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addPostalCodeForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="postal_code">{{ __('Postcode Area') }}</label>
                        <input type="text" class="form-control" id="postal_code" name="postal_code" placeholder="EH1, EH2, KY12" required>
                    </div>
                    <div class="form-group">
                        <label for="price">{{ __('Extra Charge') }}</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                    </div>
                    <div class="form-group">
                        <label for="apply_to">{{ __('Apply To') }}</label>
                        <select class="form-control postal-apply-to" id="apply_to" name="apply_to" data-prefix="add">
                            <option value="all">{{ __('All services/packages') }}</option>
                            <option value="service">{{ __('Specific service') }}</option>
                            <option value="package">{{ __('Specific package') }}</option>
                            <option value="category">{{ __('Specific category') }}</option>
                        </select>
                    </div>
                    <div class="form-group postal-target add-target add-target-service d-none">
                        <label for="service_id">{{ __('Service') }}</label>
                        <select class="form-control" id="service_id" name="service_id">
                            <option value="">{{ __('Select Service') }}</option>
                            @foreach ($services as $service)
                                <option value="{{ $service->service_id }}">{{ $service->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group postal-target add-target add-target-package d-none">
                        <label for="service_package_id">{{ __('Package') }}</label>
                        <select class="form-control" id="service_package_id" name="service_package_id">
                            <option value="">{{ __('Select Package') }}</option>
                            @foreach ($packages as $package)
                                <option value="{{ $package->id }}">{{ $package->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group postal-target add-target add-target-category d-none">
                        <label for="service_category_id">{{ __('Category') }}</label>
                        <select class="form-control" id="service_category_id" name="service_category_id">
                            <option value="">{{ __('Select Category') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Postal Code Modal -->
<div class="modal fade" id="editPostalCodeModal" tabindex="-1" role="dialog" aria-labelledby="editPostalCodeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPostalCodeModalLabel">{{ __('Edit Postcode Surcharge') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editPostalCodeForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="form-group">
                        <label for="edit_postal_code">{{ __('Postcode Area') }}</label>
                        <input type="text" class="form-control" id="edit_postal_code" name="postal_code" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_price">{{ __('Extra Charge') }}</label>
                        <input type="number" step="0.01" class="form-control" id="edit_price" name="price" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_apply_to">{{ __('Apply To') }}</label>
                        <select class="form-control postal-apply-to" id="edit_apply_to" name="apply_to" data-prefix="edit">
                            <option value="all">{{ __('All services/packages') }}</option>
                            <option value="service">{{ __('Specific service') }}</option>
                            <option value="package">{{ __('Specific package') }}</option>
                            <option value="category">{{ __('Specific category') }}</option>
                        </select>
                    </div>
                    <div class="form-group postal-target edit-target edit-target-service d-none">
                        <label for="edit_service_id">{{ __('Service') }}</label>
                        <select class="form-control" id="edit_service_id" name="service_id">
                            <option value="">{{ __('Select Service') }}</option>
                            @foreach ($services as $service)
                                <option value="{{ $service->service_id }}">{{ $service->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group postal-target edit-target edit-target-package d-none">
                        <label for="edit_service_package_id">{{ __('Package') }}</label>
                        <select class="form-control" id="edit_service_package_id" name="service_package_id">
                            <option value="">{{ __('Select Package') }}</option>
                            @foreach ($packages as $package)
                                <option value="{{ $package->id }}">{{ $package->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group postal-target edit-target edit-target-category d-none">
                        <label for="edit_service_category_id">{{ __('Category') }}</label>
                        <select class="form-control" id="edit_service_category_id" name="service_category_id">
                            <option value="">{{ __('Select Category') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
    <script>
        $(document).ready(function () {
            function updateTargetFields(prefix, applyTo) {
                $('.' + prefix + '-target').addClass('d-none');
                $('#' + (prefix === 'add' ? '' : 'edit_') + 'service_id').val('');
                $('#' + (prefix === 'add' ? '' : 'edit_') + 'service_package_id').val('');
                $('#' + (prefix === 'add' ? '' : 'edit_') + 'service_category_id').val('');

                if (applyTo && applyTo !== 'all') {
                    $('.' + prefix + '-target-' + applyTo).removeClass('d-none');
                }
            }

            $('.postal-apply-to').on('change', function () {
                updateTargetFields($(this).data('prefix'), $(this).val());
            });

            // Add Postal Code
            $('#addPostalCodeForm').on('submit', function (e) {
                e.preventDefault();
                let formData = new FormData(this);

                $.ajax({
                    url: '{{ route("admin.postal_codes.store") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $('#addPostalCodeModal').modal('hide');
                        location.reload();
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            console.log(errors);
                        }
                    }
                });
            });

            // Edit Postal Code
            $('.edit-btn').on('click', function () {
                let id = $(this).data('id');
                let postalCode = $(this).data('postal-code');
                let price = $(this).data('price');
                let serviceId = $(this).data('service-id') || '';
                let packageId = $(this).data('service-package-id') || '';
                let categoryId = $(this).data('service-category-id') || '';
                let applyTo = 'all';

                if (packageId) {
                    applyTo = 'package';
                } else if (serviceId) {
                    applyTo = 'service';
                } else if (categoryId) {
                    applyTo = 'category';
                }

                $('#edit_id').val(id);
                $('#edit_postal_code').val(postalCode);
                $('#edit_price').val(price);
                $('#edit_apply_to').val(applyTo);
                updateTargetFields('edit', applyTo);
                $('#edit_service_id').val(serviceId);
                $('#edit_service_package_id').val(packageId);
                $('#edit_service_category_id').val(categoryId);

                $('#editPostalCodeForm').attr('action', '{{ url("admin/service-managment/postal-codes/update") }}/' + id);
            });

            $('#editPostalCodeForm').on('submit', function (e) {
                e.preventDefault();
                let formData = new FormData(this);
                let actionUrl = $(this).attr('action');

                $.ajax({
                    url: actionUrl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $('#editPostalCodeModal').modal('hide');
                        location.reload();
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            console.log(errors);
                        }
                    }
                });
            });

            // Default postal price
            $('#defaultPostalPriceForm').on('submit', function (e) {
                e.preventDefault();

                $.ajax({
                    url: '{{ route("admin.postal_codes.default_price.update") }}',
                    type: 'POST',
                    data: {
                        default_price: $('#default_price').val(),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        alert(response.message);
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            console.log(errors);
                        }
                    }
                });
            });

            // Bulk delete
            $('.bulk-check').on('change', function () {
                let checkedBoxes = $('.bulk-check:checked').not('[data-val="all"]');
                if (checkedBoxes.length > 0) {
                    $('.bulk-delete').removeClass('d-none');
                } else {
                    $('.bulk-delete').addClass('d-none');
                }
            });

            $('.bulk-delete').on('click', function (e) {
                e.preventDefault();
                let href = $(this).data('href');
                let ids = [];
                $('.bulk-check:checked').not('[data-val="all"]').each(function () {
                    ids.push($(this).val());
                });

                if (ids.length > 0) {
                    if (confirm('Are you sure you want to delete selected postal codes?')) {
                        $.ajax({
                            url: href,
                            type: 'POST',
                            data: {
                                ids: ids,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (response) {
                                location.reload();
                            }
                        });
                    }
                }
            });
        });
    </script>
@endsection
