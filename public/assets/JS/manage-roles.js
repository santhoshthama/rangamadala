function initRoleOverviewTabs() {
    const tabButtons = document.querySelectorAll('.tab-trigger[data-tab]');
    if (!tabButtons.length) {
        return;
    }

    const tabContents = Array.from(tabButtons)
        .map(button => ({
            button,
            panel: document.getElementById(`tab-${button.dataset.tab}`),
        }))
        .filter(item => item.panel);

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            tabButtons.forEach(tabButton => tabButton.classList.remove('active'));
            button.classList.add('active');

            tabContents.forEach(item => item.panel.classList.remove('active'));

            const targetPanel = document.getElementById(`tab-${button.dataset.tab}`);
            if (targetPanel) {
                targetPanel.classList.add('active');
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initRoleOverviewTabs();

    const confirmForms = document.querySelectorAll('form[data-confirm]');
    confirmForms.forEach(form => {
        form.addEventListener('submit', event => {
            const message = form.getAttribute('data-confirm');
            if (message && !window.confirm(message)) {
                event.preventDefault();
            }
        });
    });

    const actionForms = document.querySelectorAll('form.js-role-action');
    actionForms.forEach(form => {
        form.addEventListener('submit', event => {
            const confirmMessage = form.getAttribute('data-confirm');
            if (confirmMessage && !window.confirm(confirmMessage)) {
                event.preventDefault();
                return;
            }

            const submitButton = form.querySelector('[type="submit"]');
            if (submitButton && !submitButton.disabled) {
                submitButton.dataset.originalContent = submitButton.innerHTML;
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Working...';
            }
        });
    });

    window.addEventListener('pageshow', event => {
        if (event.persisted) {
            document.querySelectorAll('form.js-role-action [type="submit"]').forEach(button => {
                if (button.dataset.originalContent) {
                    button.disabled = false;
                    button.innerHTML = button.dataset.originalContent;
                }
            });
        }
    });

    const flashMessage = document.querySelector('.message');
    if (flashMessage) {
        setTimeout(() => {
            flashMessage.style.transition = 'opacity 0.4s ease';
            flashMessage.style.opacity = '0';
            setTimeout(() => {
                flashMessage.remove();
            }, 400);
        }, 5000);
    }
});
