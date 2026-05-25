<?php
/*
	Aurora Theme for Question2Answer
	A modern, app-style responsive theme with light/dark mode, left sidebar
	navigation on desktop and a bottom tab bar on mobile.

	File:           qa-theme.php
	Version:        Aurora 1.0

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 2 of the License, or
	(at your option) any later version.
*/

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}

class qa_html_theme extends qa_html_theme_base
{
	protected $theme = 'aurora';

	private $remixicon_css = 'https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css';

	// remember which nav item we're rendering so we can attach an icon
	private $cur_nav_key = null;

	// map main-nav keys to Remix Icon glyph classes
	private $nav_icons = array(
		'questions'   => 'ri-chat-3-line',
		'hot'         => 'ri-fire-line',
		'unanswered'  => 'ri-question-line',
		'tag'         => 'ri-price-tag-3-line',
		'tags'        => 'ri-price-tag-3-line',
		'categories'  => 'ri-folders-line',
		'users'       => 'ri-team-line',
		'ask'         => 'ri-add-circle-line',
		'admin'       => 'ri-settings-3-line',
		'account'     => 'ri-user-line',
		'login'       => 'ri-login-circle-line',
		'register'    => 'ri-user-add-line',
		'logout'      => 'ri-logout-circle-line',
		'profile'     => 'ri-user-line',
		'updates'     => 'ri-notification-3-line',
		'pdf'         => 'ri-file-pdf-2-line',
		'moderate'    => 'ri-shield-check-line',
	);

	/* ---------------------------------------------------------------------
	   <head>
	   --------------------------------------------------------------------- */

	public function head_metas()
	{
		$this->output('<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>');
		$this->output('<meta name="theme-color" content="#2563eb"/>');
		parent::head_metas();
	}

	public function head_css()
	{
		$this->output(
			'<link rel="preconnect" href="https://fonts.googleapis.com"/>',
			'<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>'
		);
		$this->content['css_src'][] = 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap';
		$this->content['css_src'][] = $this->remixicon_css;

		parent::head_css();
	}

	/**
	 * Runs LAST in <head>, so this is where we (a) avoid the dark-mode flash and
	 * (b) neutralise leftover "Donut theme" CSS that the prod DB injects ahead of us.
	 */
	public function head_custom()
	{
		// set colour scheme before first paint
		$this->output(
			'<script>',
			'(function(){try{',
			"var t=localStorage.getItem('aurora-theme');",
			"if(t!=='light'&&t!=='dark'){t=window.matchMedia&&window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light';}",
			"document.documentElement.setAttribute('data-theme',t);",
			'}catch(e){}})();',
			'</script>'
		);

		// neutralise the leftover "Donut theme" CSS the prod DB injects after our stylesheet
		$this->output(
			'<style>',
			'body{padding-top:0 !important;}',
			'.qa-q-list-item{padding:14px 16px !important;border:1px solid var(--card-border) !important;border-radius:16px !important;margin:0 0 10px !important;}',
			'.qa-q-item-main{box-shadow:none !important;}',
			'.qa-nav-main{border:0 !important;border-radius:0 !important;}',
			'.qa-sidebar,.qa-widget-side,.qa-part-q-view{border:1px solid var(--line) !important;}',
			'.qa-footer{background:transparent !important;border:0 !important;border-radius:0 !important;}',
			'.qa-footer,.qa-footer a{color:var(--text-muted) !important;}',
			'.qa-logo{margin:0 !important;}',
			'.aurora-logo .qa-logo-link img,.aurora-logo img{height:26px !important;width:auto !important;}',
			'.qa-q-item-tag-item{box-shadow:none !important;margin:0 !important;border-radius:999px !important;}',
			'</style>'
		);

		parent::head_custom();
	}

	public function head_script()
	{
		$v = @filemtime(__DIR__ . '/js/aurora.js') ?: QA_VERSION;
		$this->content['script'][] = '<script src="' . $this->rooturl . 'js/aurora.js?' . $v . '" defer></script>';
		parent::head_script();
	}

