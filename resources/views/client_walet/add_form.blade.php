@extends('layouts.master')

@section('title') @lang('translation.Category') @endsection

@section('content')

{{--     @component('components.breadcrumb')
        @slot('li_1') Vendors @endslot
        @slot('title') Add Vendor @endslot
    @endcomponent...
 --}}
    <div class="row">
        <div class="col-xl-10">
            <div class="card">
                <div class="card-body">

                    <h4 class="card-title mb-4">Gift Product</h4>
                    <form action="{{ url('gift_product_save') }}" method="post" name="gift_form" id="category_form" enctype="multipart/form-data">@csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Product</label>
                                    <select id="formrow-inputState" name="product" class="form-select select2">
                                        @foreach ($product as $pro)
                                            <option value="{{ $pro->id }}">{{ $pro->product_name.' - '.$pro->quantity_name.' - '.$pro->measurement_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            {{-- <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Measurement</label>
                                    <select id="formrow-inputState" name="quantity" class="form-select">
                                        @foreach ($quan as $qan)
                                            <option value="{{ $qan->id }}">{{ $qan->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Unit</label>
                                    <select id="formrow-inputState" name="measurement" class="form-select">
                                        @foreach ($meas as $mea)
                                            <option value="{{ $mea->id }}">{{ $mea->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Pack</label>
                                    <input type="number" class="form-control" id="pack" name="pack" value="1">
                                    <input type="hidden" name="walet_id" id="walet_id" value="">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Date</label>
                                    <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                    <!-- <input type="hidden" name="walet_id" id="walet_id" value=""> -->
                                </div>
                            </div>
                            <div class="col-md-12 d-flex justify-content-end">

                                    <button type="submit" class="btn btn-primary w-md mt-4">Submit</button>

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
        $('#wallet').addClass('mm-active');
        $('#wallet_menu').addClass('mm-show');
        $('#client_wallet_bal').addClass('mm-active');

        // Extract ID from URL and set to hidden input
        var url = window.location.href;
        var id = url.substring(url.lastIndexOf('/') + 1);
        $('#walet_id').val(id);
    });

    $('#category_form').validate({
    rules: {
        pack: {
            required: true,
            number: true,
            min: 0
        }
    },
    messages: {
        pack: {
            required: "This field is required.",
            number: "Please enter a valid number.",
            min: "Please enter a value greater than or equal to 0."
        }
    },
    // Add a function to see if the validation is working
    invalidHandler: function(event, validator) {
        console.log('Validation failed');  // Logs a message if validation fails
    }
});

</script>

@endsection
