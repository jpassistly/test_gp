@extends('layouts.master')

@section('title') @lang('translation.User') @endsection

@section('content')

{{-- @component('components.breadcrumb')
        @slot('li_1') Vendors @endslot
        @slot('title') Add Vendor @endslot
    @endcomponent
 --}}
<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body">

                <form action="{{ url('update_admin') }}" enctype="multipart/form-data" method="post" name="user_form" id="user_form">
                    @csrf
                    <div class="row">
                        <div class="col-6">
                            @if (Session::has('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success :</strong> {{ Session::get('success_message') }}
                                </div>
                            @endif
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3 tex-center">
                                <h3 class=" mb-5">My profile</h3>

                                @if (!empty($user_details->avatar))
                                    <img class="rounded-circle" src="{{ asset($user_details->avatar) }}" alt="Current Image" style="max-width: 150px; max-height: 150px;">
                                @else
                                <img class="image-rounded" src="data:image/jpeg;base64,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" alt="Current Image" style="max-width: 150px; max-height: 150px;">
                                @endif
                            </div>
                        </div>
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
                                <label class="form-label">Update Image</label>
                                <input type="file" class="form-control" id="avatar" name="avatar">
                                @error('avatar')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <div class="col-md-6 d-none">
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
