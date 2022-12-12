<?php

namespace App\Http\Controllers;
 
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::all();
        return $this->getResponse200($stores);
    }

    public function findByUserId($id)
    {
        $stores= DB::select('select * from stores where user_id = ?', [$id]);
        return $this->getResponse200($stores);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'ubication' => 'required'
        ]);
        if (!$validator->fails()) {
            DB::beginTransaction();
            try {
                $store = new Store();
                $store->name = $request->name;
                $store->ubication = $request->ubication;
                $store->save();
                DB::commit();
                return $this->getResponse201('store', 'created', $store);
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
        $store = Store::find($id);
        if ($store != null) {
            $store->delete();
            return $this->getResponse200($store);
        }else{
            return $this->getResponse404();
        }
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'ubication' => 'required'
        ]);
        if (!$validator->fails()) {
            $store = Store::find($id);
            DB::beginTransaction();
            try {
                $store->name = $request->name;
                $store->ubication = $request->ubication;
                $store->save();
                DB::commit();
                return $this->getResponse201('store', 'updated', $store);
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
        $store = Store::find($id);
        if ($store != null) {
            $store->delete();
            return $this->getResponseDelete200("store");
        }else{
            return $this->getResponse404();
        }
    }
}
