@extends('layouts.master')

@section('title') @lang('translation.User') @endsection

@section('content')

{{-- @component('components.breadcrumb')
        @slot('li_1') Vendors @endslot
        @slot('title') Add Vendor @endslot
    @endcomponent
 --}}
<div class="row">
    <div class="col-xl-10">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title mb-4">Update Customer</h4>
                <form action="{{ url('update_vendor_update') }}" enctype="multipart/form-data" method="post" name="user_form" id="user_form">@csrf


                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" maxlength="20">
                            </div>
                        </div>
                        <script>
                                $("#name").val('{{$user_details->name}}')
                            </script>
                        <div class="col-md-6">
                            <label class="form-label">Mobile</label>
                            <input type="text" class="form-control" id="mobile" name="mobile" value="{{ old('mobile') }}"maxlength="10">
                        </div>
                        <script>
                                $("#mobile").val('{{$user_details->mobile}}')
                            </script>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
                            </div>
                        </div>
                        <script>
                                $("#email").val('{{$user_details->email}}')
                            </script>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Type</label>
                                <!-- <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}"> -->
                                <select id="type" name="type" class="form-select">
                                    <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Vendor</option>
                                    <option value="2" {{ old('status') == '2' ? 'selected' : '' }}>Buyer</option>
                                </select>
                            </div>
                        </div>
                        <script>
                                $("#type").val('{{$user_details->type}}')
                            </script>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}">
                            </div>
                        </div>
                        <script>
                                $("#address").val('{{$user_details->address}}')
                            </script>
                        <div class="col-md-6">
                            <div class="mb-3">
                            <label class="form-label">Status</label>
                                <select id="status2" name="status" class="form-select">
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <script>
                                $("#status2").val('{{$user_details->status}}')
                            </script>

                    </div>
                    <!-- images/1718282200.jpg -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <input type="hidden" name="id" value="{{ $user_details['id'] }}">


                                <button type="submit" class="btn btn-primary w-md mt-4">Update</button>
                            </div>
                        </div>
                        <div class="col-md-6">

                        </div>
                    </div>
                </form>
            </div>
            <!-- end card body -->
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->

@endsection

@section('script')

<script>
 $(document).ready(function() {
       
       $('#inventary').addClass('mm-active');
       $('#inventary_menu').addClass('mm-show');
       $('#vendor_buyer').addClass('mm-active');

      
   });

    $('#user_form').validate({ // initialize the plugin
         rules: {
            name: { required: true },
            mobile:{required:true},
            type:{required:true},
            email: { required: true, email: true },
            status: { required: true },
            address:{required: true}
         },
         messages: {
            name: "Enter name",
            mobile:"Enter Mobile",
            email: "Enter a valid email address",
            address: "Enter Address",
            type:"Select Type",
            status: "Select status"
        }
     });



    $('#togglePassword').on('click', function () {
            const passwordField = $('#password1');
            const passwordFieldType = passwordField.attr('type');
            const icon = $(this).find('i');

            if (passwordFieldType === 'password') {
                passwordField.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordField.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
</script>

@endsection
