// Mise à jour de l'heure en temps réel
function updateTime() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    document.getElementById('current-time').textContent = `${hours}:${minutes}:${seconds}`;
}

// Fonction pour mettre à jour les points de progression
function updateProgressDots() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    
    // Éviter la division par zéro sur les pages courtes
    if (scrollHeight === 0) return;
    
    const scrollPercentage = (scrollTop / scrollHeight) * 100;
    const dots = document.querySelectorAll('#progress-dots .dot');
    const totalDots = dots.length;
    const activeDots = Math.ceil((scrollPercentage / 100) * totalDots);
    
    dots.forEach((dot, index) => {
        if (index < activeDots) {
            dot.classList.add('active');
        } else {
            dot.classList.remove('active');
        }
    });
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    updateTime();
    setInterval(updateTime, 1000);
    updateProgressDots();
    window.addEventListener('scroll', updateProgressDots);
});