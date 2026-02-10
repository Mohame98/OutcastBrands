<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Traits\ApiValidator;
use App\Services\BrandService;

use App\Models\Brand; 
use App\Models\User;
use Carbon\Carbon;

class SearchController extends Controller
{
	use ApiValidator;
	protected BrandService $brandService;

	public function __construct(BrandService $brandService)
	{
		$this->brandService = $brandService;
	}

	public function searchView(): View
	{
		return view("pages.search");
	}

	/**
	 * Search API - Orchestrates scopes and delegates rendering to BrandService
	 */
	public function searchApi(Request $request): JsonResponse
	{
		// 1. Validation
		$validated = $this->validateJson($request, [
			'search'   => 'nullable|string|max:255',
			'sort'     => 'nullable|in:most popular,oldest,newest',
			'category' => 'nullable|string',
			'filter'   => 'nullable|in:past-week,past-month,past-3-months,past-year,all',
			'page'     => 'nullable|integer|min:1'
    ]);

		$query = Brand::query()
			->smartSearch($request->search)
			->byCategory($request->category)
			->createdWithin($request->filter)
			->sortBy($request->input('sort', 'most popular'));

		$result = $this->brandService->getPaginatedBrandCards($query);
		return response()->json($result);
	}
}