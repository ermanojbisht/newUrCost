@props(['items'])

<nav class="flex" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        @foreach ($items as $item)
            <li class="inline-flex items-center">
                @if(!$loop->last)
                    <a href="{{ $item['route'] }}" class="text-gray-700 dark:text-gray-300 hover:text-blue-500">
                        @if($loop->first)
                            {!! config('icons.home') !!}
                        @endif
                        <span class="ml-1">{{ $item['label'] }}</span>
                    </a>
                    <svg class="w-3 h-3 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                @else
                    <span class="text-gray-500 dark:text-gray-400">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
