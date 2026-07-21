<!-- ══════════════════════════════════════════════════════
     CONTACT TELEGRAM — Smart School Academy
     ══════════════════════════════════════════════════════ -->

<style>
/* ── TELEGRAM FLOATING BUTTON ── */
.telegram-btn {
    position: fixed;
    bottom: 156px;
    right: 30px;
    z-index: 9994;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0088CC, #003A8F);
    color: white;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    cursor: pointer;
    box-shadow: 0 8px 30px rgba(0, 136, 204, 0.35);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    animation: telegramBounce 2.5s ease-in-out infinite;
}
.telegram-btn:hover {
    transform: scale(1.1) translateY(-4px);
    box-shadow: 0 12px 40px rgba(0, 136, 204, 0.5);
    color: white;
}
.telegram-btn .telegram-tooltip {
    position: absolute;
    right: 66px;
    background: rgba(15, 23, 42, 0.95);
    backdrop-filter: blur(10px);
    color: white;
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 0.8rem;
    white-space: nowrap;
    opacity: 0;
    transform: translateX(10px);
    transition: all 0.3s ease;
    pointer-events: none;
    border: 1px solid rgba(255, 255, 255, 0.06);
}
.telegram-btn:hover .telegram-tooltip {
    opacity: 1;
    transform: translateX(0);
}

/* ── PULSE DOT ── */
.telegram-btn .pulse-dot {
    position: absolute;
    top: -2px;
    right: -2px;
    width: 14px;
    height: 14px;
    background: #22C55E;
    border: 2px solid #080c14;
    border-radius: 50%;
    animation: livePulse 2s ease-in-out infinite;
}

@keyframes telegramBounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
}
@keyframes livePulse {
    0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.5); }
    50% { box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); }
    100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
}

/* ── RESPONSIVE ── */
@media (max-width: 768px) {
    .telegram-btn {
        bottom: 140px;
        right: 16px;
        width: 48px;
        height: 48px;
        font-size: 1.25rem;
    }
}
</style>

<!-- ═══ TELEGRAM FLOATING BUTTON ═══ -->
<a href="https://t.me/SmartSchoolAcad_bot"
   class="telegram-btn"
   id="telegramBtn"
   target="_blank"
   rel="noopener noreferrer"
   aria-label="Contactez-nous sur Telegram">
    <i class="bi bi-telegram"></i>
    <span class="pulse-dot"></span>
    <span class="telegram-tooltip">Discuter sur Telegram</span>
</a>
