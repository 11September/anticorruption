<?php

namespace App\Http\Controllers;

use App\Document;
use Illuminate\Http\Request;

class DocumentsController extends Controller
{
    public function delete(Request $request)
    {
        $comment = Document::find($request->id);

        $comment->delete();

        return response(['message' => 'Successfully deleted!'], 200);
    }
}
