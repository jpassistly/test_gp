@extends('layouts.master')

@section('title')
    @lang('translation.Orders')
@endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Orders
        @endslot
        @slot('title')
            Order Details
        @endslot
    @endcomponent

    <h2> </h2>
    <div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalToggleLabel">Order Details</h1>
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
    @php

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
                        <div class="col-6">
                            <!--<a href="add_pincode"><p class="btn btn-outline-secondary waves-effect" style="float:right;">Add ++</p></a>-->
                        </div>

                        <form action="list_order" method="post">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-3">
                                    <label for="from_date">From Date</label>
                                    <input type="date" name="from_date" class="form-control" id="from_date"
                                        value="{{ old('from_date', $post_values['from_date'] ?? date('Y-m-d')) }}">
                                </div>
                                <div class="col-3">
                                    <label for="to_date">To Date</label>
                                    <input type="date" name="to_date" class="form-control" id="to_date"
                                        value="{{ old('to_date', $post_values['to_date'] ?? date('Y-m-d')) }}">
                                </div>
                                <div class="col-3">
                                    <label for="area">Area</label>
                                    <select id="area" name="area" class="form-select delivery_line">
                                        <option value="">Select</option>
                                        @foreach ($area as $d)
                                            <option value="{{ $d['id'] }}"
                                                {{ old('area', $post_values['area'] ?? '') == $d['id'] ? 'selected' : '' }}>
                                                {{ $d['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-3">
                                    <label for="pincode">Pincode</label>
                                    <select id="pincode" name="pincode" class="form-select delivery_line">
                                        <option value="">Select</option>
                                        @foreach ($pincode as $d)
                                            <option value="{{ $d['id'] }}"
                                                {{ old('pincode', $post_values['pincode'] ?? '') == $d['id'] ? 'selected' : '' }}>
                                                {{ $d['pincode'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-3 mt-4">
                                    <label for="" class="mt-1"></label>
                                    <button type="submit" class="btn btn-outline-secondary waves-effect"
                                        style="margin-top:5px">Search</button>
                                </div>
                            </div>
                        </form>


                    </div>

                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Order Id</th>
                                <th>Total value</th>
                                <th>Ordered at</th>
                                <th>Scheduled date</th>
                                <th>Delivered at</th>
                                <th>Order Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        @php
                            use Carbon\Carbon;
                        @endphp

                        <tbody>
                            @php $i = 1 @endphp
                            @foreach ($order_list as $clist)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $clist['order_id'] }}</td>
                                    <td>{{ $clist['price'] }}</td>
                                    <td>{{ Carbon::parse($clist['created_at'])->format('d-m-Y') }}</td>
                                    <td>{{ Carbon::parse($clist['delivery_date'] )->format('d-m-Y') }}</td>
                                    <td>
    @if ($clist['delivery_status'] == 'yet to deliver' || $clist['delivery_status'] == 'Undelivered')
        <!-- Display nothing -->
    @else
        {{ \Carbon\Carbon::parse($clist['delivery_at'])->format('d-m-Y h:i:s') }}
    @endif
</td>

                                    
                                    <td>{{ $clist['delivery_status'] }}</td>
                                    <td><button class="btn btn-secondary" onclick="openmodal('{{ $clist->order_id }}')"
                                            href="{{ url('view_order/' . $clist['id'] . '/') }}"><i
                                                class="fas fa-eye"></i></button>
                                    </td>
                                </tr>
                                @php $i++ @endphp
                            @endforeach

                        </tbody>
                    </table>

                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
@endsection
@section('script')
    <!-- Required datatable js -->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
    <script>
        function openmodal(id) {
            // const token = localStorage.getItem('authToken');



            $.ajax({
                url: "{{ env('APP_URL') }}/api/order_list_view",
                type: 'POST',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'id': id,
                    // 'delivery_line': delivery_line,
                },
                success: function(response) {

                    if (response.success === true) {
                        $('.content').html('');
                        $('.content').html(response.tabel);
                        $('#exampleModalToggle').modal('show');
                    } else {

                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('An error occurred while fetching areas. Please try again.');
                }
            });
        }
    </script>
@endsection
