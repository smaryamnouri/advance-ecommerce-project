<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Filters\CategoryFilter;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryResourceCollection;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{

    /**
     * @OA\Get(path="/api/categories",
     *   tags={"Categories"},
     *   summary="Returns categories as json",
     *   description="Returns categories",
     *   operationId="getCategories",
     *   parameters={},
     *   @OA\Response(
     *     response=200,
     *     description="successful operation",
     *     @OA\Schema(
     *       additionalProperties={
     *         "type":"integer",
     *         "format":"int32"
     *       }
     *     )
     *   )
     * )
     */
    public function index(CategoryFilter $filters)
    {
        [$entries, $count, $sum] = Category::filter($filters);
        $entries = $entries->get();
        return response(new CategoryResourceCollection(['data' => $entries, 'count' => $count]));
    }
    /**
     * @OA\Get(path="/api/categories/{categoryId}",
     *   tags={"Categories"},
     *   summary="Returns category by id as json",
     *   description="Returns category by id",
     *   operationId="getCategoryById",
     *
     *  @OA\Parameter(
     *       description="ID of category",
     *       name="categoryId",
     *       required=true,
     *       in="path",
     *       example="1",
     *       @OA\Schema(
     *           type="integer",
     *           format="int64"
     *       )
     *   ),
     *
     *   @OA\Response(
     *     response=200,
     *     description="successful operation",
     *     @OA\Schema(
     *       additionalProperties={
     *         "type":"integer",
     *         "format":"int32"
     *       }
     *     )
     *   )
     * )
     */
    public function show(int $id)
    {
        $entry = Category::query()->findOrFail($id);
        return response(new CategoryResource(['data' => $entry]));
    }

    public function CategoryView(){

    	$category = Category::latest()->get();
    	return view('backend.category.category_view',compact('category'));
    }

    public function CategoryStore(Request $request){

       $request->validate([
    		'category_name_en' => 'required',
    		'category_name_hin' => 'required',
    		'category_icon' => 'required',
    	],[
    		'category_name_en.required' => 'Input Category English Name',
    		'category_name_hin.required' => 'Input Category Hindi Name',
    	]);



	Category::insert([
		'category_name_en' => $request->category_name_en,
		'category_name_hin' => $request->category_name_hin,
		'category_slug_en' => strtolower(str_replace(' ', '-',$request->category_name_en)),
		'category_slug_hin' => str_replace(' ', '-',$request->category_name_hin),
		'category_icon' => $request->category_icon,

    	]);

	    $notification = array(
			'message' => 'Category Inserted Successfully',
			'alert-type' => 'success'
		);

		return redirect()->back()->with($notification);

    } // end method


    public function CategoryEdit($id){
    	$category = Category::findOrFail($id);
    	return view('backend.category.category_edit',compact('category'));

    }


    public function CategoryUpdate(Request $request ,$id){



      Category::findOrFail($id)->update([
		'category_name_en' => $request->category_name_en,
		'category_name_hin' => $request->category_name_hin,
		'category_slug_en' => strtolower(str_replace(' ', '-',$request->category_name_en)),
		'category_slug_hin' => str_replace(' ', '-',$request->category_name_hin),
		'category_icon' => $request->category_icon,

    	]);

	    $notification = array(
			'message' => 'Category Updated Successfully',
			'alert-type' => 'success'
		);

		return redirect()->route('all.category')->with($notification);


    } // end method


    public function CategoryDelete($id){

    	Category::findOrFail($id)->delete();

    	$notification = array(
			'message' => 'Category Deleted Successfully',
			'alert-type' => 'success'
		);

		return redirect()->back()->with($notification);

    } // end method


}
