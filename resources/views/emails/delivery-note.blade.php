@component('mail::message')
# Delivery Note

**Delivery Number:** {{ $delivery->delivery_number }}

**Scheduled Date:** {{ $delivery->scheduled_delivery_date }}
**Scheduled Time:** {{ $delivery->scheduled_delivery_time }}

**Driver:** {{ $delivery->driver_name }} ({{ $delivery->driver_phone }})
**Vehicle:** {{ $delivery->vehicle_number }}

**Delivery Address:** {{ $delivery->delivery_address }}

**Recipient:** {{ $delivery->recipient_name }} ({{ $delivery->recipient_phone }})

Thanks,<br>
{{ config('app.name') }}
@endcomponent 