	/**
	 * Cache-bust the stylesheet by its modification time (handy during development).
	 */
	public function css_name()
	{
		$v = @filemtime(__DIR__ . '/qa-styles.css') ?: QA_VERSION;
		return 'qa-styles.css?' . $v;
	}

	/* ---------------------------------------------------------------------
	   App shell: sidebar (left) + topbar + content + rail (right) + bottom nav
	   --------------------------------------------------------------------- */

	public function body_content()
	{
		$this->body_prefix();
		$this->notices();

		$this->widgets('full', 'top');

		$this->output('<div class="aurora-shell">');

		$this->aurora_sidebar();

		$this->output('<div class="aurora-center">');
		$this->aurora_topbar();
		$this->output('<div class="aurora-content">');
		$this->aurora_subnav();
		$this->widgets('full', 'high');
		$this->main();
		$this->widgets('full', 'low');
		$this->output('</div> <!-- /aurora-content -->');
		$this->output('</div> <!-- /aurora-center -->');

		$this->output('<div class="aurora-rail">');
		$this->sidepanel();
		$this->output('</div> <!-- /aurora-rail -->');

		$this->output('</div> <!-- /aurora-shell -->');

		$this->widgets('full', 'bottom');

		$this->aurora_bottomnav();
		$this->footer();

		$this->body_suffix();
	}

	private function aurora_sidebar()
	{
		$this->output('<aside class="aurora-sidebar" id="aurora-sidebar">');

		$this->output('<div class="aurora-brand">');
		$this->logo();
		$this->output('</div>');

		// primary call-to-action
		$this->output(
			'<a class="aurora-ask-cta" href="' . qa_path_html('ask') . '">',
			'<i class="ri-add-line"></i><span>Ask Question</span>',
			'</a>'
		);

		$this->output('<nav class="aurora-nav-main-wrap">');
		$this->output('<div class="aurora-nav-label">Menu</div>');
		$this->nav('main');
		$this->output('</nav>');

		$this->aurora_user_chip();

		$this->output('</aside>');
		$this->output('<div class="aurora-scrim" id="aurora-scrim"></div>');
	}

	/**
	 * Brand: the real uploaded logo image, placed on a light "logo chip" so a
	 * dark/colour logo stays visible against the dark sidebar.
	 */
	public function logo()
	{
		$logo = isset($this->content['logo']) && strlen($this->content['logo'])
			? $this->content['logo']
			: '<a href="' . qa_path_html('') . '">' . qa_html(qa_opt('site_title')) . '</a>';

		$this->output('<div class="aurora-logo">', $logo, '</div>');
	}

	/** Identity lives in the sidebar chip, so drop the topbar "Hello X" greeting. */
	public function logged_in()
	{
		// intentionally empty
	}

	private function aurora_user_chip()
	{
		$this->output('<div class="aurora-user-chip">');

		if (qa_is_logged_in()) {
			$handle = qa_get_logged_in_handle();
			$points = (int) qa_get_logged_in_points();
			$ptsText = qa_html(qa_format_number($points, 0, true)) . ' pts';
			$this->output(
				'<a class="aurora-user-link" href="' . qa_path_html('user/' . $handle) . '">',
				'<span class="aurora-user-av">' . $this->aurora_logged_in_avatar(40) . '</span>',
				'<span class="aurora-user-meta">',
				'<span class="aurora-user-name">' . qa_html($handle) . '</span>',
				'<span class="aurora-user-pts">' . $ptsText . '</span>',
				'</span>',
				'<i class="ri-arrow-right-s-line aurora-user-go"></i>',
				'</a>'
			);
		} else {
			$this->output(
				'<a class="aurora-signin" href="' . qa_path_html('login') . '">',
				'<i class="ri-login-circle-line"></i><span>Sign in</span>',
				'</a>'
			);
		}

		$this->output('</div>');
	}

