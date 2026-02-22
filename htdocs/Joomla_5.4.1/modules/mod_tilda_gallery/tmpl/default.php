<?php
defined('_JEXEC') or die;
?>
<style>
/* =====================================================
 * TILDA GALLERY — Modern Masonry Grid + Lightbox
 * Brand: #5c7c3b (resort green)
 * ===================================================== */

/* Grid layout */
.tg-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}

/* Every 7th item spans 2 columns for visual rhythm */
.tg-grid .tg-item:nth-child(7n+4) {
    grid-column: span 2;
}

/* Base item */
.tg-item {
    position: relative;
    overflow: hidden;
    border-radius: 12px;
    cursor: pointer;
    aspect-ratio: 4 / 3;
    background: #e8efe0;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                box-shadow 0.3s ease;
}

.tg-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 14px 36px rgba(0, 0, 0, 0.16);
}

/* Image */
.tg-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.tg-item:hover img {
    transform: scale(1.08);
}

/* Hover overlay */
.tg-item::after {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0);
    transition: background 0.3s ease;
    pointer-events: none;
}

.tg-item:hover::after {
    background: rgba(0, 0, 0, 0.28);
}

/* Expand icon on hover */
.tg-icon {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
}

.tg-item:hover .tg-icon {
    opacity: 1;
}

.tg-icon svg {
    width: 44px;
    height: 44px;
    filter: drop-shadow(0 2px 6px rgba(0,0,0,0.4));
}

/* Entrance animation */
@keyframes tg-fadein {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}

.tg-item {
    animation: tg-fadein 0.5s ease both;
}

.tg-item:nth-child(1)  { animation-delay: 0.03s; }
.tg-item:nth-child(2)  { animation-delay: 0.06s; }
.tg-item:nth-child(3)  { animation-delay: 0.09s; }
.tg-item:nth-child(4)  { animation-delay: 0.12s; }
.tg-item:nth-child(5)  { animation-delay: 0.15s; }
.tg-item:nth-child(6)  { animation-delay: 0.18s; }
.tg-item:nth-child(7)  { animation-delay: 0.21s; }
.tg-item:nth-child(8)  { animation-delay: 0.24s; }
.tg-item:nth-child(9)  { animation-delay: 0.27s; }
.tg-item:nth-child(n+10) { animation-delay: 0.3s; }

/* =====================================================
 * LIGHTBOX
 * ===================================================== */
.tg-lightbox {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.92);
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.25s ease;
    backdrop-filter: blur(6px);
}

.tg-lightbox.tg-open {
    opacity: 1;
    pointer-events: all;
}

.tg-lb-img-wrap {
    position: relative;
    max-width: 90vw;
    max-height: 88vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.tg-lb-img-wrap img {
    max-width: 90vw;
    max-height: 88vh;
    object-fit: contain;
    border-radius: 8px;
    box-shadow: 0 32px 80px rgba(0, 0, 0, 0.6);
    animation: tg-lb-in 0.28s cubic-bezier(0.4, 0, 0.2, 1);
    display: block;
}

@keyframes tg-lb-in {
    from { transform: scale(0.9); opacity: 0; }
    to   { transform: scale(1);   opacity: 1; }
}

/* Counter */
.tg-lb-counter {
    position: absolute;
    bottom: -2rem;
    left: 50%;
    transform: translateX(-50%);
    color: rgba(255,255,255,0.55);
    font-size: 0.8rem;
    letter-spacing: 0.05em;
    white-space: nowrap;
}

/* Close button */
.tg-lb-close {
    position: absolute;
    top: 1.25rem;
    right: 1.5rem;
    color: #fff;
    font-size: 1.75rem;
    line-height: 1;
    cursor: pointer;
    opacity: 0.7;
    transition: opacity 0.2s, transform 0.2s;
    background: none;
    border: none;
    padding: 0.25rem 0.5rem;
    z-index: 1;
}

.tg-lb-close:hover {
    opacity: 1;
    transform: scale(1.15);
}

/* Arrow navigation */
.tg-lb-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    color: #fff;
    font-size: 3rem;
    line-height: 1;
    cursor: pointer;
    opacity: 0.55;
    transition: opacity 0.2s, transform 0.2s;
    padding: 0.5rem 1rem;
    background: none;
    border: none;
    user-select: none;
}

.tg-lb-nav:hover {
    opacity: 1;
    transform: translateY(-50%) scale(1.1);
}

