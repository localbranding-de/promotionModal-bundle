<?php 

array_insert($GLOBALS['BE_MOD'], 1,
array(
    
    'lb_promo' => array
    (
        'lb_promo' => array
        (
            'tables'        => array('tl_lb_promo')
        )
    )
)
);

if ('BE' === TL_MODE) {
    
    $GLOBALS['TL_CSS'][] = '/bundles/promotionmodal/css/lb_be_icon.css';

    
    
}
if(\Input::get('do') == 'lb_promo'&&\Input::get('id'))
{
    $GLOBALS['TL_CSS'][] = '/bundles/promotionmodal/css/lb_be_promo.css';
    
}

$GLOBALS['TL_HOOKS']['parseFrontendTemplate'][] = array(\LocalbrandingDe\PromotionModalBundle\EventListener\PromoFrontendTemplateListener::class, 'lbPromoParseFrontendTemplate');
