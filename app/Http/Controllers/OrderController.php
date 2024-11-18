<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Measurement;
use App\Models\Unit;
use App\Models\Deliveryperson;
use App\Models\Area;
use App\Models\Pincode;


class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list(Request $request)
    {
        $post_values = $request->all();
        $area = Area::where('status', 'Y')->get();
        $pincode = Pincode::where('status', 'Active')->get();
        $delivery_person = Deliveryperson::where('status', 'Active')->get();

        // Initialize query
        $query = Order::select(
            'orders.order_id',
            DB::raw('MAX(orders.id) as id'),
            DB::raw('sum(orders.price) as price'),
            DB::raw('MAX(orders.delivery_date) as delivery_date'),
            DB::raw('MAX(orders.created_at) as created_at'),
            DB::raw('MAX(orders.delivery_status) as delivery_status'),
            DB::raw('MAX(orders.delivery_at) as delivery_at'),
            DB::raw('MAX(orders.customer_id) as customer_id'),
            DB::raw('MAX(orders.delivered_by) as delivered_by')
        )
            ->groupBy('orders.order_id')
            ->orderBy('id', 'DESC');

        if ($request->isMethod('post')) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $area1 = $request->area;
            $pincode1 = $request->pincode;
            if ($from_date && $to_date) {
                $query->whereBetween('orders.delivery_date', [$from_date, $to_date]);
            }
            if ($area1 != "") {
                $query->where('orders.area', $area1);
            }
            if ($pincode1 != "") {
                $query->where('orders.pincode', $pincode1);
            }
        }

        $order_list = $query->get();

        foreach ($order_list as $order) {
            $customer = Customer::find($order->customer_id);
            $order->customer_name = $customer ? $customer->name : 'N/A';

            $deliveryperson = Deliveryperson::find($order->delivered_by);
            $order->deliverypersons_name = $deliveryperson ? $deliveryperson->name : 'N/A';
        }

        return view('order.index')->with(compact('order_list', 'delivery_person', 'area', 'pincode', 'post_values'));
    }



    public function add()
    {
        return view('pincode/add_form');
    }

    public function update($id = null)
    {
        $pincode_details = Order::find($id);
        return view('pincode/update_form')->with(compact('pincode_details'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $data = $request->only([
            'pincode',
            'status'
        ]);
        $media = new Order(array_merge($data, [
            'created_by' => $user->id,
            'updated_by' => 0
        ]));

        if ($media->save()) {
            return redirect('pincode')->with('success_message', 'Pincode Created Sucessfully!..');
        } else {
            return redirect('pincode')->with('success_message', 'Something Wrong!..');
        }
    }

    public function update_store(Request $request)
    {
        $user = auth()->user();

        $data = $request->only([
            'id',
            'name',
            'status'
        ]);
        $media = new Order(array_merge($data, [
            'created_by' => $user->id,
            'updated_by' => $user->id
        ]));

        $media = Order::find($request->id);

        if ($media->update($request->all())) {
            return redirect('pincode')->with('success_message', 'Pincode Updated Sucessfully!..');
        } else {
            return redirect('pincode')->with('success_message', 'Something Wrong!..');
        }
    }
    public function order_list_view(Request $request)
    {
        $id = $request->id;
        $view = Order::where('order_id', $id)->get();
        $sel = "";
        $sel .= '<table class="table">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Clien Name</th>
      <th scope="col">Product</th>
      <th scope="col">Unit</th>
      <th scope="col">Measurement</th>
      <th scope="col">Quantity</th>
      <th scope="col">Price</th>
    </tr>
  </thead>
  <tbody>';


        $i = 0;

        $client = Customer::where('id', $view->customer_id)->first();
        $Cart = Cart::where('order_id', $id)->get();
        foreach ($Cart as $Carts) {
            $pro = Product::where('id', $Carts->product_id)->first();
            $mes = Measurement::where('id', $pro->measurement_id)->first();
            $qty = Unit::where('id', $pro->measurement_id)->first();
            $sel .= '<tr>
                <td scope="row">' . $i . '</td>
                <td>' . $client->name . " - " . $client->mobile . '</td>
                <td>' . $pro->name . '</td>

                <td>' . $mes->name . '</td>
                <td>' . $qty->name . '</td>
                <td>' . $Carts->quantity . '</td>
                <td>' . $Carts->price . '</td>
                </tr>';
            $i++;
        }
        $sel .= ' </tbody>
                            </table>';

        return response()->json(['success' => true, 'message' => 'check out successfully', 'tabel' => $sel]);
    }
}
