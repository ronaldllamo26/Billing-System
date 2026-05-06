// STUXZ Client Session Engine (Timer + Sync + Alerts)
console.log('STUXZ Session Engine Active...');

let secondsRemaining = null; // Changed to null to avoid immediate logout
let timerInterval;
let lastProcessedOrderId = null;

function startLocalTimer() {
    if(timerInterval) clearInterval(timerInterval);
    timerInterval = setInterval(() => {
        if (secondsRemaining !== null && secondsRemaining > 0) {
            secondsRemaining--;
            updateTimerDisplay();
        } else if (secondsRemaining === 0) {
            // TIME'S UP! Auto-logout
            clearInterval(timerInterval);
            alert('Your session has ended. Thank you for playing at STUXZ!');
            window.location.href = '../index.php'; 
        }
    }, 1000);
}

function updateTimerDisplay() {
    const display = document.getElementById('timer-display');
    if (display && secondsRemaining !== null) {
        let h = Math.floor(secondsRemaining / 3600);
        let m = Math.floor((secondsRemaining % 3600) / 60);
        let s = secondsRemaining % 60;
        display.innerText = (h > 0 ? h + ":" : "") + (m < 10 ? "0" + m : m) + ":" + (s < 10 ? "0" + s : s);
    } else if (display) {
        display.innerText = "--:--:--";
    }
}

function syncWithServer() {
    fetch('../actions/get_session_info.php')
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                // If PC is Vacant but someone is accessing this page, force logout
                if (data.pc_status === 'Vacant') {
                    window.location.href = '../index.php';
                    return;
                }

                // SYNC TIME: Adjust local timer to match server
                if (secondsRemaining === null || Math.abs(secondsRemaining - data.time_left) > 10) {
                    secondsRemaining = data.time_left;
                    updateTimerDisplay();
                }

                const balEl = document.getElementById('player-balance');
                if(balEl) balEl.innerText = `₱${parseFloat(data.balance).toFixed(2)}`;

                const userEl = document.getElementById('player-username');
                if(userEl) userEl.innerText = data.username;

                if(data.last_order_id && data.last_order_id !== lastProcessedOrderId) {
                    if(lastProcessedOrderId !== null) showServeToast(data.order_served);
                    lastProcessedOrderId = data.last_order_id;
                }
            }
        });
}

function showServeToast(items) {
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed; bottom: 60px; right: 20px; width: 300px;
        background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(15px);
        padding: 15px; border-radius: 12px; border-left: 5px solid #34d399;
        z-index: 9999; box-shadow: 0 10px 30px rgba(0,0,0,0.5); color: white;
        transform: translateX(400px); transition: 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex; gap: 12px; align-items: center;
    `;
    toast.innerHTML = `
        <div style="background: #34d399; width: 35px; height: 35px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <i data-lucide="utensils" style="color: #000; width: 18px;"></i>
        </div>
        <div style="flex: 1;">
            <div style="font-weight: 800; font-size: 11px; color: #34d399; text-transform: uppercase;">Order Ready!</div>
            <div style="font-size: 12px; opacity: 0.9;">${items}</div>
        </div>
    `;
    document.body.appendChild(toast);
    lucide.createIcons();
    setTimeout(() => toast.style.transform = 'translateX(0)', 100);
    setTimeout(() => {
        if(toast) {
            toast.style.transform = 'translateX(400px)';
            setTimeout(() => toast.remove(), 500);
        }
    }, 8000);
}

startLocalTimer();
syncWithServer();
setInterval(syncWithServer, 5000);
