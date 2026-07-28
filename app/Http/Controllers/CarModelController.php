<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CarModelModel;
use App\Models\ColorModel;
use App\Models\CarImage;
use Illuminate\Support\Facades\Storage;

class CarModelController extends Controller
{
    // --- Car Model Section ---------------------------------------------------------------

    public function carModelIndex()
    {
        $carModels = CarModelModel::orderBy('id', 'desc')->get();
        return view('CarMasters.car_model_index', compact('carModels'));
    }

    public function createCarModel()
    {
        $colors = ColorModel::get();
        return view('CarMasters.add_car_model', compact('colors'));
    }

    public function storeCarModel(Request $request)
    {
        $request->validate([
            'model_name' => 'required|unique:car_models,model_name', // បន្ថែម Unique ការពារឈ្មោះស្ទួន
        ], [
            'model_name.required' => 'សូមបញ្ចូលឈ្មោះម៉ូដែលឡាន!',
            'model_name.unique' => 'ឈ្មោះម៉ូដែលនេះមានរួចហើយ!',
        ]);

        $carModel = new CarModelModel();
        $carModel->model_name = $request->model_name;
        $carModel->save();

        // 🌟 កែត្រង់នេះ៖ បើ User មកពីទំព័រ Logistics វានឹងរុញទៅវិញដោយស្វ័យប្រវត្តិ
        return redirect()->back()->with('success', 'The Car Model is Created Successfully!');
    }

    public function editCarModel($id)
    {
        $ID = decrypt($id);
        $carModels = CarModelModel::findOrFail($ID);
        return view('CarMasters.edit_car_model', compact('carModels'));
    }

    public function updateCarModel(Request $request, $id)
    {
        $ID = decrypt($id);
        $carModels = CarModelModel::findOrFail($ID);
        
        $request->validate([
            'model_name' => 'required|unique:car_models,model_name,' . $ID,
        ]);

        $carModels->update($request->all());
        return redirect('car-models')->with('info', 'The Car Model is Updated Successfully!');
    }

    public function destroyCarModel($id)
    {
        $ID = decrypt($id);
        CarModelModel::findOrFail($ID)->delete();
        return redirect('car-models')->with('error', 'The Car Model is Deleted successfully.');
    }

    // --- Car Image Section ---------------------------------------------------------------

    public function imageCar($path)
    {
        $storagePath = storage_path('app/public/carImages/' . $path);
        if (!file_exists($storagePath)) {
            abort(404, 'File not found');
        }
        return response()->file($storagePath);
    }

    public function carImageIndex()
    {
        $carImages = CarImage::select(
                'car_images.*',
                'car_models.model_name as modelName',
                'colors.color_name as colorName'
            )
            ->join('car_models', 'car_models.id', '=', 'car_images.model_id')
            ->join('colors', 'colors.id', '=', 'car_images.color_id')
            ->orderBy('car_images.id', 'desc')
            ->get();

        return view('CarMasters.CarImage.car_image_index', compact('carImages'));
    }

    public function createCarImage() {
        $colors = ColorModel::get();
        $carModels = CarModelModel::get();
        // ត្រូវប្រាកដថា Path របស់ File Blade គឺត្រឹមត្រូវ
        return view('CarMasters.CarImage.add_car_image', compact('colors', 'carModels'));
    }

    public function storeCarImage(Request $request)
    {
        $request->validate([
            'image_name' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'model_id'   => 'required',
            'color_id'   => 'required'
        ], [
            'image_name.required' => 'Please select an image.',
            'model_id.required'   => 'Please select a model name.',
            'color_id.required'   => 'Please select a color.'
        ]);

        $carImage = new CarImage();
        $carImage->model_id = $request->model_id;
        $carImage->color_id = $request->color_id;

        if ($request->hasFile('image_name')) {
            $file = $request->file('image_name');
            $name = md5($file->getFilename() . time()) . '.' . $file->getClientOriginalExtension();
            $file->move(storage_path("app/public/carImages"), $name);
            $carImage->image_name = $name;
        }

        $carImage->save();
        return redirect('/car-images/create')->with('success', 'The Car Image is Created Successfully!');
    }

    public function editCarImage($id)
    {
        $ID = decrypt($id);
        $carImage = CarImage::findOrFail($ID);
        $colors = ColorModel::get();
        $carModels = CarModelModel::get();
        return view('CarMasters.CarImage.edit_car_image', compact('carImage', 'colors', 'carModels'));
    }

    public function updateCarImage(Request $request, $id)
    {
        $ID = decrypt($id);
        $carImage = CarImage::findOrFail($ID);

        if ($request->hasFile('image_name')) {
            // លុបរូបចាស់ចោលសិន (ជម្រើសល្អដើម្បីកុំឱ្យពេញ Storage)
            if ($carImage->image_name && file_exists(storage_path("app/public/carImages/" . $carImage->image_name))) {
                unlink(storage_path("app/public/carImages/" . $carImage->image_name));
            }

            $file = $request->file('image_name');
            $name = md5($file->getFilename() . time()) . '.' . $file->getClientOriginalExtension();
            $file->move(storage_path("app/public/carImages"), $name);
            $carImage->image_name = $name;
        }

        $carImage->model_id = $request->model_id;
        $carImage->color_id = $request->color_id;
        $carImage->save();

        return redirect('car-images')->with('info', 'The Car Image is Updated Successfully!');
    }

    public function destroyCarImage($id)
    {
        $ID = decrypt($id);
        $carImage = CarImage::findOrFail($ID);

        // លុបរូបភាពចេញពី Folder
        if ($carImage->image_name && file_exists(storage_path("app/public/carImages/" . $carImage->image_name))) {
            unlink(storage_path("app/public/carImages/" . $carImage->image_name));
        }

        $carImage->delete();
        return redirect('car-images')->with('error', 'The Car Image is deleted Successfully!');
    }

    // --- Colors Section ------------------------------------------------------------------

    public function colorsIndex()
    {
        $colors = ColorModel::orderBy('id', 'desc')->get();
        return view('CarMasters.Colors.color_index', compact('colors'));
    }

    public function createColor()
    {
        return view('CarMasters.Colors.add_color');
    }

    public function storeColor(Request $request)
    {
        $request->validate([
            'color_name' => 'required',
            'code'       => 'required'
        ]);

        ColorModel::create($request->all());
        return redirect('/colors/create')->with('success', 'The Color is Created Successfully!');
    }
}