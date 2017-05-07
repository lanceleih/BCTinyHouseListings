<?php defined('SYSPATH') or die('No direct script access.');?>
<hr>
<footer>
    <div class="row">
        <?$i=0; foreach ( Widgets::render('footer') as $widget):?>
            <div class="col-md-3">
                <?=$widget?>
            </div>
            <? $i++; if ($i%4 == 0) echo '<div class="clearfix"></div>';?>
        <?endforeach?>
    </div>
    <!--This is the license for Open Classifieds, do not remove -->
    <div class="center-block">
        <p>&copy;
            <?if (Theme::get('premium')!=1):?>
                Web Powered by <a href="https://yclas.com?utm_source=<?=URL::base()?>&utm_medium=oc_footer&utm_campaign=<?=date('Y-m-d')?>" title="Best PHP Script Classifieds Software">Yclas</a> 
                2009 - <?=date('Y')?>
            <?else:?>
                <?=core::config('general.site_name')?> <?=date('Y')?>
            <?endif?>    
            <?if(Core::config('appearance.theme_mobile')!=''):?>
                - <a href="<?=Route::url('default')?>?theme=<?=Core::config('appearance.theme_mobile')?>"><?=_e('Mobile Version')?></a>
            <?endif?>
            <?if(Cookie::get('user_location')):?>
                - <a href="<?=Route::url('default')?>?user_location=0"><?=_e('Change Location')?></a>
            <?endif?>
        </p>
    </div>
</footer>