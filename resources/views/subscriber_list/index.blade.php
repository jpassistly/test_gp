@extends('layouts.master')

@section('title') @lang('translation.Delivery_lines') @endsection

@section('css')
    <link href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" />
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') Subscriber List @endslot
        @slot('title') Subscriber  List @endslot
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
                    <!-- <div class="row mb-3">
                        <div class="col-4">
                            <label for=""> From Date</label>
                            <input type="date" name="from_date" class="form-control" id="from_date" value="{{date('Y-m-d')}}">
                        </div>
                        <div class="col-4">
                            <label for=""> To Date</label>
                            <input type="date" name="to_date" class="form-control" id="to_date" value="{{date('Y-m-d')}}">
                        </div>
                        <div class="col-3 mt-4">
                            <label for="" class="mt-1"> </label>
                            <button type="submit" class="btn btn-outline-secondary waves-effect" style="float:right;margin-top:5px">Search</button>
                        </div>
                    </div> -->
                    </form>

                    <table id="example" class="table table-bordered dt-responsive nowrap w-100 mt-3">
                        <thead>
                            <tr>
                                <th>S.No</th>

                                <th>Client Name</th>
                                <th>Product</th>
                                <th>Subscription Remaining Days</th>

                            </tr>
                        </thead>
                        <tbody id="tab_values">
                            @php
                                $totalQuantity = 0;
                                $i = 1;
                                /*dd($delivery_list);*/

                            @endphp
                            @foreach($payment as $dlist)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $dlist->subscription_customer_id}}</td>
                                    <td>{{ $dlist->subscription_products_id }}</td>
                                    <td>{{ $dlist->date_count }}</td>

                                </tr>
                               @php
                               $i++
                               @endphp
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Display Total Quantity -->

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
