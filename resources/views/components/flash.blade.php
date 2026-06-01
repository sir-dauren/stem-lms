@if(session('status') || $errors->any())
    <div class="mx-auto max-w-7xl px-4 pt-5 sm:px-6 lg:px-8">
        @if(session('status'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
                 class="flex items-center justify-between gap-3 rounded-2xl border border-cyan-400/40 bg-cyan-400/10 px-5 py-3.5 text-sm font-semibold text-ink-900">
                <span>{{ session('status') }}</span>
                <button @click="show = false" class="text-ink-500 hover:text-ink-900">✕</button>
            </div>
        @endif
        @if($errors->any())
            <div class="rounded-2xl border border-coral-400/40 bg-coral-400/10 px-5 py-3.5 text-sm text-ink-900">
                <p class="font-bold">Проверьте форму:</p>
                <ul class="mt-1.5 list-disc space-y-0.5 pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endif
