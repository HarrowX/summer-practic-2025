<div class="space-y-4">
    <h2 class="text-xl font-bold">Ваш баланс: {{ $balance }}</h2>

    <form wire:submit.prevent="applyPromo" class="flex gap-2">
        <input wire:model="code" type="text" placeholder="Введите промокод" class="flex-1">
        <button type="submit" class="px-4 py-2 bg-green-500 text-black rounded">
            Активировать
        </button>
    </form>

    @if($message)
        <div class="p-2 bg-blue-100 text-black rounded">
            {{ $message }}
        </div>
    @endif
</div>
