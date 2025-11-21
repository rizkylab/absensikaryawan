@props(['active' => false])

<a {{ $attributes->merge(['class' => 'flex items-center gap-3 px-4 py-3 rounded-lg transition ' . ($active ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700')]) }}>
    {{ $slot }}
</a>
