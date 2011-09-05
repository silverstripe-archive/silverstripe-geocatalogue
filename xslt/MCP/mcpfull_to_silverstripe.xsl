<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
	xmlns:csw="http://www.opengis.net/cat/csw/2.0.2" 
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:gmd="http://www.isotc211.org/2005/gmd" 
	xmlns:gml="http://www.opengis.net/gml" 
	xmlns:gts="http://www.isotc211.org/2005/gts" 
	xmlns:gco="http://www.isotc211.org/2005/gco" 
	xmlns:geonet="http://www.fao.org/geonetwork"
	xmlns:mcp="http://bluenet3.antcrc.utas.edu.au/mcp">

<xsl:template match="/">
	<xsl:for-each select="csw:GetRecordByIdResponse">
		<xsl:for-each select="mcp:MD_Metadata">
			$mdItem = array();
			$mdItem['fileIdentifier']          = trim('<xsl:value-of select="gmd:fileIdentifier"/>');
			$mdItem['dateStamp']               = trim('<xsl:value-of select="gmd:dateStamp"/>'); // metadata creation
			$mdItem['metadataStandardName']    = trim('<xsl:value-of select="gmd:metadataStandardName"/>');
			$mdItem['metadataStandardVersion'] = trim('<xsl:value-of select="gmd:metadataStandardVersion"/>');

			<xsl:for-each select="gmd:contact/gmd:CI_ResponsibleParty">
				$mdContact = array();
				$mdContact['MDIndividualName']   = trim('<xsl:value-of select="gmd:individualName"/>');   // (3) Metadata Contact Role
				$mdContact['MDOrganisationName'] = trim('<xsl:value-of select="gmd:organisationName"/>'); // (3) Metadata Contact Role
				$mdContact['MDPositionName']     = trim('<xsl:value-of select="gmd:positionName"/>');     // (3) Metadata Contact Role

				<xsl:for-each select="gmd:contactInfo/gmd:CI_Contact">
					<xsl:for-each select="gmd:phone/gmd:CI_Telephone">
						$mdContact['MDVoice'] = trim('<xsl:value-of select="gmd:voice"/>'); 
						$mdContact['MDFacsimile'] = trim('<xsl:value-of select="gmd:facsimile"/>');
					</xsl:for-each>
				
					<xsl:for-each select="gmd:address/gmd:CI_Address">
						$mdContact['MDDeliveryPoint'] = trim('<xsl:value-of select="gmd:deliveryPoint"/>'); 
						$mdContact['MDCity'] = trim('<xsl:value-of select="gmd:city"/>');
						$mdContact['MDAdministrativeArea'] = trim('<xsl:value-of select="gmd:administrativeArea"/>');
						$mdContact['MDPostalCode'] = trim('<xsl:value-of select="gmd:postalCode"/>');
						$mdContact['MDCountry'] = trim('<xsl:value-of select="gmd:country"/>');
						$mdContact['MDElectronicMailAddress'] = trim('<xsl:value-of select="gmd:electronicMailAddress"/>');
					</xsl:for-each>
				</xsl:for-each>
				$mdItem['MDContacts:MDContact'] = $mdContact;
			</xsl:for-each>

			<xsl:for-each select="gmd:identificationInfo/mcp:MD_DataIdentification">
				<xsl:for-each select="gmd:pointOfContact/gmd:CI_ResponsibleParty">
					$mdContact = array();
					$mdContact['MDIndividualName']   = trim('<xsl:value-of select="gmd:individualName"/>');   // (3) Metadata Contact Role
					$mdContact['MDOrganisationName'] = trim('<xsl:value-of select="gmd:organisationName"/>'); // (3) Metadata Contact Role
					$mdContact['MDPositionName']     = trim('<xsl:value-of select="gmd:positionName"/>');     // (3) Metadata Contact Role

					<xsl:for-each select="gmd:contactInfo/gmd:CI_Contact">
						<xsl:for-each select="gmd:phone/gmd:CI_Telephone">
							$mdContact['MDVoice'] = trim('<xsl:value-of select="gmd:voice"/>'); 
							$mdContact['MDFacsimile'] = trim('<xsl:value-of select="gmd:facsimile"/>');
						</xsl:for-each>
				
						<xsl:for-each select="gmd:address/gmd:CI_Address">
							$mdContact['MDDeliveryPoint'] = trim('<xsl:value-of select="gmd:deliveryPoint"/>'); 
							$mdContact['MDCity'] = trim('<xsl:value-of select="gmd:city"/>');
							$mdContact['MDAdministrativeArea'] = trim('<xsl:value-of select="gmd:administrativeArea"/>');
							$mdContact['MDPostalCode'] = trim('<xsl:value-of select="gmd:postalCode"/>');
							$mdContact['MDCountry'] = trim('<xsl:value-of select="gmd:country"/>');
							$mdContact['MDElectronicMailAddress'] = trim('<xsl:value-of select="gmd:electronicMailAddress"/>');
						</xsl:for-each>
					</xsl:for-each>
					$mdItem['PointOfContacts:MDContact'] = $mdContact;
				</xsl:for-each>
			</xsl:for-each>


			$ciOnlineResources = array();
			<xsl:for-each select="gmd:distributionInfo">
				<xsl:for-each select="gmd:MD_Distribution">
					<xsl:for-each select="gmd:transferOptions">
						<xsl:for-each select="gmd:MD_DigitalTransferOptions">
							<xsl:for-each select="gmd:onLine">
								$ciOnlineResource = array();
								<xsl:for-each select="gmd:CI_OnlineResource">
									$ciOnlineResource['CIOnlineLinkage']    = trim('<xsl:value-of select="gmd:linkage"/>');    // Resource Format
									$ciOnlineResource['CIOnlineProtocol'] = trim('<xsl:value-of select="gmd:protocol"/>'); // Resource Version
									$ciOnlineResource['CIOnlineName'] = trim('<xsl:value-of select="gmd:name"/>'); // Resource Version
									$ciOnlineResource['CIOnlineDescription'] = trim('<xsl:value-of select="gmd:description"/>'); // Resource Version
									<xsl:for-each select="gmd:function/gmd:CI_OnLineFunctionCode">
										$ciOnlineResource['CIOnlineFunction'] = trim('<xsl:value-of select="@codeListValue"/>');        // (8) Reference Date Type
									</xsl:for-each>
								</xsl:for-each>
								$ciOnlineResources[] = $ciOnlineResource;
							</xsl:for-each>
						</xsl:for-each>
					</xsl:for-each>
				</xsl:for-each>
			</xsl:for-each>
			$mdItem['CIOnlineResources:CIOnlineResource'] = $ciOnlineResources;

			<xsl:for-each select="gmd:identificationInfo">
				<xsl:for-each select="mcp:MD_DataIdentification">
					$mdItem['MDPurpose']       = trim('<xsl:value-of select="gmd:purpose"/>');
					$mdItem['MDAbstract']      = trim('<xsl:value-of select="gmd:abstract"/>');                 // (6) Abstract
					$mdItem['MDLanguage']      = trim('<xsl:value-of select="gmd:language"/>');                 // (9) Language
					
					<!-- $mdItem['MDTopicCategory'] = trim('<xsl:value-of select="gmd:topicCategory"/>');            // (10) Topic Category -->

					$mdItem['MDTopicCategory'] = array();
					<xsl:for-each select="gmd:topicCategory">
						$mdItem['MDTopicCategory'][] = '<xsl:value-of select="gmd:MD_TopicCategoryCode"/>';            // (10) Topic Category
					</xsl:for-each>

					<xsl:for-each select="gmd:citation">
						<xsl:for-each select="gmd:CI_Citation">
							
							$mdItem['MDTitle'] = trim('<xsl:value-of select="gmd:title"/>');                    // (5) Title
							$mdItem['MDEdition'] = trim('<xsl:value-of select="gmd:edition"/>');

							$mdCitationDates=array();
							<xsl:for-each select="gmd:date">
								<xsl:for-each select="gmd:CI_Date">
									$mdCitationDate=array();
									$mdCitationDate['MDDateTime'] = '<xsl:value-of select="gmd:date/gco:DateTime"/>';              // (7) Reference Date Stamp
									$mdCitationDate['MDDate'] = '<xsl:value-of select="gmd:date/gco:Date"/>';                      // (7) Reference Date only (no time)

									<xsl:for-each select="gmd:dateType/gmd:CI_DateTypeCode">
										$mdCitationDate['MDDateType'] = '<xsl:value-of select="@codeListValue"/>';        // (8) Reference Date Type
	                				</xsl:for-each>
									$mdCitationDates[]=$mdCitationDate;
								</xsl:for-each>
							</xsl:for-each>
			        		$mdItem['MDCitationDates:MDCitationDate'] = $mdCitationDates;

							<xsl:for-each select="gmd:presentationForm/gmd:CI_PresentationFormCode">
								$mdItem['MDPresentationForm'] = trim('<xsl:value-of select="@codeListValue"/>');
							</xsl:for-each>
						</xsl:for-each>
					</xsl:for-each>
	
					<xsl:for-each select="gmd:extent">
						<xsl:for-each select="gmd:EX_Extent">
							
							$mdItem['MDGeographicDiscription']  = trim('<xsl:value-of select="gmd:description"/>'); 			// core (suggestion)

							<xsl:for-each select="gmd:geographicElement">
								<xsl:for-each select="gmd:EX_GeographicBoundingBox">
								$mdItem['MDWestBound']  = trim('<xsl:value-of select="gmd:westBoundLongitude"/>'); // (11) Bounding Box Coordinates
								$mdItem['MDEastBound']  = trim('<xsl:value-of select="gmd:eastBoundLongitude"/>'); // (11) Bounding Box Coordinates
								$mdItem['MDSouthBound'] = trim('<xsl:value-of select="gmd:southBoundLatitude"/>'); // (11) Bounding Box Coordinates
								$mdItem['MDNorthBound'] = trim('<xsl:value-of select="gmd:northBoundLatitude"/>'); // (11) Bounding Box Coordinates
								</xsl:for-each>
							</xsl:for-each>
						</xsl:for-each>
					</xsl:for-each>

					$mdResourceFormat = array();
					<xsl:for-each select="gmd:resourceFormat">
						<xsl:for-each select="gmd:MD_Format">
							$mdResourceFormat['Name']    = trim('<xsl:value-of select="gmd:name"/>');    // Resource Format
							$mdResourceFormat['Version'] = trim('<xsl:value-of select="gmd:version"/>'); // Resource Version
						</xsl:for-each>
					</xsl:for-each>
					$mdItem['MDResourceFormats:MDResourceFormat'] = $mdResourceFormat;

					$mdItem['MDKeywords'] = array();
					<xsl:for-each select="gmd:descriptiveKeywords/gmd:MD_Keywords">
						<xsl:for-each select="gmd:keyword">
							$mdItem['MDKeywords:MDKeyword'][] = trim('<xsl:value-of select="gco:CharacterString"/>');
						</xsl:for-each>
					</xsl:for-each>
					
					$mdResourceConstraint = array();
					<xsl:for-each select="gmd:resourceConstraints/gmd:MD_LegalConstraints">						
						<xsl:for-each select="gmd:accessConstraints/gmd:MD_RestrictionCode">
							$mdResourceConstraint['accessConstraints'] = trim('<xsl:value-of select="@codeListValue"/>');
						</xsl:for-each>
						<xsl:for-each select="gmd:useConstraints/gmd:MD_RestrictionCode">
							$mdResourceConstraint['useConstraints'] = trim('<xsl:value-of select="@codeListValue"/>');
						</xsl:for-each>
						<xsl:for-each select="gmd:otherConstraints">
							$mdResourceConstraint['otherConstraints'] = trim('<xsl:value-of select="gco:CharacterString"/>');
						</xsl:for-each>
					</xsl:for-each>
					$mdItem['MDResourceConstraints:MDResourceConstraint'] = $mdResourceConstraint;

					$mcpMDCreativeCommonsList = array();
					<xsl:for-each select="gmd:resourceConstraints/mcp:MD_CreativeCommons">						
						$mcpMDCreativeCommons = array();
						<xsl:for-each select="gmd:useLimitation">
							$mcpMDCreativeCommons['useLimitation'] = trim('<xsl:value-of select="gco:CharacterString"/>');
						</xsl:for-each>
						<xsl:for-each select="mcp:jurisdictionLink">
							$mcpMDCreativeCommons['jurisdictionLink'] = trim('<xsl:value-of select="gmd:URL"/>');
						</xsl:for-each>
						<xsl:for-each select="mcp:licenseLink">
							$mcpMDCreativeCommons['licenseLink'] = trim('<xsl:value-of select="gmd:URL"/>');
						</xsl:for-each>
						<xsl:for-each select="mcp:imageLink">
							$mcpMDCreativeCommons['imageLink'] = trim('<xsl:value-of select="gmd:URL"/>');
						</xsl:for-each>
						<xsl:for-each select="mcp:licenseName">
							$mcpMDCreativeCommons['licenseName'] = trim('<xsl:value-of select="gco:CharacterString"/>');
						</xsl:for-each>
						$mcpMDCreativeCommonsList[] = $mcpMDCreativeCommons;
					</xsl:for-each>
					$mdItem['MCPMDCreativeCommons:MCPMDCreativeCommons'] = $mcpMDCreativeCommonsList;

					<xsl:for-each select="gmd:spatialRepresentationType/gmd:MD_SpatialRepresentationTypeCode">
						$mdItem['MDSpatialRepresentationType'] = trim('<xsl:value-of select="@codeListValue"/>');
					</xsl:for-each>
				</xsl:for-each>
				$mdArray[] = $mdItem;
			</xsl:for-each>	
		</xsl:for-each>
	</xsl:for-each>
</xsl:template>

<xsl:template match="gmd:citation">
</xsl:template>


</xsl:stylesheet>


