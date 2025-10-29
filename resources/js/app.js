import './bootstrap';
const setStoredTheme = (theme) => localStorage.setItem('theme', theme);

const getStoredTheme = () => localStorage.getItem('theme');

const getPreferredTheme = () => {
    const storedTheme = getStoredTheme();
    if (storedTheme) {
        return storedTheme;
    }
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
};

const setTheme = (theme) => {
    if (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.documentElement.setAttribute('data-bs-theme', 'dark');
    } else {
        document.documentElement.setAttribute('data-bs-theme', theme);
    }
};

setTheme(getPreferredTheme());

window.toggleTheme = () => {
    const currentTheme = getStoredTheme() || getPreferredTheme();
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    setStoredTheme(newTheme);
    setTheme(newTheme);
};

window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    const storedTheme = getStoredTheme();
    if (!storedTheme || storedTheme === 'auto') {
        setTheme(getPreferredTheme());
    }
});