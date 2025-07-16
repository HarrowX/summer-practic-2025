<div class="space-y-4">
    <h2 class="text-xl font-bold">Управление промокодами</h2>

    <form wire:submit.prevent="createPromo" class="space-y-4 p-4 bg-gray-100 rounded">
        <input wire:model="code" type="text" placeholder="Код промокода" class="block w-full">
        @error('code') <span class="text-red-500">{{ $message }}</span> @enderror

        <input wire:model="count_points" type="number" placeholder="Количество баллов" class="block w-full">
        @error('count_points') <span class="text-red-500">{{ $message }}</span> @enderror

        <input wire:model="quantity_total" type="number" placeholder="Лимит использований" class="block w-full">
        @error('quantity_total') <span class="text-red-500">{{ $message }}</span> @enderror

        <input wire:model="expired_at" type="datetime-local" class="block w-full">

        <button type="submit" class="px-4 py-2 bg-blue-500 text-black rounded">
            Создать промокод
        </button>
    </form>

    @if(session('message'))
        <div class="p-2 bg-green-100 text-green-800 rounded">
            {{ session('message') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full table-fixed">
            <thead class="bg-gray-100">
            <tr>
                <th class="w-1/4 px-4 py-2 text-left">Код</th>
                <th class="w-1/4 px-4 py-2 text-left">Баллы</th>
                <th class="w-1/4 px-4 py-2 text-left">Использовано</th>
                <th class="w-1/4 px-4 py-2 text-left">Срок действия</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
            @foreach($promos as $promo)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 whitespace-nowrap">{{ $promo->code }}</td>
                    <td class="px-4 py-2 whitespace-nowrap">{{ number_format($promo->count_points, 0, '', ' ') }}</td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        {{ $promo->quantity_total - $promo->quantity_left }}/{{ $promo->quantity_total }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        {{ $promo->expired_at }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