.tg-lb-prev { left: 0.75rem; }
.tg-lb-next { right: 0.75rem; }

/* =====================================================
 * RESPONSIVE
 * ===================================================== */
@media (max-width: 900px) {
    .tg-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }
    /* disable spanning on smaller screens */
    .tg-grid .tg-item:nth-child(7n+4) {
        grid-column: span 1;
    }
}

@media (max-width: 560px) {
    .tg-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 6px;
    }
}
</style>

<!-- Lightbox overlay -->
<div class="tg-lightbox" id="tg-lightbox" role="dialog" aria-modal="true" aria-label="Просмотр изображения">
    <button class="tg-lb-close" id="tg-close" aria-label="Закрыть">&times;</button>
    <button class="tg-lb-nav tg-lb-prev" id="tg-prev" aria-label="Предыдущее">&#8249;</button>
    <div class="tg-lb-img-wrap">
        <img src="" id="tg-lb-img" alt="">
        <div class="tg-lb-counter" id="tg-counter"></div>
    </div>
    <button class="tg-lb-nav tg-lb-next" id="tg-next" aria-label="Следующее">&#8250;</button>
</div>

<!-- Gallery grid -->
<div class="tg-grid" id="tg-grid">

    <div class="tg-item" data-src="images/gallery/1.jpg">
        <img src="images/gallery/1.jpg" alt="Дивная усадьба — фото 1" loading="lazy">
        <div class="tg-icon"><svg fill="none" stroke="white" stroke-width="1.75" viewBox="0 0 24 24"><path d="M15 3h6m0 0v6m0-6l-7 7M9 21H3m0 0v-6m0 6l7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
    </div>

    <div class="tg-item" data-src="images/gallery/2.jpg">
        <img src="images/gallery/2.jpg" alt="Дивная усадьба — фото 2" loading="lazy">
        <div class="tg-icon"><svg fill="none" stroke="white" stroke-width="1.75" viewBox="0 0 24 24"><path d="M15 3h6m0 0v6m0-6l-7 7M9 21H3m0 0v-6m0 6l7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
    </div>

    <div class="tg-item" data-src="images/gallery/3.JPG">
        <img src="images/gallery/3.JPG" alt="Дивная усадьба — фото 3" loading="lazy">
        <div class="tg-icon"><svg fill="none" stroke="white" stroke-width="1.75" viewBox="0 0 24 24"><path d="M15 3h6m0 0v6m0-6l-7 7M9 21H3m0 0v-6m0 6l7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
    </div>

    <div class="tg-item" data-src="images/gallery/4.jpg">
        <img src="images/gallery/4.jpg" alt="Дивная усадьба — фото 4" loading="lazy">
        <div class="tg-icon"><svg fill="none" stroke="white" stroke-width="1.75" viewBox="0 0 24 24"><path d="M15 3h6m0 0v6m0-6l-7 7M9 21H3m0 0v-6m0 6l7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
    </div>

    <div class="tg-item" data-src="images/gallery/01.jpg">
        <img src="images/gallery/01.jpg" alt="Дивная усадьба — фото 5" loading="lazy">
        <div class="tg-icon"><svg fill="none" stroke="white" stroke-width="1.75" viewBox="0 0 24 24"><path d="M15 3h6m0 0v6m0-6l-7 7M9 21H3m0 0v-6m0 6l7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
    </div>

    <div class="tg-item" data-src="images/gallery/03.jpg">
        <img src="images/gallery/03.jpg" alt="Дивная усадьба — фото 6" loading="lazy">
        <div class="tg-icon"><svg fill="none" stroke="white" stroke-width="1.75" viewBox="0 0 24 24"><path d="M15 3h6m0 0v6m0-6l-7 7M9 21H3m0 0v-6m0 6l7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
    </div>

    <div class="tg-item" data-src="images/gallery/04.jpg">
        <img src="images/gallery/04.jpg" alt="Дивная усадьба — фото 7" loading="lazy">
        <div class="tg-icon"><svg fill="none" stroke="white" stroke-width="1.75" viewBox="0 0 24 24"><path d="M15 3h6m0 0v6m0-6l-7 7M9 21H3m0 0v-6m0 6l7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
    </div>

    <div class="tg-item" data-src="images/gallery/02.jpg">
        <img src="images/gallery/02.jpg" alt="Дивная усадьба — фото 8" loading="lazy">
        <div class="tg-icon"><svg fill="none" stroke="white" stroke-width="1.75" viewBox="0 0 24 24"><path d="M15 3h6m0 0v6m0-6l-7 7M9 21H3m0 0v-6m0 6l7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
    </div>

    <div class="tg-item" data-src="images/gallery/08.jpg">
        <img src="images/gallery/08.jpg" alt="Дивная усадьба — фото 9" loading="lazy">
        <div class="tg-icon"><svg fill="none" stroke="white" stroke-width="1.75" viewBox="0 0 24 24"><path d="M15 3h6m0 0v6m0-6l-7 7M9 21H3m0 0v-6m0 6l7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
    </div>

    <div class="tg-item" data-src="images/gallery/09.jpg">
        <img src="images/gallery/09.jpg" alt="Дивная усадьба — фото 10" loading="lazy">
        <div class="tg-icon"><svg fill="none" stroke="white" stroke-width="1.75" viewBox="0 0 24 24"><path d="M15 3h6m0 0v6m0-6l-7 7M9 21H3m0 0v-6m0 6l7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
    </div>

    <div class="tg-item" data-src="images/gallery/10.jpg">
        <img src="images/gallery/10.jpg" alt="Дивная усадьба — фото 11" loading="lazy">
        <div class="tg-icon"><svg fill="none" stroke="white" stroke-width="1.75" viewBox="0 0 24 24"><path d="M15 3h6m0 0v6m0-6l-7 7M9 21H3m0 0v-6m0 6l7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
    </div>

    <div class="tg-item" data-src="images/gallery/d2.jpg">
        <img src="images/gallery/d2.jpg" alt="Дивная усадьба — фото 12" loading="lazy">
        <div class="tg-icon"><svg fill="none" stroke="white" stroke-width="1.75" viewBox="0 0 24 24"><path d="M15 3h6m0 0v6m0-6l-7 7M9 21H3m0 0v-6m0 6l7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
    </div>

    <div class="tg-item" data-src="images/gallery/12.jpg">
        <img src="images/gallery/12.jpg" alt="Дивная усадьба — фото 13" loading="lazy">
        <div class="tg-icon"><svg fill="none" stroke="white" stroke-width="1.75" viewBox="0 0 24 24"><path d="M15 3h6m0 0v6m0-6l-7 7M9 21H3m0 0v-6m0 6l7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
    </div>

    <div class="tg-item" data-src="images/gallery/12_.jpg">
        <img src="images/gallery/12_.jpg" alt="Дивная усадьба — фото 14" loading="lazy">
        <div class="tg-icon"><svg fill="none" stroke="white" stroke-width="1.75" viewBox="0 0 24 24"><path d="M15 3h6m0 0v6m0-6l-7 7M9 21H3m0 0v-6m0 6l7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
    </div>

    <div class="tg-item" data-src="images/gallery/11.jpg">
        <img src="images/gallery/11.jpg" alt="Дивная усадьба — фото 15" loading="lazy">
        <div class="tg-icon"><svg fill="none" stroke="white" stroke-width="1.75" viewBox="0 0 24 24"><path d="M15 3h6m0 0v6m0-6l-7 7M9 21H3m0 0v-6m0 6l7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
    </div>

    <div class="tg-item" data-src="images/gallery/13.jpg">
        <img src="images/gallery/13.jpg" alt="Дивная усадьба — фото 16" loading="lazy">
        <div class="tg-icon"><svg fill="none" stroke="white" stroke-width="1.75" viewBox="0 0 24 24"><path d="M15 3h6m0 0v6m0-6l-7 7M9 21H3m0 0v-6m0 6l7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
    </div>

    <div class="tg-item" data-src="images/gallery/14.jpg">
        <img src="images/gallery/14.jpg" alt="Дивная усадьба — фото 17" loading="lazy">
        <div class="tg-icon"><svg fill="none" stroke="white" stroke-width="1.75" viewBox="0 0 24 24"><path d="M15 3h6m0 0v6m0-6l-7 7M9 21H3m0 0v-6m0 6l7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
    </div>

    <div class="tg-item" data-src="images/gallery/15.jpg">
        <img src="images/gallery/15.jpg" alt="Дивная усадьба — фото 18" loading="lazy">
        <div class="tg-icon"><svg fill="none" stroke="white" stroke-width="1.75" viewBox="0 0 24 24"><path d="M15 3h6m0 0v6m0-6l-7 7M9 21H3m0 0v-6m0 6l7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
    </div>

    <div class="tg-item" data-src="images/gallery/17.jpg">
        <img src="images/gallery/17.jpg" alt="Дивная усадьба — фото 19" loading="lazy">
        <div class="tg-icon"><svg fill="none" stroke="white" stroke-width="1.75" viewBox="0 0 24 24"><path d="M15 3h6m0 0v6m0-6l-7 7M9 21H3m0 0v-6m0 6l7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
    </div>

    <div class="tg-item" data-src="images/gallery/18.jpg">
        <img src="images/gallery/18.jpg" alt="Дивная усадьба — фото 20" loading="lazy">
        <div class="tg-icon"><svg fill="none" stroke="white" stroke-width="1.75" viewBox="0 0 24 24"><path d="M15 3h6m0 0v6m0-6l-7 7M9 21H3m0 0v-6m0 6l7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
    </div>

    <div class="tg-item" data-src="images/gallery/IMG-1747e6202202ff00.jpg">
        <img src="images/gallery/IMG-1747e6202202ff00.jpg" alt="Дивная усадьба — фото 21" loading="lazy">
        <div class="tg-icon"><svg fill="none" stroke="white" stroke-width="1.75" viewBox="0 0 24 24"><path d="M15 3h6m0 0v6m0-6l-7 7M9 21H3m0 0v-6m0 6l7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
    </div>

    <div class="tg-item" data-src="images/gallery/2w.jpg">
        <img src="images/gallery/2w.jpg" alt="Дивная усадьба — фото 22" loading="lazy">
        <div class="tg-icon"><svg fill="none" stroke="white" stroke-width="1.75" viewBox="0 0 24 24"><path d="M15 3h6m0 0v6m0-6l-7 7M9 21H3m0 0v-6m0 6l7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
    </div>

    <div class="tg-item" data-src="images/gallery/3w.jpg">
        <img src="images/gallery/3w.jpg" alt="Дивная усадьба — фото 23" loading="lazy">
        <div class="tg-icon"><svg fill="none" stroke="white" stroke-width="1.75" viewBox="0 0 24 24"><path d="M15 3h6m0 0v6m0-6l-7 7M9 21H3m0 0v-6m0 6l7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
    </div>

    <div class="tg-item" data-src="images/gallery/4w.jpg">
        <img src="images/gallery/4w.jpg" alt="Дивная усадьба — фото 24" loading="lazy">
        <div class="tg-icon"><svg fill="none" stroke="white" stroke-width="1.75" viewBox="0 0 24 24"><path d="M15 3h6m0 0v6m0-6l-7 7M9 21H3m0 0v-6m0 6l7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
    </div>

