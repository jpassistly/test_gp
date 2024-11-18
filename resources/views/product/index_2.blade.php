@extends('layouts.master')

@section('title') @lang('translation.Products') @endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') Products @endslot
        @slot('title') Products List @endslot
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
                        <div class="col-6">
                            {{-- <a href="add_products"><p class="btn btn-outline-secondary waves-effect" style="float:right;">Add ++</p></a> --}}
                        </div>
                    </div>
                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Category</th>
                                <th>Name</th>
                                <th>Measurement</th>
                                <th>Unit</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php $i = 1 @endphp
                            @foreach($product_list as $clist)
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $clist['category_name'] }}</td>
                                <td>{{ $clist['name'] }}</td>
                                <td>{{ $clist['quantity_name'] }}</td>
                                <td>{{ $clist['measurement_name'] }}</td>
                                <td>{{ $clist['price'] }}</td>
                                <td>{{ $clist['status'] }}</td>
                                <td><a href="{{ url('update_product/'.$clist['id'].'/' )}}"><i class="fas fa-edit"></i></a></td>
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
    	$(document).ready(function () {
		$('#ecommerce').addClass('mm-active');
		$('#ecommerce').addClass('mm-show');
		$('#product').addClass('mm-active');
	});
</script>

@endsection
@section('script')
    <!-- Required datatable js -->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
@endsection
