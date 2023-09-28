<?php

namespace TestBlocks\Utils;

use TestBlocks\Helpers\LoggerHelper;
use TestBlocks\Traits\SingletonTrait;
use DOMDocument;

/**
 * Class SvgSanitizer
 * @package TestBlocks\Utils
 *
 * Important: Created from this repo https://github.com/alnorris/SVG-Sanitizer
 */
class SvgSanitizer
{
    use SingletonTrait;

    private DOMDocument $xmlDoc;
    private LoggerHelper $logger;

    // defines the whitelist of elements and attributes allowed.
    private static $whitelist = [
        "a" => ["class", "clip-path", "clip-rule", "fill", "fill-opacity", "fill-rule", "filter", "id", "mask", "opacity", "stroke", "stroke-dasharray", "stroke-dashoffset", "stroke-linecap", "stroke-linejoin", "stroke-miterlimit", "stroke-opacity", "stroke-width", "style", "systemLanguage", "transform", "href", "xlink:href", "xlink:title"],
        "circle" => ["class", "clip-path", "clip-rule", "cx", "cy", "fill", "fill-opacity", "fill-rule", "filter", "id", "mask", "opacity", "r", "requiredFeatures", "stroke", "stroke-dasharray", "stroke-dashoffset", "stroke-linecap", "stroke-linejoin", "stroke-miterlimit", "stroke-opacity", "stroke-width", "style", "systemLanguage", "transform"],
        "clipPath" => ["class", "clipPathUnits", "id"],
        "defs" => [],
        "feColorMatrix" => ["in", "values"],
        "style" => ["type"],
        "desc" => [],
        "ellipse" => ["class", "clip-path", "clip-rule", "cx", "cy", "fill", "fill-opacity", "fill-rule", "filter", "id", "mask", "opacity", "requiredFeatures", "rx", "ry", "stroke", "stroke-dasharray", "stroke-dashoffset", "stroke-linecap", "stroke-linejoin", "stroke-miterlimit", "stroke-opacity", "stroke-width", "style", "systemLanguage", "transform"],
        "feGaussianBlur" => ["class", "color-interpolation-filters", "id", "requiredFeatures", "stdDeviation"],
        "filter" => ["class", "color-interpolation-filters", "filterRes", "filterUnits", "height", "id", "primitiveUnits", "requiredFeatures", "width", "x", "xlink:href", "y"],
        "foreignObject" => ["class", "font-size", "height", "id", "opacity", "requiredFeatures", "style", "transform", "width", "x", "y"],
        "g" => ["class", "clip-path", "clip-rule", "id", "display", "fill", "fill-opacity", "fill-rule", "filter", "mask", "opacity", "requiredFeatures", "stroke", "stroke-dasharray", "stroke-dashoffset", "stroke-linecap", "stroke-linejoin", "stroke-miterlimit", "stroke-opacity", "stroke-width", "style", "systemLanguage", "transform", "font-family", "font-size", "font-style", "font-weight", "text-anchor"],
        "image" => ["class", "clip-path", "clip-rule", "filter", "height", "id", "mask", "opacity", "requiredFeatures", "style", "systemLanguage", "transform", "width", "x", "xlink:href", "xlink:title", "y"],
        "line" => ["class", "clip-path", "clip-rule", "fill", "fill-opacity", "fill-rule", "filter", "id", "marker-end", "marker-mid", "marker-start", "mask", "opacity", "requiredFeatures", "stroke", "stroke-dasharray", "stroke-dashoffset", "stroke-linecap", "stroke-linejoin", "stroke-miterlimit", "stroke-opacity", "stroke-width", "style", "systemLanguage", "transform", "x1", "x2", "y1", "y2"],
        "linearGradient" => ["class", "id", "gradientTransform", "gradientUnits", "requiredFeatures", "spreadMethod", "systemLanguage", "x1", "x2", "xlink:href", "y1", "y2"],
        "marker" => ["id", "class", "markerHeight", "markerUnits", "markerWidth", "orient", "preserveAspectRatio", "refX", "refY", "systemLanguage", "viewBox"],
        "mask" => ["class", "height", "id", "maskContentUnits", "maskUnits", "width", "x", "y", "fill", "values"],
        "metadata" => ["class", "id"],
        "path" => ["class", "clip-path", "clip-rule", "d", "fill", "fill-opacity", "fill-rule", "filter", "id", "marker-end", "marker-mid", "marker-start", "mask", "opacity", "requiredFeatures", "stroke", "stroke-dasharray", "stroke-dashoffset", "stroke-linecap", "stroke-linejoin", "stroke-miterlimit", "stroke-opacity", "stroke-width", "style", "systemLanguage", "transform"],
        "pattern" => ["class", "height", "id", "patternContentUnits", "patternTransform", "patternUnits", "requiredFeatures", "style", "systemLanguage", "viewBox", "width", "x", "xlink:href", "y"],
        "polygon" => ["class", "clip-path", "clip-rule", "id", "fill", "fill-opacity", "fill-rule", "filter", "id", "class", "marker-end", "marker-mid", "marker-start", "mask", "opacity", "points", "requiredFeatures", "stroke", "stroke-dasharray", "stroke-dashoffset", "stroke-linecap", "stroke-linejoin", "stroke-miterlimit", "stroke-opacity", "stroke-width", "style", "systemLanguage", "transform"],
        "polyline" => ["class", "clip-path", "clip-rule", "id", "fill", "fill-opacity", "fill-rule", "filter", "marker-end", "marker-mid", "marker-start", "mask", "opacity", "points", "requiredFeatures", "stroke", "stroke-dasharray", "stroke-dashoffset", "stroke-linecap", "stroke-linejoin", "stroke-miterlimit", "stroke-opacity", "stroke-width", "style", "systemLanguage", "transform"],
        "radialGradient" => ["class", "cx", "cy", "fx", "fy", "gradientTransform", "gradientUnits", "id", "r", "requiredFeatures", "spreadMethod", "systemLanguage", "xlink:href"],
        "rect" => ["class", "clip-path", "clip-rule", "fill", "fill-opacity", "fill-rule", "filter", "height", "id", "mask", "opacity", "requiredFeatures", "rx", "ry", "stroke", "stroke-dasharray", "stroke-dashoffset", "stroke-linecap", "stroke-linejoin", "stroke-miterlimit", "stroke-opacity", "stroke-width", "style", "systemLanguage", "transform", "width", "x", "y"],
        "stop" => ["class", "id", "offset", "requiredFeatures", "stop-color", "stop-opacity", "style", "systemLanguage"],
        "svg" => ["class", "clip-path", "clip-rule", "filter", "id", "height", "mask", "preserveAspectRatio", "requiredFeatures", "style", "systemLanguage", "viewBox", "width", "x", "xmlns", "xmlns:se", "xmlns:xlink", "y"],
        "switch" => ["class", "id", "requiredFeatures", "systemLanguage"],
        "symbol" => ["class", "fill", "fill-opacity", "fill-rule", "filter", "font-family", "font-size", "font-style", "font-weight", "id", "opacity", "preserveAspectRatio", "requiredFeatures", "stroke", "stroke-dasharray", "stroke-dashoffset", "stroke-linecap", "stroke-linejoin", "stroke-miterlimit", "stroke-opacity", "stroke-width", "style", "systemLanguage", "transform", "viewBox"],
        "text" => ["class", "clip-path", "clip-rule", "fill", "fill-opacity", "fill-rule", "filter", "font-family", "font-size", "font-style", "font-weight", "id", "mask", "opacity", "requiredFeatures", "stroke", "stroke-dasharray", "stroke-dashoffset", "stroke-linecap", "stroke-linejoin", "stroke-miterlimit", "stroke-opacity", "stroke-width", "style", "systemLanguage", "text-anchor", "transform", "x", "xml:space", "y"],
        "textPath" => ["class", "id", "method", "requiredFeatures", "spacing", "startOffset", "style", "systemLanguage", "transform", "xlink:href"],
        "title" => [],
        "tspan" => ["class", "clip-path", "clip-rule", "dx", "dy", "fill", "fill-opacity", "fill-rule", "filter", "font-family", "font-size", "font-style", "font-weight", "id", "mask", "opacity", "requiredFeatures", "rotate", "stroke", "stroke-dasharray", "stroke-dashoffset", "stroke-linecap", "stroke-linejoin", "stroke-miterlimit", "stroke-opacity", "stroke-width", "style", "systemLanguage", "text-anchor", "textLength", "transform", "x", "xml:space", "y"],
        "use" => ["class", "clip-path", "clip-rule", "fill", "fill-opacity", "fill-rule", "filter", "height", "id", "mask", "stroke", "stroke-dasharray", "stroke-dashoffset", "stroke-linecap", "stroke-linejoin", "stroke-miterlimit", "stroke-opacity", "stroke-width", "style", "transform", "width", "x", "xlink:href", "y", "fill", "href"],
    ];

