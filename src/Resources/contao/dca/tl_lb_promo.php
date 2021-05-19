<?php
/**
 * Table tl_lb_promo
 */


$GLOBALS['TL_DCA']['tl_lb_promo'] = array
(
    
    // Config
    'config' => array
    (
        'dataContainer'               => 'Table',
        'sql' => array
        (
            'keys' => array
            (
                'id' => 'primary',
                'promoTitle' => 'index'
            )
        )
    ),
    // Palettes
    'palettes' => array
    (
        'default'                     => '{typeAndDescription},promoTitle,promoDescription,promoTemplate,headline,bodytext;{promoButton}buttonLabel,buttonUrlBlog,buttonUrlExt,buttonProductDetail,buttonPageUrl,targetBlank;{promoNewsletterSpecial},newsletterFormAction,newsletterPrivacyConsent,newsletterHint;{publication},onPages,afterPageview,afterDuration,bestBefore,start,stop,published'       
    ),
  


    // Fields
    'fields' => array
    (
        'id' => array
        (
            'sql'                 => "int(10) unsigned NOT NULL auto_increment",
        ),
        
        'tstamp' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ),
        
        // ABSCHNITT Beschreibung und Typ (typeAndDescription)
        
        //eindeutiger Name !!
        'promoTitle' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_lb_promo']['promoTitle'],
            'inputType' => 'text',
            'eval'      => array('tl_class'=>'long','maxlength'=>100),
            'sql'       => "varchar(100) NOT NULL default ''"
        ),        
        'promoDescription' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_lb_promo']['promoDescription'],
            'inputType' => 'textarea',  // TinyMCE
            'eval'      => array('tl_class'=>'long','maxlength'=>255,'rte'=>'tinyMCE'),
            'sql'       => "varchar(256) NOT NULL default ''"
        ),

        
        'promoTemplate' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_lb_promo']['promoTemplate'],
            'inputType' => 'select',
            'exclude'   => true,
            'options_callback'      => ['tl_lb_promoClass', 'getPromoTemplates'],
            'eval'      => array('includeBlankOption'=>true, 'chosen'=>true, 'tl_class'=>'w50','mandatory'=>true),
            'sql'                   => "varchar(64) NOT NULL default ''"
        ),

        // ABSCHNITT Inhalte (promoContent)
        'headline' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_lb_promo']['headline'],
            'inputType' => 'text',
            'eval'      => array('tl_class'=>'long','maxlength'=>255),
            'sql'       => "varchar(256) NOT NULL default ''"
        ),
        'bodytext' => array // als TinyMCE
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_lb_promo']['bodytext'],
            'inputType' => 'textarea',
            'eval'      => array('tl_class'=>'long','maxlength'=>255,'rte'=>'tinyMCE'), // Länge anpassen
            'sql'       => "varchar(256) NOT NULL default ''"
        ),
               
                    
                'buttonLabel' => array(
                    'label' => array(
                        'de' => array('Button-Text', 'Bitte Text für den Button eingeben (Ziel-Optionen alternativ).'),
                    ),
                    'inputType' => 'text',
                    'eval' => array('tl_class' => 'w50'),
                    'sql'       => "varchar(256) NOT NULL default ''"
                ),

                'buttonUrlBlog' => array(
                    'label' => array(
                        'de' => array('Seite Blogbeitrag (URL)', 'Option: Interne URL eingeben, um auf einen Blogbeitrag zu verlinken.'),
                    ),
                    'inputType' => 'text',
                    'eval' => array('tl_class' => 'w50'),
                    'sql'       => "varchar(256) NOT NULL default ''"
                ),
                
                'buttonUrlExt' => array(
                    'label' => array(
                        'de' => array('Seite extern (URL)', 'Option: URL eingeben, um auf eine externe URL zu verlinken.'),
                    ),
                    'inputType' => 'text',
                    'eval' => array('tl_class' => 'w50'),
                    'sql'       => "varchar(256) NOT NULL default ''"
                ),
                
                'buttonProductDetail' => array(
                    'label' => array(
                        'de' => array('Seite Produkt-Details', 'Option: Produkt auswählen, um auf eine Produkt-Detailseite zu verlinken.'),
                    ),
                    'inputType' => 'select',
                    'eval' => array('includeBlankOption' => true,'blankOptionLabel' => 'Produktseite auswählen...','tl_class' => 'w50'),
                    'options_callback' => function() {
                    $spilce = function (&$input, $offset, $length, $replacement = array()) {
                        $replacement = (array) $replacement;
                        $key_indices = array_flip(array_keys($input));
                        if (isset($input[$offset]) && is_string($offset)) {
                            $offset = $key_indices[$offset];
                        }
                        if (isset($input[$length]) && is_string($length)) {
                            $length = $key_indices[$length] - $offset;
                        }
                        
                        $input = array_slice($input, 0, $offset, TRUE)
                        + $replacement
                        + array_slice($input, $offset + $length, NULL, TRUE);
                    };
                    $values = array();
                    $product = \Database::getinstance()->prepare("SELECT id,title,lsShopProductCode,lsShopProductPrice as price,lb_sellingUnit FROM tl_ls_shop_product WHERE published = ? ORDER BY lsShopProductCode ASC")->execute(1);
                    $variant = \Database::getinstance()->prepare("SELECT tl_ls_shop_variant.id,tl_ls_shop_variant.lsShopVariantPrice as price,tl_ls_shop_variant.title, tl_ls_shop_variant.lb_sellingUnit,tl_ls_shop_variant.pid,tl_ls_shop_variant.lsShopVariantCode
                    FROM tl_ls_shop_variant
                    LEFT JOIN tl_ls_shop_product ON tl_ls_shop_product.id=tl_ls_shop_variant.pid
                    WHERE tl_ls_shop_product.published = 1 AND tl_ls_shop_variant.published = 1 ORDER BY lsShopVariantCode DESC")->execute();
                    $variantpid = array();
                    while($product->next())
                    {
                        
                        $values[$product->id] = $product->title;
                        
                    }
                    while($variant->next())
                    {
                        /*  $insert=array( $variant->id."_".$variant->pid => "<b>".$variant->title."</b> " );
                         $key = array_search($variant->pid,$values);
                         $pos = array_search($key,array_keys($values));
                         
                         */
                        $insert=array( $variant->pid."-".$variant->id => "Variante: ".$variant->title);
                        
                        $pos = array_search($variant->pid,array_keys($values));
                        $spilce($values,$pos+1,0,$insert);
                        if(!in_array($variant->pid,$variantpid) )
                        {
                            $variantpid[]=$variant->pid;
                        }
                        // $values[$variant->id."_".$variant->pid] = "<b>".$variant->title."</b> ";
                        
                    }
                    foreach($variantpid as $pid)
                    {
                        $values[$pid]= $values[$pid]."/_/";
                    }
                    return $values;
                    },
                    'sql'       => "varchar(256) NOT NULL default ''"
                    ),
                    

                'buttonPageUrl' => array(
                    'label' => array(
                        'de' => array('Seite intern', 'Option: Seite im Seitenbaum auswählen, um auf eine Seite zu verlinken.'),
                    ),
                    'inputType' => 'pageTree',
                    'eval' => array(
                        'fieldType' => 'radio',
                        'tl_class' => 'w50'
                    ),
                    'sql'       => "blob NOT NULL default ''"
                ),
                
                'targetBlank' => array(
                    'label' => array(
                        'de' => array('Im neuen Fenster öffnen', 'Bitte angeben, ob das Ziel in einem neuen Fenster geöffnet werden soll.'),
                    ),
                    'inputType' => 'checkbox',
                    'eval' => array('tl_class' => 'w50'),
                    'sql'       => "char(1) NOT NULL default ''"
                ),
          
              
                            
        // ENDE BUTTON <--
        
        // ABSCHNITT Newsletter Zustimmung (promoNewsletterConsent)
        'newsletterFormAction' => array
       (
            'label'     => &$GLOBALS['TL_LANG']['tl_lb_promo']['newsletterFormAction'],
            'inputType' => 'text',  // OHNE TinyMCE
            'eval'      => array('tl_class'=>'long','maxlength'=>255),
            'sql'       => "varchar(256) NOT NULL default ''"
        ),
        'emailplaceholder' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_lb_promo']['newsletterFormAction'],
            'inputType' => 'text',  // OHNE TinyMCE
            'eval'      => array('tl_class'=>'long','maxlength'=>255),
            'sql'       => "varchar(256) NOT NULL default ''"
        ),
        'newsletterPrivacyConsent' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_lb_promo']['newsletterPrivacyConsent'],
            'inputType' => 'textarea',  // OHNE TinyMCE
            'eval'      => array('tl_class'=>'long','maxlength'=>255),
            'sql'       => "varchar(256) NOT NULL default ''"
        ),
        
        'newsletterHint' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_lb_promo']['newsletterHint'],
            'inputType' => 'text',  // OHNE TinyMCE
            'eval'      => array('tl_class'=>'long','maxlength'=>255),
            'sql'       => "varchar(256) NOT NULL default ''"
        ),
        
        // ABSCHNITT Veröffentlichen (publication)
        'published' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_lb_promo']['published'],
            'inputType' => 'checkbox',
            'eval'      => array('tl_class'=>'long'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        
        'onPages' => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_lb_promo']['onPages'],
            'exclude' 		=> true,
            'inputType' 	=> 'pageTree',
            'eval'                    => array('tl_class'=>'clr wizard','multiple'=>true, 'fieldType'=>'checkbox', 'mandatory'=>true),
            'sql'                   => 'blob NULL'
            
        ),
        
        'afterPageview' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_lb_promo']['afterPageviews'],
            'inputType' => 'checkbox',
            'eval'      => array('tl_class'=>'w50','submitOnChange'=>true),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        
        'afterPageviewCount' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_lb_promo']['afterPageviewCount'],
            'inputType' => 'text',
            'eval'      => array('tl_class'=>'long'),
            'sql'       => "int(10) unsigned NOT NULL default '0'"
        ),
        
        'afterDuration' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_lb_promo']['afterDuration'],
            'inputType' => 'checkbox',
            'eval'      => array('tl_class'=>'w50','submitOnChange'=>true),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        
        'afterDurationSec' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_lb_promo']['afterDurationSec'],
            'inputType' => 'text',
            'eval'      => array('tl_class'=>'long'),
            'sql'       => "int(10) unsigned NOT NULL default '0'"
        ),
        
        'bestBefore' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_lb_promo']['bestBefore'],
            'inputType' => 'text',
            'eval'      => array('tl_class'=>'w50'),
            'sql'       => "int(10) unsigned NOT NULL default '0'"
        ),
        
        'start' => array
        (
            'label'              => &$GLOBALS['TL_LANG']['tl_lb_promo']['start'],
            'inputType'          => 'text',
            'eval'               => array('rgxp'=>'datim', 'datepicker'=>true,'tl_class'=>'w50 wizard'),
            'sql'                   => "varchar(10) NOT NULL default ''"
            
        ),
        
        'stop' => array
        (
            'label'              => &$GLOBALS['TL_LANG']['tl_lb_promo']['stop'],
            'inputType'          => 'text',
            'eval'               => array('rgxp'=>'datim', 'datepicker'=>true,'tl_class'=>'w50 wizard'),
            'sql'                   => "varchar(10) NOT NULL default ''"
        ),
        

    ), 
    


    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => 2,
            'fields'                  => array('promoTitle','promoDescription','promoTemplate'),
            'panelLayout'             => 'filter;sort,search,limit'
        ),
        'label' => array
        (
            'fields'                  => array('promoTitle','promoDescription','promoTemplate'),
            'showColumns'             => true
            
        ),
        'operations' => array
        (
            'edit' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_lb_promo']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.svg'
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_lb_promo']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.svg',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            )
        )
    )
    
);


$GLOBALS['TL_DCA']['tl_lb_promo']['subpalettes']['afterDuration'] ='afterDurationSec';

$GLOBALS['TL_DCA']['tl_lb_promo']['subpalettes']['afterPageview'] ='afterPageviewCount';
$GLOBALS['TL_DCA']['tl_lb_promo']['palettes']['__selector__'][] = 'afterDuration';
$GLOBALS['TL_DCA']['tl_lb_promo']['palettes']['__selector__'][] = 'afterPageview';
class tl_lb_promoClass extends Backend
{
    
    public function getPromoTemplates($dc)
    {
        return \Contao\Controller::getTemplateGroup('lb_promo');
    }
}


