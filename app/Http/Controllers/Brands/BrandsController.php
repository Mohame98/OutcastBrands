<?php

namespace App\Http\Controllers\Brands;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiValidator;
use Illuminate\Http\JsonResponse;
use App\Models\{Brand, BrandView, BrandImage, Category};
use App\Services\{BrandService, InteractionService};
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Log};
use Illuminate\Support\Facades\DB;

class BrandsController extends Controller
{
  use ApiValidator;
  protected $brandService;
  protected $interactionService;

  public function __construct(BrandService $brandService, InteractionService $interactionService)
  {
    $this->brandService = $brandService;
    $this->interactionService = $interactionService;
  }

  public function index()
  {
    $topBrands = Brand::popular()->get();
    return view('pages.home', [
      'featuredBrand' => $topBrands->first(),
      'otherBrands'   => $topBrands->slice(1),
    ]);
  }

  public function storeBrand1(Request $request)
  {
    $validated = $this->validateJson($request, [
      'title' => "required|string|max:100|regex:/^[\p{L}\p{N} \-().,\'’]+$/u",
      'sub_title' => 'required|string|max:200|regex:/^[\p{L}\p{N}\p{P}\p{Zs}]+$/u',
      'location' => "required|string|max:60|regex:/^[\p{L}\p{N} .,'’\-()]+$/u",
      'website' => 'nullable|url|max:255',
      'launch_date' => 'nullable|date',
    ]);

    $validated['title']      = strip_tags($validated['title']);
    $validated['sub_title']  = strip_tags($validated['sub_title']);
    $validated['location']   = strip_tags($validated['location']);

    session(['step1' => $validated]);
    return response()->json(['success' => true, 'multi_step' => true]);
  }

  public function storeBrand2(Request $request)
  {
    $validated = $this->validateJson($request, ['description' => 'nullable|string|max:5000']);
    session(['step2' => $validated]);
    return response()->json(['success' => true, 'multi_step' => true]);
  }

  public function storeBrand3(Request $request)
  {
    $this->validateJson($request, [
      'photos' => 'required|array|min:1|max:4',
      'photos.*' => 'image|mimes:jpg,png,jpeg|max:12000',
    ]);

    // Custom size validation
    $totalSize = collect($request->file('photos'))->sum(fn ($p) => $p->getSize());
    $this->authorizeJson($totalSize <= 4 * 1024 * 1024, 'Total size of all files must not exceed 4MB.');

    $paths = collect($request->file('photos'))->map(fn($photo) => 
      $photo->store('temp/brands', 'public')
    )->toArray();

    session(['step3' => ['photos' => $paths]]);
    return response()->json(['success' => true, 'multi_step' => true]);
  }

  public function storeBrand4(Request $request)
  {
    $validated = $this->validateJson($request, [
      'categories' => 'required|array|min:1|max:3',
      'categories.*' => 'string|in:Footwear,Accessories,Outerwear,Casual,Formal,Activewear,Streetwear,Minimalist,Vintage,Preppy,Seasonal,Luxury,Sustainable',
    ]);

    $steps = [
      1 => session('step1'),
      2 => session('step2'),
      3 => session('step3')
    ];

    $this->authorizeJson($steps[1] && $steps[2] && $steps[3], 'Missing data from previous steps.');

    $brand = $this->brandService->finalizeBrandCreation(Auth::user(), $steps, $validated['categories']);

    session()->forget(['step1', 'step2', 'step3']);
    return response()->json(['success' => true, 'message' => 'Brand posted!']);
  }

  public function toggleSave(Brand $brand)
  {
    $this->authorizeJson(Auth::check());
    $result = $this->interactionService->toggleSave($brand);
    return response()->json(array_merge(['success' => true], $result));
  }

  public function vote(Request $request, Brand $brand)
  {
    $this->authorizeJson(Auth::check());
    $validated = $this->validateJson($request, ['vote' => 'required|in:1,-1']);

    $result = $this->interactionService->processVote($brand, (int)$validated['vote']);
    return response()->json(array_merge(['success' => true], $result));
  }

  public function showBrand(Brand $brand)
  {
    BrandView::create([
      'brand_id' => $brand->id,
      'ip' => request()->ip(),
      'user_agent' => request()->userAgent(),
    ]);

    $brand->loadCount(['views', 'voters', 'savers'])->load(['categories', 'images']);
    
    $relatedBrands = Brand::where('user_id', $brand->user_id)
      ->where('id', '!=', $brand->id)
      ->inRandomOrder()->take(3)->get();

    return view('brands.show-brand', [
      'brand' => $brand,
      'view_count' => $brand->views_count,
      'vote_count' => $brand->voters_count,
      'save_count' => $brand->savers_count,
      'relatedBrands' => $relatedBrands,
    ]);
  }

  public function deleteBrand(Brand $brand)
  {
    $this->authorizeJson($brand->user_id === Auth::id(), 'Unauthorized deletion attempt.');
    $brand->delete();
    session()->flash('flash_message', 'Brand deleted!');
    return response()->json(['success' => true, 'redirect_url' => '/']);
  }
}