    /**
     * Constructor
     */
    private function __construct()
    {
        $this->xmlDoc = new DOMDocument();
        $this->xmlDoc->preserveWhiteSpace = false;
        $this->logger = new LoggerHelper();
    }

    /**
     * Sanitize SVG code
     * @param string $svg
     *
     * @return string
     */
    public function sanitize(string $svg): string
    {
        $this->xmlDoc->loadXML($svg);
        $this->sanitizeXML();

        return $this->saveSVG();
    }

    /**
     * Do the SVG as xml sanitization
     */
    private function sanitizeXML()
    {
        // all elements in xml doc
        $allElements = $this->xmlDoc->getElementsByTagName("*");

        // loop through all elements
        for ($i = 0; $i < $allElements->length; $i++) {
            $currentNode = $allElements->item($i);

            $tagName = $currentNode->tagName;

            // does element exist in whitelist?
            if (isset(self::$whitelist[$tagName])) {
                // array of allowed attributes in specific element
                $whitelist_attr_arr = self::$whitelist[$tagName];

                for ($x = 0; $x < $currentNode->attributes->length; $x++) {

                    // get attributes name
                    $attrName = $currentNode->attributes->item($x)->name;

                    // check if attribute isn't in whiltelist
                    if (!in_array($attrName, $whitelist_attr_arr)) {
                        $this->logger->log(
                            sprintf(
                                'SVG not passing sanitizer: tagName %s and under it his attrName %s is not whitelisted',
                                $tagName,
                                $attrName
                            ),
                            400
                        );
                        $currentNode->removeAttribute($attrName);
                    }
                }
            }

            // else remove element
            else {
                $this->logger->log(
                    sprintf('SVG not passing sanitizer: tagName %s is not whitelisted', $tagName),
                    400
                );

                $currentNode->parentNode->removeChild($currentNode);
            }
        }
    }

    /**
     * Save back svg as string
     * @return false|string
     */
    private function saveSVG()
    {
        $this->xmlDoc->formatOutput = true;

        return($this->xmlDoc->saveXML());
    }
}
