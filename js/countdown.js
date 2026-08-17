document.addEventListener('DOMContentLoaded', () => {
    const outputs = document.querySelectorAll('.astroid-countdown');

    outputs.forEach((output) => {
        const target = output.dataset.target;
        if (!target) {
            return;
        }

        const labels = {
            day: output.dataset.day || output.dataset.days || 'Days',
            hour: output.dataset.hour || output.dataset.hours || 'Hours',
            minute: output.dataset.minute || output.dataset.minutes || 'Minutes',
            second: output.dataset.second || output.dataset.seconds || 'Seconds',
            expired: output.dataset.expired || 'Expired',
            and: output.dataset.and || 'and'
        };

        const targetDate = new Date(target).getTime();
        if (Number.isNaN(targetDate)) {
            return;
        }

        const render = () => {
            const remaining = targetDate - Date.now();

            if (remaining <= 0) {
                output.textContent = labels.expired;
                return false;
            }

            const days = Math.floor(remaining / 86_400_000);
            const hours = Math.floor((remaining / 3_600_000) % 24);
            const minutes = Math.floor((remaining / 60_000) % 60);
            const seconds = Math.floor((remaining / 1_000) % 60);
            const parts = [];
            if (days) {
                parts.push('<span>' + days + ' ' + labels.day + '</span>');
            }
            if (hours) {
                parts.push('<span>' + hours + ' ' + labels.hour + '</span>');
            }
            if (minutes) {
                parts.push('<span>' + minutes + ' ' + labels.minute + '</span>');
            }

            let countdownText = '<span>' + seconds + ' ' + labels.second + '</span>';
            if (parts.length > 0) {
                countdownText = parts.join(', ') + ' ' + labels.and + ' ' + countdownText;
            }

            output.innerHTML = countdownText;

            return true;
        };

        if (!render()) {
            return;
        }
        
        const timer = setInterval(() => {
            if (!render()) {
                clearInterval(timer);
            }
        }, 1000);
    });
});