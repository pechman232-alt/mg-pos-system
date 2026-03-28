<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BranchModel;
use App\Models\ProvinceModel;

class HomeController extends Controller
{
    public function homePage(){
        $countBranch = BranchModel::count();
        $countProvince = ProvinceModel::count();

        return view('layouts.dashboard', ['countBranch'=>$countBranch, 'countProvince'=>$countProvince]);
    }
}



