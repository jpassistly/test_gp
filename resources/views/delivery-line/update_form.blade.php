@extends('layouts.master')

@section('title')
    @lang('translation.Delivery_lines')
@endsection

@section('content')
    {{--     @component('components.breadcrumb')
        @slot('li_1') Vendors @endslot
        @slot('title') Edir Vendor @endslot
    @endcomponent
 --}}
    <div class="row">
        <div class="col-xl-10">
            <div class="card">
                <div class="card-body">

                    <h4 class="card-title mb-4">Edit Delivery Line</h4>
                    <form action="{{ url('update_store_line') }}" method="post" name="line_form" id="line_form">@csrf
                        <form>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Delivery Line</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            value="{{ $delivery_lines_details['name'] }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Pincode</label>
                                        <select id="pincode_id" name="pincode_id" class="form-select select2">
                                            <option value="">Please Select</option>
                                            @foreach ($pincode as $d)
                                                <option value="{{ $d['id'] }}"
                                                    {{ (isset($delivery_lines_details) && $delivery_lines_details['pincode_id'] == $d['id']) || old('pincode_id') == $d['id'] ? 'selected' : '' }}>
                                                    {{ $d['pincode'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select id="formrow-inputState" name="status" class="form-select">
                                            <option value="">Select</option>
                                            <option value="Active">Active</option>
                                            <option value="Inactive">Inactive</option>
                                        </select>
                                        <script>
                                            $('#formrow-inputState').val('{{ $delivery_lines_details['status'] }}');
                                        </script>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Select Color (Hex Code)</label>
                                    <input type="color" class="form-control form-control-color" id="color_code" name="color_code" value="{{ $delivery_lines_details['color_code'] }}">
                                </div>
                            </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <input type="hidden" name="id" value="{{ $delivery_lines_details['id'] }}">
                                        <button type="submit" class="btn btn-primary w-md mt-4">Update</button>
                                    </div>
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
            $('#delivery_lines').addClass('mm-active');
        });

        

        $('#line_form').validate({ // initialize the plugin
            rules: {
                name: {
                    required: true,
                    maxlength: 40
                },
                status: {
                    required: true
                },
                color_code: {
                    required: true
                }
            },
            messages: {
                name: {
                    required: "Enter Line",
                    maxlength: "Line must not exceed 40 characters"
                },
                status: "Select status",
                color_code: "Please select a color"
            }
        });
    </script>
@endsection