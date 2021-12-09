<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Faq;

class FaqController extends Controller
{
    /**
     * Index
     * 
     * @return view
     */
    public function view()
    {
        return view('faq');
    }

    public function list(Request $request)
    {
        $request->validate(
            [
                'search' => 'nullable|string|max:100'
            ]
        );

        $data = Faq::select('id', 'question', 'answer')
            ->where(function($query) use($request) {
                if ($request->search)
                {
                    $query->orWhere('question', 'ilike', '%'.$request->search.'%');
                    $query->orWhere('answer', 'ilike', '%'.$request->search.'%');
                }
            })
            ->orderBy('question', 'asc')
            ->get();

        return [
            'success' => 'ok',
            'data' => $data
        ];
    }
}
