@extends('layouts.master')

@section('title')
    @lang('translation.Category')
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Assign Routes</h4>
                    <div class="row">

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Pincode</label>
                                <select id="pincode" name="pincode" class="form-select pincode select2">
                                    <option value="">Please Select</option>
                                    @foreach ($pincodes as $d)
                                        <option value="{{ $d['id'] }}">
                                            {{ $d['pincode'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Select Delivery Line</label>
                                <select id="delivery_line" name="delivery_line" class="form-select delivery_line select2">
                                    @foreach ($delivery_lines as $d)
                                        <option value="{{ $d['id'] }}">
                                            {{ $d['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        {{-- <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Area</label>
                                <select id="area" name="area" class="form-select area">

                                </select>
                            </div>
                        </div> --}}
                        {{-- <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Delivery Staff</label>
                                <select id="delivery_staff" name="delivery_staff" class="form-select delivery_staff">
                                    @foreach ($delivery_staff as $d)
                                        <option value="{{ $d['id'] }}">
                                            {{ $d['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div> --}}
                        {{-- <div class="col-md-2">
                            <div class="mb-3 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary w-md mt-4">Submit</button>
                            </div>
                        </div> --}}
                    </div>
                    <table id="cus-list" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Area</th>
                                <th>Assigned Delivery Line</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id='cus-list-body'>


                        </tbody>
                    </table>
                    <div class=" row justify-content-end py-2">

                        <div class="col-md-2 d-flex align-items-md-center justify-content-end">
                            <button onclick='savecuslist()' class='btn btn-primary'>Submit</button>
                        </div>
                    </div>
                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
@endsection

@section('script')
    <script>
        console.log("{{ env('APP_URL') }}");
        // ("{{ env('APP_URL') }}");
        $(document).ready(function() {
            $('#masters').addClass('mm-active');
            $('#master_menu').addClass('mm-show');
            $('#Route_master').addClass('mm-active');

            // Bind the change event to the getArea function
            $('.pincode').on('change', getArea);
        });

        function getArea() {
            var pincode = $('.pincode').val();

            $.ajax({
                url: "{{ env('APP_URL') }}/api/get-area-list",
                type: 'POST',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'pincode': pincode
                },
                success: function(response) {

                    if (response.status === 'success') {
                        $('#cus-list-body').html('');
                        $.each(response.data, function(index, data_area) {
                            var row = '<tr>' +
                                '<td>' + (index + 1) + '</td>' +
                                '<td>' + data_area.name + '</td>' +
                                '<td>' + data_area.assign_delivery_lines + '</td>' +
                                '<td>' +
                                '<input type="checkbox" class="checked-row" data-cus-id="' + data_area
                                .id + '" > ' +
                                '</td>' +
                                '</tr>';
                            $('#cus-list-body').append(row);
                        });
                    } else {
                        alert(response.message || 'No customers found.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('An error occurred while fetching areas. Please try again.');
                }
            });
        }



        function savecuslist() {
            var pincode = $('.pincode').val();
            var delivery_line = $('.delivery_line').val();

            var checkedValues = [];

            $('.checked-row:checked').each(function() {
                checkedValues.push($(this).data('cus-id'));
            });

            console.log(checkedValues);

            $.ajax({
                url: "{{ env('APP_URL') }}/api/save-area-route-list",
                type: 'POST',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'pincode': pincode,
                    'delivery_line': delivery_line,
                    'area_id': checkedValues,
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('An error occurred while fetching customers. Please try again.');
                }
            });
        }
    </script>
@endsection
