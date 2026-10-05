<?php

namespace App\Http\Controllers\Web;

use App\Models\ViewedProfile;
use App\Http\Controllers\Controller;
use App\Models\PhotoRequest;
use App\Services\Api\ApiCommonActionModel;
use Illuminate\Http\Request;

class ViewedProfileController extends Controller
{
    public function index(Request $request)
    {
        $memberId = auth()->guard('web')->id();
        $type = $request->type ?? 'i_viewed';

        ## Relation & Member Columns :
        $isIviewed          = $type === 'i_viewed';
        $relation        = $isIviewed ? 'receiver' : 'sender';
        $selfColumn      = $isIviewed ? 'sender_member_id' : 'receiver_member_id';
        $otherColumn     = $isIviewed ? 'receiver_member_id' : 'sender_member_id';

        $query = ViewedProfile::query()
            ->active()
            ->with(ApiCommonActionModel::relation($relation))
            ->where($selfColumn, $memberId)
            ->latest();

        $resultData = $query->paginate(10);

        $otherMemberIds = $resultData->pluck($otherColumn)->unique()->values()->toArray();

        // Correct PhotoRequest check :
        $acceptedRequests = PhotoRequest::getAcceptedReceiverIds($memberId, $otherMemberIds);

        foreach ($resultData as $item) {
            $otherId = $item->{$otherColumn};

            $item->hasPhotoRequestAccess = in_array($otherId, $acceptedRequests);
        }

        if ($request->ajax()) {
            return response(
                view(_getConstant('dir_path.WEB_DIR_PATH') . '.viewedProfile.ajax_result', compact('resultData', 'type'))->render()
            )->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache')
                ->header('Vary', 'X-Requested-With');
        }

        return response(
            view(
                _getConstant('dir_path.WEB_DIR_PATH') . '.viewedProfile.index',
                compact('resultData', 'type')
            )
        )->header('Vary', 'X-Requested-With');
    }
}
