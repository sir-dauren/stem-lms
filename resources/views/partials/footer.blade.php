<footer class="mt-20 bg-blueprint text-white">
    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="grid gap-10 md:grid-cols-4">
            <div class="md:col-span-2">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-signal-300 font-display text-lg font-extrabold text-ink-950">S</span>
                    <span class="font-display text-xl font-extrabold">{{ $brandName }}</span>
                </a>
                <p class="mt-4 max-w-sm text-sm leading-relaxed text-white/60">{{ setting('tagline', __('app.brand_tagline')) }}</p>
            </div>

            <div>
                <h4 class="font-display text-sm font-bold uppercase tracking-wider text-signal-300">{{ __('app.menu') }}</h4>
                <ul class="mt-4 space-y-2.5 text-sm text-white/70">
                    <li><a href="{{ route('courses.index') }}" class="hover:text-white">{{ __('app.nav_courses') }}</a></li>
                    <li><a href="{{ route('quizzes.index') }}" class="hover:text-white">{{ __('app.nav_tests') }}</a></li>
                    <li><a href="{{ route('certificates.verify') }}" class="hover:text-white">{{ __('app.cert_verify_title') }}</a></li>
                    <li><a href="{{ route('about') }}" class="hover:text-white">{{ __('app.nav_about') }}</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-display text-sm font-bold uppercase tracking-wider text-signal-300">{{ __('app.footer_support') }}</h4>
                <ul class="mt-4 space-y-2.5 text-sm text-white/70">
                    @if($footerMenu)
                        @foreach($footerMenu->items as $item)
                            <li><a href="{{ $item->url }}" target="{{ $item->target }}" class="hover:text-white">{{ $item->label }}</a></li>
                        @endforeach
                    @endif
                    <li><a href="mailto:{{ setting('support_email', 'support@stemly.test') }}" class="hover:text-white">{{ setting('support_email', 'support@stemly.test') }}</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-12 border-t border-white/10 pt-6 text-sm text-white/50">
            {{ setting('footer_note', '© '.date('Y').' '.$brandName.'.') }}
        </div>
    </div>
</footer>
