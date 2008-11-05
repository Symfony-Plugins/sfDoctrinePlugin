  public function executeShow(sfWebRequest $request)
  {
<?php if (isset($this->params['with_propel_route']) && $this->params['with_propel_route']): ?>
    $this-><?php echo $this->getSingularName() ?> = $this->getRoute()->getObject();
<?php else: ?>
    $this-><?php echo $this->getSingularName() ?> = Doctrine::getTable('<?php echo $this->getModelClass() ?>')->find(<?php echo $this->getRetrieveByPkParamsForAction(49) ?>);
    $this->forward404Unless($this-><?php echo $this->getSingularName() ?>);
<?php endif; ?>
  }
