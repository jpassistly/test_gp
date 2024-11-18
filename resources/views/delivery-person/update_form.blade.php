@extends('layouts.master')

@section('title')
    @lang('translation.Delivery_persons')
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-10">
            <div class="card">
                <div class="card-body">

                    <h4 class="card-title mb-4">Update Delivery Person</h4>

                    <form action="{{ url('update_store_person') }}" enctype="multipart/form-data" method="post"
                        name="user_form" id="user_form">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name', $user_details['name']) }}">
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Image</label>
                                    <input type="file" class="form-control" id="pic" name="pic">
                                    @error('pic')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    @if ($user_details->pic != '')
                                        <img src="{{ env('ASSET_LINK_URL') . 'assets/images/delivery_person/' . $user_details->pic }}"
                                            alt="Current Image" style="max-width: 100px; max-height: 200px;">
                                    @else
                                        <p>No image available</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Mobile</label>
                                    <input type="text" class="form-control" id="mobile" name="mobile"
                                        value="{{ old('mobile', $user_details['mobile']) }}">
                                    @error('mobile')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password1" name="password"
                                            minlength="6" maxlength="8" value="{{ old('mobile', $user_details['password']) }}" placeholder="Enter new password (optional)">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @error('password')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Aadhar number</label>
                                    <input type="text" class="form-control" id="aadhar_number" name="aadhar_number"
                                        value="{{ old('aadhar_number', $user_details['aadhar_number']) }}">
                                    @error('aadhar_number')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select id="sstatus" name="status" class="form-select">
                                        <option value="Active"
                                            {{ old('status', $user_details['status']) == 'Active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="Inactive"
                                            {{ old('status', $user_details['status']) == 'Inactive' ? 'selected' : '' }}>
                                            Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    <script>
                                        $('#sstatus').val('{{ old('status', $user_details['status']) }}');
                                    </script>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <input type="hidden" name="old_pic" value="{{ $user_details['pic'] }}">
                                <input type="hidden" name="id" value="{{ $user_details['id'] }}">
                                <button type="submit" class="btn btn-primary w-md mt-4">Update</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#masters').addClass('mm-active');
            $('#master_menu').addClass('mm-show');
            $('#delivery_person').addClass('mm-active');
        });


        $('#togglePassword').on('click', function() {
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
