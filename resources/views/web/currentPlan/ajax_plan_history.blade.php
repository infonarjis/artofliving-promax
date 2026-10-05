<div class="plan-history-list">
    @foreach ($planHistory as $item)
        <div class="single-addon-box py-3 d-md-flex">
            <div class="left-plan-history w-100">
                <div class="plan-namesd d-flex align-items-center gap-2">
                    <div class="history-plan-icon">
                        <iconify-icon icon="ion:gift"></iconify-icon>
                    </div>
                    <div class="fts-18 fw-6 white-color-n ms-1">{{ $item->plan_name ?? '' }}</div>
                </div>
                <div class="row pt-1">
                    <div class="col-lg-4 col-6 mt-2">
                        <div class="plansingle-details">
                            <div class="fts-14 fw-4 white-color70-n">{{ __('messages.lbl_plan_duration') }}</div>
                            <div class="fts-15 fw-5 white-color-n">{{ $item->plan_validity_days }} {{ __('messages.lbl_days') }}</div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-6 mt-2">
                        <div class="plansingle-details">
                            <div class="fts-14 fw-4 white-color70-n">{{ __('messages.lbl_plan_activated_on') }}
                            </div>
                            <div class="fts-15 fw-5 white-color-n mt-1">
                                {{ _displayDate($item->plan_activate_date, 'j F, Y') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-6 mt-2">
                        <div class="plansingle-details">
                            <div class="fts-14 fw-4 white-color70-n">{{ __('messages.lbl_plan_expired_on') }}</div>
                            <div class="fts-15 fw-5 saleText-color-n mt-1">
                                {{ _displayDate($item->plan_expiry_date, 'j F, Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="right-plans-btngroup d-flex gap-2 mt-2 mt-md-0">
                <a href="{{ route('web.currentPlan.downloadInvoice',$item->id) }}" class="btn-plan-download fts-15 fw-5">
                    <iconify-icon icon="hugeicons:download-03" class="fts-24"></iconify-icon>
                    {{ __('messages.lbl_download') }}
                </a>
            </div>
        </div>
    @endforeach
</div>

<!-- pagination  -->
@if ($planHistory->hasPages())
    {{ $planHistory->links(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.pagination') }}
@endif
