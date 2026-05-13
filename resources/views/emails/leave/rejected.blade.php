<x-mail::message>
# Leave Request Rejected

Your leave request has been **rejected**.

- **Type:** {{ $leaveRequest->leaveType->name }}
- **Dates:** {{ $leaveRequest->start_date->format('M j, Y') }} - {{ $leaveRequest->end_date->format('M j, Y') }}
- **Duration:** {{ $leaveRequest->days }} day(s)
@if($leaveRequest->rejection_reason)
- **Reason:** {{ $leaveRequest->rejection_reason }}
@endif

If you have questions, please contact your manager.

<x-mail::button :url="route('leave-requests.create')">
Submit New Request
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>