@extends('layouts.master')

@section('title')
    @lang('translation.Category')
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <form action="{{ route('route-list') }}" method="post">
                    @csrf
                    <div class="card-body">
                        <h4 class="card-title mb-4">Assign Routes</h4>
                        <div class="row">
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label class="form-label">Delivery Line</label>
                                    <select id="delivery_line" name="delivery_line" class="change-class form-select delivery_line select2">
                                        <option value="">Please Select</option>
                                        @foreach ($delivery_lines as $d)
                                            <option value="{{ $d['id'] }}" {{ request()->delivery_line == $d['id'] ? 'selected' : '' }}>
                                                {{ $d['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label class="form-label">Pincode</label>
                                    <select id="pincode" name="pincode" class="change-class form-select pincode select2">
                                        <option value="">Please Select</option>
                                        @foreach ($pincodes as $d)
                                            <option value="{{ $d['id'] }}" {{ request()->pincode == $d['id'] ? 'selected' : '' }}>
                                                {{ $d['pincode'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-2">
                                <label class="form-label"></label>
                                <div class="pt-2 ">
                                    <button type="submit" class="btn btn-outline-secondary"
                                        onclick="rupee('1','9012')">Search</i></button>
                                </div>
                            </div>
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
                        <table id="example" class="table table-bordered dt-responsive  nowrap w-100">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Delivery Line</th>
                                    <th>Pincode</th>
                                    <th>Area</th>
                                    {{-- <th>Action</th> --}}
                                </tr>
                            </thead>
                            <tbody id='cus-list-body'>
                                @foreach ($tabledata as $data)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{$data->Delivery_line_name}}</td>
                                        <td>{{$data->pincode_name}}</td>
                                        <td>{{$data->area_name}}</td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                        <div class=" row justify-content-end py-2">

                            <!-- <div class="col-md-2 d-flex align-items-md-center justify-content-end">
                                        <button onclick='savecuslist()' class='btn btn-primary'>Submit</button>
                                    </div> -->
                        </div>
                    </div>
                </form>
                <!-- end card body -->
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
@endsection

@section('script')
    <link href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" />

    <!-- jQuery -->
    <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <!-- MetisMenu JS -->
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <!-- TableExport JS -->
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
    <!-- jsPDF JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#example').DataTable({
                dom: 'Bfrtip',
                buttons: [
                     'excel',
                    //  {
                    //     text: 'Reload',
                    //     action: function(e, dt, node, config) {
                    //         dt.ajax.reload();
                    //     }
                    // }
                ]
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // $('#masters').addClass('mm-active');
            // $('#master_menu').addClass('mm-show');
            // $('#Route_master').addClass('mm-active');
            // // getdata()
            // // Bind the change event to the getdata function
            // $('.change-class').on('change', getdata);
        });

        // function getdata() {
        //     var pincode = $('.pincode').val();
        //     var delivery_line = $('.delivery_line').val();

        //     $.ajax({
        //         url: "{{ env('APP_URL') }}/api/get-delivery-line-list",
        //         type: 'POST',
        //         data: {
        //             '_token': '{{ csrf_token() }}',
        //             'pincode': pincode,
        //             'delivery_line': delivery_line,
        //         },
        //         success: function(response) {

        //             if (response.status === 'success') {
        //                 $('#cus-list-body').html('');
        //                 $.each(response.data, function(index, data_area) {
        //                     var row = '<tr>' +
        //                         '<td>' + (index + 1) + '</td>' +
        //                         '<td>' + data_area.Delivery_line_name + '</td>' +
        //                         '<td>' + data_area.pincode_name + '</td>' +
        //                         '<td>' + data_area.area_name + '</td>' ;

        //                     $('#cus-list-body').append(row);
        //                 });
        //             } else {
        //                 alert(response.message || 'No customers found.');
        //             }
        //         },
        //         error: function(xhr, status, error) {
        //             console.error('Error:', error);
        //             alert('An error occurred while fetching areas. Please try again.');
        //         }
        //     });
        // }



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
