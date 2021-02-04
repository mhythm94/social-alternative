<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Reply;
use App\Tweet;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReplyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /* userId: 1
        token: 123456 */
        $rules = array(
            'userId' => 'required',
            'tweetId' => 'required',
            'token' => 'required',
            'body' => 'required|max:255'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $validator->errors();
        } else {

            $user = User::where('id', $request->userId)->get()->first();
            if ($user == null) {
                return response()->json(['error' => 'User not found'], 404);
            } else {
                $tweet = Tweet::where('id', $request->tweetId)->get()->first();
                if ($tweet == null) {
                    return response()->json(['error' => 'Tweet not found'], 404);
                } else {
                    if ($user->token == $request->token) {
                        $reply = Reply::create([
                            'user_id' => $request->userId,
                            'tweet_id' => $request->tweetId,
                            'body' => $request->body
                        ]);
                        if ($tweet) {
                            return response()->json(['success' => true, 'msg' => 'Reply published.', 'replyId' => $reply->id], 200);
                        }
                    } else {
                        return response()->json(['error' => 'User is not allowed to publish tweets'], 401);
                    }
                }
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $reply = Reply::find($id);
        if ($reply) {
            $reply = Reply::where('id', $id)->with('tweet')->get()->first();
            return response()->json($reply, 200);
        } else {
            return response()->json(['success' => false, 'msg' => 'Reply does not found'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $reply = Reply::find($id);
        if ($reply == null) {
            return response()->json(['success' => false, 'msg' => 'Reply does not found'], 404);
        } else {
            $reply->delete();
            return response()->json(['success' => true, 'msg' => 'Reply deleted'], 200);
        }
    }
}
