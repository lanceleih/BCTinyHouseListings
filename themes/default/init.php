<?php defined('SYSPATH') or die('No direct access allowed.');
/**
  * Theme Name: Ocean Free
  * Description: Clean free theme that includes full admin. It has publicity. Do not delete this theme, all the views depend in this theme.
  * Tags: HTML5, Admin, Free
  * Version: 3.2.0
  * Author: Chema <chema@open-classifieds.com> , <slobodan@open-classifieds.com>
  * License: GPL v3
  * Skins: default,green,orange 
  */


/**
 * placeholders for this theme
 */
Widgets::$theme_placeholders	= array('footer', 'sidebar', 'publish_new');

/**
 * custom options for the theme
 * @var array
 */
Theme::$options = Theme::get_options();

//we load earlier the theme since we need some info
Theme::load();

/**
 * styles and themes, loaded in this order
 */
Theme::$skin = Theme::get('theme');

/**
 * styles and themes, loaded in this order
 */

Theme::$styles = array( '//cdn.jsdelivr.net/bootstrap/3.3.7/css/bootstrap.min.css' => 'screen',
                        '//cdn.jsdelivr.net/fontawesome/4.7.0/css/font-awesome.min.css' => 'screen',
                        '//cdn.jsdelivr.net/bootstrap.image-gallery/3.1.0/css/bootstrap-image-gallery.min.css' => 'screen',
                        '//cdn.jsdelivr.net/blueimp-gallery/2.15.0/css/blueimp-gallery.min.css' => 'screen', 
                        '//cdn.jsdelivr.net/bootstrap.datepicker/0.1/css/datepicker.css' => 'screen',
                        '//cdn.jsdelivr.net/select2/4.0.2/css/select2.min.css' => 'screen',
                        'css/styles.css?v='.Core::VERSION => 'screen',
                        'css/slider.css' => 'screen',
                    );

if (Theme::$skin!='default')
    Theme::$styles = array_merge(Theme::$styles, array('css/color-'.Theme::$skin.'.css' => 'screen'));

Theme::$scripts['footer'] = array(  '//cdn.jsdelivr.net/g/jquery@1.12.4,bootstrap@3.3.7,select2@4.0.3,jquery.validation@1.15.0,holder@2.9.3',
                                    '//cdn.jsdelivr.net/blueimp-gallery/2.15.0/js/jquery.blueimp-gallery.min.js',
                                    '//cdn.jsdelivr.net/bootstrap.image-gallery/3.1.0/js/bootstrap-image-gallery.min.js',
                                    Route::url('jslocalization', array('controller'=>'jslocalization', 'action'=>'select2')),
                                    Route::url('jslocalization', array('controller'=>'jslocalization', 'action'=>'validate')),
                                    'js/bootstrap-slider.js',
                                    'js/favico.min.js',
                                    'js/curry.js',
                                    'js/bootstrap-datepicker.js',
                                    'js/default.init.js?v='.Core::VERSION,
                                    'js/theme.init.js?v='.Core::VERSION,
                                    );

if (Auth::instance()->logged_in() AND 
    (Auth::instance()->get_user()->is_admin() OR 
        Auth::instance()->get_user()->is_moderator() OR
        Auth::instance()->get_user()->is_translator()))
{
    Theme::$styles['css/bootstrap-editable.css'] = 'screen';
    Theme::$scripts['footer'][] = 'js/bootstrap-editable.min.js';
    Theme::$scripts['footer'][] = 'js/oc-panel/live-translator.js';
}

if (Core::config('general.pusher_notifications')){
    Theme::$styles['//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css'] = 'screen';
    Theme::$scripts['footer'][] = '//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js';
    Theme::$scripts['footer'][] = '//js.pusher.com/4.0/pusher.min.js';
}

if (Core::config('general.algolia_search')){
    Theme::$styles['css/algolia/algolia-autocomplete.css'] = 'screen';
    Theme::$scripts['footer'][] = '//cdn.jsdelivr.net/algoliasearch/3/algoliasearch.min.js';
    Theme::$scripts['footer'][] = '//cdn.jsdelivr.net/autocomplete.js/0/autocomplete.jquery.min.js';
}

/**
 * custom error alerts
 */
Form::$errors_tpl 	= '<div class="alert alert-danger"><a class="close" data-dismiss="alert">×</a>
			       		<p><strong>%s</strong></p>
			        	<ul>%s</ul></div>';

Form::$error_tpl 	= '<div class="alert "><a class="close" data-dismiss="alert">×</a>%s</div>';


Alert::$tpl 	= 	'<div class="alert alert-%s">
					<a class="close" data-dismiss="alert" href="#">×</a>
					<strong>%s:</strong> %s
					</div>';
