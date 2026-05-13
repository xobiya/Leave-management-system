<x-mail::message>
# New Leave Request

**{{ $leaveRequest->user->name }}** has submitted a leave request.

- **Type:** {{ $leaveRequest->leaveType->name }}
- **Dates:** {{ $leaveRequest->start_date->format('M j, Y') }} - {{ $leaveRequest->end_date->format('M j, Y') }}
- **Duration:** {{ $leaveRequest->days }} day(s)
- **Reason:** {{ $leaveRequest->reason ?: 'N/A' }}

Please review and approve or reject this request.

<x-mail::button :url="route('manager.dashboard')">
Review Request
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>