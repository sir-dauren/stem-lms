{{-- Recursive nested category list for the catalog sidebar --}}
@foreach($nodes as $node)
    @php($active = request('category') === $node->slug)
    <li>
        <a href="{{ route('courses.index', array_merge(request()->except('page'), ['category' => $node->slug])) }}"
           class="flex items-center justify-between rounded-lg px-3 py-1.5 text-sm transition {{ $active ? 'bg-ink-950 font-bold text-signal-300' : 'text-ink-700 hover:bg-white' }}"
           style="margin-left: {{ ($depth ?? 0) * 0.75 }}rem">
            <span>{{ $node->icon ? $node->icon.' ' : '' }}{{ $node->name }}</span>
        </a>
        @if($node->children->isNotEmpty())
            <ul class="mt-0.5 space-y-0.5">
                @include('partials.category-tree', ['nodes' => $node->children, 'depth' => ($depth ?? 0) + 1])
            </ul>
        @endif
    </li>
@endforeach
