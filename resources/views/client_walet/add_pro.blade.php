@extends('layouts.master')

@section('title') @lang('translation.Category') @endsection

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

                    <h4 class="card-title mb-4">Add Products</h4>
                    <form action="{{ url('add_product_save') }}" method="post" name="gift_form" id="category_form" enctype="multipart/form-data">@csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Product</label>
                                    <select id="formrow-inputState" name="product" class="form-select">
                                        @foreach ($product as $pro)
                                            <option value="{{ $pro->id }}">{{ $pro->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Quantity</label>
                                    <select id="formrow-inputState" name="quantity" class="form-select">
                                        @foreach ($quan as $qan)
                                            <option value="{{ $qan->id }}">{{ $qan->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Measurement</label>
                                    <select id="formrow-inputState" name="measurement" class="form-select">
                                        @foreach ($meas as $mea)
                                            <option value="{{ $mea->id }}">{{ $mea->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Pack</label>
                                    <input type="number" class="form-control" id="pack" name="pack" value="1">

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Price</label>
                                    <input type="text" class="form-control" id="price" name="price" value="{{ $pro->price }}" readonly>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Delivered Date</label>
                                    <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d') }}">
                                    <input type="hidden" name="cust_id" id="cust_id" value="{{$id}}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
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
    $(document).ready(function () {
        $('#ecommerce').addClass('mm-active');
        $('#ecommerce_menu').addClass('mm-show');
        $('#category').addClass('mm-active');

        // Extract ID from URL and set to hidden input
        var url = window.location.href;
        var id = url.substring(url.lastIndexOf('/') + 1);
        $('#walet_id').val(id);
    });

    $('#category_form').validate({ // initialize the plugin
        rules: {
            name: { required: true },
            pic: { required: true },
            status: { required: true }
        },
        messages: {
            name: "Enter category",
            pic: "Select picture",
            status: "Select status"
        }
    });
</script>

@endsection
