@extends('layouts.master')

@section('title')
    @lang('translation.User')
@endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@php
    use Carbon\Carbon;
@endphp
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Admin
        @endslot
        @slot('title')
            Admin List
        @endslot
    @endcomponent
<style>
    .modal .content img {
        max-width: 100%; /* Ensures the image fits within the modal width */
        height: auto; /* Maintains the aspect ratio */
    }

    .modal-dialog {
        max-width: 90%; /* Adjusts the modal width to make it more responsive */
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
                            <a href="add_admin">
                                <p class="btn btn-outline-secondary waves-effect" style="float:right;">Add ++</p>
                            </a>
                        </div>
                    </div>
                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Profile</th>

                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php $i = 1 @endphp
                            @foreach ($user as $ulist)
                                @php
                                    $astatus = $ulist['address_status'];
                                    if ($astatus == 'Y') {
                                        $disp_status = 'Verified';
                                    } else {
                                        $disp_status = 'Not Verfied';
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $ulist['name'] }}</td>
                                    <td>{{ $ulist['email'] }}</td>
                                    <td>{{ $ulist['status'] }}</td>

                                    <td>
                                        @isset($ulist['avatar'])
                                            <button style='background:white;border:0px'
                                                onclick="viewimage('{{ env('ASSET_LINK_URL') . $ulist['avatar'] }}')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @endisset

                                        {{-- <a href="{{ env('ASSET_LINK_URL') . $ulist['avatar'] }}">
                                            <img src="{{ env('ASSET_LINK_URL') . $ulist['avatar'] }}" height="50%"
                                                width="50%" alt="Description of Image" />
                                        </a> --}}
                                    </td>



                                    <td><a href="{{ url('update_user/' . $ulist['id'] . '/') }}"><i
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


    <div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel"
        tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalToggleLabel">Uploaded image</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body content m-0">

                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> --}}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
       function viewimage(imagesrc) {
            $('.content').html('');

            var contant = `
                <div class='d-flex justify-content-center'>
                    <img src="` + imagesrc + `" alt="uploaded Image" class="img-fluid" style="max-height: 80vh; width: auto;" />
                </div>`;
            
            $('.content').html(contant);
            $('#exampleModalToggle').modal('show');
        }   


    </script>
    <!-- Required datatable js -->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
@endsection
