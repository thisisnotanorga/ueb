document.addEventListener('DOMContentLoaded', function() {
    const actionTriggers = document.querySelectorAll('.action-trigger');

    actionTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();

            document.querySelectorAll('.action-dropdown.open').forEach(dropdown => {
                if (dropdown !== this.nextElementSibling) {
                    dropdown.classList.remove('open');
                }
            });

            this.nextElementSibling.classList.toggle('open');
        });
    });

    document.addEventListener('click', function() {
        document.querySelectorAll('.action-dropdown.open').forEach(dropdown => {
            dropdown.classList.remove('open');
        });
    });

    document.querySelectorAll('.action-dropdown').forEach(dropdown => {
        dropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });

    document.querySelectorAll('.action-item').forEach(item => {
        item.addEventListener('click', function() {
            const action = this.textContent.trim();
            const resultElement = this.closest('.result-wrapper, .image-item');
            const url = resultElement.querySelector('a').href;

            switch(action) {
                case 'Copy link':
                    navigator.clipboard.writeText(url);
                    showToast('Link copied!');
                    break;
                case 'Hide':
                    resultElement.style.display = 'none';
                    showToast('Content hidden');
                    break;
                case 'Report':
                    showToast('Content reported');
                    break;
            }
        });
    });

    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('show');

            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 2000);
        }, 10);
    }
});
