/*
	Aurora Theme for Question2Answer
	Progressive enhancement: dark-mode toggle, mobile sidebar drawer, header shadow.
*/
(function () {
	'use strict';

	var root = document.documentElement;

	function currentTheme() {
		return root.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
	}
	function setTheme(theme) {
		root.setAttribute('data-theme', theme);
		try { localStorage.setItem('aurora-theme', theme); } catch (e) {}
	}
	function onReady(fn) {
		if (document.readyState !== 'loading') fn();
		else document.addEventListener('DOMContentLoaded', fn);
	}

	onReady(function () {
		// Dark-mode toggle(s)
		var toggles = document.querySelectorAll('.aurora-theme-toggle');
		Array.prototype.forEach.call(toggles, function (btn) {
			btn.addEventListener('click', function () {
				setTheme(currentTheme() === 'dark' ? 'light' : 'dark');
			});
		});

		// Mobile sidebar drawer
		var sidebar = document.getElementById('aurora-sidebar');
		var scrim = document.getElementById('aurora-scrim');
		var menuBtn = document.getElementById('aurora-menu-toggle');

		function openDrawer() {
			if (sidebar) sidebar.classList.add('is-open');
			if (scrim) scrim.classList.add('is-open');
		}
		function closeDrawer() {
			if (sidebar) sidebar.classList.remove('is-open');
			if (scrim) scrim.classList.remove('is-open');
		}

		if (menuBtn) menuBtn.addEventListener('click', openDrawer);
		if (scrim) scrim.addEventListener('click', closeDrawer);
		// close the drawer after tapping a nav link
		if (sidebar) {
			sidebar.addEventListener('click', function (e) {
				if (e.target.closest('a')) closeDrawer();
			});
		}
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') closeDrawer();
		});

		// elevate the sticky topbar once the page is scrolled
		var topbar = document.querySelector('.aurora-topbar');
		if (topbar) {
			var onScroll = function () {
				if (window.scrollY > 4) topbar.classList.add('aurora-scrolled');
				else topbar.classList.remove('aurora-scrolled');
			};
			window.addEventListener('scroll', onScroll, { passive: true });
			onScroll();
		}

		// "⋮" action menus
		function closeAllActionMenus() {
			Array.prototype.forEach.call(document.querySelectorAll('.aurora-actions.is-open'), function (a) {
				a.classList.remove('is-open');
				var t = a.querySelector('.aurora-actions-toggle');
				if (t) t.setAttribute('aria-expanded', 'false');
			});
			Array.prototype.forEach.call(document.querySelectorAll('.aurora-menu-open'), function (c) {
				c.classList.remove('aurora-menu-open');
			});
		}
		document.addEventListener('click', function (e) {
			var toggle = e.target.closest('.aurora-actions-toggle');
			if (toggle) {
				var actions = toggle.closest('.aurora-actions');
				var willOpen = !actions.classList.contains('is-open');
				closeAllActionMenus();
				if (willOpen) {
					actions.classList.add('is-open');
					toggle.setAttribute('aria-expanded', 'true');
					// lift the whole post card above sibling glass cards so the menu isn't hidden
					var card = actions.closest('.qa-q-view, .qa-a-list-item');
					if (card) card.classList.add('aurora-menu-open');
				}
				return;
			}
			if (!e.target.closest('.aurora-actions-menu')) closeAllActionMenus();
		});
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') closeAllActionMenus();
		});
	});

	// Keep tabs in sync when the colour scheme changes elsewhere
	window.addEventListener('storage', function (e) {
		if (e.key === 'aurora-theme' && (e.newValue === 'light' || e.newValue === 'dark')) {
			root.setAttribute('data-theme', e.newValue);
		}
	});
})();
