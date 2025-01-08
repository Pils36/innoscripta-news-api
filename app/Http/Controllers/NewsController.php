<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::query();

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('date')) {
            $query->whereDate('published_at', $request->date);
        }

        return response()->json($query->paginate(10));
    }

    public function search(Request $request)
    {
        $query = Article::query();

        if ($request->filled('q')) {
            $query->where('title', 'LIKE', "%{$request->q}%")
            ->orWhere('content', 'LIKE', "%{$request->q}%");
        }

        return response()->json($query->paginate(10));
    }
}
