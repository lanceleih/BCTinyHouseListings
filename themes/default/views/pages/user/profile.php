<?php defined('SYSPATH') or die('No direct script access.');?>

<div class="page-header">
    <h3><?=_e('User Profile')?></h3>
</div>

<div class="row">
    <div class="col-xs-3">
        <a class="thumbnail">
            <picture>
                <?=HTML::picture($user->get_profile_image(), array('w' => 142, 'h' => 142), array('1200px' => array('w' => '179', 'h' => '179'), '992px' => array('w' => '142', 'h' => '142'), '768px' => array('w' => '205', 'h' => '205'), '480px' => array('w' => '152', 'h' => '152'), '320px' => array('w' => '80', 'h' => '80')), array('class' => 'img-rounded img-responsive', 'alt' => __('Profile Picture')))?>
            </picture>
        </a>
    </div>
    <div class="col-xs-9">
        <div class="text-description">
            <?=$user->description?>
        </div>
    </div>
</div>

<div class="page-header">
    <article class="well">
        <h3><?=$user->name?></h3>
        <ul class="list-unstyled">
            <?if (Core::config('advertisement.reviews')==1):?>
                <li>
                    <?if ($user->rate!==NULL):?>
                        <?for ($i=0; $i < round($user->rate,1); $i++):?>
                            <span class="glyphicon glyphicon-star"></span>
                        <?endfor?>
                    <?endif?>
                </li>
            <?endif?>
            <li><strong><?=_e('Created')?>:</strong> <?= Date::format($user->created, core::config('general.date_format')) ?></li>
            <?if ($user->last_login!=NULL):?>
            <li><strong><?=_e('Last Login')?>:</strong> <?= Date::format($user->last_login, core::config('general.date_format'))?></li>
            <?endif?>
            <?if (Theme::get('premium')==1):?>
            <?foreach ($user->custom_columns(TRUE) as $name => $value):?>
            	<?if($value!=''):?>
	            	<li>
	                    <strong><?=$name?>:</strong>
	                    <?if($value=='checkbox_1'):?>
	                        <i class="fa fa-check"></i>
	                    <?elseif($value=='checkbox_0'):?>
	                        <i class="fa fa-times"></i>
	                    <?else:?>
	                        <?=$value?>
	                    <?endif?>
	            	</li>
	            <?endif?>
            <?endforeach?>
            <?if(isset($user->cf_whatsapp) AND $user->cf_whatsapp!=''):?>
                <li><?=$user->cf_whatsapp?> <i class="fa fa-2x fa-whatsapp" alt="Whatsapp" title="Whatsapp" style="color:#43d854"></i></li>
            <?endif?>
            <?endif?>
        </ul>
		<div class="clearfix">&nbsp;</div>
        <!-- Popup contact form -->
        <?if (core::config('general.messaging') == TRUE AND !Auth::instance()->logged_in()) :?>
            <a class="btn btn-success" data-toggle="modal" data-dismiss="modal" href="<?=Route::url('oc-panel',array('directory'=>'user','controller'=>'auth','action'=>'login'))?>#login-modal">
                <i class="glyphicon glyphicon-envelope"></i>
                <?=_e('Send Message')?>
            </a>
        <?else :?>
            <button class="btn btn-success" type="button" data-toggle="modal" data-target="#contact-modal"><i class="glyphicon glyphicon-envelope"></i> <?=_e('Send Message')?></button>
        <?endif?>
        <div id="contact-modal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                         <a class="close" data-dismiss="modal" aria-hidden="true">&times;</a>
                        <h3><?=_e('Contact')?></h3>
                    </div>
                    
                    <div class="modal-body">
                        <?=Form::errors()?>
                        
                        <?= FORM::open(Route::url('default', array('controller'=>'contact', 'action'=>'userprofile_contact', 'id'=>$user->id_user)), array('class'=>'form-horizontal well', 'enctype'=>'multipart/form-data'))?>
                            <fieldset>
                                <?if (!Auth::instance()->get_user()):?>
                                    <div class="form-group">
                                        <?= FORM::label('name', _e('Name'), array('class'=>'col-md-2 control-label', 'for'=>'name'))?>
                                        <div class="col-md-4 ">
                                            <?= FORM::input('name', Core::request('name'), array('placeholder' => __('Name'), 'class' => 'form-control', 'id' => 'name', 'required'))?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <?= FORM::label('email', _e('Email'), array('class'=>'col-md-2 control-label', 'for'=>'email'))?>
                                        <div class="col-md-4 ">
                                            <?= FORM::input('email', Core::request('email'), array('placeholder' => __('Email'), 'class' => 'form-control', 'id' => 'email', 'type'=>'email','required'))?>
                                        </div>
                                    </div>
                                <?endif?>
                                <?if(core::config('general.messaging') != TRUE):?>
                                    <div class="form-group">
                                        <?= FORM::label('subject', _e('Subject'), array('class'=>'col-md-2 control-label', 'for'=>'subject'))?>
                                        <div class="col-md-4 ">
                                            <?= FORM::input('subject', Core::request('subject'), array('placeholder' => __('Subject'), 'class' => 'form-control', 'id' => 'subject'))?>
                                        </div>
                                    </div>
                                <?endif?>
                                <div class="form-group">
                                    <?= FORM::label('message', _e('Message'), array('class'=>'col-md-2 control-label', 'for'=>'message'))?>
                                    <div class="col-md-6">
                                        <?= FORM::textarea('message', Core::post('subject'), array('class'=>'form-control', 'placeholder' => __('Message'), 'name'=>'message', 'id'=>'message', 'rows'=>2, 'required'))?>
                                        </div>
                                </div>
                                <?if (core::config('advertisement.captcha') != FALSE):?>
                                    <div class="form-group">
                                        <?= FORM::label('captcha', _e('Captcha'), array('class'=>'col-md-2 control-label', 'for'=>'captcha'))?>
                                        <div class="col-md-4">
                                            <?if (Core::config('general.recaptcha_active')):?>
                                                <?=Captcha::recaptcha_display()?>
                                                <div id="recaptcha1"></div>
                                            <?else:?>
                                                <?=captcha::image_tag('contact')?><br />
                                                <?= FORM::input('captcha', "", array('class' => 'form-control', 'id' => 'captcha', 'required'))?>
                                            <?endif?>
                                        </div>
                                    </div>
                                <?endif?>
                                <div class="modal-footer">	
                                    <?= FORM::button('submit', _e('Send Message'), array('type'=>'submit', 'class'=>'btn btn-success', 'action'=>Route::url('default', array('controller'=>'contact', 'action'=>'userprofile_contact' , 'id'=>$user->id_user))))?>
                                </div>
                            </fieldset>
                        <?= FORM::close()?>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix">&nbsp;</div>
        <?if (Theme::get('premium')==1):?>
            <p>
              	<?if(isset($user->cf_skype) AND $user->cf_skype!=''):?>
                    <a href="skype:<?=$user->cf_skype?>?chat" title="Skype" alt="Skype"><i class="fa fa-2x fa-skype" style="color:#00aff0"></i></a>
                <?endif?>
                <?if(isset($user->cf_telegram) AND $user->cf_telegram!=''):?>
                    <a href="tg://resolve?domain=<?=$user->cf_telegram?>" id="telegram" title="Telegram" alt="Telegram"><i class="glyphicon fa-2x glyphicon-send" style="color:#0088cc"></i></a>
                <?endif?>
            </p>
        <?endif?>
	</article>
