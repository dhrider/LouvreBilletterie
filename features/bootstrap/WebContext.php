<?php

use Behat\MinkExtension\Context\MinkContext;

class WebContext extends MinkContext
{
    /**
     * @When I click on :arg1
     */
    public function iClickOn($arg1)
    {
        if ($arg1 === "AchetezDesBillets")
        {
            $xpath = '//a[@id="pageAchat"]';
        }
        elseif ($arg1 === "DateActive")
        {
            $xpath = '//a[@class="ui-state-active"]';
        }

        $page = $this->getSession()->getPage();
        $findClass = $page->find('xpath', $xpath);

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