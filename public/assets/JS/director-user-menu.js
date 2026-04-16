document.addEventListener('DOMContentLoaded', () => {
    const menus = Array.from(document.querySelectorAll('.user-menu'));

    if (menus.length === 0) {
        return;
    }

    const closeAllMenus = () => {
        menus.forEach(menu => {
            const trigger = menu.querySelector('.user-menu-trigger');
            menu.classList.remove('active');
            if (trigger) {
                trigger.setAttribute('aria-expanded', 'false');
            }
        });
    };

    menus.forEach(menu => {
        const trigger = menu.querySelector('.user-menu-trigger');
        if (!trigger) {
            return;
        }

        trigger.addEventListener('click', event => {
            event.stopPropagation();
            const willOpen = !menu.classList.contains('active');
            closeAllMenus();
            menu.classList.toggle('active', willOpen);
            trigger.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        });
    });

    document.addEventListener('click', event => {
        if (!menus.some(menu => menu.contains(event.target))) {
            closeAllMenus();
        }
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            closeAllMenus();
        }
    });
});
