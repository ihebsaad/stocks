<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        //$settings=Setting::get();
        return view('settings.index');
    }



    public function update_setting(Request $request)
    {
        $model=$request->get('model');
        $model_id=$request->get('model_id');
        $value=$request->get('value');

        Setting::where('model',$model)->where('model_id',$model_id)->update(
            [
                'value'=>$value,
            ]
        );
    }

    public function update_text(Request $request)
    {
        $model=$request->get('model');
        $model_id=$request->get('model_id');
        $value=$request->get('value');

        Setting::where('model',$model)->where('model_id',$model_id)->update(
            [
                'text'=>$value,
            ]
        );
    }


}
