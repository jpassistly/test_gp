@extends('layouts.master')

@section('title')
    @lang('translation.Category')
@endsection

@section('content')
    {{-- @component('components.breadcrumb')
        @slot('li_1') Vendors @endslot
        @slot('title') Add Vendor @endslot
    @endcomponent --}}
    <div class="row">
        <div class="col-xl-10">
            <div class="card">
                <div class="card-body">

                    <h4 class="card-title mb-4">{{ isset($data) ? 'Edit' : 'Add' }} Area Name</h4>
                    <form action="{{ route('area.store') }}" method="post" name="form" id="form" enctype="multipart/form-data">
                        @csrf
                        @isset($data)
                            <input type="hidden" class="form-control" id="id" name="id" value="{{ $data['id'] }}">
                        @endisset

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Pincode</label>
                                    <select id="pincode" name="pincode" class="form-select select2">
                                        <option value="">Please Select</option>
                                        @foreach($pincode as $d)
                                            <option value="{{ $d['id'] }}" {{ (isset($data) && $data['pincode'] == $d['id']) || (old('pincode') == $d['id']) ? 'selected' : '' }}>
                                                {{ $d['pincode'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('pincode')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ isset($data) ? $data['name'] : old('name') }}" maxlength="40">
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select id="formrow-inputState" name="status" class="form-select">
                                        <option value="Y" {{ (isset($data) && $data['status'] == 'Y') || (old('status') == 'Y') ? 'selected' : '' }}>Active</option>
                                        <option value="N" {{ (isset($data) && $data['status'] == 'N') || (old('status') == 'N') ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary w-md mt-4">{{ isset($data) ? 'Update' : 'Submit' }}</button>
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
            $('#area').addClass('mm-active');

        });
    </script>
@endsection
