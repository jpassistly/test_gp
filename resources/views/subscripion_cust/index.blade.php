@extends('layouts.master')

@section('title')
    @lang('translation.subscription_list')
@endsection

@section('css')
    <link href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Subscription List
        @endslot
        @slot('title')
            Subscription List
        @endslot
    @endcomponent

    @php
        use Carbon\Carbon;
    @endphp
    <div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalToggleLabel">Product Details</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body content">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                    <form action="subscription_list" method="post">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-3">
                                <label for=""> From Date</label>
                                <input type="date" name="from_date" class="form-control" id="from_date"
                                    value="{{ (request()->from_date) ? date('Y-m-d',strtotime(request()->from_date)) : date('Y-m-d') }}">
                            </div>

                            <div class="col-3">
                                <label for=""> To Date</label>
                                <input type="date" name="to_date" class="form-control" id="to_date"
                                    value="{{ (request()->to_date) ? date('Y-m-d',strtotime(request()->to_date)) : date('Y-m-d') }}">
                            </div>


                            <div class="col-3">
                                <label for="">Plans</label>
                                <select id="plan" name="plan" class="form-select delivery_line">
                                    <option value="">
                                        Select
                                    </option>
                                    @foreach ($plans as $dd)
                                        <option {{ request()->plan == $dd->id ? 'selected' : '' }}
                                            value="{{ $dd->id }}">
                                            {{ $dd->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-3 mt-4">
                                <label for="" class="mt-1"> </label>
                                <button type="submit" class="btn btn-outline-secondary waves-effect"
                                    style="float:right;margin-top:5px">Search</button>
                            </div>
                        </div>
                    </form>

                    <table id="example" class="table table-bordered dt-responsive nowrap w-100 mt-3">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Customer Name</th>
                                <th>Plan</th>
                                <th>Start date</th>
                                <th>End date</th>
                                <th>Subscription payment date</th>
                                <th>Action</th>

                            </tr>
                        </thead>
                        <tbody id="tab_values">
                            @php
                                $totalQuantity = 0;
                                $i = 1;
                                /*dd($delivery_list);*/
                            @endphp
                            @foreach ($payment as $dlist)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $dlist->customers_id }}</td>
                                    <td>{{ $dlist->plan_id }}</td>
                                    <td>{{ Carbon::parse($dlist->start_date)->format('d-m-Y') }}</td>
                                    <td>{{ Carbon::parse($dlist->end_date)->format('d-m-Y') }}</td>
                                    <td>{{ Carbon::parse($dlist->created_at)->format('d-m-Y') }}</td>
                                    <td><button style='background:white;border:0px;' class="btn-btn-success"
                                            onclick="pro_det('{{ $dlist->id }}')"><i class="fa fa-eye"></i></button>
                                    </td>

                                </tr>
                                @php
                                    $i++;
                                @endphp
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Display Total Quantity -->

                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
    <script>
        function pro_det(id) {
            $.ajax({
                url: "{{ env('API_APP_URL') }}/api/payment_plan",
                type: "post",
                data: {
                    id: id
                },
                success: function(response) {
                    $('.content').html('');
                    $('#exampleModalToggle').modal('show');
                    $('.content').html(response.data);
                    $('#exampleModalLabel').html('Product Details');
                },
                error: function(response) {
                    console.error("Error: ", response);
                }
            });
        }
    </script>
@endsection

@section('script')
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    <!-- <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
                <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
                <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
                <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
                <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script> -->
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
                    // {
                    //     extend: 'copy',
                    //     title: 'Subscription List', // Remove the title/heading
                    //     exportOptions: {
                    //         columns: ':not(:last-child)', // Exclude the last column (Action column)
                    //         format: {
                    //             header: function (data, columnIdx) {
                    //                 if (columnIdx === $('#example th').length - 1) {
                    //                     return ''; // Remove the "Action" column header
                    //                 }
                    //                 return data;
                    //             }
                    //         }
                    //     }
                    // },
                    {
                        extend: 'excel',
                        title: 'Subscription List', // Remove the title/heading
                        exportOptions: {
                            columns: ':not(:last-child)', // Exclude the last column (Action column)
                            format: {
                                header: function(data, columnIdx) {
                                    if (columnIdx === $('#example th').length - 1) {
                                        return ''; // Remove the "Action" column header
                                    }
                                    return data;
                                }
                            }
                        }
                    },
                    // {
                    //     extend: 'pdf',
                    //     title: 'Subscription List', // Remove the title/heading
                    //     exportOptions: {
                    //         columns: ':not(:last-child)', // Exclude the last column (Action column)
                    //         format: {
                    //             header: function (data, columnIdx) {
                    //                 if (columnIdx === $('#example th').length - 1) {
                    //                     return ''; // Remove the "Action" column header
                    //                 }
                    //                 return data;
                    //             }
                    //         }
                    //     }
                    // },
                    // {
                    //     extend: 'print',
                    //     title: 'Subscription List', // Remove the title/heading
                    //     exportOptions: {
                    //         columns: ':not(:last-child)', // Exclude the last column (Action column)
                    //         format: {
                    //             header: function(data, columnIdx) {
                    //                 if (columnIdx === $('#example th').length - 1) {
                    //                     return ''; // Remove the "Action" column header
                    //                 }
                    //                 return data;
                    //             }
                    //         }
                    //     }
                    // },
                    // {
                    //     text: 'Reload',
                    //     action: function(e, dt, node, config) {
                    //         dt.ajax.reload();
                    //     }
                    // }
                ],
                scrollX: true, // Horizontal scroll
                scrollCollapse: true, // Collapse table when less data
                paging: true, // Enable paging
                lengthChange: true, // Enable the "Show entries" dropdown
                pageLength: 10, // Set default number of rows
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ], // Options for "Show entries"
                columnDefs: [{
                        targets: -1,
                        orderable: false
                    }, // Make the "Action" column non-orderable
                    {
                        targets: -1,
                        searchable: false
                    } // Make the "Action" column non-searchable
                ]
            });
        });
    </script>
@endsection
