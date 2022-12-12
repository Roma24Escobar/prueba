<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::all();
        return $this->getResponse200($orders);
    }
    public function findByUserId($id)
    {
        //$orders = DB::select('select * from orders join products on orders.product_id = products.id join stores on orders.store_id =
        //stores.id where orders.user_id= ? and orders.status= 0',[$id]);
        $orders = Order::with("product","store")->where('user_id', '=', $id)->get();
        return $this->getResponse200($orders);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
            'amount' => 'required',
            'payment' => 'required',
            'store_id' => 'required',
            'product_id' => 'required',
            'user_id' => 'required'
        ]);
        if (!$validator->fails()) {
            DB::beginTransaction();
            try {
                $order = new Order();
                $order->status = $request->status;
                $order->amount = $request->amount;
                $order->payment = $request->payment;
                $order->product_id = $request->product_id;
                $order->store_id = $request->store_id;
                $order->user_id = $request->user_id;
                $order->save();
                DB::commit();
                return $this->getResponse201('order', 'created', $order);
            } catch (Exception $e) {
                DB::rollBack();
                return $this->getResponse500([$e->getMessage()]);
            }
        } else {
            return $this->getResponse500([$validator->errors()]);
        }
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
            'amount' => 'required',
            'payment' => 'required',
            'store_id' => 'required',
            'product_id' => 'required',
            'user_id' => 'required',
        ]);
        if (!$validator->fails()) {
            $order = Order::find($id);
            DB::beginTransaction();
            try {
                $order->status = $request->status;
                $order->amount = $request->amount;
                $order->payment = $request->payment;
                $order->product_id = $request->product_id;
                $order->store_id = $request->store_id;
                $order->user_id = $request->user_id;
                $order->save();
                DB::commit();
                return $this->getResponse201('order', 'updated', $order);
            } catch (Exception $e) {
                DB::rollBack();
                return $this->getResponse500([$e->getMessage()]);
            }
        }else{
            return $this->getResponse500([$validator->errors()]);
        }
    }

    public function show($id)
    {
        $order = Order::find($id);
        DB::beginTransaction();
        if ($order != null) {
            return $this->getResponse200($order);
        } else {
            return $this->getResponse404();
        }
    }

    public function destroy($id)
    {
        $order = Order::find($id);
        if ($order != null) {
            $order->delete();
            return $this->getResponse200($order);
        } else {
            return $this->getResponse404();
        }
    }
}
