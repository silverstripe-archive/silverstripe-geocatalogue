<gmd:MD_Metadata xmlns:gts="http://www.isotc211.org/2005/gts" xmlns:gml="http://www.opengis.net/gml" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:gco="http://www.isotc211.org/2005/gco" xmlns:gmd="http://www.isotc211.org/2005/gmd">
	<gmd:fileIdentifier>
		<gco:CharacterString xmlns:gmx="http://www.isotc211.org/2005/gmx" xmlns:srv="http://www.isotc211.org/2005/srv">$fileIdentifier</gco:CharacterString>
	</gmd:fileIdentifier>
	<gmd:language>
		<gco:CharacterString>eng</gco:CharacterString>
	</gmd:language>
	<gmd:characterSet>
		<gmd:MD_CharacterSetCode codeListValue="utf8" codeList="http://www.isotc211.org/2005/resources/codeList.xml#MD_CharacterSetCode"/>
	</gmd:characterSet>
	<gmd:parentIdentifier>
		<gco:CharacterString><![CDATA[ $MDParentIdentifier ]]></gco:CharacterString>
	</gmd:parentIdentifier>
	<% loop MDHierarchyLevel %>
	<gmd:hierarchyLevel>
		<gmd:MD_ScopeCode codeList="http://asdd.ga.gov.au/asdd/profileinfo/GAScopeCodeList.xml#MD_ScopeCode" codeListValue="$Value"><![CDATA[ $ValueNice ]]></gmd:MD_ScopeCode>
	</gmd:hierarchyLevel>
	<% end_loop %>
	<% loop MDHierarchyLevelName %>
	<gmd:hierarchyLevelName>
		<gco:CharacterString><![CDATA[ $Value ]]></gco:CharacterString>
	</gmd:hierarchyLevelName>
	<% end_loop %>
	<gmd:contact>
		<% loop MDContacts %>
		<gmd:CI_ResponsibleParty>
			<gmd:individualName>
				<gco:CharacterString>
				<![CDATA[ $MDIndividualName ]]></gco:CharacterString>
			</gmd:individualName>
			<gmd:organisationName>
				<gco:CharacterString><![CDATA[ $MDOrganisationName ]]></gco:CharacterString>
			</gmd:organisationName>
			<gmd:positionName>
				<gco:CharacterString><![CDATA[ $MDPositionName ]]></gco:CharacterString>
			</gmd:positionName>
			<gmd:contactInfo>
				<gmd:CI_Contact>
					<gmd:phone>
						<gmd:CI_Telephone>
							<% loop MDVoice %>
							<gmd:voice>
								<gco:CharacterString><![CDATA[ $Value ]]></gco:CharacterString>
							</gmd:voice>
							<% end_loop %>
							<gmd:facsimile gco:nilReason="missing">
								<gco:CharacterString/>
							</gmd:facsimile>
						</gmd:CI_Telephone>
					</gmd:phone>
					<gmd:address>
						<gmd:CI_Address>
							<gmd:deliveryPoint gco:nilReason="missing">
								<gco:CharacterString/>
							</gmd:deliveryPoint>
							<gmd:city gco:nilReason="missing">
								<gco:CharacterString/>
							</gmd:city>
							<gmd:administrativeArea gco:nilReason="missing">
								<gco:CharacterString/>
							</gmd:administrativeArea>
							<gmd:postalCode gco:nilReason="missing">
								<gco:CharacterString/>
							</gmd:postalCode>
							<gmd:country gco:nilReason="missing">
								<gco:CharacterString/>
							</gmd:country>
							<% loop MDElectronicMailAddress %>
							<gmd:electronicMailAddress>
								<gco:CharacterString><![CDATA[ $Value ]]></gco:CharacterString>
							</gmd:electronicMailAddress>
							<% end_loop %>
						</gmd:CI_Address>
					</gmd:address>
				</gmd:CI_Contact>
			</gmd:contactInfo>
			<gmd:role>
				<gmd:CI_RoleCode codeListValue="pointOfContact" codeList="http://www.isotc211.org/2005/resources/codeList.xml#CI_RoleCode"/>
			</gmd:role>
		</gmd:CI_ResponsibleParty>
		<% end_loop %>
	</gmd:contact>
	<gmd:dateStamp>
		<gco:DateTime xmlns:gmx="http://www.isotc211.org/2005/gmx" xmlns:srv="http://www.isotc211.org/2005/srv">$CreatedInRFC3339</gco:DateTime>
	</gmd:dateStamp>
	<gmd:metadataStandardName>
		<gco:CharacterString xmlns:gmx="http://www.isotc211.org/2005/gmx" xmlns:srv="http://www.isotc211.org/2005/srv">ISO 19115:2003/19139</gco:CharacterString>
	</gmd:metadataStandardName>
	<gmd:metadataStandardVersion>
		<gco:CharacterString xmlns:gmx="http://www.isotc211.org/2005/gmx" xmlns:srv="http://www.isotc211.org/2005/srv">1.0</gco:CharacterString>
	</gmd:metadataStandardVersion>
	<gmd:referenceSystemInfo>
		<gmd:MD_ReferenceSystem>
			<gmd:referenceSystemIdentifier>
				<gmd:RS_Identifier>
					<gmd:code>
						<gco:CharacterString>WGS 1984</gco:CharacterString>
					</gmd:code>
				</gmd:RS_Identifier>
			</gmd:referenceSystemIdentifier>
		</gmd:MD_ReferenceSystem>
	</gmd:referenceSystemInfo>
	<gmd:identificationInfo>
		<gmd:MD_DataIdentification>
			<gmd:citation>
				<gmd:CI_Citation>
					<gmd:title>
						<gco:CharacterString>
						<![CDATA[
							$MDTitle
						]]>
						</gco:CharacterString>
					</gmd:title>
					<% loop MDCitationDates %>
					<gmd:date>
						<gmd:CI_Date>
							<% if MDDateTime %>
							<gmd:date>
								<gco:DateTime>$MDDateTimeInRFC3339</gco:DateTime>
							</gmd:date>
							<% else %>
							<gmd:date/>
							<% end_if %>
							<gmd:dateType>
								<gmd:CI_DateTypeCode codeListValue="$MDDateType" codeList="http://www.isotc211.org/2005/resources/codeList.xml#CI_DateTypeCode"/>
							</gmd:dateType>
						</gmd:CI_Date>
					</gmd:date>
					<% end_loop %>
					<gmd:edition gco:nilReason="missing">
						<gco:CharacterString/>
					</gmd:edition>
					<gmd:presentationForm>
						<gmd:CI_PresentationFormCode codeListValue="" codeList="http://www.isotc211.org/2005/resources/codeList.xml#CI_PresentationFormCode"/>
					</gmd:presentationForm>
				</gmd:CI_Citation>
			</gmd:citation>
			<gmd:abstract>
				<gco:CharacterString>
				<![CDATA[
					$MDAbstract
				]]>
				</gco:CharacterString>
			</gmd:abstract>
			<gmd:purpose gco:nilReason="missing">
				<gco:CharacterString/>
			</gmd:purpose>
			<gmd:status>
				<gmd:MD_ProgressCode codeListValue="" codeList="http://www.isotc211.org/2005/resources/codeList.xml#MD_ProgressCode"/>
			</gmd:status>
			<gmd:pointOfContact>
				<gmd:CI_ResponsibleParty>
					<gmd:individualName gco:nilReason="missing">
						<gco:CharacterString/>
					</gmd:individualName>
					<gmd:organisationName gco:nilReason="missing">
						<gco:CharacterString/>
					</gmd:organisationName>
					<gmd:positionName gco:nilReason="missing">
						<gco:CharacterString/>
					</gmd:positionName>
					<gmd:contactInfo>
						<gmd:CI_Contact>
							<gmd:phone>
								<gmd:CI_Telephone>
									<gmd:voice gco:nilReason="missing">
										<gco:CharacterString/>
									</gmd:voice>
									<gmd:facsimile gco:nilReason="missing">
										<gco:CharacterString/>
									</gmd:facsimile>
								</gmd:CI_Telephone>
							</gmd:phone>
							<gmd:address>
								<gmd:CI_Address>
									<gmd:deliveryPoint gco:nilReason="missing">
										<gco:CharacterString/>
									</gmd:deliveryPoint>
									<gmd:city gco:nilReason="missing">
										<gco:CharacterString/>
									</gmd:city>
									<gmd:administrativeArea gco:nilReason="missing">
										<gco:CharacterString/>
									</gmd:administrativeArea>
									<gmd:postalCode gco:nilReason="missing">
										<gco:CharacterString/>
									</gmd:postalCode>
									<gmd:country gco:nilReason="missing">
										<gco:CharacterString/>
									</gmd:country>
									<gmd:electronicMailAddress gco:nilReason="missing">
										<gco:CharacterString/>
									</gmd:electronicMailAddress>
								</gmd:CI_Address>
							</gmd:address>
						</gmd:CI_Contact>
					</gmd:contactInfo>
					<gmd:role>
						<gmd:CI_RoleCode codeListValue="" codeList="http://www.isotc211.org/2005/resources/codeList.xml#CI_RoleCode"/>
					</gmd:role>
				</gmd:CI_ResponsibleParty>
			</gmd:pointOfContact>
			<gmd:resourceMaintenance>
				<gmd:MD_MaintenanceInformation>
					<gmd:maintenanceAndUpdateFrequency>
						<gmd:MD_MaintenanceFrequencyCode codeListValue="" codeList="http://www.isotc211.org/2005/resources/codeList.xml#MD_MaintenanceFrequencyCode"/>
					</gmd:maintenanceAndUpdateFrequency>
				</gmd:MD_MaintenanceInformation>
			</gmd:resourceMaintenance>
			<gmd:graphicOverview>
				<gmd:MD_BrowseGraphic>
					<gmd:fileName gco:nilReason="missing">
						<gco:CharacterString/>
					</gmd:fileName>
					<gmd:fileDescription>
						<gco:CharacterString>thumbnail</gco:CharacterString>
					</gmd:fileDescription>
				</gmd:MD_BrowseGraphic>
			</gmd:graphicOverview>
			<gmd:graphicOverview>
				<gmd:MD_BrowseGraphic>
					<gmd:fileName gco:nilReason="missing">
						<gco:CharacterString/>
					</gmd:fileName>
					<gmd:fileDescription>
						<gco:CharacterString>large_thumbnail</gco:CharacterString>
					</gmd:fileDescription>
				</gmd:MD_BrowseGraphic>
			</gmd:graphicOverview>
												
			<gmd:descriptiveKeywords>
				<gmd:MD_Keywords>
					<gmd:keyword gco:nilReason="missing">
						<gco:CharacterString/>
					</gmd:keyword>
					<gmd:type>
						<gmd:MD_KeywordTypeCode codeListValue="theme" codeList="http://www.isotc211.org/2005/resources/codeList.xml#MD_KeywordTypeCode"/>
					</gmd:type>
				</gmd:MD_Keywords>
			</gmd:descriptiveKeywords>
			<gmd:descriptiveKeywords>
				<gmd:MD_Keywords>
					<gmd:keyword gco:nilReason="missing">
							<gco:CharacterString/>
					</gmd:keyword>
					<gmd:type>
						<gmd:MD_KeywordTypeCode codeListValue="place" codeList="http://www.isotc211.org/2005/resources/codeList.xml#MD_KeywordTypeCode"/>
					</gmd:type>
				</gmd:MD_Keywords>
			</gmd:descriptiveKeywords>
			<gmd:resourceConstraints>
			<% loop MDResourceConstraints %>
				<gmd:MD_LegalConstraints>
					<gmd:useLimitation>
						<gco:CharacterString>
						<![CDATA[
							$useLimitation
						]]>
						</gco:CharacterString>
					</gmd:useLimitation>
					<gmd:useConstraints>
						<gmd:MD_RestrictionCode codeListValue="license" codeList="http://www.isotc211.org/2005/resources/codeList.xml#MD_RestrictionCode">license</gmd:MD_RestrictionCode>
					</gmd:useConstraints>
					<gmd:accessConstraints>
						<gmd:MD_RestrictionCode codeListValue="" codeList="http://www.isotc211.org/2005/resources/codeList.xml#MD_RestrictionCode"/>
					</gmd:accessConstraints>
					<gmd:otherConstraints gco:nilReason="missing">
						<gco:CharacterString/>
					</gmd:otherConstraints>
				</gmd:MD_LegalConstraints>
			<% end_loop %>
			</gmd:resourceConstraints>
			<gmd:spatialRepresentationType>
				<gmd:MD_SpatialRepresentationTypeCode codeListValue="$MDSpatialRepresentationType" codeList="http://www.isotc211.org/2005/resources/codeList.xml#MD_SpatialRepresentationTypeCode"/>
			</gmd:spatialRepresentationType>
			<gmd:language>
				<gco:CharacterString>$MDLanguage</gco:CharacterString>
			</gmd:language>
			<gmd:characterSet>
				<gmd:MD_CharacterSetCode codeListValue="utf8" codeList="http://www.isotc211.org/2005/resources/codeList.xml#MD_CharacterSetCode"/>
			</gmd:characterSet>
			<% loop MDTopicCategory %>
			<gmd:topicCategory>
				<gmd:MD_TopicCategoryCode>$Value</gmd:MD_TopicCategoryCode>
			</gmd:topicCategory>
			<% end_loop %>
			<gmd:extent>
				<gmd:EX_Extent>
					<gmd:temporalElement>
						<gmd:EX_TemporalExtent>
							<gmd:extent>
								<gml:TimePeriod gml:id="">
										<gml:beginPosition/>
										<gml:endPosition/>
								</gml:TimePeriod>
							</gmd:extent>
						</gmd:EX_TemporalExtent>
					</gmd:temporalElement>
				</gmd:EX_Extent>
			</gmd:extent>
			<gmd:extent>
				<gmd:EX_Extent>
				<gmd:description>
					<gco:CharacterString>
					<![CDATA[
						$MDGeographicDiscription
					]]>
					</gco:CharacterString>
				</gmd:description>
				<gmd:geographicElement>
						<gmd:EX_GeographicBoundingBox>
							<gmd:westBoundLongitude>
								<gco:Decimal>$MDWestBound</gco:Decimal>
							</gmd:westBoundLongitude>
							<gmd:eastBoundLongitude>
								<gco:Decimal>$MDEastBound</gco:Decimal>
							</gmd:eastBoundLongitude>
							<gmd:southBoundLatitude>
								<gco:Decimal>$MDSouthBound</gco:Decimal>
							</gmd:southBoundLatitude>
							<gmd:northBoundLatitude>
								<gco:Decimal>$MDNorthBound</gco:Decimal>
							</gmd:northBoundLatitude>
						</gmd:EX_GeographicBoundingBox>
					</gmd:geographicElement>
				</gmd:EX_Extent>
			</gmd:extent>
			<gmd:supplementalInformation gco:nilReason="missing">
				<gco:CharacterString/>
			</gmd:supplementalInformation>
		</gmd:MD_DataIdentification>
	</gmd:identificationInfo>

	<gmd:distributionInfo>
		<gmd:MD_Distribution>
			<% loop MDResourceFormats %>
			<gmd:distributionFormat>
				<gmd:MD_Format>
					<gmd:name>
						<gco:CharacterString>
						<![CDATA[
							$Name
						]]>
						</gco:CharacterString>
					</gmd:name>
					<gmd:version>
						<gco:CharacterString>
						<![CDATA[
							$Version
						]]>
						</gco:CharacterString>
					</gmd:version>
				</gmd:MD_Format>
			</gmd:distributionFormat>
			<% end_loop %>
			<gmd:transferOptions>
				<gmd:MD_DigitalTransferOptions>
					<% loop CIOnlineResources %>
					<gmd:onLine>
						<gmd:CI_OnlineResource>
							 <gmd:linkage>
								<gmd:URL><![CDATA[
											$CIOnlineLinkage
										]]></gmd:URL>
							 </gmd:linkage>
							 <gmd:protocol>
								<gco:CharacterString>$CIOnlineProtocol</gco:CharacterString>
							 </gmd:protocol>
							 <gmd:name>
										<gco:CharacterString>
											<![CDATA[
										$CIOnlineName
									]]>
								</gco:CharacterString>
							 </gmd:name>
							 <gmd:description>
										<gco:CharacterString>
										<![CDATA[
											$CIOnlineDescription
										]]>
										</gco:CharacterString>
							 </gmd:description>
						</gmd:CI_OnlineResource>
					</gmd:onLine>
					<% end_loop %>
				</gmd:MD_DigitalTransferOptions>
			</gmd:transferOptions>
		</gmd:MD_Distribution>
	</gmd:distributionInfo>
	<gmd:dataQualityInfo>
		<gmd:DQ_DataQuality>
			<gmd:scope>
				<gmd:DQ_Scope>
					<gmd:level>
						<gmd:MD_ScopeCode codeListValue="dataset" codeList="http://www.isotc211.org/2005/resources/codeList.xml#MD_ScopeCode">dataset</gmd:MD_ScopeCode>
					</gmd:level>
				</gmd:DQ_Scope>
			</gmd:scope>
			<gmd:lineage>
				<gmd:LI_Lineage>
					<gmd:statement gco:nilReason="missing">
						<gco:CharacterString/>
					</gmd:statement>
				</gmd:LI_Lineage>
			</gmd:lineage>
		</gmd:DQ_DataQuality>
	</gmd:dataQualityInfo>
</gmd:MD_Metadata>
