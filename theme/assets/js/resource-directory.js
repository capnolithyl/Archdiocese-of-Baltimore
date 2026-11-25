// Preserve native disclosure and fragment navigation; close the menu after selection.
document.addEventListener('click', (event) => {
	if (!(event.target instanceof Element)) return;
	const link = event.target.closest('.balt-resource-directory__jump a');
	if (!link) return;
	const menu = link.closest('details');
	if (menu) menu.open = false;
});
