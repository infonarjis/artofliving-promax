<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqListController extends Controller
{
    private $pageName = 'Faqs';

    public function __construct()
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
    }
    
    public function index()
    {
        ## Extra Js :
        $extraJsArr = ['/custom/js/commonList.js'];

        return view('admin.faqList.index', [
            'pageName' => 'FAQ List',
            'extraJsArr' => $extraJsArr,
            'ajaxUrl' => route('admin.faqList.ajaxPagination'),
            'changeStatusUrl' => route('admin.faqList.changeStatus'),
            'ajaxPaginationRequestUrl' => route('admin.faqList.ajaxPagination'),
            'actionBtnArr' => [
                'add' => 1,
                'delete' => 1,
                'approve' => 1,
                'unapprove' => 1,
                'edit' => 1,
                'isSearch' => 1,
            ],
            'actionButtonUrl' => [
                'add' => 'admin.faqList.create'
            ]
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX PAGINATION
    |--------------------------------------------------------------------------
    */
    public function ajaxPagination(Request $request)
    {
        $query = Faq::where('lang_code', _getDefaultLanguage());

        // Tab filter
        if ($request->conditionColumn && $request->conditionVal) {
            $query->where(
                trim($request->conditionColumn),
                trim($request->conditionVal)
            );
        }

        // Search
        if ($request->searchKeyword) {
            $search = $request->searchKeyword;
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', "%$search%")
                    ->orWhere('answer', 'like', "%$search%");
            });
        }

        $resultArr = $query->latest()->paginate($request->limit ?? 10);

        // Tab Counts
        $tabCount = [
            'allData' => Faq::count(),
            'approvedData' => Faq::where('status', 'APPROVED')->count(),
            'unapprovedData' => Faq::where('status', 'UNAPPROVED')->count(),
        ];

        $actionButtonUrl = [
            'edit' => 'admin.faqList.edit',
            'view' => 'admin.faqList.show'
        ];

        $html = view('admin.faqList.ajaxResultData', compact('resultArr', 'actionButtonUrl'))->render();

        return response()->json([
            'status' => 'success',
            'html' => $html,
            'data' => [
                'tabCount' => $tabCount
            ]
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        return view('admin.faqList.addEdit', [
            'pageName' => $this->pageName . ' Add',
            'mode' => 'add',
            'languageDataArr' => _getActiveLanguage(),
            'formAction'      => route('admin.faqList.store'),  // ← add this
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|unique:faq_master,question',
            'answer'   => 'required|string',
            'status'   => 'required|in:APPROVED,UNAPPROVED',
        ]);

        Faq::create([
            'question'  => $request->question,
            'answer'    => $request->answer,
            'status'    => $request->status,
            'lang_code' => _getDefaultLanguage()
        ]);

        return redirect()
            ->route('admin.faqList.index')
            ->with('success', 'FAQ added successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit(Faq $faq)
    {
        return view('admin.faqList.addEdit', [
            'pageName' => $this->pageName . ' Edit',
            'mode' => 'edit',
            'faq' => $faq,
            'languageDataArr' => _getActiveLanguage(),
            'formAction'      => route('admin.faqList.update', ['faq' => $faq->id]),  // ← add this
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Faq $faq)
    {
        $defaultLang = _getDefaultLanguage();
        $langId      = $request->lang_id ?? null;
        $faqId       = $faq->id;

        // Determine which record ID to ignore in unique check
        $ignoreId = ($request->lang_code != $defaultLang && $langId && $langId != $faqId)
            ? $langId   // editing an existing language row → ignore that row
            : $faqId;   // default lang or new language row → ignore main row

        $request->validate([
            'question' => 'required|string|unique:faq_master,question,' . $ignoreId,
            'answer'   => 'required|string',
            'status'   => 'required|in:APPROVED,UNAPPROVED',
        ]);

        if ($request->lang_code != $defaultLang) {

            if ($langId && $langId != $faqId) {
                ## Language record already exists — UPDATE it
                Faq::where('id', $langId)
                    ->where('lang_code', $request->lang_code)
                    ->update([
                        'question' => $request->question,
                        'answer'   => $request->answer,
                        'status'   => $request->status,
                    ]);
            } else {
                ## Language record does not exist — CREATE it
                Faq::create([
                    'question'   => $request->question,
                    'answer'     => $request->answer,
                    'status'     => $request->status,
                    'lang_code'  => $request->lang_code,
                    'lang_id'    => $faqId
                ]);
            }
        } else {
            ## Default language — update the main record
            $faq->update($request->only('question', 'answer', 'status'));
        }

        return redirect()
            ->route('admin.faqList.index')
            ->with('success', 'FAQ updated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */
    public function show(Faq $faq)
    {
        $pageName = $this->pageName . ' View';
        return view('admin.faqList.view', compact('faq', 'pageName'));
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY (Soft delete style)
    |--------------------------------------------------------------------------
    */
    public function destroy(Faq $faq)
    {
        $faq->delete();

        return redirect()->route('admin.faqList.index')->with('success', 'FAQ deleted successfully.');
    }

    ## Change Status :
    public function changeStatus(Request $request)
    {
        $responseArr = [
            'status' => 'error',
            'msg'    => _getConstant('responce_message.SOMETHING_WENT_WRONG'),
            'data'   => [],
        ];

        $postData = $request->all();
        if (empty($postData)) {
            return response()->json($responseArr, 200);
        }

        $ids = $postData['id'] ?? [];
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }

        $ids = array_filter($ids);
        if (empty($ids)) {
            $responseArr['msg'] = 'Invalid IDs supplied.';
            return response()->json($responseArr, 200);
        }

        // SOFT DELETE USING deleted_at
        if (isset($postData['is_deleted'])) {
            Faq::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            Faq::whereIn('id', $ids)->update($updateData);
        }
        $responseArr['status'] = 'success';
        $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        return response()->json($responseArr, 200);
    }

    /*
    |--------------------------------------------------------------------------
    | LANGUAGE DATA
    |--------------------------------------------------------------------------
    */
    public function getLangData(Request $request)
    {
        $faq = Faq::where('lang_code', $request->langCode)
            ->where(function ($q) use ($request) {
                $q->where('id', $request->id)
                    ->orWhere('lang_id', $request->id);
            })
            ->first();

        return response()->json([
            'lang_id' => $faq->id ?? $request->id,
            'lang_code' => $request->langCode,
            'question' => $faq->question ?? '',
            'answer' => $faq->answer ?? '',
        ]);
    }
}
