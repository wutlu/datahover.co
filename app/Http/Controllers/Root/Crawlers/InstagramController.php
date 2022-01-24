<?php

namespace App\Http\Controllers\Root\Crawlers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Http\Requests\IdRequest;

use App\Models\InstagramAccount;
use App\Models\Option;

class InstagramController extends Controller
{
    public function view()
    {
        $status = (new Option)->get('instagram.status', true);

        return view('root.crawlers.instagram', compact('status'));
    }

    public function accounts(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:1000',
            'skip' => 'required|integer|max:1000000',
            'take' => 'required|integer|max:1000',
        ]);

        $data = InstagramAccount::where(function($query) use($request) {
                if ($request->search)
                    $query->orWhere('email', 'ilike', '%'.$request->search.'%');
            })
            ->skip($request->skip)
            ->take($request->take)
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'success' => 'ok',
            'data' => $data,
            'stats' => [
                'total' => InstagramAccount::count()
            ]
        ];
    }

    public function actionAccount(IdRequest $request)
    {
        $request->validate(
            [
                'email' => 'required|email',
                'password' => 'required|string|max:64',
                'sessionid' => 'required|string|max:128'
            ]
        );

        if ($id = $request->id)
        {
            $item = InstagramAccount::findOrFail($id);
            $validate = "unique:instagram_accounts,email,$id";
            $action = 'update';
        }
        else
        {
            $item = new InstagramAccount;
            $validate = "unique:instagram_accounts,email";
            $action = 'create';
        }

        $request->validate([ 'email' => $validate ]);

        $item->email = $request->email;
        $item->password = $request->password;
        $item->sessionid = $request->sessionid;
        $item->error_hit = 0;
        $item->error_reason = null;
        $item->status = 'normal';
        $item->save();

        return [
            'success' => 'ok',
            'alert' => [
                'type' => 'success',
                'message' => $action == 'create' ? 'Account was created' : 'Account was updated'
            ]
        ];
    }

    public function deleteAccount(Request $request)
    {
        $request->validate([
            'id' => 'required|array',
            'id.*' => 'required_with:id|integer',
        ]);

        $tracks = InstagramAccount::whereIn('id', $request->id)->delete();

        return [
            'success' => 'ok',
            'alert' => [
                'type' => 'success',
                'message' => 'Items was deleted'
            ]
        ];
    }

    public function getAccount(IdRequest $request)
    {
        $account = InstagramAccount::findOrFail($request->id);

        return [
            'success' => 'ok',
            'data' => $account
        ];
    }
}
