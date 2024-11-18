@extends('layouts.master')

@section('title') @lang('translation.Customers') @endsection

@section('css')
<!-- DataTables -->
<link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

@component('components.breadcrumb')
@slot('li_1') Order History @endslot
@slot('title')Order History @endslot
@endcomponent

<!-- Modal -->
<div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalToggleLabel">Order List</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body content">
                <!-- Table content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
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
                    <div class="col-6">
                        <!--<a href="add_person"><p class="btn btn-outline-secondary waves-effect" style="float:right;">Add ++</p></a>-->
                    </div>
                </div>
                <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Order ID</th>
                            <th>Total value </th>
                            <th>Ordered at </th>
                            <th>Scheduled date </th>
                            <th>Delivered at </th>
                            <th>Order Status</th>
                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody id="tab_values">
                        @php
                        $i=1;
                        @endphp
                        @foreach ($history as $his)
                        @if($his->delivery_status=='Delivered')
                        @php
                        $del_sts=$his->delivery_at;
                        @endphp

                        @else
                        @php
                        $del_sts=""; 
                        @endphp

                        @endif
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ $his->order_id }}</td>
                            <td>{{ $his->price }}</td>
                            <td>{{Carbon::parse($his->created_at)->format('d-m-Y')}}</td>
                            <td>{{Carbon::parse($his->delivery_date)->format('d-m-Y')}}</td>
                            <td>{{ $del_sts }}</td>
                            <td>{{$his->delivery_status}}</td>
                            <td><button class="btn btn-secondary" onclick="openmodal('{{ $his->order_id }}')"><i class="fas fa-eye"></i></button></td>


                        </tr>
                        @php
                        $i++;
                        @endphp

                        @endforeach

                    </tbody>
                </table>
                <div class="row bg-white">
        <div class="col-12 text-center mt-3">
        <h4>Gift Product</h4>
        </div>
        <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                {{-- <th>Plan</th>
                                <th>Category</th> --}}
                                <th>Product </th>
                                <th>Quantity </th>
                                <th>Measurment</th>
                                <th>Applied date </th>
                                <th>Delivered date</th>
                                <!-- <th>Action</th> -->
                            </tr>
                        </thead>

                        <tbody>
                            @php $i = 1 ;

                            @endphp
                            @foreach($giftproduct as $clist)
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $clist['customer_id'] }}</td>
                                <td>{{ $clist['price'] }}</td>
                                {{-- <td></td>
                                <td>{{ $clist['category'] }}</td> --}}
                                <td>{{ $clist['product_id_name'] }}</td>
                                <td>{{ $clist['quantity_name'] }}</td>
                                <td>{{ $clist['measurement_name'] }}</td>
                                <td>{{ Carbon::parse($clist['created_at'])->format('d-m-Y') }}</td>
                                <td>{{ Carbon::parse($clist['delivery_date'])->format('d-m-Y') }}</td>
                                <!-- <td><a href="{{ url('update_product/'.$clist['id'].'/' )}}"><i class="fas fa-edit"></i></a></td> -->
                            </tr>
                            @php $i++ @endphp
                            @endforeach

                        </tbody>
                    </table>

    </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('#customer').addClass('mm-active');
    $('#customer ul').addClass('mm-show');
    $('#customer ul li').addClass('mm-active');
</script>
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
</div>
@endsection


@section('script')
<!-- Required datatable js -->
<script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
<!-- Datatable init js -->
<script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
@endsection
