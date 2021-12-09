<?php

namespace App\Http\Controllers\Root;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Faq;

class FaqController extends Controller
{
    public function view()
    {
        return view('root.faq');
    }

    public function action(Request $request)
    {
        $request->validate([
            'id' => 'nullable|integer',
            'question' => 'required|string|max:255',
            'answer' => 'required|string|max:1000',
        ]);

        $q = $request->id ? Faq::findOrFail($request->id) : new Faq;
        $q->fill($request->all());
        $q->save();

        return [
            'success' => 'ok',
            'alert' => [
                'type' => 'success',
                'message' => $request->id ? 'Item was updated' : 'Item was created'
            ],
            'data' => $q,
            'type' => $request->id ? 'edit' : 'create'
        ];
    }

    public function read(Request $request)
    {
        $request->validate([ 'id' => 'required|integer' ]);

        return [
            'success' => 'ok',
            'data' => Faq::findOrFail($request->id)
        ];
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|array',
            'id.*' => 'required_with:id|integer',
        ]);

        $tracks = Faq::whereIn('id', $request->id)->delete();

        return [
            'success' => 'ok',
            'alert' => [
                'type' => 'success',
                'message' => 'Items was deleted'
            ]
        ];
    }

    public function list(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:1000',
            'skip' => 'required|integer|max:1000000',
            'take' => 'required|integer|max:1000',
        ]);

        $data = Faq::where(function($query) use($request) {
                if ($request->search)
                {
                    $query->orWhere('question', 'ilike', '%'.$request->search.'%');
                    $query->orWhere('answer', 'ilike', '%'.$request->search.'%');
                }
            })
            ->skip($request->skip)
            ->take($request->take)
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'success' => 'ok',
            'data' => $data,
            'stats' => [
                'total' => Faq::count()
            ]
        ];
    }
}
