<?php

namespace App\Http\Controllers\Web;

use App\Helpers\CaptchaHelper;
use App\Http\Controllers\Controller;
use App\Models\VendorCategory;
use App\Models\VendorInquiry;
use App\Models\VendorReview;
use App\Models\WeddingPlanner;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WeddingVendorsController extends Controller
{
    public function index(Request $request)
    {
        $query = VendorCategory::approved()
            ->withCount(['weddingPlanners as wedding_vendor_count' => function ($q) {
                $q->approved();
            }]);
        //  Keyword filter
        if ($request->filled('keyword')) {
            $query->where('category_name', 'like', '%' . $request->keyword . '%');
        }
        // Category filter
        if ($request->filled('category')) {
            $query->where('id', $request->category);
        }
        // City filter
        if ($request->filled('city')) {
            $query->whereHas('weddingPlanners', function ($q) use ($request) {
                $q->where('city_id', $request->city);
            });
        }
        $categories = $query->orderBy('id', 'desc')
            ->paginate(9)
            ->withQueryString();

        ## Category List:
        $allcategories = VendorCategory::approved()->orderByDesc('id')->get();

        if ($request->ajax()) {
            return view(_getConstant('dir_path.WEB_DIR_PATH') . '.weddingVendors.vendorCategory.ajax_result', compact('categories', 'allcategories'))->render();
        }

        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.weddingVendors.vendorCategory.index', compact('categories', 'allcategories'));
    }


    public function vendorList(Request $request, VendorCategory $category = null)
    {
        $categoryId = $category?->id;

        $query = WeddingPlanner::approved()
            ->withCount(['reviews as reviews_count'])
            ->withAvg(['reviews as average_rating'], 'star');

        // Category from URL: /wedding-vendors/20
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($request->filled('keyword')) {
            $query->where('planner_name', 'like', '%' . $request->keyword . '%');
        }

        // Category from filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('city')) {
            $query->where('city_id', $request->city);
        }

        $vendors = $query
            ->orderByDesc('id')
            ->paginate(9)
            ->withQueryString();

        $categories = VendorCategory::approved()
            ->orderByDesc('id')
            ->get();

        if ($request->ajax()) {
            return view(
                _getConstant('dir_path.WEB_DIR_PATH') . '.weddingVendors.vendorList.ajax_result',
                compact('vendors', 'categoryId', 'categories')
            )->render();
        }

        return view(
            _getConstant('dir_path.WEB_DIR_PATH') . '.weddingVendors.vendorList.index',
            compact('vendors', 'categoryId', 'categories')
        );
    }

    public function vendorDetails(VendorCategory $category, WeddingPlanner $vendor)
    {
        $vendor->loadCount([
            'reviews as reviews_count'
        ])->loadAvg([
            'reviews as average_rating'
        ], 'star');

        if (!$vendor) {
            abort(404);
        }

        $captchaCode = CaptchaHelper::generate('vendor_book_venue_captcha');
        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.weddingVendors.vendorList.detail', compact('vendor', 'captchaCode'));
    }

    public function bookVenue(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor_id'    => 'required|exists:wedding_planner,id',
            'name'         => 'required|string|max:100',
            'country_code' => [
                'required',
                'string',
                'exists:country_master,country_code',
            ],
            'mobile'       => 'required|digits_between:8,15',
            'wedding_date' => 'required|date|after_or_equal:today',
            'total_guest'  => 'required|numeric',
            'sent_info_by' => 'required|array|min:1',
            // 'sent_info_by.*' => 'in:email,call',
            'description'  => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            VendorInquiry::create([
                'vendor_id'    => $request->vendor_id,
                'name'         => $request->name,
                'mobile'       => $request->country_code . '-' . $request->mobile,
                'wedding_date' => $request->wedding_date,
                'total_guest'  => $request->total_guest,
                'sent_info_by' => implode(',', $request->sent_info_by ?? []),
                'description'  => $request->description,
                'created_at'   => now(),
            ]);

            return response()->json([
                'status' => true,
                'message' => __('messages.msg_booking_submitted_successfully')
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_something_went_wrong')
            ]);
        }
    }

    /**
     * Refresh captcha via AJAX.
     */
    public function refreshCaptcha()
    {
        $captchaCode = CaptchaHelper::generate('vendor_book_venue_captcha');

        return response()->json([
            'success'     => true,
            'captchaCode' => $captchaCode,
        ]);
    }

    public function addVendorReview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor_id'  => 'required|exists:wedding_planner,id',
            'name'       => 'required|min:3|max:100',
            'email'      => 'required|email|max:150',
            'description' => 'required|min:10|max:1000',
            'star'       => 'required|integer|between:1,5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            // Prevent duplicate review by same email for same vendor
            $alreadyReviewed = VendorReview::where('vendor_id', $request->vendor_id)
                ->where('email', $request->email)
                ->exists();

            if ($alreadyReviewed) {
                return response()->json([
                    'status'  => false,
                    'message' => __('messages.msg_review_already_submitted')
                ], 200);
            }

            VendorReview::create([
                'vendor_id'  => $request->vendor_id,
                'name'       => $request->name,
                'email'      => $request->email,
                'description' => $request->description,
                'star'       => $request->star,
                'status'     => 'UNAPPROVED',
                'created_at' => _getCurrentDate(),
            ]);
            return response()->json([
                'status'  => true,
                'message' => __('messages.msg_review_submitted_successfully')
            ]);
        } catch (Exception $e) {
            Log::error('Vendor Review Error: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => __('messages.msg_something_went_wrong')
            ], 500);
        }
    }

    public function getVendorReviews(Request $request, $vendorId)
    {
        $sort = $request->sort ?? 'newest';

        $query = VendorReview::where('vendor_id', $vendorId)
            ->where('status', 'APPROVED');

        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }
        $reviews = $query->paginate(5);

        return response()->json([
            'status' => true,
            'html'   => view(_getConstant('dir_path.WEB_DIR_PATH') . '.weddingVendors.vendorList.reviews', compact('reviews'))->render(),
            'next_page' => $reviews->nextPageUrl()
        ]);
    }
}