</div>

<script>
(function () {
    var lb      = document.getElementById('tg-lightbox');
    var lbImg   = document.getElementById('tg-lb-img');
    var counter = document.getElementById('tg-counter');
    var items   = Array.from(document.querySelectorAll('#tg-grid .tg-item'));
    var srcs    = items.map(function (el) { return el.dataset.src; });
    var current = 0;

    function open(idx) {
        current = idx;
        lbImg.src = srcs[idx];
        counter.textContent = (idx + 1) + ' / ' + srcs.length;
        lb.classList.add('tg-open');
        document.body.style.overflow = 'hidden';
    }

    function close() {
        lb.classList.remove('tg-open');
        lbImg.src = '';
        document.body.style.overflow = '';
    }

    function prev() { open((current - 1 + srcs.length) % srcs.length); }
    function next() { open((current + 1) % srcs.length); }

    items.forEach(function (el, i) {
        el.addEventListener('click', function () { open(i); });
    });

    document.getElementById('tg-close').addEventListener('click', close);
    document.getElementById('tg-prev').addEventListener('click', function (e) { e.stopPropagation(); prev(); });
    document.getElementById('tg-next').addEventListener('click', function (e) { e.stopPropagation(); next(); });

    lb.addEventListener('click', function (e) { if (e.target === lb) close(); });

    document.addEventListener('keydown', function (e) {
        if (!lb.classList.contains('tg-open')) return;
        if (e.key === 'Escape')     close();
        if (e.key === 'ArrowLeft')  prev();
        if (e.key === 'ArrowRight') next();
    });
}());
</script>
