<div class="confirm-container">
    <div class="confirm-box">
        <h2><?php echo htmlspecialchars($this->data['title']); ?></h2>
        <p><?php echo htmlspecialchars($this->data['message']); ?></p>
        
        <div class="confirm-actions">
            <a href="<?php echo htmlspecialchars($this->data['confirmUrl']); ?>" class="btn-danger">Confirmer</a>
            <a href="<?php echo htmlspecialchars($this->data['cancelUrl']); ?>" class="btn-secondary">Annuler</a>
        </div>
    </div>
</div>

<style>
.confirm-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 300px;
}

.confirm-box {
    background: rgba(0, 15, 30, 0.7);
    border: 1px solid rgba(55, 175, 229, 0.3);
    border-radius: 5px;
    padding: 30px;
    width: 100%;
    max-width: 500px;
    text-align: center;
}

.confirm-box h2 {
    color: #d8e8f0;
    margin-bottom: 20px;
}

.confirm-box p {
    color: #8cb5e4;
    margin-bottom: 30px;
    line-height: 1.6;
}

.confirm-actions {
    display: flex;
    justify-content: center;
    gap: 20px;
}

.btn-danger {
    display: inline-block;
    padding: 10px 20px;
    background: linear-gradient(135deg, #a02020, #6a0505);
    color: white;
    border: 1px solid rgba(255, 100, 100, 0.5);
    border-radius: 3px;
    text-decoration: none;
    transition: all 0.2s;
}

.btn-danger:hover {
    background: linear-gradient(135deg, #c02020, #8a0505);
    transform: translateY(-1px);
}

.btn-secondary {
    display: inline-block;
    padding: 10px 20px;
    background: rgba(0, 30, 60, 0.5);
    color: #8cb5e4;
    border: 1px solid rgba(55, 175, 229, 0.3);
    border-radius: 3px;
    text-decoration: none;
    transition: all 0.2s;
}

.btn-secondary:hover {
    background: rgba(0, 50, 80, 0.5);
    color: #d8e8f0;
}
</style>