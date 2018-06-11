<?php

namespace Seatsio\Events;

function someLabels($ownLabel, $ownType, $parentLabel = null, $parentType = null, $section = null)
{
    $labels = new Labels();
    $labels->own = aLabelAndType($ownLabel, $ownType);
    if ($parentLabel) {
        $labels->parent = aLabelAndType($parentLabel, $parentType);
    }
    $labels->section = $section;
    return $labels;
}

function aLabelAndtype($label, $type)
{
    $labelAndType = new LabelAndType();
    $labelAndType->type = $type;
    $labelAndType->label = $label;
    return $labelAndType;
}