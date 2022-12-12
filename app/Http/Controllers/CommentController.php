<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{

    public function index()
    {
        $comments = Comment::all();
        return $this->getResponse200($comments);
    }
    public function findById($id)
    {
        $comments= DB::select('select * from comments where store_id = ?', [$id]);
        return $this->getResponse200($comments);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required',
            'user_id' => 'required',
            //'photo' => 'required',
        ]);
        if (!$validator->fails()) {
            DB::beginTransaction();
            try {
                $comment = new Comment();
                $comment->description = $request->description;
                $comment->photo = $request->photo;
                $comment->store_id = $request->store_id;
                $comment->user_id = $request->user_id;
                $comment->save();
                DB::commit();
                return $this->getResponse201('comment', 'created', $comment);
            } catch (Exception $e) {
                DB::rollBack();
                return $this->getResponse500([$e->getMessage()]);
            }
        }else{
            return $this->getResponse500([$validator->errors()]);
        }
    }
    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'description' => 'required',
            //'photo' => 'required',
            'store_id' => 'required',
            'user_id' => 'required',
        ]);
        if(!$validator->fails()){
            $comment = Comment::find($id);
            DB::beginTransaction();
            try{
                $comment->description = $request->description;
                $comment->photo = $request->photo;
                $comment->store_id= $request->store_id;
                $comment->user_id = $request->user_id;
                DB::commit();
                return $this->getResponse201('comment', 'updated', $comment);
            }catch(Exception $e){
                DB::rollBack();
                return $this->getResponse500([$e->getMessage()]);
            }
        }else{
            return $this->getResponse500([$validator->errors()]);
        }
    }

    public function show($id)
    {
        $comment = Comment::find($id);
        DB::beginTransaction();
        if ($comment != null) {
            return $this->getResponse200($comment);
        } else {
            return $this->getResponse404();
        }
    }

    public function destroy($id)
    {
        $comment = Comment::find($id);
        if ($comment != null) {
            $comment->delete();
            return $this->getResponse200($comment);
        } else {
            return $this->getResponse404();
        }
    }
}
