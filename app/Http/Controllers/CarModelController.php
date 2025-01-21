<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CarModelModel;
use App\Models\ColorModel;
use App\Models\CarImage;

class CarModelController extends Controller
{
    // Car Model ---------------------------------------------------------------------------
    public function carModelIndex(){
        $carModels = CarModelModel::get();
        return view('CarMasters.car_model_index',['carModels'=>$carModels]);
    }

    public function createCarModel(){
        $colors = ColorModel::get();
        return view('CarMasters.add_car_model',['colors'=>$colors]);
    }

    public function storeCarModel(Request $request){
        $request->validate([
            'model_name' => 'required',
            'color_id' => 'required'
        ],
            [
                'model_name' => 'please enter model name.',
                'color_id' => 'please enter color id.'
            ]
        );
        $carModels = new CarModelModel();
        $carModels->model_name = $request->model_name;
        $carModels->color_id = $request->color_id;
        $carModels->save();
        return redirect('/car-models/create')->with('success', 'The Car Model is Created Succesfully!');
    }

    public function editCarModel($id){
        $ID = decrypt($id);
        
    }

    // Car image ---------------------------------------------------------------------------
    public function imageCar($path){
        $storagePath = storage_path('app/public/carImages/'.$path);
        if (!file_exists($storagePath)) {
            abort(404, 'File not found');
        }
        return response()->file($storagePath);
    }

    public function carImageIndex(){
        $carImages = CarImage::selectRaw(
            '
            car_images.image_name,
            car_images.created_at,
            car_models.model_name as modelName,
            colors.color_name as colorName
            '    
        )
        ->join('car_models','car_models.id','=','car_images.model_id')
        ->join('colors','colors.id', '=', 'car_images.color_id')
        ->get();
        return view('CarMasters.CarImage.car_image_index',['carImages'=>$carImages]);
    }

    public function createCarImage(){
        $colors = ColorModel::get();
        $carModels = CarModelModel::get();
        return view('CarMasters.CarImage.add_car_image',['colors'=>$colors,'carModels'=>$carModels]);
    }

    public function storeCarImage(Request $request){
        $request->validate([
            'image_name' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'model_id' => 'required',
            'color_id' => 'required'
        ],
            [
                'image_name.required' => 'please select image.',
                'image_name.image' => 'The uploaded file must be an image.',
                'image_name.mimes' => 'Only JPEG, PNG, and JPG formats are allowed.',
                'image_name.max' => 'Profile picture must not exceed 2MB.',
                'model_name' => 'please select model name.',
                'color_id' => 'please select color.'
            ]
        );
        $carImage = new CarImage();
        $carImage->model_id = $request->model_id;
        $carImage->color_id = $request->color_id;
        $file = $request->file('image_name');
        if(!empty($file)){
            $name = md5($file->getFilename() . time()) . '.' . $file->getClientOriginalExtension();
            $file_dir = 'carImages';
            $file->move(storage_path("app/public/$file_dir"), $name);
            $carImage->image_name = $name;
        }
        $carImage->save();
        return redirect('/car-images/create')->with('success', 'The Car Image is Created Succesfully!');
    }

    // Colors ---------------------------------------------------------------------------
    public function colorsIndex(){
        $colors = ColorModel::get();
        return view('CarMasters.Colors.color_index',['colors'=>$colors]);
    }

    public function createColor(){
        return view('CarMasters.Colors.add_color');
    }

    public function storeColor(Request $request){
        $request->validate([
            'color_name' => 'required',
            'code' => 'required'
        ],
            [
                'color_name' => 'please enter color name.',
                'code' => 'please enter code.'
            ]
        );
        $colors = new ColorModel();
        $colors->color_name = $request->color_name;
        $colors->code = $request->code;
        $colors->save();
        return redirect('/colors/create')->with('success', 'The Color is Created Succesfully!');
    }

    public function editColors(){

    }
}