	private function aurora_logged_in_avatar($size)
	{
		if (QA_FINAL_EXTERNAL_USERS)
			return qa_get_external_avatar_html(qa_get_logged_in_user_field('userid'), $size, true);

		return qa_get_user_avatar_html(
			qa_get_logged_in_user_field('flags'),
			qa_get_logged_in_user_field('email'),
			qa_get_logged_in_handle(),
			qa_get_logged_in_user_field('avatarblobid'),
			qa_get_logged_in_user_field('avatarwidth'),
			qa_get_logged_in_user_field('avatarheight'),
			$size,
			false
		);
	}

	/* ---------------------------------------------------------------------
	   Voting rendered as thumbs up / down icons
	   --------------------------------------------------------------------- */

	private function button_icon($class)
	{
		if (strpos($class, 'vote') !== false) {
			$voted = strpos($class, 'voted') !== false;
			if (strpos($class, '-up') !== false)
				return $voted ? 'ri-thumb-up-fill' : 'ri-thumb-up-line';
			if (strpos($class, '-down') !== false)
				return $voted ? 'ri-thumb-down-fill' : 'ri-thumb-down-line';
		}
		if (strpos($class, 'a-unselect') !== false)
			return 'ri-checkbox-circle-fill';
		if (strpos($class, 'a-select') !== false)
			return 'ri-checkbox-circle-line';
		return '';
	}

	public function post_hover_button($post, $element, $value, $class)
	{
		if (!isset($post[$element]))
			return;
		$icon = $this->button_icon($class);
		if ($icon !== '') {
			$this->output('<button ' . $post[$element] . ' type="submit" class="' . $class . '-button" title="' . qa_html($value) . '"><i class="' . $icon . '"></i></button> ');
			return;
		}
		parent::post_hover_button($post, $element, $value, $class);
	}

	public function post_disabled_button($post, $element, $value, $class)
	{
		if (!isset($post[$element]))
			return;
		$icon = $this->button_icon($class);
		if ($icon !== '') {
			$this->output('<button ' . $post[$element] . ' type="submit" class="' . $class . '-disabled" disabled="disabled"><i class="' . $icon . '"></i></button> ');
			return;
		}
		parent::post_disabled_button($post, $element, $value, $class);
	}

	/** Favourite toggle with a bookmark icon (was an empty square). */
	public function favorite_button($tags, $class)
	{
		if (isset($tags)) {
			$icon = strpos($class, 'unfavorite') !== false ? 'ri-bookmark-fill' : 'ri-bookmark-line';
			$this->output('<button ' . $tags . ' class="' . $class . '-button" title="Favorite"><i class="' . $icon . '"></i></button>');
		}
	}

	/* ---------------------------------------------------------------------
	   Post actions collapsed into a "⋮" menu
	   --------------------------------------------------------------------- */

	private function aurora_action_menu($form, $class)
	{
		$this->output('<div class="' . $class . ' aurora-actions">');
		$this->output('<button type="button" class="aurora-actions-toggle" aria-haspopup="true" aria-expanded="false" aria-label="More actions"><i class="ri-more-2-fill"></i></button>');
		$this->output('<div class="aurora-actions-menu">');
		$this->form($form);
		$this->output('</div>');
		$this->output('</div>');
	}

	public function q_view_buttons($q_view)
	{
		if (!empty($q_view['form']))
			$this->aurora_action_menu($q_view['form'], 'qa-q-view-buttons');
	}

	public function a_item_buttons($a_item)
	{
		if (!empty($a_item['form']))
			$this->aurora_action_menu($a_item['form'], 'qa-a-item-buttons');
	}

	public function c_item_buttons($c_item)
	{
		if (!empty($c_item['form']))
			$this->aurora_action_menu($c_item['form'], 'qa-c-item-buttons');
	}

	/** Sub navigation rendered as a horizontal tab bar at the top of the content
	 *  (sort tabs on lists, profile tabs on user pages). */
	private function aurora_subnav()
	{
		if (isset($this->content['navigation']['sub'])) {
			$this->output('<div class="aurora-subnav">');
			$this->nav('sub');
			$this->output('</div>');
		}
	}

