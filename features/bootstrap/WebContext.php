<?php

use Behat\MinkExtension\Context\MinkContext;

class WebContext extends MinkContext
{
    /**
     * @When I click on :arg1
     */
    public function iClickOn($arg1)
    {
        $page = $this->getSession()->getPage();
        $findClass = $page->findById($arg1);
        $findClass->click();
    }

    /**
     * @Then I wait for :arg1 seconds
     */
    public function iWaitForSeconds($arg1)
    {
        $this->getSession()->wait($arg1 * 1000);
    }

    /**
     * @Then I select the date :arg1
     */
    public function iSelectTheDate($arg1)
    {
        $page = $this->getSession()->getPage();
        $date = $page->findById("dateVisite")->find('css', 'a:contains("'.$arg1.'")');
        $date->click();

    }

    /**
     * @Then I submit the form :arg1
     */
    public function iSubmitTheForm($arg1)
    {
        $page = $this->getSession()->getPage();
        $form = $page->find('css', 'form');
        $form->find('css', 'button')->click();
    }

}