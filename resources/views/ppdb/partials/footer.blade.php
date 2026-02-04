<footer class="simple-footer">
    <div class="container">
        <p>&copy; 2025 Perpustakaan SMK Negeri 5 Padang</p>
    </div>
</footer>

<style>
.footer-section {
    background-color: var(--primary);
    color: var(--text-light);
    padding: 3rem 0 0;
    margin-top: auto;
}

.footer-about p {
    opacity: 0.8;
    line-height: 1.7;
    font-size: 0.9rem;
}

.footer-heading {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    position: relative;
    padding-bottom: 0.7rem;
}

.footer-heading::after {
    content: "";
    position: absolute;
    left: 0;
    bottom: 0;
    width: 40px;
    height: 3px;
    background-color: var(--secondary);
    border-radius: 1.5px;
}

.footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links li {
    margin-bottom: 0.7rem;
}

.footer-links a {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    font-size: 0.9rem;
}

.footer-links a:hover {
    color: var(--secondary);
    transform: translateX(5px);
}

.footer-links a i {
    font-size: 0.7rem;
    margin-right: 0.5rem;
}

.footer-contact {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-contact li {
    margin-bottom: 1rem;
    display: flex;
    align-items: flex-start;
    font-size: 0.9rem;
}

.footer-contact li i {
    color: var(--secondary);
    font-size: 1rem;
    margin-right: 0.8rem;
    margin-top: 0.2rem;
}

.footer-divider {
    height: 1px;
    background-color: rgba(255, 255, 255, 0.1);
}

.footer-bottom {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.footer-bottom p {
    margin: 0;
    font-size: 0.85rem;
    opacity: 0.8;
}

/* Simple Footer */
.simple-footer {
    background-color: var(--primary);
    color: var(--text-light);
    padding: 1.5rem 0;
    text-align: center;
    margin-top: auto;
    box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.05);
}

.simple-footer p {
    margin: 0;
    font-size: 0.9rem;
    opacity: 0.9;
    font-weight: 400;
    letter-spacing: 0.3px;
}

.simple-footer p:hover {
    opacity: 1;
}
</style>