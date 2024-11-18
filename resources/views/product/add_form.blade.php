@extends('layouts.master')

@section('title')
    @lang('translation.Products')
@endsection

@section('content')
    {{--     @component('components.breadcrumb')
        @slot('li_1') Vendors @endslot
        @slot('title') Add Vendor @endslot
    @endcomponent
 --}}
    <div class="row">
        <div class="col-xl-10">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">{{ isset($product_details) ? 'Edit Product' : 'Add Product' }}</h4>

                    <form action="{{ url('storeorupdate') }}" method="post" name="product_form" id="product_form"
                        enctype="multipart/form-data">
                        @csrf


                        @isset($product_details)
                            <input type="hidden" class="form-control" id="id" name="id" min="0"
                                value="{{ old('id') ?? ($product_details->id ?? '') }}">
                        @endisset
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Category <span class='text-danger'>*</span></label>
                                    <select id="category_id" name="category_id" class="form-select select2">
                                        <option value="">Select the option</option>
                                        @foreach ($category_list as $clist)
                                            <option value="{{ $clist->id }}"
                                                {{ (old('category_id') ?? ($product_details->category_id ?? '')) == $clist->id ? 'selected' : '' }}>
                                                {{ $clist->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Name <span class='text-danger'>*</span></label>
                                    <select id="name" name="name" class="form-select select2">
                                        <option value="">Select the option</option>
                                        @foreach ($pro as $clist)
                                            <option value="{{ $clist->id }}"
                                                {{ (old('name') ?? ($product_details->product_id ?? '')) == $clist->id ? 'selected' : '' }}>
                                                {{ $clist->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Quantity <span class='text-danger'>*</span></label>
                                    <select id="quantity_id" name="quantity_id" class="form-select select2">
                                        <option value="">Select the option</option>
                                        @foreach ($quantity_list as $qlist)
                                            <option value="{{ $qlist->id }}"
                                                {{ (old('quantity_id') ?? ($product_details->quantity_id ?? '')) == $qlist->id ? 'selected' : '' }}>
                                                {{ $qlist->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('quantity_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Measurement <span class='text-danger'>*</span></label>
                                    <select id="measurement_id" name="measurement_id" class="form-select select2">
                                        <option value="">Select the option</option>
                                        @foreach ($measurement_list as $mlist)
                                            <option value="{{ $mlist->id }}"
                                                {{ (old('measurement_id') ?? ($product_details->measurement_id ?? '')) == $mlist->id ? 'selected' : '' }}>
                                                {{ $mlist->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('measurement_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Price <span class='text-danger'>*</span></label>
                                    <input type="number" class="form-control" id="price" name="price" min="0"
                                        value="{{ old('price') ?? ($product_details->price ?? '') }}">
                                    @error('price')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="{{ old('description') ?? ($product_details->description ?? '') }}">
                                    @error('description')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Picture <span class='text-danger'>*</span></label>
                                    <input type="file" class="form-control" id="pic" name="pic">
                                    @error('pic')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            @isset($product_details->pic)
                                <div class="col-md-6">
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Current Picture</label>
                                        <div>
                                            <img src="{{ url($product_details->pic) }}" alt="Product Image"
                                                style="max-height: 100px;">
                                        </div>
                                    </div>
                                </div>
                            @endisset


                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Subscription <span class='text-danger'>*</span></label>
                                    <select id="subscription" name="subscription" class="form-select">
                                        <option value="N"
                                            {{ (old('subscription') ?? ($product_details->subscription ?? '')) == 'N' ? 'selected' : '' }}>
                                            No</option>
                                        <option value="Y"
                                            {{ (old('subscription') ?? ($product_details->subscription ?? '')) == 'Y' ? 'selected' : '' }}>
                                            Yes</option>
                                    </select>
                                    @error('subscription')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status <span class='text-danger'>*</span></label>
                                    <select id="statusss" name="status" class="form-select">
                                        <option value="Active"
                                            {{ (old('status') ?? ($product_details->status ?? '')) == 'Active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="Inactive"
                                            {{ (old('status') ?? ($product_details->status ?? '')) == 'Inactive' ? 'selected' : '' }}>
                                            Inactive</option>
                                    </select>
                                    @error('status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary w-md mt-4">
                                        {{ isset($product_details) ? 'Update' : 'Submit' }}
                                    </button>
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
            $('#products_add').addClass('mm-active');
            $('#product_add_menu').addClass('mm-show');
            $('#product_add').addClass('mm-active');
        });

        // @if (request()->path() == 'add_products' || \Illuminate\Support\Str::is('update_products/*', request()->path()))

        //     $(document).ready(function() {
        //         $('#products_add').removeClass('mm-active');
        //         $('#product_add_menu').removeClass('mm-show');
        //         $('#product_add').removeClass('mm-active');


        //         $('#ecommerce').addClass('mm-active');
        //         $('#ecommerce_menu').addClass('mm-show');
        //         $('#product').addClass('mm-active');
        //     });
        // @endif

        $(document).ready(function() {
            $.validator.addMethod("jpeg", function(value, element) {
                return this.optional(element) || /\.(jpe?g)$/i.test(value);
            }, "Please select a JPEG image.");

            // $('#product_form').validate({
            //     rules: {
            //         category_id: { required: true },
            //         name: { required: true },
            //         price: { required: true,number:true, },
            //         quantity_id: { required: true },
            //         measurement_id: { required: true },
            //         pic: {
            //             required: true,
            //             jpeg: true,
            //             jpg:true
            //         },
            //         status: { required: true }
            //     },
            //     messages: {
            //         category_id: "Select the option",
            //         name: "Select the option",
            //         price:{
            //             required: "enter price",
            //             number: "enter only numbers."
            //         },
            //         quantity_id:{
            //             required: "Select the option",
            //         },
            //         pic: {
            //             required: "Select picture",
            //             jpeg: "Please select a JPEG image."
            //         },
            //         status: "Select status"
            //     }
            // });
        });
    </script>
@endsection
