<?xml version="1.0"?>
<csw:GetRecords xmlns:csw="http://www.opengis.net/cat/csw/2.0.2" xmlns:ogc="http://www.opengis.net/ogc" xmlns:gml="http://www.opengis.net/gml" service="CSW" version="2.0.2" resultType="results" outputSchema="csw:IsoRecord" maxRecords="$maxRecords" startPosition="$startPosition">

    <csw:Query typeNames="gmd:MD_Metadata">

      <ogc:SortBy>
        <ogc:SortProperty>
          <ogc:PropertyName>$sortBy</ogc:PropertyName>
          <ogc:SortOrder>$sortOrder</ogc:SortOrder>
        </ogc:SortProperty>
      </ogc:SortBy>

      <csw:ElementSetName>full</csw:ElementSetName>

      <csw:Constraint version="1.1.0">

        <Filter xmlns="http://www.opengis.net/ogc" xmlns:gml="http://www.opengis.net/gml">

          <% if WordsToSearchFor %>
            <And>
              <% control WordsToSearchFor %>
                <PropertyIsLike wildCard="%" singleChar="_" escapeChar="\">
                  <PropertyName>AnyText</PropertyName>
                  <Literal>%$word%</Literal>
                </PropertyIsLike>
              <% end_control %>
            </And>
          <% end_if %>

          <% if bboxUpper %>
            <ogc:BBOX>
              <ogc:PropertyName>BoundingBox</ogc:PropertyName>
              <gml:Envelope>
                 <gml:lowerCorner>$bboxLower</gml:lowerCorner>
                 <gml:upperCorner>$bboxUpper</gml:upperCorner>
              </gml:Envelope>
            </ogc:BBOX>
            <% end_if %>

        </Filter>

      </csw:Constraint>

    </csw:Query>
</csw:GetRecords>
