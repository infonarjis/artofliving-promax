@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')
@section('admin_content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @include('admin.message')
        <!-- Basic Layout -->
        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-body">
                        <form id="{{ $formId }}" name="{{ $formName }}" action="{{ route($formUrl) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row g-6">
                                <?php echo $fromHtml; ?>
                            </div>
                            <button type="submit" class="btn btn-primary {{ $formSubmitBtnClass }}"
                                data-formid="{{ $formSubmitBtnId }}">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Schedular List --}}
        @if (!blank($schedules))
            <div class="card mt-3">
                <div class="d-flex justify-content-between px-3 py-1 mt-2">
                    <span class="fw-medium">Recent Auto Match Scheduler History</span>
                    <a href="{{ route('admin.autoMatchSchedularHistory.index') }}">
                        View All History
                    </a>
                </div>
                <div class="table-responsive text-nowrap mt-1">
                    <table class="table">
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
                        @foreach ($schedules as $schedule)
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
                                                aria-valuenow="{{ $schedule->progress_percent }}" aria-valuemin="0"
                                                aria-valuemax="100">
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
                </div>
            </div>
        @endif
        {{-- Recent Schedular List --}}

    </div>
    <input type="hidden" name="formUrl" id="formUrl" value="{{ route($formUrl) }}">
@endsection
