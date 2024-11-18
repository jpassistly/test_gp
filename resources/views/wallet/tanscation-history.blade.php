@extends('layouts.master')

@section('title') @lang('translation.client_wallet_history') @endsection

@section('css')
    <link href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" />
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') Wallet list @endslot
        @slot('title') wallet List @endslot
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
                    <form action="transcation_list" method="post">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for=""> From Date</label>
                            <input type="date" name="from_date" class="form-control" id="from_date" value="{{ request()->from_date ?? date('Y-m-d') }}">
                        </div>
                        <div class="col-4">
                            <label for=""> To Date</label>
                            <input type="date" name="to_date" class="form-control" id="to_date" value="{{date('Y-m-d')}}">
                        </div>
                        <div class="col-3 mt-4">
                            <label for="" class="mt-1"> </label>
                            <button type="submit" class="btn btn-outline-secondary waves-effect" style="float:right;margin-top:5px">Search</button>
                        </div>
                    </div>
                    </form>

                    <table style='font-size:10px' id="example" class="table table-bordered dt-responsive nowrap w-100 mt-3">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Date</th>
                                <th>Customer </th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>Remark</th>
                            </tr>
                        </thead>
                        <tbody id="tab_values">
                            @php
                                $totalQuantity = 0;
                                $i = 1;
                                /*dd($delivery_list);*/
                            @endphp
                            @foreach($wallet_data as $wlist)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ \Carbon\Carbon::parse($wlist->created_at)->format('d-m-Y') }}</td>
                                    <td>{{ $wlist->cus_name }}</td>
                                    <td>{{ $wlist->debit_credit_status }}</td>
                                    <td>{{ $wlist->amount }}</td>
                                    <td>{{ $wlist->notes?? $wlist->debit_credit_status }}</td>
                                </tr>
                                @php
                                  $totalQuantity += $wlist->amount;
                                    $i++;
                                @endphp
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Display Total Quantity -->
                    @php
function formatINR($number) {
    $number_parts = explode('.', $number);
    $integer_part = $number_parts[0];
    $decimal_part = isset($number_parts[1]) ? '.' . $number_parts[1] : '';

    // Format the integer part using the Indian number system
    if (strlen($integer_part) > 3) {
        $last_three = substr($integer_part, -3);
        $remaining = substr($integer_part, 0, -3);
        $formatted_number = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $remaining) . $last_three;
    } else {
        $formatted_number = $integer_part;
    }

    return $formatted_number . $decimal_part;
}
@endphp

<div class="mt-3">
    <strong>Total Amount:</strong> {{ formatINR(number_format($totalQuantity, 2)) }} Rs
</div>

                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
<script>
    $(document).ready(function() {
        $('#wallet').addClass('mm-active');
        $('#wallet_menu').addClass('mm-show');
        $('#client_wallet_history').addClass('mm-active');
    });
</script>
@endsection

@section('script')
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
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
                    'excel', 'pdf', 
                ]
            });
        });


    </script>
@endsection
