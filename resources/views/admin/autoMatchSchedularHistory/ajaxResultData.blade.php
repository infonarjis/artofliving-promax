@if (isset($resultArr) && count($resultArr) > 0)
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col">Date</th>
                <th scope="col">Sending Mode</th>
                <th scope="col">Status</th>
                <th scope="col">Total Members</th>
                <th scope="col">Processed</th>
                <th scope="col">Matches Sent</th>
                <th scope="col">Emails Sent</th>
                <th scope="col">SMS Sent</th>
                <th scope="col" style="width: 220px;">Progress</th>
                <th scope="col">Started</th>
                <th scope="col">Completed</th>
            </tr>
        </thead>
        @foreach ($resultArr as $key => $schedule)
            <tbody>
                <tr class="table_data_val">
                    <td class="fw-semibold">{{ $schedule->schedule_date->format('d-m-Y') }}</td>
                    {{-- Sending Mode --}}
                    <td>
                        @if ($schedule->match_sending_mode === 'email')
                            <span class="badge bg-label-primary">
                                Email
                            </span>
                        @elseif ($schedule->match_sending_mode === 'sms')
                            <span class="badge bg-label-info">
                                SMS
                            </span>
                        @elseif ($schedule->match_sending_mode === 'both')
                            <span class="badge bg-label-success">
                                Email + SMS
                            </span>
                        @else
                            <span class="badge bg-label-dark">
                                -
                            </span>
                        @endif
                    </td>
                    <td>
                        @if ($schedule->status == 'pending')
                            <span class="badge bg-label-dark">{{ $schedule->status }}</span>
                        @elseif ($schedule->status == 'in_progress')
                            <span class="badge bg-label-warning">{{ $schedule->status }}</span>
                        @elseif ($schedule->status == 'completed')
                            <span class="badge bg-label-success">{{ $schedule->status }}</span>
                        @elseif ($schedule->status == 'failed')
                            <span class="badge bg-label-danger">{{ $schedule->status }}</span>
                        @endif
                    </td>

                    <td>{{ $schedule->total_members }}</td>
                    <td>
                        {{ $schedule->processed_members }}
                        <span class="text-muted small">/ {{ $schedule->total_members }}</span>
                    </td>
                    <td>{{ $schedule->matches_sent }}</td>
                    <td>{{ $schedule->emails_sent }}</td>
                    <td>{{ $schedule->sms_sent }}</td>
                    <td>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-{{ $schedule->status_color }}" role="progressbar"
                                style="width: {{ $schedule->progress_percent }}%;"
                                aria-schedulenow="{{ $schedule->progress_percent }}" aria-schedulemin="0"
                                aria-schedulemax="100">
                            </div>
                        </div>
                        <span class="small text-muted">{{ $schedule->progress_percent }}%</span>
                    </td>

                    <td class="small text-muted">
                        {{ $schedule->started_at?->format('d-m-Y H:i') ?? '-' }}
                    </td>
                    <td class="small text-muted">
                        {{ $schedule->completed_at?->format('d-m-Y H:i') ?? '-' }}
                    </td>
                </tr>
            </tbody>
        @endforeach
    </table>
@else
    <div class="No_dataFound text-center p-4">
        <img src="{{ _assetUrl('upload_path.ADMIN_NO_DATA_FOUND') }}" alt="No Data Found" class="Nodata-img">
    </div>
@endif

@include('admin.commonPagination')
