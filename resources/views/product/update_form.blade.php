@extends('layouts.master')

@section('title')
    @lang('translation.Products')
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

                    <h4 class="card-title mb-4">Edit Product</h4>
                    <form action="{{ url('update_store_product') }}" method="post" name="product_form" id="product_form"
                        enctype="multipart/form-data">@csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Category</label>
                                    <select id="category_id" name="category_id" class="form-select select2">
                                        <option value="">Select</option>
                                        @foreach ($category_list as $clist)
                                            <option
                                                {{ isset($product_details->id) && $product_details->id == $clist['id'] ? 'selected' : '' }}
                                                value="{{ $clist['id'] }}">
                                                {{ $clist['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <script>
                                        $('#category_id').val('{{ $product_details['category_id'] }}');
                                    </script>
                                    @error('category_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <!-- <input type="text" class="form-control" id="name" name="name"> -->
                                    <select id="name" name="name" class="form-select select2">
                                        <option value="">Select</option>
                                        @foreach ($pro as $clist)
                                            <option value="{{ $clist['id'] }}">{{ $clist['name'] }}</option>
                                        @endforeach
                                    </select> </select>
                                    <script>
                                        $('#name').val('{{ $product_details['product_id'] }}');
                                    </script>
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Quantity</label>
                                    <select id="quantity_id" name="quantity_id" class="form-select select2">
                                        <option value="">Select</option>
                                        @foreach ($quantity_list as $qlist)
                                            <option value="{{ $qlist['id'] }}">{{ $qlist['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <script>
                                        $('#quantity_id').val('{{ $product_details['quantity_id'] }}');
                                    </script>
                                    @error('quantity_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Measurement</label>
                                    <select id="measurement_id" name="measurement_id" class="form-select select2">
                                        <option value="">Select</option>
                                        @foreach ($measurement_list as $mlist)
                                            <option value="{{ $mlist['id'] }}">{{ $mlist['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <script>
                                        $('#measurement_id').val('{{ $product_details['measurement_id'] }}');
                                    </script>
                                    @error('measurement_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Price</label>
                                    <input type="text" class="form-control" id="price" name="price"
                                        value="{{ $product_details['price'] }}">
                                </div>
                                @error('price')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="{{ $product_details['description'] }}">
                                    @error('description')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Picture</label>
                                    <input type="file" class="form-control " id="npic" name="npic">
                                    <p>.jpeg,jpg Format only</p>
                                </div>
                                <div class="mb-3">

                                    @if ($product_details->pic != '')
                                        <img src="{{ asset($product_details->pic) }}" alt="Current Image"
                                            style="max-width: 200px; max-height: 200px;">
                                    @else
                                        <p>No image available</p>
                                    @endif

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">

                                <div class="mb-3">
                                    <label class="form-label">subscription</label>
                                    <select name="subscription" id="subscription"class="form-select select2">

                                        <option value="N" {{ old('subscription') == 'N' ? 'selected' : '' }}>No
                                        </option>
                                        <option value="Y" {{ old('subscription') == 'Y' ? 'selected' : '' }}>Yes
                                        </option>
                                    </select>
                                    <script>
                                        $('#subscription').val('{{ $product_details['subscription'] }}');
                                    </script>
                                    @error('subscription')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">

                                <div class="mb-3">
                                    <label class="form-label">status</label>
                                    <select name="status" id="status1" class="form-control ">

                                        <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>
                                            Inactive</option>

                                    </select>
                                    @error('status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <input type="hidden" name="old_pic" value="{{ $product_details['pic'] }}">
                                    <input type="hidden" name="id" value="{{ $product_details['id'] }}">
                                    <button type="submit" class="btn btn-primary w-md mt-4">Submit</button>
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
            $('#ecommerce').addClass('mm-active');
            $('#ecommerce_menu').addClass('mm-show');
            $('#product').addClass('mm-active');
        });

        // $('#product_form').validate({ // initialize the plugin
        // 	 rules: {
        //         category_id:{required:true },
        // 		name:{required:true },
        //         price:{required:true },
        //         quantity_id:{required:true },
        //         status:{required:true }
        // 	 },
        // 	 messages: {
        //         category_id: "Select category",
        //         name: "Enter product",
        //         price: "Enter price",
        //         quantity_id: "Select quantity",
        //         status: "Select status"
        // 	}
        //  });
    </script>
@endsection
