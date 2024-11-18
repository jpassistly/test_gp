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
                <form action="{{ url('update_admin') }}" enctype="multipart/form-data" method="post" name="user_form" id="user_form">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <!-- Use Blade's old() function to preserve input values on form validation failure, or populate with existing user details -->
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user_details->name) }}">
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user_details->email) }}">
                                @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password1" name="password" minlength="6" maxlength="8">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select id="status2" name="status" class="form-select">
                                    <option value="active" {{ old('status', $user_details->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $user_details->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Update Image</label>
                                <input type="file" class="form-control" id="avatar" name="avatar">
                                @error('avatar')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                @if (!empty($user_details->avatar))
                                    <img src="{{ asset($user_details->avatar) }}" alt="Current Image" style="max-width: 200px; max-height: 200px;">
                                @else
                                    <p>No image available</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-end">
                                <input type="hidden" name="id" value="{{ $user_details->id }}">
                                <button type="submit" class="btn btn-primary w-md my-3">Update</button>
                        </div>
                        <div class="col-md-6">
                            <!-- Empty column for layout purposes -->
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
        $('#masters').addClass('mm-active');
        $('#master_menu').addClass('mm-show');
        $('#User').addClass('mm-active');
    });
    // $('#user_form').validate({ // initialize the plugin
    //      rules: {
    //         name: { required: true },

    //         email: { required: true, email: true },
    //         password: { required: true },

    //         status: { required: true }
    //      },
    //      messages: {
    //         name: "Enter name",

    //         email: "Enter a valid email address",
    //         password: "Enter password",

    //         status: "Select status"
    //     }
    //  });


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
