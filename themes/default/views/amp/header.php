<nav class="amp-oc-title-bar">
    <div>
        <a href="<?=Route::url('default')?>">
        	<?if (Theme::get('logo_url')!=''):?>
                <amp-img src="<?=Theme::get('logo_url')?>" width="32" height="32" class="amp-oc-site-icon"></amp-img>
            <?else:?>
	            <?=core::config('general.site_name')?>
            <?endif?>
        </a>
    </div>
</nav>