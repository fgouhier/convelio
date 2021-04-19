<?php

class TemplateManager
{
    /* @var Quote $quote */
    private $quote;

    /**
     * @param Template $tpl
     * @param array $data
     *
     * @return Template
     */
    public function getTemplateComputed(Template $tpl, array $data)
    {
        if (!$tpl) {
            throw new \RuntimeException('no tpl given');
        }

        $replaced = clone($tpl);
        $replaced->subject = $this->computeText($replaced->subject, $data);
        $replaced->content = $this->computeText($replaced->content, $data);

        return $replaced;
    }

    /**
     * @param string $text
     * @param array $data
     *
     * @return mixed
     */
    private function computeText($text, array $data)
    {
        $APPLICATION_CONTEXT = ApplicationContext::getInstance();

        $this->quote = (!empty($data['quote']) and $data['quote'] instanceof Quote) ? $data['quote'] : null;

        if ($this->quote !== null) {

            $this->computeDestinationText($text);

            $this->computeSummariesText($text);



            /*
             * USER
             * [user:*]
             */
            $_user = (isset($data['user']) and ($data['user'] instanceof User)) ? $data['user'] : $APPLICATION_CONTEXT->getCurrentUser();
            if ($_user) {
                (strpos($text, '[user:first_name]') !== false) and $text = str_replace('[user:first_name]', ucfirst(mb_strtolower($_user->firstname)), $text);
            }
        }

        return $text;
    }

    /**
     * @param string $text
     */
    private function computeDestinationText(&$text)
    {
        $destination = null;
        $countryName = '';

        $containsDestinationLink = strpos($text, '[quote:destination_link]');

        if (
            strpos($text, '[quote:destination_name]') !== false
            || $containsDestinationLink !== false
        ) {
            $destination = DestinationRepository::getInstance()->getById($this->quote->destinationId);
        }

        if ($destination !== null) {
            $countryName = $destination->countryName;
        }

        $text = str_replace('[quote:destination_name]', $countryName, $text);

        if ($containsDestinationLink !== false) {
            $this->computeSiteText($text, $destination);
        } else {
            $text = str_replace('[quote:destination_link]', '', $text);
        }
    }

    /**
     * @param string $text
     * @param Destination $destination
     */
    private function computeSiteText(&$text, $destination)
    {
        $site = SiteRepository::getInstance()->getById($this->quote->siteId);

        $url = $site->url . '/' . $destination->countryName . '/quote/' . $this->quote->id;

        $text = str_replace('[quote:destination_link]', $url, $text);
    }

    /**
     * @param string $text
     */
    private function computeSummariesText(&$text)
    {
        $containsSummaryHtml = strpos($text, '[quote:summary_html]');
        $containsSummary = strpos($text, '[quote:summary]');

        if ($containsSummaryHtml !== false || $containsSummary !== false) {
            if ($containsSummaryHtml !== false) {
                $text = str_replace(
                    '[quote:summary_html]',
                    Formatter::renderHtml($this->quote),
                    $text
                );
            }

            if ($containsSummary !== false) {
                $text = str_replace(
                    '[quote:summary]',
                    Formatter::renderText($this->quote),
                    $text
                );
            }
        }
    }
}
