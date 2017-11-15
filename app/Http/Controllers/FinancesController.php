<?php

namespace App\Http\Controllers;

use App\Finance;
use Illuminate\Http\Request;

class FinancesController extends Controller
{
    public function delete(Request $request)
    {
        $comment = Finance::find($request->id);

        $comment->delete();

        return response(['message' => 'Successfully deleted!'], 200);
    }
}
