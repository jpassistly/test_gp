@extends('layouts.master')

@section('title')
    @lang('translation.Delivery_lines')
@endsection

@section('css')
    <link href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Order list
        @endslot
        @slot('title')
            Order List
        @endslot
    @endcomponent

    @php
        use Carbon\Carbon;
    @endphp

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
                    <form action="order_list" method="post">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-3">
                                <label for=""> From Date</label>
                                <input type="date" name="from_date" class="form-control" id="from_date"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-3">
                                <label for=""> To Date</label>
                                <input type="date" name="to_date" class="form-control" id="to_date"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-3">
                                <label for="">Delivery Person</label>
                                <select id="delivery_person_id" name="delivery_line" class="form-select delivery_line">
                                    @foreach ($delivery_person as $d)
                                        <option value="{{ $d['id'] }}">
                                            {{ $d['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-3 mt-4">
                                <label for="" class="mt-1"> </label>
                                <button type="submit" class="btn btn-outline-secondary waves-effect"
                                    style="margin-top:5px">Search</button>
                            </div>
                        </div>
                    </form>

                    <table id="example" class="table table-bordered dt-responsive nowrap w-100 mt-3">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Date</th>
                                <th>Client Name</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Milli Liters</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="tab_values">
                            @php
                                $totalQuantity = 0;
                                $i = 1;
                                /*dd($delivery_list);*/
                            @endphp
                            @foreach ($delivery_list as $dlist)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $dlist->delivery_date ? Carbon::parse($dlist->delivery_date)->format('j-F-Y') : 'N/A' }}
                                    </td>
                                    <td>{{ $dlist->customer_id }}</td>
                                    <td>{{ $dlist->name }}</td>
                                    <td>{{ $dlist->quantity }}</td>
                                    <td>250</td>
                                    <td>{{ $dlist->delivery_status }}</td>
                                </tr>
                                @php
                                    $totalQuantity += $dlist->quantity;
                                    $i++;
                                @endphp
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Display Total Quantity -->
                    <div class="mt-3">
                        <strong>Total Quantity:</strong> {{ $totalQuantity * 250 }} Milli Liters

                    </div>
                    <div class="mt-3">
                        <strong>Total Quantity:</strong> {{ ($totalQuantity * 250) / 1000 }} Liters
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
        });
    </script>
@endsection
