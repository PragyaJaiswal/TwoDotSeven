<?php
namespace TwoDot7\Admin\Dashboard;

#  _____                      _____ 
# /__   \__      _____       |___  |      ___     __      _    
#   / /\/\ \ /\ / / _ \         / /      / _ |___/ /_ _  (_)__ 
#  / /    \ V  V / (_) |  _    / /      / __ / _  /  ' \/ / _ \
#  \/      \_/\_/ \___/  (_)  /_/      /_/ |_\_,_/_/_/_/_/_//_/

/**
 * init throws the Markup.
 * @return	null
 * @author	Prashant Sinha <firstname,lastname>@outlook.com
 * @since	v0.0 18072014
 * @version	0.0
 */
function init() {

	\TwoDot7\Admin\Template\Dash_Broadcasts_Bits\_init(array(
		'Page' => 'PRE_DASHBOARD',
		'Call' => 'Dashboard',
		'Navigation' => \TwoDot7\Meta\Navigation::Get(array(
			'Page' => 'PRE_DASHBOARD'
			))
		));
}