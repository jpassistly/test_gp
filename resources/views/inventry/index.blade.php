@extends('layouts.master')

@section('title') @lang('translation.inventry_list') @endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') Farm @endslot
        @slot('title') Farm List @endslot
    @endcomponent
    <style>
    .table-responsive {
        max-height: 500px;
        overflow-y: auto;
    }
</style>
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
                        <a href="inventry_add">
                            <!-- <p class="btn btn-outline-secondary waves-effect" style="float:right;">Add ++</p> -->
                        </a>
                    </div>
                </div>

                <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Dairy Name</th>

                            <th>City</th>
                            <th>Area</th>
                            <th>Pincode</th>
                            <th>Location</th>
                            {{-- <th>Lon</th> --}}
                            <th>Address</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tab_values">
                        @php
                        $totalQuantity = 0;
                        $i = 1;
                        @endphp
                        @foreach($delivery_list as $dlist)
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ $dlist['name'] }}</td>
                            <td>{{ $dlist['city'] }}</td>
                            <td>{{ $dlist['area'] }}</td>
                            <td>{{ $dlist['pincode'] }}</td>
                            <td>{{ number_format($dlist->lat, 6) }},{{ number_format($dlist->lon, 6) }}</td>
                            {{-- <td>{{ number_format($dlist->lon, 6) }}</td> --}}
                            <td>{{ $dlist['address'] }}</td>

                            <td><a href="{{ url('inventry_add/edit/'.$dlist['id'].'/' )}}"><i class="fas fa-edit"></i></a></td>
                        </tr>
                        @php
                        $i++;
                        @endphp
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
