<?php

/**
 * jjsplace actions.
 *
 * @package    CUDV
 * @subpackage jjsplace
 * @author     Your name here
 */
class jjsplaceActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $count = ModelcountsQuery::create()
    ->orderByCreatedAt('desc')
    ->limit(3)
    ->find();
    $_SESSION['JJPCount'] = (($count[0]->getJJP() + $count[1]->getJJP() + $count[2]->getJJP())/200)*100;
    if($_SESSION['JJPCount']>93)
      $_SESSION['JJPCount']=93;

    $arr = $_SESSION['facebook']->getSignedRequest();
    $userId = $arr['user_id'];
    $user = UserQuery::create()->filterByUser($userId)->findOne();
    if ($user):  
      $_SESSION['foods'] = unserialize($user->getFood());
    endif;    
    $_SESSION['jjsplace'] = nutritionQuery::create()->filterByJJP(1)->find();

  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new nutritionForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new nutritionForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $nutrition = nutritionQuery::create()->findPk($request->getParameter('food_id'));
    $this->forward404Unless($nutrition, sprintf('Object nutrition does not exist (%s).', $request->getParameter('food_id')));
    $this->form = new nutritionForm($nutrition);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $nutrition = nutritionQuery::create()->findPk($request->getParameter('food_id'));
    $this->forward404Unless($nutrition, sprintf('Object nutrition does not exist (%s).', $request->getParameter('food_id')));
    $this->form = new nutritionForm($nutrition);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $nutrition = nutritionQuery::create()->findPk($request->getParameter('food_id'));
    $this->forward404Unless($nutrition, sprintf('Object nutrition does not exist (%s).', $request->getParameter('food_id')));
    $nutrition->delete();

    $this->redirect('jjsplace/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $nutrition = $form->save();

      $this->redirect('jjsplace/edit?food_id='.$nutrition->getFoodId());
    }
  }
}