	private function aurora_topbar()
	{
		$this->output('<header class="aurora-topbar">');

		$this->output('<button type="button" class="aurora-menu-toggle" id="aurora-menu-toggle" aria-label="Menu"><i class="ri-menu-line"></i></button>');

		$this->search();

		$this->output('<div class="aurora-topbar-right">');
		$this->output(
			'<button type="button" class="aurora-theme-toggle" aria-label="Toggle dark mode" title="Toggle dark mode">',
			'<i class="ri-moon-line aurora-icon-moon"></i>',
			'<i class="ri-sun-line aurora-icon-sun"></i>',
			'</button>'
		);
		$this->nav('user');
		$this->output('</div>');

		$this->output('</header>');
	}

	private function aurora_bottomnav()
	{
		// mobile: a single floating "Ask" action button (other nav lives in the drawer)
		$this->output(
			'<a class="aurora-fab" href="' . qa_path_html('ask') . '" aria-label="Ask a question" title="Ask a question">',
			'<i class="ri-add-line"></i>',
			'</a>'
		);
	}

	/* ---------------------------------------------------------------------
	   Nav items with icons
	   --------------------------------------------------------------------- */

	public function nav_item($key, $navlink, $class, $level = null)
	{
		$this->cur_nav_key = $key;
		parent::nav_item($key, $navlink, $class, $level);
	}

	public function nav_link($navlink, $class)
	{
		if (($class === 'nav-main' || $class === 'nav-user') && isset($navlink['label'])) {
			$key = $this->cur_nav_key;
			if (isset($this->nav_icons[$key]))
				$icon = $this->nav_icons[$key];
			elseif (is_string($key) && strpos($key, 'custom-') === 0)
				$icon = 'ri-external-link-line';
			else
				$icon = $class === 'nav-main' ? 'ri-arrow-right-s-line' : 'ri-circle-line';

			$navlink['label'] = '<i class="' . $icon . ' aurora-nav-ic"></i><span class="aurora-nav-tx">' . $navlink['label'] . '</span>';
		}

		parent::nav_link($navlink, $class);
	}

	/* ---------------------------------------------------------------------
	   Card stats footer: votes + answers + views together
	   --------------------------------------------------------------------- */

	public function q_item_stats($q_item)
	{
		$this->output('<div class="qa-q-item-stats">');
		$this->voting($q_item);
		$this->a_count($q_item);
		parent::view_count($q_item);
		$this->output('</div>');
	}

	public function q_view_stats($q_view)
	{
		$this->output('<div class="qa-q-view-stats">');
		$this->voting($q_view);
		$this->a_count($q_view);
		parent::view_count($q_view);
		$this->output('</div>');
	}

	/** Views are rendered inside the stats footer instead of the default spot. */
	public function view_count($post)
	{
		// no-op
	}

	/** Best-answer indicator with an icon in the badge. */
	public function a_selection($post)
	{
		$this->output('<div class="qa-a-selection">');

		if (isset($post['select_tags']))
			$this->post_hover_button($post, 'select_tags', '', 'qa-a-select');
		elseif (isset($post['unselect_tags']))
			$this->post_hover_button($post, 'unselect_tags', '', 'qa-a-unselect');
		elseif (!empty($post['selected']))
			$this->output('<div class="qa-a-selected"></div>');

		if (isset($post['select_text']))
			$this->output('<div class="qa-a-selected-text">' . qa_html($post['select_text']) . '</div>');

		$this->output('</div>');
	}

	/* ---------------------------------------------------------------------
	   Search niceties
	   --------------------------------------------------------------------- */

	public function search_field($search)
	{
		$this->output('<input type="text" placeholder="' . $search['button_label'] . '…" ' . $search['field_tags'] . ' value="' . @$search['value'] . '" class="qa-search-field"/>');
	}

	public function search_button($search)
	{
		$this->output('<button type="submit" class="qa-search-button" aria-label="' . $search['button_label'] . '"><i class="ri-search-2-line"></i></button>');
	}

	public function attribution()
	{
		$this->output('<div class="qa-attribution aurora-attribution">Aurora theme</div>');
		parent::attribution();
	}
}
