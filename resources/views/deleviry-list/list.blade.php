@extends('layouts.master')

@section('title') @lang('translation.Products') @endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') Subscription Logs @endslot
        @slot('title') Subscription Logs  @endslot
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
                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Date</th>
                                <th>Price</th>
                                <th>Remark</th>
                                
                            </tr>
                        </thead>

                        <tbody>
                            @php $i = 1 @endphp
                            @foreach($delivery_list as $clist)
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $clist['name'] }}</td>
                                <td>{{ $clist['mobile'] }}</td>
                                <td>{{ $clist['created_at'] }}</td>
                                
                                <td>{{ $clist['total_price'] }}</td>
                                <td>{{ $clist['remarks'] }}</td>
                               
                            </tr>
                            @php $i++ @endphp
                            @endforeach

                        </tbody>
                    </table>

                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
<script>
    // 	// $(document).ready(function () {
	// 	// $('#products_add').addClass('mm-active');
	// 	// $('#product_add_menu').addClass('mm-show');
	// 	// $('#product_add').addClass('mm-active');
	// });
</script>

@endsection
@section('script')
    <!-- Required datatable js -->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
@endsection
