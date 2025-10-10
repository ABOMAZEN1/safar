<x-filament::page>
    <h1 class="text-xl font-bold mb-4">صفحة إرسال الإشعارات</h1>

    {{ $this->form }}

    <hr class="my-6">

    <h2 class="text-lg font-semibold mb-2">الرسائل الجاهزة</h2>

    {{ $this->table }}
</x-filament::page>
