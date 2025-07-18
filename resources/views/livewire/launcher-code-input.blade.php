<div>
    @if(!$success)
        <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Введите код из лаунчера</h2>
            <form class="flex gap-2">
                <input
                    wire:model="code"
                    type="text"
                    class="flex-1"
                    placeholder="5-значный код"
                    maxlength="5"
                >

                @if($error)
                    <p class="text-red-500 text-sm mb-2">{{ $error }}</p>
                @endif

                <button
                    wire:click="submitCode"
                    class="px-4 py-2 bg-green-500 text-black rounded"
                >
                    Подтвердить
                </button>
            </form>
        </div>
    @else
        <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
            <div class="p-4 bg-green-100 rounded-md text-center">
                <p class="text-green-800">Данные успешно отправлены в лаунчер!</p>
            </div>
        </div>
    @endif
</div>
