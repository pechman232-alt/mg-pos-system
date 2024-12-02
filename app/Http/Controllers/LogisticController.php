<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogisticController extends Controller
{
    public function logisticIndex(){
        return view('Logistics.logistic_index');
    }

    public function createLogistics(){
        return view('Logistics.add_logistic');
    }

    public function storeLogistics(Request $request){
        $request->validate([
            ''
            ]
        );
    }

}
