<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return $this->getResponse200($products);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required'
        ]);
        if (!$validator->fails()) {
            DB::beginTransaction();
            try {
                $product = new Product();
                $product->name = $request->name;
                $product->price = $request->price;
                $product->save();
                DB::commit();
                return $this->getResponse201('product', 'created', $product);
            } catch (Exception $e) {
                DB::rollBack();
                return $this->getResponse500([$e->getMessage()]);
            }
        } else {
            return $this->getResponse500([$validator->errors()]);
        }
    }

    public function show($id)
    {
        $product = Product::find($id);
        if ($product != null) {
            return $this->getResponse200($product);
        }else{
            return $this->getResponse404();
        }
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
        if (!$validator->fails()) {
            $product = Product::find($id);
            DB::beginTransaction();
            try {
                $product->name = $request->name;
                $product->save();
                DB::commit();
                return $this->getResponse201('product', 'updated', $product);
            } catch (Exception $e) {
                DB::rollBack();
                return $this->getResponse500([$e->getMessage()]);
            }
        } else {
            return $this->getResponse500([$validator->errors()]);
        }
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if ($product != null) {
            $product->delete();
            return $this->getResponseDelete200("product");
        }else{
            return $this->getResponse404();
        }
    }
}
