@extends('layouts.master')

@section('title')
    @lang('translation.rating_report')
@endsection

@section('css')
    <link href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" />
    <style>
        .table-responsive {
            overflow-x: auto;
        }

        .dataTables_wrapper .dt-buttons {
            float: left;
        }

        .stars-outer {
            display: inline-block;
            position: relative;
            font-family: FontAwesome;
        }

        .stars-inner {
            position: absolute;
            top: 0;
            left: 0;
            white-space: nowrap;
            overflow: hidden;
        }

        .stars-outer::before {
            content: "\2606 \2606 \2606 \2606 \2606";
            /* Empty stars */
            color: #ccc;
        }

        .stars-inner::before {
            content: "\2605 \2605 \2605 \2605 \2605";
            /* Filled stars */
            color: #f39c12;
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Rating List
        @endslot
        @slot('title')
            Rating List
        @endslot
    @endcomponent

    @php
        use Carbon\Carbon;
    @endphp
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body content">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            @if (Session::has('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success :</strong> {{ Session::get('success_message') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <form action="rating_report" method="post">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-4">
                                <label for=""> From Date</label>
                                <input type="date" name="from_date" class="form-control" id="from_date"
                                    value="{{ request()->from_date ?? date('Y-m-d') }}">
                            </div>
                            <div class="col-4">
                                <label for=""> To Date</label>
                                <input type="date" name="to_date" class="form-control" id="to_date"
                                    value="{{ request()->to_date ?? date('Y-m-d') }}">
                            </div>
                            <div class="col-4">
                                <label for=""> Delivery Person</label>
                                <select id="formrow-inputState" name="delivery_person" class="form-select select2">
                                    <option value="">Select</option>
                                    @foreach ($delper as $delpe)
                                        <option {{ request()->delivery_person == $delpe->id ? 'selected' : '' }}
                                            value="{{ $delpe->id }}">{{ $delpe->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4">
                                <label for=""> Ratings</label>
                                <select id="formrow-inputState" name="ratings" class="form-select">
                                    <option value="">Select</option>

                                    <option {{ request()->ratings == 1 ? 'selected' : '' }} value="1">1</option>
                                    <option {{ request()->ratings == 2 ? 'selected' : '' }} value="2">2</option>
                                    <option {{ request()->ratings == 3 ? 'selected' : '' }} value="3">3</option>
                                    <option {{ request()->ratings == 4 ? 'selected' : '' }} value="4">4</option>
                                    <option {{ request()->ratings == 5 ? 'selected' : '' }} value="5">5</option>

                                </select>
                            </div>
                            <div class="col-8 float-end">
                                <button type="submit" class="mt-4 btn btn-outline-secondary waves-effect"
                                    style="float:right;margin-top:5px">Search</button>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table id="example" class="table table-bordered dt-responsive nowrap w-100 mt-3">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Date</th>
                                    <th>Customer Name</th>
                                    <th>Phone Number</th>
                                    <th>Delivery Person</th>
                                    <th>Rating</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="tab_values">
                                @php
                                    $totalQuantity = 0;
                                    $i = 1;
                                @endphp
                                @foreach ($delivery_list as $dlist)
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>{{ Carbon::parse($dlist['date'])->format('d-m-Y') }}</td>
                                        <td>{{ $dlist['subscription_customer_id'] }}</td>
                                        <td>{{ $dlist['mobile'] }}</td>
                                        <td>{{ $dlist['deliveryperson_id'] }}</td>
                                        <td>
                                            {{ $dlist['rating'] }} &nbsp;

                                            {{-- @php
                                                $ratingPercentage = ($dlist->rating / 5) * 100;
                                            @endphp
                                            <div class="stars-outer">
                                                <div class="stars-inner" style="width: {{ $ratingPercentage }}%;"></div>
                                            </div> --}}
                                        </td>
                                        <td>
                                            <button style='background:white;border:0px;'
                                                onclick='view("{{ $dlist->pic }}", "{{ $dlist->comments }}")'>
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @php
                                        $i++;
                                    @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Display Total Quantity -->
                    <!-- <div class="mt-3">
                                <strong>Total Quantity:</strong> Milli Liters
                            </div> -->
                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->    
    <script>
        function view(image, comments) {
            // alert(image + ' ' + comments);

            var images = image;
            $('.content').html(''); // Clear the content
            $('.content').html(
                '<div class="row">' +
                '<div class="col">' +
                '<img src="' + images + '" class="img-fluid">' + // Set the image
                '</div>' +
                '</div>' +
                '<div class="row mt-2">' +
                '<div class="col">' +
                '<p>' + comments + '</p>' + // Set the caption
                '</div>' +
                '</div>'
            );

            $('#exampleModal').modal('show'); // Show the modal
        }
    </script>
@endsection

@section('script')
    <!-- jQuery -->
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#example').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'excel',
                ],
                // scrollY: '50vh', // Vertical scroll
                scrollX: true, // Horizontal scroll
                scrollCollapse: true, // Collapse table when less data
                paging: true, // Enable paging
                lengthChange: true, // Enable the "Show entries" dropdown
                pageLength: 10, // Set default number of rows
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ], // Options for "Show entries"
            });
        });
        //     $(document).ready(function() {
        //     $('#example').DataTable({
        //         dom: 'Bfrtip',
        //         buttons: [
        //             {
        //                 extend: 'copy',
        //                 title: '', // Remove the title/heading
        //                 exportOptions: {
        //                     columns: ':not(:last-child)', // Exclude the last column (Action column)
        //                     format: {
        //                         header: function (data, columnIdx) {
        //                             if (columnIdx === $('#example th').length - 1) {
        //                                 return ''; // Remove the "Action" column header
        //                             }
        //                             return data;
        //                         }
        //                     }
        //                 }
        //             },
        //             {
        //                 extend: 'excel',
        //                 title: '', // Remove the title/heading
        //                 exportOptions: {
        //                     columns: ':not(:last-child)', // Exclude the last column (Action column)
        //                     format: {
        //                         header: function (data, columnIdx) {
        //                             if (columnIdx === $('#example th').length - 1) {
        //                                 return ''; // Remove the "Action" column header
        //                             }
        //                             return data;
        //                         }
        //                     }
        //                 }
        //             },
        //             {
        //                 extend: 'pdf',
        //                 title: '', // Remove the title/heading
        //                 exportOptions: {
        //                     columns: ':not(:last-child)', // Exclude the last column (Action column)
        //                     format: {
        //                         header: function (data, columnIdx) {
        //                             if (columnIdx === $('#example th').length - 1) {
        //                                 return ''; // Remove the "Action" column header
        //                             }
        //                             return data;
        //                         }
        //                     }
        //                 }
        //             },
        //             {
        //                 extend: 'print',
        //                 title: '', // Remove the title/heading
        //                 exportOptions: {
        //                     columns: ':not(:last-child)', // Exclude the last column (Action column)
        //                     format: {
        //                         header: function (data, columnIdx) {
        //                             if (columnIdx === $('#example th').length - 1) {
        //                                 return ''; // Remove the "Action" column header
        //                             }
        //                             return data;
        //                         }
        //                     }
        //                 }
        //             },
        //             {
        //                 text: 'Reload',
        //                 action: function(e, dt, node, config) {
        //                     dt.ajax.reload();
        //                 }
        //             }
        //         ],
        //         scrollX: true, // Horizontal scroll
        //         scrollCollapse: true, // Collapse table when less data
        //         paging: true, // Enable paging
        //         lengthChange: true, // Enable the "Show entries" dropdown
        //         pageLength: 10, // Set default number of rows
        //         lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]], // Options for "Show entries"
        //         columnDefs: [
        //             { targets: -1, orderable: false }, // Make the "Action" column non-orderable
        //             { targets: -1, searchable: false } // Make the "Action" column non-searchable
        //         ]
        //     });
        // });



        $(document).ready(function() {
            $('#reports').addClass('mm-active');
            $('#reports_sub_menu').addClass('mm-show');
            $('#rating_report').addClass('mm-active');
        });
    </script>
    
@endsection
