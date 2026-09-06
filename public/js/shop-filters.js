/**
 * فلترة AJAX مشتركة لصفحات المنتجات/اللابتوبات/البرامج — بدون تحميل صفحة كامل.
 * يعتمد على تواجد #filterForm و #resultsContainer بنفس الصفحة (اتفاقية موحدة
 * بين الصفحات الثلاث)، فما في حاجة لأي إعداد إضافي لكل صفحة.
 */
(function () {
    const form = document.getElementById('filterForm');
    const container = document.getElementById('resultsContainer');
    if (!form || !container) return;

    let inflight = null;
    let searchDebounce = null;

    function setLoading(isLoading) {
        container.classList.toggle('is-loading', isLoading);
    }

    function fetchAndSwap(url) {
        if (inflight) inflight.abort();
        const controller = new AbortController();
        inflight = controller;

        setLoading(true);
        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            signal: controller.signal,
        })
            .then((res) => res.text())
            .then((html) => {
                container.innerHTML = html;
                history.pushState(null, '', url);
            })
            .catch((err) => {
                if (err.name === 'AbortError') return;
                console.error('shop-filters: fetch failed', err);
            })
            .finally(() => {
                if (inflight === controller) {
                    setLoading(false);
                    inflight = null;
                }
            });
    }

    function submitForm() {
        const params = new URLSearchParams(new FormData(form));
        // فراغ بالبحث ما لازم يبعت name فاضي بالرابط
        const query = params.toString();
        const url = form.action + (query ? '?' + query : '');
        fetchAndSwap(url);
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        submitForm();
    });

    // بحث حي مع تأخير بسيط بعد آخر ضغطة، بدون الحاجة لزر Enter
    const searchInput = form.querySelector('#searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchDebounce);
            searchDebounce = setTimeout(submitForm, 400);
        });
    }

    // كل نقرة على رابط نتيجة/تصفية (شرائح الفلاتر النشطة أو أرقام الصفحات)
    // بتتلقّط هون — نفس الاستماع بضل شغال حتى لو استبدلنا المحتوى بالكامل،
    // لأنه معلّق على الحاوية الثابتة مش على العناصر نفسها.
    container.addEventListener('click', function (e) {
        const link = e.target.closest('a.filter-chip, .pagination a');
        if (!link) return;
        e.preventDefault();
        fetchAndSwap(link.href);
    });

    // زر رجوع/تقدّم بالمتصفح
    window.addEventListener('popstate', function () {
        fetchAndSwap(location.href);
    });
})();
