<?php
class sfDoctrineRecordListener extends Doctrine_Record_Listener
{
  public function preInsert(Doctrine_Event $event)
  {
    if ($event->getInvoker()->getTable()->hasColumn('created_at'))
    {
      $event->getInvoker()->created_at = date('Y-m-d H:i:s', time());
    }
    
    if ($event->getInvoker()->getTable()->hasColumn('updated_at'))
    {
      $event->getInvoker()->updated_at = date('Y-m-d H:i:s', time());
    }
  }
  
  public function preUpdate(Doctrine_Event $event)
  {
    if ($event->getInvoker()->getTable()->hasColumn('updated_at'))
    {
      $event->getInvoker()->updated_at = date('Y-m-d H:i:s', time());
    }
  }
}