<?php

/**
 * dininghall actions.
 *
 * @package    CUDV
 * @subpackage dininghall
 * @author     Your name here
 */
class dininghallActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $count = ModelcountsQuery::create()
    ->orderByCreatedAt('desc')
    ->limit(3)
    ->find();
    $_SESSION['JayCount'] = (($count[0]->getJAY() + $count[1]->getJAY() + $count[2]->getJAY())/500)*100;
    $_SESSION['JJPCount'] = (($count[0]->getJJP() + $count[1]->getJJP() + $count[2]->getJJP())/200)*100;
    $_SESSION['FerCount'] = (($count[0]->getFer() + $count[1]->getFer() + $count[2]->getFer())/300)*100;
    if($_SESSION['JayCount']>93)
      $_SESSION['JayCount']=93;
    if($_SESSION['FerCount']>93)
      $_SESSION['FerCount']=93;
    if($_SESSION['JJPCount']>93)
      $_SESSION['JJPCount']=93;




    $_SESSION['facebook'] = new Facebook(array(
      'appId'  => '456852741047731',
      'secret' => 'd393063ed740476afcaf29c30eee14b8',
    ));
    
    $arr = $_SESSION['facebook']->getSignedRequest();
    $userId = $arr['user_id'];
    if ($userId && $_SESSION['facebook']->getUser())
    {
      try
      {
        $me = $_SESSION['facebook']->api('/me');
        $user = UserQuery::create()->filterByUser($userId)->findOne();
        if (!$user)
        {
          $user = new User();
          $user->setUser($userId);
          $user->setName($me['name']);
          $user->setGender($me['gender']);
          $user->save();
        }
        else
        {
          $visits = $user->getVisits();
          $user->setVisits($visits+1);
          $user->save();
        }
      } catch (FacebookApiException $e) 
      {
      }
    }
  }

  public function executeSubscribe(sfWebRequest $request)
  {
    $arr = $_SESSION['facebook']->getSignedRequest();
    $userId = $arr['user_id'];
    $user = UserQuery::create()->filterByUser($userId)->find();
    $foods = $user[0]->getFood();
    $foods = unserialize($foods);
    $counter = 0;
    $id = $_GET['id'];
    $check = 0;
    while ($counter < count($foods))
    {
      if ((string)$foods[$counter] == (string)$id)
      {
        $position = $counter;
        $check = 1;
      }
      $counter = $counter + 1;
    }
    if ($check)
    {
      unset($foods[$position]);
      $foods = array_values($foods);
      $serialized = serialize($foods);
      $user[0]->setFood($serialized);
      $user[0]->save();
    }
    else
    {
      $foods[] = $id;
      $serialized = serialize($foods);
      $user[0]->setFood($serialized);
      $user[0]->save();

    }
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

    $this->redirect('dininghall/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $nutrition = $form->save();

      $this->redirect('dininghall/edit?food_id='.$nutrition->getFoodId());
    }
  }
}
