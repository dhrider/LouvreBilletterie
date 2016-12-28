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
        $findId = $page->findById($arg1);

        $findId->click();
    }

    /**
     * @When I select :arg1
     */
    public function iSelect($arg1)
    {
        $page = $this->getSession()->getPage();
        $findClass = $page->find('xpath', '//a[@id="'.$arg1.'"]');

        $findClass->click();
    }

    /**
     * @Then I wait for :arg1 seconds
     */
    public function iWaitForSeconds($arg1)
    {
        $this->getSession()->wait($arg1 * 1000);
    }


}