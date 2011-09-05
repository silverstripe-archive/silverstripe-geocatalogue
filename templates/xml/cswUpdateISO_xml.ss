<?xml version="1.0" encoding="UTF-8"?>
<csw:Transaction service="CSW" version="2.0.2" xmlns:csw="http://www.opengis.net/cat/csw/2.0.2" xmlns:ogc="http://www.opengis.net/ogc" xmlns:apiso="http://www.opengis.net/cat/csw/apiso/1.0">
    <csw:Update>
		$MDMetadataXML
    </csw:Update>
    <csw:Constraint version="1.0.0">
        <Filter xmlns="http://www.opengis.net/ogc" xmlns:gml="http://www.opengis.net/gml">
            <PropertyIsLike wildCard="%" singleChar="_" escapeChar="\">
                <PropertyName>apiso:identifier</PropertyName>
                <Literal>$fileIdentifier</Literal>
            </PropertyIsLike>
        </Filter>
    </csw:Constraint>
</csw:Transaction>
