@props(['paginator'])

@if ($paginator && $paginator->hasPages())
    @php
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();

        $pages = [];

        if ($lastPage <= 7) {
            for ($i = 1; $i <= $lastPage; $i++) {
                $pages[] = $i;
            }
        } else {
            if ($currentPage <= 3) {
                $pages = [1, 2, 3, 4, 5, '...', $lastPage];
            } elseif ($currentPage >= $lastPage - 2) {
                $pages = [1, '...', $lastPage - 4, $lastPage - 3, $lastPage - 2, $lastPage - 1, $lastPage];
            } else {
                $pages = [1, '...', $currentPage - 1, $currentPage, $currentPage + 1, '...', $lastPage];
            }
        }
    @endphp

    <div class="flex items-center gap-1">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <flux:button size="sm" icon="chevron-left" disabled square variant="subtle" class="opacity-30" aria-label="Previous page" />
        @else
            <flux:button size="sm" icon="chevron-left" wire:click="previousPage" wire:loading.attr="disabled" square variant="subtle" aria-label="Previous page" />
        @endif

        {{-- Page Numbers --}}
        @foreach ($pages as $page)
            @if ($page === '...')
                <span class="px-2 text-xs text-zinc-400 dark:text-zinc-500">...</span>
            @elseif ($page == $currentPage)
                <flux:button size="sm" variant="filled" square>{{ $page }}</flux:button>
            @else
                <flux:button size="sm" variant="subtle" wire:click="gotoPage({{ $page }})" square>{{ $page }}</flux:button>
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <flux:button size="sm" icon="chevron-right" wire:click="nextPage" wire:loading.attr="disabled" square variant="subtle" aria-label="Next page" />
        @else
            <flux:button size="sm" icon="chevron-right" disabled square variant="subtle" class="opacity-30" aria-label="Next page" />
        @endif
    </div>
@endif
