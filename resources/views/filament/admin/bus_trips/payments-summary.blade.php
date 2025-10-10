<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div class="p-3 bg-green-50 rounded">
            <div class="text-sm text-gray-600">إجمالي المدفوع</div>
            <div class="text-xl font-semibold text-green-700">{{ number_format($totalPaid, 2) }} SYP</div>
        </div>
        <div class="p-3 bg-red-50 rounded">
            <div class="text-sm text-gray-600">الإجمالي المُسترجع/الملغى</div>
            <div class="text-xl font-semibold text-red-700">{{ number_format($totalRefundedOrCanceled, 2) }} SYP</div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">#</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">العميل</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">المبلغ</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">الحالة</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">التاريخ</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($payments as $payment)
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $payment->id }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $payment->customer->user->name ?? '—' }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ number_format((float) $payment->amount, 2) }} SYP</td>
                        <td class="px-4 py-2 text-sm">
                            <span class="inline-flex items-center rounded px-2 py-0.5 text-xs {{ $payment->status === 'paid' ? 'bg-green-100 text-green-700' : ($payment->status === 'refunded' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700') }}">
                                {{ __("status.$payment->status") }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ optional($payment->created_at)->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">لا توجد مدفوعات لهذه الرحلة.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


