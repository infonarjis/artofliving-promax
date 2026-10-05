<div class="afd-table-wrap">
    <table class="afd-table">
        <thead>
            <tr>
                <th class="afd-th-active">Matri id</th>
                <th>Name</th>
                <th>Income Type</th>
                <th>Amount</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($resultData as $item)
                <tr>
                    <td class="afd-td-name">{{ $item->member->matri_id }}</td>
                    <td class="afd-td-name">{{ $item->member->fullname }}</td>
                    <td class="afd-td-name">{{ $item->income_type }}</td>
                    <td><span style="font-weight:600;">{{ $item->amount }}</span></td>
                    <td>
                        {{ _displayDate($item->created_at, 'j F, Y') }}
                    </td>
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