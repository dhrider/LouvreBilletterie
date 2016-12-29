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
        $findClass = $page->find('xpath', $arg1);

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