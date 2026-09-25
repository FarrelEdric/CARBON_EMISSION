@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:border-[#0B5A9E] focus:ring-[#0B5A9E] rounded-lg shadow-sm text-sm py-2 px-3 transition-colors']) }}>
