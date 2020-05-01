<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function root()
    {
        // echo phpinfo();
        // return view('pages.root');
    }

    public function weixin_redirect(Request $request)
    {
        $redirect_uri = $request->redirect_uri;
        $redirect = $request->redirect;
        $app_id = config('trading.app_id');

        if (empty($redirect_uri) || empty($redirect) || empty($app_id)) {
            return '404 not found';
        }

        $redirect_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $app_id . '&redirect_uri=' . urlencode($redirect_uri) . '&response_type=code&scope=snsapi_userinfo&state=' . urlencode($redirect) . '#wechat_redirect';

        return redirect()->away($redirect_url);
    }
}
