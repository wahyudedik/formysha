@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-softPink-400 dark:focus:border-softPink-500 focus:ring-softPink-300 dark:focus:ring-softPink-500 rounded-xl shadow-sm']) }}>
