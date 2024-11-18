@extends('layouts.master')

@section('title')
    @lang('translation.DeliveryLineMapping')
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-10">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Add Delivery Line</h4>
                    <form action="{{ route('delivery-lins-mapping.store') }}" method="post" name="form" id="form" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <!-- Success Message Display -->
                            @if(session('success_message')) 
                                <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success_message') }}</div>
                            @elseif(session('already_created_message')) 
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">{{ session('already_created_message') }}</div>
                            @endif

                    
                            <!-- Form Fields -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">From Date</label>
                                    <input type="date" class="form-control" id="from_date" name="fromdate" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                    @error('date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">To Date</label>
                                    <input type="date" class="form-control" id="to_date" name="to_date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                    @error('date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                    
                            <!-- Other Form Fields -->
                            <div class="col-md-12">
                                <div class="alert alert-info alert-dismissible fade show" id="alert_message" role="alert">
                                    hi...! wait for response.
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
                                                    <input type="hidden" name="delivery_line_ids[]" value="{{ $line->id }}">
                                                    {{ $line->name }}
                                                </td>
                                                <td>
                                                    <select id="delivery_staff_id" data-delivery-line-id='{{ $line->id }}' name="delivery_staff_id[]" class="form-select ">
                                                        <option value="">--- Please Select ---</option>
                                                        @foreach ($delivery_staff as $staff)
                                                            <option value="{{ $staff->id }}"
                                                                @if (isset($data)) 
                                                                    @foreach ($data as $mapping)
                                                                        @if ($mapping->delivery_line_id == $line->id && $mapping->delivery_staff_id == $staff->id)
                                                                            selected 
                                                                        @endif
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
                    
                            <!-- Submit Button -->
                            <div class="col-md-12">
                                <div class="mb-3 d-flex justify-content-end">
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
        // alert();

        $('#from_date, #to_date').on('change', function() {
            var fromDate = new Date($('#from_date').val());
            var toDate = new Date($('#to_date').val());
            var today = new Date(); 
            today.setHours(0, 0, 0, 0); 

            if (fromDate < today) {
                alert('From Date cannot be in the past.');
                $('#from_date').val("{{ \Carbon\Carbon::now()->format('Y-m-d') }}"); 
            }

            if (toDate < today) {
                alert('To Date cannot be in the past.');
                $('#to_date').val("{{ \Carbon\Carbon::now()->format('Y-m-d') }}"); 
            }

            if (toDate < fromDate) {
                alert('To Date cannot be earlier than From Date.');
                $('#to_date').val($('#from_date').val()); 
            }
 
            if (fromDate >= today && toDate >= today && toDate >= fromDate) {
                datafetch(); 
            }
        });


        $(document).ready(function() {
            $('#route').addClass('mm-active');
            $('#delivery_menu').addClass('mm-show');
            $('#delivery-lins-mapping').addClass('mm-active');
            datafetch();
        });
    </script>
    <script>
        $('#form').on('submit', function(e) {
            let valid = true;
    
            $('select[name="delivery_staff_id[]"]').each(function() {
                if (!$(this).val()) {
                    valid = false;
                    const unselectedDropdown = $(this); // Capture the dropdown element
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Missing Selection',
                        text: 'Please select a delivery staff for all delivery lines.',
                    }).then(() => {
                        // After alert closes, focus on the unselected dropdown
                        unselectedDropdown.focus();
                        // Add a temporary error highlighting class
                        unselectedDropdown.addClass('is-invalid');
                    });
    
                    // Remove the error class when the dropdown gains focus
                    unselectedDropdown.on('focus', function() {
                        $(this).removeClass('is-invalid');
                    });
    
                    return false; // Exit loop
                }
            });
    
            if (!valid) e.preventDefault(); // Stop form submission
        });
    </script>

@endsection
