<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2.5 bg-[#0B5A9E] hover:bg-[#084a82] active:bg-[#063864] text-white border border-transparent rounded-lg font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-[#0B5A9E] focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm cursor-pointer']) }}>
    {{ $slot }}
</button>
