<div class="afd-table-wrap">
    <table class="afd-table">
        <thead>
            <tr>
                <th class="afd-th-active">Date</th>
                <th>Status</th>
                <th>Amount</th>
                <th>Admin Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($resultData as $item)
                <tr>
                    <td class="afd-td-name">{{ _displayDate($item->created_at, 'j F, Y') }}</td>
                    <td>
                        @if($item->is_transfered == 1)
                            <span class="afd-ri-check">Transferred</span>
                        @else
                            <span class="afd-ri-times">Pending</span>
                        @endif
                    </td>
                    <td><span style="font-weight:600;">{{ $item->amount }}</span></td>
                    <td class="afd-td-name">{{ $item->admin_remark }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- pagination  -->
@if ($resultData->hasPages())
    {{ $resultData->links(_getConstant('dir_path.AFFILIATE_DIR_PATH') . '.layouts.pagination') }}
@endif
<!-- pagination  -->