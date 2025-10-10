<div class="space-y-4">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">#</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">العميل</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">عدد المقاعد</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">الحالة</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">الإجمالي</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">تأكيد الذهاب</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">تأكيد الإياب</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($bookings as $booking)
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $booking->id }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $booking->customer->user->name ?? '—' }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $booking->reserved_seat_count }}</td>
                        <td class="px-4 py-2 text-sm">
                            <span class="inline-flex items-center rounded px-2 py-0.5 text-xs {{ $booking->booking_status === 'paid' ? 'bg-green-100 text-green-700' : ($booking->booking_status === 'refunded' ? 'bg-yellow-100 text-yellow-700' : ($booking->booking_status === 'canceled' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700')) }}">
                                {{ __("status.$booking->booking_status") }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ number_format((float) $booking->total_price, 2) }} SYP</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $booking->is_departure_confirmed ? '✔' : '—' }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $booking->is_return_confirmed ? '✔' : '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">لا توجد حجوزات لهذه الرحلة.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


