<?php
namespace LocalbrandingDe\PromotionModalBundle\EventListener;
use Contao\FrontendUser;
use PDO;
use Contao\FrontendTemplate;
class PromoFrontendTemplateListener// Klassenname = Dateiname (ohne .php)
{
    public function __construct() {} // eventuell nicht nötig, probieren
    
    
    
    
    public function setSecureFlag($objCookie) {
        $objCookie->blnSecure = true;
        
        return $objCookie;
    }
    private function getConfig()
    {
        if (isset($_POST['id'])) {
            $id=$_POST['id'];
            $promo  = \Database::getinstance()->prepare('SELECT afterPageview,afterPageviewCount,afterDuration,afterDurationSec,bestBefore FROM  tl_lb_promo WHERE id = ?')->execute($id);
            $table_fields = $promo->fetchAllAssoc(PDO::FETCH_COLUMN);
        }
        
        

    }
    private function getParentPages($id)
    {

       $pages=[];
       $page = \Database::getinstance()->prepare('SELECT id,pid,type FROM tl_page WHERE id =?')->execute($id);
       while($page->type != "root")
       {
           $id=$page->pid;
           $pages[]= $id;
           $page = \Database::getinstance()->prepare('SELECT id,pid,type FROM tl_page WHERE id =?')->execute($id);
          
       }

       
       return $pages;
    }
    public function lbPromoParseFrontendTemplate($strContent, $strTemplate)
    {
        $assets_path= "/bundles/promotionmodal";
        if (isset($_POST['type'])) {
            /**
             * if $_POST['type']) is set then we have to handle ajax calls from fullcalendar
             *
             * We check if the given $type is an existing method
             * - if yes then call the function
             * - if no just do nothing right now (for the moment)
             */
            $type = $_POST['type'];
            if (method_exists($this, $type)) {
                $this->$type();
            }
        }
        else
        {
            
       
        //file_put_contents("tname",$strTemplate."\n",FILE_APPEND);
        
            if($strTemplate == "fe_page_lb")
            {
                
                global $objPage;
                $onPage= false;
                $setJS = false;
                $strRelativeUrl = $objPage->getFrontendUrl();
                $strAbsoluteUrl = $objPage->getAbsoluteUrl();
                if(isset($_SESSION['promo']['pagesVisited']))
                {
                    $_SESSION['promo']['pagesVisited'] += 1;
                    
                }
                else
                {
                    $_SESSION['promo']['pagesVisited'] = 0;
                }
                
                $promos  = \Database::getinstance()->prepare('SELECT * FROM  tl_lb_promo WHERE published = 1')->execute()->fetchAllAssoc(PDO::FETCH_COLUMN);
                
                
                foreach($promos as $promo)
                {
                    
                    
                    //  file_put_contents("tname",$promo['id'],FILE_APPEND);
                    
                    $parents= $this->getParentPages($objPage->id);
                    foreach(deserialize($promo['onPages']) as $page)
                    {
                        if(in_array($page,$parents))
                        {
                            $onPage=true;
                        }
                    }
                    
                    if($onPage)
                    {
                        $setJS= true;
                        $objTemplate = new FrontendTemplate($promo['promoTemplate']);
                        $objTemplate->viewCount = false;
                        
                        
                        if($promo['start']!=0)
                        {
                            if($promo['stop']<=time())
                            {
                                continue;
                            }
                            else
                            {
                                $objTemplate->startStop = true;
                            }
                        }
                        
                        foreach($promo as $fieldname => $value)
                        {
                            $objTemplate->$fieldname = $value;
                        }
                        
                        if(isset($_SESSION['promo']['pagesVisited'])&& $promo['afterPageview'])
                        {
                            if($_SESSION['promo']['pagesVisited']>= $promo['afterPageviewCount'])
                            {
                                $objTemplate->viewCount = true;
                                $template = $objTemplate->parse();
                                $strContent= $strContent.$template;
                            }
                            else
                            {
                                // continue;
                            }
                        }
                        else
                        {
                            $template = $objTemplate->parse();
                            $strContent= $strContent.$template;
                            
                        }
                    }
                    
                    
                    
                    
                    
                }
                
                
                if($setJS){
                    // JS files
                    
                    $GLOBALS['TL_JAVASCRIPT'][] = $assets_path. '/js/timeme.min.js';
                    $GLOBALS['TL_JAVASCRIPT'][] = $assets_path. '/js/micromodal.min.js';
                    $GLOBALS['TL_JAVASCRIPT'][] = $assets_path. '/js/newsletterpopup.js';
                    // CSS files
                    $GLOBALS['TL_CSS'][] = $assets_path. '/css/lb_fe_promo.css';
                    
                    
                }
                $objUser = FrontendUser::getInstance();
                
                
                
                if (FE_USER_LOGGED_IN === true) {
                    
                    foreach($groups as $group)
                    {
                    }
                    
                    //$user_name = $this->User->username;
                    // es gibt einen authentifizierten Frontend-Benutzer
                } else {
                    
                    // es gibt keinen authentifizierten Frontend-Benutzer
                    
                }
                
            }
        }
        
        return $strContent;
    }
    
}



?>
