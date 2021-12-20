<?php

namespace App\Http\Controllers\Root;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Plan;
use App\Models\Session;

use Etsetra\Library\DateTime as DT;

class UserController extends Controller
{
    public function view()
    {
        $plans = Plan::get();

        return view('root.users', compact('plans'));
    }

    public function read(Request $request)
    {
        $request->validate([ 'id' => 'required|integer' ]);

        return [
            'success' => 'ok',
            'data' => User::findOrFail($request->id)
        ];
    }

    public function update(Request $request)
    {
        $request->validate([ 'id' => 'required|integer' ]);
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|max:255|unique:users,email,'.$request->id,
            'plan_id' => 'required|string|exists:plans,id',
            'subscription_end_date' => 'required|date',
            'is_root' => 'nullable|string|in:on'
        ]);

        $user = User::findOrFail($request->id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->plan_id = $request->plan_id;
        $user->subscription_end_date = $request->subscription_end_date;
        $user->is_root = $request->is_root ? true : false;
        $user->save();

        return [
            'success' => 'ok',
            'alert' => [
                'type' => 'success',
                'message' => 'User information updated'
            ],
            'data' => $user
        ];
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|array',
            'id.*' => 'required_with:id|integer',
        ]);

        $tracks = User::where('is_root', false)->whereIn('id', $request->id)->delete();

        return [
            'success' => 'ok',
            'alert' => [
                'type' => 'success',
                'message' => 'Selected successfully deleted'
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

        $data = User::where(function($query) use($request) {
                if ($request->search)
                {
                    $query->orWhere('name', 'ilike', '%'.$request->search.'%');
                    $query->orWhere('email', 'ilike', '%'.$request->search.'%');
                }
            })
            ->skip($request->skip)
            ->take($request->take)
            ->orderBy('name', 'desc')
            ->get();

        return [
            'success' => 'ok',
            'data' => $data,
            'stats' => [
                'total' => User::count(),
                'active' => User::where('subscription_end_date', '>=', (new DT)->nowAt())->count()
            ]
        ];
    }

    public function sessions()
    {
        return view('root.sessions');
    }

    public function sessionList(Request $request)
    {
        $request->validate([
            'skip' => 'required|integer|max:1000000',
            'take' => 'required|integer|max:1000',
        ]);

        $data = Session::with('user')
            ->skip($request->skip)
            ->take($request->take)
            ->orderBy('last_activity', 'desc')
            ->get();

        $ts = strtotime((new DT)->nowAt('-10 minutes'));

        return [
            'success' => 'ok',
            'data' => $data,
            'stats' => [
                'total' => Session::count(),
                'online' => Session::where('last_activity', '>=', $ts)->count()
            ],
            'ts' => $ts
        ];
    }
}
