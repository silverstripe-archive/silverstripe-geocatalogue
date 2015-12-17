<?php

class ParseXML2MCPSummaryCommand  extends ParseXMLCommand
{

    /**
     * @param $doc
     * @return array
     */
    public function parseDocument($doc)
    {
        $response = $doc->getElementsByTagNameNS('http://www.opengis.net/cat/csw/2.0.2', "GetRecordsResponse");

        $status = $response->item(0)->getElementsByTagName("SearchResults");

        // get search summary
        $numberOfRecordsMatched = $status->item(0)->getAttribute('numberOfRecordsMatched');
        $numberOfRecordsReturned = $status->item(0)->getAttribute('numberOfRecordsReturned');
        $nextRecord = $status->item(0)->getAttribute('nextRecord');

        $metadata = $response->item(0)->getElementsByTagNameNS("http://bluenet3.antcrc.utas.edu.au/mcp", "MD_Metadata");

        $mdArray = array();

        foreach ($metadata as $item) {
            $mdItem = array();

            $element = $item->getElementsByTagNameNS('http://www.isotc211.org/2005/gmd', "fileIdentifier");
            if ($element->length > 0) {
                $node = $element->item(0)->getElementsByTagNameNS('http://www.isotc211.org/2005/gco', 'CharacterString');
                $mdItem['fileIdentifier'] = $node->item(0)->nodeValue;
            }

            $element = $item->getElementsByTagNameNS('http://www.isotc211.org/2005/gmd', "metadataStandardName");
            if ($element->length > 0) {
                $node = $element->item(0)->getElementsByTagNameNS('http://www.isotc211.org/2005/gco', 'CharacterString');
                $mdItem['metadataStandardName'] = $node->item(0)->nodeValue;
            }

            $element = $item->getElementsByTagNameNS('http://www.isotc211.org/2005/gmd', "metadataStandardVersion");
            if ($element->length > 0) {
                $node = $element->item(0)->getElementsByTagNameNS('http://www.isotc211.org/2005/gco', 'CharacterString');
                $mdItem['metadataStandardVersion'] = $node->item(0)->nodeValue;
            }

            $element = $item->getElementsByTagNameNS('http://www.isotc211.org/2005/gmd', "parentIdentifier");
            if ($element->length > 0) {
                $node = $element->item(0)->getElementsByTagNameNS('http://www.isotc211.org/2005/gco', 'CharacterString');
                $mdItem['parentIdentifier'] = $node->item(0)->nodeValue;
            }

            $element = $item->getElementsByTagNameNS('http://www.isotc211.org/2005/gmd', "hierarchyLevel");
            if ($element->length > 0) {
                $node = $element->item(0)->getElementsByTagNameNS('http://www.isotc211.org/2005/gmd', 'MD_ScopeCode');
                $mdItem['hierarchyLevel'] = $node->item(0)->nodeValue;
            }

            $element = $item->getElementsByTagNameNS('http://www.isotc211.org/2005/gmd', "hierarchyLevelName");
            if ($element->length > 0) {
                $node = $element->item(0)->getElementsByTagNameNS('http://www.isotc211.org/2005/gco', 'CharacterString');
                $mdItem['hierarchyLevelName'] = $node->item(0)->nodeValue;
            }

            $element = $item->getElementsByTagNameNS('http://www.isotc211.org/2005/gmd', "identificationInfo");
            if ($element->length > 0) {
                $mcp_element = $element->item(0)->getElementsByTagNameNS('http://bluenet3.antcrc.utas.edu.au/mcp', "MD_DataIdentification");

                $item = $mcp_element->item(0)->getElementsByTagNameNS('http://www.isotc211.org/2005/gmd', 'citation');
                $item = $item->item(0)->getElementsByTagNameNS('http://www.isotc211.org/2005/gmd', 'CI_Citation');
                $item = $item->item(0)->getElementsByTagNameNS('http://www.isotc211.org/2005/gmd', 'title');
                $mdItem['MDTitle'] = trim($item->item(0)->nodeValue);

                $item = $mcp_element->item(0)->getElementsByTagNameNS('http://www.isotc211.org/2005/gmd', 'abstract');
                $mdItem['MDAbstract'] = trim($item->item(0)->nodeValue);

                $item = $mcp_element->item(0)->getElementsByTagNameNS('http://www.isotc211.org/2005/gmd', 'topicCategory');
                $mdItem['MDTopicCategory'] = trim($item->item(0)->nodeValue);
            }
            $mdArray[] = $mdItem;
        }

//        var_dump($mdArray);
        return array($numberOfRecordsMatched, $numberOfRecordsReturned, $nextRecord, $mdArray);
    }
}
