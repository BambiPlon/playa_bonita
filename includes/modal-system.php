<style>
.custom-modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    z-index: 9999;
    animation: fadeIn 0.3s ease;
}

.custom-modal-overlay.active {
    display: flex;
    align-items: center;
    justify-content: center;
}

.custom-modal {
    background: white;
    border-radius: 12px;
    padding: 30px;
    max-width: 500px;
    width: 90%;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    animation: slideUp 0.3s ease;
    text-align: center;
}

.custom-modal.success-modal {
    border-top: 4px solid #10b981;
}

.custom-modal.confirm-modal {
    background: #2d1b1b;
    color: white;
    text-align: left;
}

.modal-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
}

.modal-icon.success {
    background: rgba(16, 185, 129, 0.1);
    border: 3px solid #10b981;
    color: #10b981;
}

.modal-icon.warning {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
    font-size: 35px;
}

.modal-title {
    font-size: 24px;
    font-weight: 700;
    margin: 0 0 15px 0;
    color: #111827;
}

.custom-modal.confirm-modal .modal-title {
    color: white;
    font-size: 14px;
    font-weight: 600;
    letter-spacing: 2px;
    margin-bottom: 20px;
}

.modal-message {
    font-size: 16px;
    color: #6b7280;
    margin: 0 0 10px 0;
    line-height: 1.5;
}

.custom-modal.confirm-modal .modal-message {
    color: white;
    font-size: 15px;
    font-weight: 500;
    margin-bottom: 15px;
}

.modal-description {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.8);
    margin: 0 0 25px 0;
    line-height: 1.6;
}

.modal-buttons {
    display: flex;
    gap: 12px;
    justify-content: center;
    margin-top: 25px;
}

.custom-modal.confirm-modal .modal-buttons {
    justify-content: flex-start;
}

.modal-button {
    padding: 12px 32px;
    border: none;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.modal-button.primary {
    background: #10b981;
    color: white;
}

.modal-button.primary:hover {
    background: #059669;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
}

.modal-button.accept {
    background: white;
    color: #111827;
    border: 1px solid #d1d5db;
}

.modal-button.accept:hover {
    background: #f3f4f6;
}

.modal-button.cancel {
    background: #7c3636;
    color: white;
}

.modal-button.cancel:hover {
    background: #5a2525;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}
</style>

<div id="customModalOverlay" class="custom-modal-overlay">
    <div id="customModal" class="custom-modal">
        <div id="modalIcon" class="modal-icon"></div>
        <h2 id="modalTitle" class="modal-title"></h2>
        <p id="modalMessage" class="modal-message"></p>
        <p id="modalDescription" class="modal-description" style="display: none;"></p>
        <div id="modalButtons" class="modal-buttons"></div>
    </div>
</div>

<script>
const CustomModal = {
    overlay: null,
    modal: null,
    
    init() {
        this.overlay = document.getElementById('customModalOverlay');
        this.modal = document.getElementById('customModal');
        
        this.overlay.addEventListener('click', (e) => {
            if (e.target === this.overlay) {
                this.close();
            }
        });
    },
    
    show(config) {
        const { type, title, message, description, onConfirm, onCancel } = config;
        
        const iconEl = document.getElementById('modalIcon');
        const titleEl = document.getElementById('modalTitle');
        const messageEl = document.getElementById('modalMessage');
        const descriptionEl = document.getElementById('modalDescription');
        const buttonsEl = document.getElementById('modalButtons');
        
        // Reset modal
        this.modal.className = 'custom-modal';
        iconEl.className = 'modal-icon';
        iconEl.innerHTML = '';
        descriptionEl.style.display = 'none';
        
        if (type === 'success') {
            this.modal.classList.add('success-modal');
            iconEl.classList.add('success');
            iconEl.innerHTML = '✓';
            titleEl.textContent = title || 'Éxito';
            messageEl.textContent = message;
            
            buttonsEl.innerHTML = `
                <button class="modal-button primary" onclick="CustomModal.close()">OK</button>
            `;
        } else if (type === 'confirm') {
            this.modal.classList.add('confirm-modal');
            iconEl.classList.add('warning');
            iconEl.innerHTML = '⚠';
            messageEl.textContent = message;
            
            if (description) {
                descriptionEl.textContent = description;
                descriptionEl.style.display = 'block';
            }
            
            buttonsEl.innerHTML = `
                <button class="modal-button accept" onclick="CustomModal.confirm()">Aceptar</button>
                <button class="modal-button cancel" onclick="CustomModal.close()">Cancelar</button>
            `;
            
            this.onConfirmCallback = onConfirm;
        }
        
        this.overlay.classList.add('active');
    },
    
    showSuccess(message, title = 'Éxito') {
        this.show({
            type: 'success',
            title: title,
            message: message
        });
    },
    
    showConfirm(message, description, onConfirm) {
        this.show({
            type: 'confirm',
            message: message,
            description: description,
            onConfirm: onConfirm
        });
    },
    
    confirm() {
        if (this.onConfirmCallback) {
            this.onConfirmCallback();
        }
        this.close();
    },
    
    close() {
        this.overlay.classList.remove('active');
        this.onConfirmCallback = null;
    }
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    CustomModal.init();
});
</script>
