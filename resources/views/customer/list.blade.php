@extends('layouts.master')

@section('title') @lang('translation.Customers') @endsection

@section('content')

@php
$calendarEvents = [];
foreach ($data as $dat) {
$calendarEvents[] = [
'title' => $dat['product'] . ' (' . $dat['quantity'] . ' * ' . $dat['unit'] . ' ' . $dat['measurement'] . ')',
'start' => $dat['date'], // Ensure this is in 'YYYY-MM-DD' format
];
}
@endphp

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        @if (Session::has('success_message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Success :</strong> {{ Session::get('success_message') }}
                        </div>
                        @endif
                    </div>
                    <div class="col-6">
                        <!--<a href="add_person"><p class="btn btn-outline-secondary waves-effect" style="float:right;">Add ++</p></a>-->
                    </div>
                </div>
                <div class="row">
                    <h5 class="text-center">Subscription Remaings</h5>

                    <div id="calendar"></div>
                </div>
                <div class="row mt-3">
                    <h5 class="text-center">Order History</h5>

                    <!-- <div id="calendar"></div> -->
                     <div class="col-12">
                     <table id="example" class="table table-bordered dt-responsive nowrap w-100 mt-3">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Date</th>
                                <th>Client Name</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Milli Liters</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="tab_values">
                            @php
                            use Carbon\Carbon;
                                $totalQuantity = 0;
                                $i = 1;
                                /*dd($delivery_list);*/
                            @endphp
                            @foreach($order_list as $dlist)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $dlist->delivery_date ? Carbon::parse($dlist->delivery_date)->format('j-F-Y') : 'N/A' }}</td>
                                    <td>{{ $dlist->customer_id }}</td>
                                    <td>{{ $dlist->name }}</td>
                                    <td>{{ $dlist->quantity }}</td>
                                    <td>250</td>
                                    <td>{{ $dlist->delivery_status }}</td>
                                </tr>
                                @php
                                    $totalQuantity += $dlist->quantity;
                                    $i++;
                                @endphp
                            @endforeach
                        </tbody>
                    </table>
                     </div>
                </div>
                <div class="row mt-3">
                    <h4>Customer Wallet Balance:{{$wallet->current_amount}}/- Rupees</h4>
                    <h5 class="text-center">Wallet History</h5>

                    <!-- <div id="calendar"></div> -->
                     <div class="col-12">
                     <table id="example" class="table table-bordered dt-responsive nowrap w-100 mt-3">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Amount</th>
                                
                            </tr>
                        </thead>
                        <tbody id="tab_values">
                            @php
                            
                               
                                $i = 1;
                                /*dd($delivery_list);*/
                            @endphp
                            @foreach($history as $dlist)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $dlist->created_at->format('d-m-y') }}</td>
                                    <td>{{ $dlist->debit_credit_status }}</td>
                                    <td>{{ $dlist->amount }}</td>
                                    </tr>
                                @php
                                    
                                    $i++;
                                @endphp
                            @endforeach
                        </tbody>
                    </table>
                     </div>
                </div>
            </div>
                <div class="row mt-3">
                    <!-- <h4></h4> -->
                    <h5 class="text-center">Deliverd List</h5>

                    <!-- <div id="calendar"></div> -->
                     <div class="col-12">
                     <table id="example" class="table table-bordered dt-responsive nowrap w-100 mt-3">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Date</th>
                                <th>Client Name</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                
                                <th>Unit Name</th>
                                <th>Delivery Person</th>
                                <th>Status</th>
                                <th>Rating</th>
                                <th>Comment</th>
                                <th>Image</th>
                                <!-- <th>Action</th> -->
                            </tr>
                        </thead>
                        <tbody id="tab_values">
                            @php
                                $totalQuantity = 0;
                                $i = 1;
                            @endphp
                            @foreach ($delivered as $dlist)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ Carbon::parse($dlist['date'])->format('j-F-Y') }}</td>
                                    <td>{{ $dlist['subscription_customer_id'] }}</td>
                                    <td>{{ $dlist['subscription_products_id'] }}</td>
                                    <td>{{ $dlist['subscription_quantity'] }}</td>
                                    <td>{{ $dlist['unit_name']."".$dlist['measurement_name']  }}</td>
                                    
                                    <td>{{ $dlist['deliveryperson_id'] }}</td>
                                    <td>{{ $dlist['delivery_status'] }}</td>
                                    <td>{{ $dlist['rating'] }}</td>
                                    <td>{{ $dlist['comments'] }}</td>
                                    <td>
                                        @if ($dlist->pic)
                                            <img src="{{ asset($dlist->pic) }}" alt="Image"
                                                style="width: 100px; height: 100px;">
                                        @else
                                            No Image
                                        @endif
                                    </td>

                                </tr>
                                @php
                                    //$totalQuantity += $dlist['subscription_total_qty'];
                                    $i++;
                                @endphp
                            @endforeach
                        </tbody>
                    </table>
                     </div>
                </div>
            </div>
        </div>
    </div> <!-- end col -->
</div>

@endsection

@section('script')
<!-- Include FullCalendar's CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/index.global.min.css" rel="stylesheet" />

<!-- Include FullCalendar's JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/index.global.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        // Output the events data to the console for debugging
        console.log(@json($calendarEvents));

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: @json($calendarEvents), // Use actual data
            eventTimeFormat: { // Customize the event time format
                hour: '2-digit',
                minute: '2-digit',
                hour12: false // This will hide the time
            },
            eventContent: function(arg) {
                let eventTitle = document.createElement('div');
                eventTitle.classList.add('fc-event-title');
                eventTitle.innerText = arg.event.title;

                let arrayOfDomNodes = [eventTitle]
                return {
                    domNodes: arrayOfDomNodes
                }
            }
        });

        calendar.render();
    });
</script>

<script>
    $(document).ready(function() {
        $('#customer').addClass('mm-active');
        $('#customer_menu').addClass('mm-show');
        $('#customer').addClass('mm-active');
    });

    
</script>
@endsection