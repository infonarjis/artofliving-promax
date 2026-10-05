@push('styles')
    <style>
        .add-on-modal .modal-content {
            background: var(--black-color-1);
        }

        .add-on-modal {
            background: #000000bf;
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
        }

        .addon-modal-content {
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(20, 20, 43, 0.18);
        }

        .addon-modal-title {
            font-weight: 700;
            font-size: 1.15rem;
            margin: 0;
            color: var(--white-color);
        }

        .addon-list-wrapper {
            max-height: 320px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 4px 6px 4px 2px;
        }

        .addon-list-wrapper::-webkit-scrollbar {
            width: 5px;
        }

        .addon-list-wrapper::-webkit-scrollbar-thumb {
            background: #c9c9e8;
            border-radius: 10px;
        }

        .addon-list-wrapper::-webkit-scrollbar-track {
            background: transparent;
        }

        .checkout-addon-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            border: 1.5px solid #ececf5;
            border-radius: 14px;
            cursor: pointer;
            transition: border-color 0.15s ease, background-color 0.15s ease, box-shadow 0.15s ease;
            margin: 0;
        }

        .checkout-addon-item:hover {
            border-color: #b9aaf5;
        }

        .checkout-addon-item.addon-item-selected {
            border-color: var(--primary-color);
            background: linear-gradient(0deg, rgba(108, 92, 231, 0.06), rgba(108, 92, 231, 0.06));
            box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.08);
        }

        .addon-radio-wrapper {
            position: relative;
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        .addon-radio {
            position: absolute;
            inset: 0;
            opacity: 0;
            margin: 0;
            cursor: pointer;
        }

        .addon-radio-circle {
            position: absolute;
            inset: 0;
            border: 2px solid #cfcfe6;
            border-radius: 50%;
            background: #fff;
            transition: border-color 0.15s ease;
        }

        .addon-radio-circle::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 10px;
            height: 10px;
            background: var(--primary-color);
            border-radius: 50%;
            transform: translate(-50%, -50%) scale(0);
            transition: transform 0.15s ease;
        }

        .addon-radio:checked+.addon-radio-circle {
            border-color: var(--primary-color);
        }

        .addon-radio:checked+.addon-radio-circle::after {
            transform: translate(-50%, -50%) scale(1);
        }

        .addon-text {
            line-height: 1.3;
        }

        .addon-name {
            font-weight: 500;
            font-size: 0.95rem;
            color: var(--white-color);
        }

        .addon-category {
            color: #9494ab;
            font-size: 0.78rem;
        }

        .addon-price {
            font-weight: 500;
            font-size: 0.92rem;
            color: var(--white-color);
            white-space: nowrap;
        }

        .add-on-modal .modal-header .btn-close {
            box-shadow: none;
            background: var(--black-color-4);
            opacity: 1;
            border: none;
            padding: 0;
            height: 32px;
            width: 32px;
            border-radius: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            line-height: 0;
            color: var(--white-color-70);
        }

        .addon-summary {
            background: var(--black-color-3);
            border: 1px solid var(--black-color-4);
            border-radius: 14px;
            padding: 14px 16px;
            margin-top: 14px;
            font-size: 0.92rem;
        }

        .addon-grand-total {
            font-weight: 700;
            font-size: 1.02rem;
            color: var(--white-color);
        }

        .addon-buy-btn {
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            font-size: 1rem;
            color: #fff;
            background: linear-gradient(90deg, var(--primary-color), #4a7bf7);
            transition: opacity 0.15s ease, transform 0.05s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .addon-buy-btn:hover:not(:disabled) {
            opacity: 0.92;
        }

        .addon-buy-btn:active:not(:disabled) {
            transform: scale(0.99);
        }

        .addon-buy-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .addon-btn-spinner {
            font-size: 1.1rem;
            color: #fff;
            vertical-align: middle;
        }

        .addon-category-badge {
            color: var(--white-color-70);
        }
    </style>
@endpush

<div class="add-on-modal modal fade" id="buyAddOnPlanModal" tabindex="-1" aria-labelledby="buyAddOnPlanModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content addon-modal-content border-0">
            <div class="modal-header border-0 pb-3">
                <h5 class="addon-modal-title" id="buyAddOnPlanModalLabel">
                    <iconify-icon icon="ph:sparkle-bold" class="text-primary align-middle me-1"></iconify-icon>
                    {{ __('messages.msg_buy_add_on_package') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
                </button>
            </div>

            <div class="modal-body pt-2 px-4 pb-4">
                <form id="addOnPurchaseForm" action="{{ route('web.membership.addonPay') }}" method="POST">
                    @csrf

                    <div class="addon-list-wrapper">
                        @foreach ($packages as $item)
                            <label for="add_on_package_{{ $item->id }}" class="checkout-addon-item"
                                data-target="add_on_package_{{ $item->id }}">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="addon-radio-wrapper">
                                        <input type="radio" name="add_on_package_id" class="addon-radio"
                                            id="add_on_package_{{ $item->id }}" value="{{ $item->id }}"
                                            data-title="{{ $item->package_title }}"
                                            data-amount="{{ $item->package_amount }}"
                                            data-currency="{{ $currentPlan->currency_code }}">
                                        <span class="addon-radio-circle"></span>
                                    </span>
                                    <div class="addon-text">
                                        <h6 class="addon-name mb-0">{{ $item->package_count }} {{ $item->package_title }}</h6>
                                        <small class="addon-category-badge mt-1">{{ $item->package_category }}</small>
                                    </div>
                                </div>
                                <span class="addon-price">
                                    {{ $currentPlan->currency_code }} {{ number_format($item->package_amount, 2) }}
                                </span>
                            </label>
                        @endforeach
                    </div>

                    <div id="addOnCouponMessage" class="small mt-2"></div>

                    <div class="addon-summary" id="addOnSummary" style="display:none;">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="white-color70-n">{{ __('messages.lbl_subtotal') }}</span>
                            <span class="white-color70-n" id="sumAddOnAmount">-</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1" id="sumTaxRow" style="display:none;">
                            <span class="white-color70-n" id="sumTaxLabel">{{ __('messages.lbl_tax') }}</span>
                            <span class="white-color70-n" id="sumTaxAmount">-</span>
                        </div>
                        <hr class="my-2 white-color-n">
                        <div class="d-flex justify-content-between addon-grand-total">
                            <span>{{ __('messages.lbl_grand_total') }}</span>
                            <span id="sumGrandTotal">-</span>
                        </div>
                    </div>

                    <div id="addOnValidationError" class="text-danger small mt-2 text-center" style="display:none;">
                        {{ __('messages.msg_select_one_addon') }}
                    </div>

                    <button type="submit" class="addon-buy-btn w-100 mt-3" id="addOnPayBtn" disabled>
                        <span class="addon-btn-text">{{ __('messages.lbl_buy_now') }}</span>
                        <iconify-icon icon="line-md:loading-twotone-loop" class="addon-btn-spinner ms-2"
                            style="display:none;"></iconify-icon>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            const currency = "{{ $currentPlan->currency_code }}";
            const calcUrl = "{{ route('web.membershipPlan.calculateAddOnPreview') }}";
            const csrfToken = "{{ csrf_token() }}";

            function getSelectedPackageId() {
                return $('.addon-radio:checked').val() || null;
            }

            function formatMoney(amount) {
                return currency + ' ' + parseFloat(amount || 0).toFixed(2);
            }

            function setButtonLoading(isLoading) {
                if (isLoading) {
                    $('#addOnPayBtn').prop('disabled', true);
                    $('.addon-btn-text').text("{{ __('messages.lbl_processing') }}...");
                    $('.addon-btn-spinner').show();
                } else {
                    $('.addon-btn-text').text("{{ __('messages.lbl_buy_now') }}");
                    $('.addon-btn-spinner').hide();
                }
            }

            function refreshSummary() {
                const id = getSelectedPackageId();

                if (!id) {
                    $('#addOnSummary').hide();
                    $('#addOnPayBtn').prop('disabled', true);
                    return;
                }

                // Show loader immediately so the user understands why the button is disabled
                setButtonLoading(true);

                $.ajax({
                    url: calcUrl,
                    method: 'POST',
                    data: {
                        _token: csrfToken,
                        package_ids: [id],
                        coupon_code: $('#addOnCouponCode').val()
                    },
                    success: function(data) {
                        $('#sumAddOnAmount').text(formatMoney(data.add_on_amount));
                        if (data.tax_applicable === 'Yes' && data.tax_amount > 0) {
                            $('#sumTaxRow').show();
                            $('#sumTaxRow').removeClass('d-none');
                            $('#sumTaxLabel').text((data.tax_name || '{{ __('messages.lbl_tax') }}') +
                                ' (' + data.tax_percentage + '%)');
                            $('#sumTaxAmount').text(formatMoney(data.tax_amount));
                        } else {
                            $('#sumTaxRow').addClass('d-none');
                        }

                        $('#sumGrandTotal').text(formatMoney(data.grand_total));
                        $('#addOnSummary').show();

                        if (data.coupon_message) {
                            $('#addOnCouponMessage')
                                .removeClass('text-danger text-success')
                                .addClass(data.coupon_applied ? 'text-success' : 'text-danger')
                                .text(data.coupon_message);
                        } else {
                            $('#addOnCouponMessage').text('');
                        }
                    },
                    error: function() {
                        $('#addOnCouponMessage')
                            .removeClass('text-success')
                            .addClass('text-danger')
                            .text("{{ __('messages.msg_something_went_wrong') }}");
                    },
                    complete: function() {
                        // Always reset the button state, whether the call succeeded or failed
                        setButtonLoading(false);
                        $('#addOnPayBtn').prop('disabled', !getSelectedPackageId());
                    }
                });
            }

            $('.addon-radio').on('change', function() {
                $('.checkout-addon-item').removeClass('addon-item-selected');
                $(this).closest('.checkout-addon-item').addClass('addon-item-selected');
                $('#addOnValidationError').hide();
                refreshSummary();
            });

            $('#addOnPurchaseForm').on('submit', function(e) {
                if (!getSelectedPackageId()) {
                    e.preventDefault();
                    $('#addOnValidationError').show();
                    return false;
                }
                $('#addOnPayBtn').prop('disabled', true);
                $('.addon-btn-text').text("{{ __('messages.lbl_processing') }}...");
                $('.addon-btn-spinner').show();
            });
        });
    </script>
@endpush
