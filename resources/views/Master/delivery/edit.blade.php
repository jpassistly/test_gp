@extends('layouts.master')

@section('title')
    @lang('translation.DeliveryLineMapping')
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-10">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Edit Delivery Line</h4>
                    <form action="{{ URL('delivery-lins-mapping-edit') }}" method="post" name="form" id="form"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Date</label>
                                    @if (isset($date))
                                        <input readonly type="date" class="form-control" id="date" name="date"
                                            value="{{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}">
                                    @endif
                                    @error('date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="alert alert-info alert-dismissible fade show " id='alert_message'
                                    role="alert">
                                    hi...! wait For response.
                                </div>
                                <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>Delivery Line</th>
                                            <th>Delivery Staff</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $i = 1 @endphp
                                        @foreach ($delivery_lines as $line)
                                            <tr>
                                                <td>{{ $i }}</td>
                                                <td>
                                                    <input type="hidden" name="delivery_line_ids[]"
                                                        value="{{ $line->id }}">
                                                    {{ $line->name }}
                                                </td>
                                                <td>
                                                    <select id="delivery_staff_id"
                                                        data-delivery-line-id='{{ $line->id }}'
                                                        name="delivery_staff_id[]" class="form-select ">
                                                        <option value="">--- Please Select ---</option>
                                                        @foreach ($delivery_staff as $staff)
                                                            <option value="{{ $staff->id }}"
                                                                @if (isset($data)) @foreach ($data as $mapping)
                                                                        @if ($mapping->delivery_line_id == $line->id && $mapping->delivery_staff_id == $staff->id)
                                                                            selected @endif
                                                                @endforeach
                                                        @endif
                                                        >
                                                        {{ $staff->name }}
                                                        </option>
                                        @endforeach
                                        </select>
                                        </td>
                                        </tr>
                                        @php $i++ @endphp
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3 d-flex justify-content-end">
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
        // alert();

        $('#date').on('change', function() {
            var today = new Date(); // Get today's date
            today.setHours(0, 0, 0, 0); // Remove time part for accurate comparison
            var selectedDate = new Date($('#date').val());

            // Compare dates
            if (selectedDate >= today) {
                datafetch();
            } else {
                alert('You cant schedule for the past date');
                $('#date').val("{{ \Carbon\Carbon::now()->format('Y-m-d') }}");
                datafetch();
            }
        });

        function datafetch() {
            var date = $('#date').val();
            // alert();
            $.ajax({
                url: "{{ env('APP_URL') }}/api/get-delivery-schedule",
                type: 'POST',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'date': date,
                },
                success: function(response) {
                    if (response.success) {

                        $('#alert_message').html(response.message);

                        response.data.forEach(function(delivery) {
                            var deliveryLineId = delivery.delivery_line_id;

                            // alert()
                            var selectElement = $('select[data-delivery-line-id="' +
                                deliveryLineId + '"]');
                            selectElement.val(delivery.delivery_staff_id);
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('An error occurred while fetching areas. Please try again.');
                }
            });
        }
        $(document).ready(function() {
            $('#route').addClass('mm-active');
            $('#delivery_menu').addClass('mm-show');
            $('#delivery-lins-mapping').addClass('mm-active');
            datafetch();
        });
    </script>
@endsection
