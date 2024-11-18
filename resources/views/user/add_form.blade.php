@extends('layouts.master')

@section('title') @lang('translation.Delivery_persons') @endsection

@section('content')

<div class="row">
    <div class="col-xl-10">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title mb-4">Add Admin Person</h4>

                {{-- Display Success Message --}}
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif


                <form action="{{ url('store_admin') }}" method="post" name="user_form" id="user_form" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}">
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
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
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                <label class="form-label">Admin Pic</label>
                                <input type="file" class="form-control" id="avatar" name="avatar">
                                @error('avatar')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary w-md mt-4">Submit</button>
                            </div>
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
