<x-mail::message>
# Leave Request Approved

Your leave request has been **approved**.

- **Type:** {{ $leaveRequest->leaveType->name }}
- **Dates:** {{ $leaveRequest->start_date->format('M j, Y') }} - {{ $leaveRequest->end_date->format('M j, Y') }}
- **Duration:** {{ $leaveRequest->days }} day(s)

Enjoy your time off!

<x-mail::button :url="route('leave-requests.index')">
View My Requests
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>