<?php

namespace Svc\VideoBundle\Service;


use Svc\LogBundle\DataProvider\DataProviderInterface;
use Svc\VideoBundle\Controller\VideoController;

class LogDataProvider implements DataProviderInterface
{

  /**
   * get the text/description for a source type
   *
   * @param integer $sourceType
   * @return string
   */
  public function getSourceTypeText(int $sourceID): string
  {
    if ($sourceID === VideoController::OBJ_TYPE_VIDEO) {
      return "video";
    } elseif ($sourceID === VideoController::OBJ_TYPE_VGROUP) {
      return "video group";
    } else {
      return $sourceID;
    }
  }


  /**
   * get the text/description for a source ID
   *
   * @param integer $sourceID
   * @return string
   */
  public function getSourceIDText(int $sourceID): string
  {
    return "ID: " . $sourceID;
  }

  /**
   * get all sourceIDs as array
   *
   * @return array
   */
  public function getSourceIDTextsArray(): array
  {
    return [];
  }

  /**
   * get all sourceTypes as array
   *
   * @return array
   */
  public function getSourceTypeTextsArray(): array
  {
    return [];
  }
}
