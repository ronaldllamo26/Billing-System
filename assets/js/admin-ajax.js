// STUXZ Admin System Core Handlers
document.addEventListener('DOMContentLoaded', function() {
    console.log('STUXZ Admin GUI Active...');

    // Order Polling Logic
    let processedOrders = new Set();
    
    function checkNewOrders() {
        fetch('../actions/get_pending_orders.php')
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    data.orders.forEach(order => {
                        if(!processedOrders.has(order.id)) {
                            // Show specific items in notification
                            const message = `🚨 NEW ORDER: Station ${order.pc_number} \nItems: ${order.order_summary} \nTotal: ₱${order.amount}`;
                            showToast(message, true); 
                            processedOrders.add(order.id);
                            playNotificationSound();
                        }
                    });
                }
            })
            .catch(err => console.error('Order poll failed:', err));
    }

    setInterval(checkNewOrders, 5000);
    checkNewOrders();
});

function playNotificationSound() {
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    const oscillator = audioCtx.createOscillator();
    const gainNode = audioCtx.createGain();
    oscillator.connect(gainNode);
    gainNode.connect(audioCtx.destination);
    oscillator.type = 'sine';
    oscillator.frequency.setValueAtTime(880, audioCtx.currentTime);
    gainNode.gain.setValueAtTime(0.2, audioCtx.currentTime);
    oscillator.start();
    oscillator.stop(audioCtx.currentTime + 0.3);
}

function showToast(message, isPersistent = false) {
    let container = document.getElementById('toast-container');
    if(!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.style.cssText = 'position: fixed; top: 70px; right: 20px; z-index: 9999;';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = 'gui-toast';
    toast.style.cssText = `
        background: #ffffff;
        color: #1e293b;
        padding: 20px;
        border-radius: 16px;
        margin-top: 15px;
        font-size: 14px;
        font-weight: 600;
        border-left: 8px solid ${isPersistent ? '#ef4444' : '#3b82f6'};
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
        display: flex;
        flex-direction: column;
        gap: 8px;
        animation: slideLeft 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        width: 320px;
    `;
    
    toast.innerHTML = `
        <div class="d-flex justify-content-between align-items-center">
            <span style="color: #ef4444; text-transform: uppercase; font-size: 10px; letter-spacing: 1px;">Incoming Order</span>
            <i data-lucide="x" style="width: 16px; cursor: pointer; color: #94a3b8;" onclick="this.parentElement.parentElement.remove()"></i>
        </div>
        <div style="white-space: pre-wrap;">${message}</div>
        <button class="btn btn-sm btn-success mt-2 fw-bold" onclick="completeOrder(${order.id}, this.parentElement)">MARK AS SERVED</button>
    `;
    container.appendChild(toast);
    lucide.createIcons();
}

function completeOrder(orderId, toastElement) {
    const formData = new FormData();
    formData.append('order_id', orderId);

    fetch('../actions/complete_order.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            toastElement.remove();
        } else {
            alert('Error: ' + data.message);
        }
    });
}
