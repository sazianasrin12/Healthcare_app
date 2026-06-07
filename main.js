const toggleDarkMode = () => {
    document.body.classList.toggle('dark-mode');
    localStorage.setItem('theme', document.body.classList.contains('dark-mode') ? 'dark' : 'light');
};
if (localStorage.getItem('theme') === 'dark') document.body.classList.add('dark-mode');

document.addEventListener('DOMContentLoaded', () => {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            alert.style.transition = 'all 0.3s ease';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});

setInterval(() => {
    const now = new Date();
    const curTime = `${now.getHours().toString().padStart(2,'0')}:${now.getMinutes().toString().padStart(2,'0')}`;
    document.querySelectorAll('.reminder-card').forEach(card => {
        const timeElem = card.querySelector('small');
        if (timeElem && timeElem.innerText.includes(curTime)) {
            const toast = document.createElement('div');
            toast.className = 'alert success';
            toast.style.position = 'fixed';
            toast.style.top = '20px';
            toast.style.right = '20px';
            toast.style.zIndex = '9999';
            toast.style.boxShadow = '0 10px 15px -3px rgba(0,0,0,0.1)';
            toast.style.minWidth = '300px';
            toast.innerHTML = `⏰ <strong>Medicine Time:</strong> ${card.querySelector('strong').innerText}`;
            document.body.appendChild(toast);
            card.style.backgroundColor = 'var(--primary-light)';
            card.style.borderColor = 'var(--primary)';
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }
    });
}, 60000);
