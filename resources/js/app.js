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

window.toggleTheme = (event) => {
    const currentTheme = getStoredTheme() || getPreferredTheme();
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';

    if (!document.startViewTransition) {
        setStoredTheme(newTheme);
        setTheme(newTheme);
        document.body.style.transition = 'background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease';
        setTimeout(() => {
            document.body.style.transition = '';
        }, 300);
        return;
    }

    window.themeTransitioning = true;

    const x = event.clientX;
    const y = event.clientY;
    const endRadius = Math.hypot(
        Math.max(x, window.innerWidth - x),
        Math.max(y, window.innerHeight - y)
    );

    const transition = document.startViewTransition(() => {
        setStoredTheme(newTheme);
        setTheme(newTheme);
    });

    transition.ready.then(() => {
        const clipPathIn = [
            `circle(0px at ${x}px ${y}px)`,
            `circle(${endRadius}px at ${x}px ${y}px)`
        ];
        const clipPathOut = [
            `circle(${endRadius}px at ${x}px ${y}px)`,
            `circle(0px at ${x}px ${y}px)`
        ];

        document.documentElement.animate(
            {
                clipPath: newTheme === 'light' ? clipPathIn : clipPathOut
            },
            {
                duration: 500,
                easing: 'ease-in-out',
                pseudoElement: newTheme === 'light'
                    ? '::view-transition-new(root)'
                    : '::view-transition-old(root)'
            }
        );
    });

    transition.finished.then(() => {
        window.themeTransitioning = false;
    });
};

window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    const storedTheme = getStoredTheme();
    if (!storedTheme || storedTheme === 'auto') {
        setTheme(getPreferredTheme());
    }
});