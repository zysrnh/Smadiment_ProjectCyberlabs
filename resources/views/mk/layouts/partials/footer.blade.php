<style>
.pc-footer {
    padding: 32px 0;
    margin-top: 60px;
    border-top: 1px solid rgba(0,0,0,0.05);
}
.footer-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.footer-text {
    font-size: 13px;
    font-weight: 500;
    color: #94a3b8;
    letter-spacing: 0.01em;
}
.footer-text strong {
    color: #475569;
    font-weight: 700;
}
.footer-links {
    display: flex;
    gap: 24px;
}
.footer-link-item {
    font-size: 13px;
    font-weight: 600;
    color: #64748b;
    text-decoration: none;
    transition: all 0.2s;
    opacity: 0.7;
}
.footer-link-item:hover {
    color: #038047;
    opacity: 1;
}
</style>

<footer class="pc-footer">
    <div class="footer-wrapper container-fluid px-4">
        <div class="footer-text">
            <strong>SMADIMENT</strong> &copy; {{ date('Y') }} — Premium Social Analytics
        </div>
        <div class="footer-links">
            <a href="{{ route('mk.dashboard') }}" class="footer-link-item">Dashboard</a>
            <a href="{{ route('mk.profile') }}" class="footer-link-item">Profile</a>
            <a href="#" class="footer-link-item">Support</a>
        </div>
    </div>
</footer>