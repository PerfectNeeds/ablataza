<?php

namespace MD\Bundle\ServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @ORM\MappedSuperclass
 */
abstract class Locale {

    protected $trans;

    public function getLocate() {
        $session = new Session();
        return $session->get('_locale');
    }

    public function getTranslation($loacle) {
        $this->trans = NULL;
        $this->translations->filter(function($translation) use ( $loacle) {
            if ($translation->getLocale() == $loacle) {
                if ($this->trans == NULL) {
                    return $this->trans = $translation;
                }
            }
        });

        if (!empty($this->trans)) {
            return $this->trans;
        } else {
            return NULL;
        }
    }

    public function getAttr($thiss, $functionName) {
        $attr = str_replace('get', '', $functionName);
        $currentVar = lcfirst($attr);

        $this->trans = NULL;
        $loacle = $this->getLocate();
        switch ($loacle) {
            case 'en':
                return $thiss->{$currentVar};
                break;
            default:
                $this->translations->filter(function($translation) use ( $loacle, $functionName) {
                    if ($translation->getLocale() == $loacle) {
                        return $this->trans = $translation->$functionName();
                        break;
                    }
                });
                if (isset($this->trans)) {
                    return $this->trans;
                } else {
                    return $thiss->{$currentVar};
                }
                break;
        }
    }

}
