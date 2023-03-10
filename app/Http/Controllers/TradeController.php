<?php

namespace App\Http\Controllers;

use App\Models\Trade;
use Illuminate\Http\Request;

class TradeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $request->user()->trades->with(['crypto'])->toArray();
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Trade $trade)
    {
        $this->authorize('view', $trade);
        $result = $trade->toArray();
        $result['crypto'] = $trade->crypto->toArray();
        foreach($trade->users as $user) {
            if($user->id == $request->user()->id){
                $request['role'] = $user->pivot->role;
            }
        }
        return $result;
    }
}
