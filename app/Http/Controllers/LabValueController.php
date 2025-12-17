<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class LabValueController extends Controller
{

    public function create()
    {
        $data['settings'] = new Setting();

        return view('lab_values.create', $data);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        unset($data['_token']);
        Setting::setSetting($data);

        return back()->with('success' , 'Done Successfully');
    }

}
