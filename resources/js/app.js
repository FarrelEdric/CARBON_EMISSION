import './bootstrap';

import Alpine from 'alpinejs';

// Global Theme Store for Dark/Light Mode
Alpine.data('themeStore', () => ({
    isDark: document.documentElement.classList.contains('dark'),
    init() {
        // Sync state if system preference changes and no explicit choice set
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            if (!localStorage.getItem('ace_theme')) {
                this.isDark = e.matches;
                document.documentElement.classList.toggle('dark', this.isDark);
            }
        });
    },
    toggleTheme() {
        this.isDark = !this.isDark;
        if (this.isDark) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('ace_theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('ace_theme', 'light');
        }
        window.dispatchEvent(new CustomEvent('theme-changed', { detail: { dark: this.isDark } }));
    }
}));

window.Alpine = Alpine;

Alpine.start();
