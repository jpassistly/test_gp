@extends('layouts.master')

@section('title') @lang('translation.Products') @endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') Gift Product Details @endslot
        @slot('title') Gift Product Details @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6"> @if (Session::has('success_message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Success :</strong> {{ Session::get('success_message') }}
                            </div>
                        @endif</div>
                        <!-- <div class="col-6">
                            <a href="add_product"><p class="btn btn-outline-secondary waves-effect" style="float:right;">Add ++</p></a>
                        </div> -->

                    </div>
                    <form action="gift_products" method="post">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-4">
                                <label for="from_date">From Date</label>
                                <input type="date" name="from_date" class="form-control" id="from_date"
                                    value="{{ request()->from_date ?? date('Y-m-d') }}">
                            </div>
                            <div class="col-4">
                                <label for="to_date">To Date</label>
                                <input type="date" name="to_date" class="form-control" id="to_date"
                                    value="{{ request()->to_date ?? date('Y-m-d') }}">
                            </div>


                            <div class="col-4 mt-4 float-end">
                                <label for="" class="mt-1"> </label>
                                <button type="submit" class="btn btn-outline-secondary waves-effect"
                                    style="float:right;margin-top:5px">Search</button>
                            </div>
                        </div>
                    </form>

                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Customer</th>
                                <th>Mobile</th>
                                <th>Amount</th>
                                {{-- <th>Plan</th> --}}
                                <th>Category</th>
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
                            use Carbon\Carbon;
                            @endphp
                            @foreach($giftproduct as $clist)
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $clist['customer_id'] }}</td>
                                <td>{{ $clist['customer_mobile'] }}</td>
                                <td>{{ $clist['price'] }}</td>
                                {{-- <td></td> --}}
                                <td>{{ $clist['category'] }}</td>
                                <td>{{ $clist['product_id'] }}</td>
                                <td>{{ $clist['quantity'] }}</td>
                                <td>{{ $clist['measurment'] }}</td>
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
        </div> <!-- end col -->
    </div> <!-- end row -->

@endsection
@section('script')
    <!-- Required datatable js -->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
@endsection
