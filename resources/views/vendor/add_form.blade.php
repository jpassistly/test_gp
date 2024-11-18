@extends('layouts.master')

@section('title') @lang('translation.Delivery_persons') @endsection

@section('content')

<div class="row">
    <div class="col-xl-10">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title mb-4">Add new Vendor / Buyer</h4>

                {{-- Display Success Message --}}
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Display Validation Errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ url('add_vendor_buyer') }}" method="post" name="user_form" id="user_form" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" maxlength="20">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mobile</label>
                            <input type="text" class="form-control" id="mobile" name="mobile" value="{{ old('mobile') }}"maxlength="10">
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
                            </div>
                        </div>
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
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                            <label class="form-label">Status</label>
                                <select id="status2" name="status" class="form-select">
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>





                    <div class="row">
                        <div class="col-md-12  d-flex align-items-end justify-content-end">
                                <button type="submit" class="btn btn-primary w-md mt-4 mb-3">Submit</button>
                        </div>
                        <div class="col-md-6">
                            <!-- Optional empty column for layout purposes -->
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
<script>
    $(document).ready(function() {
       
            $('#inventary').addClass('mm-active');
            $('#inventary_menu').addClass('mm-show');
            $('#vendor_buyer').addClass('mm-active');

           
        });

</script>
@endsection
