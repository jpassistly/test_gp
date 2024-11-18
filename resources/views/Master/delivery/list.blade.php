@extends('layouts.master')

@section('title')
    @lang('translation.Category')
@endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Delivery Schedule
        @endslot
        @slot('title')
            Delivery Schedule
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
                        <div class="col-6">
                            <a href="{{ route('delivery-lins-mapping.create') }}">
                                <p class="btn btn-outline-secondary waves-effect" style="float:right;">Add ++</p>
                            </a>
                        </div>
                    </div>
                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                {{-- <th>Delivery Line</th>
                                <th>Delivery Staff</th> --}}
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>


                        <tbody>
                            @php $i = 1 @endphp
                            @foreach ($data as $d)
                                <tr>
                                    <td>{{ $i }}</td>
                                    {{-- <td>{{ $d['delivery_line_name'] }}</td>
                                <td>{{ $d['delivery_boy_name'] }}</td> --}}
                                    <td>{{ date('d-m-Y', strtotime($d['date'])) }}</td>
                                    <td><a href="{{ route('delivery-lins-mapping-group', $d['date']) }}"><i
                                                class="fas fa-edit"></i></a></td>
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
