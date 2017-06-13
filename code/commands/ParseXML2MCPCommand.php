<?php
/**
 * Created by JetBrains PhpStorm.
 * User: rainer
 * Date: 4/12/13
 * Time: 1:46 PM
 * To change this template use File | Settings | File Templates.
 */

class ParseXML2MCPCommand extends ParseXMLCommand
{
    public function parseDocument($doc)
    {
        $numberOfRecordsMatched = 0;
        $numberOfRecordsReturned = 0;
        $nextRecord = 0;
        $mdArray = array();

        $xpath = new DOMXPath($doc);
        $xpath->registerNamespace("mcp", "http://bluenet3.antcrc.utas.edu.au/mcp");
        $xpath->registerNamespace("gmd", "http://www.isotc211.org/2005/gmd");
        $xpath->registerNamespace("gco", "http://www.isotc211.org/2005/gco");
        $xpath->registerNamespace("csw", "http://www.opengis.net/cat/csw/2.0.2");

        $metadataList = $xpath->query('mcp:MD_Metadata');
        foreach ($metadataList as $metadata) {
            $mdItem = array();
            if ($xpath->query('gmd:fileIdentifier/gco:CharacterString', $metadata)->length > 0) {
                $mdItem['fileIdentifier'] = $xpath->query('gmd:fileIdentifier/gco:CharacterString', $metadata)->item(0)->nodeValue;
            }

            $element = $metadata->getElementsByTagNameNS('http://www.isotc211.org/2005/gmd', "dateStamp");
            if ($element->length > 0) {
                $node = $element->item(0)->getElementsByTagNameNS('http://www.isotc211.org/2005/gco', 'DateTime');
                $mdItem['dateStamp'] = $node->item(0)->nodeValue;
            }

            $mdItem['metadataStandardName']    = $xpath->query('gmd:metadataStandardName/gco:CharacterString', $metadata)->item(0)->nodeValue;
            $mdItem['metadataStandardVersion'] = $xpath->query('gmd:metadataStandardVersion/gco:CharacterString', $metadata)->item(0)->nodeValue;

            $xmlOnlineResourceList = $xpath->query('gmd:distributionInfo/gmd:MD_Distribution/gmd:transferOptions/gmd:MD_DigitalTransferOptions/gmd:onLine/gmd:CI_OnlineResource', $metadata);
            $OnlineResources = array();
            foreach ($xmlOnlineResourceList as $item) {
                $ciOnlineResource = array();
                $ciOnlineResource['CIOnlineLinkage']  = $this->queryNodeValue($xpath, 'gmd:linkage/gmd:URL', $item);
                $ciOnlineResource['CIOnlineProtocol']  = $this->queryNodeValue($xpath, 'gmd:protocol/gco:CharacterString', $item);
                $ciOnlineResource['CIOnlineName']  = $this->queryNodeValue($xpath, 'gmd:name/gco:CharacterString', $item);
                $ciOnlineResource['CIOnlineDescription']  = $this->queryNodeValue($xpath, 'gmd:description/gco:CharacterString', $item);

                $ciOnlineResource['CIOnlineFunction']  = $this->queryNodeValue($xpath, 'gmd:linkage/gmd:function/gmd:CI_OnLineFunctionCode', $item);
                $OnlineResources[] = $ciOnlineResource;
            }
            $mdItem['CIOnlineResources:CIOnlineResource'] = $OnlineResources;

            $partyList = $xpath->query('gmd:contact/gmd:CI_ResponsibleParty', $metadata);
            foreach ($partyList as $party) {
                $mdContact = array();

                if ($xpath->query('gmd:individualName/gco:CharacterString', $party)->item(0)) {
                    $mdContact['MDIndividualName']   = $xpath->query('gmd:individualName/gco:CharacterString', $party)->item(0)->nodeValue;
                }

                if ($xpath->query('gmd:organisationName/gco:CharacterString', $party)->item(0)) {
                    $mdContact['MDOrganisationName'] = $xpath->query('gmd:organisationName/gco:CharacterString', $party)->item(0)->nodeValue;
                }

                if ($xpath->query('gmd:positionName/gco:CharacterString', $party)->item(0)) {
                    $mdContact['MDPositionName']     = $xpath->query('gmd:positionName/gco:CharacterString', $party)->item(0)->nodeValue;
                }

                $contact = $xpath->query('gmd:contactInfo/gmd:CI_Contact', $party)->item(0);

                $mdVoice = array();
                $voiceNumberList = $xpath->query('gmd:phone/gmd:CI_Telephone/gmd:voice', $contact);
                foreach ($voiceNumberList as $voiceNumber) {
                    $mdPhoneNumber = array();
                    $mdPhoneNumber['Value'] = $xpath->query('gco:CharacterString', $voiceNumber)->item(0)->nodeValue;
                    $mdVoice[] = $mdPhoneNumber;
                }
                $mdContact['MDVoice:MDPhoneNumber'] = $mdVoice;

                // allows only 1 facsimile number
                if ($xpath->query('gmd:phone/gmd:CI_Telephone/gmd:facsimile/gco:CharacterString', $contact)->item(0)) {
                    $mdContact['MDFacsimile'] = $xpath->query('gmd:phone/gmd:CI_Telephone/gmd:facsimile/gco:CharacterString', $contact)->item(0)->nodeValue;
                }

                $addressList = $xpath->query('gmd:address/gmd:CI_Address', $contact);
                foreach ($addressList as $address) {
                    if ($xpath->query('gmd:deliveryPoint/gco:CharacterString', $address)->item(0)) {
                        $mdContact['MDDeliveryPoint'] = $xpath->query('gmd:deliveryPoint/gco:CharacterString', $address)->item(0)->nodeValue;
                    }
                    if ($xpath->query('gmd:city/gco:CharacterString', $address)->item(0)) {
                        $mdContact['MDCity'] = $xpath->query('gmd:city/gco:CharacterString', $address)->item(0)->nodeValue;
                    }
                    if ($xpath->query('gmd:administrativeArea/gco:CharacterString', $address)->item(0)) {
                        $mdContact['MDAdministrativeArea'] = $xpath->query('gmd:administrativeArea/gco:CharacterString', $address)->item(0)->nodeValue;
                    }
                    if ($xpath->query('gmd:postalCode/gco:CharacterString', $address)->item(0)) {
                        $mdContact['MDPostalCode'] = $xpath->query('gmd:postalCode/gco:CharacterString', $address)->item(0)->nodeValue;
                    }
                    if ($xpath->query('gmd:country/gco:CharacterString', $address)->item(0)) {
                        $mdContact['MDCountry'] = $xpath->query('gmd:country/gco:CharacterString', $address)->item(0)->nodeValue;
                    }
                    if ($xpath->query('gmd:electronicMailAddress/gco:CharacterString', $address)->item(0)) {
                        $mdContact['MDElectronicMailAddress'] = $xpath->query('gmd:electronicMailAddress/gco:CharacterString', $address)->item(0)->nodeValue;
                    }
                }

                // add mdContact object to the contact relationship object
                $mdItem['MDContacts:MDContact'] = $mdContact;
            }

            $xmlDataIdentificationList = $xpath->query('gmd:identificationInfo/mcp:MD_DataIdentification', $metadata);
            foreach ($xmlDataIdentificationList as $dataIdentification) {
                $mdItem['MDPurpose']  = $this->queryNodeValue($xpath, 'gmd:purpose/gco:CharacterString', $dataIdentification);
                $mdItem['MDAbstract'] = $this->queryNodeValue($xpath, 'gmd:abstract/gco:CharacterString', $dataIdentification);
                $mdItem['MDLanguage'] = $this->queryNodeValue($xpath, 'gmd:language/gco:CharacterString', $dataIdentification);

                $mdTopicCategory = array();
                $xmlCategoryList = $xpath->query('gmd:topicCategory/gmd:MD_TopicCategoryCode', $dataIdentification);
                foreach ($xmlCategoryList as $category) {
                    if (trim($category->nodeValue)) {
                        $mdTopicCategoryItem = array();
                        $mdTopicCategoryItem['Value'] = trim($category->nodeValue);
                        $mdTopicCategory[] = $mdTopicCategoryItem;
                    }
                }
                $mdItem['MDTopicCategory:MDTopicCategory'] = $mdTopicCategory;

                $xmlCitationList = $xpath->query('gmd:citation/gmd:CI_Citation', $dataIdentification);
                foreach ($xmlCitationList as $citation) {
                    $mdItem['MDTitle'] = $this->queryNodeValue($xpath, 'gmd:title/gco:CharacterString', $citation);
                    $mdItem['MDEdition'] = $this->queryNodeValue($xpath, 'gmd:edition/gco:CharacterString', $citation);

                    $mdCitationDates=array();
                    $xmlDateList = $xpath->query('gmd:date/gmd:CI_Date', $citation);
                    foreach ($xmlDateList as $dateItem) {
                        $mdCitationDate=array();
                        $mdCitationDate['MDDateTime'] = $this->queryNodeValue($xpath, 'gmd:date/gco:DateTime', $dateItem);
                        $mdCitationDate['MDDate'] = $this->queryNodeValue($xpath, 'gmd:date/gco:Date', $dateItem);
                        $mdCitationDate['MDDateType'] = $this->queryNodeValue($xpath, 'gmd:dateType/gmd:CI_DateTypeCode', $dateItem);

                        $mdCitationDates[]=$mdCitationDate;
                    }
                    $mdItem['MDCitationDates:MDCitationDate'] = $mdCitationDates;

                    $mdItem['MDPresentationForm'] = $this->queryNodeValue($xpath, 'gmd:presentationForm/gmd:CI_PresentationFormCode', $citation);
                }

                // Geographic Extend
                $xmlList = $xpath->query('gmd:extent/gmd:EX_Extent', $dataIdentification);
                foreach ($xmlList as $extent) {
                    $mdItem['MDGeographicDiscription']  = $this->queryNodeValue($xpath, 'gmd:description/gco:CharacterString', $extent);

                    $mdItem['MDWestBound'] = $this->queryNodeValue($xpath, 'gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:westBoundLongitude/gco:Decimal', $extent);
                    $mdItem['MDEastBound'] = $this->queryNodeValue($xpath, 'gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:eastBoundLongitude/gco:Decimal', $extent);
                    $mdItem['MDSouthBound'] = $this->queryNodeValue($xpath, 'gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:southBoundLatitude/gco:Decimal', $extent);
                    $mdItem['MDNorthBound'] = $this->queryNodeValue($xpath, 'gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:northBoundLatitude/gco:Decimal', $extent);
                }

                // 2do: need to be tested
                $mdResourceFormat = array();
                $xmlList = $xpath->query('gmd:resourceFormat/gmd:MD_Format', $dataIdentification);
                foreach ($xmlList as $item) {
                    $mdResourceFormat['Name']  = $this->queryNodeValue($xpath, 'gmd:name/gco:CharacterString/gmd:westBoundLongitude/gco:Decimal', $item);
                    $mdResourceFormat['Version']  = $this->queryNodeValue($xpath, 'gmd:version/gco:CharacterString/gmd:westBoundLongitude/gco:Decimal', $item);
                }
                $mdItem['MDResourceFormats:MDResourceFormat'] = $mdResourceFormat;

                // keywords
                $keywords = array();
                $xmlList = $xpath->query('gmd:descriptiveKeywords/gmd:MD_Keywords/gmd:keyword', $dataIdentification);
                foreach ($xmlList as $item) {
                  $keywords[]  = array('Value' => $this->queryNodeValue($xpath, 'gco:CharacterString', $item));
                }
                $mdItem['MDKeywords:MDKeyword'] = $keywords;

                // iso resource contraints
                $mdResourceConstraint = array();
                $xmlList = $xpath->query('gmd:resourceConstraints/gmd:MD_LegalConstraints', $dataIdentification);
                foreach ($xmlList as $item) {
                    $mdResourceConstraint['accessConstraints']  = $this->queryNodeValue($xpath, 'gmd:accessConstraints/gmd:MD_RestrictionCode/gco:CharacterString', $item);
                    $mdResourceConstraint['useConstraints']  = $this->queryNodeValue($xpath, 'gmd:useConstraints/gmd:MD_RestrictionCode/gco:CharacterString', $item);
                    $mdResourceConstraint['otherConstraints']  = $this->queryNodeValue($xpath, 'gmd:otherConstraints/gco:CharacterString', $item);
                }
                $mdItem['MDResourceConstraints:MDResourceConstraint'] = $mdResourceConstraint;

                // mcp resource contraints
                $mcpMDCreativeCommonList = array();
                $xmlList = $xpath->query('gmd:resourceConstraints/mcp:MD_CreativeCommons', $dataIdentification);
                foreach ($xmlList as $item) {
                    $mcpMDCreativeCommon = array();
                    $mcpMDCreativeCommon['useLimitation']  = $this->queryNodeValue($xpath, 'gmd:useLimitation/gco:CharacterString', $item);
                    $mcpMDCreativeCommon['jurisdictionLink']  = $this->queryNodeValue($xpath, 'mcp:jurisdictionLink/gmd:URL', $item);
                    $mcpMDCreativeCommon['licenseLink']  = $this->queryNodeValue($xpath, 'mcp:licenseLink/gmd:URL', $item);
                    $mcpMDCreativeCommon['imageLink']  = $this->queryNodeValue($xpath, 'mcp:imageLink/gmd:URL', $item);
                    $mcpMDCreativeCommon['licenseName']  = $this->queryNodeValue($xpath, 'mcp:licenseName/gco:CharacterString', $item);
                    $mcpMDCreativeCommonList[] = $mcpMDCreativeCommon;
                }
                $mdItem['MCPMDCreativeCommons:MCPMDCreativeCommons'] = $mcpMDCreativeCommonList;

                // 2do: need to be tested
                $mdItem['MDSpatialRepresentationType']  = $this->queryNodeValue($xpath, 'gmd:spatialRepresentationType/gmd:MD_SpatialRepresentationTypeCode', $dataIdentification);

                $partyList = $xpath->query('gmd:pointOfContact/gmd:CI_ResponsibleParty', $dataIdentification);
                foreach ($partyList as $party) {
                    $mdContact = array();

                    $mdContact['MDIndividualName']   = $xpath->query('gmd:individualName/gco:CharacterString', $party)->item(0)->nodeValue;
                    $mdContact['MDOrganisationName'] = $xpath->query('gmd:organisationName/gco:CharacterString', $party)->item(0)->nodeValue;
                    $mdContact['MDPositionName']     = $xpath->query('gmd:positionName/gco:CharacterString', $party)->item(0)->nodeValue;

                    $contact = $xpath->query('gmd:contactInfo/gmd:CI_Contact', $party)->item(0);

                    $mdVoice = array();
                    $voiceNumberList = $xpath->query('gmd:phone/gmd:CI_Telephone/gmd:voice', $contact);
                    foreach ($voiceNumberList as $voiceNumber) {
                        $mdPhoneNumber = array();
                        $mdPhoneNumber['Value'] = $xpath->query('gco:CharacterString', $voiceNumber)->item(0)->nodeValue;
                        $mdVoice[] = $mdPhoneNumber;
                    }
                    $mdContact['MDVoice:MDPhoneNumber'] = $mdVoice;

                    // allows only 1 facsimile number
                    if ($xpath->query('gmd:phone/gmd:CI_Telephone/gmd:facsimile/gco:CharacterString', $contact)->item(0)) {
                        $mdContact['MDFacsimile'] = $xpath->query('gmd:phone/gmd:CI_Telephone/gmd:facsimile/gco:CharacterString', $contact)->item(0)->nodeValue;
                    }

                    $addressList = $xpath->query('gmd:address/gmd:CI_Address', $contact);
                    foreach ($addressList as $address) {
                        if ($xpath->query('gmd:deliveryPoint/gco:CharacterString', $contact)->item(0)) {
                            $mdContact['MDDeliveryPoint']         = $xpath->query('gmd:deliveryPoint/gco:CharacterString', $address)->item(0)->nodeValue;
                        }
                        if ($xpath->query('gmd:city/gco:CharacterString', $contact)->item(0)) {
                            $mdContact['MDCity']                  = $xpath->query('gmd:city/gco:CharacterString', $address)->item(0)->nodeValue;
                        }
                        if ($xpath->query('gmd:administrativeArea/gco:CharacterString', $contact)->item(0)) {
                            $mdContact['MDAdministrativeArea']    = $xpath->query('gmd:administrativeArea/gco:CharacterString', $address)->item(0)->nodeValue;
                        }
                        if ($xpath->query('gmd:postalCode/gco:CharacterString', $contact)->item(0)) {
                            $mdContact['MDPostalCode']            = $xpath->query('gmd:postalCode/gco:CharacterString', $address)->item(0)->nodeValue;
                        }
                        if ($xpath->query('gmd:country/gco:CharacterString', $contact)->item(0)) {
                            $mdContact['MDCountry']               = $xpath->query('gmd:country/gco:CharacterString', $address)->item(0)->nodeValue;
                        }
                        if ($xpath->query('gmd:electronicMailAddress/gco:CharacterString', $contact)->item(0)) {
                            $mdContact['MDElectronicMailAddress'] = $xpath->query('gmd:electronicMailAddress/gco:CharacterString', $address)->item(0)->nodeValue;
                        }
                    }

                    // add mdContact object to the contact relationship object
                    $mdItem['PointOfContacts:MDContact'] = $mdContact;
                }
            }
            $mdArray[] = $mdItem;
        }
        return array($numberOfRecordsMatched, $numberOfRecordsReturned, $nextRecord, $mdArray);
    }

    protected function queryNodeValue($xpath, $path, $field)
    {
        if ($xpath->query($path, $field)->length > 0) {
            return $xpath->query($path, $field)->item(0)->nodeValue;
        }
        return null;
    }

    protected function get_GCOCharacterString($item, $namespace, $field)
    {
        $element = $item->getElementsByTagNameNS($namespace, $field);
        if ($element->length > 0) {
            $node = $element->item(0)->getElementsByTagNameNS('http://www.isotc211.org/2005/gco', 'CharacterString');
            return $node->item(0)->nodeValue;
        }
        return null;
    }
}