</div>
<div class="page-header">
    <h3><?=$user->name.' '._e(' advertisements')?></h3>

    <?if($profile_ads!==NULL):?>
        <?foreach($profile_ads as $ad):?>
            <?if($ad->featured >= Date::unix2mysql(time())):?>
                <article class="well featured">
                    <span class="label label-danger pull-right"><?=_e('Featured')?></span>
            <?else:?>
                <article class="well">
            <?endif?>
                
                <h4><a href="<?=Route::url('ad', array('controller'=>'ad','category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))?>"><?=$ad->title?></a></h4>

                <div class="picture">
                    <a class="pull-left" title="<?=HTML::chars($ad->title)?>" href="<?=Route::url('ad', array('controller'=>'ad','category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))?>">
                        <figure>
                            <?if($ad->get_first_image() !== NULL):?>
                                  <img src="<?=Core::imagefly($ad->get_first_image(),150,150)?>" alt="<?=HTML::chars($ad->title)?>" />
                              <?elseif(( $icon_src = $ad->category->get_icon() )!==FALSE ):?>
                                  <img src="<?=Core::imagefly($icon_src,150,150)?>" class="img-responsive" alt="<?=HTML::chars($ad->title)?>" />
                              <?elseif(( $icon_src = $ad->location->get_icon() )!==FALSE ):?>
                                  <img src="<?=Core::imagefly($icon_src,150,150)?>" class="img-responsive" alt="<?=HTML::chars($ad->title)?>" />
                              <?else:?>
                                  <img data-src="holder.js/150x150?<?=str_replace('+', ' ', http_build_query(array('text' => $ad->category->name, 'size' => 14, 'auto' => 'yes')))?>" class="img-responsive" alt="<?=HTML::chars($ad->title)?>"> 
                              <?endif?>
                        </figure>
                    </a>
                </div>

                <p><strong><?=_e('Description')?>: </strong><?=Text::removebbcode($ad->description)?><p>
                <p><b><?=_e('Publish Date');?>:</b> <?= Date::format($ad->published, core::config('general.date_format'))?><p>
                
                <?$visitor = Auth::instance()->get_user()?>
                
                <?if ($visitor != FALSE && $visitor->id_role == 10):?>
                    <br>
                    <a href="<?=Route::url('oc-panel', array('controller'=>'myads','action'=>'update','id'=>$ad->id_ad))?>"><?=_e("Edit");?></a> |
                    <a href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'deactivate','id'=>$ad->id_ad))?>" 
                        onclick="return confirm('<?=__('Deactivate?')?>');"><?=_e("Deactivate");?>
                    </a> |
                    <a href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'spam','id'=>$ad->id_ad))?>" 
                        onclick="return confirm('<?=__('Spam?')?>');"><?=_e("Spam");?>
                    </a> |
                    <a href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'delete','id'=>$ad->id_ad))?>" 
                        onclick="return confirm('<?=__('Delete?')?>');"><?=_e("Delete");?>
                    </a>
                <?elseif($visitor != FALSE && $visitor->id_user == $ad->id_user):?>
                    <br>
                    <a href="<?=Route::url('oc-panel', array('controller'=>'myads','action'=>'update','id'=>$ad->id_ad))?>"><?=_e("Edit");?></a> 
                <?endif?>
                <div class="clearfix"></div>
            </article>
        <?endforeach?>
        <?=$pagination?>
    <?endif?>

</div>
	
