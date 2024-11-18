

<style>
    .btn-outline-secondary {
        border-color: #255328 !important;
    }
</style>
<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">
    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start1111 -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" key="t-menu">@lang('translation.Menu')</li>
                <li>
                    <a href="{{ url('index') }}" class="waves-effect">
                        <i class="bx bx-home-circle"></i>
                        <span key="t-dashboards">@lang('translation.Dashboards')</span>
                    </a>
                </li>
                <li id="masters" class="">
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-bar-chart-square"></i>
                        <span key="t-masters">@lang('translation.Masters')</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false" id="master_menu">
                        <li id="category" class=""><a href="{{ url('list_category') }}"
                                key="t-category">@lang('translation.Category')</a></li>
                        <li id="product_name" class=""><a href="{{ route('product-name.index') }}"
                                key="t-product_name">@lang('translation.Product_Name')</a></li>
                        <li id="pincode" class=""><a href="{{ url('pincode') }}"
                                key="t-pincode">@lang('translation.Pincodes')</a></li>
                        <li id="area" class=""><a href="{{ route('area.index') }}"
                                key="t-area">@lang('translation.Area')</a></li>
                        <li id="delivery_lines" class=""><a href="{{ url('delivery-line') }}"
                                key="t-delivery_lines">@lang('translation.Delivery_lines')</a></li>
                        <li id="Route_master" class=""><a href="{{ route('route-assign') }}"
                                key="t-sproduct">@lang('translation.Route_master')</a></li>
                        <li id="delivery_person" class=""><a href="{{ url('delivery-person') }}"
                                key="t-delivery_person">@lang('translation.Delivery_persons')</a></li>
                        <li id="Measurement" class=""><a href="{{ route('measurement.index') }}"
                                key="t-Measurement">@lang('translation.Measurement')</a></li>
                        <li id="Unit" class=""><a href="{{ route('unit.index') }}"
                                key="t-Unit">@lang('translation.Unit')</a></li>
                        <li id="User" class=""><a href="{{ url('user_reg') }}"
                                key="t-Unit">@lang('translation.User')</a></li>
                        <li id="sproduct" class=""><a href="{{ url('inventry_list') }}"
                                key="t-sproduct">@lang('translation.inventary_list')</a></li>
                    </ul>
                </li>
                <li id="customer_main_menu" class="">
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class='bx bx-hdd'></i>
                        <span key="t-subscription">@lang('translation.Customers')</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false" id="customer_menu">
                    <li id="customer" class=""><a href="{{ url('list_customer')}}" key="t-order">@lang('translation.Customers_list')</a></li>
                    <li id="customer" class=""><a href="{{ url('new_customer')}}" key="t-order">@lang('translation.Customers_new')</a></li>
                    <li id="customer" class=""><a href="{{ url('edited_customer')}}" key="t-order">@lang('translation.edited_customer')</a></li>
                    </ul>
                </li>
                <li id="products_add" class="">
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class='bx bx-hdd'></i>
                        <span key="t-subscription">@lang('translation.products')</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false" id="product_add_menu">
                        <li id="product_add" class=""><a href="{{ url('list_product') }}"
                                key="t-product">@lang('translation.Add_Products')</a></li>
                    </ul>
                </li>
                <li id="subscription" class="">
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class='bx bx-hdd'></i>
                        <span key="t-subscription">@lang('translation.Subscription')</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false" id="subscription_menu">
                        <li id="sproduct" class=""><a href="{{ url('list_sproduct') }}"
                                key="t-sproduct">@lang('translation.Products')</a></li>
                        <li id="splans" class=""><a href="{{ url('supscription_plans') }}"
                                key="t-sproduct">@lang('translation.supscription_plans')</a></li>
                    </ul>
                </li>

                <li id="ecommerce" class="">
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class='bx bx-hdd'></i>
                        <span key="t-ecommerce">@lang('translation.Ecommerce')</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false" id="ecommerce_menu">
                        <li id="product" class=""><a href="{{ url('list_product_e') }}"
                                key="t-product">@lang('translation.Products')</a></li>
                        <li id="order" class=""><a href="{{ url('list_order') }}"
                                key="t-order">@lang('translation.Orders')</a></li>

                    </ul>
                </li>


                <li id="route" class="">
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class='bx bx-hdd'></i>
                        <span key="t-subscription">@lang('translation.delivery')</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false" id="delivery_menu">
                        <li id="sproduct" class=""><a href="{{ route('route-list') }}"
                                key="t-sproduct">@lang('translation.delivery_lines')</a></li>
                        <li id="delivery-lins-mapping" class=""><a
                                href="{{ route('delivery-lins-mapping.index') }}"
                                key="t-sproduct">@lang('translation.Delivery_schedule')</a></li>
                        <li id="route-mapping.add" class=""><a href="{{ route('route-mapping.index') }}"
                                key="t-sproduct">Delivery route mapping </a></li>
                    </ul>
                </li>
                <li id="wallet" class="">
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class='bx bx-hdd'></i>
                        <span key="t-subscription">@lang('translation.wallet')</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false" id="wallet_menu">
                        <!-- <li id="client_wallet_bal" class=""><a href="{{ url('client_wallet_bal') }}" key="t-sproduct">@lang('translation.client_wallet_bal')</a></li> -->
                        <li id="client_payment_history" class=""><a href="{{ url('client_payment_history') }}"
                                key="t-sproduct">@lang('translation.client_payment_history')</a></li>
                        <li id="client_wallet_history" class=""><a href="{{ url('client_wallet_history') }}"
                                key="t-sproduct">@lang('translation.wallet_history')</a></li>
                        <li id="wallet_plans" class=""><a href="{{ url('wallet_plans') }}"
                                key="t-sproduct">@lang('translation.wallet_plans')</a></li>


                    </ul>
                </li>

                <li id="reports" class="">
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class='bx bx-hdd'></i>
                        <span key="t-subscription">@lang('translation.reports')</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false" id="reports_sub_menu">
                        <li id="delivery_list" class=""><a href="{{ url('delivery_list') }}"
                                key="t-sproduct">@lang('translation.delivery_list')</a></li>
                        <!-- <li id="order_list" class=""><a href="{{ url('order_list') }}" key="t-sproduct">@lang('translation.order_list')</a></li> -->
                        <!-- <li id="subscriber_list" class=""><a href="{{ url('subscriber_list') }}" key="t-sproduct">@lang('translation.subscriber_list')</a></li> -->

                        <li id="gift_products" class=""><a href="{{ url('gift_products')}}" key="t-sproduct">@lang('translation.gift_products')</a></li>
                        <li id="gift_amount" class=""><a href="{{ url('gift_amount')}}" key="t-sproduct">@lang('translation.gift_amount')</a></li>
                        <li id="deliver_list_person" class=""><a href="{{ url('deliver_list_person')}}" key="t-sproduct">@lang('translation.deliver_list_person')</a></li>
                        <li id="rating_report" class=""><a href="{{ url('rating_report')}}" key="t-sproduct">@lang('translation.rating_report')</a></li>
                        <li id="subscription_list " class=""><a href="{{ url('subscription_list')}}" key="t-sproduct">@lang('translation.subscription_list')</a></li>
                        <li id="subscription_log " class=""><a href="{{ url('subscription_log')}}" key="t-sproduct">@lang('translation.subscription_log')</a></li>
                    </ul>
                </li>
                <li id="inventary" class="">
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class='bx bx-hdd'></i>
                        <span key="t-subscription">@lang('translation.inventry')</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false" id="inventary_menu">

                        <li id="vendor_buyer" class=""><a href="{{ url('vendor_buyer') }}"
                                key="t-sproduct">@lang('translation.vendor_buyer')</a></li>
                        <!-- <li id="subscriber_list" class=""><a href="{{ url('subscriber_list') }}" key="t-sproduct">@lang('translation.subscriber_list')</a></li> -->
                    </ul>
                </li>
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Print Button Hide  start-->
<script>
    $(document).ready(function() {
        $('.buttons-print').hide();
        // $('#wallet_menu').addClass('mm-show');
        // $('#client_payment_history').addClass('mm-active');
    });
    // Add a click event listener to buttons with the text "Update"
    $('button:contains("Update")').on('click', function(e) {
        alert();
    e.preventDefault(); // Prevent the form from submitting immediately

    // SweetAlert confirmation dialog
    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to update this category?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, update it!",
        cancelButtonText: "No, cancel",
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Find the parent form and submit it
            $(this).closest('form').submit();
        }
    });
});
</script>

<!-- Print Button Hide  end-->
