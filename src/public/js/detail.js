document.addEventListener('DOMContentLoaded', () => {
	const btn = document.querySelector('.js-like-btn');
	const countEl = document.querySelector('.js-like-count');
	if (!btn || !countEl) return;

	btn.addEventListener('click', async () => {
		const liked = btn.classList.contains('is-liked');
		const url = liked ? btn.dataset.unlikeUrl : btn.dataset.likeUrl;

		const res = await fetch(url, {
			method: liked ? 'DELETE' : 'POST',
			credentials: 'same-origin',
			headers: { 'X-CSRF-TOKEN': btn.dataset.csrf }
		});

    	if (!res.ok) return; // 失敗時は何もしない

		btn.classList.toggle('is-liked');
		const n = parseInt(countEl.textContent, 10) || 0;
		countEl.textContent = liked ? Math.max(0, n - 1) : n + 1;
	});
});