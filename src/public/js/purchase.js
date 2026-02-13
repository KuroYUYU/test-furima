document.addEventListener('DOMContentLoaded', () => {
	const select = document.getElementById('payment_method');
	const label = document.querySelector('.js-payment-label');
	if (!select || !label) return;

	const sync = () => {
		const opt = select.options[select.selectedIndex];
		label.textContent = opt && opt.value ? opt.textContent : '';
	};

	sync();
	select.addEventListener('change', sync);
});
