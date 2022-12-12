<?php

namespace App\Http\Controllers;

use App\Models\Shelve;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ShelveController extends Controller
{
    public function index()
    {
        $shelves = Shelve::all();
        return $this->getResponse200($shelves);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
        if (!$validator->fails()) {
            DB::beginTransaction();
            try {
                $shelve = new Shelve();
                $shelve->name = $request->name;
                $shelve->save();
                DB::commit();
                return $this->getResponse201('shelve', 'created', $shelve);
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
        $shelve = Shelve::find($id);
        if ($shelve != null) {
            $shelve->delete();
            return $this->getResponse200($shelve);
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
            $shelve = Shelve::find($id);
            DB::beginTransaction();
            try {
                $shelve->name = $request->name;
                $shelve->save();
                DB::commit();
                return $this->getResponse201('shelve', 'updated', $shelve);
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
        $shelve = Shelve::find($id);
        if ($shelve != null) {
            $shelve->delete();
            return $this->getResponseDelete200("shelve");
        }else{
            return $this->getResponse404();
        }
    }
}
